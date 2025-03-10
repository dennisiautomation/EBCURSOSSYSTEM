<?php
// Iniciar a sessão
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

// Buscar os cursos em que o aluno está matriculado
$query = "SELECT c.* 
          FROM " . TABELA_CURSOS . " c 
          JOIN " . TABELA_MATRICULAS . " m ON c.id = m.course_id 
          WHERE m.student_id = ? AND c.status = 'published'
          ORDER BY c.title";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $aluno_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Verificar se há resultados
$cursos = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cursos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Aluno - EB Cursos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f9fafb;
            color: #333;
            font-family: 'Inter', sans-serif;
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
        
        h1 {
            font-size: 1.875rem;
            margin-bottom: 1.5rem;
            color: #1f2937;
        }
        
        .cursos-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .curso-card {
            background-color: #fff;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .curso-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .curso-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }
        
        .content {
            padding: 1.25rem;
        }
        
        .content h3 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }
        
        .content p {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 1.25rem;
        }
        
        .progress-bar {
            height: 0.5rem;
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
            padding: 0.5rem 1rem;
            background-color: #2563eb;
            color: #fff;
            border-radius: 0.375rem;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
            text-align: center;
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
            background-color: white;
            color: #333;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .empty-state h2 {
            font-size: 1.5rem;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        
        .empty-state p {
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="welcome">Bem-vindo(a), <?php echo htmlspecialchars($aluno_name); ?>!</div>
        <a href="logout.php" class="logout">Sair</a>
    </div>

    <div class="container">
        <h1>Meus Cursos</h1>
        
        <?php if (empty($cursos)): ?>
            <div class="empty-state">
                <h2>Você ainda não tem cursos disponíveis</h2>
                <p>Entre em contato com o administrador para ser matriculado em um curso.</p>
            </div>
        <?php else: ?>
            <div class="cursos-container">
                <?php foreach ($cursos as $curso): ?>
                    <div class="curso-card">
                        <?php if (!empty($curso['thumbnail_url'])): ?>
                            <img src="<?php echo htmlspecialchars($curso['thumbnail_url']); ?>" alt="<?php echo htmlspecialchars($curso['title']); ?>">
                        <?php else: ?>
                            <img src="../assets/images/default-course.jpg" alt="<?php echo htmlspecialchars($curso['title']); ?>">
                        <?php endif; ?>
                        
                        <div class="content">
                            <h3><?php echo htmlspecialchars($curso['title']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($curso['description'], 0, 100) . (strlen($curso['description']) > 100 ? '...' : '')); ?></p>
                            
                            <div class="progress-bar">
                                <?php
                                // Buscar o progresso do aluno neste curso
                                $progress_query = "SELECT progress_percentage FROM " . TABELA_MATRICULAS . " WHERE student_id = ? AND course_id = ?";
                                $progress_stmt = mysqli_prepare($conn, $progress_query);
                                mysqli_stmt_bind_param($progress_stmt, "ii", $aluno_id, $curso['id']);
                                mysqli_stmt_execute($progress_stmt);
                                $progress_result = mysqli_stmt_get_result($progress_stmt);
                                $progress = 0;
                                
                                if ($progress_result && $row = mysqli_fetch_assoc($progress_result)) {
                                    $progress = $row['progress_percentage'];
                                }
                                ?>
                                <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                            <div style="font-size: 0.75rem; margin-bottom: 1rem;"><?php echo $progress; ?>% concluído</div>
                            
                            <a href="curso.php?id=<?php echo $curso['id']; ?>" class="btn">Acessar Curso</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
