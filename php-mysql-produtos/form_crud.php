<?php
require_once "conexoes.php";
require_once 'utils.php';
$conn = conectarPDO();
if(isset($_POST['submit'])) {
    if (array_key_exists('ativo', $_POST)) {
        $ativo = '1';
    } else{
        $ativo = '0';
    }
    if (!isset($_POST['codigo_prd'])) {
        $stmt = $conn->prepare('INSERT INTO produto (descricao_prd, data_cadastro, preco, tipo_comissao, ativo, codigo_ctg,
foto) VALUES(:descricao_prd, :data_cadastro, :preco, :tipo_comissao, :ativo, :codigo_ctg, :foto)');
        if (empty($_FILES['foto']['tmp_name'])) {
            $foto = file_get_contents('default.png');}
        else {
            $foto = file_get_contents($_FILES['foto']['tmp_name']);
        }
        $stmt->execute(array(
            ':descricao_prd' => $_POST['descricao_prd'],
            ':data_cadastro' => $_POST['data_cadastro'],
            ':preco' => $_POST['preco'],
            ':tipo_comissao' => $_POST['tipo_comissao'],
            ':ativo' => $ativo,
            ':codigo_ctg' => $_POST['codigo_ctg'],
            ':foto' => $foto
        ));}
    else {
        $estadoFoto = (boolean) $_COOKIE['fotoLimpada'];
        $sql = 'UPDATE produto SET descricao_prd = :descricao_prd, data_cadastro = :data_cadastro, preco = :preco, tipo_comissao = :tipo_comissao,
ativo = :ativo, codigo_ctg = :codigo_ctg';
        if (!empty($_FILES['foto']['tmp_name'])) {
            $sql .= ', foto = :foto';
            $foto = file_get_contents($_FILES['foto']['tmp_name']);}
        else if ($estadoFoto) {
            $sql .= ', foto = :foto';
            $foto = file_get_contents('default.png');
        }

        $sql .= ' WHERE codigo_prd = :codigo_prd';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':descricao_prd', $_POST['descricao_prd'], PDO::PARAM_STR);
        $stmt->bindParam(':data_cadastro', $_POST['data_cadastro'], PDO::PARAM_STR);
        $stmt->bindParam(':preco', $_POST['preco'], PDO::PARAM_STR);
        $stmt->bindParam(':tipo_comissao', $_POST['tipo_comissao'], PDO::PARAM_STR);
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_BOOL);
        $stmt->bindParam(':codigo_ctg', $_POST['codigo_ctg'], PDO::PARAM_STR);
        if (!empty($_FILES['foto']['tmp_name']) or $estadoFoto) {
            $stmt->bindParam(':foto', $foto, PDO::PARAM_LOB);
        }
        $stmt->bindParam(':codigo_prd', $_POST['codigo_prd'], PDO::PARAM_STR);
        $stmt->execute();
    }

    header("Location: consulta.php");
} else {
    $codigo_prd = $_GET["codigo_prd"] ?? null;
    if (is_null($codigo_prd)) {
        $operacao = 'Inclusão';
        $descricao_prd = ''; $data_cadastro = date('Y-m-d');
        $preco = 0; $tipo_comissao = 's';
        $ativo = true; $codigo_ctg = 0;
        $foto = null;
    } else {
        $operacao = 'Alteração';
        $stmt = $conn->prepare('SELECT codigo_prd, descricao_prd, data_cadastro, preco, tipo_comissao, ativo, codigo_ctg, foto
 FROM produto WHERE codigo_prd = :codigo_prd ');
        $stmt->bindParam(':codigo_prd', $codigo_prd);
        $stmt->execute();
        $produto = $stmt->fetch();
        if (!$produto) {
            die('Falha no banco de dados!');
        }
        list($codigo_prd, $descricao_prd, $data_cadastro, $preco, $tipo_comissao, $ativo, $codigo_ctg, $foto) = $produto;
    }
    $operacao .= ' de Produto';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
          crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@200;400;500;700&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
            crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-
2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <title>Cadastro de Produtos</title>
</head>
<body>
<div class="container my-2 col" id="crud">
    <div class="d-flex justify-content-center">
        <img src="https://portal.crea-sc.org.br/wp-content/uploads/2019/04/UNOESC-300x100.jpg"
             width="300px" />
    </div>
    <h5 class="alert alert-info mt-3 p-2"><?= $operacao ?></h5>
    <a href="consulta.php">Voltar</a>
    <hr>
    <form class="was-validated" id="form" class="row gx-3 gy-0" method="post" enctype=multipart/formdata>
        <?php
        if (!is_null($codigo_prd)) {
            echo '<input type="hidden" name="codigo_prd" id="icodigo_prd" class="form-control" value="' .
                $codigo_prd . '">';
        }
        ?>
        <div class="form-floating mb-2">
            <input type="text" name="descricao_prd" id="idescricao_prd" class="form-control" value="<?= $descricao_prd ?>"
                   placeholder="Digite o nome do produto" maxlength="60" required autofocus>
            <label for="idescricao_prd">Descrição do produto</label>
        </div>
        <div class="form-floating mb-2">
            <input type="date"
                   name="data_cadastro"
                   id="idata_cadastro"
                   class="form-control"
                   value="<?= $data_cadastro ?>"
                   placeholder="Data de cadastro"
                   required />
            <label for="idata_cadastroata_ca">Data de Cadastro</label>
        </div>
        <div class="input-group mb-2">
            <span class="input-group-text">$</span>
            <div class="form-floating ">
                <input type="number" name="preco" id="ipreco" class="form-control" value="<?=
                $preco ?>"
                       step="0.01" placeholder="Digite o preço do produto" required>
                <label for="ipreco">Preço</label>
            </div>
            <span class="input-group-text">,00</span>
        </div>
        <div class="row">
            <div class="mt-2 mb-2">
                <fieldset id="tipo_comissao" class="form-control">
                    <legend class="scheduler-border">Tipo de comissão</legend>
                    <div class="legenda">
                        <div class="form-check form-check-inline">
                            <input type="radio" name="tipo_comissao" id="idsem" value="s" class="form-checkinput"
                                <?= $tipo_comissao == 's' ? "checked" : null ?> />
                            <label for="idsem">Sem Comissão</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="tipo_comissao" id="idfixa" value="f" class="form-check-input"
                                <?= $tipo_comissao == 'f' ? "checked" : null ?> />
                            <label for="idfixa">Comissão fixa</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="tipo_comissao" id="idpercentual" value="p" class="form-check-input"
                                <?= $tipo_comissao == 'p' ? "checked" : null ?> />
                            <label for="idpercentual">Comissão por percentual</label>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="form-check mb-2">
            <input type="checkbox" name="ativo" id="iAtivo" class="form-check-input"
                <?= $ativo ? "checked" : null ?> >
            <label for="iAtivo" class="form-check-label">Ativo</label>
        </div>
        <div class="form-floating mb-1">
            <select class="form-select" name="codigo_ctg" id="icategoria" required>
                <option selected disabled value="">Escolha abaixo a categoria</option>
                <?php
                $stmt = $conn->query('SELECT * FROM categoria');
                while ($categoria = $stmt->fetch()) {
                    $selecionado = ($categoria['codigo_ctg'] == $codigo_ctg) ? 'selected' : '';
                    echo "<option $selecionado value={$categoria['codigo_ctg']}>{$categoria['descricao_ctg']}</option>";
                }
                ?>
            </select>
            <label for="id_curso">Categoria</label>
        </div>
        <div class="form-group">
            <div class="input-group mb-1 px-2 py-2 rounded-pill bg-white shadow-sm">
                <input id="iFoto" type="file" name="foto" id="iFoto" class="form-control"
                       accept="image/*">
                <label id="iFoto-label" for="iFoto" class="font-weight-light text-muted">Selecione uma
                    foto</label>
                <div class="input-group-append">
                    <label for="iFoto" class="btn btn-dark m-0 rounded-pill px-4">
                        <i class="fa fa-cloud-upload mr-2"></i>
                        <small class="text-uppercase font-weight-bold">Escolher o arquivo</small>
                    </label>
                </div>
            </div>
            <div id="area-imagem" class="mt-3 mb-3 mx-auto">
                <label for="iFoto">
                    <?php
                    if (is_null($foto)) {
                        echo '<img id="iImagem" src="default.png" height="125px" class="mx-auto
rounded shadow-sm"/>';
                    } else {
                        echo '<img id="iImagem" src="data:image/png;base64,' . base64_encode($foto) .
                            '" height="125px" class="mx-auto rounded shadow-sm"/>';
                    }
                    ?>
                </label>
            </div>
        </div>
        <div class="form-group mb-3 text-center">
            <button type="button" class="btn btn-warning" onclick="limparFoto()">
                <i class="fa-solid fa-eraser"></i>
                Limpar foto
            </button>
        </div>
        <hr>
        <div class="form-group mb-3 text-center">
            <div id="operacao" class="d-inline">
                <button type="submit" name="submit" class="btn btn-success">
                    <i class="fa-solid fa-check"></i>
                    Salvar
                </button>
            </div>
            <button type="button" class="btn btn-danger" onclick="window.location.href='consulta.php'">
                <i class="fa-solid fa-cancel"></i>
                Cancelar
            </button>
        </div>
    </form>
    <div id="mensagem"></div>
</div>
<script>
    function limparFoto() {
        const fotoForm = document.querySelector("#iFoto");
        fotoForm.value = '';
        document.cookie = 'fotoLimpada=1';
        $.ajax({
            url: './limpar_imagem.php',
            type: 'POST',
            dataType: "json",
        })
            .done(function(data) {
                const img = document.querySelector('#iImagem');
                img.setAttribute('src', data.msg);
                const fotoInfoForm = document.querySelector("#iFoto-label");
                fotoInfoForm.textContent = 'Selecione uma foto';
            });
    }
    function lerURL(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.querySelector('#iImagem');
                img.setAttribute('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    window.onload = function(e) {
        document.cookie = 'fotoLimpada=0';
        const fotoForm = document.querySelector("#iFoto");
        const fotoInfoForm = document.querySelector("#iFoto-label");
        fotoForm.addEventListener("change", (e) => {
            lerURL(fotoForm);
            const nomeArquivo = e.currentTarget.files[0].name;
            fotoInfoForm.textContent = "Arquivo: " + nomeArquivo;
            document.cookie = 'fotoLimpada=0';
        });
    };
</script>
</body>
</html>