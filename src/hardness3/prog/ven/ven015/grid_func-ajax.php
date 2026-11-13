<?php
namespace hardness;
/**
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/VEN003.php');
$VEN003 = new VEN003();

switch($g['r'][3]) {

    case 'ven015incluirNotaFiscalPDV':
    	global $g;
    	require_once('bibliotecas/classes/VEN003.php');
        $VEN003 = new VEN003();

		// Busca a venda em aberto para o usuário corrente
 		$C007 = mysql_query("select C007_T007_Id_PDV, T007_Id from C007 left join T007 on T007_Id=C007_T007_Id_PDV where C007_Id = '{$g['usuarioAtual']}'");
        $mC007 = mysql_fetch_assoc($C007);
		if ($mC007['T007_Id']>0) {
            
            // Caso já exista uma venda aberta vinculada ao usuário
            $resposta['code'] = true;
			$resposta['data'] = $mC007['T007_Id'];

        } else {

            if(!empty($g['C020']['C020_Cupom_Fiscal_Cliente'])){
                $sql = mysql_query("SELECT D024_Id FROM D024 WHERE D024_Id = '{$g['C020']['C020_Cupom_Fiscal_Cliente']}'");
                if (mysql_num_rows($sql)) {
                    $row = mysql_fetch_assoc($sql);
                    $idCliente = $row['D024_Id'];
                }
            }

            if(!empty($g['C031']['codigoClienteCupomFiscal'])){
                $idCliente = $g['C031']['codigoClienteCupomFiscal'];
            }

            if(empty($idCliente)){
                $resposta['code'] = false;
                $resposta['data'] = 'Não foi possível incluir a NFC-e, cliente padrão não parametrizado, por gentileza entre em contato com o suporte técnico.';
            } else {

                $D024 = mysql_query("SELECT D024_Flag_Destino_Produto FROM D024 WHERE D024_Id = '{$idCliente}'");
                $mD024 = mysql_fetch_array($D024);

                $flagDestinoProduto = $mD024['D024_Flag_Destino_Produto'];
                
                $row = array();
                $row['T007_D024_Id']                  = $idCliente;
                $row['T007_Flag_ACP']                 = '8';
                $row['T007_Data_Emissao']             = date("d/m/Y");
                $row['T007_Hora_Emissao']             = date("H:i:s");
                $row['T007_Data_Saida']               = date("d/m/Y");
                $row['T007_Hora_Saida']               = date("H:i:s");
                $row['T007_C007_Id_Vendedor_Interno'] = $g['usuarioAtual'];
                //$row['T007_Versao_NFe']               = $g['C020']['C020_Versao_NFe'];
                $row['T007_Versao_NFe']               = $g['c004']['C004_Versao_NFe'];
                $row['T007_Flag_Entrada_Saida']       = 'S';
                $row['T007_Flag_Finalidade_NFe']      = '1';
                $row['T007_Flag_Frete']      		  = '9';
                $row['T007_Flag_Destino_Produto']     = !empty($flagDestinoProduto) ? $flagDestinoProduto : '3';
                $row['T007_Casas_Decimais_Preco']     = $g['c004']['C004_Casas_Decimais_Preco'];
                $row['T007_Casas_Decimais_Quantidade']= '2';
                $row['T007_Flag_PDV']				  = 'S';
                $row['T007_Flag_Indicador_Presenca']  = 1;
                
                require_once('bibliotecas/classes/GFormWrap.php');
                $form = new GFormWrap($row);	        
                $retorno = $VEN003->incluirNota($form, false, true);
                if($retorno === true){
                    $resposta['code'] = true;
                    $resposta['data'] = $g['mysqlLastId'];
                } else {
                    $resposta['code'] = false;
                    $resposta['data'] = $retorno;
                }
            }
        }
    break;

    case 'ven015VerificaAlterarItem':
    	global $g;

        $T008_Id 	  = isset($r_T008_Id) ? $r_T008_Id : false;
        $campoEditado = isset($r_campoEditado) ? $r_campoEditado : false;
		$valorAntigo  = isset($r_valorAntigo) ? $r_valorAntigo : false;
		$novoValor 	  = isset($r_novoValor) ? $r_novoValor : false;

        if($g['C031']['alterarItemPdvVinculadoDav'] == 'N'){
            $T008 = mysql_query("SELECT T008_T006_Id 
                                   FROM T008
                                  WHERE T008_Id = '{$T008_Id}'");

            $mT008 = mysql_fetch_array($T008);
            
            if($mT008['T008_T006_Id'] > 0){
                $valorAntigo = gCorrigeNumeroInverte($valorAntigo);
                mysqli_query("UPDATE T008 SET T008_Quantidade = '{$valorAntigo}' WHERE T008_Id = '{$T008_Id}'");
                $resposta['code'] = false;
                $resposta['data'] = 'Este item está vinculado a uma DAV, não pode ser alterado';

            } else {
                $resposta['code'] = true;
            }
        } else {
            $resposta['code'] = true;
        }        
    break;

}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";