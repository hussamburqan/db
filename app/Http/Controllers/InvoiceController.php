<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['appointment', 'patient', 'doctor', 'nclinic'])->get();
        return response()->json($invoices);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string|in:cash,credit_card,insurance,bank_transfer',
            'payment_status' => 'required|string|in:pending,paid,cancelled,refunded',
            'paid_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'appointment_id' => 'required|exists:appointments,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'nclinic_id' => 'required|exists:nclinics,id'
        ]);

        if (!isset($validated['invoice_number'])) {
            $validated['invoice_number'] = 'INV-' . Str::random(10);
        }

        if ($validated['payment_status'] === 'paid' && !isset($validated['paid_at'])) {
            $validated['paid_at'] = now();
        }

        $invoice = Invoice::create($validated);
        return response()->json($invoice->load(['appointment', 'patient', 'doctor', 'nclinic']), 201);
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load(['appointment', 'patient', 'doctor', 'nclinic']));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_number' => 'string|unique:invoices,invoice_number,'.$invoice->id,
            'amount' => 'numeric',
            'payment_method' => 'string|in:cash,credit_card,insurance,bank_transfer',
            'payment_status' => 'string|in:pending,paid,cancelled,refunded',
            'paid_at' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        if ($validated['payment_status'] === 'paid' && !$invoice->paid_at) {
            $validated['paid_at'] = now();
        }

        $invoice->update($validated);
        return response()->json($invoice->load(['appointment', 'patient', 'doctor', 'nclinic']));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(null, 204);
    }

    public function generatePDF(Invoice $invoice)
    {
        // Generate PDF logic here
        $pdf = PDF::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download('invoice-'.$invoice->invoice_number.'.pdf');
    }

    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update([
            'payment_status' => 'paid',
            'paid_at' => now()
        ]);
        return response()->json($invoice->load(['appointment', 'patient', 'doctor', 'nclinic']));
    }

    public function search(Request $request)
    {
        $query = Invoice::query();

        if ($request->has('invoice_number')) {
            $query->where('invoice_number', 'like', '%'.$request->invoice_number.'%');
        }

        if ($request->has('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $invoices = $query->with(['appointment', 'patient', 'doctor', 'nclinic'])->get();
        return response()->json($invoices);
    }

    public function getStatistics()
    {
        $stats = [
            'total_invoices' => Invoice::count(),
            'total_paid' => Invoice::where('payment_status', 'paid')->count(),
            'total_pending' => Invoice::where('payment_status', 'pending')->count(),
            'total_amount' => Invoice::where('payment_status', 'paid')->sum('amount'),
            'today_invoices' => Invoice::whereDate('created_at', today())->count(),
            'today_amount' => Invoice::whereDate('created_at', today())->where('payment_status', 'paid')->sum('amount')
        ];

        return response()->json($stats);
    }
}