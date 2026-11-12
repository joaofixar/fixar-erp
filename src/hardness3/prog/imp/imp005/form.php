<?php   
namespace hardness;
require_once('prog/imp/imp005/form_func-js.php');  
//Classes
require_once("bibliotecas/classes/IMP005.php");
$IMP005 = new IMP005();
    
switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 21/11/2011  
	*/
	case 'imp005FormDi':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T295';
		$form_campo_pk = 'T295_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp005FormDiBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp005/form/imp005FormDi/';

        $form_editor = array(
			'geral' => '{"T295_D044_Id":{"top":191,"left":408.703125,"width":127,"hidden":0},"T295_Ordem_Compra":{"top":7,"left":63.015625,"width":120,"hidden":0},"T295_D024_Id":{"top":30,"left":82.953125,"width":410,"hidden":0},"T295_Important_Remark":{"top":30,"left":604.375,"height":154,"width":232,"hidden":0},"T295_Codigo_Fabricante":{"top":30,"left":1017.96875,"width":232,"hidden":1},"T295_Flag_Transporte_DI":{"top":53,"left":38.859375,"width":431,"hidden":0},"T295_UF_Desembaraco":{"top":52.984375,"left":1202.984375,"width":63,"hidden":1},"T295_Flag_Tipo_Intermedio_DI":{"top":76,"left":33.203125,"width":431,"hidden":0},"T295_Local_Desembaraco":{"top":75.984375,"left":1015.984375,"width":232,"hidden":1},"T295_D190_Id":{"top":99,"left":60.203125,"width":431,"hidden":0},"T295_Data_Desembaraco":{"top":98.984375,"left":1195,"width":72,"hidden":1},"T295_D006_Id":{"top":122,"left":110.859375,"width":176,"hidden":0},"T295_C007_Id":{"top":122,"left":405.078125,"width":127,"hidden":0},"T295_Codigo_Exportador":{"top":121.984375,"left":1150.234375,"width":232,"hidden":1},"T295_D119_Id":{"top":145,"left":74.4375,"width":176,"hidden":0},"T295_D118_Id":{"top":145,"left":415.703125,"width":127,"hidden":0},"T295_Data_Licenca_Importacao":{"top":145,"left":1174.40625,"width":103,"hidden":1},"T295_Data_Emissao":{"top":168,"left":70.9375,"width":104,"hidden":0},"T295_Data_Embarque":{"top":168,"left":367.296875,"width":120,"hidden":0},"T295_CNPJ_Importador":{"top":168,"left":918.09375,"width":135,"hidden":1},"T295_UF_Importador":{"top":168,"left":1203.828125,"width":63,"hidden":1},"T295_Cotacao_ME":{"top":191,"left":79,"width":104,"hidden":0},"T295_Ship_To":{"top":191,"left":662.359375,"height":154,"width":232,"hidden":0},"T295_Operacao_Enquadramento_Material_Usado":{"top":191,"left":1016.46875,"width":120,"hidden":1},"T295_Peso_Bruto":{"top":214,"left":67.890625,"width":104,"hidden":0},"T295_Peso_Liquido":{"top":214,"left":362.234375,"width":120,"hidden":0},"T295_Numero_Drawback_Importacao":{"top":214,"left":1114.140625,"width":232,"hidden":1},"T295_Data_Previsao":{"top":237,"left":35.5,"width":104,"hidden":0},"T295_Transfer_Fe":{"top":237,"left":381.625,"width":120,"hidden":0},"T295_Condicao_Mercadoria":{"top":260,"left":18.9375,"width":113,"hidden":0},"T295_Enquadramento_Material_Usado":{"top":260,"left":322.21875,"width":127,"hidden":0},"T295_Orgao_Desembaraco":{"top":260,"left":998.40625,"width":120,"hidden":1},"T295_Shipping_Term":{"top":283,"left":62.6875,"width":424,"hidden":0},"T295_Shipping_Condition":{"top":306,"left":40.71875,"width":424,"hidden":0},"T295_Codigo_Moeda":{"top":306,"left":965.265625,"width":105,"hidden":1},"T295_Payment_Term":{"top":329,"left":62.078125,"width":424,"hidden":0},"T295_Cotacao_CC":{"top":328.984375,"left":1307.390625,"width":115,"hidden":1}}',
        );

		$disabled = true;
		if($g['nivelAlcada'] == 4){
			$disabled = false;
		}

		$clientePorEmpresa = '';
		if($g['C031']['separarClientePorEmpresa'] == 'S'){
			$clientePorEmpresa = "AND (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
		}
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					//'T295_Numero_DI' => array('name' => 'T295_Numero_DI', 'label' => 'Número DI', 'type' => 'text', 'length' => array(15,255)),
					'T295_Ordem_Compra' => array('name' => 'T295_Ordem_Compra', 'label' => 'Purchase Order', 'type' => 'text', 'length' => array(15,255), 'style'=>'float:right;', 'align'=>'right'),
					'T295_D024_Id' => array('name' => 'T295_D024_Id', 'label' => 'Fornecedor', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Fornecedor="S" and D024_Flag_Ativo="S" '.$clientePorEmpresa.' order by D024_Nome_Empresa','(%1) %2 [%3/%4] - %5 Tel:(%7)%8'),'length' => array(70,120)),
					'T295_D006_Id' => array('name' => 'T295_D006_Id', 'label' => 'CFOP', 'type' => 'select', 
						'select' => gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao','where D006_Flag_Inativo!="S" and D006_Flag_Estado="I" order by D006_Codigo_CFOP',true)
					),
					'T295_C007_Id' => array('name' => 'T295_C007_Id', 'label' => 'Usuário', 'type' => 'select','style'=>'float:right;', 'align'=>'right', 'disabled' => $disabled, 
						'select' => gGeraSelect('C007','C007_Id','C007_Primeiro_Nome','WHERE C007_Flag_Vendedor !="V" AND  C007_Flag_Vendedor !="N" AND C007_Ativo="S" ORDER BY C007_Primeiro_Nome',true,$g['usuarioAtual'])
					),
					'T295_D119_Id' => array('name' => 'T295_D119_Id', 'label' => 'Despachante', 'type' => 'select', 'length' => array(1,255), 'select' => 
							//gGeraSelect('D024', 'D024_Id', 'D024_Nome_Empresa', "WHERE D024_D046_Id=8 order by D024_Nome_Empresa", true)
							gGeraSelect('D119', 'D119_Id', 'D119_Nome', "order by D119_Nome", true)
					),
					'T295_D118_Id' => array('name' => 'T295_D118_Id', 'label' => 'Porto', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'length' => array(1,255), 'select' => 
							gGeraSelect('D118', 'D118_Id', 'D118_Nome', "order by D118_Nome", true)
					),
					'T295_Data_Emissao' => array('name' => 'T295_Data_Emissao', 'label' => 'Data Emissão', 'type' => 'data', 'value' => date("d/m/Y"), 'length' => array(15,255)),
					'T295_Data_Embarque' => array('name' => 'T295_Data_Embarque', 'label' => 'Data Embarque', 'type' => 'data', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right'),
					'T295_Data_Licenca_Importacao' => array('name' => 'T295_Data_Licensa_Importacao', 'label' => 'Data LI', 'type' => 'data', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right'),
					//'T295_ETA_Data' => array('name' => 'T295_ETA_Data', 'label' => 'Data ETA', 'type' => 'data', 'length' => array(15,255)),
					//'T295_ETA_Hora' => array('name' => 'T295_ETA_Hora', 'label' => 'Hora ETA', 'type' => 'tempo', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right'),
					'T295_Cotacao_ME' => array('name' => 'T295_cotacao_ME', 'label' => 'Cotação ME', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T295_Valor_SISCOMEX' => array('name' => 'T295_Valor_SISCOMEX', 'label' => 'Valor SISCOMEX R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					//'T295_Valor_Frete_Incluso' => array('name' => 'T295_Valor_Frete_Incluso', 'label' => 'Valor Frete Incluso R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T295_Valor_Frete' => array('name' => 'T295_Valor_Frete', 'label' => 'Valor Frete ME', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					//'T295_Valor_Seguro' => array('name' => 'T295_Valor_Valor_Seguro', 'label' => 'Valor Seguro ME', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T295_Valor_Despesa' => array('name' => 'T295_Valor_Despesa', 'label' => 'Valor Despesas', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					//'T295_Valor_THC' => array('name' => 'T295_Valor_THC', 'label' => 'Valor THC R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
                    //'T295_Valor_AFRMM' => array('name' => 'T295_Valor_AFRMM', 'label' => 'Valor AFRMM R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T295_Valor_ICMS' => array('name' => 'T295_Valor_ICMS', 'label' => 'Valor ICMS R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					//'T295_Valor_Imposto_Importacao' => array('name' => 'T295_Valor_Imposto_Importacao', 'label' => 'Valor II R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T295_Valor_IPI' => array('name' => 'T295_Valor_IPI', 'label' => 'Valor IPI R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					//'T295_Valor_PIS' => array('name' => 'T295_Valor_PIS', 'label' => 'Valor PIS R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T295_Valor_COFINS' => array('name' => 'T295_Valor_COFINS', 'label' => 'Valor COFINS R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					//'T295_Valor_VMLE' => array('name' => 'T295_Valor_VMLE', 'label' => 'VMLE ME', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T295_Valor_VMLD' => array('name' => 'T295_Valor_VMLD', 'label' => 'VMLD ME', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					//'T295_Valor_COFINS' => array('name' => 'T295_Valor_COFINS', 'label' => 'Valor COFINS R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					//'T295_Aliquota_ICMS' => array('name' => 'T295_Aliquota_ICMS', 'label' => '% ICMS', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T295_Aliquota_ICMS_Presumido' => array('name' => 'T295_Aliquota_ICMS_Presumido', 'label' => '% ICMS Presumido', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					//'T295_Percentual_Reducao_ICMS' => array('name' => 'T295_Percentual_Reducao_ICMS', 'label' => '% Redução ICMS', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T295_Percentual_Reducao_ICMS_Presumido' => array('name' => 'T295_Percentual_Reducao_ICMS_Presumido', 'label' => '% Redução ICMS Presumido', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					//'T295_Aliquota_ICMS_PIS_COFINS' => array('name' => 'T295_Aliquota_ICMS_PIS_COFINS', 'label' => '% ICMS PIS/COFINS', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T295_Aliquota_ICMS_Credito_Presumido' => array('name' => 'T295_Aliquota_ICMS_Credito_Presumido', 'label' => '% ICMS Credito Presumido', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T295_Peso_Bruto' => array('name' => 'T295_Peso_Bruto', 'label' => 'Peso Bruto KG', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '3', 'casasDecimais' => '4'),
					'T295_Peso_Liquido' => array('name' => 'T295_Peso_Liquido', 'label' => 'Peso Liquido KG', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '3', 'casasDecimais' => '4'),
                	'T295_D044_Id' => array('name' => 'T295_D044_Id', 'label' => 'Moeda', 'type' => 'select', 
                		'select' => gGeraSelect('D044', 'D044_Id', 'D044_Moeda,D044_Simbolo')
                	),
					'T295_Data_Previsao' => array('name' => 'T295_Data_Previsao', 'label' => 'Previsão de Chegada', 'type' => 'data', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right'),
					'T295_C004_Id' => array('name' => 'T295_C004_Id', 'label' => 'empresaAtual', 'type' => 'hidden', 'value'=>$g['empresaAtual']),

					'T295_Local_Desembaraco' => array('name' => 'T295_Local_Desembaraco', 'label' => 'Local Desembaraço', 'type' => 'text', 'length' => array(70,90)),
					'T295_Data_Desembaraco' => array('name' => 'T295_Data_Desembaraco', 'label' => 'Data Desembaraço', 'type' => 'data', 'length' => array(15,15)),
					'T295_UF_Desembaraco' => array('name' => 'T295_UF_Desembaraco', 'label' => 'UF Desembaraço', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
						gGeraSelect('D018', 'D018_UF', 'D018_UF', 'order by D018_UF,D018_Estado', true)
					),
					'T295_Codigo_Exportador' => array('name' => 'T295_Codigo_Exportador', 'label' => 'Código Exportador', 'type' => 'text', 'length' => array(70,90)),
					'T295_Codigo_Fabricante' => array('name' => 'T295_Codigo_Fabricante', 'label' => 'Código do Fabricante<br>Estrangeiro', 'type' => 'text', 'length' => array(70,90)),
					'T295_Flag_Transporte_DI' => array('name' => 'T295_Flag_Transporte_DI', 'label'=>'Forma de transporte', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value'=>'', 'selected'=>true),
							array('title' => 'Marítima', 'value' => '1'),
							array('title' => 'Fluvial', 'value' => '2'),
							array('title' => 'Lacustre', 'value' => '3'),
							array('title' => 'Aérea', 'value' => '4'),
							array('title' => 'Postal', 'value' => '5'),
							array('title' => 'Ferroviária', 'value' => '6'),
							array('title' => 'Rodoviária', 'value' => '7'),
							array('title' => 'Conduto / Rede Transmissão', 'value' => '8'),
							array('title' => 'Meios Próprios', 'value' => '9'),
							array('title' => 'Entrada / Saída ficta', 'value' => '10'),
							array('title' => 'Courier', 'value' => '11'),
							array('title' => 'Handcarry', 'value' => '12'),
						)
					),
					'T295_Flag_Tipo_Intermedio_DI' => array('name' => 'T295_Flag_Tipo_Intermedio_DI', 'label' => 'Forma de importação', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value'=>'', 'selected'=>true),
							array('title' => 'Importação por conta própria', 'value' => '1'),
							array('title' => 'Importação por conta e ordem', 'value' => '2'),
							array('title' => 'Importação por encomenda', 'value' => '3'),
						)
					),
					'T295_CNPJ_Importador' => array('name' => 'T295_CNPJ_Importador', 'label' => 'CNPJ Importador', 'type' => 'cnpj'),
					'T295_UF_Importador' => array('name' => 'T295_UF_Importador', 'label' => 'UF Importador', 'type' => 'select', 
						'select' => gGeraSelect('D018', 'D018_UF', 'D018_UF', 'order by D018_UF,D018_Estado', true)
					),
					'T295_Numero_Drawback_Importacao' => array('name' => 'T295_Numero_Drawback_Importacao', 'label' => 'Drawback', 'type' => 'text'),
					'T295_Cotacao_CC' => array('name' => 'T295_Cotacao_CC', 'label' => 'Cotação Contrato de Câmbio', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T295_Codigo_URF_Entrada' => array('name' => 'T295_Codigo_URF_Entrada', 'label' => 'URF Entrada', 'type' => 'text', 'length' => array(70,90)),
					//'T295_Codigo_URF_Despacho' => array('name' => 'T295_Codigo_URF_Despacho', 'label' => 'URF Despacho', 'type' => 'text', 'length' => array(70,90)),
					'T295_Codigo_Moeda' => array('name' =>  'T295_Codigo_Moeda', 'label' => 'Código ME', 'type' => 'text', 'length' => array(70,90)),
					//'T295_Codigo_Incoterms' => array('name' => 'T295_Codigo_Incoterms', 'label' => 'Código Incoterms', 'type' => 'text', 'length' => array(70,90)),
					'T295_Condicao_Mercadoria' => array('name' => 'T295_Condicao_Mercadoria', 'label' => 'Condição da Mercadoria', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Novo', 'value' => 'NOVO'),
							array('title' => 'Usado', 'value' => 'USADO'),
						)

					), 
					'T295_Enquadramento_Material_Usado' => array('name' => 'T295_Enquadramento_Material_Usado', 'label' => 'Enquadramento Material', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Admissão temporária', 'value' => 'ADMISSÃO TEMPORÁRIA'),
							array('title' => 'Nacionalização', 'value' => 'NACIONALIZAÇÃO'),
						)
					), 
					'T295_Operacao_Enquadramento_Material_Usado' => array('name' => 'T295_Operacao_Enquadramento_Material_Usado', 'label' => 'Operação Enquadramento ', 'type' => 'text', 'length' => array(70,90)),
					//'T295_Data_Inicio_Demurrage' => array('name' => 'T295_Data_Inicio_Demurrage', 'label' => 'Data inicio demurrage', 'type' => 'data', 'length' => array(15,255)),
					//'T295_Siscoserv_RAS' => array('name' => 'T295_Siscoserv_RAS', 'label'=>'Siscoserv RAS', 'type' => 'textarea', 'length' => array(4,50)),
					//'T295_Siscoserv_RP' => array('name' => 'T295_Siscoserv_RP', 'label'=>'Siscoserv RP', 'type' => 'textarea', 'length' => array(4,50)),
					'T295_Orgao_Desembaraco' => array('name' => 'T295_Orgao_Desembaraco', 'label'=>'Orgão Desembaraço', 'type' => 'text'),
					'T295_Shipping_Term' => array('name' => 'T295_Shipping_Term', 'label'=>'Shipping Terms', 'type' => 'text','length' => array(4,49)),
					'T295_Shipping_Condition' => array('name' => 'T295_Shipping_Condition', 'label'=>'Shipping Conditions', 'type' => 'text','length' => array(4,49)),
					'T295_Payment_Term' => array('name' => 'T295_Payment_Term', 'label'=>'Payment Terms', 'type' => 'text','length' => array(4,49)),
					'T295_Transfer_Fe' => array('name' => 'T295_Transfer_Fe', 'label'=>'Transfer Fee', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T295_Important_Remark' => array('name' => 'T295_Important_Remark', 'label'=>'Important Remarks', 'type' => 'textarea', 'length' => array(4,50)),
					'T295_Ship_To' => array('name' => 'T295_Ship_To', 'label'=>'Ship To', 'type' => 'textarea', 'length' => array(4,50)),
					'T295_D190_Id' => array('name' => 'T295_D190_Id', 'label' => 'Pacote despesa', 'type' => 'select', 'select' =>
						gGeraSelect('D190', 'D190_Id', 'D190_Tipo_Despesa','ORDER BY D190_Tipo_Despesa',true),
					),
				),
			)
		);
        $form = new Formulario('form-cad017FormPrincipal');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// pacote de despesas
				$T295_D190_Id  = $form->campoValorEnviado('T295_D190_Id');

				// Insert ou Update?
				if (!empty($r_acaoId)) {
					$T295 = mysql_query("SELECT T295_D190_Id FROM T295 WHERE T295_Id = '{$r_acaoId}'");
					$mT295 = mysql_fetch_array($T295);
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$IMP005->atualizaOC($r_acaoId);
						if($T295_D190_Id != $mT295['T295_D190_Id']){
							$incluirPacote = $IMP005->incluirPacoteDespesaPO($r_acaoId, $T295_D190_Id);
							if($T295_D190_Id > 0){
								$msgPacote = '<br><b><span style="color: red;">Atenção, incluído novo pacote de despesas<b></span>';
							}else{
								$msgPacote = '<br><b><span style="color: red;">Atenção, retirado o pacote de desepesas<b></span>';
							}
						}

						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'.$msgPacote, false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$alert}
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$acaoId = $g['mysqlLastId'];

						$C004  = mysql_query("SELECT C004_Observao_Purchase_Order FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
						$mC004 = mysql_fetch_array($C004);
						mysql_query("UPDATE T295 SET T295_Important_Remark = '{$mC004['C004_Observao_Purchase_Order']}' WHERE T295_Id = '{$acaoId}'");

						$IMP005->atualizaOC($acaoId);
						$incluirPacote = $IMP005->incluirPacoteDespesaPO($acaoId, $T295_D190_Id);
						
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Alterar DI - ID: ' + '{$acaoId}', '/imp/imp005/content/imp005ContentDiIncluir/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [900,550]);
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;
	
	
	case 'imp005FormTotal':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T295';
		$form_campo_pk = 'T295_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp005FormTotalDiBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp005/form/imp005FormTotal/';

        $form_editor = array(
             'geral' => '{"Valor_Total_Custo":{"top":7,"left":15.375,"width":103,"hidden":0},"Valor_Base_Imposto_Importacao":{"top":7,"left":317.4375,"width":103,"hidden":0},"Cotacao_ME":{"top":30,"left":47,"width":103,"hidden":0},"Valor_Imposto_Importacao":{"top":30,"left":344.5,"width":103,"hidden":0},"Valor_Total_Custo_Reais":{"top":53,"left":18.28125,"width":103,"hidden":0},"Valor_Base_PIS":{"top":53,"left":306.96875,"width":103,"hidden":0},"Valor_Total_Frete":{"top":76,"left":81.796875,"width":103,"hidden":0},"Valor_PIS":{"top":76,"left":334.015625,"width":103,"hidden":0},"Valor_Sub_Total":{"top":99,"left":44.578125,"width":103,"hidden":0},"Valor_Base_COFINS":{"top":99,"left":285.265625,"width":103,"hidden":0},"Valor_COFINS":{"top":122,"left":312.3125,"width":103,"hidden":0},"T296_Valor_Total_Preco_Nota_Fiscal":{"top":145,"left":59.6875,"width":103,"hidden":0},"Valor_Base_IPI":{"top":145,"left":310.5,"width":103,"hidden":0},"T296_Valor_Total_Custo_Final":{"top":168,"left":50.671875,"width":103,"hidden":0},"Valor_IPI":{"top":168,"left":337.5625,"width":103,"hidden":0},"T296_Quantidade":{"top":191,"left":49.21875,"width":103,"hidden":0},"Valor_Base_ICMS":{"top":191,"left":297.140625,"width":103,"hidden":0},"Valor_Base_ICMS_Normal":{"top":214,"left":258.296875,"width":103,"hidden":0},"Valor_ICMS":{"top":237,"left":324.203125,"width":103,"hidden":0},"T296_Peso_Kg_Liquido_Unitario":{"top":237,"left":41.578125,"width":103,"hidden":0},"Valor_ICMS_Presumido":{"top":260,"left":268.5625,"width":103,"hidden":0},"TotalVendidoLiquido":{"top":260,"left":0.5,"width":103,"hidden":0},"Valor_ICMS_Pagar":{"top":283,"left":278.5625,"width":103,"hidden":0},"TotalVendidoBruto":{"top":283,"left":10.203125,"width":103,"hidden":0},"T296_Valor_Total_Despesas":{"top":306,"left":303.28125,"width":103,"hidden":0}}'
        );

 		$T295=mysql_query("select *
		                     from T295
		                    where T295_Id='$r_acaoId'");
	    $mT295=mysql_fetch_array($T295);

		$T296=mysql_query("select sum(T296_Valor_Total_Frete) T296_Valor_Total_Frete,
		                          sum(T296_Valor_Total_Custo*$mT295[T295_Cotacao_ME]) T296_Valor_Total_Custo_Reais,
		                          sum(T296_Valor_Total_Custo) T296_Valor_Total_Custo,
		                          sum(T296_Valor_Aduaneira) T296_Valor_Aduaneira,
		                          sum(T296_Valor_Base_Imposto_Importacao) T296_Valor_Base_Imposto_Importacao,
		                          sum(T296_Valor_Imposto_Importacao) T296_Valor_Imposto_Importacao,
		                          sum(T296_Valor_Base_IPI) T296_Valor_Base_IPI,
		                          sum(T296_Valor_IPI) T296_Valor_IPI,
		                          sum(T296_Valor_Base_PIS) T296_Valor_Base_PIS,
		                          sum(T296_Valor_PIS) T296_Valor_PIS,
		                          sum(T296_Valor_Base_COFINS) T296_Valor_Base_COFINS,
		                          sum(T296_Valor_COFINS) T296_Valor_COFINS,
		                          sum(T296_Valor_ICMS) T296_Valor_ICMS,
		                          sum(T296_Valor_ICMS_Presumido) T296_Valor_ICMS_Presumido,
		                          sum(T296_Valor_ICMS_Pagar) T296_Valor_ICMS_Pagar,
		                          sum(T296_Peso_Kg_Liquido_Unitario*T296_Quantidade) T296_Peso_Kg_Liquido_Unitario,
		                          sum(T296_Valor_Total_Despesas) T296_Valor_Total_Despesas,
		                          sum(T296_Valor_Total_Despesas_Aduaneira) T296_Valor_Total_Despesas_Aduaneira,
		                          sum(T296_Valor_Total_Custo_Final) T296_Valor_Total_Custo_Final,
		                          sum(T296_Valor_Total_Preco_Nota_Fiscal) T296_Valor_Total_Preco_Nota_Fiscal,
		                          sum(T296_Valor_Base_ICMS_Sem_Reducao) T296_Valor_Base_ICMS_Sem_Reducao,
		                          sum(T296_Valor_Base_ICMS_Normal) T296_Valor_Base_ICMS_Normal,
                                  sum(T296_Valor_Total_AFRMM) T296_Valor_Total_AFRMM,
                                  sum(T296_Quantidade) T296_Quantidade
		                     from T296
		                    where T296_T295_Id='{$r_acaoId}'");
	    $mT296=mysql_fetch_array($T296);
	    
	    $mT296['T296_Valor_Sub_Total'] = number_format($mT296['T296_Valor_Total_Custo_Reais']+$mT296['T296_Valor_Total_Frete'], 4, ',', '.');
	    //$mT296['T296_Valor_Total_Custo_Reais'] = number_format($mT296['T296_Valor_Total_Custo_Reais'], 4, ',', '.');
	    $mT296['T296_Valor_Total_Custo'] = number_format($mT296['T296_Valor_Total_Custo'], 4, ',', '.');
	    $mT295['T295_Cotacao_ME'] = number_format($mT295['T295_Cotacao_ME'], 4, ',', '.');
	    $mT296['T296_Valor_Total_Frete'] = number_format($mT296['T296_Valor_Total_Frete'], 4, ',', '.');
	    $mT296['T296_Valor_Aduaneira'] = number_format($mT296['T296_Valor_Aduaneira'], 4, ',', '.');
	    //$mT296['T296_Valor_Imposto_Importacao'] = number_format($mT296['T296_Valor_Imposto_Importacao'], 2, ',', '.');
	  	//$mT296['T296_Valor_IPI'] = number_format($mT296['T296_Valor_IPI'], 4, ',', '.');
	    //$mT296['T296_Valor_PIS'] = number_format($mT296['T296_Valor_PIS'], 4, ',', '.');
	    //$mT296['T296_Valor_COFINS'] = number_format($mT296['T296_Valor_COFINS'], 4, ',', '.');
	    $mT296['T296_Valor_ICMS'] = number_format($mT296['T296_Valor_ICMS'], 4, ',', '.');
	    $mT296['T296_Valor_ICMS_Presumido'] = number_format($mT296['T296_Valor_ICMS_Presumido'], 4, ',', '.');
	    $mT296['T296_Valor_ICMS_Pagar'] = number_format($mT296['T296_Valor_ICMS_Pagar'], 4, ',', '.');
	    $mT296['T296_Peso_Kg_Liquido_Unitario'] = number_format($mT296['T296_Peso_Kg_Liquido_Unitario'], 4, ',', '.');
	    $mT296['T296_Valor_Total_Preco_Nota_Fiscal'] = number_format($mT296['T296_Valor_Total_Preco_Nota_Fiscal'], 4, ',', '.');
	    $mT296['T296_Valor_Total_Despesas_Aduaneira'] = number_format($mT296['T296_Valor_Total_Despesas_Aduaneira'], 4, ',', '.');
	    $mT296['T296_Valor_Total_Despesas'] = number_format($mT296['T296_Valor_Total_Despesas'], 4, ',', '.');
	    $mT296['T296_Valor_Total_Custo_Final'] = number_format($mT296['T296_Valor_Total_Custo_Final'], 4, ',', '.');
	    $mT296['T296_Valor_Base_ICMS_Sem_Reducao'] = number_format($mT296['T296_Valor_Base_ICMS_Sem_Reducao'], 4, ',', '.');
	    $mT296['T296_Valor_Base_ICMS_Normal'] = number_format($mT296['T296_Valor_Base_ICMS_Normal'], 4, ',', '.');
       // $mT296['T296_Valor_Total_AFRMM'] = number_format($mT296['T296_Valor_Total_AFRMM'], 4, ',', '.');
        $mT296['T296_Quantidade'] = number_format($mT296['T296_Quantidade'], 4, ',', '.');
		//, 'casasDecimais' => '4'
		// Definição do Formulário

        $sqlVendido = mysql_query("SELECT SUM(T006_Quantidade) AS Quantidade, SUM(T006_Valor_Total_Preco) AS Total, SUM(T006_Valor_Preco_Bruto * T006_Quantidade) AS TotalBruto FROM T006 
			LEFT JOIN T005 ON T005_Id=T006_T005_Id
			LEFT JOIN T295 ON T295_Id=T006_T295_Id 
			WHERE 
			T005_Flag_Status!='5' AND
			T006_Flag_Status!='3' AND			
			T005_Flag_Status!='7' AND
			T005_Flag_Status!='8' AND
			T005_Flag_Status!='11' AND
			T006_T295_Id = '{$r_acaoId}'");
        $resVendido = mysql_fetch_array($sqlVendido);

		$totalVendido = $resVendido['Total'];
		$totalVendidoBruto = $resVendido['TotalBruto'];
        $totalItensVendido = $resVendido['Quantidade'];
		$formArray = array('campos' => array('geral' => array()));	

		$valorTotalReais = $mT295['T295_Valor_VMLE'] * gCorrigeNumeroInverte($mT295['T295_Cotacao_ME']);
		$valorSubTotalReais = ($mT295['T295_Valor_VMLE']+ $mT295['T295_Valor_Frete']) * gCorrigeNumeroInverte($mT295['T295_Cotacao_ME']) + $mT295['T295_Valor_THC'];
		$valorFrete = $mT295['T295_Valor_Frete'] * gCorrigeNumeroInverte($mT295['T295_Cotacao_ME']);
		
		$formArray['campos']['geral']['Valor_Total_Custo'] = array('name' => 'Valor_Total_Custo', 'value' => "$mT296[T296_Valor_Total_Custo]", 'casasDecimais' => '2', 'label' => 'Valor Produtos ME', 'type' => 'moeda', 'align' => 'right', 'length' => array(15,50), 'readonly'=>true, 'reject' => true);

		//if(gCorrigeNumero($mT295['T295_Valor_VMLE'], 4) != $mT296['T296_Valor_Total_Custo']) {
		//$dif_Valor_ME = $mT295['T295_Valor_VMLE'] - gCorrigeNumeroInverte($mT296['T296_Valor_Total_Custo']);
		//$formArray['campos']['geral']['dif_Valor_ME']= array('name' => 'dif_Valor_ME', 'label' => 'Diferença Valor ME', 'value' => gCorrigeNumero($dif_Valor_ME, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right');
		//}
		
		$formArray['campos']['geral']['Cotacao_ME'] = array('name' => 'Cotacao_ME', 'casasDecimais' => '2', 'value' => "$mT295[T295_Cotacao_ME]", 'label' => 'Cotacao ME', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'align' => 'right', 'reject' => true);
		
		$formArray['campos']['geral']['Valor_Total_Custo_Reais'] = array('name' => 'Valor_Total_Custo', 'value' => gCorrigeNumero($mT296['T296_Valor_Total_Custo_Reais'],4), 'casasDecimais' => '2', 'label' => 'Valor Produtos R$', 'align' => 'right', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true);

		//if(gCorrigeNumero($valorTotalReais) != gCorrigeNumero($mT296['T296_Valor_Total_Custo_Reais'])) {
		//$dif_Valor_Reais = $valorTotalReais - $mT296['T296_Valor_Total_Custo_Reais'];
		//$formArray['campos']['geral']['dif_Valor_Reais']= array('name' => 'dif_Valor_Reais', 'label' => 'Diferença Valor Reais', 'value' => gCorrigeNumero($dif_Valor_Reais, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'align' => 'right', 'reject' => true, 'align'=>'right');
		//}
		
		$formArray['campos']['geral']['Valor_Total_Frete'] = array('name' => 'Valor_Total_Frete', 'value' => "$mT296[T296_Valor_Total_Frete]", 'casasDecimais' => '2', 'label' => 'Frete', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'align' => 'right', 'reject' => true);

		//if(gCorrigeNumero($valorFrete, 4) != $mT296['T296_Valor_Total_Frete']) {
		//$dif_Valor_Frete = $valorFrete - gCorrigeNumeroInverte($mT296['T296_Valor_Total_Frete']);
		//$formArray['campos']['geral']['dif_Valor_Frete']= array('name' => 'dif_Valor_Frete', 'label' => 'Diferença Frete', 'value' => gCorrigeNumero($dif_Valor_Frete, 4), 'casasDecimais' => '2', 'type' => 'moeda' ,'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right', 'align'=>'right');
		//}
		
		//$formArray['campos']['geral']['Valor_Aduaneira'] = array('name' => 'Valor_Aduaneira', 'value' => $mT296['T296_Valor_Aduaneira'], 'casasDecimais' => '2', 'label' => 'THC', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'align' => 'right', 'reject' => true);

		//if(gCorrigeNumero($mT295['T295_Valor_THC'], 4) != $mT296['T296_Valor_Aduaneira']) {
		//$dif_Valor_THC = $mT295['T295_Valor_THC'] - gCorrigeNumeroInverte($mT296['T296_Valor_Aduaneira']);
		//$formArray['campos']['geral']['dif_Valor_THC']= array('name' => 'dif_Valor_THC', 'label' => 'Diferença THC', 'value' => gCorrigeNumero($dif_Valor_THC, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right', 'align'=>'right'); 
		//}
		
		$formArray['campos']['geral']['Valor_Sub_Total']= array('name' => 'Valor_Sub_Total', 'value' => "$mT296[T296_Valor_Sub_Total]", 'casasDecimais' => '2', 'label' => 'Sub-Total R$', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'align' => 'right', 'reject' => true);

		//if(gCorrigeNumero($valorSubTotalReais, 4) != $mT296['T296_Valor_Sub_Total']) {
		//$dif_Valor_Sub_Total = $valorSubTotalReais - gCorrigeNumeroInverte($mT296['T296_Valor_Sub_Total']);			
		//$formArray['campos']['geral']['dif_Sub_Total']= array('name' => 'dif_Sub_Total', 'label' => 'Diferença Sub-total', 'value' => gCorrigeNumero($dif_Valor_Sub_Total, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right', 'align'=>'right');
		//}
		
		//$formArray['campos']['geral']['Valor_Total_Despesas_Aduaneira'] = array('name' => 'Valor_Total_Despesas_Aduaneira', 'value' => $mT296['T296_Valor_Total_Despesas_Aduaneira'], 'casasDecimais' => '2', 'label' => 'Siscomex', 'type' => 'moeda', 'align' => 'right', 'length' => array(15,50), 'readonly'=>true, 'reject' => true);

		//if(gCorrigeNumero($mT295['T295_Valor_SISCOMEX'], 4) != $mT296['T296_Valor_Total_Despesas_Aduaneira']) {
		//$dif_Valor_SISCOMEX = $mT295['T295_Valor_SISCOMEX'] - gCorrigeNumeroInverte($mT296['T296_Valor_Total_Despesas_Aduaneira']);
		//$formArray['campos']['geral']['dif_Valor_SISCOMEX']= array('name' => 'dif_Valor_SISCOMEX', 'label' => 'Diferença Siscomex', 'value' => gCorrigeNumero($dif_Valor_SISCOMEX, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'align' => 'right', 'reject' => true, 'align'=>'right');
		//}

		$formArray['campos']['geral']['Valor_Base_Imposto_Importacao'] = array('name' => 'Valor_Base_Imposto_Importacao', 'value' => gCorrigeNumero($mT296['T296_Valor_Base_Imposto_Importacao'],4), 'casasDecimais' => '2', 'label' => 'Valor Base II', 'align' => 'right', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true);
		$formArray['campos']['geral']['Valor_Imposto_Importacao'] = array('name' => 'Valor_Imposto_Importacao', 'value' => gCorrigeNumero($mT296['T296_Valor_Imposto_Importacao'],4), 'casasDecimais' => '2', 'label' => 'Valor II', 'type' => 'moeda', 'align' => 'right', 'length' => array(15,50), 'readonly'=>true, 'reject' => true);

		//if(gCorrigeNumero($mT295['T295_Valor_Imposto_Importacao'], 4) != gCorrigeNumero($mT296['T296_Valor_Imposto_Importacao'],4)) {
		//$dif_Valor_II = $mT295['T295_Valor_Imposto_Importacao'] - $mT296['T296_Valor_Imposto_Importacao'];
		//$formArray['campos']['geral']['dif_Valor_II']= array('name' => 'dif_Valor_II', 'label' => 'Diferença II', 'value' => gCorrigeNumero($dif_Valor_II, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right');			 
		//}		

		$formArray['campos']['geral']['Valor_Base_IPI'] = array('name' => 'Valor_Base_IPI', 'label' => 'Valor Base IPI', 'value' => gCorrigeNumero($mT296['T296_Valor_Base_IPI'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'align' => 'right', 'readonly'=>true, 'reject' => true);
		$formArray['campos']['geral']['Valor_IPI'] = array('name' => 'Valor_IPI', 'label' => 'Valor IPI', 'value' => gCorrigeNumero($mT296['T296_Valor_IPI'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');

		//if(gCorrigeNumero($mT295['T295_Valor_IPI'], 4) != gCorrigeNumero($mT296['T296_Valor_IPI'], 4)) {
		//$dif_Valor_IPI = $mT295['T295_Valor_IPI'] - $mT296['T296_Valor_IPI'];
		//$formArray['campos']['geral']['dif_Valor_IPI']= array('name' => 'dif_Valor_IPI', 'label' => 'Diferença IPI', 'value' => gCorrigeNumero($dif_Valor_IPI, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right');
		//}

		$formArray['campos']['geral']['Valor_Base_PIS'] = array('name' => 'Valor_Base_PIS', 'label' => 'Valor Base PIS', 'value' => gCorrigeNumero($mT296['T296_Valor_Base_PIS'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['Valor_PIS'] = array('name' => 'Valor_PIS', 'label' => 'Valor PIS', 'value' => gCorrigeNumero($mT296['T296_Valor_PIS'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true);

		//if(gCorrigeNumero($mT295['T295_Valor_PIS'], 4) != gCorrigeNumero($mT296['T296_Valor_PIS'], 4)) {
		//$dif_Valor_PIS = $mT295['T295_Valor_PIS'] - $mT296['T296_Valor_PIS'];
		//$formArray['campos']['geral']['dif_Valor_PIS'] = array('name' => 'dif_Valor_PIS', 'label' => 'Diferença PIS', 'value' => gCorrigeNumero($dif_Valor_PIS, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right');
		//}

		$formArray['campos']['geral']['Valor_Base_COFINS'] = array('name' => 'Valor_Base_COFINS', 'label' => 'Valor Base COFINS', 'value' => gCorrigeNumero($mT296['T296_Valor_Base_COFINS'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['Valor_COFINS'] = array('name' => 'Valor_COFINS', 'label' => 'Valor COFINS', 'value' => gCorrigeNumero($mT296['T296_Valor_COFINS'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');

		//if(gCorrigeNumero($mT295['T295_Valor_COFINS'], 4) != gCorrigeNumero($mT296['T296_Valor_COFINS'], 4)) {
		//$dif_Cofins = $mT295['T295_Valor_COFINS'] - $mT296['T296_Valor_COFINS'];
		//$formArray['campos']['geral']['dif_Valor_COFINS'] = array('name' => 'dif_Valor_COFINS', 'label' => 'Diferença Cofins', 'value' => gCorrigeNumero($dif_Cofins, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right');
		//}
		
		//$formArray['campos']['geral']['Valor_AFRMM'] = array('name' => 'Valor_AFRMM', 'label' => 'Valor AFRMM', 'value' => gCorrigeNumero($mT296['T296_Valor_Total_AFRMM'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');

		//if(gCorrigeNumero($mT295['T295_Valor_AFRMM'], 4) != gCorrigeNumero($mT296['T296_Valor_Total_AFRMM'], 4)) {
		//$dif_AFRMM = $mT295['T295_Valor_AFRMM'] - $mT296['T296_Valor_Total_AFRMM'];
		//$formArray['campos']['geral']['dif_AFRMM'] = array('name' => 'dif_AFRMM', 'label' => 'Diferenca AFRMM', 'value' => gCorrigeNumero($dif_AFRMM, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right'); 
		//}
		
		$formArray['campos']['geral']['Valor_Base_ICMS'] = array('name' => 'Valor_Base_ICMS', 'label' => 'Valor Base ICMS', 'value' => "$mT296[T296_Valor_Base_ICMS_Sem_Reducao]", 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['Valor_Base_ICMS_Normal'] = array('name' => 'Valor_Base_ICMS_Normal', 'label' => 'Valor Base ICMS Normal', 'value' => "$mT296[T296_Valor_Base_ICMS_Normal]", 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		
		$formArray['campos']['geral']['Valor_ICMS'] = array('name' => 'Valor_ICMS', 'label' => 'Valor ICMS', 'value' => "$mT296[T296_Valor_ICMS]", 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');		
		
		$formArray['campos']['geral']['Valor_ICMS_Presumido'] = array('name' => 'Valor_ICMS_Presumido', 'label' => 'Valor ICMS Presumido', 'value' => "$mT296[T296_Valor_ICMS_Presumido]", 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		
		$formArray['campos']['geral']['Valor_ICMS_Pagar'] = array('name' => 'Valor_ICMS_Pagar', 'label' => 'Valor ICMS Recolher', 'value' => "$mT296[T296_Valor_ICMS_Pagar]", 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right' );

		//if(gCorrigeNumero($mT295['T295_Valor_ICMS'], 4) != $mT296['T296_Valor_ICMS_Pagar']) {
		//$dif_Valor_ICMS = $mT295['T295_Valor_ICMS'] - gCorrigeNumeroInverte($mT296['T296_Valor_ICMS_Pagar']);			
		//$formArray['campos']['geral']['dif_Valor_ICMS_Pagar'] = array('name' => 'dif_Valor_ICMS_Pagar', 'label' => 'Diferenca ICMS Pagar', gCorrigeNumero($dif_Valor_ICMS, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right'); 
		//}
		
		$formArray['campos']['geral']['T296_Peso_Kg_Liquido_Unitario'] = array('name' => 'T296_Peso_Kg_Liquido_Unitario', 'label' => 'Peso total Kg', 'value' => "$mT296[T296_Peso_Kg_Liquido_Unitario]", 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');

		//if(gCorrigeNumero($mT295['T295_Peso_Liquido'], 4) != $mT296['T296_Peso_Kg_Liquido_Unitario']) {
		//$dif_Peso_Liquido = $mT295['T295_Peso_Liquido'] - gCorrigeNumeroInverte($mT296['T296_Peso_Kg_Liquido_Unitario']);			
		//$formArray['campos']['geral']['dif_Peso_Liquido'] = array('name' => 'dif_Peso_Liquido', 'label' => 'Diferença Peso liquido', 'value' => gCorrigeNumero($dif_Peso_Liquido, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right'); 
		//}
		
		$formArray['campos']['geral']['T296_Valor_Total_Preco_Nota_Fiscal'] = array('name' => 'T296_Valor_Total_Preco_Nota_Fiscal', 'label' => 'Total NFe', 'value' => "$mT296[T296_Valor_Total_Preco_Nota_Fiscal]", 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		
		
		$formArray['campos']['geral']['T296_Valor_Total_Despesas'] = array('name' => 'T296_Valor_Total_Despesas', 'label' => 'Total Despesas', 'value' => "$mT296[T296_Valor_Total_Despesas]", 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		
		
		$formArray['campos']['geral']['T296_Valor_Total_Custo_Final'] = array('name' => 'T296_Valor_Total_Custo_Final', 'label' => 'Total Custo', 'value' => "$mT296[T296_Valor_Total_Custo_Final]", 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');

		
		$formArray['campos']['geral']['T296_Quantidade'] = array('name' => 'T296_Quantidade', 'label' => 'Total Peças', 'value' => "$mT296[T296_Quantidade]", 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');

		//$formArray['campos']['geral']['QuantidadeVendido'] = array('name' => 'QuantidadeVendido', 'label' => 'Total Peças Vendidas', 'value' => gCorrigeNumero($totalItensVendido, 4), 'type' => 'text', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');

		$formArray['campos']['geral']['TotalVendidoLiquido'] = array('name' => 'TotalVendidoLiquido', 'label' => 'Total Vendido Líquido', 'value' => gCorrigeNumero($totalVendido), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['TotalVendidoBruto'] = array('name' => 'TotalVendidoBruto', 'label' => 'Total Vendido Bruto', 'value' => gCorrigeNumero($totalVendidoBruto), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		
		
		$form = new Formulario('form-cad017FormPrincipal');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$alert}
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$acaoId = $g['mysqlLastId'];
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Alterar DI - ID: ' + '{$acaoId}', '/imp/imp005/content/imp005ContentDiIncluir/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [900,550]);
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

	break;
	
	
	/**
	* @author AndreySliwinski
	* @since 21/11/2011  
	*/
	case 'imp005FormProdutoIncluir':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T295_Id = (isset($_REQUEST['T295_Id'])) ? $_REQUEST['T295_Id'] : false;
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/*
		$sqlT296 = mysql_query("SELECT * FROM T296 WHERE T296_T295_Id = '{$T295_Id}' ORDER BY T296_Id DESC LIMIT 1");
		if(mysql_num_rows($sqlT296) > 0){
			$T296 = mysql_fetch_assoc($sqlT296);
			$T296_Aliquota_ICMS = gCorrigeNumero($T296['T296_Aliquota_ICMS']);
			$T296_Aliquota_ICMS_Presumido = gCorrigeNumero($T296['T296_Aliquota_ICMS_Presumido']);
			$T296_Percentual_Reducao_ICMS = gCorrigeNumero($T296['T296_Percentual_Reducao_ICMS']);
			$T296_Percentual_Reducao_ICMS_Presumido = gCorrigeNumero($T296['T296_Percentual_Reducao_ICMS_Presumido']);
			$T296_Aliquota_ICMS_PIS_COFINS = gCorrigeNumero($T296['T296_Aliquota_ICMS_PIS_COFINS']);
			$T296_Aliquota_ICMS_Credito_Presumido = gCorrigeNumero($T296['T296_Aliquota_ICMS_Credito_Presumido']);
		}
		*/

		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T296';
		$form_campo_pk = 'T296_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('imp005FormProdutosBtnAdd', 'imp005FormProdutosBtnCln', 'imp005FormProdutosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp005/form/imp005FormProdutoIncluir/';

		//form editor 
		$form_editor = array(
			'geral' => '{"Procura_Produto":{"top":7,"left":35.75,"width":154,"hidden":0},"Marca":{"top":7,"left":299.65625,"width":200,"hidden":0},"T296_Aliquota_ICMS":{"top":7,"left":677.546875,"width":89,"hidden":0},"T296_Descricao_Produto":{"top":30,"left":25.71875,"width":456,"hidden":0},"T296_Aliquota_ICMS_Presumido":{"top":30,"left":621.90625,"width":89,"hidden":0},"T296_Quantidade":{"top":53,"left":18.4375,"width":88,"hidden":0},"T296_D037_Id":{"top":53,"left":210.515625,"width":95,"hidden":0},"T296_D005_Id":{"top":53,"left":386,"width":127,"hidden":0},"T296_Percentual_Reducao_ICMS":{"top":53,"left":631.9375,"width":89,"hidden":0},"T296_Valor_Custo_Unitario":{"top":76,"left":5.34375,"width":88,"hidden":0},"T296_Flag_ME":{"top":76,"left":234.75,"width":96,"hidden":0},"T296_Peso_Kg_Liquido_Unitario":{"top":76,"left":363.765625,"width":120,"hidden":0},"T296_Percentual_Reducao_ICMS_Presumido":{"top":76,"left":576.296875,"width":89,"hidden":0},"T296_Aliquota_Imposto_Importacao":{"top":99,"left":57.84375,"width":88,"hidden":0},"T296_Aliquota_IPI":{"top":99,"left":226.90625,"width":88,"hidden":0},"T296_Aliquota_PIS":{"top":99,"left":383.359375,"width":120,"hidden":0},"T296_Aliquota_ICMS_PIS_COFINS":{"top":99,"left":615.671875,"width":89,"hidden":0},"T296_Aliquota_COFINS":{"top":122,"left":25.65625,"width":88,"hidden":0},"T296_Valor_Adicao":{"top":122,"left":216.53125,"width":88,"hidden":0},"T296_T066_Id":{"top":122,"left":384,"width":127,"hidden":0},"T296_Aliquota_ICMS_Credito_Presumido":{"top":122,"left":583.9375,"width":89,"hidden":0},"T296_Codigo_Produto_Fornecedor":{"top":145,"left":27.3125,"width":88,"hidden":0},"imp005FormProdutosBtnCln":{"top":145,"left":410,"width":null,"hidden":0},"imp005FormProdutosBtnAdd":{"top":145,"left":470,"width":null,"hidden":0},"T296_Cotacao_CC":{"top":191,"left":-2.03125,"width":98,"hidden":1}}'
		);

		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			left join T295 on T295_Id = '{$T295_Id}'
			where (D009_C004_Id = '{$g['empresaAtual']}')
			  and D001_Flag_Pre_Cadastro!='S'
			  and D001_Flag_Ativo = 'S'
			group by D009_Id
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																								// tabela
			'D009_Id,T295_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D001_Descricao_Ingles,D082_Marca,D009_Quantidade_Estoque_Liquido,"' . $T295_Id . '" as T295_Id',// campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Código,Descrição,Descrição Inglês,Marca,Estoque,PO',																	// descrição dos campos
			'campoProcuraProduto',																				// callback js
			$g['divId'],																							// divId
			null,
			null, // Campos que são ignorados na cláusula where do pesquisaGrid
			null,
			null,
			null,
			null,
			null,
			null,
			'D001_Descricao_Produto,Descricao,texto||D001_Codigo_Produto,Codigo,texto||D009_Quantidade_Estoque_Liquido,Estoque,numero', // Paramentro para montar os filtros
		);

		
		$T296=mysql_query("select * 
		                    from T296 
		                left join T006 on T006_Id=T296_T006_Id    
	                   	left join D009 on D009_Id=T296_D009_Id
	                   	left join D049 on D049_Id=D009_D049_Id
	                   	left join D082 on D082_Id=D049_D082_Id
		                   where T296_Id='$acaoId'");
		$mT296=mysql_fetch_array($T296);

		$whereT270=' WHERE T270_T295_Id='.$T295_Id;
		// novo formulário
		$form = new Formulario('imp005FormProdutosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(25,60), 'reject' => true, 'value' => $mT296['T296_Codigo_Produto']),
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(25,255), 'reject' => true, 'readonly'=>true, 'style'=>'float:right', 'align'=>'right', 'value' => $mT296['D082_Marca']),
					'T296_Descricao_Produto' => array('name' => 'T296_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(75, 150)),
					'T296_Quantidade' => array('name' => 'T296_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(10, 30), 'casasDecimais' => '3'),
					'T296_D037_Id' => array('name' => 'T296_D037_Id', 'label' => 'Unidade', 'type' => 'select', 'align' => 'left', 'select' => 
						gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', '', true)
					),
					'T296_D005_Id' => array('name' => 'T296_D005_Id', 'label' => 'NCM', 'type' => 'select', 'align' => 'left', 'select' => 
						gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal', 'ORDER BY D005_Classificacao_Fiscal', true)
					),
					'T296_Valor_Custo_Unitario' => array('name' => 'T296_Valor_Custo_Unitario', 'type' => 'moeda', 'label' => 'Preço Unitário', 'casasDecimais' => '7', 'length' => array(10, 30)),
					'T296_Flag_ME' => array('name' => 'T296_Flag_ME', 'label' => 'ME', 'type' => 'select', 'align'=>'right', 'select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S', 'selected' => true)
						)
					),
					'T296_Aliquota_Imposto_Importacao' => array('name' => 'T296_Aliquota_Imposto_Importacao', 'label' => 'II %', 'type' => 'moeda', 'length' => array(10,50)),
					'T296_Aliquota_IPI' => array('name' => 'T296_Aliquota_IPI', 'label' => 'IPI %', 'type' => 'moeda', 'length' => array(10,50), 'align'=>'right'),
					'T296_Aliquota_PIS' => array('name' => 'T296_Aliquota_PIS', 'label' => 'PIS %', 'type' => 'moeda', 'length' => array(10,50), 'align'=>'right'),
					'T296_Aliquota_COFINS' => array('name' => 'T296_Aliquota_COFINS', 'label' => 'COFINS %', 'type' => 'moeda', 'length' => array(10,50)),
					'T296_Valor_Adicao' => array('name' => 'T296_Valor_Adicao', 'label' => 'Adição', 'type' => 'text', 'length' => array(10,50), 'align'=>'right'),
					'T296_Peso_Kg_Liquido_Unitario' => array('name' => 'T296_Peso_Kg_Liquido_Unitario', 'label' => 'Peso Unit', 'type' => 'moeda', 'casasDecimais' => '6', 'length' => array(10,50), 'align'=>'right'),
					'T296_Aliquota_ICMS' => array('name' => 'T296_Aliquota_ICMS', 'label' => '% ICMS', 'type' => 'moeda', 'value' => $T296_Aliquota_ICMS),
					'T296_Aliquota_ICMS_Presumido' => array('name' => 'T296_Aliquota_ICMS_Presumido', 'label' => '% ICMS Presumido', 'type' => 'moeda', 'value' => $T296_Aliquota_ICMS_Presumido),
					'T296_Percentual_Reducao_ICMS' => array('name' => 'T296_Percentual_Reducao_ICMS', 'label' => '% Redução ICMS', 'type' => 'moeda', 'value' => $T296_Percentual_Reducao_ICMS),
					'T296_Percentual_Reducao_ICMS_Presumido' => array('name' => 'T296_Percentual_Reducao_ICMS_Presumido', 'label' => '% Redução ICMS Presumido', 'type' => 'moeda', 'value' => $T296_Percentual_Reducao_ICMS_Presumido),
					'T296_Aliquota_ICMS_PIS_COFINS' => array('name' => 'T296_Aliquota_ICMS_PIS_COFINS', 'label' => '% ICMS PIS/COFINS', 'type' => 'moeda', 'value' => $T296_Aliquota_ICMS_PIS_COFINS),
					'T296_Aliquota_ICMS_Credito_Presumido' => array('name' => 'T296_Aliquota_ICMS_Credito_Presumido', 'label' => '% ICMS Credito Presumido', 'type' => 'moeda', 'value' => $T296_Aliquota_ICMS_Credito_Presumido),
     				// Campos Hidden
					'T296_D009_Id' => array('name' => 'T296_D009_Id', 'label' => 'T296_D009_Id', 'type' => 'hidden', 'length' => array(20, 20)),
					'T296_Codigo_Produto' => array('name' => 'T296_Codigo_Produto', 'label' => 'Código', 'type' => 'hidden', 'length' => array(20, 20)),
					'T296_Cotacao_CC' => array('name' => 'T296_Cotacao_CC', 'label' => 'Cotação CC', 'type' => 'moeda'),
					'T296_T295_Id' => array('name' => 'T296_T295_Id', 'label' => 'T296_T295_Id', 'type' => 'hidden', 'value' => $T295_Id),
					'T296_C004_Id' => array('name' => 'T296_C004_Id', 'label' => 'T296_C004_Id', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
			        'T296_T066_Id' => array('name' => 'T296_T066_Id', 'label' => 'Local', 'type' => 'select', 'align' => 'left', 'select' => 
						gGeraSelect('D004', 'D004_Id', 'D004_Local', 'left join T006 on T006_D004_Id where false', true)
					),
					'T296_Codigo_Produto_Fornecedor' => array('name' => 'T296_Codigo_Produto_Fornecedor', 'label' => 'Cód. Forn'),
				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['imp005FormProdutosBtnNew'] = array('name' => 'imp005FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['imp005FormProdutosBtnCln'] = array('name' => 'imp005FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['imp005FormProdutosBtnAdd'] = array('name' => 'imp005FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', '');
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					$insereDuplicado = true;
					if($g['C031']['permitirProdutosDuplicadosImportacao'] == 'N'){
						$T296_Codigo_Produto = $form->campoValorEnviado('T296_Codigo_Produto');
						$T296 = mysql_query("SELECT T296_Codigo_Produto 
											   FROM T296 
											  WHERE T296_Codigo_Produto='{$T296_Codigo_Produto}' 
											    AND T296_T295_Id='{$T295_Id}'
											");
						if(mysql_num_rows($T296) > 0){
							$insereDuplicado = false;
						}
					}
					if($insereDuplicado == false){
						echo gGeraAlertMsg('Erro', "Já existe o item com o código ".$T296_Codigo_Produto." nesta DI. Por favor, selecione outro produto.");
					}else if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)){
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}


		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);


		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
			

		break;

	case 'imp005FormAtualizarLocacao':
		global $g;
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T296';
		$form_campo_pk = 'T296_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp005FormAtualizarLocacao_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp005/form/imp005FormAtualizarLocacao/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"D146_Id":{"top":7,"left":23.546875,"width":143,"hidden":0},"imp005FormAtualizarLocacao_btn":{"top":5,"left":400,"width":null,"hidden":0}}',
        );
		
		$form = new Formulario('imp005FormAtualizarLocacao');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D146_Id' => array('name' => 'D146_Id', 'label' => 'Selecione o armazém origem das locações', 'type' => 'select', 'reject' => true,
						'select' => gGeraSelect('D146', 'D146_Id', 'D146_Descricao', 'WHERE true', true)
					),
				),
			)
		);

		$formArray['campos']['geral']['imp005FormAtualizarLocacao_btn'] = array('name' => 'imp005FormAtualizarLocacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Atualizar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				if (isset($acaoId)){	
					$D146_Id = $form->campoValorEnviado('D146_Id');
				    echo <<<EOT
				        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							setTimeout(function() {							  
								$.getJSON('/imp/imp005/form_func-ajax/validarArmazem/?ajax=true&T295_Id=' + encodeURIComponent('{$acaoId}') + '&D146_Id=' + encodeURIComponent("{$D146_Id}") + '&callback=?', function(request) {
									if (request.code) {
										$.getJSON('/imp/imp005/form_func-ajax/atualizarArmazemEmProdutos/?ajax=true&T295_Id=' + encodeURIComponent("{$acaoId}") + '&D146_Id=' + encodeURIComponent("{$D146_Id}") + '&callback=?', function(request) {
											if (request.code) {
												dialogConfirm('Atualizar locação',request.data);
											} else {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									    		dialogConfirm('Erro', request.data, \$buttons);
											}
											$('#{$g['divId']}').hideLoading();
										});
									} else {
										var \$buttons = {
											"Sim": function() {
												$('#{$g['divId']}').showLoading();
												$.getJSON('/imp/imp005/form_func-ajax/atualizarArmazemEmProdutos/?ajax=true&T295_Id=' + encodeURIComponent("{$acaoId}") + '&D146_Id=' + encodeURIComponent("{$D146_Id}") + '&callback=?', function(request) {
													if (request.code) {
														dialogConfirm('Atualizar locação',request.data);
													} else {
														var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											    		dialogConfirm('Erro', request.data, \$buttons);
													}
													$('#{$g['divId']}').hideLoading();
												});
												$(this).dialog("destroy").remove();
											},
											"Não": function() { $(this).dialog("destroy").remove(); } 
										};
										dialogConfirm('Atualização locação',request.data, \$buttons);
									}
								});
							}, 100);
				        } </script></div>
EOT;
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	
		$botoesDir = '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'imp005FormDocumentos':
				/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T155';
		$form_campo_pk = 'T155_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('imp005FormDocumentosBtnAdd', 'imp005FormDocumentosBtnCln', 'imp005FormDocumentosBtnNew');
        // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp005/form/imp005FormDocumentos/';
		// Array de definição do formulário
		$r_T295_Id = isset($r_imp005FormDocumentos) ? $r_imp005FormDocumentos['T155_T295_Id'] : $r_T295_Id;
		                                                           
		$tmp = gGeraSelect('T295', 'T295_Id', 'T295_Numero_DI', "WHERE T295_Id = $r_T295_Id");
		foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('imp005FormDocumentos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		//$formArray['campos']['geral']['__T155_T295_Id'] 		= array('name' => '__T155_T295_Id', 'label'=>'Número DI', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
		//	$tmp
		//);
		$formArray['campos']['geral']['T155_Descricao'] 	= array('name' => 'T155_Descricao', 'label'=>'Descrição', 'type' => 'textarea','length' => array(3,60));
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);
		$formArray['campos']['geral']['T155_Nome_Arquivo'] 		= array('name' => 'T155_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false);
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['imp005FormDocumentosBtnNew'] = array('name' => 'imp005FormDocumentosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['T155_T295_Id'] 			= array('name' => 'T155_T295_Id', 'type' => 'hidden', 'value' => $r_T295_Id);
		$formArray['campos']['geral']['T155_C007_Id'] 			= array('name' => 'T155_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']);
		$formArray['campos']['geral']['T155_Data_Hora_Upload']	= array('name' => 'T155_Data_Hora_UpLoad', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s'));
		//$formArray['campos']['geral']['imp005FormDocumentosBtnCln'] = array('name' => 'imp005FormDocumentosBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['imp005FormDocumentosBtnAdd'] = array('name' => 'imp005FormDocumentosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['divReload'] 	= array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);
		
		$form->definirFormularioArray($formArray);
		
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$documento=$form->campoValorEnviado('uploadArquivo');
				if($documento['error'] !== 0 && $documento['size'] <= 0){
					echo gGeraAlertMsg('Erro', 'Você deverá selecionar um arquivo!');
				}else{
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										if (typeof jQuery != 'undefined') {
											divRefresh("{$g['divId']}", true);
											divRefresh('{$form->campoValorEnviado('divReload')}', true);
										}
									} </script></div>
EOT;
						}
					} else {
						// Insert
						// Antes de criar o registro no banco, vamos verificar se o arquivo foi realmente enviado
						$arquivo = $form->campoValorEnviado('uploadArquivo');
						if ($arquivo['error'] == UPLOAD_ERR_OK) {
							// Neste caso, guardamos o nome do arquivo no banco
							$form->campoSetarValor('T155_Nome_Arquivo', $arquivo['name'], true);
							if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
								// Gravar o arquivo no disco
								$pathinfo = pathinfo($arquivo['name']);
								$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
								//$dir = "{$g['pathDados']}documentos/T295_".str_repeat('0',5-strlen($form->campoValorEnviado('T155_T295_Id'))).$form->campoValorEnviado('T155_T295_Id');
								$dir = "{$g['pathDados']}di";
								is_dir($dir) or mkdir($dir, 0777, true);
								$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
								move_uploaded_file($arquivo['tmp_name'], $file);
								echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										if (typeof jQuery != 'undefined') {
											divRefresh("{$g['divId']}", true);
											divRefresh('{$form->campoValorEnviado('divReload')}', true);
										}
									} </script></div>
EOT;
							}
						}
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo  $processado;
					}
				}
			}
		}
		
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
		
		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		// $botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;

	case 'imp005FormDocumentosDespesa':

		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T168_Id = isset($r_T168_Id) ? $r_T168_Id : false;
		$T155_T295_Id = isset($r_T155_T295_Id) ? $r_T155_T295_Id : false;
		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T155';
		$form_campo_pk = 'T155_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('imp005FormDocumentosDespesaBtnAdd', 'imp005FormDocumentosDespesaBtnCln', 'imp005FormDocumentosDespesaBtnNew');
        // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp005/form/imp005FormDocumentosDespesa/';
		// Array de definição do formulário
		$r_T295_Id = isset($r_imp005FormDocumentosDespesa) ? $r_imp005FormDocumentosDespesa['T155_T295_Id'] : $r_T295_Id;
		                                                           
		$tmp = gGeraSelect('T295', 'T295_Id', 'T295_Numero_DI', "WHERE T295_Id = $r_T295_Id");
		foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('imp005FormDocumentosDespesa');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		//$formArray['campos']['geral']['__T155_T295_Id'] 		= array('name' => '__T155_T295_Id', 'label'=>'Número DI', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
		//	$tmp
		//);
		$formArray['campos']['geral']['T155_Descricao'] 	= array('name' => 'T155_Descricao', 'label'=>'Descrição', 'type' => 'textarea','length' => array(3,60));
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);
		$formArray['campos']['geral']['T155_Nome_Arquivo'] 		= array('name' => 'T155_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false);
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['imp005FormDocumentosDespesaBtnNew'] = array('name' => 'imp005FormDocumentosDespesaBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['T155_T295_Id'] 			= array('name' => 'T155_T295_Id', 'type' => 'hidden', 'value' => $T155_T295_Id);
		$formArray['campos']['geral']['T155_T168_Id'] 			= array('name' => 'T155_T168_Id', 'type' => 'hidden', 'value' => $T168_Id);
		$formArray['campos']['geral']['T155_C007_Id'] 			= array('name' => 'T155_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']);
		$formArray['campos']['geral']['T155_Data_Hora_Upload']	= array('name' => 'T155_Data_Hora_UpLoad', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s'));
		//$formArray['campos']['geral']['imp005FormDocumentosDespesaBtnCln'] = array('name' => 'imp005FormDocumentosDespesaBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['imp005FormDocumentosDespesaBtnAdd'] = array('name' => 'imp005FormDocumentosDespesaBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['divReload'] 	= array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);
		
		$form->definirFormularioArray($formArray);
		
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$documento=$form->campoValorEnviado('uploadArquivo');
				if($documento['error'] !== 0 && $documento['size'] <= 0){
					echo gGeraAlertMsg('Erro', 'Você deverá selecionar um arquivo!');
				}else{
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										if (typeof jQuery != 'undefined') {
											divRefresh("{$g['divId']}", true);
											divRefresh('{$form->campoValorEnviado('divReload')}', true);
										}
									} </script></div>
EOT;
						}
					} else {
						// Insert
						// Antes de criar o registro no banco, vamos verificar se o arquivo foi realmente enviado
						$arquivo = $form->campoValorEnviado('uploadArquivo');
						if ($arquivo['error'] == UPLOAD_ERR_OK) {
							// Neste caso, guardamos o nome do arquivo no banco
							$form->campoSetarValor('T155_Nome_Arquivo', $arquivo['name'], true);
							if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
								// Gravar o arquivo no disco
								$pathinfo = pathinfo($arquivo['name']);
								$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
								//$dir = "{$g['pathDados']}documentos/T295_".str_repeat('0',5-strlen($form->campoValorEnviado('T155_T295_Id'))).$form->campoValorEnviado('T155_T295_Id');
								$dir = "{$g['pathDados']}di";
								is_dir($dir) or mkdir($dir, 0777, true);
								$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
								move_uploaded_file($arquivo['tmp_name'], $file);
								echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										if (typeof jQuery != 'undefined') {
											divRefresh("{$g['divId']}", true);
											divRefresh('{$form->campoValorEnviado('divReload')}', true);
										}
									} </script></div>
EOT;
							}
						}
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo  $processado;
					}
				}
			}
		}
		
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
		
		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		// $botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	case 'imp005FormCadastroDespesas':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		$T295_Id = isset($r_T295_Id) ? $r_T295_Id : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T300';
		$form_campo_pk = 'T300_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp005FormCadastroDespesasBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp005/form/imp005FormCadastroDespesas/';

		//form editor
		$form_editor = array('geral' => '{"T300_D191_Id":{"top":7,"left":57.71875,"width":383,"hidden":0},"T300_Data":{"top":30,"left":83.828125,"width":88,"hidden":0},"T300_Flag_Soma_Base_IPI":{"top":53,"left":17.65625,"width":55,"hidden":0},"T300_Flag_Soma_Base_COFINS":{"top":53,"left":328.421875,"width":55,"hidden":0},"T300_Flag_Soma_Base_ICMS":{"top":76,"left":4.296875,"width":55,"hidden":0},"T300_Flag_Soma_Base_PIS":{"top":76,"left":350.125,"width":55,"hidden":0},"T300_Flag_Soma_Base_II":{"top":99,"left":24.59375,"width":55,"hidden":0},"T300_Flag_Soma_Preco_NFe":{"top":99,"left":342.65625,"width":55,"hidden":0},"T300_Flag_Soma_Custo":{"top":122,"left":29.40625,"width":55,"hidden":0},"T300_Flag_Tipo_Rateio":{"top":145,"left":75.484375,"width":95,"hidden":0},"T300_Flag_ME":{"top":145,"left":380.4375,"width":55,"hidden":0},"T300_Flag_Tipo":{"top":168,"left":84.921875,"width":95,"hidden":0},"T300_Cotacao_ME":{"top":168,"left":383,"width":56,"hidden":0},"T300_Valor":{"top":191,"left":81.203125,"width":88,"hidden":0},"T300_Aliquota":{"top":214,"left":65.875,"width":88,"hidden":0},"T300_Formula":{"top":237,"left":66.28125,"height":108,"width":376,"hidden":0}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T300_D191_Id' => array('name' => 'T300_D191_Id', 'label' => 'Descrição', 'type' => 'select', 'select' =>
						gGeraSelect('D191', 'D191_Id', 'D191_Descricao_Despesa','ORDER BY D191_Descricao_Despesa'),
					),		
					'T300_Aliquota' => array('name' => 'T300_Aliquota', 'label' => 'Alíquota', 'type' => 'moeda', 'length' => array(20,10)),
					'T300_Data' => array('name' => 'T300_Data', 'label' => 'Data','type' => 'data', 'length' => array(15,255)),
					'T300_Flag_Soma_Base_IPI' => array('name' => 'T300_Flag_Soma_Base_IPI', 'label' => 'Somar na base IPI', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T300_Flag_Soma_Base_ICMS' => array('name' => 'T300_Flag_Soma_Base_ICMS', 'label' => 'Somar na base ICMS', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T300_Flag_Soma_Base_PIS' => array('name' => 'T300_Flag_Soma_Base_PIS', 'label' => 'Somar na base PIS', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T300_Flag_Soma_Base_COFINS' => array('name' => 'T300_Flag_Soma_Base_COFINS', 'label' => 'Somar na base COFINS', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T300_Flag_Soma_Base_II' => array('name' => 'T300_Flag_Soma_Base_II', 'label' => 'Somar na base II', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T300_Flag_Soma_Custo' => array('name' => 'T300_Flag_Soma_Custo', 'label' => 'Somar no custo', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T300_Flag_Soma_Preco_NFe' => array('name' => 'T300_Flag_Soma_Preco_NFe', 'label' => 'Somar no preço NFe', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T300_Flag_Tipo_Rateio' => array('name' => 'T300_Flag_Tipo_Rateio', 'label' => 'Rateio', 'type' => 'select','select' => 
						array(
							array('title' => 'Custo', 'value' => 'C', 'selected' => true),		
							array('title' => 'Peso', 'value' => 'P'),				
						)
					),
					'T300_Flag_Tipo' => array('name' => 'T300_Flag_Tipo', 'label' => 'Tipo', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'Valor fixo', 'value' => 'V'),	
							array('title' => 'Fórmula', 'value' => 'F'),
						)
					),
					'T300_Flag_ME' => array('name' => 'T300_Flag_ME', 'label' => 'Valor em ME', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T300_Cotacao_ME' => array('name' => 'T300_Cotacao_ME', 'label' => 'Cotação ME', 'type' => 'moeda', 'length' => array(20,10)),
					'T300_Valor' => array('name' => 'T300_Valor', 'label' => 'Valor', 'type' => 'moeda', 'length' => array(20,10)),
					'T300_Formula' => array('name' => 'T300_Formula', 'label' => 'Fórmula', 'type' => 'textarea', 'uppercase' => false, 'code' => 'php', 'codeModoSimples' => true),
				'T300_T295_Id' => array('name' => 'T300_T295_Id', 'label' => 'T300_T295_Id', 'value'=> @$T295_Id, 'type' => 'hidden', 'length' => array(2,60)),
				)
			)
		);
        $form = new Formulario('form-imp005FormDespesas');
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} #T300_Flag_Tipo').change(function() {
				if (($(this).val() == 'V')) {
					$('#{$g['divId']} #T300_Valor').closest('.formEditor').show();
					setTimeout(function() {
						$('#{$g['divId']} #T300_Valor').focus();
					}, 100);
				}else{
					$('#{$g['divId']} #T300_Valor').val('');
					$('#{$g['divId']} #T300_Valor').closest('.formEditor').hide();
				}
				if($(this).val() == 'F'){
					$('#{$g['divId']} #T300_Formula').closest('.formEditor').show();
					$('#{$g['divId']} #T300_Aliquota').closest('.formEditor').show();
					setTimeout(function() {
						$('#{$g['divId']} #T300_Formula').focus();
						$('#{$g['divId']} #T300_Aliquota').focus();
					}, 100);
				}else{
					$('#{$g['divId']} #T300_Formula').val('');
					$('#{$g['divId']} #T300_Formula').closest('.formEditor').hide();
					$('#{$g['divId']} #T300_Aliquota').val('');
					$('#{$g['divId']} #T300_Aliquota').closest('.formEditor').hide();
				}
			});
			$('#{$r_divId} #T300_Flag_Tipo').trigger('change');
			$('#{$g['divId']} #T300_Flag_ME').change(function() {
				if (($(this).val() == 'S')) {
					$('#{$g['divId']} #T300_Cotacao_ME').closest('.formEditor').show();
					setTimeout(function() {
						$('#{$g['divId']} #T300_Cotacao_ME').focus();
					}, 100);
				}else{
					$('#{$g['divId']} #T300_Cotacao_ME').val('');
					$('#{$g['divId']} #T300_Cotacao_ME').closest('.formEditor').hide();
				}
			});
			$('#{$r_divId} #T300_Flag_ME').trigger('change');
		} </script></div>
EOT;

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						//$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="imp005FormCadastroDespesasBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'imp005FormCadastroContasReceber':		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($r_fin001FormTituloReceber)) ? $r_fin001FormTituloReceber['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}
				
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T002';
		$form_campo_pk = 'T002_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp005FormCadastroContasReceberbtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp005/form/imp005FormCadastroContasReceber/';

		$form_editor = array(
			'cadastro_titulo' => '{"T002_D024_Id":{"top":7,"left":52,"width":409,"hidden":0},"T002_D014_Id":{"top":30,"left":49,"width":409,"hidden":0},"T002_Nome_Cliente":{"top":53,"left":23,"width":424,"hidden":0},"T002_Data_Emissao":{"top":76,"left":15,"width":120,"hidden":0},"T002_Data_Vencimento":{"top":76,"left":301,"width":120,"hidden":0},"T002_Numero_Documento":{"top":99,"left":19,"width":120,"hidden":0},"T002_Numero_Duplicata":{"top":99,"left":324,"width":120,"hidden":0},"T002_Flag_Status":{"top":122,"left":59,"width":128,"hidden":0},"T002_T295_Id":{"top":122,"left":383,"width":127,"hidden":0},"T002_D027_Id":{"top":145,"left":41,"width":127,"hidden":0},"T002_Valor_Duplicata":{"top":168,"left":10,"width":120,"hidden":0},"T002_Valor_Juros":{"top":191,"left":31,"width":120,"hidden":0},"T002_Valor_Desconto":{"top":191,"left":319,"width":120,"hidden":0},"T002_Valor_Recebido":{"top":214,"left":13,"width":120,"hidden":0},"T002_Valor_Total":{"top":168,"left":342,"width":120,"hidden":0},"T002_Valor_Saldo":{"top":214,"left":367,"width":120,"hidden":0},"T002_T135_Id":{"top":237,"left":48,"width":127,"hidden":0},"T002_Observacao":{"top":260,"left":29,"height":85,"width":424,"hidden":0}}'
		);
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$form = new Formulario('imp005FormCadastroContasReceber');  
		$formArray = array(
			'campos' => array(
				'cadastro_titulo' => array(
					'T002_D024_Id' => array('name' => 'Consulta_T002_D024_Id', 'label'=>'Clientes', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3/%4] - %5 Tel:(%7)%8'),'length' => array(67,30)),
					//'T002_D024_Id' => array('name' => 'T002_D024_Id', 'align' => 'right', 'style'=> 'float: right', 'type' => 'text', 'value'=>$_REQUEST['T002_Id'], 'length' => array(10,5)),
					'T002_D014_Id' => array('name' => 'Consulta_T002_D014_Id','label'=>'Contas', 'type' => 'pesquisa','pesquisa'=>array('D014','D014_Id','D014_SubConta,D154_Descricao,D014_Grupo','LEFT JOIN D154 ON D154_Id=D014_D154_Id where D014_Flag_Plano="M"'), 'length' => array(67,30)),
					//'T002_D014_Id' => array('name' => 'T002_D014_Id', 'align' => 'right', 'style' => 'float:right', 'type' => 'text', 'length' => array(10,5)),
					'T002_Nome_Cliente' => array('name' => 'T002_Nome_Cliente', 'label' => 'Complemento', 'type' => 'text', 'length' => array(67,30)),				
					'T002_Data_Emissao' => array('name' => 'T002_Data_Emissao', 'label'=>'Data Emissão','type' => 'data', 'length' => array(15,10)),
					'T002_Data_Vencimento' => array('name' => 'T002_Data_Vencimento','label'=>'Data Vencimento ', 'align'=>'right','style'=>'float:right','type' => 'data', 'length' => array(15,10)),
					'T002_Numero_Documento' => array('name' => 'T002_Numero_Documento', 'label'=>'Nº Documento','type' => 'text', 'length' => array(15,10)),
					'T002_Numero_Duplicata' => array('name' => 'T002_Numero_Duplicata', 'label'=>'Nº Duplicata ','align'=>'right','style'=>'float:right','type' => 'text', 'length' => array(15,10)),		
					'T002_Flag_Status' => array('name' => 'T002_Flag_Status', 'label'=>'Status', 'type' => 'select', 'extra'=>'style="width:110px;"','select' => 
						array(
							array('title' => 'Normal', 'value' => 1, 'selected' => true),
							array('title' => 'Perdido', 'value' => 2),
						)
					),		
					'T002_T295_Id' => array('name' => 'T002_T295_Id', 'label' => 'DI', 'type' => 'select','select' => 
						gGeraSelect('T295','T295_Id','T295_Numero_DI', '', false, $r_T295_Id),'align'=>'right','style'=>'float:right',
					),			
					'T002_D027_Id' => array('name' => 'T002_D027_Id', 'label' => 'Portador', 'type' => 'select','extra'=>'style="width:110px;"','select' => 
						gGeraSelect('D027','D027_Id','D027_Portador'),
					),
					'T002_Valor_Duplicata' => array('name' => 'T002_Valor_Duplicata', 'label'=>'Valor Duplicata','value' =>'0,00', 'type' => 'moeda', 'length' => array(15,10)),
					'T002_Valor_Juros' => array('name' => 'T002_Valor_Juros', 'label'=>'Valor Juros ','value' =>'0,00','readonly' => true, 'align'=>'right','style'=>'float:right', 'type' => 'text', 'length' => array(15,10)),
					'T002_Valor_Desconto' => array('name' => 'T002_Valor_Desconto', 'label'=>'Valor Desconto','value' =>'0,00','readonly' => true, 'type' => 'text', 'length' => array(15,10)),
					'T002_Valor_Recebido' => array('name' => 'T002_Valor_Recebido', 'label'=>'Valor Recebido ','value' =>'0,00','readonly' => true,'align'=>'right', 'style'=>'float:right', 'type' => 'moeda', 'length' => array(15,10)),
					'T002_Valor_Total' => array('name' => 'T002_Valor_Total', 'label'=>'Valor Total', 'type' => 'moeda', 'value' =>'0,00','readonly' => true, 'length' => array(15,10)),
					'T002_Valor_Saldo' => array('name' => 'T002_Valor_Saldo', 'label'=>'Saldo', 'type' => 'moeda', 'value' =>'0,00','readonly' => true, 'length' => array(15,10)),
					'T002_T135_Id' => array('name' => 'T002_T135_Id', 'label'=>'Contrato', 'type' => 'select', 'style' => 'float:right', 'align'=>'right','select' => 					
						gGeraSelect('T135','T135_Id','T135_Tipo','',true),                                                
					),					
					'T002_Observacao' => array('name' => 'T002_Observacao', 'label'=>'Observação', 'type' => 'textarea', 'length' => array(5,63)),
					
					'T002_C004_Id' => array('name' => 'T002_C004_Id','type' => 'hidden','value'=>$g['empresaAtual']),
					//'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
				),	
			)
		);
		
		$form->definirFormularioArray($formArray);
		
		/**
		* Na maioria dos casos não será necessário alterar nada daqui para baixo
		* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
		*/
		
		
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$T002_Id = false;
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						$T002_Id = $r_acaoId;
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$T002_Id = $g['mysqlLastId'];
						// Imprime uma mensagem e fecha a janela
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
				if ($T002_Id) {
					mysql_query("call T002_Gravar_Totalizacao('$T002_Id')");	
	    			mysql_query("call T002_Baixar_Titulo('$T002_Id','".date('Y-m-d')."')");
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}
		
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');		
	break;

	case 'imp005FormConfiguracoes':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T295';
		$form_campo_pk = 'T295_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp005FormConfiguracoesBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp005/form/imp005FormConfiguracoes/';

        $form_editor = array(
			'geral' => '{"T295_Calculo_THC":{"top":7,"left":92,"width":160,"hidden":0},"imp005FormConfiguracoesBtn":{"top":166,"left":256,"width":null,"hidden":0},"T295_Somar_PIS_Custo":{"top":30,"left":57,"width":59,"hidden":0},"T295_Somar_COFINS_Custo":{"top":53,"left":32,"width":59,"hidden":0},"T295_Somar_Seguro_Custo":{"top":76,"left":40,"width":59,"hidden":0},"T295_Somar_AFRMM_Custo":{"top":99,"left":34,"width":59,"hidden":0},"T295_Somar_ICMS_Pagar_Custo":{"top":122,"left":7,"width":59,"hidden":0},"T295_Somar_IPI_Custo":{"top":145,"left":61,"width":59,"hidden":0}}'
        );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T295_Calculo_THC' => array('name' => 'T295_Calculo_THC', 'label' => 'Cálculo THC', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'Rateio pelo custo', 'value' => '1'),
							array('title' => 'Rateio pelo peso', 'value' => '2'),
						)
					),
					'T295_Somar_PIS_Custo' => array('name' => 'T295_Somar_PIS_Custo', 'label' => 'Somar PIS no custo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T295_Somar_COFINS_Custo' => array('name' => 'T295_Somar_COFINS_Custo', 'label' => 'Somar COFINS no custo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T295_Somar_Seguro_Custo' => array('name' => 'T295_Somar_Seguro_Custo', 'label' => 'Somar seguro no custo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T295_Somar_AFRMM_Custo' => array('name' => 'T295_Somar_AFRMM_Custo', 'label' => 'Somar AFRMM no custo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T295_Somar_ICMS_Pagar_Custo' => array('name' => 'T295_Somar_ICMS_Pagar_Custo', 'label' => 'Somar ICMS a pagar no custo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T295_Somar_IPI_Custo' => array('name' => 'T295_Somar_IPI_Custo', 'label' => 'Somar IPI no custo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'imp005FormConfiguracoesBtn' => array('name' => 'imp005FormConfiguracoesBtn', 'type'=>'button', 'reload' => false, 'value' => 'Alterar', 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-cad017FormPrincipal');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$alert}
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$acaoId = $g['mysqlLastId'];
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Alterar DI - ID: ' + '{$acaoId}', '/imp/imp005/content/imp005ContentDiIncluir/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [900,550]);
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'imp005FormCadastroLI':
	global $g;
	/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
	
		$T295_Id = isset($r_T295_Id) ? $r_T295_Id : false;

		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
		$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
		$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T270';
		$form_campo_pk = 'T270_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp005FormCadastroLIBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp005/form/imp005FormCadastroLI/';

		$form_editor = array(
		'geral' =>    '{"T270_Numero_LI":{"top":7,"left":130,"width":120,"hidden":0},
						"T270_Numero_Processo_Anuente":{"top":30,"left":55,"width":60,"hidden":0},
						"T270_Sigla_Orgao_Anuente":{"top":30,"left":400,"width":120,"hidden":0},
						"T270_Regime_Acordo_Tributario":{"top":53,"left":55,"width":60,"hidden":0},
						"T270_Fundamento_Legal_Regime":{"top":53,"left":350,"width":120,"hidden":0},
						"T270_Numero_Dias_Limite_Pagamento":{"top":76,"left":10,"width":60,"hidden":0},
						"T270_Codigo_Orgao_Financeiro_Internacional":{"top":76,"left":350,"width":120,"hidden":0},
						"T270_Tipo_Acordo_Tarifario":{"top":99,"left":80,"width":160,"hidden":0},
						"T270_Codigo_Acordo_Alardi":{"top":99,"left":390,"width":120,"hidden":0},
						"T270_Numero_Destaque_NCM":{"top":122,"left":70,"width":120,"hidden":0},
						"T270_Drawback_Ato_Isencao":{"top":145,"left":70,"width":120,"hidden":0},
						"T270_Drawback_Ato_Suspensao":{"top":145,"left":380,"width":120,"hidden":0},
						"T270_Codigo_Motivo_Sem_Cobertura":{"top":168,"left":37,"width":160,"hidden":0},
						"T270_Cobertura_Cambial":{"top":191,"left":90,"width":200,"hidden":0},
						"T270_Modalidade_Pagamento":{"top":168,"left":390,"width":120,"hidden":0},      				
						"T270_Informacoes_Complementares":{"top":214,"left":40,"width":450,"hidden":0}
			}');

		// Definição do Formulário
		$formArray = array(
		'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
						'T270_T295_Id' => array('name' => 'T270_T295_Id', 'label' => 'Descrição', 'value'=>@$T295_Id, 'type' => 'hidden', 'length' => array(2,60)),	          
						'T270_Numero_LI' => array('name' => 'T270_Numero_LI', 'label' => 'Número LI', 'type' => 'text', 'length' => array(15,255)),
						'T270_Numero_Processo_Anuente' => array('name' => 'T270_Numero_Processo_Anuente', 'label' => 'Número Processo Anuente', 'type' => 'text', 'length' => array(15,255), 'style'=>'float:right;', 'align'=>'right'),
						'T270_Sigla_Orgao_Anuente' => array('name' => 'T270_Sigla_Orgao_Anuente', 'label' => 'Sigla Órgão Anuente', 'type' => 'text', 'length' => array(5,255)),
						'T270_Regime_Acordo_Tributario' => array('name' => 'T270_Regime_Acordo_Tributario', 'label' => 'Regime Acordo Tributário', 'type' => 'text', 'length' => array(50,255)),
						'T270_Fundamento_Legal_Regime' => array('name' => 'T270_Fundamento_Legal_Regime', 'label' => 'Fundamento Legal do Regime', 'type' => 'text', 'length' => array(5,255)),
						'T270_Tipo_Acordo_Tarifario' => array('name' => 'T270_Tipo_Acordo_Tarifario', 'label' => 'Tipo Acordo Tarifário', 'type' => 'text', 'length' => array(50,255)),
						'T270_Codigo_Acordo_Alardi' => array('name' => 'T270_Codigo_Acordo_Alardi', 'label' => 'Código do Acordo Aladi', 'type' => 'text', 'length' => array(5,15)),
						'T270_Numero_Dias_Limite_Pagamento' => array('name' => 'T270_Numero_Dias_Limite_Pagamento', 'label' => 'Número de Dias Limite Pagamento', 'type' => 'text', 'length' => array(5,50)),
						'T270_Codigo_Orgao_Financeiro_Internacional' => array('name' => 'T270_Codigo_Orgao_Financeiro_Internacional', 'label' => 'Órgão Financeiro Internacional', 'type' => 'text', 'length' => array(5,255)),
						'T270_Codigo_Motivo_Sem_Cobertura' => array('name' => 'T270_Codigo_Motivo_Sem_Cobertura', 'label' => 'Código Motivo Sem Cobertura', 'type' => 'text', 'length' => array(5,50), 'style'=>'float:right;', 'align'=>'right'),
						'T270_Numero_Destaque_NCM' => array('name' => 'T270_Numero_Destaque_NCM', 'label' => 'Número Destaque NCM', 'type' => 'text', 'length' => array(15,50)),
						'T270_Drawback_Ato_Isencao' => array('name' => 'T270_Drawback_Ato_Isencao', 'label' => 'Drawback Ato Isenção', 'type' => 'text', 'length' => array(15,50)),
						'T270_Drawback_Ato_Suspensao' => array('name' => 'T270_Drawback_Ato_Suspensao', 'label' => 'Drawback Ato Suspensão', 'type' => 'text', 'length' => array(15,50)),
						'T270_Informacoes_Complementares' => array('name' => 'T270_Informacoes_Complementares', 'label' => 'Informações Complementares', 'type' => 'textarea', 'length' => array(10,50)),
						'T270_Cobertura_Cambial' => array('name' => 'T270_Cobertura_Cambial', 'label' => 'Cobertura Cambial','type' => 'select', 'select' =>
							array(
								array('title' => 'Com Cobertura Cambial', 'value' => '1'),
								array('title' => 'Sem Cobertura Cambial', 'value' => '2'),
							)
						),
						'T270_Modalidade_Pagamento' => array('name' => 'T270_Modalidade_Pagamento', 'label' => 'Modalidade Pagamento', 'type' => 'text'),
				),
			)
		);
		
		$form = new Formulario('form-imp005FormLI');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
		// Form com erros
		if ($formCodigo == 1) {
			echo gGeraAlertMsgErroForm($form);
		// Form sem erros
		} else if ($formCodigo == 2) {
			// Insert ou Update?
			if (!empty($r_acaoId)) {
			// Update
			if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
				// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
				$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					{$alert}
					// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
					if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
						divRefresh('{$g['divIdParent']}', true);
					}
				} </script></div>
	EOT;
			}
			} else {
			// Insert
			if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
				// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
				$acaoId = $g['mysqlLastId'];
				$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					// Fecha a janela, recarrega o grid e abre a alteração
					$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
					setTimeout(function() {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Alterar LI - ID: ' + '{$acaoId}', '/imp/imp005/content/imp005ContentLIIncluir/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [900,550]);
						{$alert}
					}, 150);
					});
					$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
					divRefresh('{$g['divIdParent']}', true);
				} </script></div>
	EOT;
			}
			}
		}
		} else {
		// Carrega os valores caso seja um update
		if (!empty($r_acaoId)) {
			$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
			// Mostra erros de formulário uteis ao desenvolvedor
			if ($processado !== true) {
			reset($form->formArray['campos']);
			if ($tab == key($form->formArray['campos'])) {
				echo $processado;
			}
			}
		}
		}
		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
			
		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="imp005FormCadastroLIBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

    break;

	case 'imp005FormImportarCSV':
		
		/**
		* PARTE 1: Variáveis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T296';
		$form_campo_pk = 'T296_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp005FormImportarCSV_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp005/form/imp005FormImportarCSV/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponíveis (automacaoFormulario.php)
		$form = new Formulario('imp005FormImportarCSV');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoCSVimp005'] 		= array('name' => 'uploadArquivoCSVimp005', 'label' => 'Selecione o CSV', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['imp005FormImportarCSV_btn'] 		= array('name' => 'imp005FormImportarCSV_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		//$formArray['campos']['geral']['T296_Arquivo_Csv'] 				= array('name' => 'T296_Arquivo_Csv', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
			
		$form->definirFormularioArray($formArray);
			
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$arquivo = $form->campoValorEnviado('uploadArquivoCSVimp005');
				$retorno = $IMP005->importarCSV($arquivo, $acaoId);
					
				if($retorno !== true){
					//$retorno = htmlentities(utf8_decode($retorno));
					$retorno = htmlentities($retorno);
					echo gGeraAlertMsg('Relatório de Importação', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' . $retorno . '</textarea>');
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'CSV Importado com Sucesso!');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
						} </script></div>
	EOT;
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo  $processado;
					}
				}
			}
		}
			
			
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
			
			
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
	break;

	/**
	 * Form Principal
	 * 
	 */
	case 'imp005FormNaoLiberarTelaAnalise':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T295';
		$form_campo_pk = 'T295_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp005FormNaoLiberarBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp005/form/imp005FormNaoLiberarTelaAnalise/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T295_Liberou_Alcada_Observacao_Nao_Liberado' => array('name' => 'T295_Liberou_Alcada_Observacao_Nao_Liberado', 'label' => '* Motivo', 'type' => 'textarea', 'length' => array(14,50)),
					'T295_Id' => array('name' => 'T224_Id', 'value' => @$r_T295_Id, 'label' => 'Observação', 'type' => 'hidden', 'length' => array(50,50)),
					'divReload' => array('name' => 'divReload', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				)
			)
		);
        $form = new Formulario('imp005FormNaoLiberarTelaAnalise');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {

				$retorno = $IMP005->imp005NaoLiberarPedido($form);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					//echo gGeraAlertMsg('Operação OK', "Ordem de compra negada com Sucesso");
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							var buttons = {
								"Fechar": function() {
									$(this).dialog("destroy").remove();
									fecharJanela('{$g['divId']}');
									fecharJanela('{$g['divIdParent']}');
									divRefresh('{$form->campoValorEnviado('divReload')}');
								}
							}
                            dialogConfirm('Análise PO', 'PO negada com sucesso!', buttons);
						} </script></div>
EOT;
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk,"Você deseja negar este ordem de compra?");

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="imp005FormNaoLiberarBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

}
