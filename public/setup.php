<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../includes/db.php';

// Criar tabela de alunos se não existir
$query = "CREATE TABLE IF NOT EXISTS alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $query)) {
    echo "Tabela alunos criada/verificada com sucesso!<br>";
} else {
    echo "Erro ao criar tabela alunos: " . mysqli_error($conn) . "<br>";
}

// Cadastrar aluno de teste
$nome = 'Eric Bezerra';
$email = 'ericbezerra89@gmail.com';
$senha = 'ebcursos2024';

// Primeiro deleta se existir
mysqli_query($conn, "DELETE FROM alunos WHERE email = '$email'");

// Depois insere
$query = "INSERT INTO alunos (nome, email, senha) VALUES ('$nome', '$email', '$senha')";
if (mysqli_query($conn, $query)) {
    echo "Aluno cadastrado com sucesso!<br>";
    echo "Email: $email<br>";
    echo "Senha: $senha<br>";
} else {
    echo "Erro ao cadastrar aluno: " . mysqli_error($conn) . "<br>";
}

// Criar tabela de admin se não existir
$query = "CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $query)) {
    echo "Tabela admin criada/verificada com sucesso!<br>";
} else {
    echo "Erro ao criar tabela admin: " . mysqli_error($conn) . "<br>";
}

// Cadastrar admin
$nome = 'Administrador';
$email = 'admin@ebcursos.com.br';
$senha = 'admin123';

// Primeiro deleta se existir
mysqli_query($conn, "DELETE FROM admin WHERE email = '$email'");

// Depois insere
$query = "INSERT INTO admin (nome, email, senha) VALUES ('$nome', '$email', '$senha')";
if (mysqli_query($conn, $query)) {
    echo "Admin cadastrado com sucesso!<br>";
    echo "Email: $email<br>";
    echo "Senha: $senha<br>";
} else {
    echo "Erro ao cadastrar admin: " . mysqli_error($conn) . "<br>";
}
