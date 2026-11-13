<?php   
namespace hardness;
require_once('prog/imp/imp003/form_func-js.php');  
//Classes
require_once("bibliotecas/classes/IMP003.php");
$IMP003 = new IMP003();
    
switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 21/11/2011  
	*/
	case 'imp003FormDi':
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

		$clientePorEmpresa = '';
		if($g['C031']['separarClientePorEmpresa'] == 'S'){
			$clientePorEmpresa = "AND (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T075';
		$form_campo_pk = 'T075_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp003FormDiBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormDi/';

        $form_editor = array(
			'geral' => '{"T075_Numero_DI":{"top":6.96875,"left":86.40625,"width":104,"hidden":0},"T075_Ordem_Compra":{"top":7,"left":367.015625,"width":120,"hidden":0},"T075_CNPJ_Importador":{"top":6.96875,"left":710.09375,"width":135,"hidden":0},"T075_D024_Id":{"top":30,"left":82.953125,"width":410,"hidden":0},"T075_UF_Importador":{"top":30,"left":803.828125,"width":63,"hidden":0},"T075_Codigo_Fabricante":{"top":30,"left":1017.96875,"width":232,"hidden":1},"T075_D006_Id":{"top":52.984375,"left":110.859375,"width":176,"hidden":0},"T075_C007_Id":{"top":53,"left":397.625,"width":127,"hidden":0},"T075_Flag_Transporte_DI":{"top":52.984375,"left":598.859375,"width":240,"hidden":0},"T075_UF_Desembaraco":{"top":52.984375,"left":1202.984375,"width":63,"hidden":1},"T075_D119_Id":{"top":75.984375,"left":74.4375,"width":176,"hidden":0},"T075_D118_Id":{"top":76,"left":415.703125,"width":127,"hidden":0},"T075_Flag_Tipo_Intermedio_DI":{"top":75.984375,"left":593.203125,"width":240,"hidden":0},"T075_Local_Desembaraco":{"top":75.984375,"left":1015.984375,"width":232,"hidden":1},"T075_Data_Emissao_DI":{"top":98.984375,"left":70.9375,"width":104,"hidden":0},"T075_Data_Embarque":{"top":99,"left":396.96875,"width":120,"hidden":0},"T075_Numero_Drawback_Importacao":{"top":98.984375,"left":650.140625,"width":232,"hidden":0},"T075_Data_Desembaraco":{"top":98.984375,"left":1195,"width":72,"hidden":1},"T075_ETA_Data":{"top":121.984375,"left":93.4375,"width":104,"hidden":0},"T075_ETA_Hora":{"top":122,"left":396.59375,"width":120,"hidden":0},"T075_Siscoserv_RAS":{"top":121.984375,"left":627.859375,"height":108,"width":232,"hidden":0},"T075_Codigo_Exportador":{"top":121.984375,"left":1150.234375,"width":232,"hidden":1},"T075_Cotacao_ME":{"top":145,"left":79,"width":104,"hidden":0},"T075_Valor_SISCOMEX":{"top":145,"left":345.28125,"width":120,"hidden":0},"T075_Data_Licenca_Importacao":{"top":145,"left":1174.40625,"width":103,"hidden":1},"T075_Valor_Frete_Incluso":{"top":167.984375,"left":31.78125,"width":104,"hidden":0},"T075_Valor_Frete":{"top":168,"left":371.09375,"width":120,"hidden":0},"T075_Valor_Seguro":{"top":190.984375,"left":57.421875,"width":104,"hidden":0},"T075_Valor_Despesa":{"top":191,"left":366.4375,"width":120,"hidden":0},"T075_Aliquota_ICMS":{"top":190.984375,"left":1185.53125,"width":115,"hidden":1},"T075_Valor_THC":{"top":213.984375,"left":73.453125,"width":104,"hidden":0},"T075_Valor_ICMS":{"top":214,"left":372.53125,"width":120,"hidden":0},"T075_Valor_Imposto_Importacao":{"top":236.984375,"left":88.828125,"width":104,"hidden":0},"T075_Valor_IPI":{"top":237,"left":385.890625,"width":120,"hidden":0},"T075_Siscoserv_RP":{"top":236.984375,"left":634.625,"height":108,"width":231,"hidden":0},"T075_Aliquota_ICMS_Presumido":{"top":236.984375,"left":1257.890625,"width":115,"hidden":1},"T075_Aliquota_ICMS_Credito_Presumido":{"top":236.984375,"left":1315.375,"width":115,"hidden":1},"T075_Valor_PIS":{"top":260,"left":78.34375,"width":104,"hidden":0},"T075_Valor_COFINS":{"top":260,"left":360.640625,"width":120,"hidden":0},"T075_Percentual_Reducao_ICMS":{"top":260,"left":1090.4375,"width":115,"hidden":1},"T075_Valor_VMLE":{"top":282.984375,"left":91.25,"width":104,"hidden":0},"T075_Valor_VMLD":{"top":283,"left":394.28125,"width":120,"hidden":0},"T075_Peso_Bruto":{"top":305.984375,"left":67.890625,"width":104,"hidden":0},"T075_Peso_Liquido":{"top":306,"left":362.234375,"width":120,"hidden":0},"T075_Percentual_Reducao_ICMS_Presumido":{"top":305.984375,"left":970.8125,"width":115,"hidden":1},"T075_Data_Previsao":{"top":328.984375,"left":35.5,"width":104,"hidden":0},"T075_Valor_AFRMM":{"top":329,"left":359.5625,"width":120,"hidden":0},"T075_Cotacao_CC":{"top":328.984375,"left":1307.390625,"width":115,"hidden":1},"T075_Codigo_URF_Entrada":{"top":351.984375,"left":78.296875,"width":104,"hidden":0},"T075_Codigo_URF_Despacho":{"top":352,"left":370.9375,"width":120,"hidden":0},"T075_Codigo_Moeda":{"top":375,"left":85.265625,"width":105,"hidden":0},"T075_Codigo_Incoterms":{"top":375,"left":355.578125,"width":120,"hidden":0},"T075_Condicao_Mercadoria":{"top":397.984375,"left":18.9375,"width":113,"hidden":0},"T075_Operacao_Enquadramento_Material_Usado":{"top":398,"left":312.46875,"width":120,"hidden":0},"T075_Aliquota_ICMS_PIS_COFINS":{"top":397.984375,"left":1072.125,"width":115,"hidden":1},"T075_Enquadramento_Material_Usado":{"top":420.984375,"left":18.21875,"width":176,"hidden":0},"T075_Orgao_Desembaraco":{"top":421,"left":342.40625,"width":120,"hidden":0},"T075_Data_Inicio_Demurrage":{"top":443.984375,"left":31.328125,"width":104,"hidden":0}}',
        );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T075_Numero_DI' => array('name' => 'T075_Numero_DI', 'label' => 'Número DI', 'type' => 'text', 'length' => array(15,255)),
					'T075_Ordem_Compra' => array('name' => 'T075_Ordem_Compra', 'label' => 'Purchase Order', 'type' => 'text', 'length' => array(15,255), 'style'=>'float:right;', 'align'=>'right'),
					'T075_D024_Id' => array('name' => 'T075_D024_Id', 'label' => 'Fornecedor', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Fornecedor="S" and D024_Flag_Ativo="S" '.$clientePorEmpresa.' order by D024_Nome_Empresa','(%1) %2 [%3/%4] - %5 Tel:(%7)%8'),'length' => array(70,120)),
					'T075_D006_Id' => array('name' => 'T075_D006_Id', 'label' => 'CFOP', 'type' => 'select', 
						'select' => gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao','where D006_Flag_Inativo!="S" and D006_Flag_Estado="I" order by D006_Codigo_CFOP',true)
					),
					'T075_C007_Id' => array('name' => 'T075_C007_Id', 'label' => 'Usuário', 'type' => 'select','style'=>'float:right;', 'align'=>'right', 
						'select' => gGeraSelect('C007','C007_Id','C007_Primeiro_Nome','',true,$g['usuarioAtual'])
					),
					'T075_Numero_DI' => array('name' => 'T075_Numero_DI', 'label' => 'Número DI', 'type' => 'text', 'length' => array(15,255)),
					'T075_D119_Id' => array('name' => 'T075_D119_Id', 'label' => 'Despachante', 'type' => 'select', 'length' => array(1,255), 'select' => 
							//gGeraSelect('D024', 'D024_Id', 'D024_Nome_Empresa', "WHERE D024_D046_Id=8 order by D024_Nome_Empresa", true)
							gGeraSelect('D119', 'D119_Id', 'D119_Nome', "order by D119_Nome", true)
					),
					'T075_D118_Id' => array('name' => 'T075_D118_Id', 'label' => 'Porto', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'length' => array(1,255), 'select' => 
							gGeraSelect('D118', 'D118_Id', 'D118_Nome', "order by D118_Nome", true)
					),
					'T075_Data_Emissao_DI' => array('name' => 'T075_Data_Emissao_DI', 'label' => 'Data Emissão', 'type' => 'data', 'length' => array(15,255)),
					'T075_Data_Embarque' => array('name' => 'T075_Data_Embarque', 'label' => 'Data ETD', 'type' => 'data', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right'),
					'T075_Data_Licenca_Importacao' => array('name' => 'T075_Data_Licensa_Importacao', 'label' => 'Data LI', 'type' => 'data', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right'),
					'T075_ETA_Data' => array('name' => 'T075_ETA_Data', 'label' => 'Data ETA', 'type' => 'data', 'length' => array(15,255)),
					'T075_ETA_Hora' => array('name' => 'T075_ETA_Hora', 'label' => 'Hora ETA', 'type' => 'tempo', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right'),
					'T075_Cotacao_ME' => array('name' => 'T075_cotacao_ME', 'label' => 'Cotação ME', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Valor_SISCOMEX' => array('name' => 'T075_Valor_SISCOMEX', 'label' => 'Valor SISCOMEX R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Valor_Frete_Incluso' => array('name' => 'T075_Valor_Frete_Incluso', 'label' => 'Valor Frete Incluso R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Valor_Frete' => array('name' => 'T075_Valor_Frete', 'label' => 'Valor Frete ME', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Valor_Seguro' => array('name' => 'T075_Valor_Valor_Seguro', 'label' => 'Valor Seguro ME', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Valor_Despesa' => array('name' => 'T075_Valor_Despesa', 'label' => 'Valor Despesas', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Valor_THC' => array('name' => 'T075_Valor_THC', 'label' => 'Valor THC R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
                    'T075_Valor_AFRMM' => array('name' => 'T075_Valor_AFRMM', 'label' => 'Valor AFRMM R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Valor_ICMS' => array('name' => 'T075_Valor_ICMS', 'label' => 'Valor ICMS R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Valor_Imposto_Importacao' => array('name' => 'T075_Valor_Imposto_Importacao', 'label' => 'Valor II R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Valor_IPI' => array('name' => 'T075_Valor_IPI', 'label' => 'Valor IPI R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Valor_PIS' => array('name' => 'T075_Valor_PIS', 'label' => 'Valor PIS R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Valor_COFINS' => array('name' => 'T075_Valor_COFINS', 'label' => 'Valor COFINS R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Valor_VMLE' => array('name' => 'T075_Valor_VMLE', 'label' => 'VMLE ME', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Valor_VMLD' => array('name' => 'T075_Valor_VMLD', 'label' => 'VMLD ME', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Valor_COFINS' => array('name' => 'T075_Valor_COFINS', 'label' => 'Valor COFINS R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Aliquota_ICMS' => array('name' => 'T075_Aliquota_ICMS', 'label' => '% ICMS', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Aliquota_ICMS_Presumido' => array('name' => 'T075_Aliquota_ICMS_Presumido', 'label' => '% ICMS Presumido', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Percentual_Reducao_ICMS' => array('name' => 'T075_Percentual_Reducao_ICMS', 'label' => '% Redução ICMS', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Percentual_Reducao_ICMS_Presumido' => array('name' => 'T075_Percentual_Reducao_ICMS_Presumido', 'label' => '% Redução ICMS Presumido', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Aliquota_ICMS_PIS_COFINS' => array('name' => 'T075_Aliquota_ICMS_PIS_COFINS', 'label' => '% ICMS PIS/COFINS', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Aliquota_ICMS_Credito_Presumido' => array('name' => 'T075_Aliquota_ICMS_Credito_Presumido', 'label' => '% ICMS Credito Presumido', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Peso_Bruto' => array('name' => 'T075_Peso_Bruto', 'label' => 'Peso Bruto KG', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '3', 'casasDecimais' => '4'),
					'T075_Peso_Liquido' => array('name' => 'T075_Peso_Liquido', 'label' => 'Peso Liquido KG', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '3', 'casasDecimais' => '4'),
					'T075_D044_Id' => array('name' => 'T075_D044_Id', 'label' => 'Moeda', 'type' => 'hidden', 'length' => array(1,255), 'select' => 
							gGeraSelect('D044', 'D044_Id', 'D044_Moeda,D044_Simbolo', "order by D044_Moeda", true)
					),
					'T075_Data_Previsao' => array('name' => 'T075_Data_Previsao', 'label' => 'Previsão de Chegada', 'type' => 'data', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right'),
					'T075_C004_Id' => array('name' => 'T075_C004_Id', 'label' => 'empresaAtual', 'type' => 'hidden', 'value'=>$g['empresaAtual']),

					'T075_Local_Desembaraco' => array('name' => 'T075_Local_Desembaraco', 'label' => 'Local Desembaraço', 'type' => 'text', 'length' => array(70,90)),
					'T075_Data_Desembaraco' => array('name' => 'T075_Data_Desembaraco', 'label' => 'Data Desembaraço', 'type' => 'data', 'length' => array(15,15)),
					'T075_UF_Desembaraco' => array('name' => 'T075_UF_Desembaraco', 'label' => 'UF Desembaraço', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
						gGeraSelect('D018', 'D018_UF', 'D018_UF', 'order by D018_UF,D018_Estado', true)
					),
					'T075_Codigo_Exportador' => array('name' => 'T075_Codigo_Exportador', 'label' => 'Código Exportador', 'type' => 'text', 'length' => array(70,90)),
					'T075_Codigo_Fabricante' => array('name' => 'T075_Codigo_Fabricante', 'label' => 'Código do Fabricante<br>Estrangeiro', 'type' => 'text', 'length' => array(70,90)),
					'T075_Flag_Transporte_DI' => array('name' => 'T075_Flag_Transporte_DI', 'label'=>'Forma de transporte', 'type' => 'select', 'select' =>
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
					'T075_Flag_Tipo_Intermedio_DI' => array('name' => 'T075_Flag_Tipo_Intermedio_DI', 'label' => 'Forma de importação', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value'=>'', 'selected'=>true),
							array('title' => 'Importação por conta própria', 'value' => '1'),
							array('title' => 'Importação por conta e ordem', 'value' => '2'),
							array('title' => 'Importação por encomenda', 'value' => '3'),
						)
					),
					'T075_CNPJ_Importador' => array('name' => 'T075_CNPJ_Importador', 'label' => 'CNPJ Importador', 'type' => 'cnpj'),
					'T075_UF_Importador' => array('name' => 'T075_UF_Importador', 'label' => 'UF Importador', 'type' => 'select', 
						'select' => gGeraSelect('D018', 'D018_UF', 'D018_UF', 'order by D018_UF,D018_Estado', true)
					),
					'T075_Numero_Drawback_Importacao' => array('name' => 'T075_Numero_Drawback_Importacao', 'label' => 'Drawback', 'type' => 'text'),
					'T075_Cotacao_CC' => array('name' => 'T075_Cotacao_CC', 'label' => 'Cotação Contrato de Câmbio', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Codigo_URF_Entrada' => array('name' => 'T075_Codigo_URF_Entrada', 'label' => 'URF Entrada', 'type' => 'text', 'length' => array(70,90)),
					'T075_Codigo_URF_Despacho' => array('name' => 'T075_Codigo_URF_Despacho', 'label' => 'URF Despacho', 'type' => 'text', 'length' => array(70,90)),
					'T075_Codigo_Moeda' => array('name' =>  'T075_Codigo_Moeda', 'label' => 'Código ME', 'type' => 'text', 'length' => array(70,90)),
					'T075_Codigo_Incoterms' => array('name' => 'T075_Codigo_Incoterms', 'label' => 'Código Incoterms', 'type' => 'text', 'length' => array(70,90)),
					'T075_Condicao_Mercadoria' => array('name' => 'T075_Condicao_Mercadoria', 'label' => 'Condição da Mercadoria', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Novo', 'value' => 'NOVO'),
							array('title' => 'Usado', 'value' => 'USADO'),
						)

					), 
					'T075_Enquadramento_Material_Usado' => array('name' => 'T075_Enquadramento_Material_Usado', 'label' => 'Enquadramento Material', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Admissão temporária', 'value' => 'ADMISSÃO TEMPORÁRIA'),
							array('title' => 'Nacionalização', 'value' => 'NACIONALIZAÇÃO'),
						)
					), 
					'T075_Operacao_Enquadramento_Material_Usado' => array('name' => 'T075_Operacao_Enquadramento_Material_Usado', 'label' => 'Operação Enquadramento ', 'type' => 'text', 'length' => array(70,90)),
					'T075_Data_Inicio_Demurrage' => array('name' => 'T075_Data_Inicio_Demurrage', 'label' => 'Data inicio demurrage', 'type' => 'data', 'length' => array(15,255)),
					'T075_Siscoserv_RAS' => array('name' => 'T075_Siscoserv_RAS', 'label'=>'Siscoserv RAS', 'type' => 'textarea', 'length' => array(4,50)),
					'T075_Siscoserv_RP' => array('name' => 'T075_Siscoserv_RP', 'label'=>'Siscoserv RP', 'type' => 'textarea', 'length' => array(4,50)),
					'T075_Orgao_Desembaraco' => array('name' => 'T075_Orgao_Desembaraco', 'label'=>'Orgão Desembaraço', 'type' => 'text'),
					'T075_Observacao_NF' => array('name' => 'T075_Observacao_NF ', 'label' => 'Obs Nota Fiscal', 'type' => 'hidden'),
					'T075_Modelo_Calculos' => array('name' => 'T075_Modelo_Calculos ', 'label' => 'Modelo', 'type' => 'hidden','value'=> $g['C031']['modeloCalculosDI']),
					'T075_Valor_Outros_Acrescimos' => array('name' => 'T075_Valor_Outros_Acrescimos', 'label' => 'Valor Outros Acrésc. ME', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
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
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$IMP003->atualizaOC($r_acaoId);
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
						$IMP003->atualizaOC($acaoId);
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Alterar DI - ID: ' + '{$acaoId}', '/imp/imp003/content/imp003ContentDiIncluir/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [900,550]);
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

	case 'imp003FormDINovo':
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
		$form_tabela = 'T075';
		$form_campo_pk = 'T075_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp003FormDiBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormDINovo/';

        $form_editor = array(
			'geral' => '{"T075_Numero_DI":{"top":6.96875,"left":86.40625,"width":153,"hidden":0},"T075_Ordem_Compra":{"top":7,"left":367.015625,"width":120,"hidden":0},"T075_CNPJ_Importador":{"top":7,"left":710.09375,"width":136,"hidden":0},"T075_D024_Id":{"top":30,"left":82.953125,"width":410,"hidden":0},"T075_UF_Importador":{"top":30,"left":787.828125,"width":79,"hidden":0},"T075_Codigo_Fabricante":{"top":30,"left":1017.96875,"width":232,"hidden":1},"T075_D006_Id":{"top":52.984375,"left":110.859375,"width":159,"hidden":0},"T075_C007_Id":{"top":53,"left":397.625,"width":127,"hidden":0},"T075_Flag_Transporte_DI":{"top":53,"left":598.859375,"width":239,"hidden":0},"T075_UF_Desembaraco":{"top":52.984375,"left":1202.984375,"width":63,"hidden":1},"T075_D118_Id_Origem":{"top":76,"left":74.6875,"width":159,"hidden":0},"T075_D118_Id":{"top":76,"left":376.671875,"width":127,"hidden":0},"T075_Flag_Tipo_Intermedio_DI":{"top":76,"left":593.203125,"width":239,"hidden":0},"T075_Local_Desembaraco":{"top":75.984375,"left":1015.984375,"width":232,"hidden":1},"T075_Data_Emissao_DI":{"top":98.984375,"left":70.9375,"width":152,"hidden":0},"T075_Data_Embarque":{"top":99,"left":396.96875,"width":120,"hidden":0},"T075_Numero_Drawback_Importacao":{"top":99,"left":650.140625,"width":232,"hidden":0},"T075_Data_Desembaraco":{"top":98.984375,"left":1195,"width":72,"hidden":1},"T075_ETA_Data":{"top":121.984375,"left":93.4375,"width":152,"hidden":0},"T075_ETA_Hora":{"top":122,"left":396.59375,"width":120,"hidden":0},"T075_Siscoserv_RAS":{"top":122,"left":627.859375,"height":108,"width":232,"hidden":0},"T075_Codigo_Exportador":{"top":121.984375,"left":1150.234375,"width":232,"hidden":1},"T075_D119_Id":{"top":145,"left":74.4375,"width":159,"hidden":0},"T075_Data_Inicio_Demurrage":{"top":145,"left":335.328125,"width":120,"hidden":0},"T075_Data_Licenca_Importacao":{"top":145,"left":1174.40625,"width":103,"hidden":1},"T075_Valor_ICMS":{"top":168,"left":68.53125,"width":152,"hidden":0},"T075_Cotacao_ME":{"top":168,"left":383,"width":120,"hidden":0},"T075_Valor_Imposto_Importacao":{"top":191,"left":88.828125,"width":152,"hidden":0},"T075_Valor_PIS":{"top":191,"left":382.34375,"width":120,"hidden":0},"T075_Valor_IPI":{"top":214,"left":81.890625,"width":152,"hidden":0},"T075_Valor_COFINS":{"top":214,"left":360.640625,"width":120,"hidden":0},"T075_Valor_VMLE":{"top":237,"left":91.25,"width":152,"hidden":0},"T075_Valor_VMLD":{"top":237,"left":394.28125,"width":120,"hidden":0},"T075_Siscoserv_RP":{"top":237,"left":634.625,"height":154,"width":231,"hidden":0},"T075_Peso_Bruto":{"top":260,"left":67.890625,"width":152,"hidden":0},"T075_Peso_Liquido":{"top":260,"left":362.234375,"width":120,"hidden":0},"T075_Data_Previsao":{"top":283,"left":35.5,"width":152,"hidden":0},"T075_Orgao_Desembaraco":{"top":283,"left":342.40625,"width":120,"hidden":0},"T075_Codigo_URF_Entrada":{"top":306,"left":78.296875,"width":152,"hidden":0},"T075_Codigo_URF_Despacho":{"top":306,"left":370.9375,"width":120,"hidden":0},"T075_Codigo_Moeda":{"top":329,"left":85.265625,"width":152,"hidden":0},"T075_Codigo_Incoterms":{"top":329,"left":355.578125,"width":120,"hidden":0},"T075_Cotacao_CC":{"top":328.984375,"left":1307.390625,"width":115,"hidden":1},"T075_Condicao_Mercadoria":{"top":352,"left":18.9375,"width":159,"hidden":0},"T075_Operacao_Enquadramento_Material_Usado":{"top":352,"left":312.46875,"width":120,"hidden":0},"T075_Enquadramento_Material_Usado":{"top":375,"left":18.21875,"width":431,"hidden":0}}',
        );

		$clientePorEmpresa = '';
		if($g['C031']['separarClientePorEmpresa'] == 'S'){
			$clientePorEmpresa = "AND (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
		}
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T075_Numero_DI' => array('name' => 'T075_Numero_DI', 'label' => 'Número DI', 'type' => 'text', 'length' => array(15,255)),
					'T075_Ordem_Compra' => array('name' => 'T075_Ordem_Compra', 'label' => 'Purchase Order', 'type' => 'text', 'length' => array(15,255), 'style'=>'float:right;', 'align'=>'right'),
					'T075_D024_Id' => array('name' => 'T075_D024_Id', 'label' => 'Fornecedor', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Fornecedor="S" and D024_Flag_Ativo="S" '.$clientePorEmpresa.' order by D024_Nome_Empresa','(%1) %2 [%3/%4] - %5 Tel:(%7)%8'),'length' => array(70,120)),
					'T075_D006_Id' => array('name' => 'T075_D006_Id', 'label' => 'CFOP', 'type' => 'select', 
						'select' => gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao','where D006_Flag_Inativo!="S" and D006_Flag_Estado="I" order by D006_Codigo_CFOP',true)
					),
					'T075_C007_Id' => array('name' => 'T075_C007_Id', 'label' => 'Usuário', 'type' => 'select','style'=>'float:right;', 'align'=>'right', 
						'select' => gGeraSelect('C007','C007_Id','C007_Primeiro_Nome','',true,$g['usuarioAtual'])
					),
					'T075_Numero_DI' => array('name' => 'T075_Numero_DI', 'label' => 'Número DI', 'type' => 'text', 'length' => array(15,255)),
					'T075_D119_Id' => array('name' => 'T075_D119_Id', 'label' => 'Despachante', 'type' => 'select', 'length' => array(1,255), 'select' => 
							gGeraSelect('D119', 'D119_Id', 'D119_Nome', "order by D119_Nome", true)
					),
					'T075_D118_Id_Origem' => array('name' => 'T075_D118_Id_Origem', 'label' => 'Porto origem', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'length' => array(1,255), 'select' => 
							gGeraSelect('D118', 'D118_Id', 'D118_Nome', "order by D118_Nome", true)
					),
					'T075_D118_Id' => array('name' => 'T075_D118_Id', 'label' => 'Porto destino', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'length' => array(1,255), 'select' => 
							gGeraSelect('D118', 'D118_Id', 'D118_Nome', "order by D118_Nome", true)
					),
					'T075_Data_Emissao_DI' => array('name' => 'T075_Data_Emissao_DI', 'label' => 'Data Emissão', 'type' => 'data', 'length' => array(15,255)),
					'T075_Data_Embarque' => array('name' => 'T075_Data_Embarque', 'label' => 'Data ETD', 'type' => 'data', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right'),
					'T075_Data_Licenca_Importacao' => array('name' => 'T075_Data_Licensa_Importacao', 'label' => 'Data LI', 'type' => 'data', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right'),
					'T075_ETA_Data' => array('name' => 'T075_ETA_Data', 'label' => 'Data ETA', 'type' => 'data', 'length' => array(15,255)),
					'T075_ETA_Hora' => array('name' => 'T075_ETA_Hora', 'label' => 'Hora ETA', 'type' => 'tempo', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right'),
					'T075_Cotacao_ME' => array('name' => 'T075_cotacao_ME', 'label' => 'Cotação ME', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T075_Valor_SISCOMEX' => array('name' => 'T075_Valor_SISCOMEX', 'label' => 'Valor SISCOMEX R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					//'T075_Valor_Frete_Incluso' => array('name' => 'T075_Valor_Frete_Incluso', 'label' => 'Valor Frete Incluso R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T075_Valor_Frete' => array('name' => 'T075_Valor_Frete', 'label' => 'Valor Frete ME', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					//'T075_Valor_Seguro' => array('name' => 'T075_Valor_Valor_Seguro', 'label' => 'Valor Seguro ME', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					//'T075_Valor_Despesa' => array('name' => 'T075_Valor_Despesa', 'label' => 'Valor Despesas', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					//'T075_Valor_THC' => array('name' => 'T075_Valor_THC', 'label' => 'Valor THC R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
                    //'T075_Valor_AFRMM' => array('name' => 'T075_Valor_AFRMM', 'label' => 'Valor AFRMM R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Valor_ICMS' => array('name' => 'T075_Valor_ICMS', 'label' => 'Valor ICMS R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Valor_Imposto_Importacao' => array('name' => 'T075_Valor_Imposto_Importacao', 'label' => 'Valor II R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Valor_IPI' => array('name' => 'T075_Valor_IPI', 'label' => 'Valor IPI R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Valor_PIS' => array('name' => 'T075_Valor_PIS', 'label' => 'Valor PIS R$', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Valor_COFINS' => array('name' => 'T075_Valor_COFINS', 'label' => 'Valor COFINS R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Valor_VMLE' => array('name' => 'T075_Valor_VMLE', 'label' => 'VMLE ME', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Valor_VMLD' => array('name' => 'T075_Valor_VMLD', 'label' => 'VMLD ME', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Valor_COFINS' => array('name' => 'T075_Valor_COFINS', 'label' => 'Valor COFINS R$', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '4'),
					'T075_Peso_Bruto' => array('name' => 'T075_Peso_Bruto', 'label' => 'Peso Bruto KG', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '3', 'casasDecimais' => '4'),
					'T075_Peso_Liquido' => array('name' => 'T075_Peso_Liquido', 'label' => 'Peso Liquido KG', 'type' => 'moeda', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right', 'casasDecimais' => '3', 'casasDecimais' => '4'),
					'T075_D044_Id' => array('name' => 'T075_D044_Id', 'label' => 'Moeda', 'type' => 'hidden', 'length' => array(1,255), 'select' => 
							gGeraSelect('D044', 'D044_Id', 'D044_Moeda,D044_Simbolo', "order by D044_Moeda", true)
					),
					'T075_Data_Previsao' => array('name' => 'T075_Data_Previsao', 'label' => 'Previsão de Chegada', 'type' => 'data', 'length' => array(15,50), 'style'=>'float:right;', 'align'=>'right'),
					'T075_C004_Id' => array('name' => 'T075_C004_Id', 'label' => 'empresaAtual', 'type' => 'hidden', 'value'=>$g['empresaAtual']),

					'T075_Local_Desembaraco' => array('name' => 'T075_Local_Desembaraco', 'label' => 'Local Desembaraço', 'type' => 'text', 'length' => array(70,90)),
					'T075_Data_Desembaraco' => array('name' => 'T075_Data_Desembaraco', 'label' => 'Data Desembaraço', 'type' => 'data', 'length' => array(15,15)),
					'T075_UF_Desembaraco' => array('name' => 'T075_UF_Desembaraco', 'label' => 'UF Desembaraço', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
						gGeraSelect('D018', 'D018_UF', 'D018_UF', 'order by D018_UF,D018_Estado', true)
					),
					'T075_Codigo_Exportador' => array('name' => 'T075_Codigo_Exportador', 'label' => 'Código Exportador', 'type' => 'text', 'length' => array(70,90)),
					'T075_Codigo_Fabricante' => array('name' => 'T075_Codigo_Fabricante', 'label' => 'Código do Fabricante<br>Estrangeiro', 'type' => 'text', 'length' => array(70,90)),
					'T075_Flag_Transporte_DI' => array('name' => 'T075_Flag_Transporte_DI', 'label'=>'Forma de transporte', 'type' => 'select', 'select' =>
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
					'T075_Flag_Tipo_Intermedio_DI' => array('name' => 'T075_Flag_Tipo_Intermedio_DI', 'label' => 'Forma de importação', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value'=>'', 'selected'=>true),
							array('title' => 'Importação por conta própria', 'value' => '1'),
							array('title' => 'Importação por conta e ordem', 'value' => '2'),
							array('title' => 'Importação por encomenda', 'value' => '3'),
						)
					),
					'T075_CNPJ_Importador' => array('name' => 'T075_CNPJ_Importador', 'label' => 'CNPJ Importador', 'type' => 'cnpj'),
					'T075_UF_Importador' => array('name' => 'T075_UF_Importador', 'label' => 'UF Importador', 'type' => 'select', 
						'select' => gGeraSelect('D018', 'D018_UF', 'D018_UF', 'order by D018_UF,D018_Estado', true)
					),
					'T075_Numero_Drawback_Importacao' => array('name' => 'T075_Numero_Drawback_Importacao', 'label' => 'Drawback', 'type' => 'text'),
					'T075_Cotacao_CC' => array('name' => 'T075_Cotacao_CC', 'label' => 'Cotação Contrato de Câmbio', 'type' => 'moeda', 'length' => array(15,50), 'casasDecimais' => '4'),
					'T075_Codigo_URF_Entrada' => array('name' => 'T075_Codigo_URF_Entrada', 'label' => 'URF Entrada', 'type' => 'text', 'length' => array(70,90)),
					'T075_Codigo_URF_Despacho' => array('name' => 'T075_Codigo_URF_Despacho', 'label' => 'URF Despacho', 'type' => 'text', 'length' => array(70,90)),
					'T075_Codigo_Moeda' => array('name' =>  'T075_Codigo_Moeda', 'label' => 'Código ME', 'type' => 'text', 'length' => array(70,90)),
					'T075_Codigo_Incoterms' => array('name' => 'T075_Codigo_Incoterms', 'label' => 'Código Incoterms', 'type' => 'text', 'length' => array(70,90)),
					'T075_Condicao_Mercadoria' => array('name' => 'T075_Condicao_Mercadoria', 'label' => 'Condição da Mercadoria', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Novo', 'value' => 'NOVO'),
							array('title' => 'Usado', 'value' => 'USADO'),
						)

					), 
					'T075_Enquadramento_Material_Usado' => array('name' => 'T075_Enquadramento_Material_Usado', 'label' => 'Enquadramento Material', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Admissão temporária', 'value' => 'ADMISSÃO TEMPORÁRIA'),
							array('title' => 'Nacionalização', 'value' => 'NACIONALIZAÇÃO'),
						)
					), 
					'T075_Operacao_Enquadramento_Material_Usado' => array('name' => 'T075_Operacao_Enquadramento_Material_Usado', 'label' => 'Operação Enquadramento ', 'type' => 'text', 'length' => array(70,90)),
					'T075_Data_Inicio_Demurrage' => array('name' => 'T075_Data_Inicio_Demurrage', 'label' => 'Data inicio demurrage', 'type' => 'data', 'length' => array(15,255)),
					'T075_Siscoserv_RAS' => array('name' => 'T075_Siscoserv_RAS', 'label'=>'Siscoserv RAS', 'type' => 'textarea', 'length' => array(4,50)),
					'T075_Siscoserv_RP' => array('name' => 'T075_Siscoserv_RP', 'label'=>'Siscoserv RP', 'type' => 'textarea', 'length' => array(4,50)),
					'T075_Orgao_Desembaraco' => array('name' => 'T075_Orgao_Desembaraco', 'label'=>'Orgão Desembaraço', 'type' => 'text'),
					'T075_Observacao_NF' => array('name' => 'T075_Observacao_NF ', 'label' => 'Obs Nota Fiscal', 'type' => 'hidden'),
					'T075_Modelo_Calculos' => array('name' => 'T075_Modelo_Calculos ', 'label' => 'Modelo', 'type' => 'hidden','value'=> $g['C031']['modeloCalculosDI']),
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
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$IMP003->atualizaOC($r_acaoId);
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
						$IMP003->atualizaOC($acaoId);
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Alterar DI - ID: ' + '{$acaoId}', '/imp/imp003/content/imp003ContentDiIncluir/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [900,550]);
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
	
	
	case 'imp003FormTotal':
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
		$form_tabela = 'T075';
		$form_campo_pk = 'T075_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp003FormTotalDiBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormTotal/';

        $form_editor = array(
             'geral' => '{"Valor_Total_Custo":{"top":7,"left":15.375,"width":103,"hidden":0},"Valor_Base_Imposto_Importacao":{"top":7,"left":317.4375,"width":103,"hidden":0},"dif_Valor_ME":{"top":7,"left":605.234375,"width":103,"hidden":0},"Cotacao_ME":{"top":30,"left":47,"width":103,"hidden":0},"Valor_Imposto_Importacao":{"top":30,"left":344.5,"width":103,"hidden":0},"dif_Valor_Reais":{"top":30,"left":594.171875,"width":103,"hidden":0},"Valor_Total_Custo_Reais":{"top":53,"left":18.28125,"width":103,"hidden":0},"Valor_Base_PIS":{"top":53,"left":306.96875,"width":103,"hidden":0},"dif_Valor_Frete":{"top":53,"left":624.40625,"width":103,"hidden":0},"Valor_Total_Frete":{"top":76,"left":81.796875,"width":103,"hidden":0},"Valor_PIS":{"top":76,"left":334.015625,"width":103,"hidden":0},"dif_Valor_THC":{"top":76,"left":627.875,"width":103,"hidden":0},"Valor_Total_THC":{"top":99,"left":85.03125,"width":104,"hidden":0},"Valor_Base_COFINS":{"top":99,"left":285.265625,"width":103,"hidden":0},"dif_Sub_Total":{"top":99,"left":605.421875,"width":103,"hidden":0},"Valor_Sub_Total":{"top":122,"left":44.578125,"width":103,"hidden":0},"Valor_COFINS":{"top":122,"left":312.3125,"width":103,"hidden":0},"dif_Valor_SISCOMEX":{"top":122,"left":601.40625,"width":103,"hidden":0},"Valor_Base_IPI":{"top":145,"left":310.5,"width":103,"hidden":0},"dif_AFRMM":{"top":145,"left":609.984375,"width":103,"hidden":0},"Valor_Total_Siscomex":{"top":145,"left":58.78125,"width":104,"hidden":0},"Valor_IPI":{"top":168,"left":337.5625,"width":103,"hidden":0},"dif_Valor_II":{"top":168,"left":643.234375,"width":103,"hidden":0},"Valor_AFRMM":{"top":168,"left":39.25,"width":103,"hidden":0},"Valor_Base_ICMS":{"top":191,"left":297.140625,"width":103,"hidden":0},"dif_Valor_PIS":{"top":190.984375,"left":632.765625,"width":103,"hidden":0},"Valor_Base_ICMS_Normal":{"top":214,"left":258.296875,"width":103,"hidden":0},"dif_Valor_COFINS":{"top":213.96875,"left":617.953125,"width":103,"hidden":0},"T055_Valor_Total_Preco_Nota_Fiscal":{"top":191,"left":59.6875,"width":103,"hidden":0},"Valor_ICMS":{"top":237,"left":324.203125,"width":103,"hidden":0},"dif_Valor_IPI":{"top":236.96875,"left":636.296875,"width":103,"hidden":0},"T055_Valor_Total_Custo_Final":{"top":214,"left":50.671875,"width":103,"hidden":0},"Valor_ICMS_Presumido":{"top":260,"left":268.5625,"width":103,"hidden":0},"dif_Valor_ICMS_Pagar":{"top":259.96875,"left":593,"width":103,"hidden":1},"T055_Quantidade":{"top":260,"left":49.21875,"width":103,"hidden":0},"Valor_ICMS_Pagar":{"top":283,"left":278.5625,"width":103,"hidden":0},"dif_Peso_Liquido":{"top":282.96875,"left":589.046875,"width":103,"hidden":0},"QuantidadeVendido":{"top":283,"left":1.109375,"width":103,"hidden":0},"T055_Valor_Total_Despesas_Aduaneira":{"top":329,"left":272.6875,"width":104,"hidden":0},"T055_Peso_Kg_Liquido_Unitario":{"top":306,"left":41.578125,"width":103,"hidden":0},"T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal":{"top":352,"left":273.421875,"width":104,"hidden":0},"TotalVendidoLiquido":{"top":329,"left":0.5,"width":103,"hidden":0},"T055_Valor_Total_Despesas":{"top":375,"left":295.453125,"width":103,"hidden":0},"TotalVendidoBruto":{"top":352,"left":10.203125,"width":103,"hidden":0}}'
        );

 		$T075=mysql_query("SELECT *
		                     FROM T075
		                    WHERE T075_Id='$r_acaoId'");
	    $mT075=mysql_fetch_array($T075);

		if($mT075['T075_Modelo_Calculos'] == '2'){

			$T168 = mysql_query("SELECT T168_Valor_Confirmado,
										T168_Tipo_Despesa
								   FROM T168
								  WHERE T168_T075_Id = '{$r_acaoId}'");
			
			$valorTotalTHC		= 0;
			$valorTotalSiscomex = 0;
			$valorTotalAFRMM    = 0;
			$valorTotalFrete    = 0;
			$valorTotalSeguro   = 0;
			$valorTotalDespOutros   = 0;
			$valorTotalDespAcessorias = 0;
			$valorTotalDespAduaneiras = 0;

			while($mT168 = mysql_fetch_array($T168)){

				if($mT168['T168_Tipo_Despesa'] == '1'){
					$valorTotalDespOutros += $mT168['T168_Valor_Confirmado'];
				}

				if($mT168['T168_Tipo_Despesa'] == '2'){
					$valorTotalTHC += $mT168['T168_Valor_Confirmado'];
				}

				if($mT168['T168_Tipo_Despesa'] == '3'){
					$valorTotalSeguro += $mT168['T168_Valor_Confirmado'];
				}

				if($mT168['T168_Tipo_Despesa'] == '4'){
					$valorTotalSiscomex += $mT168['T168_Valor_Confirmado'];
				}

				if($mT168['T168_Tipo_Despesa'] == '5'){
					$valorTotalAFRMM += $mT168['T168_Valor_Confirmado'];
				}

				if($mT168['T168_Tipo_Despesa'] == '6'){
					$valorTotalFrete += $mT168['T168_Valor_Confirmado'];
				}

				if($mT168['T168_Tipo_Despesa'] == '7'){
					$valorTotalDespAcessorias += $mT168['T168_Valor_Confirmado'];
				}

				if($mT168['T168_Tipo_Despesa'] == '8'){
					$valorTotalDespAduaneiras += $mT168['T168_Valor_Confirmado'];
				}

			}

			$mT075['T075_Valor_THC'] 	  = $valorTotalTHC;
			$mT075['T075_Valor_SISCOMEX'] = $valorTotalSiscomex;
			$mT075['T075_Valor_AFRMM']    = $valorTotalAFRMM;
			$mT075['T075_Valor_Seguro']   = $valorTotalSeguro;
			$mT075['T075_Valor_Frete']    = $valorTotalFrete;

		};

		$T055=mysql_query("SELECT SUM(T055_Valor_Total_Custo*$mT075[T075_Cotacao_ME]) T055_Valor_Total_Custo_Reais,
		                          SUM(T055_Valor_Total_Custo) T055_Valor_Total_Custo,
		                          SUM(T055_Valor_Aduaneira) T055_Valor_Aduaneira,
		                          SUM(T055_Valor_Base_Imposto_Importacao) T055_Valor_Base_Imposto_Importacao,
		                          SUM(T055_Valor_Imposto_Importacao) T055_Valor_Imposto_Importacao,
		                          SUM(T055_Valor_Base_IPI) T055_Valor_Base_IPI,
		                          SUM(T055_Valor_IPI) T055_Valor_IPI,
		                          SUM(T055_Valor_Base_PIS) T055_Valor_Base_PIS,
		                          SUM(T055_Valor_PIS) T055_Valor_PIS,
		                          SUM(T055_Valor_Base_COFINS) T055_Valor_Base_COFINS,
		                          SUM(T055_Valor_COFINS) T055_Valor_COFINS,
		                          SUM(T055_Valor_ICMS) T055_Valor_ICMS,
		                          SUM(T055_Valor_ICMS_Presumido) T055_Valor_ICMS_Presumido,
		                          SUM(T055_Valor_ICMS_Pagar) T055_Valor_ICMS_Pagar,
		                          SUM(T055_Peso_Kg_Liquido_Unitario*T055_Quantidade) T055_Peso_Kg_Liquido_Unitario,
		                          SUM(T055_Valor_Total_Custo_Final) T055_Valor_Total_Custo_Final,
		                          SUM(T055_Valor_Base_ICMS_Sem_Reducao) T055_Valor_Base_ICMS_Sem_Reducao,
		                          SUM(T055_Valor_Base_ICMS_Normal) T055_Valor_Base_ICMS_Normal,
								  SUM(T055_Valor_Total_Frete) T055_Valor_Total_Frete,
								  SUM(T055_Valor_Total_Seguro) T055_Valor_Total_Seguro,
		                          SUM(T055_Valor_Total_Preco_Nota_Fiscal) T055_Valor_Total_Preco_Nota_Fiscal,
                                  SUM(T055_Valor_Total_AFRMM) T055_Valor_Total_AFRMM,
                                  IF({$mT075['T075_Modelo_Calculos']} = 2, SUM(T055_Valor_Total_THC),SUM(T055_Valor_Aduaneira)) T055_Valor_Total_THC,
								  IF({$mT075['T075_Modelo_Calculos']} = 2, SUM(T055_Valor_Total_Siscomex),SUM(T055_Valor_Total_Despesas_Aduaneira)) T055_Valor_Total_Siscomex,
		                          SUM(T055_Valor_Total_Despesas) T055_Valor_Total_Despesas,
		                          SUM(T055_Valor_Total_Despesas_Aduaneira) T055_Valor_Total_Despesas_Aduaneira,
		                          SUM(T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal) T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal,
                                  SUM(T055_Quantidade) T055_Quantidade
		                     FROM T055
		                    WHERE T055_T075_Id='{$r_acaoId}'");
	    $mT055=mysql_fetch_array($T055);
	    
		/*
	    //$mT055['T055_Valor_Total_Custo_Reais'] 	  = number_format($mT055['T055_Valor_Total_Custo_Reais'], 4, ',', '.');
	    $mT055['T055_Valor_Total_Custo'] 			  = number_format($mT055['T055_Valor_Total_Custo'], 4, ',', '.');
	    $mT075['T075_Cotacao_ME'] 					  = number_format($mT075['T075_Cotacao_ME'], 4, ',', '.');
	    //$mT055['T055_Valor_Total_Frete'] 			  = number_format($mT055['T055_Valor_Total_Frete'], 4, ',', '.');
	    $mT055['T055_Valor_Aduaneira'] 				  = number_format($mT055['T055_Valor_Aduaneira'], 4, ',', '.');
	    //$mT055['T055_Valor_Imposto_Importacao']     = number_format($mT055['T055_Valor_Imposto_Importacao'], 2, ',', '.');
	  	//$mT055['T055_Valor_IPI'] 					  = number_format($mT055['T055_Valor_IPI'], 4, ',', '.');
	    //$mT055['T055_Valor_PIS'] 					  = number_format($mT055['T055_Valor_PIS'], 4, ',', '.');
	    //$mT055['T055_Valor_COFINS'] 				  = number_format($mT055['T055_Valor_COFINS'], 4, ',', '.');
	    $mT055['T055_Valor_ICMS'] 					  = number_format($mT055['T055_Valor_ICMS'], 4, ',', '.');
	    $mT055['T055_Valor_ICMS_Presumido']			  = number_format($mT055['T055_Valor_ICMS_Presumido'], 4, ',', '.');
	    $mT055['T055_Valor_ICMS_Pagar'] 			  = number_format($mT055['T055_Valor_ICMS_Pagar'], 4, ',', '.');
	    $mT055['T055_Peso_Kg_Liquido_Unitario']       = number_format($mT055['T055_Peso_Kg_Liquido_Unitario'], 4, ',', '.');
	    $mT055['T055_Valor_Total_Preco_Nota_Fiscal']  = number_format($mT055['T055_Valor_Total_Preco_Nota_Fiscal'], 4, ',', '.');
	    $mT055['T055_Valor_Total_Despesas_Aduaneira'] = number_format($mT055['T055_Valor_Total_Despesas_Aduaneira'], 4, ',', '.');
	    $mT055['T055_Valor_Total_Despesas'] 		  = number_format($mT055['T055_Valor_Total_Despesas'], 4, ',', '.');
	    $mT055['T055_Valor_Total_Custo_Final'] 		  = number_format($mT055['T055_Valor_Total_Custo_Final'], 4, ',', '.');
	    $mT055['T055_Valor_Base_ICMS_Sem_Reducao'] 	  = number_format($mT055['T055_Valor_Base_ICMS_Sem_Reducao'], 4, ',', '.');
	    $mT055['T055_Valor_Base_ICMS_Normal'] 		  = number_format($mT055['T055_Valor_Base_ICMS_Normal'], 4, ',', '.');
       // $mT055['T055_Valor_Total_AFRMM']			  = number_format($mT055['T055_Valor_Total_AFRMM'], 4, ',', '.');
        $mT055['T055_Quantidade'] 					  = number_format($mT055['T055_Quantidade'], 4, ',', '.');
		//, 'casasDecimais' => '4'
		*/

		// Definição do Formulário

        $sqlVendido = mysql_query("SELECT SUM(T006_Quantidade) AS Quantidade, 
										  SUM(T006_Valor_Total_Preco) AS Total, 
										  SUM(T006_Valor_Preco_Bruto * T006_Quantidade) AS TotalBruto 
									 FROM T006 
			                    LEFT JOIN T005 ON T005_Id=T006_T005_Id
			                    LEFT JOIN T075 ON T075_Id=T006_T075_Id 
							        WHERE T006_T075_Id = '{$r_acaoId}' 
									  AND T005_Flag_Status != '5' 
									  AND T006_Flag_Status != '3'
									  AND T005_Flag_Status != '7'
									  AND T005_Flag_Status != '8' 
									  AND T005_Flag_Status != '11'");
        $resVendido = mysql_fetch_array($sqlVendido);

		$totalVendido       = $resVendido['Total'];
		$totalVendidoBruto  = $resVendido['TotalBruto'];
        $totalItensVendido  = $resVendido['Quantidade'];

		if($mT075['T075_Modelo_Calculos'] !== '2'){
			$mT075['T075_Valor_Frete'] = $mT075['T075_Valor_Frete'] * $mT075['T075_Cotacao_ME'];
		}

		$valorTotalReais    = $mT075['T075_Valor_VMLE'] * $mT075['T075_Cotacao_ME'];
		$valorSubTotalReais = ($mT075['T075_Valor_VMLE'] * $mT075['T075_Cotacao_ME']) + $mT075['T075_Valor_Frete'] + $mT075['T075_Valor_THC'];

		$mT055['T055_Valor_Sub_Total'] = $mT055['T055_Valor_Total_Custo_Reais']+$mT055['T055_Valor_Total_Frete']+$mT055['T055_Valor_Total_THC'];

		// Calcula as difereças entre os valores
		$dif_Valor_ME 		 = $mT075['T075_Valor_VMLE']     - $mT055['T055_Valor_Total_Custo'];
		$dif_Valor_Reais 	 = $valorTotalReais              - $mT055['T055_Valor_Total_Custo_Reais'];
		$dif_Valor_Sub_Total = $valorSubTotalReais           - $mT055['T055_Valor_Sub_Total'];			
		$dif_Valor_II	     = $mT075['T075_Valor_Imposto_Importacao'] - $mT055['T055_Valor_Imposto_Importacao'];
		$dif_Valor_IPI 		 = $mT075['T075_Valor_IPI']      - $mT055['T055_Valor_IPI'];
		$dif_Valor_PIS 	     = $mT075['T075_Valor_PIS']      - $mT055['T055_Valor_PIS'];
		$dif_Cofins 		 = $mT075['T075_Valor_COFINS']   - $mT055['T055_Valor_COFINS'];
		$dif_Valor_ICMS		 = $mT075['T075_Valor_ICMS']     - $mT055['T055_Valor_ICMS_Pagar'];			
		$dif_Peso_Liquido 	 = $mT075['T075_Peso_Liquido']   - $mT055['T055_Peso_Kg_Liquido_Unitario'];			

		$dif_Valor_Frete 	 = $mT075['T075_Valor_Frete']    - $mT055['T055_Valor_Total_Frete'];
		$dif_Valor_THC 		 = $mT075['T075_Valor_THC']      - $mT055['T055_Valor_Total_THC'];
		$dif_Valor_SISCOMEX  = $mT075['T075_Valor_SISCOMEX'] - $mT055['T055_Valor_Total_Siscomex'];
		$dif_AFRMM 			 = $mT075['T075_Valor_AFRMM']    - $mT055['T055_Valor_Total_AFRMM'];

		$formArray = array('campos' => array('geral' => array()));	

		$formArray['campos']['geral']['Valor_Total_Custo']			        = array('name' => 'Valor_Total_Custo', 'value' => gCorrigeNumero($mT055[T055_Valor_Total_Custo],4), 'casasDecimais' => '2', 'label' => 'Valor Produtos ME', 'type' => 'moeda', 'align' => 'right', 'length' => array(15,50), 'readonly'=>true, 'reject' => true);
		$formArray['campos']['geral']['dif_Valor_ME']           		    = array('name' => 'dif_Valor_ME', 'label' => 'Diferença Valor ME', 'value' => gCorrigeNumero($dif_Valor_ME, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right');			
		$formArray['campos']['geral']['Cotacao_ME']       				    = array('name' => 'Cotacao_ME', 'casasDecimais' => '2', 'value' => gCorrigeNumero($mT075[T075_Cotacao_ME],4), 'label' => 'Cotacao ME', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'align' => 'right', 'reject' => true);
		$formArray['campos']['geral']['Valor_Total_Custo_Reais'] 		    = array('name' => 'Valor_Total_Custo', 'value' => gCorrigeNumero($mT055['T055_Valor_Total_Custo_Reais'],4), 'casasDecimais' => '2', 'label' => 'Valor Produtos R$', 'align' => 'right', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true);
		$formArray['campos']['geral']['dif_Valor_Reais']         		    = array('name' => 'dif_Valor_Reais', 'label' => 'Diferença Valor Reais', 'value' => gCorrigeNumero($dif_Valor_Reais, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'align' => 'right', 'reject' => true, 'align'=>'right');	
		$formArray['campos']['geral']['Valor_Total_Frete']       		    = array('name' => 'Valor_Total_Frete', 'value' => gCorrigeNumero($mT075['T075_Valor_Frete'],4), 'casasDecimais' => '2', 'label' => 'Frete', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'align' => 'right', 'reject' => true);
		$formArray['campos']['geral']['dif_Valor_Frete']       			    = array('name' => 'dif_Valor_Frete', 'label' => 'Diferença Frete', 'value' => gCorrigeNumero($dif_Valor_Frete, 4), 'casasDecimais' => '2', 'type' => 'moeda' ,'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right', 'align'=>'right');
		$formArray['campos']['geral']['Valor_Total_THC']           		    = array('name' => 'Valor_Total_THC', 'value' => gCorrigeNumero($mT055['T055_Valor_Total_THC'],4), 'casasDecimais' => '2', 'label' => 'THC', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'align' => 'right', 'reject' => true);
		$formArray['campos']['geral']['dif_Valor_THC']          		    = array('name' => 'dif_Valor_THC', 'label' => 'Diferença THC', 'value' => gCorrigeNumero($dif_Valor_THC, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right', 'align'=>'right'); 	
		$formArray['campos']['geral']['Valor_Sub_Total']       			    = array('name' => 'Valor_Sub_Total', 'value' => gCorrigeNumero($mT055[T055_Valor_Sub_Total],4), 'casasDecimais' => '2', 'label' => 'Sub-Total R$', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'align' => 'right', 'reject' => true);
		$formArray['campos']['geral']['dif_Sub_Total']          		    = array('name' => 'dif_Sub_Total', 'label' => 'Diferença Sub-total', 'value' => gCorrigeNumero($dif_Valor_Sub_Total, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right', 'align'=>'right');
		$formArray['campos']['geral']['Valor_Total_Siscomex']               = array('name' => 'Valor_Total_Siscomex', 'value' => gCorrigeNumero($mT055['T055_Valor_Total_Siscomex'],4), 'casasDecimais' => '2', 'label' => 'Siscomex', 'type' => 'moeda', 'align' => 'right', 'length' => array(15,50), 'readonly'=>true, 'reject' => true);
		$formArray['campos']['geral']['dif_Valor_SISCOMEX']                 = array('name' => 'dif_Valor_SISCOMEX', 'label' => 'Diferença Siscomex', 'value' => gCorrigeNumero($dif_Valor_SISCOMEX, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'align' => 'right', 'reject' => true, 'align'=>'right');
		$formArray['campos']['geral']['Valor_Base_Imposto_Importacao']      = array('name' => 'Valor_Base_Imposto_Importacao', 'value' => gCorrigeNumero($mT055['T055_Valor_Base_Imposto_Importacao'],4), 'casasDecimais' => '2', 'label' => 'Valor Base II', 'align' => 'right', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true);
		$formArray['campos']['geral']['Valor_Imposto_Importacao'] 	        = array('name' => 'Valor_Imposto_Importacao', 'value' => gCorrigeNumero($mT055['T055_Valor_Imposto_Importacao'],4), 'casasDecimais' => '2', 'label' => 'Valor II', 'type' => 'moeda', 'align' => 'right', 'length' => array(15,50), 'readonly'=>true, 'reject' => true);
		$formArray['campos']['geral']['dif_Valor_II']				        = array('name' => 'dif_Valor_II', 'label' => 'Diferença II', 'value' => gCorrigeNumero($dif_Valor_II, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right');			 
		$formArray['campos']['geral']['Valor_Base_IPI'] 		            = array('name' => 'Valor_Base_IPI', 'label' => 'Valor Base IPI', 'value' => gCorrigeNumero($mT055['T055_Valor_Base_IPI'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'align' => 'right', 'readonly'=>true, 'reject' => true);
		$formArray['campos']['geral']['Valor_IPI'] 					        = array('name' => 'Valor_IPI', 'label' => 'Valor IPI', 'value' => gCorrigeNumero($mT055['T055_Valor_IPI'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['dif_Valor_IPI']				        = array('name' => 'dif_Valor_IPI', 'label' => 'Diferença IPI', 'value' => gCorrigeNumero($dif_Valor_IPI, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right');
		$formArray['campos']['geral']['Valor_Base_PIS'] 			        = array('name' => 'Valor_Base_PIS', 'label' => 'Valor Base PIS', 'value' => gCorrigeNumero($mT055['T055_Valor_Base_PIS'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['Valor_PIS'] 					        = array('name' => 'Valor_PIS', 'label' => 'Valor PIS', 'value' => gCorrigeNumero($mT055['T055_Valor_PIS'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true);
		$formArray['campos']['geral']['dif_Valor_PIS'] 				        = array('name' => 'dif_Valor_PIS', 'label' => 'Diferença PIS', 'value' => gCorrigeNumero($dif_Valor_PIS, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right');
		$formArray['campos']['geral']['Valor_Base_COFINS'] 			        = array('name' => 'Valor_Base_COFINS', 'label' => 'Valor Base COFINS', 'value' => gCorrigeNumero($mT055['T055_Valor_Base_COFINS'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['Valor_COFINS'] 				        = array('name' => 'Valor_COFINS', 'label' => 'Valor COFINS', 'value' => gCorrigeNumero($mT055['T055_Valor_COFINS'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['dif_Valor_COFINS']			        = array('name' => 'dif_Valor_COFINS', 'label' => 'Diferença Cofins', 'value' => gCorrigeNumero($dif_Cofins, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right');	
		$formArray['campos']['geral']['Valor_AFRMM'] 				        = array('name' => 'Valor_AFRMM', 'label' => 'Valor AFRMM', 'value' => gCorrigeNumero($mT055['T055_Valor_Total_AFRMM'],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['dif_AFRMM'] 					        = array('name' => 'dif_AFRMM', 'label' => 'Diferenca AFRMM', 'value' => gCorrigeNumero($dif_AFRMM, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right'); 
		$formArray['campos']['geral']['Valor_Base_ICMS']			        = array('name' => 'Valor_Base_ICMS', 'label' => 'Valor Base ICMS', 'value' => gCorrigeNumero($mT055[T055_Valor_Base_ICMS_Sem_Reducao],4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['Valor_Base_ICMS_Normal']		        = array('name' => 'Valor_Base_ICMS_Normal', 'label' => 'Valor Base ICMS Normal', 'value' => gCorrigeNumero($mT055[T055_Valor_Base_ICMS_Normal],4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['Valor_ICMS'] 				        = array('name' => 'Valor_ICMS', 'label' => 'Valor ICMS', 'value' => gCorrigeNumero($mT055[T055_Valor_ICMS],4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');		
		$formArray['campos']['geral']['Valor_ICMS_Presumido'] 		        = array('name' => 'Valor_ICMS_Presumido', 'label' => 'Valor ICMS Presumido', 'value' => gCorrigeNumero($mT055[T055_Valor_ICMS_Presumido],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['Valor_ICMS_Pagar'] 			        = array('name' => 'Valor_ICMS_Pagar', 'label' => 'Valor ICMS Recolher', 'value' => gCorrigeNumero($mT055[T055_Valor_ICMS_Pagar],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right' );
		$formArray['campos']['geral']['dif_Valor_ICMS_Pagar'] 		        = array('name' => 'dif_Valor_ICMS_Pagar', 'label' => 'Diferenca ICMS Pagar', gCorrigeNumero($dif_Valor_ICMS, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right'); 
		$formArray['campos']['geral']['T055_Peso_Kg_Liquido_Unitario']      = array('name' => 'T055_Peso_Kg_Liquido_Unitario', 'label' => 'Peso total Kg', 'value' => gCorrigeNumero($mT055[T055_Peso_Kg_Liquido_Unitario],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['dif_Peso_Liquido']                   = array('name' => 'dif_Peso_Liquido', 'label' => 'Diferença Peso liquido', 'value' => gCorrigeNumero($dif_Peso_Liquido, 4), 'casasDecimais' => '2', 'type' => 'moeda', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align'=>'right', 'align' => 'right'); 
		$formArray['campos']['geral']['T055_Valor_Total_Preco_Nota_Fiscal'] = array('name' => 'T055_Valor_Total_Preco_Nota_Fiscal', 'label' => 'Total NFe', 'value' => gCorrigeNumero($mT055[T055_Valor_Total_Preco_Nota_Fiscal],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['T055_Valor_Total_Custo_Final']       = array('name' => 'T055_Valor_Total_Custo_Final', 'label' => 'Total Custo', 'value' => gCorrigeNumero($mT055[T055_Valor_Total_Custo_Final],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['T055_Quantidade']			        = array('name' => 'T055_Quantidade', 'label' => 'Total Peças', 'value' => gCorrigeNumero($mT055[T055_Quantidade],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['QuantidadeVendido'] 			  	    = array('name' => 'QuantidadeVendido', 'label' => 'Total Peças Vendidas', 'value' => gCorrigeNumero($totalItensVendido, 4), 'type' => 'text', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['TotalVendidoLiquido'] 		 		= array('name' => 'TotalVendidoLiquido', 'label' => 'Total Vendido Líquido', 'value' => gCorrigeNumero($totalVendido), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		$formArray['campos']['geral']['TotalVendidoBruto'] 					= array('name' => 'TotalVendidoBruto', 'label' => 'Total Vendido Bruto', 'value' => gCorrigeNumero($totalVendidoBruto), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		if($mT075['T075_Modelo_Calculos'] == '2'){
			$formArray['campos']['geral']['T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal'] = array('name' => 'T055_Valor_Total_Despesas', 'label' => 'Despesas Acessórias', 'value' => gCorrigeNumero($mT055[T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
			$formArray['campos']['geral']['T055_Valor_Total_Despesas_Aduaneira']= array('name' => 'T055_Valor_Total_Despesas_Aduaneira', 'label' => 'Despesas Aduaneiras', 'value' => gCorrigeNumero($mT055[T055_Valor_Total_Despesas_Aduaneira],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');
		}		
		$formArray['campos']['geral']['T055_Valor_Total_Despesas']          = array('name' => 'T055_Valor_Total_Despesas', 'label' => 'Outras Despesas', 'value' => gCorrigeNumero($mT055[T055_Valor_Total_Despesas],4), 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(15,50), 'readonly'=>true, 'reject' => true, 'align' => 'right');

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
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Alterar DI - ID: ' + '{$acaoId}', '/imp/imp003/content/imp003ContentDiIncluir/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [900,550]);
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
	case 'imp003FormProdutoIncluir':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T075_Id = (isset($_REQUEST['T075_Id'])) ? $_REQUEST['T075_Id'] : false;
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		/*
		$sqlT055 = mysql_query("SELECT * FROM T055 WHERE T055_T075_Id = '{$T075_Id}' ORDER BY T055_Id DESC LIMIT 1");
		if(mysql_num_rows($sqlT055) > 0){
			$T055 = mysql_fetch_assoc($sqlT055);
			$T055_Aliquota_ICMS = gCorrigeNumero($T055['T055_Aliquota_ICMS']);
			$T055_Aliquota_ICMS_Presumido = gCorrigeNumero($T055['T055_Aliquota_ICMS_Presumido']);
			$T055_Percentual_Reducao_ICMS = gCorrigeNumero($T055['T055_Percentual_Reducao_ICMS']);
			$T055_Percentual_Reducao_ICMS_Presumido = gCorrigeNumero($T055['T055_Percentual_Reducao_ICMS_Presumido']);
			$T055_Aliquota_ICMS_PIS_COFINS = gCorrigeNumero($T055['T055_Aliquota_ICMS_PIS_COFINS']);
			$T055_Aliquota_ICMS_Credito_Presumido = gCorrigeNumero($T055['T055_Aliquota_ICMS_Credito_Presumido']);
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
		$form_tabela = 'T055';
		$form_campo_pk = 'T055_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('imp003FormProdutosBtnAdd', 'imp003FormProdutosBtnCln', 'imp003FormProdutosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormProdutoIncluir/';

		//form editor 
		$form_editor = array(
			'geral' => '{"T055A_Altura":{"top":168,"left":685.609375,"width":88,"hidden":0},"Procura_Produto":{"top":7,"left":35.75,"width":154,"hidden":0},"Marca":{"top":7,"left":299.65625,"width":200,"hidden":0},"T055_Aliquota_ICMS":{"top":7,"left":677.546875,"width":89,"hidden":0},"T055A_Comprimento":{"top":191,"left":647.671875,"width":88,"hidden":0},"T055_Descricao_Produto":{"top":30,"left":25.71875,"width":456,"hidden":0},"T055_Aliquota_ICMS_Presumido":{"top":30,"left":621.90625,"width":89,"hidden":0},"T055_Quantidade":{"top":53,"left":18.4375,"width":88,"hidden":0},"T055_D037_Id":{"top":53,"left":210.515625,"width":95,"hidden":0},"T055_D005_Id":{"top":53,"left":386,"width":127,"hidden":0},"T055_Percentual_Reducao_ICMS":{"top":53,"left":631.9375,"width":89,"hidden":0},"T055_Valor_Custo_Unitario":{"top":76,"left":5.34375,"width":88,"hidden":0},"T055_Flag_ME":{"top":76,"left":234.75,"width":95,"hidden":0},"T055_Peso_Kg_Liquido_Unitario":{"top":76,"left":363.765625,"width":120,"hidden":0},"T055_Percentual_Reducao_ICMS_Presumido":{"top":76,"left":576.296875,"width":89,"hidden":0},"T055_Aliquota_Imposto_Importacao":{"top":99,"left":57.84375,"width":88,"hidden":0},"T055_Aliquota_IPI":{"top":99,"left":226.90625,"width":88,"hidden":0},"T055_Aliquota_PIS":{"top":99,"left":383.359375,"width":120,"hidden":0},"T055_Aliquota_ICMS_PIS_COFINS":{"top":99,"left":615.671875,"width":89,"hidden":0},"T055_Aliquota_COFINS":{"top":122,"left":25.65625,"width":88,"hidden":0},"T055_Valor_Adicao":{"top":122,"left":216.53125,"width":88,"hidden":0},"T055_T066_Id":{"top":122,"left":384,"width":127,"hidden":0},"T055_Aliquota_ICMS_Credito_Presumido":{"top":122,"left":583.9375,"width":89,"hidden":0},"T055_Item_Ac_Drawback":{"top":143,"left":9.140625,"width":88,"hidden":0},"T055_T270_Id":{"top":143,"left":235.90625,"width":95,"hidden":0},"T055A_Largura":{"top":145,"left":677.359375,"width":88,"hidden":0},"imp003FormProdutosBtnCln":{"top":145,"left":410,"width":null,"hidden":0},"imp003FormProdutosBtnAdd":{"top":145,"left":470,"width":null,"hidden":0},"T055_Cotacao_CC":{"top":191,"left":-2.03125,"width":98,"hidden":1}}'
		);

		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			left join T075 on T075_Id = '{$T075_Id}'
			where (D009_C004_Id = '{$g['empresaAtual']}')
			  and D001_Flag_Pre_Cadastro!='S'
			  and D001_Flag_Ativo = 'S'
			group by D009_Id
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																								// tabela
			'D009_Id,T075_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D001_Descricao_Ingles,D082_Marca,D009_Quantidade_Estoque_Liquido,"' . $T295_Id . '" as T295_Id', // campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Código,Descrição,Descrição Inglês,Marca,Estoque,DI',																	// descrição dos campos
			'campoProcuraProduto',																				// callback js
			$g['divId'],																						// divId
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

		$T055=mysql_query("select T055_Codigo_Produto,
								  D082_Marca
		                     from T055 
		                left join T006 on T006_Id=T055_T006_Id    
	                   	left join D009 on D009_Id=T055_D009_Id
	                   	left join D049 on D049_Id=D009_D049_Id
	                   	left join D082 on D082_Id=D049_D082_Id
		                    where T055_Id='$acaoId'");
		$mT055=mysql_fetch_array($T055);

		$T075=mysql_query("select T075_D024_Id
  			 				 from T075
						    where T075_Id='$T075_Id'");
		$mT075=mysql_fetch_array($T075);

		$whereT270=' WHERE T270_T075_Id='.$T075_Id;
		// novo formulário
		$form = new Formulario('imp003FormProdutosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(25,60), 'reject' => true, 'value' => $mT055['T055_Codigo_Produto']),
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(25,255), 'reject' => true, 'readonly'=>true, 'style'=>'float:right', 'align'=>'right', 'value' => $mT055['D082_Marca']),
					'T055_Descricao_Produto' => array('name' => 'T055_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(75, 150)),
					'T055_Descricao_Produto_Ingles' => array('name' => 'T055_Descricao_Produto_Ingles', 'label' => 'Descrição inglês', 'type' => 'hidden', 'length' => array(75, 150)),
					'T055_Quantidade' => array('name' => 'T055_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(10, 30), 'casasDecimais' => '3'),
					'T055_D037_Id' => array('name' => 'T055_D037_Id', 'label' => 'Unidade', 'type' => 'select', 'align' => 'left', 'select' => 
						gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', '', true)
					),
					'T055_D005_Id' => array('name' => 'T055_D005_Id', 'label' => 'NCM', 'type' => 'select', 'align' => 'left', 'select' => 
						gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal', 'ORDER BY D005_Classificacao_Fiscal', true)
					),
					'T055_Valor_Custo_Unitario' => array('name' => 'T055_Valor_Custo_Unitario', 'type' => 'moeda', 'label' => 'Preço Unitário', 'casasDecimais' => '7', 'length' => array(10, 30)),
					'T055_Flag_ME' => array('name' => 'T055_Flag_ME', 'label' => 'ME', 'type' => 'select', 'align'=>'right', 'select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S', 'selected' => true)
						)
					),
					'T055_Aliquota_Imposto_Importacao' => array('name' => 'T055_Aliquota_Imposto_Importacao', 'label' => 'II %', 'type' => 'moeda', 'length' => array(10,50)),
					'T055_Aliquota_IPI' => array('name' => 'T055_Aliquota_IPI', 'label' => 'IPI %', 'type' => 'moeda', 'length' => array(10,50), 'align'=>'right'),
					'T055_Aliquota_PIS' => array('name' => 'T055_Aliquota_PIS', 'label' => 'PIS %', 'type' => 'moeda', 'length' => array(10,50), 'align'=>'right'),
					'T055_Aliquota_COFINS' => array('name' => 'T055_Aliquota_COFINS', 'label' => 'COFINS %', 'type' => 'moeda', 'length' => array(10,50)),
					'T055_Valor_Adicao' => array('name' => 'T055_Valor_Adicao', 'label' => 'Adição', 'type' => 'text', 'length' => array(10,50), 'align'=>'right'),
					'T055_Peso_Kg_Liquido_Unitario' => array('name' => 'T055_Peso_Kg_Liquido_Unitario', 'label' => 'Peso Unit', 'type' => 'moeda', 'casasDecimais' => '6', 'length' => array(10,50), 'align'=>'right'),
					'T055_Aliquota_ICMS' => array('name' => 'T055_Aliquota_ICMS', 'label' => '% ICMS', 'type' => 'moeda', 'value' => $T055_Aliquota_ICMS),
					'T055_Aliquota_ICMS_Presumido' => array('name' => 'T055_Aliquota_ICMS_Presumido', 'label' => '% ICMS Presumido', 'type' => 'moeda', 'value' => $T055_Aliquota_ICMS_Presumido),
					'T055_Percentual_Reducao_ICMS' => array('name' => 'T055_Percentual_Reducao_ICMS', 'label' => '% Redução ICMS', 'type' => 'moeda', 'value' => $T055_Percentual_Reducao_ICMS),
					'T055_Percentual_Reducao_ICMS_Presumido' => array('name' => 'T055_Percentual_Reducao_ICMS_Presumido', 'label' => '% Redução ICMS Presumido', 'type' => 'moeda', 'value' => $T055_Percentual_Reducao_ICMS_Presumido),
					'T055_Aliquota_ICMS_PIS_COFINS' => array('name' => 'T055_Aliquota_ICMS_PIS_COFINS', 'label' => '% ICMS PIS/COFINS', 'type' => 'moeda', 'value' => $T055_Aliquota_ICMS_PIS_COFINS),
					'T055_Aliquota_ICMS_Credito_Presumido' => array('name' => 'T055_Aliquota_ICMS_Credito_Presumido', 'label' => '% ICMS Credito Presumido', 'type' => 'moeda', 'value' => $T055_Aliquota_ICMS_Credito_Presumido),
     				// Campos Hidden
					'T055_D009_Id' => array('name' => 'T055_D009_Id', 'label' => 'T055_D009_Id', 'type' => 'hidden', 'length' => array(20, 20)),
					'T055_Codigo_Produto' => array('name' => 'T055_Codigo_Produto', 'label' => 'Código', 'type' => 'hidden', 'length' => array(20, 20)),
					'T055_Cotacao_CC' => array('name' => 'T055_Cotacao_CC', 'label' => 'Cotação CC', 'type' => 'moeda'),
					'T055_T075_Id' => array('name' => 'T055_T075_Id', 'label' => 'T055_T075_Id', 'type' => 'hidden', 'value' => $T075_Id),
					'T055_D024_Id' => array('name' => 'T055_D024_Id', 'label' => 'T055_D024_Id', 'type' => 'hidden', 'value' => $mT075['T075_D024_Id']),
					'T055_C004_Id' => array('name' => 'T055_C004_Id', 'label' => 'T055_C004_Id', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T055_T270_Id' => array('name' => 'T055_T270_Id', 'label' => 'L.I.', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>  
											gGeraSelect('T270', 'T270_Id', 'T270_Numero_LI', "$whereT270 order by T270_Numero_LI", true)),
			        'T055_Item_Ac_Drawback' => array('name' => 'T055_Item_Ac_Drawback', 'label' => 'AC Drawback', 'type' => 'text'),
			        'T055_T066_Id' => array('name' => 'T055_T066_Id', 'label' => 'Local', 'type' => 'select', 'align' => 'left', 'select' => 
						gGeraSelect('D004', 'D004_Id', 'D004_Local', 'left join T006 on T006_D004_Id where false', true)
					),
					'T055A_Largura' => array('name' => 'T055A_Largura', 'label' => 'Largura', 'type' => 'moeda', 'casasDecimais' => '6', 'length' => array(10,50), 'align'=>'right','type' => 'hidden'),
					'T055A_Altura' => array('name' => 'T055A_Altura', 'label' => 'Altura', 'type' => 'moeda', 'casasDecimais' => '6', 'length' => array(10,50), 'align'=>'right','type' => 'hidden'),
					'T055A_Comprimento' => array('name' => 'T055A_Comprimento', 'label' => 'Comprimento', 'type' => 'moeda', 'casasDecimais' => '6', 'length' => array(10,50), 'align'=>'right','type' => 'hidden'),
				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['imp003FormProdutosBtnNew'] = array('name' => 'imp003FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['imp003FormProdutosBtnCln'] = array('name' => 'imp003FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['imp003FormProdutosBtnAdd'] = array('name' => 'imp003FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
						$T055_Codigo_Produto = $form->campoValorEnviado('T055_Codigo_Produto');
						$T055 = mysql_query("SELECT T055_Codigo_Produto 
											   FROM T055 
											  WHERE T055_Codigo_Produto='{$T055_Codigo_Produto}' 
											    AND T055_T075_Id='{$T075_Id}'
											");
						if(mysql_num_rows($T055) > 0){
							$insereDuplicado = false;
						}
					}
					if($insereDuplicado == false){
						echo gGeraAlertMsg('Erro', "Já existe o item com o código ".$T055_Codigo_Produto." nesta DI. Por favor, selecione outro produto.");
					}else if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)){
						$T055_Id = $g['mysqlLastId'];
						//mysql_query("INSERT INTO T055A (T055A_T055_Id) VALUES ('{$T055_Id}')");
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

	case 'imp003FormAtualizarLocacao':
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
		log("T075_Id: ".$acaoId);
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T055';
		$form_campo_pk = 'T055_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp003FormAtualizarLocacao_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormAtualizarLocacao/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"D146_Id":{"top":7,"left":23.546875,"width":143,"hidden":0},"imp003FormAtualizarLocacao_btn":{"top":5,"left":400,"width":null,"hidden":0}}',
        );
		
		$form = new Formulario('imp003FormAtualizarLocacao');
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

		$formArray['campos']['geral']['imp003FormAtualizarLocacao_btn'] = array('name' => 'imp003FormAtualizarLocacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Atualizar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			log("clicado botao");
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
								$.getJSON('/imp/imp003/form_func-ajax/validarArmazem/?ajax=true&T075_Id=' + encodeURIComponent('{$acaoId}') + '&D146_Id=' + encodeURIComponent("{$D146_Id}") + '&callback=?', function(request) {
									if (request.code) {
										$('#{$g['divId']}').showLoading();
										$.getJSON('/imp/imp003/form_func-ajax/atualizarArmazemEmProdutos/?ajax=true&T075_Id=' + encodeURIComponent("{$acaoId}") + '&D146_Id=' + encodeURIComponent("{$D146_Id}") + '&callback=?', function(request) {
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
												$.getJSON('/imp/imp003/form_func-ajax/atualizarArmazemEmProdutos/?ajax=true&T075_Id=' + encodeURIComponent("{$acaoId}") + '&D146_Id=' + encodeURIComponent("{$D146_Id}") + '&callback=?', function(request) {
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

	case 'imp003FormDocumentos':
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
		$enviar_btnId = array('imp003FormDocumentosBtnAdd', 'imp003FormDocumentosBtnCln', 'imp003FormDocumentosBtnNew');
        // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormDocumentos/';
		// Array de definição do formulário
		$r_T075_Id = isset($r_imp003FormDocumentos) ? $r_imp003FormDocumentos['T155_T075_Id'] : $r_T075_Id;
		                                                           
		$tmp = gGeraSelect('T075', 'T075_Id', 'T075_Numero_DI', "WHERE T075_Id = $r_T075_Id");
		foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('imp003FormDocumentos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		//$formArray['campos']['geral']['__T155_T075_Id'] 		= array('name' => '__T155_T075_Id', 'label'=>'Número DI', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
		//	$tmp
		//);
		$formArray['campos']['geral']['T155_Descricao'] 	= array('name' => 'T155_Descricao', 'label'=>'Descrição', 'type' => 'textarea','length' => array(3,60));
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);
		$formArray['campos']['geral']['T155_Nome_Arquivo'] 		= array('name' => 'T155_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false);
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['imp003FormDocumentosBtnNew'] = array('name' => 'imp003FormDocumentosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['T155_T075_Id'] 			= array('name' => 'T155_T075_Id', 'type' => 'hidden', 'value' => $r_T075_Id);
		$formArray['campos']['geral']['T155_C007_Id'] 			= array('name' => 'T155_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']);
		$formArray['campos']['geral']['T155_Data_Hora_Upload']	= array('name' => 'T155_Data_Hora_UpLoad', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s'));
		//$formArray['campos']['geral']['imp003FormDocumentosBtnCln'] = array('name' => 'imp003FormDocumentosBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['imp003FormDocumentosBtnAdd'] = array('name' => 'imp003FormDocumentosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
								//$dir = "{$g['pathDados']}documentos/T075_".str_repeat('0',5-strlen($form->campoValorEnviado('T155_T075_Id'))).$form->campoValorEnviado('T155_T075_Id');
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

	case 'imp003FormDocumentosDespesa':

		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T168_Id = isset($r_T168_Id) ? $r_T168_Id : false;
		$T155_T075_Id = isset($r_T155_T075_Id) ? $r_T155_T075_Id : false;
		log("form T155_T075_Id=".$T155_T075_Id);
		log("form documentos T168_Id=".$r_T168_Id);
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
		$enviar_btnId = array('imp003FormDocumentosDespesaBtnAdd', 'imp003FormDocumentosDespesaBtnCln', 'imp003FormDocumentosDespesaBtnNew');
        // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormDocumentosDespesa/';
		// Array de definição do formulário
		$r_T075_Id = isset($r_imp003FormDocumentosDespesa) ? $r_imp003FormDocumentosDespesa['T155_T075_Id'] : $r_T075_Id;
		                                                           
		$tmp = gGeraSelect('T075', 'T075_Id', 'T075_Numero_DI', "WHERE T075_Id = $r_T075_Id");
		foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('imp003FormDocumentosDespesa');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		//$formArray['campos']['geral']['__T155_T075_Id'] 		= array('name' => '__T155_T075_Id', 'label'=>'Número DI', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
		//	$tmp
		//);
		$formArray['campos']['geral']['T155_Descricao'] 	= array('name' => 'T155_Descricao', 'label'=>'Descrição', 'type' => 'textarea','length' => array(3,60));
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);
		$formArray['campos']['geral']['T155_Nome_Arquivo'] 		= array('name' => 'T155_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false);
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['imp003FormDocumentosDespesaBtnNew'] = array('name' => 'imp003FormDocumentosDespesaBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['T155_T075_Id'] 			= array('name' => 'T155_T075_Id', 'type' => 'hidden', 'value' => $T155_T075_Id);
		$formArray['campos']['geral']['T155_T168_Id'] 			= array('name' => 'T155_T168_Id', 'type' => 'hidden', 'value' => $T168_Id);
		$formArray['campos']['geral']['T155_C007_Id'] 			= array('name' => 'T155_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']);
		$formArray['campos']['geral']['T155_Data_Hora_Upload']	= array('name' => 'T155_Data_Hora_UpLoad', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s'));
		//$formArray['campos']['geral']['imp003FormDocumentosDespesaBtnCln'] = array('name' => 'imp003FormDocumentosDespesaBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['imp003FormDocumentosDespesaBtnAdd'] = array('name' => 'imp003FormDocumentosDespesaBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
								//$dir = "{$g['pathDados']}documentos/T075_".str_repeat('0',5-strlen($form->campoValorEnviado('T155_T075_Id'))).$form->campoValorEnviado('T155_T075_Id');
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
	
	case 'imp003FormCadastroDespesas':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		$T075_Id = isset($r_T075_Id) ? $r_T075_Id : false;
		
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
		$form_tabela = 'T168';
		$form_campo_pk = 'T168_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp003FormCadastroDespesasBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormCadastroDespesas/';

		//form editor
		$form_editor = array('geral' => '{"T168_Descricao":{"top":7,"left":50.265625,"height":28,"width":317,"hidden":0},"T168_Data":{"top":53,"left":76.375,"width":116,"hidden":0},"T168_Data_Pagamento":{"top":76,"left":24.796875,"width":116,"hidden":0},"T168_Valor":{"top":99,"left":73.734375,"width":116,"hidden":0},"T168_Flag_ME":{"top":122,"left":78.09375,"width":63,"hidden":0}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					// 'D131_D116_Id' => array('name' => 'D131_D116_Id', 'label' => 'Veículo', 'type' => 'select', 
						// 'select' =>  gGeraSelect('D116','D116_Id','D116_Modelo','',true),
					// ),
					'T168_Descricao' => array('name' => 'T168_Descricao', 'label' => 'Descrição', 'type' => 'textarea', 'length' => array(2,50)),
					'T168_Data' => array('name' => 'T168_Data', 'label' => 'Data', 'type' => 'data', 'length' => array(15,70)),
					'T168_Data_Pagamento' => array('name' => 'T168_Data_Pagamento', 'label' => 'Data Pagamento', 'type' => 'data', 'length' => array(15,70)),
					'T168_Valor' => array('name' => 'T168_Valor', 'label' => 'Valor', 'type' => 'moeda', 'length' => array(15,70), 'casasDecimais' => '4'),
					'T168_Flag_ME' => array('name' => 'T168_Flag_ME', 'label' => 'ME?', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Não', 'value' => 'N'), 
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'T168_T075_Id' => array('name' => 'T168_T075_Id', 'label' => 'Descrição', 'value'=>@$T075_Id, 'type' => 'hidden', 'length' => array(2,60)),
				)
			)
		);
        $form = new Formulario('form-imp003FormDespesas');
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
		$botoesDir .= '<button type="submit" id="imp003FormCadastroDespesasBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'imp003FormCadastroDespesasNovo':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		$T075_Id = isset($r_T075_Id) ? $r_T075_Id : false;
		
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
		$form_tabela = 'T168';
		$form_campo_pk = 'T168_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp003FormCadastroDespesasBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormCadastroDespesasNovo/';

		//form editor
		$form_editor = array('geral' => '{"T168_Flag_Base_Rateio":{"top":145,"left":323.6875,"width":127,"hidden":0},"T168_Descricao":{"top":7,"left":50.265625,"width":398,"hidden":0},"T168_Data":{"top":30,"left":76.375,"width":116,"hidden":0},"T168_Flag_Soma_Base_IPI":{"top":53,"left":17.65625,"width":55,"hidden":0},"T168_Flag_Soma_Base_COFINS":{"top":53,"left":328.421875,"width":63,"hidden":0},"T168_Flag_Soma_Base_ICMS":{"top":76,"left":4.296875,"width":55,"hidden":0},"T168_Flag_Soma_Base_PIS":{"top":76,"left":350.125,"width":63,"hidden":0},"T168_Flag_Soma_Base_II":{"top":99,"left":24.59375,"width":55,"hidden":0},"T168_Flag_Soma_Preco_NFe":{"top":99,"left":342.65625,"width":63,"hidden":0},"T168_Flag_Soma_Custo":{"top":122,"left":29.40625,"width":55,"hidden":0},"T168_Flag_Tipo_Rateio":{"top":145,"left":75.484375,"width":95,"hidden":0},"T168_Flag_ME":{"top":122,"left":372.96875,"width":63,"hidden":0},"T168_Flag_Tipo":{"top":168,"left":84.921875,"width":96,"hidden":0},"T168_Cotacao_ME":{"top":168,"left":383,"width":56,"hidden":0},"T168_Valor":{"top":191,"left":81.203125,"width":88,"hidden":0},"T168_Tipo_Despesa":{"top":191,"left":311.828125,"width":127,"hidden":0},"T168_Aliquota":{"top":214,"left":65.875,"width":137,"hidden":0},"T168_Tipo":{"top":214,"left":356.921875,"width":127,"hidden":0},"T168_Formula":{"top":237,"left":66.28125,"height":85,"width":392,"hidden":0}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T168_Descricao' => array('name' => 'T168_Descricao', 'label' => 'Descrição', 'type' => 'text', 'length' => array(2,50)),
					'T168_Aliquota' => array('name' => 'T168_Aliquota', 'label' => 'Alíquota', 'type' => 'moeda', 'length' => array(20,10)),
					'T168_Data' => array('name' => 'T168_Data', 'label' => 'Data','type' => 'data', 'length' => array(15,255)),
					'T168_Flag_Soma_Base_IPI' => array('name' => 'T168_Flag_Soma_Base_IPI', 'label' => 'Somar na base IPI', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T168_Flag_Soma_Base_ICMS' => array('name' => 'T168_Flag_Soma_Base_ICMS', 'label' => 'Somar na base ICMS', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T168_Flag_Soma_Base_PIS' => array('name' => 'T168_Flag_Soma_Base_PIS', 'label' => 'Somar na base PIS', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T168_Flag_Soma_Base_COFINS' => array('name' => 'T168_Flag_Soma_Base_COFINS', 'label' => 'Somar na base COFINS', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T168_Flag_Soma_Base_II' => array('name' => 'T168_Flag_Soma_Base_II', 'label' => 'Somar na base II', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T168_Flag_Soma_Custo' => array('name' => 'T168_Flag_Soma_Custo', 'label' => 'Somar no custo', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T168_Flag_Soma_Preco_NFe' => array('name' => 'T168_Flag_Soma_Preco_NFe', 'label' => 'Somar no preço NFe', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T168_Flag_Tipo_Rateio' => array('name' => 'T168_Flag_Tipo_Rateio', 'label' => 'Rateio', 'type' => 'select','select' => 
						array(
							array('title' => 'Custo', 'value' => 'C', 'selected' => true),		
							array('title' => 'Peso', 'value' => 'P'),				
						)
					),
					'T168_Flag_Base_Rateio' => array('name' => 'T168_Flag_Base_Rateio', 'label' => 'Base rateio', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '0'),		
							array('title' => 'Total DI', 'value' => '1'),		
							array('title' => 'Total Adição', 'value' => '2'),				
							array('title' => 'Peso Adição', 'value' => '3'),				
						)
					),
					'T168_Flag_Tipo' => array('name' => 'T168_Flag_Tipo', 'label' => 'Tipo', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'Valor fixo', 'value' => 'V'),	
							array('title' => 'Fórmula', 'value' => 'F'),
						)
					),
					'T168_Flag_ME' => array('name' => 'T168_Flag_ME', 'label' => 'Valor em ME', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'T168_Cotacao_ME' => array('name' => 'T168_Cotacao_ME', 'label' => 'Cotação ME', 'type' => 'moeda', 'length' => array(20,10)),
					'T168_Valor' => array('name' => 'T168_Valor', 'label' => 'Valor', 'type' => 'moeda', 'length' => array(20,10)),
					'T168_Formula' => array('name' => 'T168_Formula', 'label' => 'Fórmula', 'type' => 'textarea', 'uppercase' => false, 'code' => 'php', 'codeModoSimples' => true),
					'T168_T075_Id' => array('name' => 'T168_T075_Id', 'label' => 'T168_T075_Id', 'value'=> @$T075_Id, 'type' => 'hidden', 'length' => array(2,60)),
					'T168_Tipo_Despesa' => array('name' => 'T168_Tipo_Despesa', 'label' => 'Tipo Despesa', 'type' => 'select','select' => 
						array(
							array('title' => 'Outros', 'value' => '1'),		
							array('title' => 'THC', 'value' => '2'),
							array('title' => 'Seguro', 'value' => '3'),	
							array('title' => 'Siscomex', 'value' => '4'),	
							array('title' => 'AFRMM', 'value' => '5'),	
							array('title' => 'Frete', 'value' => '6')
						)
					),
					'T168_Tipo' => array('name' => 'T168_Tipo', 'label' => 'Tipo', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '0'),		
							array('title' => 'Outras', 'value' => '1'),		
							array('title' => 'Aduaneira', 'value' => '2'),				
							array('title' => 'Acessórias', 'value' => '3'),				
						)
					),
				)
			)
		);
        $form = new Formulario('form-imp003FormDespesas');
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} #T168_Flag_Tipo').change(function() {
				if (($(this).val() == 'V')) {
					$('#{$g['divId']} #T168_Valor').closest('.formEditor').show();
					setTimeout(function() {
						$('#{$g['divId']} #T168_Valor').focus();
					}, 100);
				}else{
					$('#{$g['divId']} #T168_Valor').val('');
					$('#{$g['divId']} #T168_Valor').closest('.formEditor').hide();
				}
				if($(this).val() == 'F'){
					$('#{$g['divId']} #T168_Formula').closest('.formEditor').show();
					$('#{$g['divId']} #T168_Aliquota').closest('.formEditor').show();
					setTimeout(function() {
						$('#{$g['divId']} #T168_Formula').focus();
						$('#{$g['divId']} #T168_Aliquota').focus();
					}, 100);
				}else{
					$('#{$g['divId']} #T168_Formula').val('');
					$('#{$g['divId']} #T168_Formula').closest('.formEditor').hide();
					$('#{$g['divId']} #T168_Aliquota').val('');
					$('#{$g['divId']} #T168_Aliquota').closest('.formEditor').hide();
				}
			});
			$('#{$r_divId} #T168_Flag_Tipo').trigger('change');
			$('#{$g['divId']} #T168_Flag_ME').change(function() {
				if (($(this).val() == 'S')) {
					$('#{$g['divId']} #T168_Cotacao_ME').closest('.formEditor').show();
					setTimeout(function() {
						$('#{$g['divId']} #T168_Cotacao_ME').focus();
					}, 100);
				}else{
					$('#{$g['divId']} #T168_Cotacao_ME').val('');
					$('#{$g['divId']} #T168_Cotacao_ME').closest('.formEditor').hide();
				}
			});
			$('#{$r_divId} #T168_Flag_ME').trigger('change');
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

						$T168_Id = $g['mysqlLastId'];
						$T168    = mysql_query("SELECT T168_Ordem FROM T168 WHERE T168_T075_Id = '{$T075_Id}' ORDER BY T168_Ordem DESC LIMIT 1");
						$mT168   = mysql_fetch_array($T168);
						mysql_query("UPDATE T168 SET T168_Ordem = {$mT168['T168_Ordem']} + 1 WHERE T168_Id = '{$T168_Id}'");
						
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
		$botoesDir .= '<button type="submit" id="imp003FormCadastroDespesasBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	
	case 'imp003FormCadastroContasPagar':
		global $idFormDecImport;

		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$T075_Id = isset($r_T075_Id) ? $r_T075_Id : false;

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
		$form_tabela = 'T015';
		$form_campo_pk = 'T015_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp003FormCadastroContasPagar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormCadastroContasPagar/';
		
		$sql = "SELECT * FROM T075 left join D024 on D024_Id=T075_D024_Id WHERE T075_Id='{$T075_Id}' ";
		$result = mysql_query($sql);
		$linha = mysql_fetch_assoc($result);
		$D024_Id = $linha['D024_Id'];
		
		// Novo formulário
		$idFormDecImport = "form_imp003FormCadastroContasReceber";
		$form = new Formulario('form_imp003FormCadastroContasReceber');
		if ($g['C031']['usarMoedaEstrangeira'] == 'N') {
			$form_editor = array(
				'geral' => '{"T015_D024_Id":{"top":7,"left":50.953125,"width":409,"hidden":0},"T015_D032_Id":{"top":30,"left":69.421875,"width":160,"hidden":0},"T015_D014_Id":{"top":30,"left":342.0625,"width":159,"hidden":0},"T015_D073_Id":{"top":53,"left":35.21875,"width":159,"hidden":0},"T015_Data_Emissao":{"top":76,"left":31.484375,"width":152,"hidden":0},"T015_Data_Vencimento":{"top":76,"left":284.53125,"width":152,"hidden":0},"T015_Numero_Documento":{"top":99,"left":34.4375,"width":153,"hidden":0},"T015_Numero_Duplicata":{"top":99,"left":306.75,"width":151,"hidden":0},"T015_D031_Id":{"top":122,"left":63.6875,"width":159,"hidden":0},"T015_T075_Id":{"top":122,"left":368.390625,"width":159,"hidden":0},"T015_Valor_Duplicata":{"top":145,"left":43.359375,"width":153,"hidden":0},"T015_Valor_Juros":{"top":168,"left":48.0625,"width":152,"hidden":0},"T015_Valor_Desconto":{"top":168,"left":303.90625,"width":152,"hidden":0},"T015_Valor_Pago":{"top":191,"left":50.453125,"width":153,"hidden":0},"T015_Valor_Total":{"top":191,"left":326.140625,"width":152,"hidden":0},"T015_Observacao":{"top":214,"left":48.296875,"height":85,"width":424,"hidden":0}}'
			);
		} else {
			$form_editor = array(
				'geral' => '{"T015_D024_Id":{"top":7,"left":50.953125,"width":409,"hidden":0},"T015_D032_Id":{"top":30,"left":69.421875,"width":143,"hidden":0},"T015_D014_Id":{"top":30,"left":342.0625,"width":159,"hidden":0},"T015_D073_Id":{"top":53,"left":35.21875,"width":143,"hidden":0},"T015_Data_Vencimento":{"top":76,"left":284.53125,"width":151,"hidden":0},"T015_Data_Emissao":{"top":76,"left":31.484375,"width":136,"hidden":0},"T015_Numero_Documento":{"top":99,"left":34.4375,"width":136,"hidden":0},"T015_Numero_Duplicata":{"top":99,"left":306.75,"width":152,"hidden":0},"T015_D031_Id":{"top":122,"left":63.6875,"width":143,"hidden":0},"T015_T075_Id":{"top":122,"left":368.390625,"width":159,"hidden":0},"T015_Valor_Duplicata_Moeda":{"top":145,"left":55.15625,"width":136,"hidden":0},"T015_Valor_Duplicata":{"top":145,"left":322.8125,"width":152,"hidden":0},"T015_Valor_Cambio_Moeda":{"top":168,"left":39.53125,"width":136,"hidden":0},"T015_Valor_Total":{"top":191,"left":326.140625,"width":152,"hidden":0},"T015_Valor_Desconto":{"top":191,"left":31.90625,"width":136,"hidden":0},"T015_Valor_Pago":{"top":214,"left":322.453125,"width":152,"hidden":0},"T015_Valor_Juros":{"top":214,"left":48.0625,"width":136,"hidden":0},"T015_Observacao":{"top":237,"left":48.296875,"height":85,"width":424,"hidden":0}}'
			);
		}

		$T015    = mysql_fetch_array(mysql_query("SELECT T015_D032_Id FROM T015 WHERE T015_Id = '{$acaoId}'"));
		$D032_Id = (!empty($T015['T015_D032_Id'])) ? $T015['T015_D032_Id'] : 0;

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T015_D024_Id' => array('name' => 'Consulta_T015_D024_Id', 'label'=>'Fornecedor', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3/%4] - %5 Tel:(%7)%8'),'length' => array(65,30)),
					'T015_D032_Id' => array('name' => 'T015_D032_Id', 'label' => 'Grupo', 'type' => 'select','extra' => "onchange=\"alteraConta(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D032', 'D032_Id', 'D032_Descricao','WHERE D032_Flag_Ativo="S" ORDER BY D032_Descricao', true)
					),
					'T015_D014_Id' => array('name' => 'T015_D014_Id', 'label' => 'Conta', 	'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta',"LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '{$D032_Id}' AND D014_Flag_Ativo='S' ORDER BY D014_SubConta", true)
					),
					//'T015_D014_Id' => array('name' => 'Consulta_T015_D014_Id','label'=>'Conta', 'type' => 'pesquisa','pesquisa'=>array('D014','D014_Id','D014_SubConta,D154_Descricao,D014_Grupo', 'LEFT JOIN D154 ON D154_Id=D014_D154_Id'), 'length' => array(65,30)),
					'T015_D073_Id' => array('name' => 'T015_D073_Id', 'label' => 'Centro Custo', 'type' => 'select','select' => 
						gGeraSelect('D073','D073_Id','D073_Nome', 'WHERE D073_Flag_Ativo = "S"'),
					),
					'T015_Data_Emissao' => array('name' => 'T015_Data_Emissao', 'label'=>'Data Emissão','type' => 'data', 'length' => array(20,10)),
					'T015_Data_Vencimento' => array('name' => 'T015_Data_Vencimento','label'=>'Data Vencimento ', 'align'=>'right','style'=>'float:right','type' => 'data', 'length' => array(20,10)),
					'T015_Numero_Documento' => array('name' => 'T015_Numero_Documento', 'label'=>'Nº Documento','type' => 'text', 'length' => array(20,10)),
					'T015_Numero_Duplicata' => array('name' => 'T015_Numero_Duplicata', 'label'=>'Nº Duplicata ','align'=>'right','style'=>'float:right','type' => 'text', 'length' => array(20,10)),		
					'T015_D031_Id' => array('name' => 'T015_D031_Id', 'label' => 'Portador', 'type' => 'select','select' => 
						gGeraSelect('D031','D031_Id','D031_Portador'),
					),
					'T015_T075_Id' => array('name' => 'T015_D075_Id', 'label' => 'DI', 'type' => 'select','select' => 
						gGeraSelect('T075','T075_Id','T075_Numero_DI', '', false, $r_T075_Id),'align'=>'right','style'=>'float:right',
					),
					'T015_Valor_Juros' => array('name' => 'T015_Valor_Juros', 'label'=>'Valor Juros ','align'=>'right','style'=>'float:right', 'type' => 'moeda', 'disabled'=>true, 'length' => array(20,10)),
					'T015_Valor_Desconto' => array('name' => 'T015_Valor_Desconto', 'label'=>'Valor Desconto', 'type' => 'moeda', 'disabled'=>true, 'length' => array(20,10)),
					'T015_Valor_Pago' => array('name' => 'T015_Valor_Pago', 'label'=>'Valor Pago ','align'=>'right', 'disabled'=>true, 'style'=>'float:right', 'type' => 'moeda', 'length' => array(20,10)),
					'T015_Valor_Total' => array('name' => 'T015_Valor_Total', 'label'=>'Valor Total', 'type' => 'moeda', 'disabled'=>true, 'length' => array(20,10)),
					'T015_Observacao' => array('name' => 'T015_Observacao', 'label'=>'Observação', 'type' => 'textarea', 'length' => array(5,61)),
			
					'T015_C004_Id' => array('name' => 'T015_C004_Id','type' => 'hidden','value'=>$g['empresaAtual']),
					'T015_Data_Pagamento' => array('name' => 'T015_Data_Pagamento','type' => 'hidden'),
					//'T015_T075_Id' => array('name' => 'T015_T075_Id','type' => 'hidden','value'=>$T075_Id),
			
				)
			)
		);

		if ($g['C031']['usarMoedaEstrangeira'] == 'S') {
			$formArray['campos']['geral']['T015_Valor_Duplicata_Moeda'] =  array('name' => 'T015_Valor_Duplicata_Moeda', 'label'=>'Valor ME', 'type' => 'moeda','casasDecimais' => '2', 'disabled'=>false, 'length' => array(20,14));
			$formArray['campos']['geral']['T015_Valor_Cambio_Moeda'] =  array('name' => 'T015_Valor_Cambio_Moeda', 'label'=>'Cotação ME', 'type' => 'moeda', 'disabled'=>false,'casasDecimais' => '4', 'length' => array(20,10));
			$formArray['campos']['geral']['T015_Valor_Duplicata'] =  array('name' => 'T015_Valor_Duplicata', 'label'=>'Valor Título', 'type' => 'moeda', 'length' => array(20,14));
		} else {
			$formArray['campos']['geral']['T015_Valor_Duplicata'] =  array('name' => 'T015_Valor_Duplicata', 'label'=>'Valor Título', 'type' => 'moeda', 'length' => array(20,14));
		}

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
				if ($g['C031']['usarMoedaEstrangeira'] == 'S' && ($form->campoValorEnviado('T015_Valor_Cambio_Moeda') > 0 && $form->campoValorEnviado('T015_Valor_Duplicata_Moeda') > 0)) {
					// (Atualização) Multiplicação dos campo "T015_Valor_Duplicata" e "T015_Valor_Cambio_Moeda", armazenando o resultado em "T015_Valor_Duplicata" e setando este para o campo no formulário 
					$T015_Valor_Cambio_Moeda 		= gCorrigeNumeroInverte($form->campoValorEnviado('T015_Valor_Cambio_Moeda'));
					$T015_Valor_Duplicata_Moeda 	= gCorrigeNumeroInverte($form->campoValorEnviado('T015_Valor_Duplicata_Moeda'));
					$T015_Valor_Duplicata      		= $T015_Valor_Cambio_Moeda * $T015_Valor_Duplicata_Moeda;
					$T015_Valor_Duplicata      		= gCorrigeNumero($T015_Valor_Duplicata);
					$form->campoSetarValor('T015_Valor_Duplicata', $T015_Valor_Duplicata, true);
				}

				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
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
						$T015_Id = $g['mysqlLastId'];
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
				
				// NÃO EXISTEM ESSAS FUNÇÕES PARA A TABELA T015. VER QUAIS OS NOMES DAS FUNÇÕES
				if ($T015_Id) {
					mysql_query("call T015_Gravar_Totalizacao('$T015_Id')");
	    			mysql_query("call T015_Baixar_Titulo('$T015_Id','".date('Y-m-d')."')");
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="imp003FormCadastroContasPagar_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	
	case 'imp003FormCadastroContasReceber':		
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
		$enviar_btnId = 'imp003FormCadastroContasReceberbtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormCadastroContasReceber/';

		$form_editor = array(
			'cadastro_titulo' => '{"T002_D024_Id":{"top":7,"left":52,"width":409,"hidden":0},"T002_D014_Id":{"top":30,"left":49,"width":409,"hidden":0},"T002_Nome_Cliente":{"top":53,"left":23,"width":424,"hidden":0},"T002_Data_Emissao":{"top":76,"left":15,"width":120,"hidden":0},"T002_Data_Vencimento":{"top":76,"left":301,"width":120,"hidden":0},"T002_Numero_Documento":{"top":99,"left":19,"width":120,"hidden":0},"T002_Numero_Duplicata":{"top":99,"left":324,"width":120,"hidden":0},"T002_Flag_Status":{"top":122,"left":59,"width":128,"hidden":0},"T002_T075_Id":{"top":122,"left":383,"width":127,"hidden":0},"T002_D027_Id":{"top":145,"left":41,"width":127,"hidden":0},"T002_Valor_Duplicata":{"top":168,"left":10,"width":120,"hidden":0},"T002_Valor_Juros":{"top":191,"left":31,"width":120,"hidden":0},"T002_Valor_Desconto":{"top":191,"left":319,"width":120,"hidden":0},"T002_Valor_Recebido":{"top":214,"left":13,"width":120,"hidden":0},"T002_Valor_Total":{"top":168,"left":342,"width":120,"hidden":0},"T002_Valor_Saldo":{"top":214,"left":367,"width":120,"hidden":0},"T002_T135_Id":{"top":237,"left":48,"width":127,"hidden":0},"T002_Observacao":{"top":260,"left":29,"height":85,"width":424,"hidden":0}}'
		);
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$form = new Formulario('imp003FormCadastroContasReceber');  
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
					'T002_T075_Id' => array('name' => 'T002_T075_Id', 'label' => 'DI', 'type' => 'select','select' => 
						gGeraSelect('T075','T075_Id','T075_Numero_DI', '', false, $r_T075_Id),'align'=>'right','style'=>'float:right',
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

	case 'imp003FormConfiguracoes':
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
		$form_tabela = 'T075';
		$form_campo_pk = 'T075_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp003FormConfiguracoesBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormConfiguracoes/';

        $form_editor = array(
			'geral' => '{"T075_Calculo_THC":{"top":7,"left":93.953125,"width":160,"hidden":0},"T075_Somar_PIS_Custo":{"top":30,"left":58.234375,"width":59,"hidden":0},"T075_Somar_COFINS_Custo":{"top":53,"left":36.53125,"width":59,"hidden":0},"T075_Somar_Seguro_Custo":{"top":76,"left":41.0625,"width":59,"hidden":0},"T075_Somar_AFRMM_Custo":{"top":99,"left":35.453125,"width":59,"hidden":0},"T075_Somar_ICMS_Pagar_Custo":{"top":122,"left":8.953125,"width":59,"hidden":0},"T075_Somar_IPI_Custo":{"top":145,"left":61.765625,"width":59,"hidden":0},"T075_Modelo_Calculos":{"top":168,"left":51.71875,"width":159,"hidden":0},"imp003FormConfiguracoesBtn":{"top":212,"left":272,"width":null,"hidden":0}}'
        );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T075_Calculo_THC' => array('name' => 'T075_Calculo_THC', 'label' => 'Cálculo THC', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'Rateio pelo custo', 'value' => '1'),
							array('title' => 'Rateio pelo peso', 'value' => '2'),
						)
					),
					'T075_Somar_PIS_Custo' => array('name' => 'T075_Somar_PIS_Custo', 'label' => 'Somar PIS no custo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T075_Somar_COFINS_Custo' => array('name' => 'T075_Somar_COFINS_Custo', 'label' => 'Somar COFINS no custo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T075_Somar_Seguro_Custo' => array('name' => 'T075_Somar_Seguro_Custo', 'label' => 'Somar seguro no custo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T075_Somar_AFRMM_Custo' => array('name' => 'T075_Somar_AFRMM_Custo', 'label' => 'Somar AFRMM no custo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T075_Somar_ICMS_Pagar_Custo' => array('name' => 'T075_Somar_ICMS_Pagar_Custo', 'label' => 'Somar ICMS a pagar no custo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T075_Somar_IPI_Custo' => array('name' => 'T075_Somar_IPI_Custo', 'label' => 'Somar IPI no custo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T075_Modelo_Calculos' => array('name' => 'T075_Modelo_Calculos', 'label' => 'Modelo Despesas D.I', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => 'Padrão', 'value' => '1'),
							array('title' => 'Novo', 'value' => '2'),
						),
						'extra' => "title='" . "PADRÃO: Cálculos do custo e impostos são formados com valores digitados no cabeçalho\n    NOVO: Cálculos do custo e impostos são formados com os valores cadastrados nas despesas" . "'"
					),
					'imp003FormConfiguracoesBtn' => array('name' => 'imp003FormConfiguracoesBtn', 'type'=>'button', 'reload' => false, 'value' => 'Alterar', 'reject' => true),
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
								divRefresh('{$r_divRefresh}', true);
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
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Alterar DI - ID: ' + '{$acaoId}', '/imp/imp003/content/imp003ContentDiIncluir/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [900,550]);
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

case 'imp003FormCadastroLI':
  global $g;
  /**
     * PARTE 1: Variavéis envidas para o formulário
     */
    // Variável indica qual tab do form será exibida
    $tab = (isset($r_tab)) ? $r_tab : false;
   
	$T075_Id = isset($r_T075_Id) ? $r_T075_Id : false;

		log("--------------------acaoID:".$r_acaoId);
		log("*************T270_Id:".$T075_id);
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
    $enviar_btnId = 'imp003FormCadastroLIBtn';
    // Form que será executado ao clicar no botão
    $enviar_acaoHref = '/imp/imp003/form/imp003FormCadastroLI/';

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
	log("T270:".$T270_Id);
    $formArray = array(
      'campos' => array(
	        'geral' => array(
		          // Campo normal (do banco)
					'T270_T075_Id' => array('name' => 'T270_T075_Id', 'label' => 'Descrição', 'value'=>@$T075_Id, 'type' => 'hidden', 'length' => array(2,60)),	          
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
	
    $form = new Formulario('form-imp003FormLI');
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
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Alterar LI - ID: ' + '{$acaoId}', '/imp/imp003/content/imp003ContentLIIncluir/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [900,550]);
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
    log("Cadastro LI");
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
	$botoesDir .= '<button type="submit" id="imp003FormCadastroLIBtn">Enviar</button>';
	$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
	$g['smarty']->assign('botoesId', $g['divId']);
	$g['smarty']->assign('botoesEsq', $botoesEsq);
	$g['smarty']->assign('botoesMid', $botoesMid);
	$g['smarty']->assign('botoesDir', $botoesDir);
	$g['smarty']->display('buttons-floatBottom.tpl');


  break;

case 'imp003FormImportarCSV':
	
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
	$form_tabela = 'T055';
	$form_campo_pk = 'T055_Id';
	// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
	$enviar_btnId = 'imp003FormImportarCSV_btn';
	// Form que será executado ao clicar no botão
	$enviar_acaoHref = '/imp/imp003/form/imp003FormImportarCSV/';
	// Array de definição do formulário
	// Veja documentação ou exemplos para entender todas as variavéis de definição disponíveis (automacaoFormulario.php)
	$form = new Formulario('imp003FormImportarCSV');
	$formArray = array(
		'campos' => array(
			'geral' => array(
			)
		)
	);
	$formArray['campos']['geral']['uploadArquivoCSVIMP003'] 		= array('name' => 'uploadArquivoCSVIMP003', 'label' => 'Selecione o CSV', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
	$formArray['campos']['geral']['imp003FormImportarCSV_btn'] 		= array('name' => 'imp003FormImportarCSV_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
	//$formArray['campos']['geral']['T055_Arquivo_Csv'] 				= array('name' => 'T055_Arquivo_Csv', 'type' => 'hidden');
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
			$arquivo = $form->campoValorEnviado('uploadArquivoCSVIMP003');
			$retorno = $IMP003->importarCSV($arquivo, $acaoId);
				
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

case 'imp003FormImportarXML':
	
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
	$form_tabela = 'T075';
	$form_campo_pk = 'T075_Id';
	// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
	$enviar_btnId = 'imp003FormImportarXML_btn';
	// Form que será executado ao clicar no botão
	$enviar_acaoHref = '/imp/imp003/form/imp003FormImportarXML/';
	// Array de definição do formulário
	// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
	$form_editor = array(
		'geral' => '{"uploadArquivoXMLImp003":{"top":7,"left":57.015625,"width":233,"hidden":0},"imp003FormImportarXML_btn":{"top":5,"left":416,"width":null,"hidden":0},"incluirItens":{"top":30,"left":14.625,"width":13,"hidden":0}}');
	$form = new Formulario('imp003FormImportarXML');
	$formArray = array(
		'campos' => array(
			'geral' => array(
			)
		)
	);
	$formArray['campos']['geral']['uploadArquivoXMLImp003'] 		= array('name' => 'uploadArquivoXMLImp003', 'label' => 'Selecione o XML', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
	$formArray['campos']['geral']['imp003FormImportarXML_btn'] 		= array('name' => 'imp003FormImportarXML_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
	$formArray['campos']['geral']['T075_Arquivo_XML'] 				= array('name' => 'T075_Arquivo_XML', 'type' => 'hidden');
	$formArray['campos']['geral']['incluirItens']					= array('name' => 'incluirItens', 'label' => 'Importar os ítens do XML', 'type' => 'checkbox', 'checked' => '', 'reject' => true);
	$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
	$formArray['campos']['geral']['T075_Id'] 						= array('name' => 'T075_Id', 'type' => 'hidden','value' => @$acaoId);
	
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
			$arquivo      = $form->campoValorEnviado('uploadArquivoXMLImp003');
			$incluirItens = $form->campoValorEnviado('incluirItens');
			$retorno      = $IMP003->prepararImportarXML($arquivo, $incluirItens, $acaoId);
			
			if($retorno !== true){
				//$retorno = htmlentities(utf8_decode($retorno));
				$retorno = htmlentities($retorno);
				echo gGeraAlertMsg('Relatório de Importação', '<textarea style="width:900px; height:400px; text-transform: none;" class="itemForm itemForm-arrayColunas formInput ui-state-default ui-corner-all">' . $retorno . '</textarea>');
			}else{
				// Imprime uma mensagem
				echo gGeraAlertMsg('Operação OK', 'Importado dados da D.I com sucesso!');
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						fecharJanela("{$g['divId']}");
						divRefresh('{$g['divIdParent']}', true);
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
	$g['smarty']->display('formEditor.tpl');
break;

	case 'imp003FormAssociarItemProduto':
		global $g;
		require_once("prog/imp/imp003/form_func-js.php");
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T055_Id = (isset($r_T055_Id)) ? $r_T055_Id : false;

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
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp003FormAssociarItemProduto_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormAssociarItemProduto/';

		$form_editor = array(
			'geral' => '{"Procura_Produto_Associar":{"top":7,"left":3.75,"width":602,"hidden":0}}',
        );

		$filtro = '';
        if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
              $filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
        }else{
              $filtro = '';
        }
		
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where (D009_C004_Id = '{$g['empresaAtual']}' AND D001_Flag_Ativo != 'N')
			{$filtro}
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																								// tabela
			'D009_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D037_Unidade,D009_Quantidade_Estoque',// campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Código,Descrição,Marca,Un,Estoque',																// descrição dos campos
			'campoProcuraProdutoAssociarItem',																		// callback js
			$g['divId'],																						// divId
			''																									// parametro extra
		);
		
		// novo formulário
		$form = new Formulario('imp003FormAssociarItemProduto');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto_Associar' => array('name' => 'Procura_Produto_Associar' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(55,50)),
					// Campos Hidden
					'D001_Codigo_Produto' => array('name' => 'D001_Codigo_Produto', 'label' => 'D001_Codigo_Produto', 'type' => 'hidden', 'restricao' => false),
					'D009_Id' => array('name' => 'D009_Id', 'label' => 'D009_Id', 'type' => 'hidden'),
					'T055_Id' => array('name' => 'T055_Id', 'label' => 'T055_Id','value' => $T055_Id, 'type' => 'hidden'),
				),
			)
		);
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
				// Gerar Boleto
				//$form->campoValorEnviado(T117_D024_Id)
				$T055_Id = $form->campoValorEnviado('T055_Id');
				$retorno = $IMP003->associarProduto($form);
				if ($retorno === true) {
					$alert = gGeraAlertMsg('Operação OK', 'Produto associado com sucesso!.', false);
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						fecharJanela("{$g['divId']}");
						divRefresh('{$g['divIdParent']}', true);
					} </script></div>
EOT;
				} else {
					echo gGeraAlertMsg('Error', 'Erro:<br />' . $retorno);
				}
			}
		}


		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
        /**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="imp003PreCadastrarProdutoBtn">Pré-Cadastrar produto</button>';
		$botoesDir .= '<button type="submit" id="imp003FormAssociarItemProduto_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		//$botoesDir .= '<button onclick="">Novo</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#imp003PreCadastrarProdutoBtn').unbind('click').bind('click', function(data) {
				//var id = $('#{$g['divId']} #T003_D024_Id').val();
				abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-Cadastrar Produto', '/est/est008/content/est008ContentPreCadastrarProduto/','&T059_Id=' + encodeURIComponent('{$array['todosCampos']['T059_Id']}') + '&T010_Id=' + encodeURIComponent('{$array['todosCampos']['T010_Id']}'), [550,400]);
				return false;
			});
		} </script></div>
EOT;

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'imp003FormPacoteDespesas':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T075_Id         = (isset($r_T075_Id)) ? $r_T075_Id : false;
		$excluirDespesas = (isset($r_excluirDespesas)) ? $r_excluirDespesas : false;

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
		$form_tabela = 'D190';
		$form_campo_pk = 'D190_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'imp003FormPacoteDespesas_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/imp/imp003/form/imp003FormPacoteDespesas/';

		$form_editor = array(
			'geral' => '{"D190_Id":{"top":7,"left":5,"width":402,"hidden":0}}',
        );

		// novo formulário
		$form = new Formulario('imp003FormAssociarItemProduto');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D190_Id' => array('name' => 'D190_Id', 'label' => 'Pacote', 'type' => 'select', 'select' =>
						gGeraSelect('D190', 'D190_Id', 'D190_Tipo_Despesa',"ORDER BY D190_Tipo_Despesa"),
					),						
				),
			)
		);
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

				$D190_Id = $form->campoValorEnviado('D190_Id');
				$retorno = $IMP003->incluirPacoteDespesas($D190_Id,$T075_Id, $excluirDespesas);
				if ($retorno === true) {
					$alert = gGeraAlertMsg('Operação OK', 'Incluído pacote de despesas', false);
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						fecharJanela("{$g['divId']}");
						divRefresh('{$g['divIdParent']}', true);
					} </script></div>
EOT;
				} else {
					echo gGeraAlertMsg('Error', 'Erro:<br />' . $retorno);
				}
			}
		}


		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
        /**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="imp003FormPacoteDespesas_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		//$botoesDir .= '<button onclick="">Novo</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#imp003PreCadastrarProdutoBtn').unbind('click').bind('click', function(data) {
				//var id = $('#{$g['divId']} #T003_D024_Id').val();
				abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-Cadastrar Produto', '/est/est008/content/est008ContentPreCadastrarProduto/','&T059_Id=' + encodeURIComponent('{$array['todosCampos']['T059_Id']}') + '&T010_Id=' + encodeURIComponent('{$array['todosCampos']['T010_Id']}'), [550,400]);
				return false;
			});
		} </script></div>
EOT;

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;
}
