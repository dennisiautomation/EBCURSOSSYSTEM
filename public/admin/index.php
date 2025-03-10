<?php
session_start();
require_once '../../includes/auth.php';
require_admin_auth();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - EB Cursos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-container {
            padding: 2rem;
        }
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card h3 {
            color: var(--text-dark);
            margin-bottom: 1rem;
        }
        .number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        .recent-activity {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .recent-activity h2 {
            margin-bottom: 1rem;
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

    <div class="container admin-container">
        <header style="margin-bottom: 2rem;">
            <h1>Dashboard</h1>
            <p>Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
        </header>

        <div class="dashboard-cards">
            <div class="card">
                <h3>Total de Alunos</h3>
                <p class="number">0</p>
            </div>
            <div class="card">
                <h3>Cursos Ativos</h3>
                <p class="number">0</p>
            </div>
            <div class="card">
                <h3>Matrículas Ativas</h3>
                <p class="number">0</p>
            </div>
        </div>

        <section class="recent-activity">
            <h2>Atividade Recente</h2>
            <div class="activity-list">
                <p>Nenhuma atividade recente</p>
            </div>
        </section>
    </div>
</body>
</html>
