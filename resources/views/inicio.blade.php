<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard RRHH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #34495e;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background-color: var(--primary);
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            margin: 5px 0;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .stat-card {
            text-align: center;
            padding: 20px;
        }

        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
        }

        .stat-card .label {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .bg-employees {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--secondary);
        }

        .bg-vacations {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success);
        }

        .bg-absences {
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning);
        }

        .bg-recruitment {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger);
        }

        .header {
            background-color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background-color: var(--primary);
            color: white;
        }

        .badge-vacation {
            background-color: var(--success);
        }

        .badge-sick {
            background-color: var(--warning);
        }

        .badge-training {
            background-color: var(--secondary);
        }

        .badge-other {
            background-color: var(--dark);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar d-none d-md-block">
                <div class="text-center mb-4">
                    <h3><i class="fas fa-users"></i> RRHH</h3>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-user-friends"></i> Empleados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-calendar-alt"></i> Asistencia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-plane"></i> Vacaciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-file-invoice-dollar"></i> Nóminas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-chart-line"></i> Reportes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-cog"></i> Configuración</a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Header -->
                <div class="header d-flex justify-content-between align-items-center">
                    <h2>Dashboard de Recursos Humanos</h2>
                    <div class="user-info">
                        <div class="user-avatar">JD</div>
                        <div>
                            <div class="fw-bold">Juan Díaz</div>
                            <div class="text-muted small">Administrador RRHH</div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card stat-card bg-employees">
                            <i class="fas fa-users"></i>
                            <div class="number">142</div>
                            <div class="label">Total Empleados</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-vacations">
                            <i class="fas fa-plane"></i>
                            <div class="number">18</div>
                            <div class="label">En Vacaciones</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-absences">
                            <i class="fas fa-user-times"></i>
                            <div class="number">7</div>
                            <div class="label">Ausencias Hoy</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-recruitment">
                            <i class="fas fa-user-plus"></i>
                            <div class="number">5</div>
                            <div class="label">Nuevas Contrataciones</div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Tables -->
                <div class="row mt-4">
                    <!-- Left Column -->
                    <div class="col-md-8">
                        <!-- Department Distribution -->
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Distribución por Departamento</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="departmentChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activities -->
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Actividad Reciente</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Empleado</th>
                                                <th>Actividad</th>
                                                <th>Fecha</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>María González</td>
                                                <td>Solicitud de vacaciones</td>
                                                <td>15/05/2023</td>
                                                <td><span class="badge badge-vacation text-white">Aprobado</span></td>
                                            </tr>
                                            <tr>
                                                <td>Carlos López</td>
                                                <td>Incorporación</td>
                                                <td>14/05/2023</td>
                                                <td><span class="badge badge-success">Completado</span></td>
                                            </tr>
                                            <tr>
                                                <td>Ana Martínez</td>
                                                <td>Licencia médica</td>
                                                <td>13/05/2023</td>
                                                <td><span class="badge badge-sick text-white">En proceso</span></td>
                                            </tr>
                                            <tr>
                                                <td>Pedro Sánchez</td>
                                                <td>Evaluación de desempeño</td>
                                                <td>12/05/2023</td>
                                                <td><span class="badge badge-warning">Pendiente</span></td>
                                            </tr>
                                            <tr>
                                                <td>Laura Ramírez</td>
                                                <td>Solicitud de capacitación</td>
                                                <td>11/05/2023</td>
                                                <td><span class="badge badge-training text-white">Aprobado</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-4">
                        <!-- Upcoming Events -->
                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Próximos Eventos</h5>
                                <span class="position-relative">
                                    <i class="fas fa-bell"></i>
                                    <span class="notification-badge">3</span>
                                </span>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6>Reunión de equipo</h6>
                                            <small class="text-muted">16/05/2023 - 10:00 AM</small>
                                        </div>
                                        <span class="badge badge-primary">Hoy</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6>Capacitación de seguridad</h6>
                                            <small class="text-muted">18/05/2023 - 2:00 PM</small>
                                        </div>
                                        <span class="badge badge-warning">Próximo</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6>Evaluaciones trimestrales</h6>
                                            <small class="text-muted">22/05/2023 - Todo el día</small>
                                        </div>
                                        <span class="badge badge-secondary">Próxima semana</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6>Celebración aniversario</h6>
                                            <small class="text-muted">30/05/2023 - 4:00 PM</small>
                                        </div>
                                        <span class="badge badge-success">Social</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Attendance Summary -->
                        <div class="card mt-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Resumen de Asistencia</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="attendanceChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Department Distribution Chart
        const departmentCtx = document.getElementById('departmentChart').getContext('2d');
        const departmentChart = new Chart(departmentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Ventas', 'TI', 'Marketing', 'Finanzas', 'Operaciones', 'RH'],
                datasets: [{
                    data: [25, 20, 15, 12, 18, 10],
                    backgroundColor: [
                        '#3498db',
                        '#2ecc71',
                        '#9b59b6',
                        '#e74c3c',
                        '#f39c12',
                        '#1abc9c'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Attendance Summary Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'],
                datasets: [{
                    label: 'Presentes',
                    data: [135, 142, 138, 140, 139],
                    backgroundColor: '#2ecc71'
                }, {
                    label: 'Ausentes',
                    data: [7, 5, 9, 6, 8],
                    backgroundColor: '#e74c3c'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
