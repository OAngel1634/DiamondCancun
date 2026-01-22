<?php
require 'db.php';
$db = new AdminDB();
$conn = $db->connect();

// Iniciar sesión para mensajes flash
session_start();

// Manejo de acciones
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Procesar formularios POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar y validar datos
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
    $duracion = filter_input(INPUT_POST, 'duracion', FILTER_VALIDATE_INT);
    $profundidad = filter_input(INPUT_POST, 'profundidad', FILTER_VALIDATE_INT);
    $guia = isset($_POST['guia']) ? 1 : 0;
    
    // Validar datos requeridos
    if (empty($nombre) || empty($descripcion) || $duracion === false || $profundidad === false) {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Datos inválidos o incompletos'];
        header("Location: actividades.php?action=".$action.($id ? "&id=$id" : ""));
        exit;
    }
    
    try {
        if ($action === 'create') {
            // Crear nueva actividad - sin especificar ID
            $sql = "INSERT INTO Actividad (Nombre, Descripcion, Duracion, Profundidad_Max, Requiere_Guia) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $duracion, $profundidad, $guia]);
            
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Actividad creada correctamente'];
        } elseif ($action === 'edit' && $id > 0) {
            // Actualizar actividad existente
            $sql = "UPDATE Actividad SET 
                    Nombre = ?, 
                    Descripcion = ?, 
                    Duracion = ?, 
                    Profundidad_Max = ?, 
                    Requiere_Guia = ? 
                    WHERE Id_Actividad = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $duracion, $profundidad, $guia, $id]);
            
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Actividad actualizada correctamente'];
        }
        header("Location: actividades.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Error: ' . $e->getMessage()];
        header("Location: actividades.php?action=".$action.($id ? "&id=$id" : ""));
        exit;
    }
} 

// Procesar eliminación
elseif (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        try {
            $stmt = $conn->prepare("DELETE FROM Actividad WHERE Id_Actividad = ?");
            $stmt->execute([$id]);
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Actividad eliminada correctamente'];
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Error al eliminar: ' . $e->getMessage()];
        }
    }
    header("Location: actividades.php");
    exit;
}

// Obtener todas las actividades
$actividades = [];
try {
    $stmt = $conn->query("SELECT * FROM Actividad");
    $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Error al cargar actividades: ' . $e->getMessage()];
}

// Obtener actividad específica para edición
$actividad = null;
if ($action === 'edit' && $id > 0) {
    try {
        $stmt = $conn->prepare("SELECT * FROM Actividad WHERE Id_Actividad = ?");
        $stmt->execute([$id]);
        $actividad = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$actividad) {
            $_SESSION['flash_message'] = ['type' => 'warning', 'message' => 'Actividad no encontrada'];
            header("Location: actividades.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Error al cargar actividad: ' . $e->getMessage()];
        header("Location: actividades.php");
        exit;
    }
}

// Calcular estadísticas
$totalActividades = count($actividades);
$totalDuracion = 0;
$totalProfundidad = 0;
$requiereGuia = 0;

foreach ($actividades as $act) {
    $totalDuracion += (int)$act['Duracion'];
    $totalProfundidad += (int)$act['Profundidad_Max'];
    if ($act['Requiere_Guia']) $requiereGuia++;
}

// Promedios
$promedioDuracion = $totalActividades > 0 ? round($totalDuracion / $totalActividades, 1) : 0;
$promedioProfundidad = $totalActividades > 0 ? round($totalProfundidad / $totalActividades, 1) : 0;

// Mostrar mensajes flash
$flashMessage = '';
if (isset($_SESSION['flash_message'])) {
    $flash = $_SESSION['flash_message'];
    $flashMessage = '<div class="alert alert-'.$flash['type'].' alert-dismissible fade show" role="alert">
        '.$flash['message'].'
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    unset($_SESSION['flash_message']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Actividades</title>
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
        .btn-delete {
            transition: all 0.3s;
        }
        .btn-delete:hover {
            transform: scale(1.1);
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .action-icon {
            font-size: 1.2rem;
            margin-right: 5px;
        }
        .delete-icon {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .btn-confirm-delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            transition: all 0.3s;
        }
        .btn-confirm-delete:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: scale(1.05);
        }
        .badge-guia {
            background: #198754;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: 500;
        }
        .badge-no-guia {
            background: #6c757d;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: 500;
        }
        .duration-badge {
            background: #0dcaf0;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
        }
        .depth-badge {
            background: #6610f2;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #198754;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
    </style>
</head>
<body>
    <!-- Botón de regreso a la página anterior -->
    <button class="btn btn-outline-secondary back-btn" onclick="history.back()">
        <i class="bi bi-arrow-left"></i> Volver
    </button>

    <div class="container">
        <!-- Encabezado mejorado -->
        <div class="header text-center">
            <h1><i class="bi bi-activity"></i> Gestión de Actividades</h1>
            <p class="lead">Administra tus experiencias de buceo y actividades acuáticas</p>
        </div>
        
        <!-- Mensajes Flash -->
        <?= $flashMessage ?>

        <!-- Tarjetas de estadísticas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <h5><i class="bi bi-list-check"></i> Total Actividades</h5>
                        <h3><?= $totalActividades ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <h5><i class="bi bi-clock"></i> Duración Promedio</h5>
                        <h3>
                            <?= $totalActividades ? number_format($totalDuracion / $totalActividades) . ' min' : '-' ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <h5><i class="bi bi-person-check"></i> Requieren Guía</h5>
                        <h3>
                            <?= $requiereGuia ?> (<?= $totalActividades ? number_format(($requiereGuia / $totalActividades) * 100) . '%' : '-' ?>)
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($action === 'create' || $action === 'edit'): ?>
            <!-- Formulario de creación/edición -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><?= ($action === 'create') ? 'Nueva Actividad' : 'Editar Actividad' ?></h4>
                    <a href="actividades.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" value="<?= $actividad['Nombre'] ?? '' ?>" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3" required><?= $actividad['Descripcion'] ?? '' ?></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Duración (minutos)</label>
                                <input type="number" class="form-control" name="duracion" value="<?= $actividad['Duracion'] ?? '' ?>" required min="1">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Profundidad Máxima (metros)</label>
                                <input type="number" class="form-control" name="profundidad" value="<?= $actividad['Profundidad_Max'] ?? '' ?>" required min="1">
                            </div>
                            
                            <div class="col-12">
                                <div class="form-check form-switch d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox" name="guia" id="guia" style="width: 50px; height: 25px;" <?= ($actividad && $actividad['Requiere_Guia'] == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label ms-3" for="guia">Requiere Guía</label>
                                </div>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-save"></i> <?= ($action === 'create') ? 'Crear Actividad' : 'Actualizar Actividad' ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Barra de acciones -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="actividades.php?action=create" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Nueva Actividad
                </a>
                
                <div class="d-flex gap-2">
                    <input type="text" class="form-control" placeholder="Buscar actividades..." id="searchInput">
                    <button class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <!-- Lista de actividades -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Listado de Actividades</h4>
                    <div>
                        <span class="badge bg-primary">
                            Total: <?= count($actividades) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($actividades) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Duración</th>
                                        <th>Profundidad</th>
                                        <th>Guía</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($actividades as $act): ?>
                                    <tr>
                                        <td><?= $act['Id_Actividad'] ?></td>
                                        <td><?= $act['Nombre'] ?></td>
                                        <td><?= substr($act['Descripcion'], 0, 50) ?>...</td>
                                        <td>
                                            <span class="duration-badge">
                                                <?= $act['Duracion'] ?> min
                                            </span>
                                        </td>
                                        <td>
                                            <span class="depth-badge">
                                                <?= $act['Profundidad_Max'] ?> m
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($act['Requiere_Guia']): ?>
                                                <span class="badge-guia">
                                                    <i class="bi bi-check-circle"></i> Sí
                                                </span>
                                            <?php else: ?>
                                                <span class="badge-no-guia">
                                                    <i class="bi bi-x-circle"></i> No
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="action-buttons justify-content-end">
                                                <a href="actividades.php?action=edit&id=<?= $act['Id_Actividad'] ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil action-icon"></i> Editar
                                                </a>
                                                <button class="btn btn-sm btn-danger btn-delete" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal"
                                                        data-id="<?= $act['Id_Actividad'] ?>"
                                                        data-nombre="<?= $act['Nombre'] ?>"
                                                        data-duracion="<?= $act['Duracion'] ?> min"
                                                        data-profundidad="<?= $act['Profundidad_Max'] ?> m">
                                                    <i class="bi bi-trash action-icon delete-icon"></i> Eliminar
                                                </button>
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
                            <p class="text-muted">Crea tu primera actividad usando el botón "Nueva Actividad"</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
    
    <!-- Modal de Eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar esta actividad?</p>
                    <div class="alert alert-warning mt-3">
                        <strong>Detalles de la actividad:</strong>
                        <div class="mt-2" id="actividadDetails"></div>
                    </div>
                    <p class="fw-bold text-danger mt-3">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i> Cancelar
                    </button>
                    <a href="#" id="confirmDelete" class="btn btn-confirm-delete">
                        <i class="bi bi-trash me-1"></i> Sí, eliminar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Actualizar título de la página cuando está inactiva
        let originalTitle = document.title;
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                document.title = '¡No te vayas! 😢';
            } else {
                document.title = originalTitle;
            }
        });
        
        // Configurar el modal de eliminación
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nombre = button.getAttribute('data-nombre');
            const duracion = button.getAttribute('data-duracion');
            const profundidad = button.getAttribute('data-profundidad');
            
            // Actualizar detalles de la actividad
            document.getElementById('actividadDetails').innerHTML = `
                <div><strong>Nombre:</strong> ${nombre}</div>
                <div><strong>Duración:</strong> ${duracion}</div>
                <div><strong>Profundidad:</strong> ${profundidad}</div>
            `;
            
            // Configurar el enlace de eliminación
            document.getElementById('confirmDelete').href = `actividades.php?delete=${id}`;
        });

        // Funcionalidad de búsqueda
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>