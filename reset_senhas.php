<?php
require_once 'includes/db.php';

echo "<h1>Resetando senhas dos alunos</h1>";

// Nova senha para todos os alunos
$nova_senha = 'FLGV123@';

// Criar hash da senha
$senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

// 1. Resetar senhas na tabela ALUNOS
$query_alunos = "UPDATE " . TABELA_ALUNOS . " SET senha = ?";
$stmt_alunos = mysqli_prepare($conn, $query_alunos);
mysqli_stmt_bind_param($stmt_alunos, "s", $senha_hash);
$resultado_alunos = mysqli_stmt_execute($stmt_alunos);

// Verificar quantos alunos foram atualizados na tabela ALUNOS
$alunos_atualizados = mysqli_affected_rows($conn);

// 2. Resetar senhas na tabela STUDENTS
$query_students = "UPDATE " . TABELA_STUDENTS . " SET password = ?";
$stmt_students = mysqli_prepare($conn, $query_students);
mysqli_stmt_bind_param($stmt_students, "s", $senha_hash);
$resultado_students = mysqli_stmt_execute($stmt_students);

// Verificar quantos alunos foram atualizados na tabela STUDENTS
$students_atualizados = mysqli_affected_rows($conn);

// Exibir resultados
echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>";

if ($resultado_alunos) {
    echo "<p style='color: green;'>✅ Senhas da tabela ALUNOS resetadas com sucesso!</p>";
    echo "<p>Total de alunos atualizados na tabela ALUNOS: <strong>{$alunos_atualizados}</strong></p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao resetar senhas na tabela ALUNOS: " . mysqli_error($conn) . "</p>";
}

if ($resultado_students) {
    echo "<p style='color: green;'>✅ Senhas da tabela STUDENTS resetadas com sucesso!</p>";
    echo "<p>Total de alunos atualizados na tabela STUDENTS: <strong>{$students_atualizados}</strong></p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao resetar senhas na tabela STUDENTS: " . mysqli_error($conn) . "</p>";
}

echo "<hr>";
echo "<p><strong>Nova senha para todos os alunos:</strong> {$nova_senha}</p>";
echo "<p>Os alunos agora podem fazer login usando seus emails e esta senha.</p>";
echo "</div>";
?>
