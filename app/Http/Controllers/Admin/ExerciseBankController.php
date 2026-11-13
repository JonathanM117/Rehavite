<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;

class ExerciseBankController extends Controller
{
    /**
     * Show the exercise bank tool pre-filled with a patient's data.
     */
    public function show(Patient $patient)
    {
        abort_unless($patient->canBeAccessedBy(auth()->user()), 403);

        $patient->load(['user']);

        return $this->render([
            'nombre' => trim($patient->name . ' ' . $patient->last_name),
            'edad'   => $patient->age,
            'exp'    => $patient->expediente_id,
            'dx'     => $patient->diagnosis,
            'fisio'  => $patient->user->name ?? '',
        ]);
    }

    /**
     * Show the exercise bank tool with no pre-filled data, for people who
     * aren't registered as a patient yet.
     */
    public function preview()
    {
        return $this->render([]);
    }

    /**
     * Serve the currently uploaded exercise bank HTML with patient data injected.
     */
    protected function render(array $data)
    {
        $path = SystemSetting::get('exercise_bank_path');

        if (!$path || !Storage::disk('public')->exists($path)) {
            return response()->view('admin.exercise-bank.missing', [
                'isAdmin' => (bool) auth()->user()?->admin,
            ], 404);
        }

        $html = Storage::disk('public')->get($path);

        $script = '<script>'
            . 'window._ejPaciente = ' . json_encode($data, JSON_UNESCAPED_UNICODE) . ';'
            . '(function(){'
            . 'var d = window._ejPaciente;'
            . 'var map = {f_nom:"nombre", f_edad:"edad", f_exp:"exp", f_dx:"dx", f_fisio:"fisio"};'
            . 'function llenar(){'
            . 'Object.keys(map).forEach(function(id){'
            . 'var el = document.getElementById(id);'
            . 'var val = d[map[id]];'
            . 'if(el && !el.value && val !== null && val !== undefined && val !== "") el.value = val;'
            . '});'
            . '}'
            . 'llenar();'
            . 'var _orig = window.abrirEditor;'
            . 'if(typeof _orig === "function"){'
            . 'window.abrirEditor = function(){ _orig(); llenar(); };'
            . '}'
            . '})();'
            . '</script>';

        $pos = strripos($html, '</body>');
        if ($pos !== false) {
            $html = substr_replace($html, $script, $pos, 0);
        } else {
            $html .= $script;
        }

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
