<?php
require_once 'includes/db.php';

echo "<h2>Migrando alunos da tabela ALUNOS para STUDENTS</h2>";

// Verificar se já existem emails duplicados
$check_duplicates = "SELECT a.email 
                    FROM " . TABELA_ALUNOS . " a 
                    JOIN " . TABELA_STUDENTS . " s ON a.email = s.email";
$duplicate_result = mysqli_query($conn, $check_duplicates);

$duplicates = [];
if ($duplicate_result && mysqli_num_rows($duplicate_result) > 0) {
    while ($row = mysqli_fetch_assoc($duplicate_result)) {
        $duplicates[] = $row['email'];
    }
    
    echo "<p>Encontrados " . count($duplicates) . " emails duplicados que serão ignorados:</p>";
    echo "<ul>";
    foreach ($duplicates as $email) {
        echo "<li>" . htmlspecialchars($email) . "</li>";
    }
    echo "</ul>";
}

// Buscar todos os alunos da tabela ALUNOS que não estão na tabela STUDENTS
$query = "SELECT a.id, a.nome, a.email, a.senha 
          FROM " . TABELA_ALUNOS . " a
          LEFT JOIN " . TABELA_STUDENTS . " s ON a.email = s.email
          WHERE s.id IS NULL";
$result = mysqli_query($conn, $query);

$migrated_count = 0;
$failed_count = 0;

if ($result && mysqli_num_rows($result) > 0) {
    echo "<p>Encontrados " . mysqli_num_rows($result) . " alunos para migrar.</p>";
    
    // Preparar a instrução de inserção
    $insert_query = "INSERT INTO " . TABELA_STUDENTS . " (name, email, password) VALUES (?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_query);
    
    if ($insert_stmt) {
        mysqli_stmt_bind_param($insert_stmt, "sss", $nome, $email, $senha);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $nome = $row['nome'];
            $email = $row['email'];
            $senha = $row['senha'];
            
            if (mysqli_stmt_execute($insert_stmt)) {
                $migrated_count++;
                echo "<p>✅ Migrado: " . htmlspecialchars($nome) . " (" . htmlspecialchars($email) . ")</p>";
            } else {
                $failed_count++;
                echo "<p>❌ Falha ao migrar: " . htmlspecialchars($nome) . " (" . htmlspecialchars($email) . ") - Erro: " . mysqli_error($conn) . "</p>";
            }
        }
        
        mysqli_stmt_close($insert_stmt);
    } else {
        echo "<p>Erro ao preparar a instrução de inserção: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p>Nenhum aluno novo para migrar.</p>";
}

echo "<h2>Resumo da Migração</h2>";
echo "<p>Total de alunos migrados com sucesso: " . $migrated_count . "</p>";
echo "<p>Total de falhas na migração: " . $failed_count . "</p>";
echo "<p>Total de emails duplicados (ignorados): " . count($duplicates) . "</p>";

echo "<p><a href='listar_alunos_tabela_students.php'>Verificar alunos nas tabelas</a></p>";
?>
