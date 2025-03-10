<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../../includes/db.php';

// Buscar todos os alunos
$query = "SELECT * FROM " . TABELA_ALUNOS . " ORDER BY nome";
$result = mysqli_query($conn, $query);
$alunos = [];
if ($result) {
    while ($aluno = mysqli_fetch_assoc($result)) {
        $alunos[] = $aluno;
    }
}

// Processar formulário de novo aluno
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = password_hash($_POST['senha'] ?? '', PASSWORD_DEFAULT);
    
    $query = "INSERT INTO " . TABELA_ALUNOS . " (nome, email, senha) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $nome, $email, $senha);
    
    if (mysqli_stmt_execute($stmt)) {
        header('Location: alunos.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos - EB Cursos</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <style>
        .content {
            padding: 20px;
        }
        
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-novo {
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-novo:hover {
            background: #1d4ed8;
        }
        
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        th {
            font-weight: 600;
            color: #374151;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 500px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #374151;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        
        .btn-secondary:hover {
            background: #d1d5db;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Menu Lateral -->
        <aside class="sidebar">
            <div class="logo">
                <img src="../assets/images/logopreto.png" alt="EB Cursos">
            </div>
            
            <nav class="menu">
                <a href="dashboard.php">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
                <a href="cursos.php">
                    <i class="fas fa-graduation-cap"></i>
                    Cursos
                </a>
                <a href="alunos.php" class="active">
                    <i class="fas fa-users"></i>
                    Alunos
                </a>
                <a href="matriculas.php">
                    <i class="fas fa-clipboard-list"></i>
                    Matrículas
                </a>
                <a href="logout.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </a>
            </nav>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="content">
            <div class="top-bar">
                <h1>Gerenciar Alunos</h1>
                <button class="btn-novo" onclick="showModal()">
                    <i class="fas fa-plus"></i>
                    Novo Aluno
                </button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Data de Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($alunos)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center">Nenhum aluno cadastrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($alunos as $aluno): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($aluno['email']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($aluno['created_at'])); ?></td>
                                    <td>
                                        <button class="btn-icon" onclick="editarAluno(<?php echo $aluno['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon delete" onclick="excluirAluno(<?php echo $aluno['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal Novo Aluno -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <h2>Novo Aluno</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="hideModal()">Cancelar</button>
                    <button type="submit" class="btn-novo">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <script>
        function showModal() {
            document.getElementById('modal').style.display = 'flex';
        }
        
        function hideModal() {
            document.getElementById('modal').style.display = 'none';
        }
        
        function editarAluno(id) {
            // Implementar edição
            alert('Em breve: editar aluno ' + id);
        }
        
        function excluirAluno(id) {
            if (confirm('Tem certeza que deseja excluir este aluno?')) {
                // Implementar exclusão
                alert('Em breve: excluir aluno ' + id);
            }
        }
    </script>
</body>
</html>
