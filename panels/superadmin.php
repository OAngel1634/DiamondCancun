<?php
// Las estadísticas ya fueron cargadas en dashboard.php
$stats = $stats ?? [];
?>
<div class="panel superadmin-panel">
    <div class="page-header">
        <h2>Resumen General</h2>
        <p class="text-muted">Vista general del sistema</p>
    </div>
    
    <!-- Tarjetas de estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?= $stats['total_users'] ?? '—' ?></h3>
                <p>Usuarios totales</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🧑‍💼</div>
            <div class="stat-info">
                <h3><?= $stats['total_clients'] ?? '—' ?></h3>
                <p>Clientes registrados</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⚓</div>
            <div class="stat-info">
                <h3><?= $stats['active_tours'] ?? '—' ?></h3>
                <p>Personal activo</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏢</div>
            <div class="stat-info">
                <h3><?= $stats['total_agencies'] ?? '—' ?></h3>
                <p>Agencias asociadas</p>
            </div>
        </div>
    </div>
    
    <!-- Tablas de actividad reciente -->
    <div class="recent-activity">
        <div class="card">
            <div class="card-header">
                <h3>Últimos usuarios registrados</h3>
                <a href="/admin/usuarios.php" class="btn-link">Ver todos →</a>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Registro</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $pdo = getConnection();
                            $stmt = $pdo->query("SELECT email, user_role, created_at, is_active FROM system_users ORDER BY created_at DESC LIMIT 5");
                            while ($row = $stmt->fetch()):
                        ?>
                        <tr>
                            <td><?= e($row['email']) ?></td>
                            <td><span class="badge-role"><?= e($row['user_role']) ?></span></td>
                            <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                            <td><?= $row['is_active'] ? '✅ Activo' : '❌ Inactivo' ?></td>
                        </tr>
                        <?php endwhile; } catch (Exception $e) { ?>
                        <tr><td colspan="4">Error al cargar datos</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Acciones rápidas -->
    <div class="quick-actions">
        <h3>Acciones rápidas</h3>
        <div class="actions-grid">
            <a href="/admin/usuarios/nuevo.php" class="action-btn">
                <span class="icon">➕</span> Nuevo Usuario
            </a>
            <a href="/admin/reportes/generar.php" class="action-btn">
                <span class="icon">📊</span> Generar Reporte
            </a>
            <a href="/admin/configuracion.php" class="action-btn">
                <span class="icon">⚙️</span> Configuración
            </a>
        </div>
    </div>
</div>