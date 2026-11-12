<?php

// E - 65 - xNome Razão Social 2-60
$mCabecalho['nomeCliente'] = trim($mCabecalho['nomeCliente']);
if((strlen($mCabecalho['nomeCliente']) < 2) || (strlen($mCabecalho['nomeCliente']) > 60)){
    $erros .= "Razão social cliente " . $mCabecalho['nomeCliente'] . " deve conter entre 2-60 caracteres! \n";
}

// E - 67 - xLgr Logradouro 2-60
$mCabecalho['enderecoCliente'] = trim($mCabecalho['enderecoCliente']);
if((strlen($mCabecalho['enderecoCliente']) < 2) || (strlen($mCabecalho['enderecoCliente']) > 60)){
    $erros .= "Endereço cliente " . $mCabecalho['enderecoCliente']. " deve conter entre 2-60 caracteres! \n";
}

// E - 68 - nro Número 1-60
$mCabecalho['numeroEnderecoCliente'] = trim($mCabecalho['numeroEnderecoCliente']);
if((strlen($mCabecalho['numeroEnderecoCliente']) < 1) || (strlen($mCabecalho['numeroEnderecoCliente']) > 60)){
    $erros .= "Número de endereço cliente " . $mCabecalho['numeroEnderecoCliente'] . " deve conter entre 1-60 caracteres! \n";
}

// E - 70 - xBairro Bairro 2-60
$mCabecalho['Bairro'] = trim($mCabecalho['Bairro']);
if((strlen($mCabecalho['bairroCliente']) < 2) || (strlen($mCabecalho['bairroCliente']) > 60)){
    $erros .= "Bairro cliente " . $mCabecalho['bairroCliente'] . " deve conter entre 2-60 caracteres! \n";
}

// E - 72 - xMun Nome do município 2-60
$mCabecalho['cidadeCliente'] = trim($mCabecalho['cidadeCliente']);
if((strlen($mCabecalho['cidadeCliente']) < 2) || (strlen($mCabecalho['cidadeCliente']) > 60)){
    $erros .= "Cidade cliente " . $mCabecalho['cidadeCliente'] . " deve conter entre 2-60 caracteres! \n";
}

// E - 74 - CEP 8
$mCabecalho['CEPCliente'] = trim(str_replace("-","",$mCabecalho['CEPCliente']));
if((strlen($mCabecalho['CEPCliente']) < 8) || (strlen($mCabecalho['CEPCliente']) > 8)){
    $erros .= "CEP cliente " . $mCabecalho['CEPCliente'] . " deve conter 8 caracteres! \n";
}
?>