<?php
require_once "conexoes.php";
$codigo_prd = $_REQUEST['codigo_prd'];
if($codigo_prd) {
    $conn = conectarPDO();
    $sql = 'DELETE FROM produto WHERE codigo_prd=:codigo_prd';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':codigo_prd', $codigo_prd, PDO::PARAM_STR);
    if ($stmt->execute()) {
        if ($stmt->rowCount()) {
            echo json_encode(array('statusCode' => 200));
        } else {
            echo json_encode(array('statusCode' => 201));
        }
    }
}
?>


