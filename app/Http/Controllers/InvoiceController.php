<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PatientArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    private function validateInvoice(Request $request)
    {
        return $request->validate([
            'invoice_number' => 'sometimes|unique:invoices,invoice_number',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,credit_card,debit_card,insurance',
            'payment_status' => 'required|in:pending,paid,cancelled,refunded',
            'paid_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'patient_archive_id' => 'required|exists:patient_archive,id',
            'nclinic_id' => 'required|exists:nclinics,id'
        ]);
    }

    public function index(Request $request)
    {
        try {
            $query = Invoice::with(['archive.patient.user', 'archive.doctor.user', 'clinic']);

            if ($request->has('clinic_id')) {
                $query->where('nclinic_id', $request->clinic_id);
            }

            if ($request->has('patient_id')) {
                $query->whereHas('archive', function($q) use ($request) {
                    $q->where('patient_id', $request->patient_id);
                });
            }

            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            if ($request->has('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->has('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            $invoices = $query->latest()->paginate(10);

            return response()->json([
                'status' => true,
                'data' => InvoiceResource::collection($invoices)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0',
                'reservation_id' => 'required|exists:reservations,id',
                'payment_method' => 'required|in:cash,credit_card,debit_card,insurance',
                'payment_status' => 'required|in:pending,paid,cancelled,refunded',
                'notes' => 'nullable|string'
            ]);
 
            $reservation = Reservation::findOrFail($validated['reservation_id']);
            
            $validated['invoice_number'] = 'INV-' . date('Y') . Str::random(6);
            $validated['nclinic_id'] = $reservation->nclinic_id;
            $validated['patient_archive_id'] = $reservation->patient_archive_id;
 
            if ($validated['payment_status'] === 'paid') {
                $validated['paid_at'] = now();
            }
 
            $invoice = Invoice::create($validated);
 
            if($request->wantsJson()) {
                return response()->json([
                    'status' => true, 
                    'message' => 'تم إنشاء الفاتورة بنجاح',
                    'data' => new InvoiceResource($invoice)
                ], 201);
            }
 
            return back()->with('success', 'تم إنشاء الفاتورة بنجاح');
 
        } catch (\Exception $e) {
            if($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
 
            return back()->with('error', 'حدث خطأ أثناء إنشاء الفاتورة');
        }
    }

    public function show(Invoice $invoice)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => new InvoiceResource($invoice->load(['patientarchive.patient.user', 'patientarchive.doctor.user', 'clinic']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Invoice $invoice)
    {
        try {
            if ($invoice->payment_status === 'paid') {
                return response()->json([
                    'status' => false,
                    'message' => 'لا يمكن تعديل فاتورة مدفوعة'
                ], 422);
            }

            $validated = $this->validateInvoice($request);

            if ($validated['payment_status'] === 'paid' && !$invoice->paid_at) {
                $validated['paid_at'] = now();
            }

            $invoice->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث الفاتورة بنجاح',
                'data' => new InvoiceResource($invoice)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Invoice $invoice)
    {
        try {
            if ($invoice->payment_status === 'paid') {
                return response()->json([
                    'status' => false,
                    'message' => 'لا يمكن حذف فاتورة مدفوعة'
                ], 422);
            }

            $invoice->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف الفاتورة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function markAsPaid(Invoice $invoice)
    {
        try {
            if ($invoice->payment_status === 'paid') {
                return response()->json([
                    'status' => false,
                    'message' => 'الفاتورة مدفوعة بالفعل'
                ], 422);
            }

            $invoice->update([
                'payment_status' => 'paid',
                'paid_at' => now()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث حالة الدفع بنجاح',
                'data' => new InvoiceResource($invoice)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatistics(Request $request)
    {
        try {
            $query = Invoice::query();

            if ($request->has('clinic_id')) {
                $query->where('nclinic_id', $request->clinic_id);
            }

            $stats = [
                'total_amount' => $query->clone()->sum('amount'),
                'paid_amount' => $query->clone()->where('payment_status', 'paid')->sum('amount'),
                'pending_amount' => $query->clone()->where('payment_status', 'pending')->sum('amount'),
                
                'total_invoices' => $query->clone()->count(),
                'paid_invoices' => $query->clone()->where('payment_status', 'paid')->count(),
                'pending_invoices' => $query->clone()->where('payment_status', 'pending')->count(),
                
                'today_amount' => $query->clone()->whereDate('created_at', today())->sum('amount'),
                'today_count' => $query->clone()->whereDate('created_at', today())->count(),
                
                'this_month_amount' => $query->clone()->whereMonth('created_at', now()->month)->sum('amount'),
                'this_month_count' => $query->clone()->whereMonth('created_at', now()->month)->count(),
            ];

            return response()->json([
                'status' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}