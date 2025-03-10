<?php
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['aluno_id'])) {
    header('Location: /login.php');
    exit;
}

$aluno_id = $_SESSION['aluno_id'];
$aluno_name = $_SESSION['aluno_name'] ?? 'Aluno';

// Verificar se o ID do curso foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$curso_id = $_GET['id'];

// Buscar detalhes do curso
$query = "SELECT c.*, m.progress_percentage as progress 
          FROM " . TABELA_CURSOS . " c 
          LEFT JOIN " . TABELA_MATRICULAS . " m ON c.id = m.course_id AND m.student_id = ?
          WHERE c.id = ? AND c.status = 'published'";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $aluno_id, $curso_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Verificar se o curso existe
if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: index.php');
    exit;
}

$curso = mysqli_fetch_assoc($result);
$progresso = $curso['progress'] ?? 0;

// Obter ID do vídeo do YouTube a partir do link completo
$youtube_video_id = '';
if (!empty($curso['youtube_link'])) {
    // Extrair ID do vídeo de diferentes formatos de URL do YouTube
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $curso['youtube_link'], $matches);
    if (isset($matches[1])) {
        $youtube_video_id = $matches[1];
    }
}

// Se não houver link do YouTube, usar vídeo padrão para demonstração
if (empty($youtube_video_id)) {
    $youtube_video_id = 'dQw4w9WgXcQ'; // Vídeo padrão
}

// Processar atualização de progresso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aumentar_progresso'])) {
    // Verificar se o aluno já está matriculado
    $check_query = "SELECT id FROM " . TABELA_MATRICULAS . " WHERE course_id = ? AND student_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ii", $curso_id, $aluno_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Aluno já matriculado, atualizar progresso
        $matricula = mysqli_fetch_assoc($check_result);
        $novo_progresso = min($progresso + 10, 100); // Aumenta 10%, máximo 100%
        
        $update_query = "UPDATE " . TABELA_MATRICULAS . " SET progress_percentage = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "ii", $novo_progresso, $matricula['id']);
        mysqli_stmt_execute($update_stmt);
    } else {
        // Matricular o aluno e definir progresso inicial
        $novo_progresso = 10; // Começa com 10%
        
        $insert_query = "INSERT INTO " . TABELA_MATRICULAS . " (course_id, student_id, progress_percentage, status) VALUES (?, ?, ?, 'active')";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "iii", $curso_id, $aluno_id, $novo_progresso);
        mysqli_stmt_execute($insert_stmt);
    }
    
    // Atualizar a página para mostrar o novo progresso
    header("Location: curso.php?id=$curso_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($curso['title']); ?> - EB Cursos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
            color: #333;
        }
        
        .header {
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .course-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .video-container {
            background: black;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .course-info {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .course-info h1 {
            font-size: 1.875rem;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .course-info p {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .progress-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-top: 1.5rem;
        }
        
        .progress-section h2 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .progress-bar {
            height: 1rem;
            background-color: #e5e7eb;
            border-radius: 9999px;
            margin-bottom: 0.5rem;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #2563eb;
            border-radius: 9999px;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #2563eb;
            color: #fff;
            border: none;
            border-radius: 0.375rem;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #1d4ed8;
        }
        
        .welcome {
            font-weight: 600;
        }
        
        .logout {
            padding: 0.5rem 1rem;
            background-color: #ef4444;
            color: white;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        
        .logout:hover {
            background-color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="welcome">Bem-vindo(a), <?php echo htmlspecialchars($aluno_name); ?>!</div>
        <div>
            <a href="index.php" style="margin-right: 1rem; text-decoration: none; color: #2563eb;">Voltar</a>
            <a href="logout.php" class="logout">Sair</a>
        </div>
    </div>

    <div class="course-container">
        <div class="video-container">
            <iframe src="https://www.youtube.com/embed/<?php echo $youtube_video_id; ?>?rel=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        
        <div class="course-info">
            <h1><?php echo htmlspecialchars($curso['title']); ?></h1>
            <p><?php echo nl2br(htmlspecialchars($curso['description'])); ?></p>
        </div>
        
        <div class="progress-section">
            <h2>Seu Progresso</h2>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $progresso; ?>%"></div>
            </div>
            <p><?php echo $progresso; ?>% concluído</p>
            
            <form method="POST">
                <button type="submit" name="aumentar_progresso" class="btn">Marcar como Assistido (<?php echo min($progresso + 10, 100); ?>%)</button>
            </form>
        </div>
    </div>
</body>
</html>
