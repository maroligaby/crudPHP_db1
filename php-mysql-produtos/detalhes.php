<?php
require_once "conexoes.php";
require_once 'utils.php';
if (!isset($_GET["codigo_prd"])) {
    header("Location: consulta.php");
}
$conn = conectarPDO();
$codigo_prd = $_GET["codigo_prd"];
$stmt = $conn->prepare('SELECT codigo_prd, descricao_prd, data_cadastro, preco, tipo_comissao, ativo, c.descricao_ctg AS categoria, foto
FROM produto p 
JOIN categoria c ON p.codigo_ctg=c.codigo_ctg
WHERE p.codigo_prd = :codigo_prd ');
$stmt->bindParam(':codigo_prd', $codigo_prd);
$stmt->execute();
$produto = $stmt->fetch();
if (!$produto) {
    die('Falha no banco de dados!');
}
list($codigo_prd, $descricao_prd, $data_cadastro, $preco, $tipo_comissao, $ativo, $categoria, $foto) = $produto;
$data_cadastro = date('d/m/Y', strtotime($data_cadastro));
$preco = 'R$ ' . number_format($preco,2,',','.');
$tipos_comissao = ['s' => 'Sem comissão', 'f' => 'Comissão fixa', 'p' => 'Percentual de comissão'];
$tipo_comissao = $tipos_comissao[$produto['tipo_comissao']];
$ativo = $produto['ativo'] ? 'Sim' : 'Não';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Página de Detalhes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
          crossorigin="anonymous">
    <link rel="stylesheet" href="./style.css">
</head>
<body>
<div class="container table-responsive" id="detalhes_produto">
    <h2>Detalhes do Produto</h2>
    <hr>
    <ul>
        <li class="imagem">
            <?php echo '<img src="data:image/png;base64,' . base64_encode($foto) . '" width="200px"/>'; ?>
        </li>
        <li><b>Código: </b><?= $codigo_prd ?></li>
        <li><b>Descrição: </b><?= $descricao_prd ?></li>
        <li><b>Data Cadastro: </b><?= $data_cadastro ?></li>
        <li><b>Preço: </b><?= $preco ?></li>
        <li><b>Comissão: </b><?= $tipo_comissao ?></li>
        <li><b>Ativo: </b><?= $ativo ?></li>
        <li><b>Categoria: </b><?= $categoria ?></li>
    </ul>
    <hr>
    <button type="button" onclick="window.history.back()" class="btn btn-outline-danger btn-lg">
        <i class="fas fa-door-open"></i>
        Voltar
    </button>
</div>;
</body>
</html>