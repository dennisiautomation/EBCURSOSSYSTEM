<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verifica se está logado
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../../includes/db.php';

// Busca estatísticas
$total_alunos = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM alunos"))['total'];
$total_cursos = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM cursos"))['total'];
$total_matriculas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM matriculas"))['total'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EB Cursos</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .menu-item {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #374151;
            transition: all 0.3s;
            text-align: center;
        }
        
        .menu-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            background: #2563eb;
            color: white;
        }
        
        .menu-item i {
            font-size: 2rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        .menu-item h3 {
            margin: 0;
            font-size: 1.25rem;
        }
        
        .menu-item p {
            margin: 0.5rem 0 0;
            opacity: 0.8;
            font-size: 0.875rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card i {
            font-size: 2rem;
            color: #2563eb;
            margin-bottom: 1rem;
        }
        
        .stat-card h3 {
            margin: 0;
            font-size: 2rem;
            color: #111827;
        }
        
        .stat-card p {
            margin: 0.5rem 0 0;
            color: #6b7280;
        }
        
        .welcome-header {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .welcome-header h1 {
            margin: 0;
            color: #111827;
        }
        
        .welcome-header .user-info {
            text-align: right;
        }
        
        .welcome-header .user-name {
            font-weight: 600;
            color: #111827;
        }
        
        .welcome-header .user-email {
            color: #6b7280;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/admin" class="navbar-brand">
                <img src="../assets/images/logopreto.png" alt="EB Cursos">
            </a>
            <div class="nav-links">
                <a href="/admin" class="active">Dashboard</a>
                <a href="/admin/cursos.php">Cursos</a>
                <a href="/admin/alunos.php">Alunos</a>
                <a href="/admin/matriculas.php">Matrículas</a>
                <a href="/admin/logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="welcome-header">
            <div>
                <h1>Bem-vindo ao Painel Administrativo</h1>
                <p>Gerencie seus cursos e alunos</p>
            </div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
                <div class="user-email"><?php echo htmlspecialchars($_SESSION['admin_email']); ?></div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3><?php echo $total_alunos; ?></h3>
                <p>Alunos</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-graduation-cap"></i>
                <h3><?php echo $total_cursos; ?></h3>
                <p>Cursos</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clipboard-list"></i>
                <h3><?php echo $total_matriculas; ?></h3>
                <p>Matrículas</p>
            </div>
        </div>

        <div class="menu-grid">
            <a href="cursos.php" class="menu-item">
                <i class="fas fa-graduation-cap"></i>
                <h3>Cursos</h3>
                <p>Gerenciar cursos e conteúdos</p>
            </a>
            
            <a href="alunos.php" class="menu-item">
                <i class="fas fa-users"></i>
                <h3>Alunos</h3>
                <p>Gerenciar alunos e matrículas</p>
            </a>
            
            <a href="matriculas.php" class="menu-item">
                <i class="fas fa-clipboard-list"></i>
                <h3>Matrículas</h3>
                <p>Gerenciar matrículas dos alunos</p>
            </a>
        </div>
    </div>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</body>
</html>
