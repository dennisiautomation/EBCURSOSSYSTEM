<?php
session_start();
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';
$success = '';

// Função para verificar requisitos
function check_requirements() {
    $requirements = array();
    $requirements['php'] = version_compare(PHP_VERSION, '7.4.0', '>=');
    $requirements['mysql'] = extension_loaded('mysqli');
    $requirements['pdo'] = extension_loaded('pdo');
    $requirements['write'] = is_writable(__DIR__);
    return $requirements;
}

// Função para testar conexão
function test_database($host, $user, $pass, $name) {
    try {
        $conn = new mysqli($host, $user, $pass, $name);
        if ($conn->connect_error) {
            return false;
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Se form foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 2) {
        $db_host = $_POST['db_host'] ?? '';
        $db_user = $_POST['db_user'] ?? '';
        $db_pass = $_POST['db_pass'] ?? '';
        $db_name = $_POST['db_name'] ?? '';
        
        if (test_database($db_host, $db_user, $db_pass, $db_name)) {
            // Salva as configurações
            $config = "<?php\n";
            $config .= "// Configurações do banco de dados\n";
            $config .= "\$db_host = '$db_host';\n";
            $config .= "\$db_user = '$db_user';\n";
            $config .= "\$db_pass = '$db_pass';\n";
            $config .= "\$db_name = '$db_name';\n\n";
            $config .= "// Conexão com o banco\n";
            $config .= "\$conn = mysqli_connect(\$db_host, \$db_user, \$db_pass, \$db_name);\n\n";
            $config .= "// Verifica a conexão\n";
            $config .= "if (!\$conn) {\n";
            $config .= "    die(\"Erro de conexão: \" . mysqli_connect_error());\n";
            $config .= "}\n\n";
            $config .= "// Define o charset para UTF-8\n";
            $config .= "mysqli_set_charset(\$conn, \"utf8mb4\");";
            
            file_put_contents(__DIR__ . '/../includes/db.php', $config);
            
            // Criar aluno teste
            require_once '../includes/db.php';
            $nome = "Aluno Teste";
            $email = "teste@ebcursos.com.br";
            $cpf = "000.000.000-00";
            $senha = password_hash("teste123", PASSWORD_DEFAULT);

            $query = "INSERT INTO alunos (nome, email, cpf, senha) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssss", $nome, $email, $cpf, $senha);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Aluno teste criado com sucesso!";
            } else {
                $error = "Erro ao criar aluno teste: " . mysqli_error($conn);
            }
            
            header('Location: install.php?step=3');
            exit;
        } else {
            $error = "Não foi possível conectar ao banco de dados. Verifique as credenciais.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - EB Cursos</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --success-color: #059669;
            --danger-color: #dc2626;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.5;
            background: #f3f4f6;
            color: #1f2937;
        }
        .container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .install-box {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo img {
            max-width: 200px;
        }
        h1 {
            text-align: center;
            margin-bottom: 1rem;
            color: #111827;
        }
        .steps {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            gap: 1rem;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #6b7280;
        }
        .step.active {
            background: var(--primary-color);
            color: white;
        }
        .step.done {
            background: var(--success-color);
            color: white;
        }
        .requirements {
            margin-bottom: 2rem;
        }
        .requirement {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-bottom: 0.5rem;
        }
        .requirement.success {
            border-color: #059669;
            background: #ecfdf5;
        }
        .requirement.error {
            border-color: #dc2626;
            background: #fef2f2;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 1rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn:hover {
            background: var(--primary-dark);
        }
        .btn-success {
            background: var(--success-color);
        }
        .btn-success:hover {
            background: #047857;
        }
        .error-message {
            background: #fef2f2;
            color: #dc2626;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        .success-message {
            background: #ecfdf5;
            color: #059669;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="install-box">
            <div class="logo">
                <img src="assets/images/logopreto.png" alt="EB Cursos">
            </div>
            
            <div class="steps">
                <div class="step <?php echo $step >= 1 ? 'active' : ''; ?><?php echo $step > 1 ? ' done' : ''; ?>">1</div>
                <div class="step <?php echo $step >= 2 ? 'active' : ''; ?><?php echo $step > 2 ? ' done' : ''; ?>">2</div>
                <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>">3</div>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($step === 1): ?>
                <h1>Verificação do Sistema</h1>
                <div class="requirements">
                    <?php
                    $requirements = check_requirements();
                    $can_proceed = true;
                    ?>
                    
                    <div class="requirement <?php echo $requirements['php'] ? 'success' : 'error'; ?>">
                        <span>PHP 7.4 ou superior</span>
                        <span><?php echo PHP_VERSION; ?></span>
                    </div>
                    
                    <div class="requirement <?php echo $requirements['mysql'] ? 'success' : 'error'; ?>">
                        <span>Extensão MySQL</span>
                        <span><?php echo $requirements['mysql'] ? 'OK' : 'Não encontrada'; ?></span>
                    </div>
                    
                    <div class="requirement <?php echo $requirements['pdo'] ? 'success' : 'error'; ?>">
                        <span>Extensão PDO</span>
                        <span><?php echo $requirements['pdo'] ? 'OK' : 'Não encontrada'; ?></span>
                    </div>
                    
                    <div class="requirement <?php echo $requirements['write'] ? 'success' : 'error'; ?>">
                        <span>Permissões de escrita</span>
                        <span><?php echo $requirements['write'] ? 'OK' : 'Erro'; ?></span>
                    </div>
                </div>

                <?php if ($requirements['php'] && $requirements['mysql'] && $requirements['pdo'] && $requirements['write']): ?>
                    <div style="text-align: center;">
                        <a href="?step=2" class="btn">Continuar</a>
                    </div>
                <?php else: ?>
                    <div class="error-message">
                        Por favor, corrija os requisitos acima antes de continuar.
                    </div>
                <?php endif; ?>

            <?php elseif ($step === 2): ?>
                <h1>Configuração do Banco de Dados</h1>
                <form method="POST">
                    <div class="form-group">
                        <label for="db_host">Host</label>
                        <input type="text" id="db_host" name="db_host" value="localhost" required>
                    </div>

                    <div class="form-group">
                        <label for="db_name">Nome do Banco</label>
                        <input type="text" id="db_name" name="db_name" value="u850202022_flvg" required>
                    </div>

                    <div class="form-group">
                        <label for="db_user">Usuário</label>
                        <input type="text" id="db_user" name="db_user" value="u850202022_flvg" required>
                    </div>

                    <div class="form-group">
                        <label for="db_pass">Senha</label>
                        <input type="password" id="db_pass" name="db_pass" required>
                    </div>

                    <div style="text-align: center;">
                        <button type="submit" class="btn">Configurar</button>
                    </div>
                </form>

            <?php elseif ($step === 3): ?>
                <h1>Instalação Concluída!</h1>
                <div style="text-align: center; margin: 2rem 0;">
                    <p>O sistema foi configurado com sucesso!</p>
                    <p>Você já pode fazer login como administrador:</p>
                    <p style="margin: 1rem 0;">
                        <strong>Email:</strong> admin@ebcursos.com.br<br>
                        <strong>Senha:</strong> admin123
                    </p>
                    <p>Os alunos podem fazer login com:</p>
                    <p style="margin: 1rem 0;">
                        <strong>Email:</strong> email cadastrado<br>
                        <strong>Senha:</strong> ebcursos2024
                    </p>
                </div>
                <div style="text-align: center;">
                    <a href="/login.php" class="btn btn-success">Ir para Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
