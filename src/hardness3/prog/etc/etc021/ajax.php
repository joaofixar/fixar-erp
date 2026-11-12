<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

switch($g['r'][3]) {
	
	case 'etc021ProdutoNovo':

        global $g, $confUsuario;

        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();        

        $imgDefault = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTqKJdyxnIegv_KSh9Wuf_6x_of4YowqgmI3w&usqp=CAU";
        $codbarra = isset($r_codbarra) ? $r_codbarra : '';
        $codbarra = mysql_real_escape_string($codbarra);
        $cod = unserialize(base64_decode($r_cod));
        $itens = json_decode($r_itens, true);	
        $flag = base64_decode($r_flag);
        $desc = base64_decode($r_desc);
        $qtd = base64_decode($r_qtd);


        $sqlEt = mysql_query(
            "SELECT D001_Id FROM D001 LEFT JOIN D083 ON D083_D001_Id = D001_Id 
                WHERE D083_Codigo_Produto_Fornecedor = '{$codbarra}' 
            "
        );

        $idItem = intval($rowGrid['T014_Id']);
        $resEt = mysql_fetch_assoc($sqlEt);
        $idProd = $resEt['D001_Id'];

        $impressao = <<<EOT

            <button class="ui-icon ui-icon-tag" id="etc021ImprimirEtiqueta" style="border: none; cursor: pointer; overflow: hidden;"></button>

            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#etc021ImprimirEtiqueta').unbind('click').bind('click', function(data) {

                    $.ajax({
                        url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
                        type: 'GET',
                        dataType: 'jsonp',
                        data: {
                            'ajax': 'true',
                            'acaoId': '0b21c4ea16c39ba82439aae2c02b5917',
                            'link': '/sistema/personalizacao/tela/0b21c4ea16c39ba82439aae2c02b5917/?ajax=true&D001_Id={$idProd}&sql={$r_sql}&T014_Id={$idItem}',
                        },
                        success: function(resposta) {
                            var printWindow = window.open(resposta.data, '_blank');
                            printWindow.addEventListener('load', function() {
                                printWindow.print();
                            }, false);
                        },
                        error: function(error) {
                            $(this).css('background', 'red');
                        }
                    });	

                });
            }</script></div>
EOT;	

        if (empty($codbarra)) {
            $resposta['code'] = false;
            $resposta['data'] = "Código de barras não encontrado no sistema!";
        } else {
            
            $sql = base64_decode($r_sql);
            $resGrid = mysql_query($sql);
            $encontrou = false;
            
            while ($rowGrid = mysql_fetch_assoc($resGrid)) {

                $qtdInput = intval($rowGrid['T014_Quantidade']);
                $idProd = $rowGrid['D001_Id'];

                foreach ($cod as $codValue) {
                    $linhaArray = json_encode($rowGrid);
                    $codBarras = explode('|', $rowGrid[$codValue]);
                    $multiplicadores = explode('|', $rowGrid['D083A_Multiplicador']);
                    foreach ($codBarras as $codBarrasItem) {
                        
                        if (empty($codBarrasItem)) { continue; }
                        if ($codBarrasItem == $codbarra) {
                            $quantidade = 1;
                            $value      = '';
                            $encontrou  = true;
                            foreach ($multiplicadores as $multiplicador) {
                                $quantidades = explode('X', $multiplicador);
                                if ((count($quantidades) === 2) and ($quantidades[0] === $codbarra)) {
                                    $quantidade = floatval($quantidades[1]);
                                    $value      = $quantidades[1];
                                }
                            }

                            if($rowGrid[$qtd] < ($itens[$codbarra] + 1))  {
                                $resposta['code'] = false;
                                $resposta['data'] = "Quantidade excedida para o item {$codbarra}";
                            } else {										

                            $descricao = $codBarrasItem . ' | ' . $rowGrid[$desc];
                            $disabledAttribute = $qtdInput < 10 ? 'disabled' : '';

                            $foto = $CAD002->cad002FotoProduto($idProd, 50);

                            $picking = intval(trim($rowGrid['picking'], '"'));
                            $transito = intval(trim($rowGrid['transito'], '"'));

                            if ($picking > 0) { 
                                        
                                $tipoEstoque = 'PICKING'; 
                                $EstoqueColor = 'rgba(103, 103, 255, 0.733)'; 
                                
                                $picking--;
                                $pickingValue = "UPDATE T014A SET T014A_JSON = JSON_SET(T014A_JSON, '$.picking', $picking) WHERE T014A_T014_Id =". $idItem;
                                $resPicking = mysql_query($pickingValue);

                            }  elseif ($transito > 0) { 

                                $tipoEstoque = 'TRANSITO';
                                $EstoqueColor = 'rgba(255, 159, 103, 0.733)';

                                $transito--;
                                $transitoValue = "UPDATE T014A SET T014A_JSON = JSON_SET(T014A_JSON, '$.transito', $transito) WHERE T014A_T014_Id =". $idItem;
                                $resTransito = mysql_query($transitoValue);									
                            }

                            if ($foto != '' || $foto != null) { $produto = $foto;} 
                            else { $produto = "<img style=\"width: 50px; height: 50px;\" src=\"{$imgDefault}\" alt=\"Guimepa\"/>";}
                                                        
                            $resposta['data'] = <<<EOT
                                <tr class="item" style="width: 100%; height: 35px;" verificado="1" cod="{$rowGrid[$codValue]}">
                                    <td style="width: 1%;">
                                        <span title="Cancelar" class="ui-icon ui-icon-circle-close" style="margin: 0 8px; cursor: pointer;" onclick="$(this).closest('tr').remove(); verCodBarraAjustesUI();"></span>
                                    </td>
                                    <td style="width: 60px;">
                                        <div style="display: flex; justify-content: center; align-items: center;">
                                            {$produto}
                                        </div>
                                    </td>
                                    <td style="text-align: left;">
                                        <div style="font-size: 1.3em; font-weight: bold;">
                                            {$descricao}
                                        </div>
                                    </td>
                                    <td style="width: 50px;">
                                        <div style="display: flex; justify-content: center; align-items: center; background: rgba(103, 103, 255, 0.733);">
                                            {$picking}
                                        </div>
                                        <div style="display: flex; justify-content: center; align-items: center; background: rgba(255, 159, 103, 0.733);">
                                            {$transito}
                                        </div>
                                    </td>
                                    <td style="width: 50px;">
                                        <div style="display: flex; justify-content: center; align-items: center;">
                                            {$impressao}
                                        </div>
                                    </td>
                                    <td style="width: 100px; background:$EstoqueColor;">
                                        <div style=" width: 100%; display: flex; color: #ffffff; font-size: 14px; cursor: default; font-weight: bold; justify-content: center; align-items: center;">
                                            {$tipoEstoque}
                                        </div>
                                    </td>
                                    <td style="text-align: right; width: 30px; padding: 6px 11px;">
                                        <input style=" width: 100%; background: #eee; font-size: 15px; text-align: right; border-radius: 5px; text-align: center; font-weight: bold; padding: 5px 0;" 
                                            onkeypress="if (event.which == 13) { $(this).blur(); }" onblur="verCodBarraAjustesUI();" 
                                            placeholder="{$quantidade}" 
                                            value="{$value}" 
                                            type="text" 
                                            {$disabledAttribute}
                                        />
                                    </td>
                                </tr>
EOT;
                            }
                        }
                    }
                }
            }
            if (!$encontrou) {
                $resposta['code'] = false;
                $sql = "SELECT D001_Codigo_Produto, D001_Descricao_Produto FROM D001 WHERE D001_Codigo_Barras = '{$codbarra}'";
                $resD001 = mysql_query($sql);
                if (!mysql_num_rows($resD001)) {
                    $sql = "SELECT D001_Codigo_Produto, D001_Descricao_Produto FROM D083 LEFT JOIN D001 ON D001_Id = D083_D001_Id WHERE D083_Codigo_Barras = '{$codbarra}' LIMIT 1";
                    $resD001 = mysql_query($sql);
                    if (!mysql_num_rows($resD001)) {
                        $resposta['data'] = "Código de barras não encontrado no sistema!";
                    } else {
                        $rowD001 = mysql_fetch_assoc($resD001);
                        $resposta['data'] = "Produto não encontrado no pedido: " . $rowD001['D001_Codigo_Produto'] . ' - ' . substr($rowD001['D001_Descricao_Produto'], 0, 40) . " ...";	
                    }
                } else {
                    $rowD001 = mysql_fetch_assoc($resD001);
                    $resposta['data'] = "Produto não encontrado no pedido: " . $rowD001['D001_Codigo_Produto'] . ' - ' . substr($rowD001['D001_Descricao_Produto'], 0, 40) . " ...";	
                }
            }
        }

	break;

	case 'etc021Verificar':
		
        $itens = json_decode($r_itens, true);
        $flag = base64_decode($r_flag);
        $cod = unserialize(base64_decode($r_cod));
        $qtd = base64_decode($r_qtd);
        $desc = base64_decode($r_desc);
        $tabela = explode('_', $flag);
        $tabela = $tabela[0];
    
        // Agrupamento quantidade por cod. barra (GRID)
        $arrayGrid = array();
        $arrayGridIds = array();
        $sql = base64_decode($r_sql);
        $resGrid = mysql_query($sql);
        while ($rowGrid = mysql_fetch_assoc($resGrid)) {
            $codIguais = array(); // para não duplicar caso os campos tenham o mesmo código de barras
            foreach ($cod as $codValue) {
                if (in_array($rowGrid[$codValue], $codIguais)) { continue; }
                if (!isset($arrayGrid[$rowGrid[$codValue]])) {
                    $arrayGrid[$rowGrid[$codValue]] = 0;
                }
                $arrayGrid[$rowGrid[$codValue]] += $rowGrid[$qtd];
                // precisamos gravar as PK dos itens para fazer o update em todos depois
                $chavePrimaria = $rowGrid[$tabela . '_Id'];
                $arrayGridIds[$rowGrid[$codValue]][] = $chavePrimaria;
                $codIguais[] = $rowGrid[$codValue];
            }
        }
    
        // Agrupamento quantidade por cod. barra (ITENS)
        $arrayItens = array();
        foreach ($itens as $item) {
            if (!isset($arrayItens[$item['cod']])) {
                $arrayItens[$item['cod']] = 0;
            }
            $item['qtd'] = str_replace(',', '.', $item['qtd']);
            $arrayItens[$item['cod']] += $item['qtd'];
        }
        // Verifica
        foreach ($arrayGrid as $arrayGridKey => $arrayGridValue) {
            foreach ($arrayItens as $arrayItensKey => $arrayItensValue) {
                if ($arrayGridKey == $arrayItensKey && $arrayGridValue == $arrayItensValue) {
                    foreach ($arrayGridIds[$arrayGridKey] as $chavePrimaria) {
                        $sql = "UPDATE {$tabela} SET {$flag} = 'S' WHERE {$tabela}_Id = '{$chavePrimaria}'";
                        mysql_query($sql);
                    }
                }
            }
        }

	break;

	case 'etc021SqlCodVar':
		
        $codbarra = ltrim($r_codbarra, '0');
        $sql_codvar = base64_decode($r_sql_codvar);
        $sql_codvar = str_replace('[[CODVAR]]', $codbarra, $sql_codvar);
    
        $res = mysql_query($sql_codvar);
        if (!$res || mysql_num_rows($res) == 0) {
            $resposta['code'] = false;
            $resposta['data'] = "ID não encontrado no sistema!";
        } else {
            $resposta['data'] = base64_encode($sql_codvar);
        }

	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
