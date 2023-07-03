<?php
require_once "conexoes.php";
require_once 'utils.php';
function listarDadosPDO($filtro='%%') {
    $conn = conectarPDO();
    $stmt = $conn->prepare('SELECT * FROM produto WHERE descricao_prd LIKE :descricao_prd');
    $stmt->bindParam(':descricao_prd', $filtro, PDO::PARAM_STR);
    $stmt->execute();
    echo '<div class="container table-responsive">';
    echo '<table class="table table-striped table-bordered table-hover">
 <caption>Relação de Produtos</caption>
 <thead class="table-dark">
 <tr>
 <th>Código</th>
<th>Descriçao</th>
<th>Cadastro</th>
<th>Preço (R$)</th>
 </tr>
 </thead>';
    while($produto = $stmt->fetch()) {
        $data_cadastro = date('d-m-Y', strtotime($produto['data_cadastro']));
        $preco = number_format($produto['preco'],2,',','.');
        echo "<tr>
 <td style='width: 10%;'>{$produto['codigo_prd']}</td>
 <td style='width: 40%;'>{$produto['descricao_prd']}</td>
 <td style='width: 25%;' class='text-center'>{$data_cadastro}</td>
 <td style='width: 25%;' class='text-end'>{$preco}</td>
 </tr>";
    }
    echo '<tfoot><tr><td colspan="5" style="text-align: center">Data atual: ' . retornarDataAtual() .
        '</td></tr>';
    echo '</table></div>';
    $stmt->execute();
    $produtos_num = $stmt->fetchAll(PDO::FETCH_NUM);
    $stmt->execute();
    $produtos_assoc = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->execute();
    $produtos_obj = $stmt->fetchAll(PDO::FETCH_OBJ);
    echo '<br><h4>Lista de Produtos (<code>PDO::FETCH_NUM</code> / <code>foreach()</code>)</h4>';
    echo '<ul>';
    foreach ($produtos_num as $produto) {
        echo "<li>Codigo: <strong>{$produto[0]}</strong> - ";
        echo "Descrição: <strong>{$produto[1]}</strong></li>";
    }
    echo '</ul>';
    echo '<br><h4>Lista de Produtos (<code>PDO::FETCH_ASSOC</code> / <code>foreach()</code> associativo)</h4>';
    echo '<ul>';
    foreach ($produtos_assoc as $produto) {
        echo '<li>';
        foreach ($produto as $campo => $valor) {
            echo "$campo: <strong>$valor</strong> - ";
        }
        echo '</li>';
    }
    echo '</ul>';
    echo '<br><h4>Lista de Produtos (<code>PDO::FETCH_NUM</code> / <code>for()</code> com matriz)</h4>';
    echo '<ul>';
    for ($i = 0; $i < $stmt->rowCount(); $i++) {
        echo "<li>Codigo: <strong>{$produtos_num[$i][0]}</strong> - ";
        echo "Descrição: <strong>{$produtos_num[$i][1]}</strong></li>";
    }
    echo '</ul>';
    echo '<br><h4>Lista de Produtos (<code>PDO::FETCH_OBJ</code> / <code>foreach()</code>)</h4>';
    echo '<ul>';
    foreach ($produtos_obj as $produto) {
        echo "<li>Código: <strong>{$produto->codigo_prd}</strong> - ";
        echo "Descrição: <strong>{$produto->descricao_prd}</strong></li>";
    }
    echo '</ul>';
    $stmt = null;
    $conn = null;
}