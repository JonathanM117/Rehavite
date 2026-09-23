<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TelemetryController extends Controller
{
    /**
     * Log telemetry clicks and navigation events silently.
     */
    public function logClick(Request $request)
    {
        try {
            $event = $request->input('event', 'click');
            $page = $request->input('page', '/');
            $label = $request->input('label', '');
            
            $action = 'telemetry_' . $event;
            $details = "Página: {$page}" . ($label ? " | Elemento: {$label}" : "");

            AuditLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => $action,
                'model_type' => User::class,
                'model_id' => Auth::id() ?? 1,
                'description' => $details,
                'changes' => null,
                'created_at' => now(),
            ]);

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500);
        }
    }
}
