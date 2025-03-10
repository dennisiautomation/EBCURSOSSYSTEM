<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    // Primeiro buscar aluno na tabela ALUNOS (original)
    $query = "SELECT id, nome, email, senha FROM " . TABELA_ALUNOS . " WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $aluno = mysqli_fetch_assoc($result);
        
        // Verificar se a senha está correta
        if (password_verify($senha, $aluno['senha']) || $senha == $aluno['senha']) {
            // Autenticação bem-sucedida na tabela ALUNOS

            // Verificar se o aluno já existe na tabela STUDENTS
            $check_query = "SELECT id FROM " . TABELA_STUDENTS . " WHERE email = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, "s", $email);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);

            if (mysqli_num_rows($check_result) == 0) {
                // Aluno não existe na tabela STUDENTS, vamos migrar
                $insert_query = "INSERT INTO " . TABELA_STUDENTS . " (name, email, password) VALUES (?, ?, ?)";
                $insert_stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($insert_stmt, "sss", $aluno['nome'], $aluno['email'], $aluno['senha']);
                mysqli_stmt_execute($insert_stmt);
                $student_id = mysqli_insert_id($conn);
            } else {
                // Aluno já existe na tabela STUDENTS, pegar o ID
                $student_row = mysqli_fetch_assoc($check_result);
                $student_id = $student_row['id'];
            }

            // Iniciar sessão e redirecionar
            session_start();
            $_SESSION['aluno_id'] = $student_id; // Usar o ID da tabela STUDENTS
            $_SESSION['aluno_email'] = $aluno['email'];
            $_SESSION['aluno_name'] = $aluno['nome'];
            header('Location: /aluno/index.php');
            exit;
        }
    } else {
        // Aluno não encontrado na tabela ALUNOS, verificar na tabela STUDENTS
        $query = "SELECT id, name as nome, email, password as senha FROM " . TABELA_STUDENTS . " WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $aluno = mysqli_fetch_assoc($result);
            
            // Verificar se a senha está correta
            if (password_verify($senha, $aluno['senha']) || $senha == $aluno['senha']) {
                session_start();
                $_SESSION['aluno_id'] = $aluno['id'];
                $_SESSION['aluno_email'] = $aluno['email'];
                $_SESSION['aluno_name'] = $aluno['nome'];
                header('Location: /aluno/index.php');
                exit;
            }
        }
    }
    
    $error = "Email ou senha inválidos";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de Aluno - EB Cursos</title>
    <link href="assets/css/login.css" rel="stylesheet">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f2f5;
        }
        
        .login-box {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo img {
            max-width: 200px;
        }
        
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        
        button:hover {
            background-color: #1d4ed8;
        }
        
        .error {
            background-color: #fef2f2;
            color: #b91c1c;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        a {
            color: #2563eb;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <img src="assets/images/logopreto.png" alt="EB Cursos">
            </div>
            
            <h2>Login de Aluno</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                
                <button type="submit">Entrar</button>
            </form>
            
            <div style="margin-top: 20px; text-align: center;">
                <a href="/admin/login.php">Área do Administrador</a>
            </div>
        </div>
    </div>
</body>
</html>
