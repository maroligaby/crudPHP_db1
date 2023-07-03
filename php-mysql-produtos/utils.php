<?php
function retornarDataAtual()
{
    date_default_timezone_set('America/Sao_Paulo');
    $dateTime = new DateTime();
    $formatter = new IntlDateFormatter(
        'pt_BR',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        'America/Sao_Paulo',
        IntlDateFormatter::GREGORIAN,
        "dd 'de' MMMM 'de' YYYY"
    );
    $retorno = $formatter->format($dateTime);
    return $retorno;
}
function console_log($output, $with_script_tags = true)
{
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}
