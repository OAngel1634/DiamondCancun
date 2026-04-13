<?php
$rol = $_SESSION['AUTH_USER']['rol'] ?? 'customer';
$nombre = $_SESSION['AUTH_USER']['nombre'] ?? 'Usuario';
$email = $_SESSION['AUTH_USER']['email'] ?? '';
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="/assets/imagenes/logo.jpg" alt="Diamond Bright" class="sidebar-logo">
        <h2>Diamond Bright</h2>
    </div>
    
    <div class="user-profile-mini">
        <div class="avatar"><?= strtoupper(substr($nombre, 0, 1)) ?></div>
        <div class="user-info">
            <span class="user-name"><?= e($nombre) ?></span>
            <span class="user-role"><?= e($rol) ?></span>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <!-- Enlaces comunes -->
            <li><a href="/dashboard.php"><span class="icon">🏠</span> Inicio</a></li>
            
            <!-- Super Admin / Admin -->
            <?php if (in_array($rol, ['super_admin', 'admin'])): ?>
                <li class="nav-section">Administración</li>
                <li><a href="/admin/usuarios.php"><span class="icon">👥</span> Usuarios</a></li>
                <li><a href="/admin/clientes.php"><span class="icon">🧑‍💼</span> Clientes</a></li>
                <li><a href="/admin/agencias.php"><span class="icon">🏢</span> Agencias</a></li>
                <li><a href="/admin/staff.php"><span class="icon">⚓</span> Personal</a></li>
                <li class="nav-section">Reportes</li>
                <li><a href="/admin/reportes/ventas.php"><span class="icon">💰</span> Ventas</a></li>
                <li><a href="/admin/reportes/reservas.php"><span class="icon">📅</span> Reservas</a></li>
                <li><a href="/admin/configuracion.php"><span class="icon">⚙️</span> Configuración</a></li>
            <?php endif; ?>
            
            <!-- Customer -->
            <?php if ($rol === 'customer'): ?>
                <li class="nav-section">Mis Actividades</li>
                <li><a href="/cliente/reservas.php"><span class="icon">📋</span> Mis Reservas</a></li>
                <li><a href="/cliente/nueva-reserva.php"><span class="icon">➕</span> Nueva Reserva</a></li>
                <li><a href="/cliente/facturas.php"><span class="icon">🧾</span> Facturas</a></li>
                <li><a href="/cliente/perfil.php"><span class="icon">👤</span> Mi Perfil</a></li>
            <?php endif; ?>
            
            <!-- Staff (capitán/marine) -->
            <?php if (in_array($rol, ['captain', 'marine'])): ?>
                <li class="nav-section">Tripulación</li>
                <li><a href="/staff/horarios.php"><span class="icon">⏰</span> Horarios</a></li>
                <li><a href="/staff/embarcaciones.php"><span class="icon">🚤</span> Embarcaciones</a></li>
                <li><a href="/staff/partes.php"><span class="icon">📝</span> Partes de trabajo</a></li>
                <li><a href="/staff/incidencias.php"><span class="icon">⚠️</span> Incidencias</a></li>
            <?php endif; ?>
            
            <!-- Agencias -->
            <?php if (in_array($rol, ['agency_admin', 'agency_agent', 'informal_agent'])): ?>
                <li class="nav-section">Gestión Agencia</li>
                <li><a href="/agencia/reservas.php"><span class="icon">📅</span> Reservas</a></li>
                <li><a href="/agencia/clientes.php"><span class="icon">🧑‍🤝‍🧑</span> Clientes</a></li>
                <li><a href="/agencia/comisiones.php"><span class="icon">💵</span> Comisiones</a></li>
                <?php if ($rol === 'agency_admin'): ?>
                    <li><a href="/agencia/agentes.php"><span class="icon">👔</span> Agentes</a></li>
                <?php endif; ?>
            <?php endif; ?>
            
            <li class="nav-separator"></li>
            <li><a href="/perfil.php"><span class="icon">⚙️</span> Mi Cuenta</a></li>
            <li><a href="/logout.php"><span class="icon">🚪</span> Cerrar Sesión</a></li>
        </ul>
    </nav>
</aside>