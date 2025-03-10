<?php
session_start();
require_once '../includes/auth.php';
require_student_auth();

// Simular dados dos cursos (depois será carregado do banco de dados)
$cursos = [
    [
        'id' => 1,
        'titulo' => 'Inteligência Artificial para Líderes',
        'imagem' => '../assets/images/curso-ia.jpg',
        'progresso' => 0,
        'total_aulas' => 12,
        'aulas_assistidas' => 0
    ],
    [
        'id' => 2,
        'titulo' => 'Engajamento e Liderança',
        'imagem' => '../assets/images/curso-engajamento.jpg',
        'progresso' => 0,
        'total_aulas' => 8,
        'aulas_assistidas' => 0
    ]
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Aluno - EB Cursos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="brand">
                <img src="../assets/images/logo-black.svg" alt="EB Cursos" style="filter: brightness(0) invert(1); height: 30px; margin-bottom: 2rem;">
            </div>
            <div class="user-info">
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['student_name'], 0, 1)); ?>
                </div>
                <div class="name"><?php echo htmlspecialchars($_SESSION['student_name']); ?></div>
                <div class="email"><?php echo htmlspecialchars($_SESSION['student_email']); ?></div>
            </div>
            
            <ul class="nav-menu">
                <li>
                    <a href="/alunos" class="active">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Meus Cursos</span>
                    </a>
                </li>
                <li>
                    <a href="/alunos/perfil">
                        <i class="fas fa-user"></i>
                        <span>Meu Perfil</span>
                    </a>
                </li>
                <li>
                    <a href="/alunos/certificados">
                        <i class="fas fa-certificate"></i>
                        <span>Certificados</span>
                    </a>
                </li>
                <li>
                    <a href="/alunos/suporte">
                        <i class="fas fa-headset"></i>
                        <span>Suporte</span>
                    </a>
                </li>
                <li>
                    <a href="/alunos/logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sair</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Meus Cursos</h1>
            </div>

            <div class="course-grid">
                <?php foreach ($cursos as $curso): ?>
                    <div class="course-card">
                        <div class="course-image">
                            <img src="<?php echo htmlspecialchars($curso['imagem']); ?>" alt="<?php echo htmlspecialchars($curso['titulo']); ?>">
                            <div class="course-progress">
                                <div class="progress-bar" style="width: <?php echo $curso['progresso']; ?>%"></div>
                            </div>
                        </div>
                        <div class="course-content">
                            <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                            <div class="course-stats">
                                <span><?php echo $curso['aulas_assistidas']; ?> de <?php echo $curso['total_aulas']; ?> aulas</span>
                                <span><?php echo $curso['progresso']; ?>% concluído</span>
                            </div>
                            <div class="course-actions">
                                <a href="/alunos/curso/<?php echo $curso['id']; ?>" class="btn-continue">
                                    <?php echo $curso['progresso'] > 0 ? 'Continuar' : 'Começar'; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Futuramente: implementar carregamento dinâmico dos cursos via AJAX
        // e atualização do progresso em tempo real
    });
    </script>
</body>
</html>
