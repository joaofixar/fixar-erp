<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho Cliente
	 * Chamado em: cad001tabCadastro
	 */
	case 'cad034CabecalhoProjecao':
		// Select
		$D014_Id = (isset($r_acaoId)) ? $r_acaoId : false;
		$ano = (isset($r_ano)) ? $r_ano : date("Y");
		
		$sql = "SELECT T018_Ano FROM T018 WHERE T018_D014_Id='{$D014_Id}' GROUP BY T018_Ano";
		$result = mysql_query($sql);
		
		// Print
		$retorno = <<<EOT
			<div class="" style="float:left;width:100%;padding:2px;">
				<table>
					<tr>
						<td>Ano</td>
						<td>
							<select name="ano" onchange='selecionaAno($(this).val(), "{$r_divRefresh}")'>
								<option>...</option>
EOT;
		for ($i=2000; $i <= 2030; $i++) {
			if(!empty($ano)){
				if($i == $ano){
					$selected = 'selected="selected"';
				}else{
					$selected="";
				}
			}else{
				$selected="";
			}
			$retorno .= <<<EOT
								<option value='{$i}' {$selected}>{$i}</option>
EOT;
		}
		$retorno .=<<<EOT
							</select>
						</td>
					</tr>
				</table>
			</div>
EOT;
		echo $retorno;

	break;
	
	case 'cad034CabecalhoPlanoDeContas':
		$D014_Id = (isset($r_acaoId)) ? $r_acaoId : false;
		
		$sql =<<<EOT
		SELECT * FROM D014
		LEFT JOIN D032 on D032_Id=D014_D032_Id 
		WHERE D014_Id='{$D014_Id}';
EOT;
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		
		if($linha['D014_Flag_Tipo'] == "PG"){
			$linha['D014_Flag_Tipo'] = "PAGAMENTO";
		}else{
			$linha['D014_Flag_Tipo']="RECEBIMENTO";
		}
		if(mysql_num_rows($result) == 0){
			$linha = null;
		}
		echo <<<EOT
		<div class="contentCabecalhoLayout">
			<table class="tabela ui-state-default ui-corner-all">
				<tr class="linhaDesc">
					<td>Grupo</td>
					<td>Conta</td>
					<td>Vencimento</td>
					<td>(+ / - )</td>
				</tr>
				<tr class="linhaVal">
					<td>{$linha['D032_Descricao']}</td>
					<td>{$linha['D014_SubConta']}</td>
					<td>{$linha['D014_Dia_Vencimento']}</td>
					<td>{$linha['D014_Flag_Tipo']}</td>
				</tr>
			</table>
		</div>
EOT;
		break;

		case 'cad034ManualCSVPlanoDeContas':

        echo <<<EOT
        
        <style>
            .tabelaManual{
                border:1px solid black;
                border-colapse: colapse;    
                cellspace="10";
                font-size:20px;
                padding:10px 15px;
                background-color:#000;
                text-align:left;
            }
            .LinhaReferencia{
                text-align:center;
                background: rgba(76, 175, 80, 0);
                padding:0px 20px 0px 27px;
            }
            .linhaTabelaManual{               
                font-size:15px;
                padding:10px 15px;
                background-color:#FFF;
                text-align:center;
                

            }   
        </style>

        <div style="padding-left:10px;">
        <h2>Modelo de documento CSV</h2>
        <h3>Observações:</h3>
        <p style="font-size:12px">-Abaixo está o formato do modelo do documento para a importação!
        <br/>-O documento deve ter <strong>15 colunas</strong> ao todo, caso seja necessário modificar o número de colunas para a importação, contactar o suporte técnico.
        <br/>-Os nomes das colunas devem estar necessáriamente na primeira linha(1) do documento e ter a mesma grafia do documento modelo.
        <br/>-A extensão do documento deve ser CSV (Delimitado por vírgulas)(*.csv)
        <br/>-Ao final do documento não deve haver nenhuma linha em branco. 
        <br/>-Os campos com * são obrigatórios. 
        <br/>-O campo <strong>Dia Vencimento</strong> deve ser preenchido com um número entre 1 e 31.
        <br/>-O campo <strong>TIPO</strong> deve ser preenchido com PG (para pagamento) ou RC (para recebimento).
        <br/>-Os campos <strong>Centro de Custos, Grupo e Sub-Grupo</strong> devem ser preenchidos conforme o nome que está no cadastro.
        <br/>-O campo <strong>Grupo DRE</strong> deve ser preenchido com um número entre 1 e 4, onde: 1-Despesas com Vendas, 2-Despesas administrativas, 3-Participação nos lucros, 4-Impostos, 5-Investimentos, 6-Receita bruta, 7-Devoluções ou 8-Fornecedores.
        <br/>-O campo <strong>O dia da Semana</strong> de ver preenchido com um número entre 1 e 7 1-Domingo, 2-Segunda-feira, 3-terça-feira, 4-quarta-feira, 5-quinta-feira, 6-sexta-feira, 7-sábado. 
        <br/>-O campo <strong>Portador</strong> deve ser preenchido com: cobrança bancaria, carteira, deposito, dinheiro, cheque, cartão de crédito, guia ou crédito troca.
        <br/>-O campo <strong>Empresa</strong> deve ser preenchido somente o código da empresa.
        <br/>-O campo <strong>Ativo</strong> deve ser preenchdo com S (para sim) ou N (para Não).
        <br/>-O campo <strong>Verificar Saldo</strong> deve ser preenchdo com S (para sim) ou N (para Não). 
        <br/>-O campo <strong>Registrar Conta</strong> deve ser preenchdo com S (para sim) ou N (para Não). 
        <br/>-O campo <strong>Pagamento único</strong> deve ser preenchdo com S (para sim) ou N (para Não).
        </p>
        </div>
        <table cellspacing=1px bgcolor="black">     
            <tr>        
            
                <td class="linhaTabelaManual" > *NOME DA CONTA </td>
                <td class="linhaTabelaManual" > *DIA VENCIMENTO </td>
                <td class="linhaTabelaManual" > *TIPO </td>
                <td class="linhaTabelaManual" > GRUPO </td>
                <td class="linhaTabelaManual" > SUB-GRUPO </td>
            	<td class="linhaTabelaManual" > CENTRO DE CUSTOS </td>
            	<td class="linhaTabelaManual" > GRUPO DRE </td>
            	<td class="linhaTabelaManual" > PORTADOR </td>
            	<td class="linhaTabelaManual" > DIA SEMANA VENCIMENTO  </td>
            	<td class="linhaTabelaManual" > EMPRESA </td>
            	<td class="linhaTabelaManual" > OBSERVACAO </td>
            	<td class="linhaTabelaManual" > ATIVO </td>
            	<td class="linhaTabelaManual" > VERIFICAR SALDO </td>
            	<td class="linhaTabelaManual" > REGISTRAR CONTA </td>
            	<td class="linhaTabelaManual" > PAGAMENTO UNICO </td>

            </tr>
            <tr>   

                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                            
            </tr>
            <tr>            
                
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>

            </tr>
            <tr>            
                
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
            	<td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>

            </tr>

        </table>
EOT;
 
    break;

    case 'cad034ManualCSVGrupos':

        echo <<<EOT
        
        <style>
            .tabelaManual{
                border:1px solid black;
                border-colapse: colapse;    
                cellspace="10";
                font-size:20px;
                padding:10px 15px;
                background-color:#000;
                text-align:left;
            }
            .LinhaReferencia{
                text-align:center;
                background: rgba(76, 175, 80, 0);
                padding:0px 20px 0px 27px;
            }
            .linhaTabelaManual{               
                font-size:15px;
                padding:10px 15px;
                background-color:#FFF;
                text-align:center;
                

            }   
        </style>

        <div style="padding-left:10px;">
        <h2>Modelo de documento CSV</h2>
        <h3>Observações:</h3>
        <p style="font-size:12px">-Abaixo está o formato do modelo do documento para a importação!
        <br/>-O documento deve ter <strong>3 colunas</strong> ao todo, caso seja necessário modificar o número de colunas para a importação, contactar o suporte técnico.
        <br/>-Os nomes das colunas devem estar necessáriamente na primeira linha(1) do documento e ter a mesma grafia do documento modelo, elas devem estar em <strong>MAIÚSCULO</strong>.
        <br/>-A extensão do documento deve ser CSV (Delimitado por vírgulas)(*.csv)
        <br/>-Ao final do documento não deve haver nenhuma linha em branco. 
        <br/>-Os campos com * são obrigatórios. 
        <br/>-O campo <strong>Grupo</strong> deve estar em maiúsculo.
        <br/>-O campo <strong>Ativo</strong> deve ser preenchdo com S (para sim) ou N (para Não).
        <br/>-Preencha o campo <strong>Tipo</strong> com 01 (para receitas), 10 (para fixas), 20 (para variáveis), 25 (para fornecedores), 30 (para investimentos), 40 (para sócios) ou 50 (para outros).
        </p>
        </div>
        <table cellspacing=1px bgcolor="black">     
            <tr>        
            
                <td class="linhaTabelaManual" > *GRUPO </td>
                <td class="linhaTabelaManual" > ATIVO </td>
                <td class="linhaTabelaManual" > *TIPO </td>
    
            </tr>
            <tr>   

                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                            
            </tr>
            <tr>            
                
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>

            </tr>
            <tr>            
                
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>

            </tr>

        </table>
EOT;
 
    break;

    case 'cad034ManualCSVSubGrupos':

        echo <<<EOT
        
        <style>
            .tabelaManual{
                border:1px solid black;
                border-colapse: colapse;    
                cellspace="10";
                font-size:20px;
                padding:10px 15px;
                background-color:#000;
                text-align:left;
            }
            .LinhaReferencia{
                text-align:center;
                background: rgba(76, 175, 80, 0);
                padding:0px 20px 0px 27px;
            }
            .linhaTabelaManual{               
                font-size:15px;
                padding:10px 15px;
                background-color:#FFF;
                text-align:center;
                

            }   
        </style>

        <div style="padding-left:10px;">
        <h2>Modelo de documento CSV</h2>
        <h3>Observações:</h3>
        <p style="font-size:12px">-Abaixo está o formato do modelo do documento para a importação!
        <br/>-O documento deve ter <strong>1 coluna</strong> ao todo, caso seja necessário modificar o número de colunas para a importação, contactar o suporte técnico.
        <br/>-Os nomes das colunas devem estar necessáriamente na primeira linha(1) do documento e ter a mesma grafia do documento modelo, elas devem estar em MAIÚSCULO.
        <br/>-A extensão do documento deve ser CSV (Delimitado por vírgulas)(*.csv)
        <br/>-Ao final do documento não deve haver nenhuma linha em branco. 
        <br/>-Os campos com * são obrigatórios. 

        </p>
        </div>
        <table cellspacing=1px bgcolor="black">     
            <tr>        
            
                <td class="linhaTabelaManual" > *SUB-GRUPO </td>

            </tr>
            <tr>   

                <td class="linhaTabelaManual">---</td>
                            
            </tr>
            <tr>            
                
                <td class="linhaTabelaManual">---</td>
                
            </tr>
            <tr>            
                
                <td class="linhaTabelaManual">---</td>
                
            </tr>

        </table>
EOT;
 
    break;
}
?>