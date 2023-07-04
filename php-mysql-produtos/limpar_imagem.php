<?php
$imagem = 'default.png';
$imageData = base64_encode(file_get_contents($imagem));
$src = 'data: '.mime_content_type($imagem).';base64,'.$imageData;
echo json_encode([
    'arquivo' => $imagem,
    'msg' => $src
]);
?>
