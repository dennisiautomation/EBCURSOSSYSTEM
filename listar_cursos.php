<?php
// Incluir arquivo de conexão com o banco
require_once 'includes/db.php';

// Buscar todos os cursos
$query = "SELECT id, title FROM " . TABELA_CURSOS;
$result = mysqli_query($conn, $query);

echo "<h2>Cursos Disponíveis</h2>";

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<p>Nenhum curso encontrado.</p>";
} else {
    echo "<ul>";
    while ($curso = mysqli_fetch_assoc($result)) {
        echo "<li>ID: {$curso['id']} - Título: {$curso['title']}</li>";
    }
    echo "</ul>";
}
?>
