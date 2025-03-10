<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_admin_auth();

$success = "";
$error = "";

// Processar nova matrícula
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['matricular'])) {
    $aluno_id = $_POST['aluno_id'] ?? '';
    $curso_id = $_POST['curso_id'] ?? '';
    
    if (empty($aluno_id) || empty($curso_id)) {
        $error = "Por favor, selecione um aluno e um curso.";
    } else {
        // Verificar se já existe uma matrícula
        $check_query = "SELECT id FROM " . TABELA_MATRICULAS . " WHERE student_id = ? AND course_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "ii", $aluno_id, $curso_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Este aluno já está matriculado neste curso.";
        } else {
            // Inserir nova matrícula
            $insert_query = "INSERT INTO " . TABELA_MATRICULAS . " (student_id, course_id, status, progress_percentage) VALUES (?, ?, 'active', 0)";
            $insert_stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($insert_stmt, "ii", $aluno_id, $curso_id);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                $success = "Matrícula realizada com sucesso!";
            } else {
                $error = "Erro ao realizar matrícula: " . mysqli_error($conn);
            }
        }
    }
}

// Processar cancelamento de matrícula
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar'])) {
    $matricula_id = $_POST['matricula_id'] ?? '';
    
    if (empty($matricula_id)) {
        $error = "Matrícula não especificada.";
    } else {
        $delete_query = "DELETE FROM " . TABELA_MATRICULAS . " WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $matricula_id);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            $success = "Matrícula cancelada com sucesso!";
        } else {
            $error = "Erro ao cancelar matrícula: " . mysqli_error($conn);
        }
    }
}

// Buscar todas as matrículas
$query = "SELECT m.id, m.status, m.progress_percentage, 
          s.name as aluno_nome, s.email as aluno_email,
          c.title as curso_titulo
          FROM " . TABELA_MATRICULAS . " m
          JOIN " . TABELA_STUDENTS . " s ON m.student_id = s.id
          JOIN " . TABELA_CURSOS . " c ON m.course_id = c.id
          ORDER BY m.id DESC";
$result = mysqli_query($conn, $query);
$matriculas = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $matriculas[] = $row;
    }
}

// Buscar alunos para o formulário
$alunos_query = "SELECT id, name as nome, email FROM " . TABELA_STUDENTS . " ORDER BY nome";
$alunos_result = mysqli_query($conn, $alunos_query);
$alunos = [];
if ($alunos_result) {
    while ($row = mysqli_fetch_assoc($alunos_result)) {
        $alunos[] = $row;
    }
}

// Buscar cursos para o formulário
$cursos_query = "SELECT id, title FROM " . TABELA_CURSOS . " WHERE status = 'published' ORDER BY title";
$cursos_result = mysqli_query($conn, $cursos_query);
$cursos = [];
if ($cursos_result) {
    while ($row = mysqli_fetch_assoc($cursos_result)) {
        $cursos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Matrículas - EB Cursos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 1rem;
            align-items: end;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        
        select, input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.875rem;
        }
        
        .btn-red {
            background-color: #dc2626;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        th {
            font-weight: 600;
            color: #374151;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
        }
        
        .progress-bar {
            height: 0.5rem;
            background-color: #e5e7eb;
            border-radius: 9999px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #2563eb;
            border-radius: 9999px;
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
                <a href="/admin/cursos.php">Cursos</a>
                <a href="/admin/alunos.php">Alunos</a>
                <a href="/admin/matriculas.php" class="active">Matrículas</a>
                <a href="/admin/logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container" style="padding: 2rem;">
        <header style="margin-bottom: 2rem;">
            <h1>Gerenciar Matrículas</h1>
            <p>Matricule alunos em cursos e gerencie as matrículas existentes</p>
        </header>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <strong>Sucesso!</strong> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <strong>Erro!</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2 style="margin-top: 0; margin-bottom: 1rem;">Nova Matrícula</h2>
            
            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="aluno_id">Aluno</label>
                        <select name="aluno_id" id="aluno_id" required>
                            <option value="">Selecione um aluno</option>
                            <?php foreach ($alunos as $aluno): ?>
                                <option value="<?php echo $aluno['id']; ?>"><?php echo htmlspecialchars($aluno['nome']) . ' (' . htmlspecialchars($aluno['email']) . ')'; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="curso_id">Curso</label>
                        <select name="curso_id" id="curso_id" required>
                            <option value="">Selecione um curso</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?php echo $curso['id']; ?>"><?php echo htmlspecialchars($curso['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <button type="submit" name="matricular" class="btn">Matricular Aluno</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h2 style="margin-top: 0; margin-bottom: 1rem;">Matrículas Existentes</h2>
            
            <?php if (empty($matriculas)): ?>
                <div class="empty-state">
                    <h3>Nenhuma matrícula encontrada</h3>
                    <p>As matrículas que você criar aparecerão aqui.</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Aluno</th>
                                <th>Curso</th>
                                <th>Status</th>
                                <th>Progresso</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matriculas as $matricula): ?>
                                <tr>
                                    <td><?php echo $matricula['id']; ?></td>
                                    <td><?php echo htmlspecialchars($matricula['aluno_nome']); ?><br>
                                        <small><?php echo htmlspecialchars($matricula['aluno_email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($matricula['curso_titulo']); ?></td>
                                    <td><?php echo ucfirst($matricula['status']); ?></td>
                                    <td>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $matricula['progress_percentage']; ?>%"></div>
                                        </div>
                                        <small><?php echo $matricula['progress_percentage']; ?>%</small>
                                    </td>
                                    <td>
                                        <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja cancelar esta matrícula?');">
                                            <input type="hidden" name="matricula_id" value="<?php echo $matricula['id']; ?>">
                                            <button type="submit" name="cancelar" class="btn btn-red">Cancelar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
