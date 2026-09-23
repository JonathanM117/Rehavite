<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentInstallment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $payments = Payment::with(['patient', 'installments.user'])
            ->when(!$user->admin, function ($query) use ($user) {
                $query->whereHas('patient', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('collaborators', function ($q2) use ($user) {
                          $q2->where('user_id', $user->id);
                      });
                });
            })
            ->when($request->filled('patient_id'), function ($query) use ($request) {
                $query->where('patient_id', $request->patient_id);
            })
            ->when($user->admin && $request->filled('therapist_id'), function ($query) use ($request) {
                $query->whereHas('patient', function ($q) use ($request) {
                    $q->where('user_id', $request->therapist_id);
                });
            })
            ->orderByDesc('date')
            ->paginate(20)
            ->withQueryString();

        $patients = Patient::query()
            ->when(!$user->admin, function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('collaborators', function ($q2) use ($user) {
                          $q2->where('user_id', $user->id);
                      });
                });
            })
            ->orderBy('name')
            ->get();

        $therapists = $user->admin ? User::orderBy('name')->get() : collect();

        return view('admin.payments.index', compact('payments', 'patients', 'therapists'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'     => ['required', 'exists:patients,id'],
            'consultation_id'=> ['nullable', 'exists:consultations,id'],
            'concept'        => ['nullable', 'string', 'max:255'],
            'date'           => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'total_amount'   => ['required', 'numeric', 'min:0'],
            'amount_paid'    => ['required', 'numeric', 'min:0'],
            'balance'        => ['nullable', 'numeric'],
            'therapist'      => ['nullable', 'string', 'max:255'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ]);

        if (!isset($validated['balance'])) {
            $validated['balance'] = $validated['total_amount'] - $validated['amount_paid'];
        }

        $validated['user_id'] = auth()->id();
        $payment = Payment::create($validated);

        \App\Models\AuditLog::record('created', $payment, 'Registró un pago de $' . number_format($validated['total_amount'], 2));

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Pago registrado con éxito');
    }

    public function storeInstallment(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'date'           => ['required', 'date'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $validated['payment_id'] = $payment->id;
        $validated['user_id']    = auth()->id();

        $installment = PaymentInstallment::create($validated);

        \App\Models\AuditLog::record('created', $installment, 'Agregó un abono de $' . number_format($validated['amount'], 2) . ' al pago #' . $payment->id);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Abono registrado. Saldo actualizado.');
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'concept'        => ['nullable', 'string', 'max:255'],
            'date'           => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'total_amount'   => ['required', 'numeric', 'min:0'],
            'amount_paid'    => ['required', 'numeric', 'min:0'],
            'balance'        => ['nullable', 'numeric'],
            'therapist'      => ['nullable', 'string', 'max:255'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ]);

        if (!isset($validated['balance'])) {
            $validated['balance'] = $validated['total_amount'] - $validated['amount_paid'];
        }

        $payment->update($validated);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Pago actualizado con éxito');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Pago eliminado con éxito');
    }
}
