<?php
// Lista as conexões que a base tentou abrir. Vazio é o isolamento.
$caminho = '/tmp/fixar-saidas.log';
header('Content-Type: text/plain; charset=utf-8');
if (is_file($caminho)) {
    readfile($caminho);
}
