<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	case 'demonstrativoPrecoVenda':
        global $g;
        $origem = '';
        if ($r_extra == 'T00X') {
            $tmp = explode('-', $r_acaoId);
            $r_extra = $tmp[0];
            $r_acaoId = $tmp[1];
        }
        if ($r_extra == 'T004') {
            $sql = "SELECT T004_Demonstrativo_Preco, T004_Demonstrativo_Preco_Tabela, T004_Demonstrativo_Preco_Cotacao, T004_Valor_Preco_Compra, T004_Valor_Preco_Cotacao, T004_Valor_Preco_Tabela, T004_Valor_Preco_Original, T004_T001_Id, T004_D009_Id, T004_T003_Id, T004_T219_Id, T004_Demonstrativo_Custo_Tabela FROM T004 WHERE T004_Id = '{$r_acaoId}'";
            log($sql);
            $res = mysql_query($sql);
            $row = mysql_fetch_assoc($res);
            $preco = gCorrigeNumero($row['T004_Valor_Preco_Original'],5);
            $precoCompra = gCorrigeNumero($row['T004_Valor_Preco_Compra'],5);
            $precoTabela = gCorrigeNumero($row['T004_Valor_Preco_Tabela'],5);
            $precoCotacao = gCorrigeNumero($row['T004_Valor_Preco_Cotacao'],5);
            $T001_Id = $row['T004_T001_Id'];
            $D009_Id = $row['T004_D009_Id'];
            $demonstrativo = unserialize($row['T004_Demonstrativo_Preco']);
            $demonstrativoPrecoTabela = unserialize($row['T004_Demonstrativo_Preco_Tabela']);
            $demonstrativoPrecoCotacao = unserialize($row['T004_Demonstrativo_Preco_Cotacao']);
            $demonstrativoCustoTabela = $row['T004_Demonstrativo_Custo_Tabela'];
            $origem = "(Orc. {$row['T004_T003_Id']})";
            $acaoId_Cotacao = $row['T004_T219_Id'];
            $tabela_Cotacao = 'T219';
        } else if ($r_extra == 'D009') {
            $sql = "SELECT D009_Preco_Loja_Demonstrativo, D009_Preco_Loja, D009_Preco_Loja_T001_Id, D009_Id FROM D009 WHERE D009_Id = '{$r_acaoId}'";
            $res = mysql_query($sql);
            $row = mysql_fetch_assoc($res);
            $preco = gCorrigeNumero($row['D009_Preco_Loja'],2);
            $T001_Id = $row['D009_Preco_Loja_T001_Id'];
            $D009_Id = $row['D009_Id'];
            $demonstrativo = unserialize($row['D009_Preco_Loja_Demonstrativo']);
            if (empty($row['D009_Preco_Loja_Demonstrativo'])) {
                echo "Demonstrativo de preço não disponível.";
                die();
            }
        } else if ($r_extra == 'D009_Real') {
            $sql = "SELECT D009_Preco_Loja_Real_Demonstrativo, D009_Preco_Loja_Real, D009_Preco_Loja_Real_T001_Id, D009_Id FROM D009 WHERE D009_Id = '{$r_acaoId}'";
            $res = mysql_query($sql);
            $row = mysql_fetch_assoc($res);
            $preco = gCorrigeNumero($row['D009_Preco_Loja_Real'],2);
            $precoCompra = gCorrigeNumero($row['D009_Preco_Loja_Real'],2);
            $T001_Id = $row['D009_Preco_Loja_Real_T001_Id'];
            $D009_Id = $row['D009_Id'];
            $demonstrativo = unserialize($row['D009_Preco_Loja_Real_Demonstrativo']);
            if (empty($row['D009_Preco_Loja_Real_Demonstrativo'])) {
                echo "Demonstrativo de preço não disponível.";
                die();
            }    
        } else {
            $sql = "SELECT T006_Demonstrativo_Preco, T006_Demonstrativo_Preco_Tabela, T006_Demonstrativo_Preco_Cotacao, T006_Valor_Preco_Compra, T006_Valor_Preco_Tabela, T006_Valor_Preco_Cotacao, T006_Valor_Preco_Original, T006_T001_Id, T006_D009_Id, T006_T005_Id, T006_T219_Id, T006_T225_Id, T006_Demonstrativo_Custo_Tabela FROM T006 WHERE T006_Id = '{$r_acaoId}'";
            $res = mysql_query($sql);
            $row = mysql_fetch_assoc($res);
            $preco = gCorrigeNumero($row['T006_Valor_Preco_Original'],5);
            $precoCompra = gCorrigeNumero($row['T006_Valor_Preco_Compra'],5);
            $precoTabela = gCorrigeNumero($row['T006_Valor_Preco_Tabela'],5);
            $precoCotacao = gCorrigeNumero($row['T006_Valor_Preco_Cotacao'],5);
            $T001_Id = $row['T006_T001_Id'];
            $D009_Id = $row['T006_D009_Id'];
            $T219_Id = $row['T006_T219_Id'];
            $demonstrativo = unserialize($row['T006_Demonstrativo_Preco']);
            $demonstrativoPrecoTabela = unserialize($row['T006_Demonstrativo_Preco_Tabela']);
            $demonstrativoPrecoCotacao = unserialize($row['T006_Demonstrativo_Preco_Cotacao']);
            $demonstrativoCustoTabela = $row['T006_Demonstrativo_Custo_Tabela'];
            $origem = "(Ped. {$row['T006_T005_Id']})";
            if(isset($row['T006_T225_Id']) && $row['T006_T225_Id'] > 0) {
                $acaoId_Cotacao = $row['T006_T225_Id'];
                $tabela_Cotacao = 'T225';
            } else {
                $acaoId_Cotacao = $row['T006_T219_Id'];
                $tabela_Cotacao = 'T219';
            }
        }

        $height = ($g['C031']['tipoPreco'] == 'I') ? '275' : '220';
        $colspan = 2;
        $retorno = <<<EOT
            <table border=0 cellspacing=0 cellpadding=0 style="min-height: {$height}px;">
            <tr>
            <td style='width: 210px'>
            <div style="width: 200px" id="demonstrativoCusto"></div>
            </td>
            <td style='width: 210px'>
                <div style="width: 200px;" id="demonstrativoCustoTabela">
EOT;
        if(!empty($demonstrativoCustoTabela)){
            $retorno .= $demonstrativoCustoTabela;
        }
        $retorno .= <<<EOT
                </div>
            </td>
EOT;
        if(isset($acaoId_Cotacao) && $acaoId_Cotacao > 0) {
            $colspan++;
            $retorno .= <<<EOT
                <td style='width: 210px;'>
                <div style="width: 200px;" id="demonstrativoCustoCompras"></div>
                </td>
EOT;
        }
        $retorno .= <<<EOT
            </tr>
            <tr>
EOT;
        if($g['C031']['tipoPreco'] == 'I'){
            $retorno .= montarDemonstrativo($demonstrativo, $precoCompra);
            if(!empty($demonstrativoPrecoTabela) && $demonstrativoPrecoTabela != ''){
                $retorno .= montarDemonstrativo($demonstrativoPrecoTabela, $precoTabela);
            }
            if(!empty($demonstrativoPrecoCotacao) && $demonstrativoPrecoCotacao != ''){
                $retorno .= montarDemonstrativo($demonstrativoPrecoCotacao, $precoCotacao);
            }
        }
        $retorno .= <<<EOT
                </td>
                </tr>
                    <td colspan="{$colspan}" style="font-weight: bold; text-align: center;">Preço venda {$origem} {$preco}</td>
                </tr>
            </table>
EOT;
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $.ajax({
                    url: '/cad/cad002/outro/demonstrativoCusto/',
                    dataType: 'html',
                    data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&T001_Id=' + encodeURIComponent('{$T001_Id}'),
                    type: 'post',
                    success: function(data) {
                        $('#demonstrativoCusto').html(data);
                    }
                });
EOT;
        if(empty($demonstrativoCustoTabela)){
            echo <<<EOT
                $.ajax({
                    url: '/cad/cad002/outro/demonstrativoCustoTabela/',
                    dataType: 'html',
                    data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&D009_Id=' + encodeURIComponent('{$D009_Id}'),
                    type: 'post',
                    success: function(data) {
                        $('#demonstrativoCustoTabela').html(data);
                    }
                });
EOT;
        }
        echo "} </script></div>";
        if(isset($acaoId_Cotacao) && $acaoId_Cotacao > 0 && !empty($demonstrativoPrecoCotacao) && $demonstrativoPrecoCotacao != ''){
            echo <<<EOT
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    $.ajax({
                        url: '/cad/cad002/outro/demonstrativoCustoCompras/',
                        dataType: 'html',
                        data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&acaoId=' + encodeURIComponent('{$acaoId_Cotacao}') + '&tabela=' + encodeURIComponent('{$tabela_Cotacao}'),
                        type: 'post',
                        success: function(data) {
                            $('#demonstrativoCustoCompras').html(data);
                        }
                    });
                } </script></div>
EOT;
        }
        echo $retorno;
    break;

    case 'adicionarItensPDV':
        $resposta = array('code' => true, 'data' => array());

        $T003_Id = $r_T003_Id;
        $itens = json_decode($r_itens, true);

        // Agrupamento quantidade
        $arrayItens = array();
        foreach ($itens as $item) {
            if (!isset($arrayItens[$item['cod']])) {
                $arrayItens[$item['cod']] = 0;
            }
            $arrayItens[$item['cod']] += $item['qtd'];
        }

        foreach ($arrayItens as $itemCod => $itemQtd) { 
            // Simula o carregamento do ajax
            $_REQUEST['D009_Id'] = $itemCod;
            $_REQUEST['T003_Id'] = $T003_Id;
            $row = gCarregarPrograma('/ven/ven001/form_func-ajax/atualizarCamposProdutos/', 'row');
            
            // Simula o envio do form (o preenchimento dos campos foram copiados do ven001CampoProcuraProdutos.js)
            $formItens = array(
                    'Procura_Produto_Pesquisa' => $row['D001_Codigo_Produto'],
                    'T004_D037_Id' => $row['D001_D037_Id'],
                    'T004_D005_Id' => $row['D001_D005_Id'],
                    'T004_Descricao_Produto' => $row['D001_Descricao_Produto'],
                    'T004_Numero_Pedido_Compra' => $row['T004_Numero_Pedido_Compra'],
                    'T004_D009_Id' => $row['D009_Id'],
                    'T004_D006_Id' => $row['D006_Id'],
                    'T004_Codigo_Produto' => $row['D001_Codigo_Produto'],
                    
                    'Marca' => $row['D082_Marca'],
                    'NcmProduto' => $row['D001_D005_Id'],
                    'T004_Codigo_Substituto' => $row['D083_Codigo_Produto_Fornecedor'],
                    
                    'T004_Valor_Preco_Sem_Desconto_Unitario' => $row['Preco_Produto'],
                    'PrecoMinimo' => $row['PrecoMinimo'],
                    'T004_IPV' => $row['IPV'],
                    'T004_Aliquota_IPI' => $row['D005_Aliquota_IPI'],
                    'T004_Aliquota_IPI' => $row['Aliquota_IPI'],
                    'UltimoPrecoVenda' => $row['ultimoPrecoVenda'],
                    //'T004_D144_Id' => D144_Id,
                    
                    'T004_T003_Id' => $T003_Id,
                    'T004_C004_Id' => $g['empresaAtual'],
                    'T004_Quantidade' => $itemQtd
            );
            require_once('bibliotecas/classes/GFormWrap.php');
            $form = new GFormWrap($formItens);
            
            require_once("bibliotecas/classes/VEN001.php");
            $VEN001 = new VEN001();
            $retorno = $VEN001->incluirProduto($form);
            if ($retorno !== true) {
                die("Erro incluindo produto: {$itemCod}");
            }
        }

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;
}

function montarDemonstrativo($demonstrativo, $preco){
    $html .= "<td style='width: 210px;'><table cellpadding=0 cellspacing=0 border=0 width=198px style='float: left;'>";
    $index = 0;
    foreach ($demonstrativo as $campo) {
        $border = '';
        if($index == 0){
            $index++;
            continue;
        }
        $valor = gCorrigeNumero($campo['valor'],5);
        if ($campo['campo'] == 'Custo Compra' || $campo['campo'] == 'Preço Bruto') {
            $border = 'border-bottom: 1px solid black;';
            //$valor = "<a href=\"#\" onclick=\"nova_janela('/hardness/empresas/E003/externos/T001_demonstracao_custo.php3?Abre_Janela=N&T001_Id={$T001_Id}',150,300,450,400,'yes','no','no','no','yes','no','Teste')\">$valor</a>";
        }
        if($campo['campo'] == 'Custo Líquido' || $campo['campo'] == 'Custo Bruto' || $campo['campo'] == 'Preço Bruto'){
            $valor = "<b>".$valor."</b>";
            $campo['campo'] = "<b>".$campo['campo']."</b>";
        }
        $cssLinha = ($campo['exibir'] == 'N') ? 'color: gray;' : '';
        $html .= <<<EOT
            <tr>
                <td style="padding: 2px 2px;font-size:11px;{$cssLinha}{$border}">
                    {$campo['campo']}
                </td>
                <td style="padding: 2px 2px;font-size:11px;{$cssLinha}{$border}">
                    {$campo['operacao']}
                </td>
                <td style='text-align: right; padding: 2px 2px;font-size:11px;{$cssLinha}{$border}'>
                    {$valor}
                </td>
            </tr>
EOT;
    }
    $html .= <<<EOT
            <tr>
                <td style="border-top: 1px solid black; padding: 2px 2px;">
                    Preço venda
                </td>
                <td style="border-top: 1px solid black; padding: 2px 2px;">
                    =
                </td>
                <td style="border-top: 1px solid black; text-align: right; padding: 2px 2px;">
                    {$preco}
                </td>
            </tr>
            </table>
        </td>
EOT;
    return $html;
}