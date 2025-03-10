<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_admin_auth();

$success = false;
$error = false;

// Processar exclusão de curso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
    $curso_id = $_POST['curso_id'] ?? 0;
    
    // Verificar se há matrículas para este curso
    $check_matriculas = "SELECT COUNT(*) as total FROM " . TABELA_MATRICULAS . " WHERE course_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_matriculas);
    mysqli_stmt_bind_param($check_stmt, "i", $curso_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $check_row = mysqli_fetch_assoc($check_result);
    
    if ($check_row['total'] > 0) {
        $error = "Não é possível excluir este curso porque existem alunos matriculados nele. Cancele as matrículas primeiro.";
    } else {
        // Excluir o curso
        $delete_query = "DELETE FROM " . TABELA_CURSOS . " WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $curso_id);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            $success = "Curso excluído com sucesso!";
        } else {
            $error = "Erro ao excluir curso: " . mysqli_error($conn);
        }
    }
}

// Processar formulário de novo curso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $thumbnail = $_POST['thumbnail'] ?? '';
    $youtube_link = $_POST['youtube_link'] ?? '';
    $status = 'published';
    
    $query = "INSERT INTO " . TABELA_CURSOS . " (title, description, thumbnail, youtube_link, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $title, $description, $thumbnail, $youtube_link, $status);
    
    if (mysqli_stmt_execute($stmt)) {
        $success = "Curso cadastrado com sucesso!";
    } else {
        $error = "Erro ao cadastrar curso: " . mysqli_error($conn);
    }
}

// Buscar cursos cadastrados
$query = "SELECT * FROM " . TABELA_CURSOS . " ORDER BY id DESC";
$result = mysqli_query($conn, $query);
$cursos = [];
if ($result) {
    while ($curso = mysqli_fetch_assoc($result)) {
        $cursos[] = $curso;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cursos - EB Cursos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
            margin-bottom: 30px;
        }
        
        .navbar-brand img {
            height: 40px;
        }
        
        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-links {
            display: flex;
            gap: 20px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .nav-links a:hover {
            background-color: #f1f1f1;
        }
        
        .nav-links a.active {
            background-color: #e5e5e5;
        }
        
        h1 {
            margin-bottom: 30px;
            color: #2c3e50;
            font-weight: 800;
        }
        
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .form-title {
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #4a5568;
        }
        
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .btn-danger {
            background-color: #e74c3c;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .course-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        
        .card-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-bottom: 1px solid #eee;
        }
        
        .card-body {
            padding: 15px;
        }
        
        .card-title {
            margin-top: 0;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .card-text {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .card-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/admin/index.php" class="navbar-brand">
                <img src="../assets/images/logopreto.png" alt="EB Cursos">
            </a>
            <div class="nav-links">
                <a href="/admin/index.php">Dashboard</a>
                <a href="/admin/cursos.php" class="active">Cursos</a>
                <a href="/admin/alunos.php">Alunos</a>
                <a href="/admin/matriculas.php">Matrículas</a>
                <a href="/admin/logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Gerenciar Cursos</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <h2 class="form-title">Cadastrar Novo Curso</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">Título do Curso</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Descrição</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="thumbnail">URL da Imagem (thumbnail)</label>
                    <input type="url" id="thumbnail" name="thumbnail" placeholder="https://exemplo.com/imagem.jpg">
                </div>
                
                <div class="form-group">
                    <label for="youtube_link">Link do Vídeo YouTube</label>
                    <input type="url" id="youtube_link" name="youtube_link" placeholder="https://www.youtube.com/watch?v=XXXXXXXXXXX">
                    <small style="display: block; margin-top: 5px; color: #666;">Cole aqui o link completo do vídeo do YouTube</small>
                </div>
                
                <button type="submit" name="cadastrar" class="btn">Cadastrar Curso</button>
            </form>
        </div>
        
        <h2>Cursos Cadastrados</h2>
        
        <?php if (empty($cursos)): ?>
            <p>Nenhum curso cadastrado ainda.</p>
        <?php else: ?>
            <div class="course-grid">
                <?php foreach ($cursos as $curso): ?>
                    <div class="course-card">
                        <?php if (!empty($curso['thumbnail'])): ?>
                            <img src="<?php echo htmlspecialchars($curso['thumbnail']); ?>" alt="<?php echo htmlspecialchars($curso['title']); ?>" class="card-img">
                        <?php else: ?>
                            <div class="card-img" style="background-color: #eee; display: flex; align-items: center; justify-content: center;">
                                <span style="color: #aaa;">Sem imagem</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h3 class="card-title"><?php echo htmlspecialchars($curso['title']); ?></h3>
                            <p class="card-text"><?php echo mb_substr(htmlspecialchars($curso['description']), 0, 100) . '...'; ?></p>
                            
                            <?php if (!empty($curso['youtube_link'])): ?>
                                <p class="card-text">
                                    <i class="fas fa-video" style="color: #c4302b;"></i> 
                                    <small>Vídeo do YouTube incluído</small>
                                </p>
                            <?php endif; ?>
                            
                            <div class="card-actions">
                                <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja excluir este curso?');">
                                    <input type="hidden" name="curso_id" value="<?php echo $curso['id']; ?>">
                                    <button type="submit" name="excluir" class="btn btn-danger">Excluir</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
