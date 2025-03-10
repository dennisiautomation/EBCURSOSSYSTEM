<?php
// Incluir arquivo de conexão com o banco
require_once 'includes/db.php';

// Aluno ID 65 - Aluno Teste
$aluno_id = 65;
$nova_senha = "123456";
$senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

// Atualizar a senha
$query = "UPDATE " . TABELA_ALUNOS . " SET senha = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "si", $senha_hash, $aluno_id);

if (mysqli_stmt_execute($stmt)) {
    echo "<h2>Senha redefinida com sucesso!</h2>";
    echo "<p>Aluno ID: $aluno_id</p>";
    echo "<p>Nova senha: $nova_senha</p>";
    echo "<p>Você pode fazer login agora usando:</p>";
    echo "<ul>";
    echo "<li>Email: teste@ebcursos.com.br</li>";
    echo "<li>Senha: $nova_senha</li>";
    echo "</ul>";
    
    echo "<p><a href='/login.php'>Ir para página de login</a></p>";
} else {
    echo "<h2>Erro ao redefinir senha</h2>";
    echo "<p>Erro: " . mysqli_error($conn) . "</p>";
}
?>
