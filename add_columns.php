<?php
// Incluir arquivo de conexão com o banco
require_once 'includes/db.php';

// SQL para adicionar as colunas que faltam
$sql1 = "ALTER TABLE ".TABELA_CURSOS." ADD COLUMN thumbnail VARCHAR(255) NULL";
$sql2 = "ALTER TABLE ".TABELA_CURSOS." ADD COLUMN youtube_link VARCHAR(255) NULL";

// Executar os comandos SQL
try {
    // Primeira tentativa - adicionar coluna thumbnail
    $result1 = mysqli_query($conn, $sql1);
    if ($result1) {
        echo "Coluna 'thumbnail' adicionada com sucesso!<br>";
    } else {
        echo "Erro ao adicionar coluna 'thumbnail': " . mysqli_error($conn) . "<br>";
    }
    
    // Segunda tentativa - adicionar coluna youtube_link
    $result2 = mysqli_query($conn, $sql2);
    if ($result2) {
        echo "Coluna 'youtube_link' adicionada com sucesso!<br>";
    } else {
        echo "Erro ao adicionar coluna 'youtube_link': " . mysqli_error($conn) . "<br>";
    }
    
    echo "Atualização concluída! Agora você pode cadastrar cursos com thumbnails e links do YouTube.";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
