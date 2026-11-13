<?php

/**
 * Essa tela aparece para o cliente quando o sistema trava por falta de pagamento
 */

namespace hardness;

require_once("prog/etc/etc011/outros.php");

// Pega o HTML dos boletos ====================================================================
ob_start();

// Printa os boletos de pagamento do parceiro para a hardness
echo getBoletosParceiroFwNovo();

// Se "a" for vazio, significa que a suspensão foi só pela falta de pagamento do parceiro, não dos clientes
if (!empty($_GET["a"])) {
    // Nesse bloco if else, printa os boletos que os clientes precisam pagar para os parceiros
    // Caso o servidor central for no modelo do sistema novo
    if (isset($sys['dbHostFwNovo'])) {
        $con = @mysqli_connect($sys['dbHostFwNovo'], $sys['dbUserFwNovo'], $sys['dbPassFwNovo'],$sys['dbDatabaseFwNovo']);
        $g['conexaoBanco'] = $con;
        
        $clientId = $_GET["a"];
        $qPES001 = "SELECT PES001_CNPJ,
                           PES001_Razao_Social
                      FROM PES001
                     WHERE md5(CONCAT('PES001_Id-', PES001_Id)) = '{$clientId}'";
        $PES001 = mysql_query($qPES001);
        if ($erro = mysql_error()) {
            echo $erro;
            return false;
        }
        $mPES001 = mysql_fetch_assoc($PES001);

        // Precisamos carregar essas variáveis para aparecer corretamente no boleto
        $g['C004']['C004_Cnpj'] = $mPES001["PES001_CNPJ"];
        $g['C004']['C004_Nome_Abreviado'] = $mPES001["PES001_Razao_Social"];
        echo getBoletosFwNovo();
    
    // Se não usamos o servidor central do sistema antigo
    } else {
        $con = @mysqli_connect($sys['dbHost'], $sys['dbUser'], $sys['dbPass'],$sys['dbDatabase']);
        $g['conexaoBanco'] = $con;
    
        $clientId = $_GET["a"];
        $qD024 = "SELECT D024_CNPJ,
                         D024_Nome_Empresa
                    FROM D024
                   WHERE md5(CONCAT('D024_Id-', D024_Id)) = '{$clientId}'";
        $D024 = mysql_query($qD024);
        if ($erro = mysql_error()) {
            echo $erro;
            return false;
        }
        $mD024 = mysql_fetch_assoc($D024);

        // Precisamos carregar essas variáveis para aparecer corretamente no boleto
        $g['C004']['C004_Cnpj'] = $mD024["D024_CNPJ"];
        $g['C004']['C004_Nome_Abreviado'] = $mD024["D024_Nome_Empresa"];
        echo getBoletos();
    }
}

$htmlBoletos = ob_get_contents();
ob_end_clean();

// Printa o html dos boletos junto com o aviso de suspensão do sistema ========================

echo <<<HTML
    <style>
        body {
            background: url("{$confUsuario['urlRaiz']}/hardness3/static/css/temas/padrao/images/desktop.png") repeat scroll 0 0 #FFFFFF;
        }

        .boletoNotificacao {
            border: 1px solid #888;
            margin: 2px;
        }
	</style>
    <div style="margin-left:30%;width: 700px; padding-left:4px;min-height: 300px; overflow: hidden; border: 1px solid #888; border-radius: 5px; font-family: Arial; background-color: #FFF; align: center; margin-top: 6px;">
        <br>
        <b>SISTEMA SUSPENSO POR FALTA DE PAGAMENTO</b>
        <br><br>
        Favor realizar o pagamento das pendências a baixo:
        $htmlBoletos
    </div>
HTML;
