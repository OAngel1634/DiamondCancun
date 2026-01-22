<?php
require 'db.php';
$db = new AdminDB();
$conn = $db->connect();

session_start(); // Añadido para manejar mensajes flash

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_field = isset($_GET['filter_field']) ? $_GET['filter_field'] : 'all';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $id = $_POST['delete'];
        try {
            $stmt = $conn->prepare("DELETE FROM Viaje WHERE Id_Viaje = ?");
            $stmt->execute([$id]);
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Viaje eliminado correctamente'];
            header("Location: viajes.php");
            exit;
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Error al eliminar el viaje: ' . $e->getMessage()];
            header("Location: viajes.php");
            exit;
        }
    } else {
        $confirmacion = $_POST['confirmacion'];
        $direccion = $_POST['direccion'];
        $fecha = $_POST['fecha'];
        $hora_salida = $_POST['hora_salida'];
        $hora_fin = $_POST['hora_fin'];
        $matricula = $_POST['matricula'];
        $lugares = $_POST['lugares'];
        $calle = $_POST['calle'];
        $edificio = $_POST['edificio'];
        
        if ($action === 'create') {
            try {
                // SOLUCIÓN: Generar nuevo ID para viaje
                $stmtMax = $conn->query("SELECT MAX(Id_Viaje) AS max_id FROM Viaje");
                $maxId = $stmtMax->fetch(PDO::FETCH_ASSOC)['max_id'];
                $newId = $maxId ? $maxId + 1 : 1;
                
                $sql = "INSERT INTO Viaje (
                    Id_Viaje, No_Confirmacion, Direccion, Fecha, 
                    Hora_Salida, Hora_Finalizacion, Matricula, 
                    Lugares_Disponibles, Calle, No_Edificio
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    $newId, $confirmacion, $direccion, $fecha, 
                    $hora_salida, $hora_fin, $matricula, 
                    $lugares, $calle, $edificio
                ]);
                
                $_SESSION['flash_message'] = [
                    'type' => 'success', 
                    'message' => "Viaje creado correctamente (ID: $newId)"
                ];
                header("Location: viajes.php");
                exit;
            } catch (PDOException $e) {
                $_SESSION['flash_message'] = [
                    'type' => 'danger', 
                    'message' => 'Error al crear el viaje: ' . $e->getMessage()
                ];
            }
        } elseif ($action === 'edit' && $id > 0) {
            try {
                $sql = "UPDATE Viaje SET 
                        No_Confirmacion=?, 
                        Direccion=?, 
                        Fecha=?, 
                        Hora_Salida=?, 
                        Hora_Finalizacion=?, 
                        Matricula=?, 
                        Lugares_Disponibles=?, 
                        Calle=?, 
                        No_Edificio=? 
                        WHERE Id_Viaje=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    $confirmacion, $direccion, $fecha, 
                    $hora_salida, $hora_fin, $matricula, 
                    $lugares, $calle, $edificio, $id
                ]);
                $_SESSION['flash_message'] = [
                    'type' => 'success', 
                    'message' => 'Viaje actualizado correctamente'
                ];
                header("Location: viajes.php");
                exit;
            } catch (PDOException $e) {
                $_SESSION['flash_message'] = [
                    'type' => 'danger', 
                    'message' => 'Error al actualizar el viaje: ' . $e->getMessage()
                ];
            }
        }
    }
}

// Obtener datos con búsqueda y filtro
$sql = "SELECT * FROM Viaje";
$params = [];

if (!empty($search)) {
    $searchTerm = "%$search%";
    
    switch ($filter_field) {
        case 'id':
            $sql .= " WHERE Id_Viaje = ?";
            $params[] = $search;
            break;
        case 'confirmacion':
            $sql .= " WHERE No_Confirmacion LIKE ?";
            $params[] = $searchTerm;
            break;
        case 'fecha':
            $sql .= " WHERE Fecha LIKE ?";
            $params[] = $searchTerm;
            break;
        case 'matricula':
            $sql .= " WHERE Matricula LIKE ?";
            $params[] = $searchTerm;
            break;
        case 'lugares':
            $sql .= " WHERE Lugares_Disponibles = ?";
            $params[] = $search;
            break;
        case 'calle':
            $sql .= " WHERE Calle LIKE ?";
            $params[] = $searchTerm;
            break;
        default:
            $sql .= " WHERE 
                No_Confirmacion LIKE ? OR 
                Direccion LIKE ? OR 
                Fecha LIKE ? OR 
                Matricula LIKE ? OR 
                Calle LIKE ? OR
                Lugares_Disponibles LIKE ?";
            $params = array_fill(0, 6, $searchTerm);
            break;
    }
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$confirmaciones = $conn->query("SELECT No_Confirmacion FROM Confirmacion")->fetchAll(PDO::FETCH_COLUMN);
$matriculas = $conn->query("SELECT Matricula FROM Catamaran")->fetchAll(PDO::FETCH_COLUMN);
$viaje = ($action === 'edit' && $id > 0) ? $conn->query("SELECT * FROM Viaje WHERE Id_Viaje = $id")->fetch(PDO::FETCH_ASSOC) : null;

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
    <title>Gestión de Viajes</title>
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
            <h1><i class="bi bi-geo-alt"></i> Gestión de Viajes</h1>
            <p class="lead">Administra los viajes programados de forma eficiente</p>
        </div>
        
        <!-- Mensajes Flash -->
        <?= $flashMessage ?>

        <!-- Tarjetas de estadísticas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <h5><i class="bi bi-list-check"></i> Total Viajes</h5>
                        <h3><?= count($viajes) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <h5><i class="bi bi-check-circle"></i> Confirmaciones</h5>
                        <h3><?= count($confirmaciones) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <h5><i class="bi bi-ship"></i> Catamaranes</h5>
                        <h3><?= count($matriculas) ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($action === 'create' || $action === 'edit'): ?>
            <!-- Formulario de creación/edición -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><?= ($action === 'create') ? 'Nuevo Viaje' : 'Editar Viaje' ?></h4>
                    <a href="viajes.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Confirmación</label>
                                <select class="form-select" name="confirmacion" required>
                                    <?php foreach ($confirmaciones as $conf): ?>
                                        <option value="<?= $conf ?>" <?= ($viaje && $viaje['No_Confirmacion'] == $conf) ? 'selected' : '' ?>>
                                            <?= $conf ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Matrícula</label>
                                <select class="form-select" name="matricula" required>
                                    <?php foreach ($matriculas as $mat): ?>
                                        <option value="<?= $mat ?>" <?= ($viaje && $viaje['Matricula'] == $mat) ? 'selected' : '' ?>>
                                            <?= $mat ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Dirección</label>
                                <input type="text" class="form-control" name="direccion" value="<?= $viaje['Direccion'] ?? '' ?>" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Fecha</label>
                                <input type="date" class="form-control" name="fecha" value="<?= $viaje['Fecha'] ?? '' ?>" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Hora Salida</label>
                                <input type="time" class="form-control" name="hora_salida" value="<?= $viaje['Hora_Salida'] ?? '' ?>" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Hora Finalización</label>
                                <input type="time" class="form-control" name="hora_fin" value="<?= $viaje['Hora_Finalizacion'] ?? '' ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Lugares Disponibles</label>
                                <input type="number" class="form-control" name="lugares" value="<?= $viaje['Lugares_Disponibles'] ?? '' ?>" required min="1">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Calle</label>
                                <input type="text" class="form-control" name="calle" value="<?= $viaje['Calle'] ?? '' ?>" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Número de Edificio</label>
                                <input type="text" class="form-control" name="edificio" value="<?= $viaje['No_Edificio'] ?? '' ?>" required>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-save"></i> <?= ($action === 'create') ? 'Crear Viaje' : 'Actualizar Viaje' ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Barra de acciones y búsqueda -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="viajes.php?action=create" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Nuevo Viaje
                </a>
                
                <!-- Contenedor de filtros -->
                <div class="filter-container">
                    <form method="GET">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="search" placeholder="Buscar viajes..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                            
                            <div class="col-md-4">
                                <select class="form-select" name="filter_field">
                                    <option value="all" <?= $filter_field === 'all' ? 'selected' : '' ?>>Buscar en todos los campos</option>
                                    <option value="id" <?= $filter_field === 'id' ? 'selected' : '' ?>>ID del Viaje</option>
                                    <option value="confirmacion" <?= $filter_field === 'confirmacion' ? 'selected' : '' ?>>Número de Confirmación</option>
                                    <option value="fecha" <?= $filter_field === 'fecha' ? 'selected' : '' ?>>Fecha</option>
                                    <option value="matricula" <?= $filter_field === 'matricula' ? 'selected' : '' ?>>Matrícula</option>
                                    <option value="lugares" <?= $filter_field === 'lugares' ? 'selected' : '' ?>>Lugares Disponibles</option>
                                    <option value="calle" <?= $filter_field === 'calle' ? 'selected' : '' ?>>Calle</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary w-100" type="submit">
                                        <i class="bi bi-search"></i> Buscar
                                    </button>
                                    <?php if (!empty($search)): ?>
                                        <a href="viajes.php" class="btn btn-outline-danger">
                                            <i class="bi bi-x"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
            </div>

            <!-- Lista de viajes -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Listado de Viajes</h4>
                    <div>
                        <?php if (!empty($search)): ?>
                            <span class="badge bg-info">
                                Filtro: <?= 
                                    $filter_field === 'all' ? 'Todos los campos' : 
                                    ($filter_field === 'id' ? 'ID' : 
                                    ($filter_field === 'confirmacion' ? 'Confirmación' : 
                                    ($filter_field === 'fecha' ? 'Fecha' : 
                                    ($filter_field === 'matricula' ? 'Matrícula' : 
                                    ($filter_field === 'lugares' ? 'Lugares' : 
                                    ($filter_field === 'calle' ? 'Calle' : 'Todos')))))) 
                                ?>
                            </span>
                            <span class="badge bg-primary ms-2">
                                Resultados: <?= count($viajes) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($viajes) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Confirmación</th>
                                        <th>Fecha</th>
                                        <th>Hora Salida</th>
                                        <th>Matrícula</th>
                                        <th>Lugares</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($viajes as $viaje): ?>
                                    <tr>
                                        <td><?= $viaje['Id_Viaje'] ?></td>
                                        <td><?= $viaje['No_Confirmacion'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($viaje['Fecha'])) ?></td>
                                        <td><?= substr($viaje['Hora_Salida'], 0, 5) ?></td>
                                        <td><?= $viaje['Matricula'] ?></td>
                                        <td>
                                            <span class="badge bg-<?= ($viaje['Lugares_Disponibles'] > 10) ? 'success' : 'warning' ?>">
                                                <?= $viaje['Lugares_Disponibles'] ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="action-buttons justify-content-end">
                                                <a href="viajes.php?action=edit&id=<?= $viaje['Id_Viaje'] ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil action-icon"></i> Editar
                                                </a>
                                                <button class="btn btn-sm btn-danger btn-delete" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal"
                                                        data-id="<?= $viaje['Id_Viaje'] ?>"
                                                        data-confirmacion="<?= $viaje['No_Confirmacion'] ?>"
                                                        data-fecha="<?= date('d/m/Y', strtotime($viaje['Fecha'])) ?>"
                                                        data-hora="<?= substr($viaje['Hora_Salida'], 0, 5) ?>">
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
                            <h4 class="mt-3">No se encontraron viajes</h4>
                            <p class="text-muted"><?= !empty($search) ? 'Intenta con otro término de búsqueda' : 'Crea tu primer viaje usando el botón "Nuevo Viaje"' ?></p>
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
                    <p>¿Estás seguro de que deseas eliminar este viaje?</p>
                    <div class="alert alert-warning mt-3">
                        <strong>Detalles del viaje:</strong>
                        <div class="mt-2" id="viajeDetails"></div>
                    </div>
                    <p class="fw-bold text-danger mt-3">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i> Cancelar
                    </button>
                    <form method="POST" id="deleteForm">
                        <input type="hidden" name="delete" id="deleteId" value="">
                        <button type="submit" class="btn btn-confirm-delete">
                            <i class="bi bi-trash me-1"></i> Sí, eliminar
                        </button>
                    </form>
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
        
        // Guardar preferencias de filtro
        document.addEventListener('DOMContentLoaded', function() {
            // Guardar selección de filtro
            const filterSelect = document.querySelector('select[name="filter_field"]');
            if (filterSelect) {
                filterSelect.addEventListener('change', function() {
                    localStorage.setItem('filterPreference', this.value);
                });
                
                // Cargar preferencia guardada
                const savedFilter = localStorage.getItem('filterPreference');
                if (savedFilter) {
                    filterSelect.value = savedFilter;
                }
            }
            
            // Configurar el modal de eliminación
            const deleteModal = document.getElementById('deleteModal');
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const confirmacion = button.getAttribute('data-confirmacion');
                const fecha = button.getAttribute('data-fecha');
                const hora = button.getAttribute('data-hora');
                
                // Actualizar detalles del viaje
                document.getElementById('viajeDetails').innerHTML = `
                    <div><strong>Confirmación:</strong> ${confirmacion}</div>
                    <div><strong>Fecha:</strong> ${fecha}</div>
                    <div><strong>Hora salida:</strong> ${hora}</div>
                `;
                
                // Configurar el ID para eliminar
                document.getElementById('deleteId').value = id;
            });
        });
    </script>
</body>
</html>