<?php
namespace hardness;

error_reporting(E_ALL ^ E_DEPRECATED || E_NOTICE);

function b133_digitoVerificador_nossonumero($numero)
{
    $numero = preg_replace('/[^0-9]/', '', (string)$numero);
    $numero = str_pad(substr($numero, -13), 13, '0', STR_PAD_LEFT);
    $pesos = '2765432765432';
    $soma = 0;

    for ($i = 0; $i < 13; $i++) {
        $soma += ((int)$numero[$i]) * ((int)$pesos[$i]);
    }

    $resto = $soma % 11;
    if ($resto == 0) {
        return '0';
    }
    if ($resto == 1) {
        return 'P';
    }

    return (string)(11 - $resto);
}

function b133_digitoVerificador_barra($numero)
{
    $resto2 = b133_modulo_11($numero, 9, 1);
    $digito = 11 - $resto2;
    if ($digito == 0 || $digito == 1 || $digito > 9) {
        return 1;
    }
    return $digito;
}

function b133_formata_numero($numero, $loop, $insert, $tipo = "geral")
{
    $numero = (string)$numero;

    if ($tipo == "geral") {
        $numero = preg_replace('/[^0-9]/', '', $numero);
        while (strlen($numero) < $loop) {
            $numero = $insert . $numero;
        }
    }

    if ($tipo == "valor") {
        $numero = str_replace(",", "", $numero);
        $numero = str_replace(".", "", $numero);
        $numero = preg_replace('/[^0-9]/', '', $numero);
        while (strlen($numero) < $loop) {
            $numero = $insert . $numero;
        }
    }

    if ($tipo == "convenio") {
        while (strlen($numero) < $loop) {
            $numero = $numero . $insert;
        }
    }

    return substr($numero, -$loop);
}

function b133_normaliza_texto($texto)
{
    $texto = strtoupper(trim((string)$texto));
    $texto = strtr($texto, array(
        'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O',
        'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'Ç' => 'C',
    ));
    return preg_replace('/[^A-Z0-9]/', '', $texto);
}

function b133_normaliza_decimal($valor)
{
    if ($valor === null || $valor === '') {
        return 0.0;
    }

    if (is_string($valor)) {
        $valor = trim($valor);
        $ultimaVirgula = strrpos($valor, ',');
        $ultimoPonto = strrpos($valor, '.');

        if ($ultimaVirgula !== false && $ultimoPonto !== false) {
            if ($ultimaVirgula > $ultimoPonto) {
                // Formato brasileiro: 1.000,00.
                $valor = str_replace('.', '', $valor);
                $valor = str_replace(',', '.', $valor);
            } else {
                // Formato internacional: 1,000.00.
                $valor = str_replace(',', '', $valor);
            }
        } elseif ($ultimaVirgula !== false) {
            $valor = str_replace(',', '.', $valor);
        }
    }

    return round((float)$valor, 2);
}

function b133_resolve_especie_titulo($especieDocumento = '', $especieVisual = '')
{
    $mapa = array(
        'R$' => array('codigo_remessa' => '02', 'sigla_boleto' => 'DM'),
        'RS' => array('codigo_remessa' => '02', 'sigla_boleto' => 'DM'),
        'REAL' => array('codigo_remessa' => '02', 'sigla_boleto' => 'DM'),
        '01' => array('codigo_remessa' => '01', 'sigla_boleto' => 'CH'),
        'CH' => array('codigo_remessa' => '01', 'sigla_boleto' => 'CH'),
        'CHEQUE' => array('codigo_remessa' => '01', 'sigla_boleto' => 'CH'),
        '02' => array('codigo_remessa' => '02', 'sigla_boleto' => 'DM'),
        'DM' => array('codigo_remessa' => '02', 'sigla_boleto' => 'DM'),
        'DUPLICATAMERCANTIL' => array('codigo_remessa' => '02', 'sigla_boleto' => 'DM'),
        '04' => array('codigo_remessa' => '04', 'sigla_boleto' => 'DS'),
        'DS' => array('codigo_remessa' => '04', 'sigla_boleto' => 'DS'),
        'DUPLICATADESERVICO' => array('codigo_remessa' => '04', 'sigla_boleto' => 'DS'),
        '06' => array('codigo_remessa' => '06', 'sigla_boleto' => 'DR'),
        'DR' => array('codigo_remessa' => '06', 'sigla_boleto' => 'DR'),
        'DUPLICATARURAL' => array('codigo_remessa' => '06', 'sigla_boleto' => 'DR'),
        '07' => array('codigo_remessa' => '07', 'sigla_boleto' => 'LC'),
        'LC' => array('codigo_remessa' => '07', 'sigla_boleto' => 'LC'),
        'LETRADECAMBIO' => array('codigo_remessa' => '07', 'sigla_boleto' => 'LC'),
        '12' => array('codigo_remessa' => '12', 'sigla_boleto' => 'NP'),
        'NP' => array('codigo_remessa' => '12', 'sigla_boleto' => 'NP'),
        'NOTAPROMISSORIA' => array('codigo_remessa' => '12', 'sigla_boleto' => 'NP'),
        '17' => array('codigo_remessa' => '17', 'sigla_boleto' => 'RC'),
        'RC' => array('codigo_remessa' => '17', 'sigla_boleto' => 'RC'),
        'RECIBO' => array('codigo_remessa' => '17', 'sigla_boleto' => 'RC'),
        '19' => array('codigo_remessa' => '19', 'sigla_boleto' => 'ND'),
        'ND' => array('codigo_remessa' => '19', 'sigla_boleto' => 'ND'),
        'NOTADEDEBITO' => array('codigo_remessa' => '19', 'sigla_boleto' => 'ND'),
        '26' => array('codigo_remessa' => '26', 'sigla_boleto' => 'WR'),
        'WR' => array('codigo_remessa' => '26', 'sigla_boleto' => 'WR'),
        'WARRANT' => array('codigo_remessa' => '26', 'sigla_boleto' => 'WR'),
        '30' => array('codigo_remessa' => '30', 'sigla_boleto' => 'EC'),
        'EC' => array('codigo_remessa' => '30', 'sigla_boleto' => 'EC'),
        'ENCARGOSCONDOMINIAIS' => array('codigo_remessa' => '30', 'sigla_boleto' => 'EC'),
        '99' => array('codigo_remessa' => '99', 'sigla_boleto' => 'OU'),
        'OUTROS' => array('codigo_remessa' => '99', 'sigla_boleto' => 'OU'),
    );

    $candidatos = array(
        strtoupper(trim((string)$especieDocumento)),
        b133_normaliza_texto($especieDocumento),
        strtoupper(trim((string)$especieVisual)),
        b133_normaliza_texto($especieVisual),
    );

    foreach ($candidatos as $candidato) {
        if ($candidato !== '' && isset($mapa[$candidato])) {
            return $mapa[$candidato];
        }
    }

    return array(
        'codigo_remessa' => '02',
        'sigla_boleto' => 'DM',
    );
}

function b133_nosso_numero_info($nossoNumero, $carteira = '09')
{
    $nossoNumeroOriginal = strtoupper(trim((string)$nossoNumero));
    $base = '';
    $carteira = preg_replace('/[^0-9]/', '', (string)$carteira);
    $carteira = ($carteira === '') ? '09' : str_pad(substr($carteira, -2), 2, '0', STR_PAD_LEFT);

    if (preg_match('/^(\d{2})\/(\d{1,11})\-[0-9A-Z]$/', $nossoNumeroOriginal, $partes)) {
        if ($partes[1] !== $carteira) {
            return array(
                'base' => '',
                'dv' => '',
                'erro' => "Carteira do Nosso Número inválida: esperado {$carteira}, recebido {$partes[1]}",
            );
        }

        $base = $partes[2];
    } elseif (preg_match('/^\d{1,11}$/', $nossoNumeroOriginal)) {
        // Número sem separadores é sempre a sequência completa, nunca carteira + DV.
        $base = $nossoNumeroOriginal;
    } else {
        return array(
            'base' => '',
            'dv' => '',
            'erro' => 'Nosso Número Cresol inválido. Informe até 11 dígitos ou o formato ' . $carteira . '/<base>-<dv>.',
        );
    }

    $base = str_pad(substr(preg_replace('/[^0-9]/', '', $base), -11), 11, '0', STR_PAD_LEFT);

    return array(
        'base' => $base,
        // O DV persistido é apenas apresentação; a regra Cresol sempre prevalece.
        'dv' => b133_digitoVerificador_nossonumero($carteira . $base),
        'erro' => '',
    );
}

function b133_resolve_valor_titulo($valorInformado, $nT117_Id = null)
{
    $valorInformado = b133_normaliza_decimal($valorInformado);
    $valorPersistido = null;

    if (!empty($nT117_Id)) {
        $nT117_Id = (int)$nT117_Id;
        $sql = mysql_query("SELECT T117_Valor_Total FROM T117 WHERE T117_Id = '{$nT117_Id}'");
        if ($sql && mysql_num_rows($sql) > 0) {
            $titulo = mysql_fetch_array($sql);
            $valorPersistido = b133_normaliza_decimal($titulo['T117_Valor_Total']);
        }
    }

    $valorFinal = ($valorPersistido !== null) ? $valorPersistido : $valorInformado;
    $aviso = '';

    if ($valorPersistido !== null && $valorInformado > 0 && abs($valorPersistido - $valorInformado) >= 0.01) {
        $aviso = "Valor do boleto Cresol divergente para T117 {$nT117_Id}: include=" .
            number_format($valorInformado, 2, '.', '') .
            " titulo=" .
            number_format($valorPersistido, 2, '.', '');
    }

    return array(
        'valor' => $valorFinal,
        'aviso' => $aviso,
    );
}

function fbarcode($valor)
{
    $fino = 1;
    $largo = 3;
    $altura = 50;

    $barcodes[0] = "00110";
    $barcodes[1] = "10001";
    $barcodes[2] = "01001";
    $barcodes[3] = "11000";
    $barcodes[4] = "00101";
    $barcodes[5] = "10100";
    $barcodes[6] = "01100";
    $barcodes[7] = "00011";
    $barcodes[8] = "10010";
    $barcodes[9] = "01010";
    for ($f1 = 9; $f1 >= 0; $f1--) {
        for ($f2 = 9; $f2 >= 0; $f2--) {
            $f = ($f1 * 10) + $f2;
            $texto = "";
            for ($i = 1; $i < 6; $i++) {
                $texto .= substr($barcodes[$f1], ($i - 1), 1) . substr($barcodes[$f2], ($i - 1), 1);
            }
            $barcodes[$f] = $texto;
        }
    }

    ?><img src=imagens/p.png width=<?php echo $fino ?> height=<?php echo $altura ?> border=0><img
src=imagens/b.png width=<?php echo $fino ?> height=<?php echo $altura ?> border=0><img
src=imagens/p.png width=<?php echo $fino ?> height=<?php echo $altura ?> border=0><img
src=imagens/b.png width=<?php echo $fino ?> height=<?php echo $altura ?> border=0><img
<?php
    $texto = $valor;
    if ((strlen($texto) % 2) <> 0) {
        $texto = "0" . $texto;
    }

    while (strlen($texto) > 0) {
        $i = round(b133_esquerda($texto, 2));
        $texto = b133_direita($texto, strlen($texto) - 2);
        $f = $barcodes[$i];
        for ($i = 1; $i < 11; $i += 2) {
            if (substr($f, ($i - 1), 1) == "0") {
                $f1 = $fino;
            } else {
                $f1 = $largo;
            }
?>
    src=imagens/p.png width=<?php echo $f1 ?> height=<?php echo $altura ?> border=0><img
<?php
            if (substr($f, $i, 1) == "0") {
                $f2 = $fino;
            } else {
                $f2 = $largo;
            }
?>
    src=imagens/b.png width=<?php echo $f2 ?> height=<?php echo $altura ?> border=0><img
<?php
        }
    }
?>
src=imagens/p.png width=<?php echo $largo ?> height=<?php echo $altura ?> border=0><img
src=imagens/b.png width=<?php echo $fino ?> height=<?php echo $altura ?> border=0><img
src=imagens/p.png width=<?php echo 1 ?> height=<?php echo $altura ?> border=0>
<?php
}

function b133_esquerda($entra, $comp)
{
    return substr($entra, 0, $comp);
}

function b133_direita($entra, $comp)
{
    return substr($entra, strlen($entra) - $comp, $comp);
}

function b133_fator_vencimento($data)
{
    if (strpos($data, '/') !== false) {
        $data = explode("/", $data);
        $ano = $data[2];
        $mes = $data[1];
        $dia = $data[0];
    } else {
        $data = explode("-", $data);
        $ano = $data[0];
        $mes = $data[1];
        $dia = $data[2];
    }

    $diasVencimento = b133_dateToDays($ano, $mes, $dia);
    $dataBaseAntiga = b133_dateToDays("1997", "10", "07");
    $dataBaseNova = b133_dateToDays("2025", "02", "22");

    if ($diasVencimento < $dataBaseNova) {
        return abs($dataBaseAntiga - $diasVencimento);
    }

    return 1000 + abs($dataBaseNova - $diasVencimento);
}

function b133_dateToDays($year, $month, $day)
{
    $century = substr($year, 0, 2);
    $year = substr($year, 2, 2);
    if ($month > 2) {
        $month -= 3;
    } else {
        $month += 9;
        if ($year) {
            $year--;
        } else {
            $year = 99;
            $century--;
        }
    }

    return (floor((146097 * $century) / 4) +
            floor((1461 * $year) / 4) +
            floor((153 * $month + 2) / 5) +
            $day + 1721119);
}

function b133_modulo_10($num)
{
    $numtotal10 = 0;
    $fator = 2;

    for ($i = strlen($num); $i > 0; $i--) {
        $numeros[$i] = substr($num, $i - 1, 1);
        $temp = $numeros[$i] * $fator;
        $temp0 = 0;
        foreach (preg_split('//', $temp, -1, PREG_SPLIT_NO_EMPTY) as $k => $v) {
            $temp0 += $v;
        }
        $parcial10[$i] = $temp0;
        $numtotal10 += $parcial10[$i];
        if ($fator == 2) {
            $fator = 1;
        } else {
            $fator = 2;
        }
    }

    $resto = $numtotal10 % 10;
    $digito = 10 - $resto;
    if ($resto == 0) {
        $digito = 0;
    }

    return $digito;
}

function b133_modulo_11($num, $base = 9, $r = 0)
{
    $soma = 0;
    $fator = 2;

    for ($i = strlen($num); $i > 0; $i--) {
        $numeros[$i] = substr($num, $i - 1, 1);
        $parcial[$i] = $numeros[$i] * $fator;
        $soma += $parcial[$i];
        if ($fator == $base) {
            $fator = 1;
        }
        $fator++;
    }

    if ($r == 0) {
        $soma *= 10;
        $digito = $soma % 11;
        if ($digito == 10) {
            $digito = 0;
        }
        return $digito;
    } elseif ($r == 1) {
        return $soma % 11;
    }
}

function b133_monta_linha_digitavel($codigo)
{
    $campo1 = substr($codigo, 0, 4) . substr($codigo, 19, 5);
    $campo1 .= b133_modulo_10($campo1);
    $campo1 = substr($campo1, 0, 5) . '.' . substr($campo1, 5);

    $campo2 = substr($codigo, 24, 10);
    $campo2 .= b133_modulo_10($campo2);
    $campo2 = substr($campo2, 0, 5) . '.' . substr($campo2, 5);

    $campo3 = substr($codigo, 34, 10);
    $campo3 .= b133_modulo_10($campo3);
    $campo3 = substr($campo3, 0, 5) . '.' . substr($campo3, 5);

    $campo4 = substr($codigo, 4, 1);
    $campo5 = substr($codigo, 5, 14);

    return $campo1 . " " . $campo2 . " " . $campo3 . " " . $campo4 . " " . $campo5;
}

function b133_geraCodigoBanco($numero)
{
    $parte1 = substr($numero, 0, 3);
    $parte2 = b133_modulo_11($parte1);
    return $parte1 . "-" . $parte2;
}
