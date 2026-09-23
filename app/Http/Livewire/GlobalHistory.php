<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\AuditLog;
use App\Models\SystemSetting;

class GlobalHistory extends Component
{
    public $perPage = 50;

    public function render()
    {
        $groupMinutes = (int) SystemSetting::get('audit_group_minutes', 10);

        // Load all logs ordered by newest first
        $logs = AuditLog::with('user')
            ->orderByDesc('created_at')
            ->take(300)
            ->get();

        // Group: if same user + same action + same model_type + same model_id
        // within $groupMinutes → merge into one expandable group
        $grouped = [];
        foreach ($logs as $log) {
            $key = $log->user_id . '|' . $log->action . '|' . $log->model_type . '|' . $log->model_id;
            if (!empty($grouped)) {
                $last = end($grouped);
                $lastKey = $last['user_id'] . '|' . $last['action'] . '|' . $last['model_type'] . '|' . $last['model_id'];
                $timeDiff = $last['created_at']->diffInMinutes($log->created_at);

                if ($lastKey === $key && $timeDiff <= $groupMinutes) {
                    // Add to existing group
                    $grouped[count($grouped) - 1]['children'][] = $log;
                    continue;
                }
            }
            $grouped[] = [
                'user_id'    => $log->user_id,
                'action'     => $log->action,
                'model_type' => $log->model_type,
                'model_id'   => $log->model_id,
                'description'=> $log->description,
                'created_at' => $log->created_at,
                'user'       => $log->user,
                'children'   => [],
                'main_log'   => $log,
            ];
        }

        return view('livewire.global-history', compact('grouped'));
    }
}
