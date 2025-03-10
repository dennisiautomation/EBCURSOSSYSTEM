<?php
require_once 'includes/db.php';

echo "<h2>Resumo de Alunos e Matrículas</h2>";

// Contar alunos na tabela STUDENTS
$query = "SELECT COUNT(*) as total FROM " . TABELA_STUDENTS;
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
echo "<p><strong>Total de alunos na tabela STUDENTS:</strong> " . $row['total'] . "</p>";

// Contar alunos na tabela ALUNOS
$query = "SELECT COUNT(*) as total FROM " . TABELA_ALUNOS;
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
echo "<p><strong>Total de alunos na tabela ALUNOS:</strong> " . $row['total'] . "</p>";

// Contar matrículas
$query = "SELECT COUNT(*) as total FROM " . TABELA_MATRICULAS;
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
echo "<p><strong>Total de matrículas:</strong> " . $row['total'] . "</p>";

// Listar os primeiros 5 alunos da tabela STUDENTS
echo "<h3>Primeiros 5 alunos da tabela STUDENTS:</h3>";
$query = "SELECT id, name, email FROM " . TABELA_STUDENTS . " ORDER BY id LIMIT 5";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Nenhum aluno encontrado na tabela STUDENTS.</p>";
}

// Listar matrículas se houver
echo "<h3>Últimas matrículas (limite de 5):</h3>";
$query = "SELECT m.id, m.student_id, s.name as aluno_nome, 
          m.course_id, c.title as curso_titulo, m.status
          FROM " . TABELA_MATRICULAS . " m
          JOIN " . TABELA_STUDENTS . " s ON m.student_id = s.id
          JOIN " . TABELA_CURSOS . " c ON m.course_id = c.id
          ORDER BY m.id DESC LIMIT 5";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Aluno</th><th>Curso</th><th>Status</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['aluno_nome']) . " (ID: " . $row['student_id'] . ")</td>";
        echo "<td>" . htmlspecialchars($row['curso_titulo']) . " (ID: " . $row['course_id'] . ")</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Nenhuma matrícula encontrada.</p>";
}
?>
