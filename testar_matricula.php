<?php
require_once 'includes/db.php';

echo "<h2>Testando a matrícula de um aluno</h2>";

// Vamos usar o aluno com ID 3 (o primeiro aluno migrado) e o curso "Aula - 04"
// Primeiro, encontrar o ID do curso "Aula - 04"
$query = "SELECT id FROM " . TABELA_CURSOS . " WHERE title = 'Aula - 04'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $curso = mysqli_fetch_assoc($result);
    $curso_id = $curso['id'];
    
    // Agora vamos matricular o aluno ID 3 neste curso
    $aluno_id = 3; // Alana
    
    // Verificar se já existe uma matrícula
    $check_query = "SELECT id FROM " . TABELA_MATRICULAS . " WHERE student_id = ? AND course_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ii", $aluno_id, $curso_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo "<p>Este aluno já está matriculado neste curso.</p>";
    } else {
        // Inserir nova matrícula
        $insert_query = "INSERT INTO " . TABELA_MATRICULAS . " (student_id, course_id, status, progress_percentage) VALUES (?, ?, 'active', 0)";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "ii", $aluno_id, $curso_id);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            echo "<p>✅ Matrícula realizada com sucesso!</p>";
            echo "<p>Aluno ID: " . $aluno_id . " foi matriculado no curso ID: " . $curso_id . " (Aula - 04)</p>";
        } else {
            echo "<p>❌ Erro ao realizar matrícula: " . mysqli_error($conn) . "</p>";
        }
    }
} else {
    echo "<p>Curso 'Aula - 04' não encontrado.</p>";
}

// Verificar a matrícula
echo "<h3>Verificando matrículas após a tentativa:</h3>";
$query = "SELECT m.id, s.name as aluno_nome, c.title as curso_titulo 
          FROM " . TABELA_MATRICULAS . " m
          JOIN " . TABELA_STUDENTS . " s ON m.student_id = s.id
          JOIN " . TABELA_CURSOS . " c ON m.course_id = c.id
          ORDER BY m.id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Aluno</th><th>Curso</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['aluno_nome']) . "</td>";
        echo "<td>" . htmlspecialchars($row['curso_titulo']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Nenhuma matrícula encontrada após a tentativa.</p>";
}
?>
