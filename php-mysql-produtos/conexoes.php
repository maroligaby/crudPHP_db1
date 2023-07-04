<?php
require_once 'dados_acesso.php';
require_once 'utils.php';
mysqli_report(MYSQLI_REPORT_OFF);


function verificaBD($conn) {
    $stmt = $conn->query('SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "' .
        BANCODEDADOS . '"');
    if (!$stmt->fetchColumn()) {
        $stmt = $conn->query('CREATE DATABASE IF NOT EXISTS ' . BANCODEDADOS);
    }
}
function verificaTabelaCategoria($conn) {
    $stmt = $conn->query('SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES
 WHERE (TABLE_SCHEMA = "'.BANCODEDADOS.'") AND (TABLE_NAME = "categoria")');
    if (!$stmt->fetchColumn()) {
        $stmt = $conn->query('CREATE TABLE IF NOT EXISTS categoria(
                codigo_ctg int primary key not null,
                descricao_ctg varchar(50) unique not null)ENGINE=InnoDB;');
        $stmt = $conn->query('INSERT INTO categoria (descricao_ctg) VALUES("laticineos"),("higiene");');
        $stmt->execute();
    }
}

function verificaTabelaProduto($conn) {
    $stmt = $conn->query('SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES
 WHERE (TABLE_SCHEMA = "'.BANCODEDADOS.'") AND (TABLE_NAME = "produto")');
    if (!$stmt->fetchColumn()) {
        $stmt = $conn->query('CREATE TABLE IF NOT EXISTS produto (
 codigo_prd INT AUTO_INCREMENT PRIMARY KEY,
descricao_prd VARCHAR(50) UNIQUE NOT NULL,
data_cadastro DATE default (current_timestamp()) not null,
preco DECIMAL (10,2) NOT NULL DEFAULT 0.0 CHECK (preco >=0),
ativo BOOLEAN NOT NULL DEFAULT true,
unidade CHAR(5) DEFAULT "un",
tipo_comissao ENUM ("s","f","p") not null default "s",
codigo_ctg int not null,
CONSTRAINT FOREIGN KEY (codigo_ctg)REFERENCES categoria(codigo_ctg),
foto longblob) ENGINE=InnoDB;');
        $foto = file_get_contents('default.png');
        $stmt->bindParam(':foto', $foto, PDO::PARAM_LOB);
        $stmt->execute();
    }
}
function conectarPDO()
{
    try {
        $conn = new PDO(DSN . ':host=' . SERVIDOR,
            USUARIO,
            SENHA);
        console_log('Conexão com PDO realizada com sucesso!');
        verificaBD($conn);
        $conn = new PDO(DSN . ':host=' . SERVIDOR . ';dbname=' . BANCODEDADOS,
            USUARIO,
            SENHA);
        verificaTabelaCategoria($conn);
        verificaTabelaProduto($conn);
        return $conn;
    } catch (PDOException $e) {
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
