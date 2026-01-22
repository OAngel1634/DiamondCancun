<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barra Lateral - Diamond Bright</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0d6efd;
            --secondary: #6c757d;
            --success: #198754;
            --info: #0dcaf0;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #212529;
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
            --content-bg: #f5f7fa;
            --card-bg: #ffffff;
            --border-color: #eaeaea;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--content-bg);
            color: var(--dark);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Sidebar */
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
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            transition: all 0.3s;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        /* Toggle button */
        .toggle-btn {
            background: transparent;
            border: none;
            color: var(--dark);
            font-size: 20px;
            cursor: pointer;
            display: none;
        }
        
        .content-container {
            max-width: 800px;
            text-align: center;
            padding: 30px;
            background: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .content-container h1 {
            margin-bottom: 20px;
            color: var(--dark);
        }
        
        .content-container p {
            color: var(--secondary);
            line-height: 1.6;
        }
        
        /* Responsive */
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
            
            .toggle-btn {
                display: block;
            }
        }
        
        @media (max-width: 576px) {
            .sidebar {
                left: -70px;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Barra Lateral -->
    <div class="sidebar">
        <div class="sidebar-header p-3">
            <h3 class="d-flex align-items-center gap-2">
                <i class="bi bi-gem"></i> 
                <span>Diamond Bright</span>
            </h3>
        </div>
        <ul class="nav-links list-unstyled">
            <li><a href="catamaranes.php"><i class="bi bi-ship"></i> <span>Catamaranes</span></a></li>
            <li><a href="capitanes.php"><i class="bi bi-person-badge"></i> <span>Capitanes</span></a></li>
            <li><a href="marineros.php"><i class="bi bi-people"></i> <span>Marineros</span></a></li>
            <li><a href="clientes.php"><i class="bi bi-person-lines-fill"></i> <span>Clientes</span></a></li>
            <li><a href="reservas.php"><i class="bi bi-calendar-check"></i> <span>Reservas</span></a></li>
            <li><a href="confirmaciones.php"><i class="bi bi-check-circle"></i> <span>Confirmaciones</span></a></li>
            <li><a href="viajes.php" class="active"><i class="bi bi-geo-alt"></i> <span>Viajes</span></a></li>
            <li><a href="tours.php"><i class="bi bi-signpost-split"></i> <span>Tours</span></a></li>
            <li><a href="actividades.php"><i class="bi bi-lightning"></i> <span>Actividades</span></a></li>
            <li><a href="reportes.php"><i class="bi bi-graph-up"></i> <span>Reportes</span></a></li>
            <li><a href="#"><i class="bi bi-box-arrow-right"></i> <span>Cerrar Sesión</span></a></li>
        </ul>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.querySelector('.toggle-btn').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>