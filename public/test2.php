<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../includes/db.php';

echo "Conectado ao banco<br><br>";

// Lista todos os alunos
$query = "SELECT * FROM alunos";
$result = mysqli_query($conn, $query);

echo "<h3>Alunos cadastrados:</h3>";
while ($aluno = mysqli_fetch_assoc($result)) {
    echo "ID: " . $aluno['id'] . "<br>";
    echo "Nome: " . $aluno['nome'] . "<br>";
    echo "Email: " . $aluno['email'] . "<br>";
    echo "Senha: " . $aluno['senha'] . "<br>";
    echo "<hr>";
}

// Tenta login específico
$email = 'ericbezerra89@gmail.com';
$senha = 'ebcursos2024';

echo "<h3>Tentando login com:</h3>";
echo "Email: $email<br>";
echo "Senha: $senha<br><br>";

$query = "SELECT * FROM alunos WHERE email = '$email' AND senha = '$senha'";
echo "Query: " . $query . "<br><br>";

$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
    echo "LOGIN OK!";
} else {
    echo "LOGIN FALHOU!";
}
