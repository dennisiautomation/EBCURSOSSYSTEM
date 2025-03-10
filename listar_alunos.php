<?php
// Incluir arquivo de conexão com o banco
require_once 'includes/db.php';

// Buscar todos os alunos
$query = "SELECT id, nome, email FROM " . TABELA_ALUNOS;
$result = mysqli_query($conn, $query);

echo "<h2>Alunos Disponíveis</h2>";

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<p>Nenhum aluno encontrado na tabela ALUNOS.</p>";
} else {
    echo "<h3>Tabela ALUNOS</h3>";
    echo "<ul>";
    while ($aluno = mysqli_fetch_assoc($result)) {
        echo "<li>ID: {$aluno['id']} - Nome: {$aluno['nome']} - Email: {$aluno['email']}</li>";
    }
    echo "</ul>";
}

// Buscar todos os estudantes da tabela students
$query2 = "SELECT id, name, email FROM " . TABELA_STUDENTS;
$result2 = mysqli_query($conn, $query2);

if (!$result2 || mysqli_num_rows($result2) == 0) {
    echo "<p>Nenhum aluno encontrado na tabela STUDENTS.</p>";
} else {
    echo "<h3>Tabela STUDENTS</h3>";
    echo "<ul>";
    while ($aluno = mysqli_fetch_assoc($result2)) {
        echo "<li>ID: {$aluno['id']} - Nome: {$aluno['name']} - Email: {$aluno['email']}</li>";
    }
    echo "</ul>";
}
?>
