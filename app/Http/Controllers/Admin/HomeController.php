<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\User;
use App\Models\Consultation;

class HomeController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalPatients = Patient::count();

        $genderDistribution = Patient::selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')
            ->pluck('total', 'gender');

        $totalMen = $genderDistribution->get('masculino', 0);
        $totalWomen = $genderDistribution->get('femenino', 0);

        $patientsByMonth = Patient::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $ageGroups = [
            '0-17' => Patient::whereNotNull('birth_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 0 AND 17')->count(),
            '18-30' => Patient::whereNotNull('birth_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 30')->count(),
            '31-45' => Patient::whereNotNull('birth_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 31 AND 45')->count(),
            '46-60' => Patient::whereNotNull('birth_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 46 AND 60')->count(),
            '61+' => Patient::whereNotNull('birth_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) > 60')->count(),
        ];

        $newPatientsThisMonth = Patient::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalConsultations = Consultation::count();
        $openConsultations = Consultation::where('status', 'open')->count();

        $statusDistribution = Patient::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.index', compact(
            'totalUsers',
            'totalPatients',
            'totalMen',
            'totalWomen',
            'genderDistribution',
            'patientsByMonth',
            'ageGroups',
            'newPatientsThisMonth',
            'totalConsultations',
            'openConsultations',
            'statusDistribution'
        ));
    }
}
