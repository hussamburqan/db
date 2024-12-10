<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class InvoiceController extends Controller
{
    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'nclinic_id' => 'required|exists:nclinics,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,credit_card,debit_card,insurance',
            'payment_status' => 'required|in:pending,paid',
            'notes' => 'nullable|string'
        ]);

        // Check if invoice already exists
        $existingInvoice = Invoice::where('reservation_id', $validated['reservation_id'])->first();
        if ($existingInvoice) {
            return response()->json([
                'status' => false,
                'message' => 'الفاتورة موجودة مسبقاً لهذا الموعد'
            ], 422);
        }

        $invoice = Invoice::create([
            ...$validated,
            'invoice_number' => 'INV-' . date('Y') . sprintf('%06d', Invoice::count() + 1),
            'paid_at' => $validated['payment_status'] === 'paid' ? now() : null
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الفاتورة بنجاح',
            'data' => $invoice
        ], 201);

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

            $validated = $request->validate([
                'amount' => 'sometimes|required|numeric|min:0',
                'payment_method' => 'sometimes|required|in:cash,credit_card,debit_card,insurance',
                'payment_status' => 'sometimes|required|in:pending,paid,cancelled,refunded',
                'notes' => 'nullable|string'
            ]);

            if (isset($validated['payment_status']) && $validated['payment_status'] === 'paid') {
                $validated['paid_at'] = now();
            }

            $invoice->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث الفاتورة بنجاح',
                'data' => $invoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Invoice $invoice)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => $invoice->load(['reservation', 'clinic'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $query = Invoice::with(['reservation', 'clinic']);

            if ($request->has('clinic_id')) {
                $query->where('nclinic_id', $request->clinic_id);
            }

            if ($request->has('reservation_id')) {
                $query->where('reservation_id', $request->reservation_id);
            }

            $invoices = $query->latest()->get();

            return response()->json([
                'status' => true,
                'data' => $invoices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}