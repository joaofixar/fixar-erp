<?php
namespace hardness;

function validaCampoFornecedoresMarcados($array){
    global $g;
    $T010_Id = $array['todosCampos']['T059_T010_Id'];
    $T059_Id = $array['todosCampos']['T059_Id'];
    
    $sql = <<<EOT
        SELECT * FROM T174 
        LEFT JOIN T173 on T173_Id=T174_T173_Id
        LEFT JOIN D024 on D024_Id=T173_D024_Id 
        WHERE T174_T010_Id='{$T010_Id}' AND T174_T059_Id='{$T059_Id}' AND T174_Flag_Gerar_OC='1' ORDER BY T174_Id ASC
EOT;
    $result = mysql_query($sql);
    $retorno = "";
    while($linha = mysql_fetch_array($result)){
        if($linha['T174_Valor_ST'] > 0){
            $linha['T174_Preco_Venda'] = ($linha['T174_Valor_Custo_Unitario'] + ($linha['T174_Valor_Custo_Unitario'] * ($linha['T174_IPI']/100)) + ($linha['T174_Valor_ST']/$mT059['T174_Quantidade']))*$linha['T174_IPV'];
            $linha['T174_Valor_Custo_Unitario'] = ($linha['T174_Valor_Custo_Unitario'] + ($linha['T174_Valor_Custo_Unitario'] * ($linha['T174_IPI']/100)) + ($linha['T174_Valor_ST']/$linha['T174_Quantidade']));
        } else {
            $linha['T174_Preco_Venda'] = ($linha['T174_Valor_Custo_Unitario'] + ($linha['T174_Valor_Custo_Unitario'] * ($linha['T174_IPI']/100)))*$linha['T174_IPV'];
            $linha['T174_Valor_Custo_Unitario'] = ($linha['T174_Valor_Custo_Unitario'] + ($linha['T174_Valor_Custo_Unitario'] * ($linha['T174_IPI']/100)));
        }
        $linha['T174_Valor_Total'] = gCorrigeNumero($linha['T174_Valor_Total']);
        
        $sqlT010 = "SELECT * FROM T010 WHERE T010_Id='{$T010_Id}'";
        $resultT010 = mysql_query($sqlT010);
        $linhaT010 = mysql_fetch_array($resultT010);
        $sqlT011 = "SELECT * FROM T011 WHERE T011_T010_Id='{$T010_Id}' AND T011_D024_Id='{$linha['T173_D024_Id']}'";
        $resultT011 = mysql_query($sqlT011);
        $totalT011 = mysql_num_rows($resultT011);
        $linhaT011 = mysql_fetch_array($resultT011);
        if($totalT011 > 0 || $linhaT010['T010_Flag_Cotacao_Finalizada'] == "S"){
            $disabled = "disabled='disabled'";
            if(!empty($linhaT011['T011_Id'])){
                $novoIdTelaForm = uniqid();
                //$numOC = "<a href='#' onclick='abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de Compra: ' + id, '/est/est006/content/est006ContentAbreEditarOrdemCompra/', '&acaoId=' + encodeURIComponent('{$linhaT011['T011_Id']}'), [1000,600]);'><b>OC ".$linhaT011['T011_Id']."</b></a>";
                $numOC = <<<EOT
                <a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de Compra: ' + {$linhaT011['T011_Id']}, '/est/est006/content/est006ContentAbreEditarOrdemCompra/', '&acaoId=' + encodeURIComponent('{$linhaT011['T011_Id']}'), [1000,600]);"><b>OC {$linhaT011['T011_Id']}</b></a>
EOT;
            }
        } else {
            $disabled="";
            $numOC="";
        }
        
        $retorno .= <<<EOT
        <input type='checkbox' name='T174_Flag_Gerar_OC' title='Gerar OC' {$disabled} onChange='est008CheckboxGerarOC(this, "{$linha['T174_Id']}", "{$g['divId']}"); divRefresh("{$g['divId']}")' 
EOT;
        if($linha['T174_Flag_Gerar_OC'] == '1'){
            $retorno .= <<<EOT
            checked = 'checked'
EOT;
            $retorno .= <<<EOT
             ></EOT>{$numOC}
EOT;
            $D024_Nome_Empresa = substr($linha['D024_Nome_Empresa'], 0, 20);
            $retorno .= <<<EOT
            {$D024_Nome_Empresa} - R\$: {$linha['T174_Valor_Total']}<br ></EOT>
EOT;
        }
    }
    if($retorno == ""){
        return "Nenhum marcado";
    }
    return $retorno;
}
