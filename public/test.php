<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Teste 1: PHP funcionando<br>";

try {
    require_once __DIR__ . '/../includes/db.php';
    echo "Teste 2: Conexão com banco OK<br>";
    
    $query = "SELECT * FROM alunos LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $aluno = mysqli_fetch_assoc($result);
        echo "Teste 3: Query no banco OK<br>";
        echo "Primeiro aluno: " . $aluno['nome'] . "<br>";
    }
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}

echo "Teste 4: Script finalizado";
