<?php
namespace hardness;

/** Separa o DV de apresentacao sem alterar os zeros do codigo cadastrado. */
function b104_separar_beneficiario($valor)
{
    $valor = trim((string) $valor);
    if (!preg_match('/\A([0-9]{1,7})(?:-([0-9]))?\z/', $valor, $partes)
        || (int) $partes[1] === 0) {
        throw new \InvalidArgumentException(
            'Codigo do Beneficiario CAIXA invalido: informe de 1 a 7 algarismos, opcionalmente seguidos de hifen e um digito.'
        );
    }

    return array(
        'codigo' => $partes[1],
        'digito' => isset($partes[2]) ? $partes[2] : null,
        // CNAB400 versao 007: NE004 A2/B e NE065.
        'cnab' => str_pad($partes[1], 7, '0', STR_PAD_LEFT),
    );
}

/** Decodifica as posicoes 31-37 do header de retorno (NE004 A1/A2/B). */
function b104_beneficiario_retorno($campo, $versao)
{
    if (!preg_match('/\A[0-9]{7}\z/', $campo)) {
        throw new \InvalidArgumentException('Codigo do Beneficiario CAIXA invalido no retorno.');
    }

    $versao = trim($versao);
    if ($versao === '') {
        // Versao antiga: seis algarismos a partir da posicao 31 e zero na 37.
        if ($campo[6] !== '0') {
            throw new \InvalidArgumentException('Codigo do Beneficiario CAIXA incompativel com a versao em branco do retorno.');
        }
        $campo = substr($campo, 0, 6);
    } elseif ($versao !== '007') {
        throw new \InvalidArgumentException('Versao do retorno CAIXA nao suportada para identificar o beneficiario.');
    }

    $beneficiario = b104_separar_beneficiario($campo);
    return $beneficiario['cnab'];
}
