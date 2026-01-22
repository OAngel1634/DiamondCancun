<?php
// confirmaciones.php
require_once 'db.php';

$db = new AdminDB();
$conn = $db->connect();

// Operaciones CRUD
$mensaje = '';
$mensaje_tipo = 'info';
$confirmacion_editar = null;

// Crear o Actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_confirmacion = $_POST['no_confirmacion'] ?? 0;
    $id_reserva = $_POST['id_reserva'] ?? null;
    $fecha_confirmacion = $_POST['fecha_confirmacion'];
    $estado = $_POST['estado'] ?? 'Pendiente';
    $iva_acreditable = $_POST['iva_acreditable'] ?? 0;
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    $metodo_confirmacion = $_POST['metodo_confirmacion'] ?? '';

    try {
        if ($no_confirmacion > 0) {
            // Actualizar
            $stmt = $conn->prepare("UPDATE Confirmacion SET 
                Id_Reserva = ?,
                Fecha_Confirmacion = ?,
                Estado = ?,
                IVA_Acreditable = ?,
                Metodo_Pago = ?,
                Metodo_Confirmacion = ?
                WHERE No_Confirmacion = ?");
                
            $stmt->execute([
                $id_reserva, $fecha_confirmacion, $estado, 
                $iva_acreditable, $metodo_pago, $metodo_confirmacion,
                $no_confirmacion
            ]);
            $mensaje = "Confirmación actualizada correctamente";
            $mensaje_tipo = 'success';
        } else {
            // SOLUCIÓN: Generar nuevo número de confirmación
            $stmtMax = $conn->query("SELECT MAX(No_Confirmacion) AS max_id FROM Confirmacion");
            $maxId = $stmtMax->fetch(PDO::FETCH_ASSOC)['max_id'];
            $newId = $maxId ? $maxId + 1 : 1;

            // Crear nueva confirmación con ID generado
            $stmt = $conn->prepare("INSERT INTO Confirmacion (
                No_Confirmacion, Id_Reserva, Fecha_Confirmacion, 
                Estado, IVA_Acreditable, Metodo_Pago, Metodo_Confirmacion
            ) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $newId, $id_reserva, $fecha_confirmacion, 
                $estado, $iva_acreditable, $metodo_pago, $metodo_confirmacion
            ]);
            $mensaje = "Confirmación creada correctamente (N°: $newId)";
            $mensaje_tipo = 'success';
        }
    } catch (PDOException $e) {
        $mensaje = "Error: " . $e->getMessage();
        $mensaje_tipo = 'danger';
    }
}

// Eliminar
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    try {
        $stmt = $conn->prepare("DELETE FROM Confirmacion WHERE No_Confirmacion = ?");
        $stmt->execute([$id]);
        $mensaje = "Confirmación eliminada correctamente";
        $mensaje_tipo = 'success';
    } catch (PDOException $e) {
        $mensaje = "Error al eliminar: " . $e->getMessage();
        $mensaje_tipo = 'danger';
    }
}

// Editar (cargar datos)
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    try {
        $stmt = $conn->prepare("SELECT * FROM Confirmacion WHERE No_Confirmacion = ?");
        $stmt->execute([$id]);
        $confirmacion_editar = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $mensaje = "Error al cargar datos: " . $e->getMessage();
        $mensaje_tipo = 'danger';
    }
}

// Obtener parámetros de búsqueda
$filtro = $_GET['filtro'] ?? '';
$campo = $_GET['campo'] ?? 'No_Confirmacion';

// Consulta base con filtros
$sql = "SELECT c.*, r.Fecha_Reserva, r.Monto 
        FROM Confirmacion c
        JOIN Reserva r ON c.Id_Reserva = r.Id_Reserva";
$params = [];

// Aplicar filtros si existen
if (!empty($filtro)) {
    $sql .= " WHERE $campo LIKE ?";
    $params[] = "%$filtro%";
}

// Obtener todas las confirmaciones con filtros
$confirmaciones = [];
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $confirmaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensaje = "Error al cargar confirmaciones: " . $e->getMessage();
    $mensaje_tipo = 'danger';
}

// Obtener reservas para el dropdown
$reservas = $conn->query("SELECT Id_Reserva, Fecha_Reserva, Monto FROM Reserva")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Confirmaciones - Diamond Bright</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
            --primary: #0d6efd;
            --secondary: #6c757d;
            --success: #198754;
            --info: #0dcaf0;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #212529;
            --content-bg: #f5f7fa;
            --card-bg: #ffffff;
            --border-color: #eaeaea;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--content-bg);
            color: var(--dark);
        }
        
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            color: white;
            height: 100vh;
            position: fixed;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h3 {
            font-weight: 600;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar-header h3 i {
            color: var(--info);
        }
        
        .nav-links {
            padding: 20px 0;
        }
        
        .nav-links li {
            list-style: none;
            margin-bottom: 5px;
        }
        
        .nav-links a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            gap: 12px;
            font-size: 15px;
            font-weight: 500;
        }
        
        .nav-links a:hover, .nav-links a.active {
            background: var(--sidebar-hover);
            color: white;
            border-left: 4px solid var(--info);
        }
        
        .nav-links a i {
            width: 24px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            border: none;
        }
        
        .card-header {
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
            font-weight: 600;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .btn-action {
            padding: 5px 8px;
            margin: 0 2px;
        }
        
        .search-card {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        
        .required::after {
            content: " *";
            color: var(--danger);
        }
        
        .edit-mode {
            background-color: #fff8e1;
            border-left: 4px solid var(--warning);
        }
        
        .status-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 12px;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #dee2e6;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .badge-confirmada { background-color: var(--success); }
        .badge-pendiente { background-color: var(--warning); }
        .badge-cancelada { background-color: var(--danger); }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-header h3 span, .nav-links a span {
                display: none;
            }
            
            .sidebar-header h3 {
                justify-content: center;
            }
            
            .nav-links a {
                justify-content: center;
                padding: 12px;
            }
            
            .nav-links a i {
                font-size: 18px;
            }
            
            .main-content {
                margin-left: 70px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="bi bi-gem"></i> <span>Diamond Bright</span></h3>
        </div>
        <ul class="nav-links">
            <li><a href="catamaranes.php"><i class="bi bi-ship"></i> <span>Catamaranes</span></a></li>
            <li><a href="capitanes.php"><i class="bi bi-person-badge"></i> <span>Capitanes</span></a></li>
            <li><a href="marineros.php"><i class="bi bi-people"></i> <span>Marineros</span></a></li>
            <li><a href="clientes.php"><i class="bi bi-person-lines-fill"></i> <span>Clientes</span></a></li>
            <li><a href="reservas.php"><i class="bi bi-calendar-check"></i> <span>Reservas</span></a></li>
            <li><a href="confirmaciones.php" class="active"><i class="bi bi-check-circle"></i> <span>Confirmaciones</span></a></li>
            <li><a href="viajes.php"><i class="bi bi-geo-alt"></i> <span>Viajes</span></a></li>
            <li><a href="tours.php"><i class="bi bi-signpost-split"></i> <span>Tours</span></a></li>
            <li><a href="actividades.php"><i class="bi bi-lightning"></i> <span>Actividades</span></a></li>
            <li><a href="reportes.php"><i class="bi bi-graph-up"></i> <span>Reportes</span></a></li>
            <li><a href="#"><i class="bi bi-box-arrow-right"></i> <span>Cerrar Sesión</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><i class="bi bi-check-circle me-2"></i>Administración de Confirmaciones</h2>
                <?php if ($confirmacion_editar): ?>
                    <span class="badge bg-warning text-dark status-badge">
                        <i class="bi bi-pencil"></i> Editando Confirmación #<?= $confirmacion_editar['No_Confirmacion'] ?>
                    </span>
                <?php else: ?>
                    <span class="badge bg-primary status-badge">
                        <i class="bi bi-list"></i> Modo Visualización
                    </span>
                <?php endif; ?>
            </div>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $mensaje_tipo ?> alert-dismissible fade show">
                    <?= $mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Formulario CRUD -->
            <div class="card mb-4 <?= $confirmacion_editar ? 'edit-mode' : '' ?>">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-check-circle me-2"></i>
                        <?= $confirmacion_editar ? 'Editar Confirmación' : 'Agregar Nueva Confirmación' ?>
                    </h5>
                    <?php if ($confirmacion_editar): ?>
                        <span class="badge bg-white text-dark">N°: <?= $confirmacion_editar['No_Confirmacion'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="no_confirmacion" value="<?= $confirmacion_editar['No_Confirmacion'] ?? '' ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Número de Confirmación</label>
                                <input type="number" class="form-control" name="no_confirmacion" 
                                    value="<?= htmlspecialchars($confirmacion_editar['No_Confirmacion'] ?? '') ?>" 
                                    <?= !$confirmacion_editar ? 'required' : 'readonly' ?>>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label required">Fecha de Confirmación</label>
                                <input type="date" class="form-control" name="fecha_confirmacion" 
                                    value="<?= htmlspecialchars($confirmacion_editar['Fecha_Confirmacion'] ?? date('Y-m-d')) ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label required">Reserva</label>
                                <select class="form-select" name="id_reserva" required>
                                    <option value="">Seleccionar reserva...</option>
                                    <?php foreach ($reservas as $reserva): ?>
                                        <option value="<?= $reserva['Id_Reserva'] ?>"
                                            <?= ($confirmacion_editar && $confirmacion_editar['Id_Reserva'] == $reserva['Id_Reserva']) ? 'selected' : '' ?>>
                                            Reserva #<?= $reserva['Id_Reserva'] ?> (<?= date('d/m/Y', strtotime($reserva['Fecha_Reserva'])) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label required">Estado</label>
                                <select class="form-select" name="estado" required>
                                    <option value="Confirmada" <?= ($confirmacion_editar && $confirmacion_editar['Estado'] == 'Confirmada') ? 'selected' : '' ?>>Confirmada</option>
                                    <option value="Pendiente" <?= ($confirmacion_editar && $confirmacion_editar['Estado'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                                    <option value="Cancelada" <?= ($confirmacion_editar && $confirmacion_editar['Estado'] == 'Cancelada') ? 'selected' : '' ?>>Cancelada</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">IVA Acreditable (MXN)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="iva_acreditable" step="0.01" min="0"
                                        value="<?= htmlspecialchars($confirmacion_editar['IVA_Acreditable'] ?? '0.00') ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label required">Método de Pago</label>
                                <select class="form-select" name="metodo_pago" required>
                                    <option value="Efectivo" <?= ($confirmacion_editar && $confirmacion_editar['Metodo_Pago'] == 'Efectivo') ? 'selected' : '' ?>>Efectivo</option>
                                    <option value="Tarjeta" <?= ($confirmacion_editar && $confirmacion_editar['Metodo_Pago'] == 'Tarjeta') ? 'selected' : '' ?>>Tarjeta</option>
                                    <option value="Transferencia" <?= ($confirmacion_editar && $confirmacion_editar['Metodo_Pago'] == 'Transferencia') ? 'selected' : '' ?>>Transferencia</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label required">Método de Confirmación</label>
                                <select class="form-select" name="metodo_confirmacion" required>
                                    <option value="Email" <?= ($confirmacion_editar && $confirmacion_editar['Metodo_Confirmacion'] == 'Email') ? 'selected' : '' ?>>Email</option>
                                    <option value="Teléfono" <?= ($confirmacion_editar && $confirmacion_editar['Metodo_Confirmacion'] == 'Teléfono') ? 'selected' : '' ?>>Teléfono</option>
                                    <option value="Presencial" <?= ($confirmacion_editar && $confirmacion_editar['Metodo_Confirmacion'] == 'Presencial') ? 'selected' : '' ?>>Presencial</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Guardar
                                </button>
                                
                                <?php if ($confirmacion_editar): ?>
                                    <?php
                                    // Mantener parámetros de búsqueda para cancelar
                                    $queryParams = http_build_query([
                                        'filtro' => $_GET['filtro'] ?? '',
                                        'campo' => $_GET['campo'] ?? ''
                                    ]);
                                    ?>
                                    <a href="confirmaciones.php?<?= $queryParams ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-x me-1"></i> Cancelar
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($confirmacion_editar): ?>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi bi-trash me-1"></i> Eliminar Confirmación
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Barra de búsqueda -->
            <div class="card search-card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" name="filtro" 
                                    placeholder="Buscar confirmaciones..." 
                                    value="<?= htmlspecialchars($_GET['filtro'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="campo">
                                <option value="c.No_Confirmacion" <?= ($_GET['campo'] ?? '') === 'c.No_Confirmacion' ? 'selected' : '' ?>>Número de Confirmación</option>
                                <option value="r.Id_Reserva" <?= ($_GET['campo'] ?? '') === 'r.Id_Reserva' ? 'selected' : '' ?>>ID Reserva</option>
                                <option value="c.Estado" <?= ($_GET['campo'] ?? '') === 'c.Estado' ? 'selected' : '' ?>>Estado</option>
                                <option value="c.Metodo_Pago" <?= ($_GET['campo'] ?? '') === 'c.Metodo_Pago' ? 'selected' : '' ?>>Método de Pago</option>
                                <option value="c.Metodo_Confirmacion" <?= ($_GET['campo'] ?? '') === 'c.Metodo_Confirmacion' ? 'selected' : '' ?>>Método de Confirmación</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                            <a href="confirmaciones.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-repeat"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Confirmaciones -->
            <div class="card">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Lista de Confirmaciones</h5>
                    <span class="badge bg-light text-dark">
                        <?= count($confirmaciones) ?> <?= count($confirmaciones) === 1 ? 'Registro' : 'Registros' ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if (!empty($confirmaciones)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>N° Confirmación</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Reserva</th>
                                        <th>Monto Reserva</th>
                                        <th>IVA Acreditable</th>
                                        <th>Método Pago</th>
                                        <th>Método Confirmación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($confirmaciones as $conf): ?>
                                        <tr>
                                            <td>
                                                <?= $conf['No_Confirmacion'] ?>
                                                <?php if ($confirmacion_editar && $confirmacion_editar['No_Confirmacion'] == $conf['No_Confirmacion']): ?>
                                                    <span class="badge bg-warning text-dark ms-2">Editando</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($conf['Fecha_Confirmacion'])) ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?= match($conf['Estado']) {
                                                        'Confirmada' => 'badge-confirmada',
                                                        'Pendiente' => 'badge-pendiente',
                                                        'Cancelada' => 'badge-cancelada',
                                                        default => 'bg-secondary'
                                                    } ?>">
                                                    <?= $conf['Estado'] ?>
                                                </span>
                                            </td>
                                            <td>Reserva #<?= $conf['Id_Reserva'] ?></td>
                                            <td>$<?= number_format($conf['Monto'], 2) ?></td>
                                            <td>$<?= number_format($conf['IVA_Acreditable'], 2) ?></td>
                                            <td><?= $conf['Metodo_Pago'] ?></td>
                                            <td><?= $conf['Metodo_Confirmacion'] ?></td>
                                            <td>
                                                <?php
                                                // Mantener parámetros de búsqueda en URLs
                                                $queryParams = http_build_query([
                                                    'filtro' => $_GET['filtro'] ?? '',
                                                    'campo' => $_GET['campo'] ?? ''
                                                ]);
                                                ?>
                                                <div class="action-buttons">
                                                    <a href="confirmaciones.php?editar=<?= $conf['No_Confirmacion'] ?>&<?= $queryParams ?>" 
                                                       class="btn btn-sm btn-warning btn-action"
                                                       title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger btn-action" 
                                                        title="Eliminar"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal"
                                                        data-id="<?= $conf['No_Confirmacion'] ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="bi bi-check-circle"></i>
                            <h4>No se encontraron confirmaciones</h4>
                            <p class="text-muted">Intenta cambiar tus filtros o agregar una nueva confirmación</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar esta confirmación?</p>
                    <p class="fw-bold">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i> Cancelar
                    </button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Sí, eliminar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para manejar la eliminación con modal
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteModal');
            const confirmDeleteBtn = document.getElementById('confirmDelete');
            
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                
                // Construir URL con parámetros de búsqueda
                const searchParams = new URLSearchParams(window.location.search);
                const filtro = searchParams.get('filtro') || '';
                const campo = searchParams.get('campo') || '';
                
                // Configurar el enlace de eliminación
                const deleteUrl = `confirmaciones.php?eliminar=${id}&filtro=${filtro}&campo=${campo}`;
                confirmDeleteBtn.href = deleteUrl;
            });
        });
    </script>
</body>
</html>