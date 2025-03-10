<?php
// Incluir arquivo de conexão com o banco
require_once 'includes/db.php';

// 1. Criar um novo aluno na tabela students
$nome = "Aluno Teste YouTube";
$email = "teste.youtube@ebcursos.com.br";
$senha = "123456";
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Verificar se o aluno já existe
$check_query = "SELECT id FROM " . TABELA_STUDENTS . " WHERE email = ?";
$check_stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($check_stmt, "s", $email);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) > 0) {
    // Aluno já existe, usar o ID existente
    $student = mysqli_fetch_assoc($check_result);
    $student_id = $student['id'];
    echo "<h2>Aluno já existe na tabela students</h2>";
    echo "<p>ID: $student_id</p>";
    echo "<p>Email: $email</p>";
} else {
    // Inserir novo aluno
    $insert_query = "INSERT INTO " . TABELA_STUDENTS . " (name, email, password) VALUES (?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, "sss", $nome, $email, $senha_hash);
    
    if (mysqli_stmt_execute($insert_stmt)) {
        $student_id = mysqli_insert_id($conn);
        echo "<h2>Novo aluno criado com sucesso!</h2>";
        echo "<p>ID: $student_id</p>";
        echo "<p>Nome: $nome</p>";
        echo "<p>Email: $email</p>";
        echo "<p>Senha: $senha</p>";
    } else {
        die("Erro ao criar aluno: " . mysqli_error($conn));
    }
}

// 2. Matricular o aluno no curso "Aula - 04" (ID: 8)
$curso_id = 8;

// Verificar se já existe matrícula
$check_matricula = "SELECT id FROM " . TABELA_MATRICULAS . " WHERE student_id = ? AND course_id = ?";
$check_stmt = mysqli_prepare($conn, $check_matricula);
mysqli_stmt_bind_param($check_stmt, "ii", $student_id, $curso_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) > 0) {
    echo "<h2>Aluno já está matriculado neste curso</h2>";
} else {
    // Matricular o aluno
    $insert_query = "INSERT INTO " . TABELA_MATRICULAS . " (student_id, course_id, status, progress_percentage) VALUES (?, ?, 'active', 0)";
    $insert_stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, "ii", $student_id, $curso_id);
    
    if (mysqli_stmt_execute($insert_stmt)) {
        echo "<h2>Matrícula realizada com sucesso!</h2>";
        echo "<p>Aluno ID: $student_id foi matriculado no curso ID: $curso_id</p>";
        echo "<p>Você pode fazer login agora usando:</p>";
        echo "<ul>";
        echo "<li>Email: $email</li>";
        echo "<li>Senha: $senha</li>";
        echo "</ul>";
    } else {
        echo "<h2>Erro ao realizar matrícula</h2>";
        echo "<p>Erro: " . mysqli_error($conn) . "</p>";
    }
}

echo "<p><a href='/login.php'>Ir para página de login</a></p>";
?>
