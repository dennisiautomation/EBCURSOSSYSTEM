<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    // Uso de prepared statement para aumentar a segurança
    $query = "SELECT * FROM " . TABELA_ADMIN . " WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        session_start();
        $admin = mysqli_fetch_assoc($result);
        
        // Verificar se a senha está correta (direto ou com hash)
        if ($senha == $admin['senha'] || password_verify($senha, $admin['senha'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['nome'];
            header('Location: index.php');
            exit;
        } else {
            $error = "Email ou senha inválidos";
        }
    } else {
        $error = "Email ou senha inválidos";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - EB Cursos</title>
    <link href="../assets/css/login.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <img src="../assets/images/logopreto.png" alt="EB Cursos">
            </div>
            
            <h2>Área Administrativa</h2>
            
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
                <a href="/login.php">Área do Aluno</a>
            </div>
        </div>
    </div>
</body>
</html>
