<?php
// catamaranes.php
require_once 'db.php';

$db = new AdminDB();
$conn = $db->connect();

// Operaciones CRUD
$mensaje = '';
$mensaje_tipo = 'info'; // info, success, danger
$catamaran_editar = null;

// Obtener parámetros de búsqueda
$filtro = $_GET['filtro'] ?? '';
$campo = $_GET['campo'] ?? 'Matricula';

// Obtener capitanes para el dropdown
$capitanes = [];
try {
    $stmt = $conn->query("SELECT Id_Capitan, Nombre, Apellido_P FROM Capitan");
    $capitanes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensaje = "Error al cargar capitanes: " . $e->getMessage();
    $mensaje_tipo = 'danger';
}

// Crear o Actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = $_POST['matricula'];
    $id_capitan = $_POST['id_capitan'];
    $modelo = $_POST['modelo'] ?? '';
    $color = $_POST['color'] ?? '';
    $seguro = $_POST['seguro'] ?? '';
    $capacidad = $_POST['capacidad'] ?? null;
    $equipo = $_POST['equipo'] ?? '';
    $seguridad = $_POST['seguridad'] ?? '';
    $combustible = $_POST['combustible'] ?? '';

    try {
        if (!empty($_POST['matricula_original'])) {
            // Actualizar
            $stmt = $conn->prepare("UPDATE Catamaran SET 
                Matricula = ?,
                Id_Capitan = ?,
                Modelo = ?,
                Color = ?,
                Seguro = ?,
                Capacidad = ?,
                Equipo = ?,
                Seguridad = ?,
                Combustible = ?
                WHERE Matricula = ?");
                
            $stmt->execute([
                $matricula, $id_capitan, $modelo, $color, 
                $seguro, $capacidad, $equipo, $seguridad, 
                $combustible, $_POST['matricula_original']
            ]);
            $mensaje = "Catamarán actualizado correctamente";
            $mensaje_tipo = 'success';
        } else {
            // Crear nuevo
            $stmt = $conn->prepare("INSERT INTO Catamaran (
                Matricula, Id_Capitan, Modelo, Color, Seguro, 
                Capacidad, Equipo, Seguridad, Combustible
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $matricula, $id_capitan, $modelo, $color, 
                $seguro, $capacidad, $equipo, $seguridad, $combustible
            ]);
            $mensaje = "Catamarán creado correctamente";
            $mensaje_tipo = 'success';
        }
    } catch (PDOException $e) {
        $mensaje = "Error: " . $e->getMessage();
        $mensaje_tipo = 'danger';
    }
}

// Eliminar
if (isset($_GET['eliminar'])) {
    $matricula = $_GET['eliminar'];
    try {
        $stmt = $conn->prepare("DELETE FROM Catamaran WHERE Matricula = ?");
        $stmt->execute([$matricula]);
        $mensaje = "Catamarán eliminado correctamente";
        $mensaje_tipo = 'success';
    } catch (PDOException $e) {
        $mensaje = "Error al eliminar: " . $e->getMessage();
        $mensaje_tipo = 'danger';
    }
}

// Editar (cargar datos)
if (isset($_GET['editar'])) {
    $matricula = $_GET['editar'];
    try {
        $stmt = $conn->prepare("SELECT * FROM Catamaran WHERE Matricula = ?");
        $stmt->execute([$matricula]);
        $catamaran_editar = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $mensaje = "Error al cargar datos: " . $e->getMessage();
        $mensaje_tipo = 'danger';
    }
}

// Consulta base con filtros
$sql = "SELECT c.*, cap.Nombre AS nombre_capitan, cap.Apellido_P 
        FROM Catamaran c 
        JOIN Capitan cap ON c.Id_Capitan = cap.Id_Capitan";
$params = [];

// Aplicar filtros si existen
if (!empty($filtro)) {
    $sql .= " WHERE $campo LIKE ?";
    $params[] = "%$filtro%";
}

// Obtener catamaranes con filtros
$catamaranes = [];
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $catamaranes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensaje = "Error al cargar catamaranes: " . $e->getMessage();
    $mensaje_tipo = 'danger';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Catamaranes - Diamond Bright</title>
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
            <li><a href="catamaranes.php" class="active"><i class="bi bi-ship"></i> <span>Catamaranes</span></a></li>
            <li><a href="capitanes.php"><i class="bi bi-person-badge"></i> <span>Capitanes</span></a></li>
            <li><a href="marineros.php"><i class="bi bi-people"></i> <span>Marineros</span></a></li>
            <li><a href="clientes.php"><i class="bi bi-person-lines-fill"></i> <span>Clientes</span></a></li>
            <li><a href="reservas.php"><i class="bi bi-calendar-check"></i> <span>Reservas</span></a></li>
            <li><a href="confirmaciones.php"><i class="bi bi-check-circle"></i> <span>Confirmaciones</span></a></li>
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
                <h2 class="mb-0"><i class="bi bi-ship me-2"></i>Administración de Catamaranes</h2>
                <?php if ($catamaran_editar): ?>
                    <span class="badge bg-warning text-dark status-badge">
                        <i class="bi bi-pencil"></i> Editando Catamarán: <?= $catamaran_editar['Matricula'] ?>
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
            <div class="card mb-4 <?= $catamaran_editar ? 'edit-mode' : '' ?>">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        <?= $catamaran_editar ? 'Editar Catamarán' : 'Agregar Nuevo Catamarán' ?>
                    </h5>
                    <?php if ($catamaran_editar): ?>
                        <span class="badge bg-white text-dark">Matrícula: <?= $catamaran_editar['Matricula'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php if ($catamaran_editar): ?>
                            <input type="hidden" name="matricula_original" value="<?= htmlspecialchars($catamaran_editar['Matricula']) ?>">
                        <?php endif; ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Matrícula</label>
                                <input type="text" class="form-control" name="matricula" 
                                    value="<?= htmlspecialchars($catamaran_editar['Matricula'] ?? '') ?>" required
                                    maxlength="20">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label required">Capitán</label>
                                <select class="form-select" name="id_capitan" required>
                                    <option value="">Seleccionar capitán</option>
                                    <?php foreach ($capitanes as $capitan): ?>
                                        <option value="<?= $capitan['Id_Capitan'] ?>"
                                            <?= ($catamaran_editar && $catamaran_editar['Id_Capitan'] == $capitan['Id_Capitan']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($capitan['Nombre']) ?> <?= htmlspecialchars($capitan['Apellido_P']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Modelo</label>
                                <input type="text" class="form-control" name="modelo" 
                                    value="<?= htmlspecialchars($catamaran_editar['Modelo'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Color</label>
                                <input type="text" class="form-control" name="color" 
                                    value="<?= htmlspecialchars($catamaran_editar['Color'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Combustible</label>
                                <input type="text" class="form-control" name="combustible" 
                                    value="<?= htmlspecialchars($catamaran_editar['Combustible'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Seguro</label>
                                <input type="text" class="form-control" name="seguro" 
                                    value="<?= htmlspecialchars($catamaran_editar['Seguro'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Capacidad</label>
                                <input type="number" class="form-control" name="capacidad" 
                                    value="<?= htmlspecialchars($catamaran_editar['Capacidad'] ?? '') ?>" min="1">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Equipo</label>
                                <textarea class="form-control" name="equipo" rows="2"><?= htmlspecialchars($catamaran_editar['Equipo'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Medidas de Seguridad</label>
                                <textarea class="form-control" name="seguridad" rows="2"><?= htmlspecialchars($catamaran_editar['Seguridad'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Guardar
                                </button>
                                
                                <?php if ($catamaran_editar): ?>
                                    <?php
                                    // Mantener parámetros de búsqueda para cancelar
                                    $queryParams = http_build_query([
                                        'filtro' => $_GET['filtro'] ?? '',
                                        'campo' => $_GET['campo'] ?? ''
                                    ]);
                                    ?>
                                    <a href="catamaranes.php?<?= $queryParams ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-x me-1"></i> Cancelar
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($catamaran_editar): ?>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi bi-trash me-1"></i> Eliminar Catamarán
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
                                    placeholder="Buscar catamaranes..." 
                                    value="<?= htmlspecialchars($_GET['filtro'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="campo">
                                <option value="Matricula" <?= ($_GET['campo'] ?? '') === 'Matricula' ? 'selected' : '' ?>>Matrícula</option>
                                <option value="Modelo" <?= ($_GET['campo'] ?? '') === 'Modelo' ? 'selected' : '' ?>>Modelo</option>
                                <option value="Color" <?= ($_GET['campo'] ?? '') === 'Color' ? 'selected' : '' ?>>Color</option>
                                <option value="Combustible" <?= ($_GET['campo'] ?? '') === 'Combustible' ? 'selected' : '' ?>>Combustible</option>
                                <option value="Seguro" <?= ($_GET['campo'] ?? '') === 'Seguro' ? 'selected' : '' ?>>Seguro</option>
                                <option value="Capacidad" <?= ($_GET['campo'] ?? '') === 'Capacidad' ? 'selected' : '' ?>>Capacidad</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                            <a href="catamaranes.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-repeat"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Catamaranes -->
            <div class="card">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Lista de Catamaranes</h5>
                    <span class="badge bg-light text-dark">
                        <?= count($catamaranes) ?> <?= count($catamaranes) === 1 ? 'Registro' : 'Registros' ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if (!empty($catamaranes)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Matrícula</th>
                                        <th>Capitán</th>
                                        <th>Modelo</th>
                                        <th>Color</th>
                                        <th>Capacidad</th>
                                        <th>Combustible</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($catamaranes as $cat): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($cat['Matricula']) ?></strong>
                                                <?php if ($catamaran_editar && $catamaran_editar['Matricula'] == $cat['Matricula']): ?>
                                                    <span class="badge bg-warning text-dark ms-2">Editando</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($cat['nombre_capitan']) ?> <?= htmlspecialchars($cat['Apellido_P']) ?></td>
                                            <td><?= htmlspecialchars($cat['Modelo']) ?></td>
                                            <td>
                                                <span class="badge" style="background-color: <?= htmlspecialchars($cat['Color']) ?>; color: #333;">
                                                    <?= htmlspecialchars($cat['Color']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($cat['Capacidad']) ?></td>
                                            <td><?= htmlspecialchars($cat['Combustible']) ?></td>
                                            <td>
                                                <?php
                                                // Mantener parámetros de búsqueda en URLs
                                                $queryParams = http_build_query([
                                                    'filtro' => $_GET['filtro'] ?? '',
                                                    'campo' => $_GET['campo'] ?? ''
                                                ]);
                                                ?>
                                                <div class="action-buttons">
                                                    <a href="catamaranes.php?editar=<?= $cat['Matricula'] ?>&<?= $queryParams ?>" 
                                                       class="btn btn-sm btn-warning btn-action"
                                                       title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger btn-action" 
                                                        title="Eliminar"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal"
                                                        data-id="<?= $cat['Matricula'] ?>">
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
                            <i class="bi bi-ship"></i>
                            <h4>No se encontraron catamaranes</h4>
                            <p class="text-muted">Intenta cambiar tus filtros o agregar un nuevo catamarán</p>
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
                    <p>¿Estás seguro de que deseas eliminar este catamarán?</p>
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
                const deleteUrl = `catamaranes.php?eliminar=${id}&filtro=${filtro}&campo=${campo}`;
                confirmDeleteBtn.href = deleteUrl;
            });
        });
    </script>
</body>
</html>