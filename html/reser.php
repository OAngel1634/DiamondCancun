<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: inicio-sesion.php');
    exit;
}

require_once('conexion.php');
$database = new Database();
$conn = $database->connect();

// Obtener tours disponibles
$tours = [];
try {
    $stmt = $conn->query("SELECT id, nombre, precio_base, duracion, tipo FROM tours WHERE activo = 1");
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al cargar los tours: " . $e->getMessage();
}

// Obtener fechas disponibles para el tour seleccionado
$fechas_disponibles = [];
if (isset($_GET['tour_id'])) {
    $tour_id = intval($_GET['tour_id']);
    $hoy = date('Y-m-d');
    
    try {
        $stmt = $conn->prepare("SELECT id, fecha, hora_inicio, disponibilidad, precio_especial 
                                FROM calendario_tours 
                                WHERE tour_id = :tour_id AND fecha >= :hoy 
                                ORDER BY fecha");
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt->bindParam(':hoy', $hoy);
        $stmt->execute();
        $fechas_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error al cargar las fechas: " . $e->getMessage();
    }
}

// Procesar la reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reservar'])) {
    $tour_id = intval($_POST['tour_id']);
    $fecha_id = intval($_POST['fecha_id']);
    $adultos = intval($_POST['adultos']);
    $ninos = intval($_POST['ninos']);
    $total_personas = $adultos + $ninos;
    
    // Validar disponibilidad
    try {
        $stmt = $conn->prepare("SELECT disponibilidad FROM calendario_tours WHERE id = :fecha_id");
        $stmt->bindParam(':fecha_id', $fecha_id, PDO::PARAM_INT);
        $stmt->execute();
        $disponibilidad = $stmt->fetchColumn();
        
        if ($disponibilidad >= $total_personas) {
            // Obtener precio del tour
            $stmt = $conn->prepare("SELECT precio_base FROM tours WHERE id = :tour_id");
            $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
            $stmt->execute();
            $precio_base = $stmt->fetchColumn();
            
            // Calcular precio total (niños con 20% descuento)
            $precio_total = ($adultos * $precio_base) + ($ninos * $precio_base * 0.8);
            
            // Generar código de reserva único
            $codigo_reserva = 'RES' . strtoupper(uniqid());
            
            // Obtener fecha y hora del calendario
            $stmt = $conn->prepare("SELECT fecha, hora_inicio FROM calendario_tours WHERE id = :fecha_id");
            $stmt->bindParam(':fecha_id', $fecha_id, PDO::PARAM_INT);
            $stmt->execute();
            $fecha_info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Insertar reserva
            $stmt = $conn->prepare("INSERT INTO reservas 
                (codigo_reserva, usuario_id, tour_id, fecha_tour, hora_tour, numero_adultos, numero_ninos, precio_total, estado) 
                VALUES 
                (:codigo, :usuario_id, :tour_id, :fecha_tour, :hora_tour, :adultos, :ninos, :precio_total, 'pendiente')");
            
            $stmt->bindParam(':codigo', $codigo_reserva);
            $stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
            $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_tour', $fecha_info['fecha']);
            $stmt->bindParam(':hora_tour', $fecha_info['hora_inicio']);
            $stmt->bindParam(':adultos', $adultos, PDO::PARAM_INT);
            $stmt->bindParam(':ninos', $ninos, PDO::PARAM_INT);
            $stmt->bindParam(':precio_total', $precio_total);
            
            if ($stmt->execute()) {
                // Actualizar disponibilidad
                $nueva_disponibilidad = $disponibilidad - $total_personas;
                $stmt = $conn->prepare("UPDATE calendario_tours SET disponibilidad = :disp WHERE id = :fecha_id");
                $stmt->bindParam(':disp', $nueva_disponibilidad, PDO::PARAM_INT);
                $stmt->bindParam(':fecha_id', $fecha_id, PDO::PARAM_INT);
                $stmt->execute();
                
                $reserva_exitosa = true;
                $codigo_reserva_mostrar = $codigo_reserva;
            } else {
                $error = "Error al realizar la reserva. Por favor, inténtelo de nuevo.";
            }
        } else {
            $error = "Lo sentimos, no hay suficiente disponibilidad para la fecha seleccionada.";
        }
    } catch (PDOException $e) {
        $error = "Error en la base de datos: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - Tours Isla Mujeres</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        :root {
            --primary: #1a6ea0;
            --primary-dark: #0c4a6e;
            --secondary: #ffd700;
            --accent: #28a745;
            --light: #f8f9fa;
            --dark: #333;
            --gray: #6c757d;
            --border: #ced4da;
            --shadow: 0 5px 15px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        body {
            background-color: #f0f8ff;
            color: var(--dark);
            line-height: 1.6;
            background-image: linear-gradient(135deg, rgba(26, 110, 160, 0.05) 0%, rgba(12, 74, 110, 0.05) 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 15px 0;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        .logo i {
            color: var(--secondary);
        }
        
        .nav-links {
            display: flex;
            list-style: none;
            gap: 25px;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 0;
            position: relative;
            transition: var(--transition);
        }
        
        .nav-links a:hover {
            color: var(--secondary);
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: var(--secondary);
            transition: var(--transition);
        }
        
        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }
        
        .nav-links a.active {
            color: var(--secondary);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.1);
            padding: 8px 15px;
            border-radius: 50px;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .user-info:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--secondary);
        }
        
        /* Sección Hero */
        .hero {
            background: linear-gradient(rgba(12, 74, 110, 0.8), rgba(12, 74, 110, 0.8)), url('https://images.unsplash.com/photo-1564604761388-83eafc96f668?ixlib=rb-4.0.3') no-repeat center/cover;
            color: white;
            text-align: center;
            padding: 80px 20px;
            margin: 30px 0;
            border-radius: 15px;
            box-shadow: var(--shadow);
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
        }
        
        /* Proceso de Reserva */
        .reservation-process {
            display: flex;
            justify-content: space-between;
            max-width: 800px;
            margin: 40px auto;
            position: relative;
            padding: 0 20px;
        }
        
        .reservation-process::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 10%;
            width: 80%;
            height: 3px;
            background: var(--primary);
            z-index: 1;
        }
        
        .step {
            text-align: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }
        
        .step-circle {
            width: 50px;
            height: 50px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            font-size: 1.2rem;
            transition: var(--transition);
        }
        
        .step.active .step-circle {
            background: var(--primary-dark);
            box-shadow: 0 0 0 8px rgba(12, 74, 110, 0.2);
            transform: scale(1.1);
        }
        
        .step.completed .step-circle {
            background: var(--accent);
        }
        
        /* Secciones de reserva */
        .reservation-section {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            padding: 30px;
            margin-bottom: 40px;
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .reservation-section.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .section-header {
            color: var(--primary-dark);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .section-header i {
            font-size: 1.8rem;
            color: var(--primary);
        }
        
        /* Tours */
        .tours-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        
        .tour-card {
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
        }
        
        .tour-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .tour-card.selected {
            border: 2px solid var(--primary);
            background-color: rgba(26, 110, 160, 0.05);
        }
        
        .tour-image {
            height: 200px;
            background: linear-gradient(45deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            position: relative;
            overflow: hidden;
        }
        
        .tour-image::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
        }
        
        .tour-tag {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            z-index: 2;
        }
        
        .tour-content {
            padding: 20px;
        }
        
        .tour-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--primary-dark);
        }
        
        .tour-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .tour-price {
            font-weight: bold;
            font-size: 1.3rem;
            color: var(--accent);
        }
        
        .tour-duration {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--gray);
        }
        
        .tour-features {
            list-style: none;
            margin-top: 15px;
        }
        
        .tour-features li {
            padding: 5px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .tour-features i {
            color: var(--primary);
        }
        
        /* Fechas */
        .calendar-container {
            display: flex;
            gap: 30px;
            margin-top: 20px;
        }
        
        .calendar {
            flex: 1;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .calendar-nav {
            display: flex;
            gap: 10px;
        }
        
        .calendar-nav button {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: none;
            background: var(--light);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .calendar-nav button:hover {
            background: var(--primary);
            color: white;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }
        
        .calendar-day-name {
            text-align: center;
            font-weight: bold;
            color: var(--gray);
            padding: 10px 0;
        }
        
        .calendar-day {
            text-align: center;
            padding: 12px 0;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }
        
        .calendar-day:hover {
            background: var(--light);
        }
        
        .calendar-day.available {
            background: rgba(40, 167, 69, 0.1);
            color: var(--accent);
            font-weight: bold;
        }
        
        .calendar-day.available:hover {
            background: rgba(40, 167, 69, 0.2);
        }
        
        .calendar-day.selected {
            background: var(--primary);
            color: white;
            font-weight: bold;
        }
        
        .calendar-day.disabled {
            color: #ccc;
            cursor: not-allowed;
        }
        
        .calendar-day.today::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
            width: 5px;
            height: 5px;
            background: var(--primary);
            border-radius: 50%;
        }
        
        .time-slots {
            flex: 0 0 300px;
        }
        
        .time-header {
            margin-bottom: 15px;
            color: var(--primary-dark);
        }
        
        .time-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .time-option {
            padding: 15px;
            border: 1px solid var(--border);
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .time-option:hover {
            border-color: var(--primary);
        }
        
        .time-option.selected {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .time-value {
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .time-availability {
            font-size: 0.9rem;
            color: var(--gray);
            margin-top: 5px;
        }
        
        /* Personas */
        .person-selector {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        .person-type {
            flex: 1;
            padding: 25px;
            border: 1px solid var(--border);
            border-radius: 10px;
            text-align: center;
            transition: var(--transition);
        }
        
        .person-type.selected {
            border: 2px solid var(--primary);
            background: rgba(26, 110, 160, 0.05);
        }
        
        .person-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .person-title {
            font-size: 1.2rem;
            color: var(--primary-dark);
            margin-bottom: 15px;
        }
        
        .person-description {
            color: var(--gray);
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        
        .counter {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .counter-btn {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .counter-btn:disabled {
            background: var(--border);
            cursor: not-allowed;
        }
        
        .counter-value {
            margin: 0 15px;
            font-size: 1.5rem;
            min-width: 40px;
            text-align: center;
            font-weight: bold;
            color: var(--primary-dark);
        }
        
        /* Confirmación */
        .reservation-summary {
            background: var(--light);
            border-radius: 10px;
            padding: 25px;
            margin-top: 20px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid var(--border);
        }
        
        .summary-item:last-child {
            border-bottom: none;
        }
        
        .summary-label {
            font-weight: 500;
            color: var(--gray);
        }
        
        .summary-value {
            font-weight: 500;
            color: var(--dark);
        }
        
        .total {
            font-size: 1.4rem;
            font-weight: bold;
            color: var(--primary-dark);
        }
        
        .payment-methods {
            margin-top: 30px;
        }
        
        .payment-options {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .payment-option {
            flex: 1;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .payment-option:hover {
            border-color: var(--primary);
        }
        
        .payment-option.selected {
            border: 2px solid var(--primary);
            background: rgba(26, 110, 160, 0.05);
        }
        
        .payment-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .payment-name {
            font-weight: 500;
            color: var(--dark);
        }
        
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 30px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(0,0,0,0.1);
        }
        
        .btn-secondary {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        
        .btn-secondary:hover {
            background: var(--light);
        }
        
        .btn-large {
            padding: 18px 40px;
            font-size: 1.2rem;
            display: block;
            width: 100%;
            max-width: 400px;
            margin: 30px auto;
        }
        
        /* Confirmación exitosa */
        .confirmation-container {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            max-width: 800px;
            margin: 50px auto;
        }
        
        .confirmation-icon {
            width: 120px;
            height: 120px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            margin: 0 auto 30px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(40, 167, 69, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
        }
        
        .reservation-code {
            background: var(--primary-dark);
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 20px auto;
            display: inline-block;
            font-family: monospace;
        }
        
        .confirmation-details {
            background: var(--light);
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }
        
        /* Footer */
        footer {
            background: var(--primary-dark);
            color: white;
            padding: 50px 0 20px;
            margin-top: 50px;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            padding: 0 20px;
        }
        
        .footer-section h3 {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--secondary);
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .footer-links a:hover {
            color: var(--secondary);
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .social-links a:hover {
            background: var(--secondary);
            transform: translateY(-5px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.6);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-links {
                gap: 15px;
            }
            
            .reservation-process {
                flex-direction: column;
                gap: 30px;
            }
            
            .reservation-process::before {
                display: none;
            }
            
            .calendar-container {
                flex-direction: column;
            }
            
            .person-selector {
                flex-direction: column;
            }
            
            .btn-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-ship"></i>
                <span>Diamond Bright Tours</span>
            </div>
            
            <ul class="nav-links">
                <li><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="#" class="active"><i class="fas fa-ship"></i> Tours</a></li>
                <li><a href="#"><i class="fas fa-calendar-check"></i> Reservas</a></li>
                <li><a href="#"><i class="fas fa-info-circle"></i> Información</a></li>
                <li><a href="#"><i class="fas fa-phone"></i> Contacto</a></li>
            </ul>
        
        </div>
    </header>
    
    <div class="container">
        <!-- Hero Section -->
        <section class="hero">
            <h1>Reserva Tu Tour a Isla Mujeres</h1>
            <p>Vive una experiencia inolvidable explorando las aguas cristalinas y paisajes paradisíacos de Isla Mujeres. Reserva ahora y aprovecha nuestras promociones exclusivas.</p>
        </section>
        
        <!-- Proceso de Reserva -->
        <div class="reservation-process">
            <div class="step active">
                <div class="step-circle">1</div>
                <div>Elegir Tour</div>
            </div>
            <div class="step">
                <div class="step-circle">2</div>
                <div>Fecha y Personas</div>
            </div>
            <div class="step">
                <div class="step-circle">3</div>
                <div>Confirmar</div>
            </div>
        </div>
        
        <!-- Paso 1: Selección de Tour -->
        <section id="tour-selection" class="reservation-section active">
            <div class="section-header">
                <i class="fas fa-map-marked-alt"></i>
                <h2>Selecciona Tu Tour</h2>
            </div>
            
            <p>Descubre nuestras increíbles experiencias en Isla Mujeres. Selecciona el tour que más te guste y continúa con tu reserva.</p>
            
            <div class="tours-grid">
                <!-- Tour 1 -->
                <div class="tour-card selected" onclick="selectTour(this, 1)">
                    <div class="tour-image">
                        <i class="fas fa-island"></i>
                        <div class="tour-tag">Más Popular</div>
                    </div>
                    <div class="tour-content">
                        <h3 class="tour-title">Tour Premium Isla Mujeres</h3>
                        <div class="tour-details">
                            <span class="tour-price">$120.00</span>
                            <span class="tour-duration"><i class="fas fa-clock"></i> 8 horas</span>
                        </div>
                        <ul class="tour-features">
                            <li><i class="fas fa-check-circle"></i> Transporte incluido</li>
                            <li><i class="fas fa-check-circle"></i> Comida y bebidas</li>
                            <li><i class="fas fa-check-circle"></i> Equipo de snorkel</li>
                            <li><i class="fas fa-check-circle"></i> Fotos profesionales</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Tour 2 -->
                <div class="tour-card" onclick="selectTour(this, 2)">
                    <div class="tour-image">
                        <i class="fas fa-water"></i>
                    </div>
                    <div class="tour-content">
                        <h3 class="tour-title">Tour de Snorkel</h3>
                        <div class="tour-details">
                            <span class="tour-price">$85.00</span>
                            <span class="tour-duration"><i class="fas fa-clock"></i> 4 horas</span>
                        </div>
                        <ul class="tour-features">
                            <li><i class="fas fa-check-circle"></i> Arrecife de coral</li>
                            <li><i class="fas fa-check-circle"></i> Equipo profesional</li>
                            <li><i class="fas fa-check-circle"></i> Guía especializado</li>
                            <li><i class="fas fa-check-circle"></i> Refrigerios incluidos</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Tour 3 -->
                <div class="tour-card" onclick="selectTour(this, 3)">
                    <div class="tour-image">
                        <i class="fas fa-umbrella-beach"></i>
                    </div>
                    <div class="tour-content">
                        <h3 class="tour-title">Club de Playa Premium</h3>
                        <div class="tour-details">
                            <span class="tour-price">$65.00</span>
                            <span class="tour-duration"><i class="fas fa-clock"></i> Todo el día</span>
                        </div>
                        <ul class="tour-features">
                            <li><i class="fas fa-check-circle"></i> Acceso exclusivo</li>
                            <li><i class="fas fa-check-circle"></i> Buffet gourmet</li>
                            <li><i class="fas fa-check-circle"></i> Bebidas ilimitadas</li>
                            <li><i class="fas fa-check-circle"></i> Hamacas y camastros</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Tour 4 -->
                <div class="tour-card" onclick="selectTour(this, 4)">
                    <div class="tour-image">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div class="tour-content">
                        <h3 class="tour-title">Tour MUSA</h3>
                        <div class="tour-details">
                            <span class="tour-price">$90.00</span>
                            <span class="tour-duration"><i class="fas fa-clock"></i> 5 horas</span>
                        </div>
                        <ul class="tour-features">
                            <li><i class="fas fa-check-circle"></i> Museo Subacuático</li>
                            <li><i class="fas fa-check-circle"></i> Snorkel o buceo</li>
                            <li><i class="fas fa-check-circle"></i> Guía certificado</li>
                            <li><i class="fas fa-check-circle"></i> Transporte incluido</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="btn-container">
                <div></div> <!-- Espacio vacío para alinear a la derecha -->
                <button class="btn" onclick="nextStep(1)">Continuar <i class="fas fa-arrow-right"></i></button>
            </div>
        </section>
        
        <!-- Paso 2: Fecha y Personas -->
        <section id="date-selection" class="reservation-section">
            <div class="section-header">
                <i class="fas fa-calendar-alt"></i>
                <h2>Selecciona Fecha y Personas</h2>
            </div>
            
            <div class="calendar-container">
                <div class="calendar">
                    <div class="calendar-header">
                        <h3>Julio 2025</h3>
                        <div class="calendar-nav">
                            <button><i class="fas fa-chevron-left"></i></button>
                            <button><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                    
                    <div class="calendar-grid">
                        <div class="calendar-day-name">Lun</div>
                        <div class="calendar-day-name">Mar</div>
                        <div class="calendar-day-name">Mié</div>
                        <div class="calendar-day-name">Jue</div>
                        <div class="calendar-day-name">Vie</div>
                        <div class="calendar-day-name">Sáb</div>
                        <div class="calendar-day-name">Dom</div>
                        
                        <!-- Días del calendario -->
                        <div class="calendar-day disabled">29</div>
                        <div class="calendar-day disabled">30</div>
                        <div class="calendar-day">1</div>
                        <div class="calendar-day">2</div>
                        <div class="calendar-day">3</div>
                        <div class="calendar-day">4</div>
                        <div class="calendar-day">5</div>
                        
                        <div class="calendar-day">6</div>
                        <div class="calendar-day">7</div>
                        <div class="calendar-day">8</div>
                        <div class="calendar-day">9</div>
                        <div class="calendar-day today available">10</div>
                        <div class="calendar-day available">11</div>
                        <div class="calendar-day available">12</div>
                        
                        <div class="calendar-day available">13</div>
                        <div class="calendar-day available">14</div>
                        <div class="calendar-day available">15</div>
                        <div class="calendar-day available">16</div>
                        <div class="calendar-day available">17</div>
                        <div class="calendar-day available">18</div>
                        <div class="calendar-day available">19</div>
                        
                        <div class="calendar-day available">20</div>
                        <div class="calendar-day available">21</div>
                        <div class="calendar-day available">22</div>
                        <div class="calendar-day available">23</div>
                        <div class="calendar-day available">24</div>
                        <div class="calendar-day available">25</div>
                        <div class="calendar-day available">26</div>
                        
                        <div class="calendar-day available">27</div>
                        <div class="calendar-day available">28</div>
                        <div class="calendar-day available">29</div>
                        <div class="calendar-day available">30</div>
                        <div class="calendar-day available">31</div>
                        <div class="calendar-day disabled">1</div>
                        <div class="calendar-day disabled">2</div>
                    </div>
                </div>
                
                <div class="time-slots">
                    <h3 class="time-header">Horarios Disponibles</h3>
                    <div class="time-options">
                        <div class="time-option selected" onclick="selectTime(this)">
                            <div class="time-value">09:00 AM</div>
                            <div class="time-availability">15 cupos disponibles</div>
                        </div>
                        <div class="time-option" onclick="selectTime(this)">
                            <div class="time-value">10:30 AM</div>
                            <div class="time-availability">8 cupos disponibles</div>
                        </div>
                        <div class="time-option" onclick="selectTime(this)">
                            <div class="time-value">12:00 PM</div>
                            <div class="time-availability">20 cupos disponibles</div>
                        </div>
                        <div class="time-option" onclick="selectTime(this)">
                            <div class="time-value">02:00 PM</div>
                            <div class="time-availability">12 cupos disponibles</div>
                        </div>
                    </div>
                    
                    <h3 class="time-header" style="margin-top: 30px;">Número de Personas</h3>
                    <div class="person-selector">
                        <div class="person-type selected">
                            <div class="person-icon"><i class="fas fa-user"></i></div>
                            <div class="person-title">Adultos</div>
                            <div class="person-description">(13 años y más)</div>
                            <div class="counter">
                                <button class="counter-btn" onclick="updateCounter('adultos', -1)">-</button>
                                <div class="counter-value" id="adultos-value">2</div>
                                <button class="counter-btn" onclick="updateCounter('adultos', 1)">+</button>
                            </div>
                        </div>
                        
                        <div class="person-type">
                            <div class="person-icon"><i class="fas fa-child"></i></div>
                            <div class="person-title">Niños</div>
                            <div class="person-description">(3-12 años)</div>
                            <div class="counter">
                                <button class="counter-btn" onclick="updateCounter('ninos', -1)">-</button>
                                <div class="counter-value" id="ninos-value">1</div>
                                <button class="counter-btn" onclick="updateCounter('ninos', 1)">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="btn-container">
                <button class="btn btn-secondary" onclick="prevStep(1)"><i class="fas fa-arrow-left"></i> Regresar</button>
                <button class="btn" onclick="nextStep(2)">Continuar <i class="fas fa-arrow-right"></i></button>
            </div>
        </section>
        
        <!-- Paso 3: Confirmación -->
        <section id="confirmation" class="reservation-section">
            <div class="section-header">
                <i class="fas fa-check-circle"></i>
                <h2>Confirma Tu Reserva</h2>
            </div>
            
            <p>Por favor revisa los detalles de tu reserva antes de confirmar. Si todo está correcto, completa tu pago para finalizar la reserva.</p>
            
            <div class="reservation-summary">
                <div class="summary-item">
                    <span class="summary-label">Tour:</span>
                    <span class="summary-value">Tour Premium Isla Mujeres</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Fecha:</span>
                    <span class="summary-value">10 de Julio, 2025</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Hora:</span>
                    <span class="summary-value">09:00 AM</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Adultos:</span>
                    <span class="summary-value">2 x $120.00</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Niños:</span>
                    <span class="summary-value">1 x $96.00</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Descuento:</span>
                    <span class="summary-value">-$20.00</span>
                </div>
                <div class="summary-item total">
                    <span class="summary-label">Total:</span>
                    <span class="summary-value">$316.00</span>
                </div>
            </div>
            
            <div class="payment-methods">
                <h3>Método de Pago</h3>
                <div class="payment-options">
                    <div class="payment-option selected" onclick="selectPayment(this, 'tarjeta')">
                        <div class="payment-icon"><i class="fas fa-credit-card"></i></div>
                        <div class="payment-name">Tarjeta de Crédito</div>
                    </div>
                    <div class="payment-option" onclick="selectPayment(this, 'paypal')">
                        <div class="payment-icon"><i class="fab fa-paypal"></i></div>
                        <div class="payment-name">PayPal</div>
                    </div>
                    <div class="payment-option" onclick="selectPayment(this, 'efectivo')">
                        <div class="payment-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="payment-name">Efectivo</div>
                    </div>
                </div>
            </div>
            
            <div class="btn-container">
                <button class="btn btn-secondary" onclick="prevStep(2)"><i class="fas fa-arrow-left"></i> Regresar</button>
                <button class="btn" onclick="confirmReservation()">Confirmar Reserva <i class="fas fa-check"></i></button>
            </div>
        </section>
        
        <!-- Confirmación exitosa (oculta inicialmente) -->
        <section id="success" class="reservation-section">
            <div class="confirmation-container">
                <div class="confirmation-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2>¡Reserva Confirmada!</h2>
                <p>Tu reserva ha sido realizada con éxito. Hemos enviado un correo de confirmación a tu dirección de email.</p>
                
                <div class="reservation-code">DBT-20250710-092</div>
                
                <p>Guarda este código para presentarlo el día del tour. ¡Esperamos que disfrutes tu experiencia!</p>
                
                <div class="confirmation-details">
                    <div class="detail-item">
                        <span>Tour:</span>
                        <span>Tour Premium Isla Mujeres</span>
                    </div>
                    <div class="detail-item">
                        <span>Fecha:</span>
                        <span>Viernes, 10 de Julio 2025</span>
                    </div>
                    <div class="detail-item">
                        <span>Hora:</span>
                        <span>09:00 AM</span>
                    </div>
                    <div class="detail-item">
                        <span>Punto de encuentro:</span>
                        <span>Marina 1, Cancún</span>
                    </div>
                    <div class="detail-item">
                        <span>Personas:</span>
                        <span>2 Adultos, 1 Niño</span>
                    </div>
                    <div class="detail-item">
                        <span>Total pagado:</span>
                        <span>$316.00 USD</span>
                    </div>
                </div>
                
                <button class="btn btn-large" onclick="window.location.href='#'"><i class="fas fa-file-pdf"></i> Descargar Comprobante</button>
                <button class="btn btn-large btn-secondary" onclick="window.location.href='#'"><i class="fas fa-ship"></i> Ver Otros Tours</button>
            </div>
        </section>
    </div>
    
    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Diamond Bright Tours</h3>
                <p>Ofreciendo las mejores experiencias en Isla Mujeres desde 2015. Nuestros tours premium te llevarán a descubrir la magia del Caribe.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Enlaces Rápidos</h3>
                <ul class="footer-links">
                    <li><a href="#"><i class="fas fa-ship"></i> Todos los Tours</a></li>
                    <li><a href="#"><i class="fas fa-percent"></i> Promociones</a></li>
                    <li><a href="#"><i class="fas fa-question-circle"></i> Preguntas Frecuentes</a></li>
                    <li><a href="#"><i class="fas fa-map-marked-alt"></i> Puntos de Encuentro</a></li>
                    <li><a href="#"><i class="fas fa-file-alt"></i> Términos y Condiciones</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Contáctanos</h3>
                <ul class="footer-links">
                    <li><a href="#"><i class="fas fa-map-marker-alt"></i> Av. Kukulcán, Cancún, México</a></li>
                    <li><a href="#"><i class="fas fa-phone"></i> +52 998 123 4567</a></li>
                    <li><a href="#"><i class="fas fa-envelope"></i> info@diamondbright.com</a></li>
                    <li><a href="#"><i class="fas fa-clock"></i> Lunes a Domingo: 8:00 AM - 8:00 PM</a></li>
                </ul>
            </div>
        </div>
        
        <div class="copyright">
            &copy; 2025 Diamond Bright Tours. Todos los derechos reservados.
        </div>
    </footer>
    
    <script>
        // Variables para almacenar los datos de la reserva
        let selectedTour = 1;
        let selectedDate = '2025-07-10';
        let selectedTime = '09:00 AM';
        let adultsCount = 2;
        let childrenCount = 1;
        let paymentMethod = 'tarjeta';
        
        // Función para seleccionar un tour
        function selectTour(element, tourId) {
            document.querySelectorAll('.tour-card').forEach(card => {
                card.classList.remove('selected');
            });
            element.classList.add('selected');
            selectedTour = tourId;
        }
        
        // Función para seleccionar una hora
        function selectTime(element) {
            document.querySelectorAll('.time-option').forEach(option => {
                option.classList.remove('selected');
            });
            element.classList.add('selected');
            selectedTime = element.querySelector('.time-value').textContent;
        }
        
        // Función para seleccionar método de pago
        function selectPayment(element, method) {
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('selected');
            });
            element.classList.add('selected');
            paymentMethod = method;
        }
        
        // Función para actualizar contador de personas
        function updateCounter(type, change) {
            const valueElement = document.getElementById(`${type}-value`);
            let value = parseInt(valueElement.textContent);
            
            value += change;
            
            // Validar valores mínimos y máximos
            if (value < 0) value = 0;
            if (value > 10) value = 10;
            
            valueElement.textContent = value;
            
            if (type === 'adultos') {
                adultsCount = value;
            } else {
                childrenCount = value;
            }
        }
        
        // Navegación entre pasos
        function nextStep(step) {
            // Ocultar sección actual
            document.querySelector('.reservation-section.active').classList.remove('active');
            
            // Actualizar indicador de pasos
            document.querySelector(`.step:nth-child(${step})`).classList.remove('active');
            document.querySelector(`.step:nth-child(${step + 1})`).classList.add('active');
            
            // Mostrar siguiente sección
            const sections = ['tour-selection', 'date-selection', 'confirmation', 'success'];
            document.getElementById(sections[step]).classList.add('active');
        }
        
        function prevStep(step) {
            // Ocultar sección actual
            document.querySelector('.reservation-section.active').classList.remove('active');
            
            // Actualizar indicador de pasos
            document.querySelector(`.step:nth-child(${step + 1})`).classList.remove('active');
            document.querySelector(`.step:nth-child(${step})`).classList.add('active');
            
            // Mostrar sección anterior
            const sections = ['tour-selection', 'date-selection', 'confirmation', 'success'];
            document.getElementById(sections[step - 1]).classList.add('active');
        }
        
        // Función para confirmar la reserva
        function confirmReservation() {
            // Ocultar sección actual
            document.querySelector('.reservation-section.active').classList.remove('active');
            
            // Actualizar indicador de pasos
            document.querySelectorAll('.step').forEach(step => {
                step.classList.remove('active');
                step.classList.add('completed');
            });
            
            // Mostrar confirmación exitosa
            document.getElementById('success').classList.add('active');
        }
        
        // Inicializar eventos para los días del calendario
        document.querySelectorAll('.calendar-day.available').forEach(day => {
            day.addEventListener('click', function() {
                document.querySelectorAll('.calendar-day').forEach(d => {
                    d.classList.remove('selected');
                });
                this.classList.add('selected');
                selectedDate = this.textContent + ' de Julio, 2025';
            });
        });
        
        // Seleccionar el día actual por defecto
        document.querySelector('.calendar-day.today').click();
    </script>
</body>
</html>