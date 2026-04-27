<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'consultation_id' => ['nullable', 'exists:consultations,id'],
            'session_number' => ['nullable', 'integer', 'min:1'],
            'date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'balance' => ['nullable', 'numeric'],
            'therapist' => ['nullable', 'string', 'max:255'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Auto-calculate balance if not provided
        if (!isset($validated['balance'])) {
            $validated['balance'] = $validated['total_amount'] - $validated['amount_paid'];
        }

        $validated['user_id'] = auth()->id();
        Payment::create($validated);

        return redirect()
            ->route('admin.patients.show', $validated['patient_id'])
            ->with('success', 'Pago registrado con éxito');
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'session_number' => ['nullable', 'integer', 'min:1'],
            'date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'balance' => ['nullable', 'numeric'],
            'therapist' => ['nullable', 'string', 'max:255'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if (!isset($validated['balance'])) {
            $validated['balance'] = $validated['total_amount'] - $validated['amount_paid'];
        }

        $payment->update($validated);

        return redirect()
            ->route('admin.patients.show', $payment->patient_id)
            ->with('success', 'Pago actualizado con éxito');
    }

    public function destroy(Payment $payment)
    {
        $patientId = $payment->patient_id;
        $payment->delete();

        return redirect()
            ->route('admin.patients.show', $patientId)
            ->with('success', 'Pago eliminado con éxito');
    }
}
