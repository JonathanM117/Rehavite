@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    {{-- KPI Cards --}}
    <div class="grid grid-4 mb-24">
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fas fa-users"></i></div>
            <div class="stat-value">{{ $totalPatients }}</div>
            <div class="stat-label">Total Pacientes</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-user-shield"></i></div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label">Usuarios del Sistema</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pink"><i class="fas fa-venus"></i></div>
            <div class="stat-value">{{ $totalWomen }}</div>
            <div class="stat-label">Pacientes Mujeres</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-mars"></i></div>
            <div class="stat-value">{{ $totalMen }}</div>
            <div class="stat-label">Pacientes Hombres</div>
        </div>
    </div>

    <div class="grid grid-3 mb-24">
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fas fa-calendar-plus"></i></div>
            <div class="stat-value">{{ $newPatientsThisMonth }}</div>
            <div class="stat-label">Nuevos este mes</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fas fa-notes-medical"></i></div>
            <div class="stat-value">{{ $totalConsultations }}</div>
            <div class="stat-label">Total Consultas</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-folder-open"></i></div>
            <div class="stat-value">{{ $openConsultations }}</div>
            <div class="stat-label">Consultas Abiertas</div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-2">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="card-title"><i class="fas fa-chart-bar"></i> Pacientes por Grupo Etario</div>
            </div>
            <div class="chart-container">
                <canvas id="ageGroupChart"></canvas>
            </div>
        </div>

        <div class="glass-card">
            <div class="card-header-custom">
                <div class="card-title"><i class="fas fa-chart-line"></i> Pacientes por Mes</div>
            </div>
            <div class="chart-container">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <div class="glass-card">
            <div class="card-header-custom">
                <div class="card-title"><i class="fas fa-chart-pie"></i> Distribución por Género</div>
            </div>
            <div class="chart-container">
                <canvas id="genderChart"></canvas>
            </div>
        </div>

        <div class="glass-card">
            <div class="card-header-custom">
                <div class="card-title"><i class="fas fa-chart-doughnut"></i> Estado de Pacientes</div>
            </div>
            <div class="chart-container">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.borderColor = 'rgba(148,163,184,0.1)';
    Chart.defaults.font.family = 'Inter';

    // Age Groups Chart
    new Chart(document.getElementById('ageGroupChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($ageGroups)) !!},
            datasets: [{
                label: 'Pacientes',
                data: {!! json_encode(array_values($ageGroups)) !!},
                backgroundColor: ['#14b8a6', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444'],
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Monthly Chart
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($patientsByMonth->keys()) !!},
            datasets: [{
                label: 'Pacientes nuevos',
                data: {!! json_encode($patientsByMonth->values()) !!},
                borderColor: '#14b8a6',
                backgroundColor: 'rgba(20,184,166,0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#14b8a6',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Gender Distribution
    new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($genderDistribution->keys()) !!},
            datasets: [{
                data: {!! json_encode($genderDistribution->values()) !!},
                backgroundColor: ['#3b82f6', '#ec4899', '#f59e0b', '#94a3b8'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
        }
    });

    // Status Distribution
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusDistribution->keys()) !!},
            datasets: [{
                data: {!! json_encode($statusDistribution->values()) !!},
                backgroundColor: ['#10b981', '#f59e0b', '#94a3b8'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
        }
    });
</script>
@endsection