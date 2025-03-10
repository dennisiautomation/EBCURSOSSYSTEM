<?php
// Configurações do banco de dados remoto
$db_host = 'srv1719.hstgr.io'; // ou '193.203.175.151'
$db_user = 'u850202022_flvg';
$db_pass = 'Laura0202@@@';
$db_name = 'u850202022_flvg';

// Configurar timeout mais alto para conexões lentas
ini_set('default_socket_timeout', 60);
ini_set('mysql.connect_timeout', 60);
ini_set('max_execution_time', 60);

// Conexão com o banco
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Verifica a conexão
if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}

// Define o charset para UTF-8
mysqli_set_charset($conn, "utf8mb4");

// Aumentar tempo limite para consultas
mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 60);

// Definir tabelas para compatibilidade
define('TABELA_ALUNOS', 'alunos');  // Já existe no banco
define('TABELA_STUDENTS', 'students'); // Esta tabela também existe e é relacionada com enrollments
define('TABELA_ADMIN', 'admin');    // Já existe no banco
define('TABELA_CURSOS', 'courses'); // Em vez de 'cursos'
define('TABELA_MATRICULAS', 'enrollments'); // Em vez de 'matriculas'
