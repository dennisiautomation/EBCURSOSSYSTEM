<?php
// Incluir arquivo de conexão com o banco
require_once 'includes/db.php';

// Definir diretamente o ID do curso que encontramos
$curso_id = 8; // ID do curso "Aula - 04"

// Buscar todos os alunos
$query_alunos = "SELECT id FROM " . TABELA_ALUNOS;
$result_alunos = mysqli_query($conn, $query_alunos);

if (!$result_alunos || mysqli_num_rows($result_alunos) == 0) {
    die("Nenhum aluno encontrado.");
}

$contador = 0;
$erros = 0;

// Matricular cada aluno no curso
while ($aluno = mysqli_fetch_assoc($result_alunos)) {
    $aluno_id = $aluno['id'];
    
    // Verificar se o aluno já está matriculado
    $check_query = "SELECT id FROM " . TABELA_MATRICULAS . " 
                    WHERE course_id = ? AND student_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ii", $curso_id, $aluno_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    // Se não estiver matriculado, matricular
    if (mysqli_num_rows($check_result) == 0) {
        $insert_query = "INSERT INTO " . TABELA_MATRICULAS . " 
                         (student_id, course_id, status, progress_percentage) 
                         VALUES (?, ?, 'active', 0)";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "ii", $aluno_id, $curso_id);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            $contador++;
        } else {
            $erros++;
            echo "Erro ao matricular aluno ID $aluno_id: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "Aluno ID $aluno_id já está matriculado no curso.<br>";
    }
}

echo "<h2>Matrícula em massa concluída</h2>";
echo "<p>$contador alunos foram matriculados com sucesso no curso 'Aula - 04' (ID: $curso_id).</p>";
echo "<p>$erros erros ocorreram durante o processo.</p>";
echo "<p><a href='/admin/matriculas.php'>Voltar para a página de matrículas</a></p>";
?>
