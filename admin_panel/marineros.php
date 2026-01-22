<?php
// marineros.php
require_once 'db.php';

$db = new AdminDB();
$conn = $db->connect();

// Operaciones CRUD
$mensaje = '';
$mensaje_tipo = 'info';
$marinero_editar = null;

// Crear o Actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $nombre = $_POST['nombre'] ?? '';
    $apellido_p = $_POST['apellido_p'] ?? '';
    $apellido_m = $_POST['apellido_m'] ?? '';
    $edad = $_POST['edad'] ?? null;
    $sexo = $_POST['sexo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $rfc = $_POST['rfc'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $sueldo = $_POST['sueldo'] ?? 0;
    $certificado = $_POST['certificado'] ?? '';

    try {
        if ($id > 0) {
            // Actualizar
            $stmt = $conn->prepare("UPDATE Marinero SET 
                Nombre = ?,
                Apellido_P = ?,
                Apellido_M = ?,
                Edad = ?,
                Sexo = ?,
                Telefono = ?,
                RFC = ?,
                Correo = ?,
                Sueldo = ?,
                Certificado_Nautico = ?
                WHERE Id_Marinero = ?");
                
            $stmt->execute([
                $nombre, $apellido_p, $apellido_m, $edad, $sexo,
                $telefono, $rfc, $correo, $sueldo, $certificado, $id
            ]);
            $mensaje = "Marinero actualizado correctamente";
            $mensaje_tipo = 'success';
        } else {
            // SOLUCIÓN: Generar nuevo ID para marinero
            $stmtMax = $conn->query("SELECT MAX(Id_Marinero) AS max_id FROM Marinero");
            $maxId = $stmtMax->fetch(PDO::FETCH_ASSOC)['max_id'];
            $newId = $maxId ? $maxId + 1 : 1;

            // Crear nuevo marinero con ID generado
            $stmt = $conn->prepare("INSERT INTO Marinero (
                Id_Marinero, Nombre, Apellido_P, Apellido_M, Edad, Sexo,
                Telefono, RFC, Correo, Sueldo, Certificado_Nautico
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $newId, $nombre, $apellido_p, $apellido_m, $edad, $sexo,
                $telefono, $rfc, $correo, $sueldo, $certificado
            ]);
            $mensaje = "Marinero creado correctamente (ID: $newId)";
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
        $stmt = $conn->prepare("DELETE FROM Marinero WHERE Id_Marinero = ?");
        $stmt->execute([$id]);
        $mensaje = "Marinero eliminado correctamente";
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
        $stmt = $conn->prepare("SELECT * FROM Marinero WHERE Id_Marinero = ?");
        $stmt->execute([$id]);
        $marinero_editar = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $mensaje = "Error al cargar datos: " . $e->getMessage();
        $mensaje_tipo = 'danger';
    }
}

// Obtener parámetros de búsqueda
$filtro = $_GET['filtro'] ?? '';
$campo = $_GET['campo'] ?? 'Nombre';

// Consulta base con filtros
$sql = "SELECT * FROM Marinero";
$params = [];

// Aplicar filtros si existen
if (!empty($filtro)) {
    $sql .= " WHERE $campo LIKE ?";
    $params[] = "%$filtro%";
}

// Obtener marineros con filtros
$marineros = [];
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $marineros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensaje = "Error al cargar marineros: " . $e->getMessage();
    $mensaje_tipo = 'danger';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Marineros - Diamond Bright</title>
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
            <li><a href="catamaranes.php"><i class="bi bi-ship"></i> <span>Catamaranes</span></a></li>
            <li><a href="capitanes.php"><i class="bi bi-person-badge"></i> <span>Capitanes</span></a></li>
            <li><a href="marineros.php" class="active"><i class="bi bi-people"></i> <span>Marineros</span></a></li>
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
                <h2 class="mb-0"><i class="bi bi-people me-2"></i>Administración de Marineros</h2>
                <?php if ($marinero_editar): ?>
                    <span class="badge bg-warning text-dark status-badge">
                        <i class="bi bi-pencil"></i> Editando Marinero #<?= $marinero_editar['Id_Marinero'] ?>
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
            <div class="card mb-4 <?= $marinero_editar ? 'edit-mode' : '' ?>">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-person-plus me-2"></i>
                        <?= $marinero_editar ? 'Editar Marinero' : 'Agregar Nuevo Marinero' ?>
                    </h5>
                    <?php if ($marinero_editar): ?>
                        <span class="badge bg-white text-dark">ID: <?= $marinero_editar['Id_Marinero'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $marinero_editar['Id_Marinero'] ?? '' ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label required">Nombre</label>
                                <input type="text" class="form-control" name="nombre" 
                                    value="<?= htmlspecialchars($marinero_editar['Nombre'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" name="apellido_p" 
                                    value="<?= htmlspecialchars($marinero_editar['Apellido_P'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control" name="apellido_m" 
                                    value="<?= htmlspecialchars($marinero_editar['Apellido_M'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Edad</label>
                                <input type="number" class="form-control" name="edad" 
                                    value="<?= htmlspecialchars($marinero_editar['Edad'] ?? '') ?>" min="18" max="70">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Sexo</label>
                                <select class="form-select" name="sexo">
                                    <option value="">Seleccionar</option>
                                    <option value="M" <?= ($marinero_editar && $marinero_editar['Sexo'] == 'M') ? 'selected' : '' ?>>Masculino</option>
                                    <option value="F" <?= ($marinero_editar && $marinero_editar['Sexo'] == 'F') ? 'selected' : '' ?>>Femenino</option>
                                    <option value="O" <?= ($marinero_editar && $marinero_editar['Sexo'] == 'O') ? 'selected' : '' ?>>Otro</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" name="telefono" 
                                    value="<?= htmlspecialchars($marinero_editar['Telefono'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">RFC</label>
                                <input type="text" class="form-control" name="rfc" 
                                    value="<?= htmlspecialchars($marinero_editar['RFC'] ?? '') ?>" maxlength="13">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Correo</label>
                                <input type="email" class="form-control" name="correo" 
                                    value="<?= htmlspecialchars($marinero_editar['Correo'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Sueldo (MXN)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="sueldo" step="0.01"
                                        value="<?= htmlspecialchars($marinero_editar['Sueldo'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Certificado Náutico</label>
                                <input type="text" class="form-control" name="certificado" 
                                    value="<?= htmlspecialchars($marinero_editar['Certificado_Nautico'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Guardar
                                </button>
                                
                                <?php if ($marinero_editar): ?>
                                    <?php
                                    // Mantener parámetros de búsqueda para cancelar
                                    $queryParams = http_build_query([
                                        'filtro' => $_GET['filtro'] ?? '',
                                        'campo' => $_GET['campo'] ?? ''
                                    ]);
                                    ?>
                                    <a href="marineros.php?<?= $queryParams ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-x me-1"></i> Cancelar
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($marinero_editar): ?>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi bi-trash me-1"></i> Eliminar Marinero
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
                                    placeholder="Buscar marineros..." 
                                    value="<?= htmlspecialchars($_GET['filtro'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="campo">
                                <option value="Nombre" <?= ($_GET['campo'] ?? '') === 'Nombre' ? 'selected' : '' ?>>Nombre</option>
                                <option value="Apellido_P" <?= ($_GET['campo'] ?? '') === 'Apellido_P' ? 'selected' : '' ?>>Apellido Paterno</option>
                                <option value="Apellido_M" <?= ($_GET['campo'] ?? '') === 'Apellido_M' ? 'selected' : '' ?>>Apellido Materno</option>
                                <option value="Telefono" <?= ($_GET['campo'] ?? '') === 'Telefono' ? 'selected' : '' ?>>Teléfono</option>
                                <option value="RFC" <?= ($_GET['campo'] ?? '') === 'RFC' ? 'selected' : '' ?>>RFC</option>
                                <option value="Correo" <?= ($_GET['campo'] ?? '') === 'Correo' ? 'selected' : '' ?>>Correo</option>
                                <option value="Certificado_Nautico" <?= ($_GET['campo'] ?? '') === 'Certificado_Nautico' ? 'selected' : '' ?>>Certificado</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                            <a href="marineros.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-repeat"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Marineros -->
            <div class="card">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Lista de Marineros</h5>
                    <span class="badge bg-light text-dark">
                        <?= count($marineros) ?> <?= count($marineros) === 1 ? 'Registro' : 'Registros' ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if (!empty($marineros)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Apellidos</th>
                                        <th>Edad</th>
                                        <th>Sexo</th>
                                        <th>Teléfono</th>
                                        <th>Sueldo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($marineros as $marinero): ?>
                                        <tr>
                                            <td><?= $marinero['Id_Marinero'] ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($marinero['Nombre']) ?></strong>
                                                <?php if ($marinero_editar && $marinero_editar['Id_Marinero'] == $marinero['Id_Marinero']): ?>
                                                    <span class="badge bg-warning text-dark ms-2">Editando</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($marinero['Apellido_P']) ?> <?= htmlspecialchars($marinero['Apellido_M']) ?>
                                            </td>
                                            <td><?= $marinero['Edad'] ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?= $marinero['Sexo'] == 'M' ? 'bg-primary' : '' ?>
                                                    <?= $marinero['Sexo'] == 'F' ? 'bg-danger' : '' ?>
                                                    <?= $marinero['Sexo'] == 'O' ? 'bg-info' : '' ?>">
                                                    <?= match($marinero['Sexo']) {
                                                        'M' => 'Masculino',
                                                        'F' => 'Femenino',
                                                        'O' => 'Otro',
                                                        default => $marinero['Sexo']
                                                    } ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($marinero['Telefono']) ?></td>
                                            <td><?= $marinero['Sueldo'] ? '$' . number_format($marinero['Sueldo'], 2) : '' ?></td>
                                            <td>
                                                <?php
                                                // Mantener parámetros de búsqueda en URLs
                                                $queryParams = http_build_query([
                                                    'filtro' => $_GET['filtro'] ?? '',
                                                    'campo' => $_GET['campo'] ?? ''
                                                ]);
                                                ?>
                                                <div class="action-buttons">
                                                    <a href="marineros.php?editar=<?= $marinero['Id_Marinero'] ?>&<?= $queryParams ?>" 
                                                       class="btn btn-sm btn-warning btn-action"
                                                       title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger btn-action" 
                                                        title="Eliminar"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal"
                                                        data-id="<?= $marinero['Id_Marinero'] ?>">
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
                            <i class="bi bi-people"></i>
                            <h4>No se encontraron marineros</h4>
                            <p class="text-muted">Intenta cambiar tus filtros o agregar un nuevo marinero</p>
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
                    <p>¿Estás seguro de que deseas eliminar este marinero?</p>
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
                const deleteUrl = `marineros.php?eliminar=${id}&filtro=${filtro}&campo=${campo}`;
                confirmDeleteBtn.href = deleteUrl;
            });
        });
    </script>
</body>
</html>