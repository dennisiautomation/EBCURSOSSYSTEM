<?php
session_start();
require_once '../includes/auth.php';
require_student_auth();

$curso_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Simular dados do curso (depois será carregado do banco de dados)
$cursos = [
    1 => [
        'id' => 1,
        'titulo' => 'Inteligência Artificial para Líderes',
        'imagem' => '../assets/images/curso-ia.jpg',
        'progresso' => 0,
        'total_aulas' => 12,
        'aulas_assistidas' => 0,
        'aulas' => [
            ['titulo' => 'Introdução à IA', 'duracao' => '15:00', 'assistida' => false],
            ['titulo' => 'Fundamentos de Machine Learning', 'duracao' => '25:00', 'assistida' => false],
            ['titulo' => 'IA na Tomada de Decisões', 'duracao' => '20:00', 'assistida' => false],
            ['titulo' => 'Implementando IA na sua Empresa', 'duracao' => '30:00', 'assistida' => false]
        ]
    ],
    2 => [
        'id' => 2,
        'titulo' => 'Engajamento e Liderança',
        'imagem' => '../assets/images/curso-engajamento.jpg',
        'progresso' => 0,
        'total_aulas' => 8,
        'aulas_assistidas' => 0,
        'aulas' => [
            ['titulo' => 'O que é Engajamento?', 'duracao' => '20:00', 'assistida' => false],
            ['titulo' => 'Liderança Moderna', 'duracao' => '25:00', 'assistida' => false],
            ['titulo' => 'Comunicação Efetiva', 'duracao' => '30:00', 'assistida' => false],
            ['titulo' => 'Gestão de Equipes', 'duracao' => '35:00', 'assistida' => false]
        ]
    ]
];

if (!isset($cursos[$curso_id])) {
    header('Location: /alunos');
    exit;
}

$curso = $cursos[$curso_id];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($curso['titulo']); ?> - EB Cursos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/curso.css">
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
            <div class="course-header">
                <div class="course-info">
                    <h1><?php echo htmlspecialchars($curso['titulo']); ?></h1>
                    <div class="course-stats">
                        <span><i class="fas fa-book"></i> <?php echo count($curso['aulas']); ?> aulas</span>
                        <span><i class="fas fa-clock"></i> <?php echo $curso['progresso']; ?>% concluído</span>
                    </div>
                </div>
                <div class="course-progress">
                    <div class="progress-bar" style="width: <?php echo $curso['progresso']; ?>%"></div>
                </div>
            </div>

            <div class="course-content">
                <div class="lessons-list">
                    <?php foreach ($curso['aulas'] as $index => $aula): ?>
                        <div class="lesson-item <?php echo $aula['assistida'] ? 'completed' : ''; ?>">
                            <div class="lesson-info">
                                <div class="lesson-status">
                                    <?php if ($aula['assistida']): ?>
                                        <i class="fas fa-check-circle"></i>
                                    <?php else: ?>
                                        <i class="far fa-circle"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="lesson-details">
                                    <h3><?php echo htmlspecialchars($aula['titulo']); ?></h3>
                                    <span class="lesson-duration">
                                        <i class="fas fa-clock"></i> <?php echo $aula['duracao']; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="lesson-actions">
                                <button class="btn-watch" onclick="watchLesson(<?php echo $curso['id']; ?>, <?php echo $index; ?>)">
                                    <?php echo $aula['assistida'] ? 'Rever Aula' : 'Assistir'; ?>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function watchLesson(courseId, lessonIndex) {
        // Futuramente: implementar a lógica para assistir a aula
        // e atualizar o progresso via AJAX
        alert('Em breve você poderá assistir esta aula!');
    }
    </script>
</body>
</html>
