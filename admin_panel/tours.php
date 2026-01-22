<?php
require 'db.php';
$db = new AdminDB();
$conn = $db->connect();

// Iniciar sesión para mensajes flash
session_start();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_viaje = $_POST['id_viaje'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $capacidad = $_POST['capacidad'];
    
    try {
        if ($action === 'create') {
            // SOLUCIÓN: Generar nuevo ID para tour
            $stmtMax = $conn->query("SELECT MAX(Id_Tour) AS max_id FROM Tour");
            $maxId = $stmtMax->fetch(PDO::FETCH_ASSOC)['max_id'];
            $newId = $maxId ? $maxId + 1 : 1;

            $sql = "INSERT INTO Tour (Id_Tour, Id_Viaje, Nombre, Descripcion, Precio, Capacidad) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$newId, $id_viaje, $nombre, $descripcion, $precio, $capacidad]);
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Tour creado correctamente (ID: '.$newId.')'];
        } elseif ($action === 'edit' && $id > 0) {
            $sql = "UPDATE Tour SET Id_Viaje=?, Nombre=?, Descripcion=?, Precio=?, Capacidad=? WHERE Id_Tour=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_viaje, $nombre, $descripcion, $precio, $capacidad, $id]);
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Tour actualizado correctamente'];
        }
        header("Location: tours.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Error: ' . $e->getMessage()];
        header("Location: tours.php?action=".$action.($id ? "&id=$id" : ""));
        exit;
    }
} elseif (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        // Usar sentencia preparada para eliminar (más seguro)
        $stmt = $conn->prepare("DELETE FROM Tour WHERE Id_Tour = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Tour eliminado correctamente'];
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Error al eliminar: ' . $e->getMessage()];
    }
    header("Location: tours.php");
    exit;
}

// Obtener datos
$tours = $conn->query("SELECT * FROM Tour")->fetchAll(PDO::FETCH_ASSOC);
$viajes = $conn->query("SELECT Id_Viaje FROM Viaje")->fetchAll(PDO::FETCH_COLUMN);
$tour = ($action === 'edit' && $id > 0) ? $conn->query("SELECT * FROM Tour WHERE Id_Tour = $id")->fetch(PDO::FETCH_ASSOC) : null;

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
    <title>Gestión de Tours</title>
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
        .price-tag {
            background: #198754;
            color: white;
            border-radius: 4px;
            padding: 2px 8px;
            font-weight: bold;
        }
        .capacity-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 50%;
            background: #0dcaf0;
            color: white;
            font-weight: bold;
            width: 40px;
            height: 40px;
            line-height: 30px;
            text-align: center;
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
            <h1><i class="bi bi-map"></i> Gestión de Tours</h1>
            <p class="lead">Administra tus experiencias turísticas de forma eficiente</p>
        </div>
        
        <!-- Mensajes Flash -->
        <?= $flashMessage ?>

        <!-- Tarjetas de estadísticas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <h5><i class="bi bi-list-check"></i> Total Tours</h5>
                        <h3><?= count($tours) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <h5><i class="bi bi-currency-exchange"></i> Precio Promedio</h5>
                        <h3>
                            <?php 
                                $total = 0;
                                foreach($tours as $t) $total += $t['Precio'];
                                echo count($tours) ? '$' . number_format($total/count($tours), 2) : '-';
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <h5><i class="bi bi-people"></i> Capacidad Promedio</h5>
                        <h3>
                            <?php 
                                $total = 0;
                                foreach($tours as $t) $total += $t['Capacidad'];
                                echo count($tours) ? number_format($total/count($tours)) : '-';
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($action === 'create' || $action === 'edit'): ?>
            <!-- Formulario de creación/edición -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><?= ($action === 'create') ? 'Nuevo Tour' : 'Editar Tour' ?></h4>
                    <a href="tours.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Viaje ID</label>
                                <select class="form-select" name="id_viaje" required>
                                    <?php foreach ($viajes as $vid): ?>
                                        <option value="<?= $vid ?>" <?= ($tour && $tour['Id_Viaje'] == $vid) ? 'selected' : '' ?>>
                                            <?= $vid ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" value="<?= $tour['Nombre'] ?? '' ?>" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3" required><?= $tour['Descripcion'] ?? '' ?></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Precio ($)</label>
                                <input type="number" step="0.01" class="form-control" name="precio" value="<?= $tour['Precio'] ?? '' ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Capacidad</label>
                                <input type="number" class="form-control" name="capacidad" value="<?= $tour['Capacidad'] ?? '' ?>" required>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-save"></i> <?= ($action === 'create') ? 'Crear Tour' : 'Actualizar Tour' ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Barra de acciones -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="tours.php?action=create" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Nuevo Tour
                </a>
                
                <div class="d-flex gap-2">
                    <input type="text" class="form-control" placeholder="Buscar tours..." id="searchInput">
                    <button class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <!-- Lista de tours -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Listado de Tours</h4>
                    <div>
                        <span class="badge bg-primary">
                            Total: <?= count($tours) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($tours) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Viaje ID</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Precio</th>
                                        <th>Capacidad</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tours as $tour): ?>
                                    <tr>
                                        <td><?= $tour['Id_Tour'] ?></td>
                                        <td><?= $tour['Id_Viaje'] ?></td>
                                        <td><?= $tour['Nombre'] ?></td>
                                        <td><?= substr($tour['Descripcion'], 0, 50) ?>...</td>
                                        <td>
                                            <span class="price-tag">
                                                $<?= number_format($tour['Precio'], 2) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="capacity-badge">
                                                <?= $tour['Capacidad'] ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="action-buttons justify-content-end">
                                                <a href="tours.php?action=edit&id=<?= $tour['Id_Tour'] ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil action-icon"></i> Editar
                                                </a>
                                                <button class="btn btn-sm btn-danger btn-delete" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal"
                                                        data-id="<?= $tour['Id_Tour'] ?>"
                                                        data-nombre="<?= $tour['Nombre'] ?>"
                                                        data-precio="$<?= number_format($tour['Precio'], 2) ?>">
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
                            <h4 class="mt-3">No se encontraron tours</h4>
                            <p class="text-muted">Crea tu primer tour usando el botón "Nuevo Tour"</p>
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
                    <p>¿Estás seguro de que deseas eliminar este tour?</p>
                    <div class="alert alert-warning mt-3">
                        <strong>Detalles del tour:</strong>
                        <div class="mt-2" id="tourDetails"></div>
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
            const precio = button.getAttribute('data-precio');
            
            // Actualizar detalles del tour
            document.getElementById('tourDetails').innerHTML = `
                <div><strong>Nombre:</strong> ${nombre}</div>
                <div><strong>Precio:</strong> ${precio}</div>
            `;
            
            // Configurar el enlace de eliminación
            document.getElementById('confirmDelete').href = `tours.php?delete=${id}`;
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