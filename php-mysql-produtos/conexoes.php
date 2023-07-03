<?php
require_once 'dados_acesso.php';
require_once 'utils.php';
mysqli_report(MYSQLI_REPORT_OFF);
function conectarPDO()
{
    try {
        $conn = new PDO(DSN . ':host=' . SERVIDOR . ';dbname=' . BANCODEDADOS, USUARIO, SENHA);
        console_log('Conexão com PDO realizada com sucesso!');
        return $conn;
    } catch (PDOException $e) {
// echo '<h3>Erro: ' . mb_convert_encoding($e->getMessage(), 'UTF-8', 'ISO-8859-1') . '</h3>';
        echo '<h3>Erro: ' . $e->getMessage() . '</h3>';
        exit();
    }
}
function conectarMySQLi_PD()
{
    $conn = @mysqli_connect(SERVIDOR, USUARIO, SENHA, BANCODEDADOS);
    if (!$conn) {
// die('<h3>Erro: ' . mb_convert_encoding(mysqli_connect_error(), 'UTF-8', 'ISO-8859-1') . '</h3>');
        die('<h3>Erro: ' . mysqli_connect_error() . '</h3>');
    } else {
        console_log('Conexão com MySQLi Procedural realizada com sucesso!');
    }
    return $conn;
}
function conectarMySQLi_OO()
{
    $conn = @new mysqli(SERVIDOR, USUARIO, SENHA, BANCODEDADOS);
    if ($conn->connect_error) {
// echo '<h3>Erro: ' . mb_convert_encoding($conn->connect_error, 'UTF-8', 'ISO-8859-1') . '</h3>';
        echo '<h3>Erro: ' . $conn->connect_error . '</h3>';
        exit();
    } else {
        console_log('Conexão com MySQLi Orientado a Objetos realizada com sucesso!');
    }
    return $conn;
}
