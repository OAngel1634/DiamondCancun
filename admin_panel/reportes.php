<?php
require 'db.php';
$db = new AdminDB();
$conn = $db->connect();

// Reporte 1: Tours más populares
$toursPopulares = $conn->query("
    SELECT t.Id_Tour, t.Nombre, COUNT(v.Id_Viaje) AS TotalViajes
    FROM Tour t
    LEFT JOIN Viaje v ON t.Id_Viaje = v.Id_Viaje
    GROUP BY t.Id_Tour
    ORDER BY TotalViajes DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Reporte 2: Ingresos por mes
$ingresosMensuales = $conn->query("
    SELECT YEAR(r.Fecha_Reserva) AS Anio, MONTH(r.Fecha_Reserva) AS Mes, SUM(r.Monto) AS Total
    FROM Reserva r
    JOIN Confirmacion c ON r.Id_Reserva = c.Id_Reserva
    WHERE c.Estado = 'Confirmado'
    GROUP BY YEAR(r.Fecha_Reserva), MONTH(r.Fecha_Reserva)
    ORDER BY Anio DESC, Mes DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Reporte 3: Actividades más comunes
$actividadesComunes = $conn->query("
    SELECT a.Nombre, COUNT(ta.Id_Tour) AS TotalTours
    FROM Actividad a
    LEFT JOIN Tour_Actividad ta ON a.Id_Actividad = ta.Id_Actividad
    GROUP BY a.Id_Actividad
    ORDER BY TotalTours DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas para las tarjetas superiores
$totalIngresos = 0;
foreach ($ingresosMensuales as $ingreso) {
    $totalIngresos += $ingreso['Total'];
}

$maxPopularidad = 0;
if (!empty($toursPopulares)) {
    $maxPopularidad = max(array_column($toursPopulares, 'TotalViajes'));
}

$maxActividades = 0;
if (!empty($actividadesComunes)) {
    $maxActividades = max(array_column($actividadesComunes, 'TotalTours'));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes del Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
            padding-bottom: 40px;
        }
        .header {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            border: none;
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            background-color: #f8f9fa;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-action {
            width: 80px;
            margin-right: 5px;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
        .search-container {
            position: relative;
            margin-bottom: 20px;
        }
        .search-container .btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }
        .stat-card {
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 100;
        }
        .filter-container {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .progress-bar-custom {
            border-radius: 10px;
            overflow: hidden;
        }
        .trend-up {
            color: #198754;
            font-weight: bold;
        }
        .trend-down {
            color: #dc3545;
            font-weight: bold;
        }
        .trend-stable {
            color: #ffc107;
            font-weight: bold;
        }
        .report-icon {
            font-size: 2rem;
            margin-right: 15px;
            background: rgba(255, 255, 255, 0.2);
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-report {
            transition: all 0.3s;
            border-top: 4px solid;
        }
        .card-report:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .card-report-1 {
            border-top-color: #0d6efd;
        }
        .card-report-2 {
            border-top-color: #198754;
        }
        .card-report-3 {
            border-top-color: #0dcaf0;
        }
        .report-title {
            display: flex;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }
        .chart-container {
            height: 200px;
            display: flex;
            align-items: flex-end;
            padding: 20px 0;
            margin-top: 20px;
        }
        .chart-bar {
            flex: 1;
            margin: 0 5px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .chart-value {
            background: #0d6efd;
            width: 40px;
            border-radius: 5px 5px 0 0;
            transition: all 0.5s ease;
        }
        .chart-label {
            margin-top: 10px;
            text-align: center;
            font-size: 0.85rem;
        }
        .chart-bar:hover .chart-value {
            background: #0b5ed7;
            transform: scale(1.05);
        }
        .chart-bar:hover .chart-label {
            font-weight: bold;
        }
        .month-chart .chart-value {
            background: #198754;
        }
        .month-chart .chart-bar:hover .chart-value {
            background: #157347;
        }
        .activity-chart .chart-value {
            background: #0dcaf0;
        }
        .activity-chart .chart-bar:hover .chart-value {
            background: #0aa2c0;
        }
        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 100;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
                background: white;
            }
            .container {
                max-width: 100%;
            }
            .card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
            .header {
                background: #0d6efd;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Botón de regreso a la página anterior -->
    <button class="btn btn-outline-secondary back-btn no-print" onclick="history.back()">
        <i class="bi bi-arrow-left"></i> Volver
    </button>

    <div class="container">
        <!-- Encabezado mejorado -->
        <div class="header text-center">
            <h1><i class="bi bi-graph-up"></i> Reportes del Sistema</h1>
            <p class="lead">Análisis y estadísticas para una mejor toma de decisiones</p>
        </div>

        <!-- Tarjetas de resumen -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="report-icon bg-primary text-white">
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Tour Más Popular</h5>
                                <?php if (!empty($toursPopulares)): ?>
                                    <h3><?= $toursPopulares[0]['Nombre'] ?></h3>
                                    <p class="mb-0"><?= $toursPopulares[0]['TotalViajes'] ?> viajes realizados</p>
                                <?php else: ?>
                                    <p>No hay datos disponibles</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="report-icon bg-success text-white">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Ingresos Totales</h5>
                                <h3>$<?= number_format($totalIngresos, 2) ?></h3>
                                <p class="mb-0">En <?= count($ingresosMensuales) ?> meses registrados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="report-icon bg-info text-white">
                                <i class="bi bi-activity"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Actividad Más Común</h5>
                                <?php if (!empty($actividadesComunes)): ?>
                                    <h3><?= $actividadesComunes[0]['Nombre'] ?></h3>
                                    <p class="mb-0">En <?= $actividadesComunes[0]['TotalTours'] ?> tours</p>
                                <?php else: ?>
                                    <p>No hay datos disponibles</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte 1: Tours más populares -->
        <div class="card card-report card-report-1">
            <div class="card-header">
                <h4><i class="bi bi-star-fill me-2"></i> Tours Más Populares</h4>
                <span class="badge bg-primary">
                    Total: <?= count($toursPopulares) ?>
                </span>
            </div>
            <div class="card-body">
                <?php if (!empty($toursPopulares)): ?>
                    <div class="chart-container">
                        <?php foreach ($toursPopulares as $tour): 
                            $height = $maxPopularidad ? ($tour['TotalViajes'] / $maxPopularidad * 100) : 0;
                        ?>
                        <div class="chart-bar">
                            <div class="chart-value" style="height: <?= $height ?>%;"></div>
                            <div class="chart-label"><?= $tour['Nombre'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Tour</th>
                                    <th>Nombre</th>
                                    <th>Total de Viajes</th>
                                    <th>Popularidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($toursPopulares as $tour): 
                                    $width = min($tour['TotalViajes'] * 10, 100);
                                ?>
                                <tr>
                                    <td><?= $tour['Id_Tour'] ?></td>
                                    <td><?= $tour['Nombre'] ?></td>
                                    <td><?= $tour['TotalViajes'] ?></td>
                                    <td>
                                        <div class="progress progress-bar-custom" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?= $width ?>%;" 
                                                 aria-valuenow="<?= $tour['TotalViajes'] ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="<?= $maxPopularidad ?>">
                                                <?= $tour['TotalViajes'] ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-info-circle" style="font-size: 3rem; color: #6c757d;"></i>
                        <h4 class="mt-3">No se encontraron tours</h4>
                        <p class="text-muted">No hay datos disponibles para mostrar este reporte</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Reporte 2: Ingresos por mes -->
        <div class="card card-report card-report-2">
            <div class="card-header">
                <h4><i class="bi bi-currency-dollar me-2"></i> Ingresos Mensuales</h4>
                <span class="badge bg-success">
                    Meses: <?= count($ingresosMensuales) ?>
                </span>
            </div>
            <div class="card-body">
                <?php if (!empty($ingresosMensuales)): 
                    $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                    $maxIngreso = max(array_column($ingresosMensuales, 'Total'));
                ?>
                    <div class="chart-container month-chart">
                        <?php foreach ($ingresosMensuales as $ingreso): 
                            $nombreMes = $meses[$ingreso['Mes'] - 1];
                            $height = $maxIngreso ? ($ingreso['Total'] / $maxIngreso * 100) : 0;
                        ?>
                        <div class="chart-bar">
                            <div class="chart-value" style="height: <?= $height ?>%;"></div>
                            <div class="chart-label"><?= $nombreMes ?><br><?= $ingreso['Anio'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Año</th>
                                    <th>Mes</th>
                                    <th>Total Ingresos</th>
                                    <th>Tendencia</th>
                                    <th>Variación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $prevTotal = null;
                                foreach ($ingresosMensuales as $index => $ingreso): 
                                    $nombreMes = $meses[$ingreso['Mes'] - 1];
                                    
                                    // Calcular tendencia y variación
                                    $tendencia = '';
                                    $variacion = '';
                                    $variacionClass = '';
                                    
                                    if ($prevTotal !== null) {
                                        $diferencia = $ingreso['Total'] - $prevTotal;
                                        $porcentaje = $prevTotal ? ($diferencia / $prevTotal * 100) : 0;
                                        
                                        if ($diferencia > 0) {
                                            $tendencia = '↑';
                                            $variacionClass = 'trend-up';
                                            $variacion = '+$' . number_format($diferencia, 2) . ' (' . number_format($porcentaje, 2) . '%)';
                                        } elseif ($diferencia < 0) {
                                            $tendencia = '↓';
                                            $variacionClass = 'trend-down';
                                            $variacion = '-$' . number_format(abs($diferencia), 2) . ' (' . number_format(abs($porcentaje), 2) . '%)';
                                        } else {
                                            $tendencia = '→';
                                            $variacionClass = 'trend-stable';
                                            $variacion = 'Sin cambios';
                                        }
                                    } else {
                                        $tendencia = 'N/A';
                                        $variacion = 'Primer registro';
                                        $variacionClass = 'trend-stable';
                                    }
                                    
                                    $prevTotal = $ingreso['Total'];
                                ?>
                                <tr>
                                    <td><?= $ingreso['Anio'] ?></td>
                                    <td><?= $nombreMes ?></td>
                                    <td>$<?= number_format($ingreso['Total'], 2) ?></td>
                                    <td class="<?= $variacionClass ?>"><?= $tendencia ?></td>
                                    <td class="<?= $variacionClass ?>"><?= $variacion ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-info-circle" style="font-size: 3rem; color: #6c757d;"></i>
                        <h4 class="mt-3">No se encontraron ingresos</h4>
                        <p class="text-muted">No hay datos disponibles para mostrar este reporte</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Reporte 3: Actividades más comunes -->
        <div class="card card-report card-report-3">
            <div class="card-header">
                <h4><i class="bi bi-activity me-2"></i> Actividades Más Comunes</h4>
                <span class="badge bg-info">
                    Actividades: <?= count($actividadesComunes) ?>
                </span>
            </div>
            <div class="card-body">
                <?php if (!empty($actividadesComunes)): ?>
                    <div class="chart-container activity-chart">
                        <?php foreach ($actividadesComunes as $act): 
                            $height = $maxActividades ? ($act['TotalTours'] / $maxActividades * 100) : 0;
                            $shortName = strlen($act['Nombre']) > 15 ? substr($act['Nombre'], 0, 12) . '...' : $act['Nombre'];
                        ?>
                        <div class="chart-bar">
                            <div class="chart-value" style="height: <?= $height ?>%;"></div>
                            <div class="chart-label"><?= $shortName ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Actividad</th>
                                    <th>Tours que la incluyen</th>
                                    <th>Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $maxTours = max(array_column($actividadesComunes, 'TotalTours'));
                                foreach ($actividadesComunes as $act): 
                                    $porcentaje = ($maxTours > 0) ? round(($act['TotalTours'] / $maxTours) * 100) : 0;
                                ?>
                                <tr>
                                    <td><?= $act['Nombre'] ?></td>
                                    <td><?= $act['TotalTours'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-bar-custom flex-grow-1" style="height: 20px;">
                                                <div class="progress-bar bg-info" role="progressbar" 
                                                     style="width: <?= $porcentaje ?>%;" 
                                                     aria-valuenow="<?= $act['TotalTours'] ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="<?= $maxTours ?>">
                                                    <?= $porcentaje ?>%
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-info-circle" style="font-size: 3rem; color: #6c757d;"></i>
                        <h4 class="mt-3">No se encontraron actividades</h4>
                        <p class="text-muted">No hay datos disponibles para mostrar este reporte</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Botón para imprimir reportes -->
        <button class="btn btn-primary print-btn no-print" onclick="window.print()" title="Imprimir Reportes">
            <i class="bi bi-printer"></i>
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Actualizar título de la página cuando está inactiva
        let originalTitle = document.title;
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                document.title = '¡Reportes Importantes! 📊';
            } else {
                document.title = originalTitle;
            }
        });
        
        // Animación de las barras de los gráficos al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const chartValues = document.querySelectorAll('.chart-value');
            chartValues.forEach(value => {
                const height = value.style.height;
                value.style.height = '0%';
                setTimeout(() => {
                    value.style.height = height;
                }, 300);
            });
        });
    </script>
</body>
</html>