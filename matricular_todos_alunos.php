<?php
require_once 'includes/db.php';

// Definir saída mais simples para evitar truncamento
header('Content-Type: text/plain');

echo "=== MATRICULANDO TODOS OS ALUNOS NO CURSO ===\n\n";

// 1. Verificar os cursos disponíveis
$query_cursos = "SELECT * FROM " . TABELA_CURSOS . " WHERE status = 'published'";
$result_cursos = mysqli_query($conn, $query_cursos);

if (!$result_cursos || mysqli_num_rows($result_cursos) == 0) {
    echo "ERRO: Não foram encontrados cursos publicados no sistema.\n";
    exit;
}

// Listar os cursos disponíveis
echo "CURSOS DISPONÍVEIS:\n";
while ($curso = mysqli_fetch_assoc($result_cursos)) {
    echo "- ID: " . $curso['id'] . " - " . $curso['title'] . "\n";
}
echo "\n";

// Resetar o cursor do resultado para reutilizar
mysqli_data_seek($result_cursos, 0);

// Usar o primeiro curso como padrão
$curso = mysqli_fetch_assoc($result_cursos);
$curso_id = $curso['id'];
$curso_nome = $curso['title'];

echo "Matriculando alunos no curso: " . $curso_nome . " (ID: {$curso_id})\n\n";

// 2. Buscar todos os alunos da tabela STUDENTS
$query_alunos = "SELECT id, name, email FROM " . TABELA_STUDENTS;
$result_alunos = mysqli_query($conn, $query_alunos);

if (!$result_alunos || mysqli_num_rows($result_alunos) == 0) {
    echo "ERRO: Não foram encontrados alunos no sistema.\n";
    exit;
}

// Preparar a consulta para verificar matrículas existentes
$check_query = "SELECT id FROM " . TABELA_MATRICULAS . " WHERE student_id = ? AND course_id = ?";
$check_stmt = mysqli_prepare($conn, $check_query);

// Preparar a consulta para inserir novas matrículas
$insert_query = "INSERT INTO " . TABELA_MATRICULAS . " (student_id, course_id, status, progress_percentage) VALUES (?, ?, 'active', 0)";
$insert_stmt = mysqli_prepare($conn, $insert_query);

// Contadores
$total_alunos = mysqli_num_rows($result_alunos);
$alunos_ja_matriculados = 0;
$alunos_matriculados = 0;
$erros = 0;

// Realizar matrículas
echo "PROCESSANDO MATRÍCULAS:\n";

while ($aluno = mysqli_fetch_assoc($result_alunos)) {
    $aluno_id = $aluno['id'];
    $aluno_nome = $aluno['name'];
    $aluno_email = $aluno['email'];
    
    echo "Aluno: {$aluno_id} - {$aluno_nome} ({$aluno_email}) - ";
    
    // Verificar se o aluno já está matriculado
    mysqli_stmt_bind_param($check_stmt, "ii", $aluno_id, $curso_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo "Já matriculado\n";
        $alunos_ja_matriculados++;
    } else {
        // Matricular o aluno
        mysqli_stmt_bind_param($insert_stmt, "ii", $aluno_id, $curso_id);
        if (mysqli_stmt_execute($insert_stmt)) {
            echo "Matriculado com sucesso\n";
            $alunos_matriculados++;
        } else {
            echo "Erro: " . mysqli_error($conn) . "\n";
            $erros++;
        }
    }
}

// Exibir resumo
echo "\nRESUMO DA OPERAÇÃO:\n";
echo "- Total de alunos: {$total_alunos}\n";
echo "- Alunos já matriculados: {$alunos_ja_matriculados}\n";
echo "- Alunos matriculados agora: {$alunos_matriculados}\n";
echo "- Erros: {$erros}\n";

// Verificar as matrículas
$matriculas_query = "SELECT COUNT(*) as total FROM " . TABELA_MATRICULAS;
$matriculas_result = mysqli_query($conn, $matriculas_query);
$matriculas = mysqli_fetch_assoc($matriculas_result);
echo "- Total de matrículas no sistema: {$matriculas['total']}\n";
?>
