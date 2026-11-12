<?php
namespace hardness;
// Classes
require_once('bibliotecas/classes/FIN001.php');
$FIN001 = new FIN001();
// Carrega funções callback
require_once('prog/fin/fin001/grid_func-callback.php');
// Carrega funções do ajax
require_once('prog/fin/fin001/grid_func-js.php');

/**
 * Ações
 */
switch($g['r'][3]) {
	/**
	* Grid Principal
	* Chamado em: fin001tab01
	*/
	case 'fin001grid01':
		global $importacaoDados;
		
		$importacaoDados = true;		
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
		
		$gridModelos = array(
    		'COMISSOES' => 'COMISSÕES',
			'Contas' => 'Contas',
			'Vencimento' => 'Vencimento',
			'Cliente' => 'Cliente',
    	);
		
		/**
		* Definição do Grid (Tabela)
		*/
    	if (modoModelo('COMISSOES')) {
			$grid = array(
				array('colunaValor' => 'validaCampoExtratoCliente', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%; font-size:11px', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T002_Numero_Documento', 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center', 'callback' => 'gMostraMaisNotificacao', 'callbackParameter' => array('15', ','), 'style' => 'width:1%'),
	            array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'T002_D024_Id', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
				array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id','colunaExibir' => false),
	            array('colunaValor' => 'D024_Cnpj', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
	            array('colunaValor' => 'T002_Codigo_Cliente', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'left', 'style' => 'width: 1%', 'colunaExibir' => false),
	            array('colunaValor' => 'substr(D024_Nome_Empresa,1,40)', 'colunaTitulo' => 'Empresa','colunaDimensao' => 'S'),
	            array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'N.Dupl.', 'callback' => 'validaCampoDuplicata', 'callbackParameter' => array('15', ',', 'T002_Observacao_Reparcelamento'), 'style' => 'width:1%;'),
	            array('colunaValor' => 'concat(T002_Numero_Parcela,\'/\',T002_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
	            array('colunaValor' => 'datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prz', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'if(T002_T002_Id_Agrupado > 0, 0 ,datediff(if(T002_Data_Recebimento=\'0000-00-00\',curdate(),T002_Data_Recebimento),T002_Data_Vencimento)) as Atraso', 'colunaTitulo' => 'Ats', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Baixa', 'colunaTitulo' => 'Baixa', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaTotal' => true),
	            array('colunaValor' => 'T002_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaTotal' => true),
	            array('colunaValor' => 'T002_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaTotal' => true),
	            array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaTotal' => true),
	            array('colunaValor' => 'T002_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaTotal' => true),
	            array('colunaValor' => 'T002_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaTotal' => true),
	            array('colunaValor' => 'T002_Valor_Total_Cheques', 'colunaTitulo' => 'Cheques', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Percentual_Comissao', 'colunaTitulo' => '%Com', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Comissao', 'colunaTitulo' => 'Comissão', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaTotal' => true),
	            array('colunaValor' => 'T002A_Percentual_Comissao_Supervisor', 'colunaTitulo' => '%Comis Sup.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaExibir' => true),
	            array('colunaValor' => 'T002A_Valor_Total_Comissao_Supervisor', 'colunaTitulo' => 'Comis. Sup', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaExibir' => true, 'colunaTotal' => true),
				array('colunaValor' => 'T002_Percentual_Comissao_Representante', 'colunaTitulo' => '% Comis. Rep.', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T002_Valor_Comissao_Representante', 'colunaTitulo' => 'Comis. Rep.', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaAlinhar' => 'right', 'colunaTotal' => true),
				array('colunaValor' => 'T002_D021_Id', 'colunaTitulo' => 'Representante', 'colunaEditarGeraSelect' => array('D021', 'D021_Id', 'D021_Nome_Empresa', "ORDER BY D021_Nome_Empresa", true), 'callback' => 'campoMostraValor', 'callbackParameter' => 'D021_Nome_Empresa', 'colunaEditar' => true, 'colunaEditarTodos' => true),
	            array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante', 'colunaExibir' => false),
	            array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Observacao', 'colunaTitulo' => 'Observação'),
	            array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	            array('colunaValor' => 'D046_Nome', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_T045_Id', 'colunaTitulo' => 'Lote', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Status', 'colunaTitulo' => 'Status', 'callback' => 'fin001CampoStatus', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Baixar_Lote', 'colunaTitulo' => 'Lote', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Agrupamento', 'colunaTitulo' => 'Agrupamento', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Enviar_Banco', 'colunaTitulo' => '', 'colunaExibir' => false),
	            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
	            array('colunaValor' => 'T117_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Reparcelar', 'colunaTitulo' => 'Reparcelar', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Cancelada', 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
	            array('colunaValor' => 'T035_Data_Processamento', 'colunaTitulo' => 'Processamento', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Agrupado_Reparcelado', 'colunaTitulo' => 'Agrupado/Reparcelado', 'colunaExibir' => false),
	            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Tipo portador', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_T002_Id_Agrupado', 'colunaTitulo' => 'T002_T002_Id_Agrupado', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Observacao_Reparcelamento', 'colunaTitulo' => 'T002_Observacao_Reparcelamento', 'colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0) as Atrasado', 'colunaTitulo' => 'Atrasado', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0) as aReceber', 'colunaTitulo' => 'A receber','colunaCasasDecimais' => '2','colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00",T002_Valor_Saldo,0) as totalReceber', 'colunaTitulo' => 'A receber','colunaCasasDecimais' => '2','colunaExibir' => false),
	        );
		} else if(modoModelo('Contas')){
			$grid = array(
	            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'validaCampoExtratoCliente', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%; font-size:11px', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T002_Numero_Documento', 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center', 'callback' => 'gMostraMaisNotificacao', 'callbackParameter' => array('15', ','), 'style' => 'width:1%'),
	            array('colunaValor' => 'T002_T035_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Enviar_Banco', 'colunaTitulo' => 'Rm', 'callback' => 'validaCampoRm', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false, 'style' => 'width:1%'),
	            array('colunaValor' => 'T002_Flag_Baixar_Lote', 'colunaTitulo' => 'Bx', 'callback' => 'validaCampoBx', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false, 'style' => 'width:1%', 'colunaExibir' => true),
	            array('colunaValor' => 'T002_Flag_Reparcelar', 'colunaTitulo' => 'Rp', 'callback' => 'validaCampoRp', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false, 'style' => 'width:1%', 'colunaExibir' => true),
	            array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'T002_D024_Id', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
	            array('colunaValor' => 'D024_Cnpj', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
				array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id','colunaExibir' => false),
				array('colunaValor' => 'T002_Codigo_Cliente', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
	            array('colunaValor' => 'substr(D024_Nome_Empresa,1,40)', 'colunaTitulo' => 'Empresa', 'colunaDimensao' => 'S'),
	            array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'N.Dupl.', 'callback' => 'validaCampoDuplicata', 'callbackParameter' => array('15', ',', 'T002_Observacao_Reparcelamento'), 'style' => 'width:1%;'),
	            array('colunaValor' => 'concat(T002_Numero_Parcela,\'/\',T002_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
	            array('colunaValor' => 'datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prz', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
				array('colunaValor' => 'if(T002_T002_Id_Agrupado > 0, 0 ,datediff(if(T002_Data_Recebimento=\'0000-00-00\',curdate(),T002_Data_Recebimento),T002_Data_Vencimento)) as Atraso', 'colunaTitulo' => 'Ats', 'style' => 'width:1%;'),
				array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Baixa', 'colunaTitulo' => 'Baixa', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;','colunaTotal' => true),
	            array('colunaValor' => 'T002_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Taxas', 'colunaTitulo' => 'Taxas', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;','colunaTotal' => true),
	            array('colunaValor' => 'T002_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Total_Cheques', 'colunaTitulo' => 'Cheques', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Percentual_Comissao', 'colunaTitulo' => '%Com', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Comissao', 'colunaTitulo' => 'Comissão', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
				array('colunaValor' => 'T002A_Percentual_Comissao_Supervisor', 'colunaTitulo' => '%Com Sup.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaExibir' => false),
	            array('colunaValor' => 'T002A_Valor_Total_Comissao_Supervisor', 'colunaTitulo' => 'Comissão Sup.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;','colunaExibir' => false),
	            array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaDimensao' => 'S', 'style' => 'width:1%;', 'colunaExibir'=>false),
	            array('colunaValor' => 'T002_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('D027', 'D027_Id', 'D027_Portador'), 'callback' => 'T180_D027_Id_Portador', 'style' => 'width:1%'),
	            array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Nosso Número', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Observacao', 'colunaTitulo' => 'Observação'),
	            array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta', 'style' => 'width:1%;'),
				array('colunaValor' => 'D014_SubConta', 'colunaTitulo' => 'Conta', 'colunaDimensao' => 'S', 'colunaExibir' => false),
	            array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	            array('colunaValor' => 'D046_Nome', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_T045_Id', 'colunaTitulo' => 'Lote', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Status', 'colunaTitulo' => 'Status', 'callback' => 'fin001CampoStatus', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Agrupamento', 'colunaTitulo' => 'Agrupamento', 'colunaExibir' => false),
	            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
	            array('colunaValor' => 'T117_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Cancelada', 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
	            array('colunaValor' => 'T035_Data_Processamento', 'colunaTitulo' => 'Processamento', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Agrupado_Reparcelado', 'colunaTitulo' => 'Agrupado/Reparcelado', 'colunaExibir' => false),
	            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Tipo portador', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_T002_Id_Agrupado', 'colunaTitulo' => 'T002_T002_Id_Agrupado', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Observacao_Reparcelamento', 'colunaTitulo' => 'T002_Observacao_Reparcelamento', 'colunaExibir' => false),
	            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_T002_Id_Agrupado <=0 and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0) as Atrasado', 'colunaTitulo' => 'Atrasado', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_T002_Id_Agrupado <=0 and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0) as aReceber', 'colunaTitulo' => 'A receber','colunaCasasDecimais' => '2','colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_T002_Id_Agrupado <=0,T002_Valor_Saldo,0) as totalReceber', 'colunaTitulo' => 'A receber','colunaCasasDecimais' => '2','colunaExibir' => false),
	        );
		} else if(modoModelo('Vencimento')){
			$grid = array(
	            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'validaCampoExtratoCliente', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%; font-size:11px', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T002_Numero_Documento', 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center', 'callback' => 'gMostraMaisNotificacao', 'callbackParameter' => array('15', ','), 'style' => 'width:1%'),
	            array('colunaValor' => 'T002_T035_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Enviar_Banco', 'colunaTitulo' => 'Rm', 'callback' => 'validaCampoRm', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false, 'style' => 'width:1%'),
	            array('colunaValor' => 'T002_Flag_Baixar_Lote', 'colunaTitulo' => 'Bx', 'callback' => 'validaCampoBx', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false, 'style' => 'width:1%', 'colunaExibir' => true),
	            array('colunaValor' => 'T002_Flag_Reparcelar', 'colunaTitulo' => 'Rp', 'callback' => 'validaCampoRp', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false, 'style' => 'width:1%', 'colunaExibir' => true),
	            array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'T002_D024_Id', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
	            array('colunaValor' => 'D024_Cnpj', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
				array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id','colunaExibir' => false),
				array('colunaValor' => 'T002_Codigo_Cliente', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
	            array('colunaValor' => 'substr(D024_Nome_Empresa,1,40)', 'colunaTitulo' => 'Empresa', 'colunaDimensao' => 'S'),
	            array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'N.Dupl.', 'callback' => 'validaCampoDuplicata', 'callbackParameter' => array('15', ',', 'T002_Observacao_Reparcelamento'), 'style' => 'width:1%;'),
	            array('colunaValor' => 'concat(T002_Numero_Parcela,\'/\',T002_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
	            array('colunaValor' => 'datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prz', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
				array('colunaValor' => 'if(T002_T002_Id_Agrupado > 0, 0 ,datediff(if(T002_Data_Recebimento=\'0000-00-00\',curdate(),T002_Data_Recebimento),T002_Data_Vencimento)) as Atraso', 'colunaTitulo' => 'Ats', 'style' => 'width:1%;'),
				array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Baixa', 'colunaTitulo' => 'Baixa', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;','colunaTotal' => true),
	            array('colunaValor' => 'T002_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Taxas', 'colunaTitulo' => 'Taxas', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;','colunaTotal' => true),
	            array('colunaValor' => 'T002_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Total_Cheques', 'colunaTitulo' => 'Cheques', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Percentual_Comissao', 'colunaTitulo' => '%Com', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Comissao', 'colunaTitulo' => 'Comissão', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
				array('colunaValor' => 'T002A_Percentual_Comissao_Supervisor', 'colunaTitulo' => '%Com Sup.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaExibir' => false),
	            array('colunaValor' => 'T002A_Valor_Total_Comissao_Supervisor', 'colunaTitulo' => 'Comissão Sup.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;','colunaExibir' => false),
	            array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaDimensao' => 'S', 'style' => 'width:1%;', 'colunaExibir'=>false),
	            array('colunaValor' => 'T002_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('D027', 'D027_Id', 'D027_Portador'), 'callback' => 'T180_D027_Id_Portador', 'style' => 'width:1%'),
	            array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Nosso Número', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Observacao', 'colunaTitulo' => 'Observação'),
	            array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta', 'style' => 'width:1%;'),
				array('colunaValor' => 'D014_SubConta', 'colunaTitulo' => 'Conta', 'colunaDimensao' => 'S', 'colunaExibir' => false),
	            array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	            array('colunaValor' => 'D046_Nome', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_T045_Id', 'colunaTitulo' => 'Lote', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Status', 'colunaTitulo' => 'Status', 'callback' => 'fin001CampoStatus', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Agrupamento', 'colunaTitulo' => 'Agrupamento', 'colunaExibir' => false),
	            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
	            array('colunaValor' => 'T117_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Cancelada', 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
	            array('colunaValor' => 'T035_Data_Processamento', 'colunaTitulo' => 'Processamento', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Agrupado_Reparcelado', 'colunaTitulo' => 'Agrupado/Reparcelado', 'colunaExibir' => false),
	            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Tipo portador', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_T002_Id_Agrupado', 'colunaTitulo' => 'T002_T002_Id_Agrupado', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Observacao_Reparcelamento', 'colunaTitulo' => 'T002_Observacao_Reparcelamento', 'colunaExibir' => false),
	            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_T002_Id_Agrupado <=0 and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0) as Atrasado', 'colunaTitulo' => 'Atrasado', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_T002_Id_Agrupado <=0 and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0) as aReceber', 'colunaTitulo' => 'A receber','colunaCasasDecimais' => '2','colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_T002_Id_Agrupado <=0,T002_Valor_Saldo,0) as totalReceber', 'colunaTitulo' => 'A receber','colunaCasasDecimais' => '2','colunaExibir' => false),
	        );
		} else if(modoModelo('Cliente')){
			$grid = array(
	            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'validaCampoExtratoCliente', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%; font-size:11px', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T002_Numero_Documento', 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center', 'callback' => 'gMostraMaisNotificacao', 'callbackParameter' => array('15', ','), 'style' => 'width:1%'),
	            array('colunaValor' => 'T002_T035_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Enviar_Banco', 'colunaTitulo' => 'Rm', 'callback' => 'validaCampoRm', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false, 'style' => 'width:1%'),
	            array('colunaValor' => 'T002_Flag_Baixar_Lote', 'colunaTitulo' => 'Bx', 'callback' => 'validaCampoBx', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false, 'style' => 'width:1%', 'colunaExibir' => true),
	            array('colunaValor' => 'T002_Flag_Reparcelar', 'colunaTitulo' => 'Rp', 'callback' => 'validaCampoRp', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false,'style' => 'width:1%', 'colunaExibir' => true),
	            array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'T002_D024_Id', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
	            array('colunaValor' => 'D024_Cnpj', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
				array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id','colunaExibir' => false),
				array('colunaValor' => 'T002_Codigo_Cliente', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
	            array('colunaValor' => 'substr(D024_Nome_Empresa,1,40)', 'colunaTitulo' => 'Empresa', 'colunaDimensao' => 'S'),
	            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'N.Dupl.', 'callback' => 'validaCampoDuplicata', 'callbackParameter' => array('15', ',', 'T002_Observacao_Reparcelamento'), 'style' => 'width:1%;'),
	            array('colunaValor' => 'concat(T002_Numero_Parcela,\'/\',T002_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
	            array('colunaValor' => 'datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prz', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
				array('colunaValor' => 'if(T002_T002_Id_Agrupado > 0, 0 ,datediff(if(T002_Data_Recebimento=\'0000-00-00\',curdate(),T002_Data_Recebimento),T002_Data_Vencimento)) as Atraso', 'colunaTitulo' => 'Ats', 'style' => 'width:1%;'),
				array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Baixa', 'colunaTitulo' => 'Baixa', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;','colunaTotal' => true),
	            array('colunaValor' => 'T002_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Taxas', 'colunaTitulo' => 'Taxas', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;','colunaTotal' => true),
	            array('colunaValor' => 'T002_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Total_Cheques', 'colunaTitulo' => 'Cheques', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Percentual_Comissao', 'colunaTitulo' => '%Com', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Comissao', 'colunaTitulo' => 'Comissão', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
				array('colunaValor' => 'T002A_Percentual_Comissao_Supervisor', 'colunaTitulo' => '%Com Sup.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaExibir' => false),
	            array('colunaValor' => 'T002A_Valor_Total_Comissao_Supervisor', 'colunaTitulo' => 'Comissão Sup.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;','colunaExibir' => false),
	            array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaDimensao' => 'S', 'style' => 'width:1%;', 'colunaExibir'=>false),
	            array('colunaValor' => 'T002_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('D027', 'D027_Id', 'D027_Portador'), 'callback' => 'T180_D027_Id_Portador', 'style' => 'width:1%'),
	            array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Nosso Número', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Observacao', 'colunaTitulo' => 'Observação'),
	            array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta', 'style' => 'width:1%;'),
				array('colunaValor' => 'D014_SubConta', 'colunaTitulo' => 'Conta', 'colunaDimensao' => 'S', 'colunaExibir' => false),
	            array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	            array('colunaValor' => 'D046_Nome', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_T045_Id', 'colunaTitulo' => 'Lote', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Status', 'colunaTitulo' => 'Status', 'callback' => 'fin001CampoStatus', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Agrupamento', 'colunaTitulo' => 'Agrupamento', 'colunaExibir' => false),
	            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
	            array('colunaValor' => 'T117_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Cancelada', 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
	            array('colunaValor' => 'T035_Data_Processamento', 'colunaTitulo' => 'Processamento', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Agrupado_Reparcelado', 'colunaTitulo' => 'Agrupado/Reparcelado', 'colunaExibir' => false),
	            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Tipo portador', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_T002_Id_Agrupado', 'colunaTitulo' => 'T002_T002_Id_Agrupado', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Observacao_Reparcelamento', 'colunaTitulo' => 'T002_Observacao_Reparcelamento', 'colunaExibir' => false),
	            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_T002_Id_Agrupado <=0 and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0) as Atrasado', 'colunaTitulo' => 'Atrasado', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_T002_Id_Agrupado <=0 and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0) as aReceber', 'colunaTitulo' => 'A receber','colunaCasasDecimais' => '2','colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_T002_Id_Agrupado <=0,T002_Valor_Saldo,0) as totalReceber', 'colunaTitulo' => 'A receber','colunaCasasDecimais' => '2','colunaExibir' => false),
	        );
		} else {
			$grid = array(
	            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'validaCampoExtratoCliente', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%; font-size:11px', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T002_Numero_Documento', 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center', 'callback' => 'gMostraMaisNotificacao', 'callbackParameter' => array('15', ','), 'style' => 'width:1%'),
	            array('colunaValor' => 'T002_T035_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Enviar_Banco', 'colunaTitulo' => 'Rm', 'callback' => 'validaCampoRm', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false, 'style' => 'width:1%'),
	            array('colunaValor' => 'T002_Flag_Baixar_Lote', 'colunaTitulo' => 'Bx', 'callback' => 'validaCampoBx', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false, 'style' => 'width:1%', 'colunaExibir' => true),
	            array('colunaValor' => 'T002_Flag_Reparcelar', 'colunaTitulo' => 'Rp', 'callback' => 'validaCampoRp', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false, 'style' => 'width:1%', 'colunaExibir' => true),
	            array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'T002_D024_Id', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
	            array('colunaValor' => 'D024_Cnpj', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
				array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id','colunaExibir' => false),
				array('colunaValor' => 'T002_Codigo_Cliente', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
	            array('colunaValor' => 'substr(D024_Nome_Empresa,1,40)', 'colunaTitulo' => 'Empresa', 'colunaDimensao' => 'S'),
	            array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'N.Dupl.', 'callback' => 'validaCampoDuplicata', 'callbackParameter' => array('15', ',', 'T002_Observacao_Reparcelamento'), 'style' => 'width:1%;'),
	            array('colunaValor' => 'concat(T002_Numero_Parcela,\'/\',T002_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
	            array('colunaValor' => 'datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prz', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
				array('colunaValor' => 'if(T002_T002_Id_Agrupado > 0, 0 ,datediff(if(T002_Data_Recebimento=\'0000-00-00\',curdate(),T002_Data_Recebimento),T002_Data_Vencimento)) as Atraso', 'colunaTitulo' => 'Ats', 'style' => 'width:1%;'),
				array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Data_Baixa', 'colunaTitulo' => 'Baixa', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Taxas', 'colunaTitulo' => 'Taxas', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Total_Cheques', 'colunaTitulo' => 'Cheques', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Percentual_Comissao', 'colunaTitulo' => '%Com', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Valor_Comissao', 'colunaTitulo' => 'Comissão', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
				array('colunaValor' => 'T002A_Percentual_Comissao_Supervisor', 'colunaTitulo' => '%Com Sup.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaExibir' => false),
	            array('colunaValor' => 'T002A_Valor_Total_Comissao_Supervisor', 'colunaTitulo' => 'Comissão Sup.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;','colunaExibir' => false),
	            array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaDimensao' => 'S', 'style' => 'width:1%;', 'colunaExibir'=>false),
	            array('colunaValor' => 'T002_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('D027', 'D027_Id', 'D027_Portador'), 'callback' => 'T180_D027_Id_Portador', 'style' => 'width:1%'),
	            array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Nosso Número', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T002_Observacao', 'colunaTitulo' => 'Observação'),
	            array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
	            array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta', 'style' => 'width:1%;'),
				array('colunaValor' => 'D014_SubConta', 'colunaTitulo' => 'Conta', 'colunaDimensao' => 'S', 'colunaExibir' => false),
	            array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	            array('colunaValor' => 'D046_Nome', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_T045_Id', 'colunaTitulo' => 'Lote', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Status', 'colunaTitulo' => 'Status', 'callback' => 'fin001CampoStatus', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Agrupamento', 'colunaTitulo' => 'Agrupamento', 'colunaExibir' => false),
	            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
	            array('colunaValor' => 'T117_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Cancelada', 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
	            array('colunaValor' => 'T035_Data_Processamento', 'colunaTitulo' => 'Processamento', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Flag_Agrupado_Reparcelado', 'colunaTitulo' => 'Agrupado/Reparcelado', 'colunaExibir' => false),
	            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Tipo portador', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_T002_Id_Agrupado', 'colunaTitulo' => 'T002_T002_Id_Agrupado', 'colunaExibir' => false),
	            array('colunaValor' => 'T002_Observacao_Reparcelamento', 'colunaTitulo' => 'T002_Observacao_Reparcelamento', 'colunaExibir' => false),
	            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_T002_Id_Agrupado <=0 and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0) as Atrasado', 'colunaTitulo' => 'Atrasado', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_T002_Id_Agrupado <=0 and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0) as aReceber', 'colunaTitulo' => 'A receber','colunaCasasDecimais' => '2','colunaExibir' => false),
				array('colunaValor' => 'IF(T002_Data_Recebimento = "0000-00-00" and T002_T002_Id_Agrupado <=0,T002_Valor_Saldo,0) as totalReceber', 'colunaTitulo' => 'A receber','colunaCasasDecimais' => '2','colunaExibir' => false),
	        );
		}
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
		-- JOIN
			LEFT JOIN D024 ON D024_Id=T002_D024_Id
			LEFT JOIN D027 ON D027_Id=T002_D027_Id
			LEFT JOIN T117 ON T117_Id=T002_T117_Id
			LEFT JOIN D014 ON D014_Id=T002_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			LEFT JOIN D032 ON D032_Id=T002_D032_Id
			LEFT JOIN D046 ON D046_Id=D024_D046_Id
			LEFT JOIN T007 ON T007_Id=T002_T007_Id
			LEFT JOIN T035 ON T035_Id=T002_T035_Id
			LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
			LEFT JOIN T103 ON T002_Id=T103_T002_Id
			LEFT JOIN D021 ON D021_Id=T002_D021_Id
			LEFT JOIN T005 ON T005_Id=T007_T005_Id
			LEFT JOIN D007 ON D027_D007_Id=D007_Id
			LEFT JOIN T002A ON T002_Id=T002A_T002_Id
		-- WHERE
			WHERE (T002_C004_Id={$g['empresaAtual']})
			AND IF('{$g['C031']['exibirTitulosReceberAgrupados']}' = 'S',1=1,(T002_T002_Id_Agrupado <= 0 OR T002_T002_Id_Agrupado IS NULL))
			AND T002_Flag_Cancelada != 'S'
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T002_T002_Id_Agrupado'] > 0){
				return 'linhaLaranja';
			} else if($array['todosCampos']['T002_Data_Recebimento']!='0000-00-00'){
				return 'linhaCinza';
			} else if ($array['todosCampos']['T002_Flag_Status']=='2') {
				return 'linhaVermelho';
			} else if ($array['todosCampos']['T002_Flag_Status']=='3') {
                return 'linhaAzul';
            }
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
            'T002_Numero_Duplicata,T002_Observacao_Reparcelamento' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T002_Valor_Duplicata,T002_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
            'T002_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T002_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
            'T002_Data_Recebimento' => array('titulo' => 'Recebimento', 'tipo' => 'data'),
            'T002_Data_Baixa' => array('titulo' => 'Baixa', 'tipo' => 'data'),
            'D027_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
            'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
            'T117_Nosso_Numero' => array('titulo' => 'Nosso Numero', 'tipo' => 'texto'),
            'C007_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            'T002_T035_Id' => array('titulo' => 'Remessa', 'tipo' => 'numero'),
            'D024_Cnpj' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
            'T002_Flag_Status' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Normal', 'value' => '1'),
                array('title' => 'Cobrança', 'value' => '3'),
                array('title' => 'Perdido', 'value' => '2'),
              ),
              'naoGerarExtra' => false),
            'T002_Flag_Enviar_Banco' => array('titulo' => 'Marcado Rm', 'tipo' => 'texto'),
            'T002_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
            'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
            'T007_Flag_ACP' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
	                array('title' => 'Todos', 'value' => ''),
	                array('title' => 'NF-e', 'value' => '6'),
					array('title' => 'NFC-e', 'value' => '8'),
	                array('title' => 'CF', 'value' => '4'),
	                array('title' => 'ACP', 'value' => '3'),
            	),
            ),
            'IF(T002_T002_Id_Agrupado > 0[[virgula]]"S"[[virgula]]"N")' => array('titulo' => 'Reparcelado', 'tipo' => 'select', 'select' => array(
	                array('title' => '...', 'value' => ''),
	                array('title' => 'SIM', 'value' => 'S'),
	                array('title' => 'NÃO', 'value' => 'N'),
            	),
            ),
        );

		if (modoModelo('COMISSOES')) {
			$gridFiltro['Agrupar'] = array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(array('title' => 'Representante', 'campoAgrupar' => 'D021_Nome_Empresa', 'campoMostrar' => 'D021_Nome_Empresa', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)));
		}

		if (modoModelo('Contas')) {
			$gridFiltro['Agrupar'] = array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(array('title' => 'Contas', 'campoAgrupar' => 'D014_SubConta', 'campoMostrar' => 'D014_SubConta', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)));
		}

		if (modoModelo('Vencimento')) {
			$gridFiltro['Agrupar'] = array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(array('title' => 'Vencimento', 'campoAgrupar' => 'T002_Data_Vencimento', 'campoMostrar' => 'T002_Data_Vencimento', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)));
		}

		if (modoModelo('Cliente')) {
			$gridFiltro['Agrupar'] = array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(array('title' => 'Cliente', 'campoAgrupar' => 'T002_D024_Id', 'campoMostrar' => 'D024_Nome_Empresa', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)));
		}

		

		// Gera o filtro e acrescenta ao $extra
		$extra   = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_  = urlencode(base64_encode(gRetirarOrderBy($extra)));
		$extra__ = base64_encode(gRetirarGroupBy(gRetirarOrderBy($extra)));

		// Não executar quando houver o refresh de linha
		if (empty($r_linhaGridId)) {
			$janelaBaixa         = "abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Baixar', '/fin/fin001/content/fin001content05/', '&extra={$extra_}', 'auto');";
			$janelaBaixaAgrupada = "abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Baixa Agrupada', '/fin/fin001/content/fin001content05/', '&extra={$extra_}', [1550,550]);";

            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin001Vencimento').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar Vencimento', '/fin/fin001/content/fin001ContentAlterarVencimento/', '&extra=' + encodeURIComponent('{$extra__}'), [320,150]);
				});
				$('#fin001Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Receber', '/fin/fin001/content/fin001content02/', '', [1050,500]);
				});			
				$('#fin001Remessa').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.ajax({
						url: '/fin/fin001/grid_func-ajax/verificaRemessa/',
						dataType: 'json',
						data: 'ajax=true&callback=?',
						type: 'POST',
						success: function(request) {
							//$.getJSON('/fin/fin001/grid_func-ajax/verificaRemessa/?ajax=true&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code==0) {
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Gerar Remessa Bancária', '/fin/fin001/content/fin001content03/', '&qteTitulos=' + request.data, [450,250]);
							} else {
								var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}
								dialogConfirm('Remessa', request.data, buttons);							
							}
						}
					});
				});				
				$('#fin001Agrupar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();

					$.ajax({
						url: '/fin/fin001/grid_func-ajax/verificaAgrupar/',
						dataType: 'json',
						data: 'ajax=true&extra={$extra_}&callback=?',
						type: 'POST',
						success: function(request) {

							//$.getJSON('/fin/fin001/grid_func-ajax/verificaAgrupar/?ajax=true&extra={$extra_}&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code==0) {
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Agrupar', '/fin/fin001/content/fin001content04/', '&extra={$extra_}', [700,500]);
							} else {
								var buttons = {
										"Ok": function() { $(this).dialog("destroy").remove(); }
									}		
								dialogConfirm('Agrupamento', request.data, buttons);							
							}
						}
					});
				});
				$('#fin001Baixar').unbind('click').bind('click', function(data) {
				    $('#{$g['divId']}').showLoading();
				    $.ajax({
						url:      '/fin/fin001/grid_func-ajax/verificaBaixar/',
						dataType: 'json',
						data:     'ajax=true&extra={$extra_}&callback=?',
						type:     'POST',
						success: function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code == 0) {
								$.ajax({
									url:      '/fin/fin001/grid_func-ajax/verificaSaldoCredito/',
									dataType: 'json',
									data:     'ajax=true&extra={$extra_}&callback=?',
									type:     'POST',
									success:  function(request) {
										var mensagem = '';
										if (request.code) {
											mensagem = request.data + '<br>';

											var buttonsBaixaCredito = {
												"Baixar": function() {
													$(this).dialog("destroy").remove();
													$('#{$g['divId']}').showLoading();
													$.ajax({
														url:      '/fin/fin001/grid_func-ajax/baixarSaldoCredito/',
														dataType: 'json',
														data:     'ajax=true&extra={$extra_}&callback=?',
														type:     'POST',
														success: function(request) {
															console.log(request);
															$('#{$g['divId']}').hideLoading();
															if (request.code == 0) {
																if (request.data == 2) {
																	var buttonsCredito = {
																		"Ok": function() { $(this).dialog("destroy").remove(); }
																	}
																	setTimeout(function() {
																		dialogConfirm('Baixa de títulos', 'Todos os titulos selecionados foram baixados com crédito do cliente!', buttonsCredito);
																	}, 150); 
																	divRefresh('{$g['divId']}');
																} else {
																	$janelaBaixa
																	var buttonsSaldo = {
																			"Ok": function() { $(this).dialog("destroy").remove(); }
																		}
																	setTimeout(function() {
																		dialogConfirm('Baixa de títulos', 'O saldo de crédito do cliente foi utilizado para baixar parcialmente os titulos selecionados!', buttonsSaldo);
																	}, 150);
																}
															} else {
																var buttonsError = {
																		"Ok": function() { $(this).dialog("destroy").remove(); }
																	}
																dialogConfirm('Baixa de títulos', request.data, buttonsError);
															}
														}
													});
												},
												"Não": function() { 
													$(this).dialog("destroy").remove();
													$janelaBaixa
												 },
												 "Cancelar": function() { $(this).dialog("destroy").remove(); }
											}
											dialogConfirm('Confirmação', "Um ou mais clientes selecionados possuem saldo de crédito que poderá abater o valor do título.<br><br>" + mensagem + "Você deseja utlizar o saldo para baixar o título?", buttonsBaixaCredito);
										} else {
											$janelaBaixa
										}
									}
								});
							} else {
								var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}
								dialogConfirm('Baixa de Títulos', request.data, buttons);
							}
						}
					});
				});
				$('#fin001BaixaAgrupada').unbind('click').bind('click', function(data) {
					var buttonsBaixaAgrupada = {
						"Baixar": function() {
							$(this).dialog("destroy").remove();
							$('#{$g['divId']}').showLoading();
							$.ajax({
								url:      '/fin/fin001/grid_func-ajax/verificaBaixaAgrupada/',
								dataType: 'json',
								data:     'ajax=true&extra={$extra_}&callback=?',
								type:     'POST',
								success: function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code == 0) {
										$.ajax({
											url:      '/fin/fin001/grid_func-ajax/verificaSaldoCredito/',
											dataType: 'json',
											data:     'ajax=true&extra={$extra_}&callback=?',
											type:     'POST',
											success:  function(request) {
												var mensagem = '';
												if (request.code) {
													mensagem = request.data + '<br>';

													var buttonsBaixaCredito = {
														"Baixar": function() {
															$(this).dialog("destroy").remove();
															$('#{$g['divId']}').showLoading();
															$.ajax({
																url:      '/fin/fin001/grid_func-ajax/baixarSaldoCredito/',
																dataType: 'json',
																data:     'ajax=true&extra={$extra_}&callback=?',
																type:     'POST',
																success: function(request) {
																	console.log(request);
																	$('#{$g['divId']}').hideLoading();
																	if (request.code == 0) {
																		if (request.data == 2) {
																			var buttonsCredito = {
																				"Ok": function() { $(this).dialog("destroy").remove(); }
																			}
																			setTimeout(function() {
																				dialogConfirm('Baixa agrupada de títulos', 'Todos os titulos selecionados foram baixados com crédito do cliente!', buttonsCredito);
																			}, 150); 
																			divRefresh('{$g['divId']}');
																		} else {
																			$janelaBaixaAgrupada
																			var buttonsSaldo = {
																					"Ok": function() { $(this).dialog("destroy").remove(); }
																				}
																			setTimeout(function() {
																				dialogConfirm('Baixa agrupada de títulos', 'O saldo de crédito do cliente foi utilizado para baixar parcialmente os titulos selecionados!', buttonsSaldo);
																			}, 150);
																		}
																	} else {
																		var buttonsError = {
																				"Ok": function() { $(this).dialog("destroy").remove(); }
																			}
																		dialogConfirm('Baixa agrupada de títulos', request.data, buttonsError);
																	}
																}
															});
														},
														"Não": function() { 
															$(this).dialog("destroy").remove();
															$janelaBaixaAgrupada
														},
														"Cancelar": function() { $(this).dialog("destroy").remove(); }
													}
													dialogConfirm('Confirmação', "Um ou mais clientes selecionados possuem saldo de crédito que poderá abater o valor do título.<br><br>" + mensagem + "Você deseja utlizar o saldo para baixar o título?", buttonsBaixaCredito);
												} else {
													$janelaBaixaAgrupada
												}
											}
										});
									} else {
										var buttons = {
											"Ok": function() { $(this).dialog("destroy").remove(); }
										}
										dialogConfirm('Baixa agrupada de Títulos', request.data, buttons);
									}
								}
							});
						},
						"Cancelar": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Confirmação', "Este processo irá agrupar os títulos selecionados, gerando um único título para realizar a baixa.<br><br>Você deseja continuar?", buttonsBaixaAgrupada);
				});
				$('#fin001Reparcelar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.ajax({
						url: '/fin/fin001/grid_func-ajax/verificaReparcelar/',
						dataType: 'json',
						data: 'ajax=true&extra={$extra_}&callback=?',
						type: 'POST',
						success: function(request) {
							//$.getJSON('/fin/fin001/grid_func-ajax/verificaReparcelar/?ajax=true&extra={$extra_}&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code == 0) {
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Duplicatas para Reparcelamento', '/fin/fin001/content/fin001ContentPagamento/', '&extra={$extra_}', [900,550]);
							} else {
								var buttons = {
										"Ok": function() { $(this).dialog("destroy").remove(); }
									}
								dialogConfirm('Reparcelar', request.data, buttons);
							}
						}
					});
				});

				$('#fin001Duplicar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin001/grid_func-ajax/verificaDuplicar/?ajax=true&extra={$extra_}&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Duplicar Títulos a Receber', '/fin/fin001/content/fin001ContentDuplicarTitulos/', '&extra={$extra_}', [250,100]);
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Duplicar Títulos', request.data, buttons);
						}
					});
				});

				$('#fin001AgrupamentoAutomatico').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin001/grid_func-ajax/agrupamentoAutomatico/?ajax=true&extra={$extra_}&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Duplicar Títulos', request.data, buttons);
							divRefresh('{$g['divId']}');
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Duplicar Títulos', request.data, buttons);
						}
					});
				});

				
				$('#fin001Dividir').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/fin/fin001/grid_func-ajax/verificaDividir/?ajax=true&extra={$extra_}&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        if (request.code == 0) {
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Duplicatas para Divisão', '/fin/fin001/content/fin001ContentDividir/', '&extra={$extra_}', [1000,600]);
                        } else {
                            var buttons = {
                                    "Ok": function() { $(this).dialog("destroy").remove(); }
                                }
                            dialogConfirm('Reparcelar', request.data, buttons);
                        }
                    });
                });

			} </script></div>
EOT;
        }

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*
		if (!modoModelo('COMISSOES')) {
			if (!personalizacaoBotoes()) {
				echo <<<EOT
					<div class="buttonsBottomTop">
						<button type="button" id="fin001Incluir">Incluir</button>
						<button type="button" id="fin001Remessa">Remessa</button>
						<button type="button" id="fin001Baixar">Baixar</button>
						<button type="button" id="fin001Agrupar">Agrupar</button>
						<button type="button" id="xfin001Reparcelar">Reparcelar</button>
						<!--<button type="button" id="fin001Vencimento">Vencimento</button>-->
						<!--<button type="button" id="fin001AgrupamentoAutomatico">Agrupamento Automático</button>-->
						<!--<button type="button" id="fin001Duplicar">Duplicar</button>-->
					</div>
EOT;
			}
		}*/

        // TODO: remover teste
		// Botões
        $botoes = array(
            array('titulo' => 'Incluir', 'id' => 'fin001Incluir'),
            array('titulo' => 'Remessa', 'id' => 'fin001Remessa'),
            array('titulo' => 'Baixar', 'id' => 'fin001Baixar'),
			array('titulo' => 'Baixar Agrupado', 'id' => 'fin001BaixaAgrupada'),
            array('titulo' => 'Agrupar', 'id' => 'fin001Agrupar'),
            array('titulo' => 'Reparcelar', 'id' => 'fin001Reparcelar'),
			array('titulo' => 'Dividir', 'id' => 'fin001Dividir'),
            //array('titulo' => 'Vencimento', 'id' => 'fin001Vencimento'),
            //array('titulo' => 'Agrupamento automatico', 'id' => 'fin001AgrupamentoAutomatico'),
            //array('titulo' => 'Duplicar', 'id' => 'fin001Duplicar'),
        );
        echo Botoes::processar($botoes);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor_Duplicata', 'valor' => 'if(T002_T002_Id_Agrupado > 0,0,T002_Valor_Duplicata)', 'titulo' => 'Valor'),
			array('nome' => 'Valor_Juros', 'valor' => 'if(T002_T002_Id_Agrupado > 0,0,T002_Valor_Juros)', 'titulo' => 'Juros'),
			array('nome' => 'Valor_Desconto', 'valor' => 'if(T002_T002_Id_Agrupado > 0,0,T002_Valor_Desconto)', 'titulo' => 'Desconto'),
			array('nome' => 'Valor_Taxa', 'valor' => 'if(T002_T002_Id_Agrupado > 0,0,T002_Valor_Taxas)', 'titulo' => 'Taxa'),
			array('nome' => 'Valor_Total', 'valor' => 'if(T002_T002_Id_Agrupado > 0,0,T002_Valor_Total)', 'titulo' => 'Total'),
			array('nome' => 'Valor_Recebido', 'valor' => 'if(T002_T002_Id_Agrupado > 0,0,T002_Valor_Recebido)', 'titulo' => 'Recebido'),
			array('nome' => 'Valor_Atrasado', 'valor' => 'Atrasado', 'titulo' => 'Atrasado'),
			array('nome' => 'Valor_Receber', 'valor' => 'aReceber', 'titulo' => 'A Receber'),
			array('nome' => 'Valor_Total_Receber', 'valor' => 'totalReceber', 'titulo' => 'Total Receber'),
			array('nome' => 'Valor_Comissao', 'valor' => 'if(T002_T002_Id_Agrupado > 0,0,T002_Valor_Comissao)', 'titulo' => 'Comissão'),			
		);

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		 /**
		 * Totalizadores
		 */
		$extra = gInsertExtraWhere($extra, "(T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)");

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Remessa<br><input type="text" class="whiteBg" id="inputRemessa" style="width:80px" readonly></span>
				<span class="item" style="text-align:right">Baixar<br><input type="text" class="whiteBg" id="inputBaixar" style="width:80px" readonly></span>
				<span class="item" style="text-align:right">Reparcelar<br><input type="text" class="whiteBg" id="inputReparcelar" style="width:80px" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin001ExtraBtns').data('extra', '{$extra__}');
				carregaInputBaixar();
				carregaInputReparcelar();
				carregaInputRemessa();
			} </script></div>
EOT;
		//<span class="item" style="text-align:right">Agrupar<br><input type="text" class="whiteBg" id="inputAgrupar" size="11" readonly></span>
		//carregaInputAgrupar();

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				acaoClickLinha('{$g['divId']}', function(data, array) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID : ' + array['todosCampos']['T002_Id'] , '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T002_Id']) + '&tabela={$from}', [1050,540]);
				});
			} </script></div>
EOT;
		
		/**
		 * Geração: Grid
		 */		
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('totais', $totaisHTML);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); };

	break;
	
	case 'fin001GridRemessas':

			
		 /**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'excluirLinha1', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T035_Id', $g['divId']), 'colunaExibir' => false),
			array('colunaValor' => "excluirLinha2", 'colunaTipo' => "livre", 'callback' => 'campoExclusaoRemessa', 'style'=>'width:1%;'),
			array('colunaValor' => "T035_Id", 'colunaTitulo' => 'Id','chavePrimaria'=>true,'callback' => 'campoDownloadRemessa1', 'colunaAlinhar' => 'center' ),			
			array('colunaValor' => "D007_Nome_Banco", 'colunaTitulo' => 'Banco', 'style' => 'width:1%', 'callback' => 'campoDownloadRemessa6'),
			array('colunaValor' => "T035_Data_Processamento", 'colunaTitulo' => 'Data processamento','callback' => 'campoDownloadRemessa2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "T035_Nome_Arquivo", 'colunaTitulo' => 'Nome Arquivo', 'callback' => 'campoDownloadRemessa3'),
			array('colunaValor' => "T035_Numero_Titulos", 'colunaTitulo' => 'Total Títulos','callback' => 'campoDownloadRemessa4'),
			array('colunaValor' => "T035_Total_Remessa", 'colunaTitulo' => 'Valor remessa R$','callback' => 'campoDownloadRemessa5'),
		);
			
		/**
		* Definição da SQL
		*/
		$from = 'T035';
		
		$extra = <<<EOT
		LEFT JOIN D007 ON D007_Id=T035_D007_Id
		WHERE (T035_C004_Id='{$g['empresaAtual']}')
EOT;

		/*
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T035_Id-/, '');
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin001/grid_func-ajax/downloadRemessa/?T002_T035_Id=' + encodeURIComponent(id) + 'ajax=true&callback=?', function(request) {
					$('#{$g['divId']}').hideLoading();
				});
			});
		} </script></div>
EOT;
*/
		
		
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T035_Numero_Titulos']<=0){
				return 'linhaVermelho';
			}
		}		
		
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);
    
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T035_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Remessa', 'value' => '1'),
                array('title' => 'Retorno', 'value' => '2'),
            ), 'naoGerarExtra' => false, 'exibir' => false),
            'T035_Data_Processamento' => array('titulo' => 'Data processamento', 'tipo' => 'data'),        

		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		

		/**
		 * Geração: Grid
		 */
		 $gridTabela = $g['ui']->grid($grid, $geradoDados);
         $g['smarty']->assign('thead', $gridTabela['thead']);
         $g['smarty']->assign('tbody', $gridTabela['tbody']);
         $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
         gBotaoAuditoria($from);
		 if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); } 

     break;
	/**
	* Demostrativo de Acrescimos e Descontos
	* Chamado em: fin001form02
	*/
	case 'fin001grid02':
			
		 /**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'campoExclusaoAd'),
			array('colunaValor' => "T020_Id", 'colunaTitulo' => 'ID','chavePrimaria'=>true),			
			array('colunaValor' => "T020_Data_Lancamento", 'colunaTitulo' => 'Data','callback' => 'gCorrigeData'),
			array('colunaValor' => "T020_Historico", 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => "T020_Valor", 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => "T020_Flag_Deb_Cred", 'colunaTitulo' => 'Tipo','extra'=>'style="text-align:center"'),
			//
			array('colunaValor' => "T020_T002_Id", 'colunaExibir'=>false),
		);
			
		/**
		* Definição da SQL
		*/
		$from = 'T020';
		
		$extra = <<<EOT
				-- WHERE
					where T020_T002_Id={$_REQUEST['acaoId']}
				-- ORDER BY
					order by T020_Data_Lancamento
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Geração: Grid
		 */
		 $gridTabela = $g['ui']->grid($grid, $geradoDados);
         $g['smarty']->assign('thead', $gridTabela['thead']);
         $g['smarty']->assign('tbody', $gridTabela['tbody']);
         $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); } 
     break;
	 
	 /**
	 * Demonstrativo de Recebimentos
	 * Chamado em: fin001form03
	 */
	 case 'fin001grid03':
	 
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "T019_Id", 'colunaTitulo' => 'ID'),
			array('colunaValor' => "T019_Data_Lancamento", 'colunaTitulo' => 'Data','callback' => 'gCorrigeData'),
			array('colunaValor' => "Descricao",'colunaTipo'=>'livre', 'colunaTitulo' => 'Descrição','callback' => 'defineDescricao'),
			array('colunaValor' => "T019_Valor_Lancamento", 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => "D027_Portador", 'colunaExibir'=>false),		
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T019';
		
		$extra = <<<EOT
					left join D027 on D027_Id=T019_D027_Id
					where T019_T002_Id={$_REQUEST['acaoId']}
					order by T019_Data_Lancamento
EOT;
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;	     
	
	/**
	* Agrupamentos
	* Chamado em: fin001form04
	*/
	case 'fin001grid04':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(			
			array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'Número'),
			array('colunaValor' => "T002_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "datediff(T002_Data_Vencimento,T002_Data_Emissao) Prazo", 'colunaTitulo' => 'Prazo'),
			array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T002_Valor_Total", 'colunaTitulo' => 'Total'),
			array('colunaValor' => "T002_Valor_Saldo", 'colunaTitulo' => 'Saldo'),
			array('colunaValor' => "T117_Nosso_Numero", 'colunaTitulo' => 'Nosso Número'),
			
			array('colunaValor' => "T002_Id", 'colunaTitulo' => 'ID','colunaExibir'=>false),
			array('colunaValor' => "T002_T035_Id", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),			
			array('colunaValor' => "IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),			
			array('colunaValor' => "T002_Data_Recebimento", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Valor_Duplicata", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Valor_Juros", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Valor_Desconto", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Observacao", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Agrupamento", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Flag_Enviar_Banco", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),						
			array('colunaValor' => "D027_Portador", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Flag_Baixar_Lote", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Flag_Reparcelar", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Valor_Recebido", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "D046_Nome", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),			
			array('colunaValor' => "C007_Nome", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),			

		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
					left join D024 on D024_Id=T002_D024_Id
					left join D027 on D027_Id=T002_D027_Id
					left join T117 on T117_Id=T002_T117_Id
					left join D014 on D014_Id=T002_D014_Id
					LEFT JOIN D154 ON D154_Id=D014_D154_Id
					left join D046 on D046_Id=D024_D046_Id
					left join T007 on T007_Id=T002_T007_Id
					left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno	
					where T002_T002_Id_Agrupado={$_REQUEST['acaoId']}
					order by T002_Numero_Duplicata
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Geração: Grid
		 */
		 $gridTabela = $g['ui']->grid($grid, $geradoDados);
         $g['smarty']->assign('thead', $gridTabela['thead']);
         $g['smarty']->assign('tbody', $gridTabela['tbody']);
         $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
         gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;	    

	/**
	* Agrupar
	* Chamado em: fin001form07
	*/
	case 'fin001grid05':		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(			
			array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'Título'),
			array('colunaValor' => "T002_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T002_Valor_Duplicata", 'colunaTitulo' => 'Valor'),
			array('colunaValor' => "T002_Valor_Desconto", 'colunaTitulo' => 'Desconto'),
			array('colunaValor' => "T002_Valor_Juros", 'colunaTitulo' => 'Acréscimo'),
			array('colunaValor' => "T002_Valor_Total", 'colunaTitulo' => 'Valor Total'),
			array('colunaValor' => "T002_Valor_Saldo", 'colunaTitulo' => 'Saldo'),
			//array('colunaValor' => "SUM(T002_Valor_Total) as soma_valor_total", 'colunaTitulo' => 'Total'),
			/*
			array('colunaValor' => "T002_Id", 'colunaTitulo' => 'ID','colunaExibir'=>false),
			array('colunaValor' => "T002_T035_Id", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),			
			array('colunaValor' => "D014_SubConta", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),			
			array('colunaValor' => "T002_Data_Recebimento", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Valor_Duplicata", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Valor_Juros", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Valor_Desconto", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Observacao", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Agrupamento", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Flag_Enviar_Banco", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),						
			array('colunaValor' => "D027_Portador", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Flag_Baixar_Lote", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Flag_Reparcelar", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Valor_Recebido", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => "D046_Nome", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),			
			array('colunaValor' => "C007_Nome", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),			
			*/
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
					left join D024 on D024_Id=T002_D024_Id
					left join D027 on D027_Id=T002_D027_Id
					left join T117 on T117_Id=T002_T117_Id
					left join D014 on D014_Id=T002_D014_Id
					left join D046 on D046_Id=D024_D046_Id
					left join T007 on T007_Id=T002_T007_Id
					left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno	
					left join T005 on T005_Id=T007_T005_Id
					where
EOT;

		$extra .= gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "ifnull(T002_T035_Id,0)<=0 and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) AND T002_Flag_Reparcelar='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}'"));
		$extra .="order by T002_Data_Vencimento";
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Geração: Grid
		 */
		 $gridTabela = $g['ui']->grid($grid, $geradoDados);
		 
		 // calcular o total
		 $total=0;
		 foreach ($gridTabela['tbody'] as $linha){
		 	$total+=@$linha['dados'][7];
		 }
		 // inserir o total na última linha da tabela
		 array_push($gridTabela['tbody'], array('dados'=>array('','','','','','','<b>Total</b>','<b>'.number_format($total,2).'</b>')));
		 
         $g['smarty']->assign('thead', $gridTabela['thead']);
         $g['smarty']->assign('tbody', $gridTabela['tbody']);
         $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
                  
         gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;	 
	
	/**
	* Reparcelar
	* Chamado em: fin001form09
	*/
	case 'fin001grid06':
	
		//echo '<p style="font-weight:bold;" align="center">Duplicatas para Reparcelamento</p>';
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(			
			array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'Duplicata'),
			array('colunaValor' => "T002_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T002_Valor_Duplicata", 'colunaTitulo' => 'Valor Total', 'callback'=>'gCorrigeNumero'),
			// array('colunaValor' => "T002_Valor_Desconto", 'colunaTitulo' => 'Desconto'),
			// array('colunaValor' => "T002_Valor_Juros", 'colunaTitulo' => 'Acréscimo')
			array('colunaValor' => "", 'colunaTitulo' => 'Portador', 'colunaTipo'=>'livre'),
			array('colunaValor' => "T002_Valor_Total", 'colunaTitulo' => 'Total', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Valor_Saldo", 'colunaTitulo' => 'Saldo', 'colunaExibir'=>false),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
			
					left join D024 on D024_Id=T002_D024_Id
					left join D027 on D027_Id=T002_D027_Id
					left join T117 on T117_Id=T002_T117_Id
					left join D014 on D014_Id=T002_D014_Id
					left join D046 on D046_Id=D024_D046_Id
					left join T007 on T007_Id=T002_T007_Id
					left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno	
					left join T005 on T005_Id=T007_T005_Id
					where					
EOT;

		$extra .= gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "(T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) AND ifnull(T002_T035_Id,0)<=0 AND T002_Flag_Reparcelar='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}' "));
		$extra .= " order by T002_Data_Vencimento";
		
		/**
		* Filtro
		*/
		
		/*
		 * Totalizadores
		
		$valorTotalPago= gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T015_Valor_Pago)", $from, $extra));
		$valorTotalVencido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento<curdate(),T015_Valor_Total,0))", $from, $extra));
		$valorAVencer = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento>=curdate(),T015_Valor_Total,0))", $from, $extra));
		$valorTotalAPagar = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T015_Valor_Total)", $from, $extra));
		*/
		$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("SUM(T002_Valor_Duplicata)", $from, $extra);
		$valorTotal = gCorrigeNumero($valorTotal);
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" value="{$totalTitulos}" size="11" readonly></span>
				<span class="item" style="text-align:right">Total R$<br><input type="text" class="whiteBg" value="{$valorTotal}" size="11" readonly></span>
				<div id="fin002ExtraBtns" style="clear:both;"></div>
			</div>
EOT;
		
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Geração: Grid
		 */
		 $gridTabela = $g['ui']->grid($grid, $geradoDados);
		 
		 // calcular o total
		 $total=0;
		 foreach ($gridTabela['tbody'] as $linha){
		 	$total+=@$linha['dados'][4];
		 }
		 // inserir o total na última linha da tabela
		 array_push($gridTabela['tbody'], array('dados'=>array('','','','<b>Total</b>','<b>'.number_format($total,2).'</b>','')));
		 
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;	 	
	
	/**
	* Baixar Títulos
	* Chamado em: fin001form08
	*/
	case 'fin001grid07':
		
		// Dentro desta tabela temos campos de formulário
		// Definimos um formulário aqui apenas para gerar esses campos
		$form = new Formulario('form_fin001_form1');  
		$formArray = array(
			'campos' => array(
				'baixar_titulos' => array(
					'T103_Valor_Recebido' => array('name' => 'T103_Valor_Recebido', 'type' => 'moeda', 'length' => array(10,20)),
					'T103_Valor_Desconto' => array('name' => 'T103_Valor_Desconto', 'type' => 'moeda', 'length' => array(10,20)),
					'T103_Valor_Taxas' => array('name' => 'T103_Valor_Taxas', 'type' => 'moeda', 'length' => array(10,20)),
					'T103_Valor_Juros' => array('name' => 'T103_Valor_Juros', 'type' => 'moeda', 'length' => array(10,20)),				
					'T103_D027_Id' => array('name' => 'T103_D027_Id', 'type' => 'select', 'select' =>
						gGeraSelect('D027', 'D027_Id', 'D027_Portador', 'order by D027_Id')
					),
					'T002_Flag_Baixar_Lote' => array('name' => 'T002_Flag_Baixar_Lote', 'type' => 'checkbox')
				)
			)
		);

		if ($g['C031']['utilizaTrocoBaixa'] === 'S') {
			$IdDivForm = $_REQUEST['idDivForm'];
			$formArray['campos']['baixar_titulos']['T103_Valor_Recebido']['extra'] = "autocomplete=\"off\" onfocusout=\"calcularValorTroco('{$IdDivForm}', '{$r_extra}');\"";
		}

		$form->definirFormularioArray($formArray);
	
		// Tabela temporária T103
		$retorno = $FIN001->atualizaTitulosParaBaixarT103();
		if ($retorno !== true) {
			echo gGeraAlertMsg('Erro', 'Erro ao listar títulos para baixar (e inserir na T103): ' . $retorno);
		}

		//echo '<p style="font-weight:bold;" align="center">Títulos a Baixar</p>';

		/**
		* Definição do Grid (Tabela)
		*/
		
			$grid = array(
				array('colunaValor' => 'T103_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false, 'chavePrimaria' => true),
				array('colunaValor' => "T002_Id", 'colunaTitulo' => 'Valor Total', 'colunaExibir' => false, 'chavePrimaria' => true),
				array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Empresa'),
				array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'Documento'),
				array('colunaValor' => "T002_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
				array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
				array('colunaValor' => "T002_Valor_Total", 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold'),
				array('colunaValor' => "T002_Valor_Saldo", 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => true),
				array('colunaValor' => "T103_Valor_Recebido", 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'right','colunaEditar' => true, 'colunaExibir' => false, 'callback' => 'gCorrigeNumero'),
				array('colunaValor' => "T103_Valor_Desconto", 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaExibir' => false, 'callback' => 'gCorrigeNumero'),
				array('colunaValor' => "T103_Valor_Taxas", 'colunaTitulo' => 'Taxas','colunaEditar' => true, 'colunaAlinhar' => 'right', 'colunaExibir' => false, 'callback' => 'gCorrigeNumero'),
				array('colunaValor' => "T103_Valor_Juros", 'colunaTitulo' => 'Juros','colunaEditar' => true, 'colunaAlinhar' => 'right', 'colunaExibir' => false, 'callback' => 'gCorrigeNumero'),
				array('colunaValor' => "T103_Valor_Recebido_", 'colunaTitulo' => 'Valor Recebido', 'callback' => 'baixarTitulosValorRecebido', 'callbackParameter' => $form, 'colunaTipo' => 'livre', 'colunaExibir' => true),
				array('colunaValor' => "T103_Valor_Juros_", 'colunaTitulo' => 'Juros', 'callback' => 'baixarTitulosValorJuros', 'callbackParameter' => $form, 'colunaTipo' => 'livre', 'colunaExibir' => true),
				array('colunaValor' => "T103_Valor_Taxas_", 'colunaTitulo' => 'Taxas', 'callback' => 'baixarTitulosValorTaxas', 'callbackParameter' => $form, 'colunaTipo' => 'livre', 'colunaExibir' => true),
				array('colunaValor' => "T103_Valor_Desconto_", 'colunaTitulo' => 'Desconto', 'callback' => 'baixarTitulosValorDesconto', 'callbackParameter' => $form, 'colunaTipo' => 'livre', 'colunaExibir' => true),
				array('colunaValor' => "T103_Valor_Total_Recebido", 'colunaTitulo' => 'Total Recebido', 'colunaAlinhar' => 'right', 'colunaExibir' => true, 'callback' => 'gCorrigeNumero'),	
				array('colunaValor' => "T103_D027_Id", 'colunaTitulo' => 'Portador', 'callback' => 'baixarTitulosPortador', 'callbackParameter' => $form),
				array('colunaValor' => "T002_Flag_Baixar_Lote", 'colunaTitulo' => 'Bx', 'callback' => 'baixarTitulosBx', 'callbackParameter' => $form),			
			);


		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
            left join T103 on T002_Id=T103_T002_Id
			left join D024 on D024_Id=T002_D024_Id
			LEFT JOIN D021 on T002_D021_Id=D021_Id
			left join D027 on D027_Id=T002_D027_Id
			left join T117 on T117_Id=T002_T117_Id
			left join D014 on D014_Id=T002_D014_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T007 on T007_Id=T002_T007_Id
			left join T002A on T002_Id=T002A_T002_Id
			left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
			left join T005 on T005_Id=T007_T005_Id
					
EOT;

		if (isset($r_extra)) {
			$extra .= 'WHERE ' . gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "T002_Flag_Baixar_Lote='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}'"));
		}

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor_Recebido', 'valor' => 'T103_Valor_Total_Recebido', 'titulo' => 'Total Recebido'),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		
	 
		// inserir o total na última linha da tabela
		array_push($gridTabela['tbody'], array('dados'=>array('<b>Total</b>','','','','<b><span id="fin001BaixarValor"></span></b>','<b><span id="fin001BaixarValorRecebido"></span></b>','','')));
		
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
 		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
                 
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
         
        // Ações JS
        echo <<<EOT
         	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
         		$('#{$g['divId']} input:text').unbind('blur');
         		$('#{$g['divId']} input:text').bind('blur', function () {
         			baixarTitulosEditarCampo($(this),'{$g['divId']}');
         		});
         		$('#{$g['divId']} select').unbind('change');
         		$('#{$g['divId']} select').bind('change', function () {
         			baixarTitulosEditarCampo($(this),'{$g['divId']}');
         		});
         		$('#{$g['divId']} input:checkbox').unbind('change');
         		$('#{$g['divId']} input:checkbox').bind('change', function () {
         			baixarTitulosEditarCampo($(this),'{$g['divId']}');
         		});
         		atualizaTotaisBaixar();
         	} </script></div>
EOT;
	break;	 		
	
	/**
	* Total Mensal
	* Chamado em: fin001tab01
	*/
	case 'fin001grid08':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "T002_Id", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Emissao", 'colunaExibir' => false),
			array('colunaValor' => "month(T002_Data_Vencimento) as Mes", 'colunaTitulo' => 'Mês', 'callback' => 'formataMesAno'),
			array('colunaValor' => "year(T002_Data_Vencimento) as Ano", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Vencimento", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Recebimento", 'colunaExibir' => false),
			array('colunaValor' => "T002_Valor_Duplicata", 'colunaExibir' => false),
			array('colunaValor' => "T002_Valor_Juros", 'colunaExibir' => false),			
			array('colunaValor' => "T002_Valor_Desconto", 'colunaExibir' => false),
			array('colunaValor' => "sum(T002_Valor_Saldo) Total", 'colunaExibir' => false),
			array('colunaValor' => "sum(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Recebimento>=curdate(),T002_Valor_Saldo,0)) Total_Receber", 'colunaExibir' => false),
			array('colunaValor' => "sum(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Recebido,0)) as Total_Recebido", 'colunaTitulo' => 'Recebido'),
			array('colunaValor' => "acumuladoRecebido", 'colunaTitulo'=>'Acumulado','colunaTipo' => 'livre', 'callback' => 'calculaAcumuladoRecebido'),
			array('colunaValor' => "sum(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0)) as Total_Vencer", 'colunaTitulo' => 'A Receber'),
			array('colunaValor' => "acumuladoReceber", 'colunaTitulo'=>'Acumulado','colunaTipo' => 'livre', 'callback' => 'calculaAcumuladoReceber'),
			array('colunaValor' => "T002_Numero_Documento", 'colunaExibir' => false),
			array('colunaValor' => "D024_Nome_Empresa", 'colunaExibir' => false),
			array('colunaValor' => "D024_Nome_Fantasia", 'colunaExibir' => false),			
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
			left join D024 on D024_Id=T002_D024_Id
			left join D027 on D027_Id=T002_D027_Id
			left join T117 on T117_Id=T002_T117_Id
			left join D014 on D014_Id=T002_D014_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T007 on T007_Id=T002_T007_Id
			left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno	
			where T002_C004_Id={$g['empresaAtual']}
			group by year(T002_Data_Vencimento),month(T002_Data_Vencimento)
			order by T002_Data_Vencimento			
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;
	
	/**
	* Total Conta
	* Chamado em: fin001tab01
	*/
	case 'fin001grid09':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(			
	
			array('colunaValor' => "T002_Id", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Emissao", 'colunaExibir' => false),
			array('colunaValor' => "IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)", 'colunaTitulo' => 'Conta'),
			array('colunaValor' => "month(T002_Data_Vencimento) as Mes", 'colunaExibir' => false),
			array('colunaValor' => "year(T002_Data_Vencimento) as Ano", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Vencimento", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Recebimento", 'colunaExibir' => false),
			array('colunaValor' => "T002_Valor_Duplicata", 'colunaExibir' => false),
			array('colunaValor' => "T002_Valor_Juros", 'colunaExibir' => false),			
			array('colunaValor' => "T002_Valor_Desconto", 'colunaExibir' => false),
			//array('colunaValor' => "sum(T002_Valor_Total) Total", 'colunaExibir' => false),
			array('colunaValor' => "sum(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Recebido,0)) as Total_Recebido", 'colunaTitulo' => 'Recebido'),
			array('colunaValor' => "sum(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0)) as Total_Atrasado", 'colunaTitulo' => 'Atrasado'),
			array('colunaValor' => "sum(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0)) as Total_Vencer", 'colunaTitulo' => 'A Receber'),
			array('colunaValor' => "acumuladoReceber",'colunaTipo'=>'livre', 'colunaTitulo'=>'Total a Receber','callback' => 'calculaTotalReceber'),						
			array('colunaValor' => "T002_Numero_Documento", 'colunaExibir' => false),
			array('colunaValor' => "D014_Id", 'colunaExibir' => false),						
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
			
		left join D024 on D024_Id=T002_D024_Id
		left join D027 on D027_Id=T002_D027_Id
		left join T117 on T117_Id=T002_T117_Id
		left join D014 on D014_Id=T002_D014_Id
		LEFT JOIN D154 ON D154_Id=D014_D154_Id
		left join D046 on D046_Id=D024_D046_Id
		left join T007 on T007_Id=T002_T007_Id
		left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno	
		where T002_C004_Id={$g['empresaAtual']}
		and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)							
		group by D014_Id
		order by sum(T002_Valor_Saldo) desc			
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }        

	break;		
	
	/**
	* Total Diário
	* Chamado em: fin001tab01
	*/
	case 'fin001grid10':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(			
	
			array('colunaValor' => "T002_Id", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Emissao", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Dia', 'callback'=>'gCorrigeData'),
			array('colunaValor' => "T002_Data_Recebimento", 'colunaExibir' => false),
			array('colunaValor' => "T002_Valor_Duplicata", 'colunaExibir' => false),
			array('colunaValor' => "T002_Valor_Juros", 'colunaExibir' => false),			
			array('colunaValor' => "T002_Valor_Desconto", 'colunaExibir' => false),
			array('colunaValor' => "sum(T002_Valor_Saldo) Total", 'colunaExibir' => false),
			array('colunaValor' => "sum(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Recebido,0)) as Total_Recebido", 'colunaTitulo' => 'Recebido'),
			array('colunaValor' => "acumuladoRecebido",'colunaTipo'=>'livre', 'colunaTitulo'=>'Acumulado','callback' => 'calculaAcumuladoRecebido'),
			array('colunaValor' => "sum(if(T002_Data_Recebimento='0000-00-00',T002_Valor_Saldo,0)) as Total_Vencer", 'colunaTitulo' => 'Acumulado'),
			array('colunaValor' => "acumuladoReceber",'colunaTipo'=>'livre', 'colunaTitulo'=>'Acumulado','callback' => 'calculaAcumuladoReceber'),						
			array('colunaValor' => "T002_Numero_Documento", 'colunaExibir' => false),
			array('colunaValor' => "D024_Nome_Empresa", 'colunaExibir' => false),						
			array('colunaValor' => "D024_Nome_Fantasia", 'colunaExibir' => false),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
			
					left join D024 on D024_Id=T002_D024_Id
					left join D027 on D027_Id=T002_D027_Id
					left join T117 on T117_Id=T002_T117_Id
					left join D014 on D014_Id=T002_D014_Id
					left join D046 on D046_Id=D024_D046_Id
					left join T007 on T007_Id=T002_T007_Id
					left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno	
					where T002_C004_Id={$g['empresaAtual']}	
					and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)							
					group by T002_Data_Vencimento
					order by T002_Data_Vencimento			
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;			
	
	/**
	* Total Portador
	* Chamado em: fin001tab01
	*/
	case 'fin001grid11':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(			
	
			array('colunaValor' => "T002_Id", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Emissao", 'colunaExibir' => false),
			array('colunaValor' => "D027_Portador", 'colunaTitulo' => 'Portador'),
			array('colunaValor' => "month(T002_Data_Vencimento) as Mes", 'colunaExibir' => false),
			array('colunaValor' => "year(T002_Data_Vencimento) as Ano", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Recebimento", 'colunaExibir' => false),
			array('colunaValor' => "T002_Valor_Duplicata", 'colunaExibir' => false),
			array('colunaValor' => "T002_Valor_Juros", 'colunaExibir' => false),			
			array('colunaValor' => "T002_Valor_Desconto", 'colunaExibir' => false),
			array('colunaValor' => "sum(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Recebido,0)) as Total_Recebido", 'colunaTitulo' => 'Recebido'),
			array('colunaValor' => "sum(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0)) as Total_Atrasado", 'colunaTitulo' => 'Atrasado'),
			array('colunaValor' => "sum(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0)) as Total_Vencer", 'colunaTitulo' => 'A Receber'),
			array('colunaValor' => "acumuladoReceber",'colunaTipo'=>'livre', 'colunaTitulo'=>'Total a Receber','callback' => 'calculaTotalReceber'),
			array('colunaValor' => "T002_Numero_Documento", 'colunaExibir' => false),
			array('colunaValor' => "D027_Id", 'colunaExibir' => false),
										
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
			
					left join D024 on D024_Id=T002_D024_Id
					left join D027 on D027_Id=T002_D027_Id
					left join T117 on T117_Id=T002_T117_Id
					left join D014 on D014_Id=T002_D014_Id
					left join D046 on D046_Id=D024_D046_Id
					left join T007 on T007_Id=T002_T007_Id
					left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno	
					where T002_C004_Id={$g['empresaAtual']}	
					and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)																		
					group by D027_Id
					order by sum(T002_Valor_Saldo) desc			
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }        

	break;			
	
	/**
	* Total Cliente
	* Chamado em: fin001tab01
	*/
	case 'fin001grid12':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(			
	
			array('colunaValor' => "T002_Id", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Emissao", 'colunaExibir' => false),
			array('colunaValor' => "month(T002_Data_Vencimento) as Mes", 'colunaExibir' => false),
			array('colunaValor' => "year(T002_Data_Vencimento) as Ano", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Recebimento", 'colunaExibir' => false),
			array('colunaValor' => "T002_Valor_Duplicata", 'colunaExibir' => false),
			array('colunaValor' => "T002_Valor_Juros", 'colunaExibir' => false),			
			array('colunaValor' => "T002_Valor_Desconto", 'colunaExibir' => false),
			array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => "sum(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Recebido,0)) as Total_Recebido", 'colunaTitulo' => 'Recebido'),
			array('colunaValor' => "sum(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0)) as Total_Atrasado", 'colunaTitulo' => 'Atrasado'),
			array('colunaValor' => "sum(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0)) as Total_Vencer", 'colunaTitulo' => 'A Receber'),			
			array('colunaValor' => "acumuladoReceber",'colunaTipo'=>'livre', 'colunaTitulo'=>'Total a Receber','callback' => 'calculaTotalReceber'),
			array('colunaValor' => "T002_Numero_Documento", 'colunaExibir' => false),
			array('colunaValor' => "D024_Id", 'colunaExibir' => false),			
			array('colunaValor' => "D024_Nome_Fantasia", 'colunaExibir' => false),
										
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
			
					left join D024 on D024_Id=T002_D024_Id
					left join D027 on D027_Id=T002_D027_Id
					left join T117 on T117_Id=T002_T117_Id
					left join D014 on D014_Id=T002_D014_Id
					left join D046 on D046_Id=D024_D046_Id
					left join T007 on T007_Id=T002_T007_Id
					left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno						
					where T002_C004_Id={$g['empresaAtual']}
					and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)										
					group by D024_Id
					order by sum(T002_Valor_Saldo) desc			
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		
	break;		

	/**
	* Total Vendedor
	* Chamado em: fin001tab01
	*/
	case 'fin001grid13':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(			
	
			array('colunaValor' => "T002_Id", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Emissao", 'colunaExibir' => false),
			array('colunaValor' => "month(T002_Data_Vencimento) as Mes", 'colunaExibir' => false),
			array('colunaValor' => "year(T002_Data_Vencimento) as Ano", 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Recebimento", 'colunaExibir' => false),
			array('colunaValor' => "T002_Valor_Duplicata", 'colunaExibir' => false),
			array('colunaValor' => "T002_Valor_Juros", 'colunaExibir' => false),			
			array('colunaValor' => "T002_Valor_Desconto", 'colunaExibir' => false),
			array('colunaValor' => "C007_Primeiro_Nome", 'colunaTitulo' => 'Vendedor'),
			array('colunaValor' => "sum(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Recebido,0)) as Total_Recebido", 'colunaTitulo' => 'Recebido'),
			array('colunaValor' => "sum(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0)) as Total_Atrasado", 'colunaTitulo' => 'Atrasado'),
			array('colunaValor' => "sum(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0)) as Total_Vencer", 'colunaTitulo' => 'A Receber'),			
			array('colunaValor' => "acumuladoReceber",'colunaTipo'=>'livre', 'colunaTitulo'=>'Total a Receber','callback' => 'calculaTotalReceber'),
			array('colunaValor' => "T002_Numero_Documento", 'colunaExibir' => false),
			array('colunaValor' => "C007_Id", 'colunaExibir' => false),						
										
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
			
					left join D024 on D024_Id=T002_D024_Id
					left join D027 on D027_Id=T002_D027_Id
					left join T117 on T117_Id=T002_T117_Id
					left join D014 on D014_Id=T002_D014_Id
					left join D046 on D046_Id=D024_D046_Id
					left join T007 on T007_Id=T002_T007_Id
					left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno						
					where T002_C004_Id={$g['empresaAtual']}
					and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)										
					group by C007_Id
					order by sum(T002_Valor_Saldo) desc			
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;		
	
	/**
	* @author AndreySliwinski
	* @since 17/01/2012
	*/                 
	case 'fin001gridReparcelamentos':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T166_Id', $g['divId'])),
			array('colunaValor' => 'T166_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true),
			array('colunaValor' => 'T166_Tipo', 'colunaTitulo' => 'Tipo', 'callback'=>'validaCampoTipo'),
			array('colunaValor' => 'T166_D024_Id', 'colunaTitulo' => 'Cliente/Fornecedor', 'colunaExibir'=>false),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente/Fornecedor'),
			array('colunaValor' => 'T166_C007_Id', 'colunaTitulo' => 'Usuário', 'colunaExibir'=>false),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'T166_Data_Emissao', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T166_Valor_Total', 'colunaTitulo' => 'Valor Total','colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T166_Titulos_Geradores', 'colunaTitulo' => 'Títulos Geradores', 'callback'=>'formataGeradores'),
			array('colunaValor' => 'T166_Titulos_Gerados', 'colunaTitulo' => 'Títulos Gerados' , 'callback'=>'formataGerados'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T166';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
			left join C007 on C007_Id=T166_C007_Id
		    left join D024 on D024_Id=T166_D024_Id
		    WHERE (T166_C004_Id='{$g['empresaAtual']}') 
		    AND (T166_Tipo = 'A' || T166_Tipo = 'R')
EOT;
		
		// /**
		// * Botões (Versão Acima do GRID)
		// */
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="fin001EnviarEmailReparcelamento">Enviar Email</button>
// 				
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// $('#etc010FormUsuariosClienteBtnIncluir').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Usuários Clientes', '/etc/etc010/content/etc010ContentFormUsuariosClientesIncluir/', '&tab=geral', [400,300]);
				// });
			// } </script></div>
// EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente/Fornecedor', 'tipo' => 'texto'),
			'T166_Data_Emissao' => array('titulo' => 'Data Reparcelamento', 'tipo' => 'data'),
			'T166_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
			'T166_Titulos_Geradores' => array('titulo' => 'Títulos Geradores', 'tipo' => 'texto'),
			'T166_Titulos_Gerados' => array('titulo' => 'Títulos Gerados', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T166_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Reparcelamentos e Agrupamentos - ID: ' + acaoId, '/fin/fin001/content/fin001ContentReparcelar/', '&acaoId=' + encodeURIComponent(acaoId), [500,400]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;
	
	/**
	* @author AndreySliwinski
	* @since 05/03/2012
	*/                 
	case 'fin001GridAcrescimosDescontos':
		global $g;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T020_Id', $g['divId'])),
			array('colunaValor' => 'T020_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T020_T002_Id', 'colunaTitulo' => 'T002', 'colunaExibir'=>false),
			array('colunaValor' => 'T020_Data_Lancamento', 'colunaTitulo' => 'Data Lançamento', 'callback'=>'gCorrigeData', 'colunaAlinhar'=>'center', 'style'=>'width:80px;'),
			array('colunaValor' => 'T002_Numero_Documento', 'colunaTitulo' => 'Documento'),
			array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'Duplicata'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'concat(ifnull(D024_CNPJ,""),ifnull(D024_CPF,"")) as CnpjCpf', 'colunaTitulo' => 'CNPJ / CPF'),
			array('colunaValor' => 'T020_Historico', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T020_Valor', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2', 'colunaAlinhar'=>'right'),
			array('colunaValor' => 'T020_Flag_Deb_Cred', 'colunaTitulo' => 'Tipo', 'callback'=>'validaCampoTipoAcrescimosDescontos'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T020';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
			left join T002 on T002_Id=T020_T002_Id
			left join D024 on D024_Id=T002_D024_Id
			WHERE 
			T020_C004_Id='{$g['empresaAtual']}'
		
EOT;
		
		// /**
		// * Botões (Versão Acima do GRID)
		// */
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="fin001EnviarEmailReparcelamento">Enviar Email</button>
// 				
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// $('#etc010FormUsuariosClienteBtnIncluir').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Usuários Clientes', '/etc/etc010/content/etc010ContentFormUsuariosClientesIncluir/', '&tab=geral', [400,300]);
				// });
			// } </script></div>
// EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T020_Id' => array('titulo' => 'ID', 'tipo' => 'numero'),
			'T020_Data_Lancamento' => array('titulo' => 'Lançamento', 'tipo' => 'data'),
			'T002_Numero_Duplicata' => array('titulo' => 'Duplicata', 'tipo' => 'numero'),
			'T002_Nome_Cliente' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T020_Valor' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'T020_Historico' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			//'T020_Flag_Deb_Cred' => array('titulo' => 'Tipo(D/C)', 'tipo' => 'texto'),
			'T020_Flag_Deb_Cred' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Débito', 'value' => 'D'),
				array('title' => 'Crédito', 'value' => 'C'),
			), 'naoGerarExtra' => false),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Total_Credito', 'valor' => "IF(T020_Flag_Deb_Cred=\"C\", T020_Valor, 0)", 'titulo' => 'Total Crédito'),
			array('nome' => 'Total_Debito', 'valor' => "IF(T020_Flag_Deb_Cred=\"D\", T020_Valor, 0)", 'titulo' => 'Total Débito'),
        );
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		 /**
		 * Totalizadores
		 */
		//$valorTotalCredito = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(T020_Flag_Deb_Cred=\"C\", T020_Valor, 0))", $from, $extra));
		//$valorTotalDebito = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(T020_Flag_Deb_Cred=\"D\", T020_Valor, 0))", $from, $extra));
		// $valorAReceber = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento>=curdate(),T002_Valor_Total,0))", $from, $extra));
		// $valorTotalAReceber = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento='0000-00-00',T002_Valor_Total,0))", $from, $extra));
		// $totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);


 		/**
		* Botões (Versão Acima do GRID)
		*/
		/*echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Crédito<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorTotalCredito}" size="11" readonly></span>
				<span class="item" style="text-align:right">Total Débito<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorTotalDebito}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;*/
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T166_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Reparcelamentos e Agrupamentos - ID: ' + acaoId, '/fin/fin001/content/fin001ContentReparcelar/', '&acaoId=' + encodeURIComponent(acaoId), [500,400]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;


		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'fin001GridPagamento':

//		$queryT002 = <<<EOT
//			SELECT 
//			T002_Numero_Duplicata, 
//			T002_Data_Emissao, 
//			D024_Nome_Empresa,
//			D024_Id, 
//			T002_Data_Vencimento, 
//			T002_Valor_Duplicata, 
//			T002_Valor_Total 
//			FROM T002 
//			left join D024 on D024_Id=T002_D024_Id 
//			left join D027 on D027_Id=T002_D027_Id 
//			left join T117 on T117_Id=T002_T117_Id 
//			left join D014 on D014_Id=T002_D014_Id 
//			left join D046 on D046_Id=D024_D046_Id 
//			left join T007 on T007_Id=T002_T007_Id 
//			left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno 
//			WHERE ((1=1)) 
//			AND T002_C004_Id='{$g['empresaAtual']}'
//			and T002_Data_Recebimento='0000-00-00'
//			and T002_Flag_Reparcelar='S' 
//			and ifnull(T002_T035_Id,0)<=0 
//			and (T002_T002_Id_Agrupado<=0 
//			or T002_T002_Id_Agrupado is null) 
//			ORDER BY T002_Data_Vencimento 
//			LIMIT 0,500
//EOT;
			$query = base64_decode($r_extra);
			$extra = gInsertExtraWhere($query, "T002_Flag_Reparcelar='S' AND T002_Data_Recebimento='0000-00-00' AND ifnull(T002_T035_Id,0)<=0 AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)");
			$queryFinal = "SELECT D024_Id,T002_Valor_Saldo FROM T002 ".$extra;
			
			$T002 = mysql_query($queryFinal);
		
			$valorSomado = 0;
			while($aT002 = mysql_fetch_array($T002)){
				$D024_Id = $aT002['D024_Id'];
				$valorSomado = ($valorSomado + $aT002['T002_Valor_Saldo']);
			}
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
	            array('colunaValor' => 'T180_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
	            array('colunaValor' => 'T180_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
	            array('colunaValor' => 'T180_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T180_Prazos_Dias'),
	            array('colunaValor' => 'T180_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T180_Prazos_Dias'),
	            array('colunaValor' => 'ven002_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo' => 'livre', 'callback' => 'fin001_Dia_Semana'),
	            array('colunaValor' => 'T180_Valor_Parcela', 'colunaTitulo' => 'Valor parcela', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T180_Prazos_Dias'),
	            array('colunaValor' => 'T180_Valor_Acrescimo', 'colunaTitulo' => 'Acréscimo', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
	            array('colunaValor' => 'T180_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
	            array('colunaValor' => '(T180_Valor_Parcela+T180_Valor_Acrescimo-T180_Valor_Desconto)', 'colunaTitulo' => 'Valor total', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
	            array('colunaValor' => 'T180_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T180_Prazos_Dias', 'colunaEditarGeraSelect' => array('D027', 'D027_Id', 'D027_Portador', 'order by D027_Portador', true), 'callback' => 'T180_D027_Id_Portador'),
	            array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
	            array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
	            array('colunaValor' => 'ven002_Detalhes', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'fin001_Detalhes'),
	        );

			/**
			*
			* Definição da SQL
			*/
			$from = 'T180';

			$extra = <<<EOT
				left join D027 on D027_Id=T180_D027_Id
				WHERE T180_D024_Id = '{$D024_Id}'
EOT;
			/*
			* Filtro
			
			// Gera o filtro e acrescenta ao $extra
			$gridFiltro = array(
				
			);
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint();
            */
			
			//Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T180_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
					return false;
				});
				
				// Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_T180_Prazos_Dias(T180_Id, campoEditado, valorAntigo, novoValor) {
					T180_Id = T180_Id.replace(/T180_Id-/, '');
					if (campoEditado == 'T180_Prazos_Dias') {
						$.getJSON('/fin/fin001/grid_func-ajax/fin001atualizarParcelas/?ajax=true&T180_Id='      		+ encodeURIComponent(T180_Id) + 
																							      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																							      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																							      '&novoValor='			+ encodeURIComponent(novoValor) +  
																							      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T180_Data_Vencimento') {
						$.getJSON('/fin/fin001/grid_func-ajax/fin001atualizarParcelas/?ajax=true&T180_Id='      		+ encodeURIComponent(T180_Id) + 
																							      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																							      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																							      '&novoValor='			+ encodeURIComponent(novoValor) +  
																							      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T180_Valor_Parcela') {
						$.getJSON('/fin/fin001/grid_func-ajax/fin001atualizarParcelas/?ajax=true&T180_Id='      		+ encodeURIComponent(T180_Id) + 
																							      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																							      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																							      '&novoValor='			+ encodeURIComponent(novoValor) +
																							      '&valorTotal='		+ encodeURIComponent('{$valorSomado}') +   
																							      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T180_D027_Id') {
						divRefresh('{$g['divId']}');						
					}
				}
			} </script></div>
EOT;
			

			/**
			* Geração: Monta o SQL e retorna o dados
			*/
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

			/**
			* Geração: Grid
			*/
			$gridTabela = $g['ui']->grid($grid, $geradoDados);
			$g['smarty']->assign('thead', $gridTabela['thead']);
			$g['smarty']->assign('tbody', $gridTabela['tbody']);
			$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
			gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

			$novoIdTelaForm = uniqid();
			$sql2 = "SELECT T180_C007_Id FROM T180 WHERE T180_D024_Id='{$D024_Id}'";
			$result2 = mysql_query($sql2);
			$C007_Id='';
			while($linha3 = mysql_fetch_array($result2)){
				$C007_Id = $linha3['T180_C007_Id'];
			}
			$extra_ = urlencode($r_extra);

            // Não executar quando houver o refresh de linha
            if (empty($r_linhaGridId)) {
                echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#fin001ReparcelarFinal').unbind('click');
					$('#fin001ReparcelarFinal').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								//var T005_Id = $('#{$g['divId']} #T005_Id').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/fin/fin001/grid_func-ajax/fin001Reparcelar/?ajax=true&D024_Id=' + encodeURIComponent("{$D024_Id}") + '&C007_Id=' + encodeURIComponent("{$C007_Id}") + '&valorSomado=' + encodeURIComponent("{$valorSomado}")+ '&extra={$extra_}&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    		dialogConfirm('Ok', 'Processo concluído com sucesso', \$buttons);
										divRefresh('{$g['divIdParent']}', true);
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    		dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$g['divId']}');
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Atualizar Alíquota', 'Você tem certeza? ', \$buttons);
					});
					$('#fin001LimparGrid').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								//var T005_Id = $('#{$g['divId']} #T005_Id').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/fin/fin001/grid_func-ajax/fin001LimparGrid/?ajax=true&D024_Id=' + encodeURIComponent("{$D024_Id}") + '&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    		dialogConfirm('Ok', 'Processo concluído com sucesso', \$buttons);
										divRefresh('{$g['divId']}', true);
										//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    		dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$g['divId']}');
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Atualizar Alíquota', 'Você tem certeza? ', \$buttons);
					});
				} </script></div>				
EOT;
            }
		break;
		
	case 'fin001gridTitulosGeradores':

		$grid = array( 	
				array('colunaValor' => "T002_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
				array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'N.Dupl.'),
				array('colunaValor' => "T002_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
				array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),								
				array('colunaValor' => "T002_Valor_Duplicata", 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			);
		
		/**
		 * Definição da SQL
		 */
		$from = "T002";

		$T166 = mysql_query("SELECT T166_Titulos_Geradores from T166 WHERE T166_Id='{$r_acaoId}'");
		$aT166 = mysql_fetch_array($T166);
		$res=explode("\n", $aT166['T166_Titulos_Geradores']);
		$indices="";
		foreach ($res as $elemento){
			if (trim($elemento)!=""){
				$part=explode(";", $elemento);
				if (isset($part[0]) && is_numeric(trim($part[0]))) {
					$indices.=trim($part[0]).",";
				}
			}
		}
		while (substr($indices,-1,1)==","){
			$indices=substr_replace($indices, '', -1);	
		}

		if (!empty($indices)) {
			$extra = <<<EOT
			WHERE T002_Id IN ({$indices})
EOT;
		} else {
			$extra = <<<EOT
			WHERE 1=0
EOT;
		}
			
		/**
		* Filtro
		*/
		$gridFiltro = array(
		);		

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		
			/**
			 * Geração: Monta o SQL e retorna o dados
			 */
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);					

		
		/**
		 * Geração: Grid
		 */
		 $gridTabela = $g['ui']->grid($grid, $geradoDados);
         $g['smarty']->assign('thead', $gridTabela['thead']);
         $g['smarty']->assign('tbody', $gridTabela['tbody']);
         $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
         gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
  	break;		
  	
	case 'fin001gridTitulosGerados':
		
		$T166 = mysql_query("SELECT T166_Titulos_Gerados from T166 WHERE T166_Id='{$r_acaoId}'");
		$aT166 = mysql_fetch_array($T166);
		$res=explode("\n", $aT166['T166_Titulos_Gerados']);
		$indices="";
		foreach ($res as $elemento){
			if (trim($elemento)!=""){
				$part=explode(";", $elemento);
				if (isset($part[0]) && is_numeric(trim($part[0]))) {
					$indices.=trim($part[0]).",";
				}
			}
		}
		while (substr($indices,-1,1)==","){
			$indices=substr_replace($indices, '', -1);	
		}		

		$grid = array( 	
				array('colunaValor' => "T002_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
				array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'N.Dupl.'),
				array('colunaValor' => "T002_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
				array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),							
				array('colunaValor' => "T002_Valor_Duplicata", 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			);
		
		/**
		 * Definição da SQL
		 */
		$from = "T002";
		
		if (!empty($indices)) {
			$extra = <<<EOT
			WHERE T002_Id IN ({$indices})
EOT;
		} else {
			$extra = <<<EOT
			WHERE 1=0
EOT;
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
		);		

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);

		
			
			/**
			 * Geração: Monta o SQL e retorna o dados
			 */
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);					

		
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
  	break;	  	
  	
  	case 'fin001GridCaixaBanco':
  	
  		$T002_Id = (isset($r_acaoId)) ? $r_acaoId : false;
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
		array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T019_Id', $g['divId'])),
		array('colunaValor'=>'T019_Id', 'colunaTitulo'=>'ID','chavePrimaria' => true),
		array('colunaValor'=>'D007_Nome_Banco', 'colunaTitulo'=>'Banco', 'style' => 'width:1%'),
		array('colunaValor'=>'T019_Data_Lancamento', 'colunaTitulo'=>'Data', 'callback'=>'gCorrigeData','colunaAlinhar' => 'center', 'style' => 'width:1%'),
		array('colunaValor'=>'T019_Referente', 'colunaTitulo'=>'Descrição'),
		array('colunaValor'=>'T019_Complemento', 'colunaTitulo'=>'Complemento'),
		array('colunaValor'=>'T019_Valor_Lancamento', 'colunaTitulo'=>'Valor', 'colunaCasasDecimais'=>'2','colunaAlinhar' => 'right', 'style' => 'width:1%'),
		array('colunaValor'=>'T019_Saldo', 'colunaTitulo'=>'Saldo', 'colunaCasasDecimais'=>'2','colunaAlinhar' => 'right', 'style' => 'width:1%'),
		array('colunaValor'=>'D027_Portador', 'colunaTitulo'=>'Portador'),
		array('colunaValor'=>'D024_Nome_Fantasia', 'colunaTitulo'=>'Empresa'),
		array('colunaValor'=>'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo'=>'Conta'),
		
		);
		
		$from = 'T019';
		$extra = <<<EOT
		    left join D024 on D024_Id=T019_D024_Id
			left join D027 on D027_Id=T019_D027_Id
			left join D014 on D014_Id=T019_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join D007 on D007_Id=T019_D007_Id
			where (T019_T002_Id='$T002_Id')
			ORDER BY T019_Data_Lancamento DESC, T019_Id DESC
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		//echo <<<EOT
//			<div class="buttonsBottomTop">
//				<!-- <button type="button" id="fin003BtnInserirLancamento">Incluir Lançamento</button> -->
//				<!-- <button type="button" id="fin003BtnAtualizarSaldos">Atualizar Saldos</button> -->
//			</div>
//			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
//				$('#fin003BtnInserirLancamento').unbind('click').bind('click', function(event) {
//					abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Lançamentos' , '/fin/fin003/form/fin003FormLancamentos/', '&tab=geral&D007_Id=' + encodeURIComponent('$D007_Id'), [450,400]);
//				});
//				$('#fin003BtnAtualizarSaldos').unbind('click').bind('click', function(event) {
//				var buttons = {
//					"Sim": function() {
//							$.getJSON('/fin/fin003/grid_func-ajax/atualizarSaldos/?ajax=true&D007_Id=' + encodeURIComponent('$D007_Id') + '&callback=?', function(request) {
//							if(request.data){
//								dialogConfirm('OK!', request.data);
//							}else{
//								dialogConfirm('Erro!', request.data);
//							}
//							divRefresh('{$g['divId']}');
//							divRefresh('{$r_divReload}'); 
//							});
//							$(this).dialog("destroy").remove();
//					     },
//						 "Não": function() { $(this).dialog("destroy").remove(); }
//					}
//					dialogConfirm('Confirmar', "ATENCAO! Deseja atualizar saldo?", buttons);
//				});
//			} </script></div>
//EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		 /**
		* Filtro
		*/
		$gridFiltro = array(
			'T019_Referente' => array('titulo' => 'Referente', 'tipo' => 'texto'),
			'T019_Complemento' => array('titulo' => 'Complemento', 'tipo' => 'texto'),
			'T019_Data_Lancamento' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T019_Valor_Lancamento' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'T019_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'moeda'),
			'D027_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*$valorTotalLancamentos = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra));
		$valorTotalRecebimentos = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(if(T019_Valor_Lancamento>0,T019_Valor_Lancamento,0))", $from, $extra));
		$valorTotalPagamentos = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(if(T019_Valor_Lancamento<0,T019_Valor_Lancamento,0))", $from, $extra));
		$valorTotalResultado = ($valorTotalRecebimentos + $valorTotalPagamentos);
		$ResultadoTotal = gCorrigeNumero($valorTotalResultado);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Lançamentos<br><input type="text" value="{$valorTotalLancamentos}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Recebimentos<br><input type="text" value="{$valorTotalRecebimentos}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Pagamentos<br><input type="text" value="{$valorTotalPagamentos}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Resultado<br><input type="text" value="{$ResultadoTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/

		/**
		* Define a abertura do form
		*/	
		
		$novoIdTelaForm = uniqid();
		//echo  <<<EOT
//		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
//				var dbclickPrevent = false;
//				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
//				if (dbclickPrevent) { return false; }
//				dbclickPrevent = true;
//				setTimeout(function() { dbclickPrevent = false; }, 500);
//				var chavePrimaria = 'T019_Id';	
//				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
//					var id = $(this).closest('tr').attr('id').replace(/T019_Id-/, '');
					//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Lançamento - ID: ' + id, '/fin/fin003/content/fin003ContentEditaLancamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}&D007_Id='+{$D007_Id}, [600,500]);
//				
				//var id = $(this).closest('tr').attr('id').split('-');
				//var acaoId = id[1].replace(chavePrimaria , '-', '');
				//divSetarAcaoId('{$r_divReload}', acaoId);
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Lançamentos' , '/fin/fin003/content/fin003ContentEditaLancamento/', '&tab=geral&T019_Id='+acaoId+'&acaoId='+acaoId+'&D007_Id='+{$D007_Id}, [450,400]);
//			});
//		} </script></div>
//EOT;

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Recebimentos', 'valor' => 'if(T019_Valor_Lancamento>0,T019_Valor_Lancamento,0)', 'titulo' => 'Recebimentos'),
			array('nome' => 'Pagamentos', 'valor' => 'if(T019_Valor_Lancamento<0,T019_Valor_Lancamento,0)', 'titulo' => 'Pagamentos'),
			array('nome' => 'Resultado', 'tipo'=>'totais', 'valor' => '{Recebimentos}+{Pagamentos}', 'titulo' => 'Resultado'),
        );
			
	     /**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }		
  		
  	break;
  	
  	case 'fin001GridCobrancas':
		/**
		* Definição do Grid (Tabela)
		*/

		$filtroObrigatorio = true;

		$grid = array(
			array('colunaValor' => 'validaCampoExtratoCliente', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusao', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Flag_Reparcelar', 'colunaTitulo' => 'Rp', 'callback' => 'validaCampoRp', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'T002_D024_Id', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Cnpj', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'left', 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'C004_Id', 'colunaTitulo' => 'Empresa', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
			array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Cliente', 'colunaExibir' => false, 'colunaDimensao' => 'S'),
			array('colunaValor' => "C004_Nome_Abreviado", 'colunaTitulo' => 'Empresa', 'colunaExibir' => false, 'colunaDimensao' => 'S'),
			array('colunaValor' => "T002_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'N.Dupl.', 'callback' => 'validaCampoDuplicata', 'callbackParameter' => array('15', ',', 'T002_Observacao_Reparcelamento'), 'style' => 'width:1%;'),
			array('colunaValor' => "T002_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
			array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'datediff(if(T002_Data_Recebimento=\'0000-00-00\',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) as Atraso', 'colunaTitulo' => 'Ats', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "T002_Data_Recebimento", 'colunaTitulo' => 'Receb', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
			array('colunaValor' => "T117_Data_Vencimento", 'colunaTitulo' => 'Boleto', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
			array('colunaValor' => "T002_Valor_Duplicata", 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'style' => 'width: 1%', 'colunaTotal' => true, 'colunaMetrica' => 'S'),
			array('colunaValor' => "T002_Valor_Juros", 'colunaTitulo' => 'Jur','colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'style' => 'width: 1%', 'colunaTotal' => true, 'colunaMetrica' => 'S'),
			array('colunaValor' => "T002_Valor_Desconto", 'colunaTitulo' => 'Desc','colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'style' => 'width: 1%', 'colunaTotal' => true, 'colunaMetrica' => 'S'),
			array('colunaValor' => "T002_Valor_Recebido", 'colunaTitulo' => 'Receb','colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'style' => 'width: 1%', 'colunaTotal' => true, 'colunaMetrica' => 'S'),
			array('colunaValor' => "T002_Valor_Total", 'colunaTitulo' => 'Total','colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'style' => 'width: 1%', 'colunaTotal' => true, 'colunaMetrica' => 'S'),
			array('colunaValor' => "T002_Valor_Saldo", 'colunaTitulo' => 'Saldo','colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'style' => 'width: 1%', 'colunaTotal' => true, 'colunaExibir' => false),
			array('colunaValor' => "D027_Portador", 'colunaTitulo' => 'Portador', 'style' => 'width: 1%'),
			array('colunaValor' => "C007_Primeiro_Nome", 'colunaTitulo' => 'Vendedor', 'style' => 'width: 1%'),
			array('colunaValor' => "T002_Data_Previsao_Recebimento", 'colunaTitulo' => 'Previsão', 'colunaEditar' => true, 'callback' => 'gCorrigeData', 'style' => 'width: 65px'),
			array('colunaValor' => "D085_Data_Previsao_Retorno", 'colunaTitulo' => 'Retornar', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
			//array('colunaValor' => "T002_Observacao", 'colunaTitulo' => 'Observação', 'colunaEditar' => true),
			array('colunaValor' => "D085_Data", 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "IFNULL(D085_Mensagem,'Incluir Observação')", 'colunaTitulo' => 'Observação', 'callback' => 'abrirJanela', 'callbackParameter' => array('Agenda', '/fin/fin001/content/fin001ContentAgendaCobranca/', 'T002_Id', '1060,600')),
			array('colunaValor' => "CONCAT(D024_Id,' - ',D024_Nome_Fantasia,' (',D024_Nome_Empresa,')',' - ',ifnull(D020_Nome_Cidade,''),' - ',ifnull(D018_UF,''),' / ',
                                                                   ifnull(D024_DDD_Telefone_1, ''),' ',ifnull(D024_Telefone_1,''),' / ',
                                                                   ifnull(D024_DDD_Telefone_2,''),' ',ifnull(D024_Telefone_2,''),' / ',
                                                                   ifnull(D024_DDD_Celular,''), ' ', ifnull(D024_Celular,''),' / ',
                                                                   ifnull((SELECT substring_index(GROUP_CONCAT(D013_Nome_Contato SEPARATOR ', '), ', ', 1) FROM D013 WHERE  D013_D024_Id=D024_Id),'')) as Empresa", 'colunaTitulo' => 'Empresa', 'colunaExibir' => false),
			array('colunaValor' => "T002_Flag_Status", 'colunaTitulo' => 'Status', 'colunaExibir' => false),
			

			//array('colunaValor' => "T002_Data_Recebimento", 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
			//array('colunaValor' => "T002_Numero_Documento", 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "T002_T035_Id", 'colunaTitulo' => 'Rm', 'callback' => 'validaCampoRm', 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
			//array('colunaValor' => "CampoBx", 'colunaTipo' => "livre", 'colunaTitulo' => 'Bx', 'callback' => 'validaCampoBx', 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
			//array('colunaValor' => "concat(T002_Numero_Parcela,'/',T002_Total_Parcelas) as Parcelas", 'colunaTitulo' => 'Parc'),
			//array('colunaValor' => "datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo", 'colunaTitulo' => 'Prz'),
			//array('colunaValor' => "datediff(if(T002_Data_Recebimento='0000-00-00',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) as Atraso", 'colunaTitulo' => 'Ats'),
			//array('colunaValor' => "T117_Nosso_Numero", 'colunaTitulo' => 'Nosso Número'),
			//array('colunaValor' => "C007_Nome", 'colunaTitulo' => 'Vendedor'),
			//array('colunaValor' => "D014_SubConta", 'colunaTitulo' => 'Conta'),
			//array('colunaValor' => "D046_Nome", 'colunaTitulo' => 'Grupo'),
			//array('colunaValor' => "T002_T045_Id", 'colunaTitulo' => 'Lote'),
            //array('colunaValor' => "T002_Flag_Status", 'colunaTitulo' => 'Status', 'callback' => 'fin001CampoStatus'),
			//array('colunaValor' => "T002_Flag_Baixar_Lote", 'colunaTitulo' => 'Lote', 'colunaExibir' => false),
			//array('colunaValor' => "T002_Agrupamento", 'colunaTitulo' => 'Agrupamento', 'colunaExibir' => false),
			//array('colunaValor' => "T002_Flag_Enviar_Banco", 'colunaTitulo' => '', 'colunaExibir' => false),
			//array('colunaValor' => "D024_Nome_Fantasia", 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
			//array('colunaValor' => "T117_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'colunaExibir' => false),
			//array('colunaValor' => "T002_Flag_Reparcelar", 'colunaTitulo' => 'Reparcelar', 'colunaExibir' => false),
			//array('colunaValor' => "T002_Flag_Cancelada", 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
			//array('colunaValor' => "T035_Data_Processamento", 'colunaTitulo' => 'Processamento', 'colunaExibir' => false),
			//array('colunaValor' => "T007_Id", 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
		-- JOIN
			left join D024 on D024_Id=T002_D024_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D027 on D027_Id=T002_D027_Id
			left join T117 on T117_Id=T002_T117_Id and T117_Flag_Cancelado!='S'
			left join D014 on D014_Id=T002_D014_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T007 on T007_Id=T002_T007_Id
			left join T035 on T035_Id=T002_T035_Id
			left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
			left join D085 on T002_Id=D085_T002_Id AND D085_Id = (SELECT MAX(D085_Id) FROM D085 Where D085_T002_Id = T002_Id)
			left join T103 on T002_Id=T103_T002_Id
			left join C004 on C004_Id=T002_C004_Id
		-- WHERE
			where (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
			  and T002_Flag_Cancelada!='S'
			  and T002_Valor_Total>0
		-- GROUP BY
			GROUP BY T002_Id
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T002_T002_Id_Agrupado'] > 0){
				return 'linhaLaranja';
			} else if($array['todosCampos']['T002_Data_Recebimento']!='0000-00-00'){
				return 'linhaCinza';
			} else if ($array['todosCampos']['T002_Flag_Status']=='2') {
				return 'linhaVermelho';
			} else if ($array['todosCampos']['T002_Flag_Status']=='3') {
                return 'linhaAzul';
            }
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Id' => array('titulo' => 'Cod Cliente', 'tipo' => 'numero'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'D024_Cnpj' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
			'T002_Numero_Duplicata' => array('titulo' => 'Número', 'tipo' => 'texto'),
			'T002_Valor_Duplicata,T002_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'T002_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T002_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
			'T002_Data_Recebimento' => array('titulo' => 'Recebimento', 'tipo' => 'data'),
			'D027_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
			'T117_Nosso_Numero' => array('titulo' => 'Nosso Numero', 'tipo' => 'texto'),
			'T117_Data_Vencimento' => array('titulo' => 'Boleto', 'tipo' => 'data'),
			'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'D046_Nome' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'T002_Data_Previsao_Recebimento' => array('titulo' => 'Previsão', 'tipo' => 'data'),
			'D085_Data_Previsao_Retorno' => array('titulo' => 'Retornar', 'tipo' => 'data'),
			'D085_Data' => array('titulo' => 'Data Obs', 'tipo' => 'data'),
			'D085_Mensagem' => array('titulo' => 'Observaçao', 'tipo' => 'texto'),
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'campos' => array(array('title' => 'Rota', 'campoAgrupar' => 'D024_Id', 'campoMostrar' => 'Empresa', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),), 'colunaValor' => 'Agrupar', 'exibir' => false),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode(gRetirarOrderBy($extra)));
		$extra__ = base64_encode(gRetirarOrderBy($extra));

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Reparcelar', 'id' => 'fin001Reparcelar2'),
			array('titulo' => 'Simular', 'id' => 'fin001Simular'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
        // Não executar quando houver o refresh de linha
        /*if (empty($r_linhaGridId)) {
            
			<div class="buttonsBottomTop">
				<!--<button type="button" id="fin001Incluir">Incluir</button>-->
				<!--<button type="button" id="fin001Remessa">Remessa</button>-->
				<!--<button type="button" id="fin001Baixar">Baixar</button>-->
				<!--<button type="button" id="fin001Agrupar">Agrupar</button>-->
				<button type="button" id="fin001Reparcelar2">Reparcelar</button>
				<button type="button" id="fin001Simular">Simular</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin001Incluir').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Receber', '/fin/fin001/content/fin001content02/', '', [600,500]);
				});			
				$('#fin001Remessa').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin001/grid_func-ajax/verificaRemessa/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code==0) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Gerar Remessa Bancária', '/fin/fin001/content/fin001content03/', '', [325,150]);
						} else {
							var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}		
							dialogConfirm('Remessa', request.data, buttons);							
						}
					});
				});				
				$('#fin001Agrupar').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin001/grid_func-ajax/verificaAgrupar/?ajax=true&extra={$extra_}&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code==0) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Agrupar', '/fin/fin001/content/fin001content04/', '&extra={$extra_}', [700,500]);
						} else {
							var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}		
							dialogConfirm('Agrupamento', request.data, buttons);							
						}
					});
				});				
				$('#fin001Baixar').bind('click', function(data) {			    			
				    $('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin001/grid_func-ajax/verificaBaixar/?ajax=true&extra={$extra_}&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code==0) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Baixar', '/fin/fin001/content/fin001content05/', '&extra={$extra_}', [900,550]);
						} else {
							var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}		
							dialogConfirm('Baixa de Títulos', request.data, buttons);							
						}
					});
				});
				$('#fin001Reparcelar2').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin001/grid_func-ajax/verificaReparcelar/?ajax=true&extra={$extra_}&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code == 0) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Duplicatas para Reparcelamento', '/fin/fin001/content/fin001ContentPagamento/', '&extra={$extra_}', [950,650]);
						} else {
							var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}
							dialogConfirm('Reparcelar', request.data, buttons);
						}
					});
				});

				$('#fin001Simular').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin001/grid_func-ajax/verificaReparcelar/?ajax=true&extra={$extra_}&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code == 0) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Duplicatas para Reparcelamento', '/fin/fin001/content/fin001ContentPagamento/', '&extra={$extra_}&simular=true', [950,650]);
						} else {
							var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}
							dialogConfirm('Reparcelar', request.data, buttons);
						}
					});
				});
			} </script></div>
EOT;
        //}

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Recebido', 'valor' => "if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Recebido,0)", 'titulo' => 'Recebido'),
			array('nome' => 'Atrasado', 'valor' => "if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0)", 'titulo' => 'Atrasado'),
			array('nome' => 'aReceber', 'valor' => "if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0)", 'titulo' => 'A Receber'),
			array('nome' => 'Total_Receber', 'valor' => "if(T002_Data_Recebimento='0000-00-00',T002_Valor_Saldo,0)", 'titulo' => 'Total Receber'),
			//array('nome' => 'Reparcelar', 'valor' => "", 'titulo' => 'Reparcelar'),
        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000);
		 /**
		 * Totalizadores
		 */
		/*$extraTotal = gRetirarGroupBy($extra);
		$valorTotalRecebido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Recebido,0))", $from, $extraTotal));
		$valorTotalAtrasado = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0))", $from, $extraTotal));
		$valorAReceber = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0))", $from, $extraTotal));
		$valorTotalAReceber = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento='0000-00-00',T002_Valor_Saldo,0))", $from, $extraTotal));
		$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extraTotal);*/


 		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<!--<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalTitulos}" size="11" readonly></span>
				<span class="item" style="text-align:right">Recebido<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorTotalRecebido}" size="11" readonly></span>
				<span class="item" style="text-align:right">Atrasado<br><input type="text" class="whiteBg" id="inputAtrasado" value="{$valorTotalAtrasado}" size="11" readonly></span>
				<span class="item" style="text-align:right">A Receber<br><input type="text" class="whiteBg" id="inputAReceber" value="{$valorAReceber}" size="11" readonly></span>
				<span class="item" style="text-align:right">Total Receber<br><input type="text" class="whiteBg" id="inputTotalReceber" value="{$valorTotalAReceber}" size="11" readonly></span>
				<span class="item" style="width:10px;border-right:1px solid lightblue"> </span>
				<span class="item" style="width:10px;"> </span>-->
				<!-- <span class="item" style="text-align:right">Baixar<br><input type="text" class="whiteBg" id="inputBaixar" size="11" readonly></span> -->
				<span class="item" style="text-align:right">Reparcelar<br><input type="text" class="whiteBg" id="inputReparcelar2" size="11" readonly></span>
				<!-- <span class="item" style="text-align:right">Agrupar<br><input type="text" class="whiteBg" id="inputAgrupar" size="11" readonly></span> -->
				<!-- <span class="item" style="text-align:right">Remessa<br><input type="text" class="whiteBg" id="inputRemessa" size="11" readonly></span> -->
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin001ExtraBtns').data('extra', '{$extra__}');
				//carregaInputBaixar();
				carregaInputReparcelar2();
				//carregaInputAgrupar();
				//carregaInputRemessa();
			} </script></div>
EOT;
	  
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T002_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1050,540]);
			});
		} </script></div>
EOT;
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); };
	break;

	case 'fin001GridRetorno':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "T038_Id", 'colunaTitulo' => 'Id','chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
			array('colunaValor' => "T038_T002_Id", 'colunaTitulo' => 'Id','chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
			array('colunaValor' => 'T038_Codigo_Natureza_Recebimento', 'colunaTitulo' => 'Descrição Recebimento', 'colunaExibir' => false),
			array('colunaValor' => "T002_Data_Recebimento", 'colunaTitulo' => 'Data Recebimento', 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
			array('colunaValor' => "T037_Nome_Arquivo", 'colunaTitulo' => 'Arquivo', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "T037_Codigo_Banco", 'colunaTitulo' => 'Banco', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => "concat(T038_Nosso_Numero, '-', T038_Digito_Verificador_Nosso_Numero) as NossoNumero", 'colunaTitulo' => 'Nosso número', 'style' => 'width:1%', 'colunaTipo' => 'banco'),
			array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'Nº Dupl', 'style' => 'width:1%'),
			array('colunaValor' => "Ocorrencia", 'colunaTitulo' => 'Ocorrência', 'callback' => 'statusOcorrenciaRetorno', 'colunaTipo' => 'livre'),
			array('colunaValor' => "Descricao", 'colunaTitulo' => 'Descrição', 'callback' => 'descricaoOcorrenciaRetorno', 'colunaTipo' => 'livre'),
			array('colunaValor' => "T038_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback'=>'gCorrigeData'),
			array('colunaValor' => "T038_Data_Recebimento", 'colunaTitulo' => 'Recebimento', 'callback'=>'gCorrigeData'),
			array('colunaValor' => "T038_Data_Credito", 'colunaTitulo' => 'Data Crédito', 'callback'=>'gCorrigeData'),
			array('colunaValor' => "T038_Valor_Titulo", 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "T002_Valor_Total", 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			array('colunaValor' => "T002_Valor_Saldo", 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			array('colunaValor' => "T038_Valor_Abatimento", 'colunaTitulo' => 'Abatimento', 'colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			array('colunaValor' => "T038_Valor_Desconto", 'colunaTitulo' => 'Desconto', 'colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "T038_Valor_Juros_Mora", 'colunaTitulo' => 'Juros Mora', 'colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "T038_Valor_Taxa_IOF", 'colunaTitulo' => 'IOF', 'colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			array('colunaValor' => "T038_Valor_Outros_Recebimentos", 'colunaTitulo' => 'Outros Vlrs.', 'colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "T038_Valor_Recebido", 'colunaTitulo' => 'Recebido', 'colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "C007_Primeiro_Nome", 'colunaTitulo' => 'Processou'),
			array('colunaValor' => "T038_Data_Hora_Processado", 'colunaTitulo' => 'Data Processado', 'callback' => 'gCorrigeData'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T038';

		$extra = <<<EOT
			LEFT JOIN T002 ON T002_Id = T038_T002_Id
			LEFT JOIN T037 ON T037_Id = T038_T037_Id
			LEFT JOIN C007 ON C007_Id = T038_C007_Id_Processado
			WHERE T038_T002_Id = '{$r_acaoId}'
			ORDER BY T038_Id DESC
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );


		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'fin001GridRetornoRemessas':

			
		 /**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(		
			array('colunaValor' => "excluirRetorno", 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center', 'callback'=>'excluirRetorno', 'colunaExibir'=>true, 'style' => 'width:1%'),
			array('colunaValor' => "T037_Id", 'colunaTitulo' => 'Id','chavePrimaria'=>true, 'colunaAlinhar' => 'center' ),			
			array('colunaValor' => "T037_Data_Processamento", 'colunaTitulo' => 'Data processamento', 'colunaAlinhar' => 'center', 'callback' => "gCorrigeData"),
			array('colunaValor' => "T037_Nome_Arquivo", 'colunaTitulo' => 'Nome Arquivo'),
			array('colunaValor' => "T037_Numero_Titulos", 'colunaTitulo' => 'Total Títulos'),
			array('colunaValor' => "D007_Nome_Banco", 'colunaTitulo' => 'Banco'),
			array('colunaValor' => "baixarRetorno", 'colunaTipo'=>'livre', 'colunaTitulo' => 'Baixar', 'callback'=>'fin001BaixarRetorno', 'style'=>'width:1%;', 'colunaExibir'=>true),
			//array('colunaValor' => "T035_Total_Remessa", 'colunaTitulo' => 'Valor remessa R$','callback' => 'campoDownloadRemessa5'),
		);
			
		/**
		* Definição da SQL
		*/
		$from = 'T037';
		
		$extra = <<<EOT
		LEFT JOIN D007 ON D007_Id=T037_D007_Id
		WHERE (T037_C004_Id='{$g['empresaAtual']}')
EOT;

		/*
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T037_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Relatório retorno - ID: ' + id, '/fin/fin001/content/fin001contentRelatorioRetorno/', '&T037_Id=' + encodeURIComponent(id) + '&tabela={$from}', [900,550]);
				});
			} </script></div>
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);
    
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T037_Nome_Arquivo' => array('titulo' => 'Nome do Arquivo', 'tipo' => 'texto'),
            'T037_Data_Processamento' => array('titulo' => 'Data de Processamento', 'tipo' => 'data'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		


		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); } 
    break;
     
    case 'fin001GridRelatorioRetorno':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T038_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T038_T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T038_T037_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T037_Codigo_Banco', 'colunaTitulo' => 'Banco', 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Data Recebimento', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Valor_Duplicata', 'colunaTitulo' => 'Data Recebimento', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T038_Flag_Autorizado', 'colunaTitulo' => '', 'callback' => 'campoAutorizarBaixaRetorno', 'style' => 'width:1%'),
            array('colunaValor' => 'concat(T038_Nosso_Numero, \'-\', ifnull(T038_Digito_Verificador_Nosso_Numero, \'\')) as NossoNumero', 'colunaTitulo' => 'Nosso número', 'style' => 'width:1%', 'colunaTipo' => 'banco'),
            array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'Nº Dupl', 'style' => 'max-width:200px;white-space:normal; word-wrap: break-word;'),
            array('colunaValor' => 'SUBSTR(C004_Nome_Abreviado, 1, 20) as Empresa', 'colunaTitulo' => 'Empresa', 'style' => 'width:1%', 'colunaTipo' => 'banco'),
            array('colunaValor' => 'SUBSTR(D024_Nome_Empresa, 1, 20) as Cliente', 'colunaTitulo' => 'Cliente', 'style' => 'width:1%', 'colunaTipo' => 'banco'),
            array('colunaValor' => 'T038_Digito_Verificador_Nosso_Numero', 'colunaTitulo' => '', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T038_Codigo_Natureza_Recebimento', 'colunaTitulo' => 'Ocorrência', 'callback' => 'statusOcorrenciaRetorno'),
            array('colunaValor' => 'T038_Codigo_Motivo', 'colunaTitulo' => 'Descrição', 'callback' => 'descricaoOcorrenciaRetorno'),
            array('colunaValor' => 'T038_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'datediff(T038_Data_Recebimento,T038_Data_Vencimento) as Atraso', 'colunaTitulo' => 'Ats', 'style' => 'width:1%;'),
            array('colunaValor' => 'T038_Data_Recebimento', 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T038_Data_Credito', 'colunaTitulo' => 'Data Crédito', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T038_Valor_Titulo', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T038_Valor_Multa', 'colunaTitulo' => 'Multa', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T038_Valor_Abatimento', 'colunaTitulo' => 'Abatimento', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T038_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T038_Valor_Juros_Mora', 'colunaTitulo' => 'Juros Mora', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T038_Valor_Taxa_IOF', 'colunaTitulo' => 'IOF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T038_Valor_Outros_Recebimentos', 'colunaTitulo' => 'Outros Vlrs.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T038_Valor_Outras_Despesas', 'colunaTitulo' => 'Tarifas', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T038_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T038_Info_Complementar_1', 'colunaTitulo' => 'T038_Info_Complementar_1', 'colunaExibir' => false),
            array('colunaValor' => 'T038_Info_Complementar_2', 'colunaTitulo' => 'T038_Info_Complementar_2', 'colunaExibir' => false),
            array('colunaValor' => 'T038_Info_Complementar_3', 'colunaTitulo' => 'T038_Info_Complementar_3', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Valor_Saldo', 'colunaTitulo' => 'T002_Valor_Saldo', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => "C007_Primeiro_Nome", 'colunaTitulo' => 'Processou'),
			array('colunaValor' => "T038_Data_Hora_Processado", 'colunaTitulo' => 'Data Processado', 'callback' => 'gCorrigeData'),
        );
			
		/**
		* Definição da SQL
		*/
		$from = 'T038';
		
		$extra = <<<EOT
			LEFT JOIN T002 ON T002_Id=T038_T002_Id
			LEFT JOIN D024 ON D024_Id=T002_D024_Id
			LEFT JOIN C004 ON C004_Id=T002_C004_Id
			LEFT JOIN T037 ON T037_Id=T038_T037_Id
			LEFT JOIN C007 ON C007_Id = T038_C007_Id_Processado
			WHERE (T038_T037_Id='{$r_T037_Id}')
			ORDER BY T038_Codigo_Natureza_Recebimento, T038_Id 
EOT;
		function gridLinha($array) {
			global $g;
			$codigoBanco = $array['todosCampos']['T037_Codigo_Banco'];		
			if($codigoBanco == '237'){
				$ocorrenciasAceitas = array('06','15','17');
			} else if($codigoBanco == '341'){
				$ocorrenciasAceitas = array('06','08');
			} else if($codigoBanco == '399'){
				$ocorrenciasAceitas = array('06','07','15','16','31','32','33','36','38','39');
			} else if($codigoBanco == '001'){
				$ocorrenciasAceitas = array('05','06','15');
			} else if($codigoBanco == '748'){
				$ocorrenciasAceitas = array('6','15','17');
			}else if($codigoBanco == '756'){
				$ocorrenciasAceitas = array('5','6','15');
			}else if($codigoBanco == '104'){
				$ocorrenciasAceitas = array('21','22','23','25');
			}else if($codigoBanco == '422'){
				$ocorrenciasAceitas = array('06','15','41');
			}
			$statusOc = $array['todosCampos']['T038_Codigo_Natureza_Recebimento'];
			$T002_Id = $array['todosCampos']['T038_T002_Id'];
			$T002_Valor_Saldo = $array['todosCampos']['T002_Valor_Saldo'];
			$valorRecebido = $array['todosCampos']['T038_Valor_Recebido'];
			$outrasRecebimentos = $array['todosCampos']['T038_Valor_Outros_Recebimentos'];
			$valorMulta = $array['todosCampos']['T038_Valor_Multa'];
			$outrasDespesas = $array['todosCampos']['T038_Valor_Outras_Despesas'];
			$valorIOF = $array['todosCampos']['T038_Valor_Taxa_IOF'];
			$valorJuros = $array['todosCampos']['T038_Valor_Juros_Mora'];
			$valorAbatimento = $array['todosCampos']['T038_Valor_Abatimento'];
			$valorDesconto = $array['todosCampos']['T038_Valor_Desconto'];
			$dataRecebimento = $array['todosCampos']['T002_Data_Recebimento'];
			$dataCredito = $array['todosCampos']['T038_Data_Credito'];
			$valorCalculado = $T002_Valor_Saldo + $valorJuros + $outrasRecebimentos + $valorIOF + $valorMulta - $outrasDespesas;
			$valorCalculado = number_format($valorCalculado, 2, '.', '');
			$T002_Valor_Total = $array['todosCampos']['T002_Valor_Total'];

			if($dataRecebimento != '0000-00-00' && $T002_Id != '0'){
				return 'linhaCinza';
			}else if($T002_Id == '0') {
				return 'linhaVermelhoBranco';
			}else if($array['todosCampos']['T038_Flag_Autorizado'] != ''){
				return 'linhaVermelho';
			}else if($T002_Id != '0' && $valorRecebido == $T002_Valor_Total && (in_array($statusOc, $ocorrenciasAceitas) && $dataCredito != '0000-00-00')){
				return 'linhaVerde';
			}

			/*if($dataRecebimento != '0000-00-00' && $T002_Id != '0'){
				return 'linhaCinza';
			} else if($T002_Id != '0' && $valorRecebido == $T002_Valor_Total && (in_array($statusOc, $ocorrenciasAceitas) && $dataCredito != '0000-00-00')){
				return 'linhaVerde';
			} else if(in_array($statusOc, $ocorrenciasAceitas))  {
				if($T002_Id == '0') {
					return 'linhaVermelho';
				} else if($T002_Id != '0' && ($valorRecebido != $T002_Valor_Total || ($valorAbatimento > 0 || $valorDesconto > 0))){
					return 'linhaVermelho';
				}
			}*/
		}
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var T002_Id = id[1].replace(/T038_T002_Id-/, '');
					if(T002_Id != 0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + T002_Id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(T002_Id) + '&tabela=T002', [920,540]);
					}
				});
			} </script></div>
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);
    
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T002_Numero_Duplicata' => array('titulo' => 'Número duplicata', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T038_Codigo_Natureza_Recebimento' => array('titulo' => 'Ocôrrência', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor', 'valor' => 'T038_Valor_Titulo', 'titulo' => 'Valor'),
			array('nome' => 'Abatimento', 'valor' => 'T038_Valor_Abatimento', 'titulo' => 'Abatimento'),
			array('nome' => 'Desconto', 'valor' => 'T038_Valor_Desconto', 'titulo' => 'Desconto'),
			array('nome' => 'Juros_Mora', 'valor' => 'T038_Valor_Juros_Mora', 'titulo' => 'Juros mora'),
			array('nome' => 'Multa', 'valor' => 'T038_Valor_Multa', 'titulo' => 'Multa'),
			array('nome' => 'IOF', 'valor' => 'T038_Valor_Taxa_IOF', 'titulo' => 'IOF'),
			array('nome' => 'Outros_Valores', 'valor' => 'T038_Valor_Outros_Recebimentos', 'titulo' => 'Outros valores'),
			array('nome' => 'Tarifas', 'valor' => 'T038_Valor_Outras_Despesas', 'titulo' => 'Tarifas'),
			array('nome' => 'Recebido', 'valor' => 'T038_Valor_Recebido', 'titulo' => 'Recebido'),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Totais(Versão Acima do GRID)
		*/
		/*if (!personalizacaoTotais(false, true)) {
		    $sqlTotal = $g['sqlAuto']->pegarSqlCampos("COUNT(*),
			        		                           SUM(T038_Valor_Titulo),
													   SUM(T038_Valor_Abatimento),
													   SUM(T038_Valor_Desconto),
													   SUM(T038_Valor_Juros_Mora),
													   SUM(T038_Valor_Multa),
													   SUM(T038_Valor_Taxa_IOF),
													   SUM(T038_Valor_Outros_Recebimentos),
													   SUM(T038_Valor_Outras_Despesas),
													   SUM(T038_Valor_Recebido)",
		        	        	                       $from,
		        	            	                   $extra);

			list($numeroRegistros,
				$T038_Valor_Titulo,
				$T038_Valor_Abatimento,
				$T038_Valor_Desconto,
				$T038_Valor_Juros_Mora,
				$T038_Valor_Multa,
				$T038_Valor_Taxa_IOF,
				$T038_Valor_Outros_Recebimentos,
				$T038_Valor_Outras_Despesas,
				$T038_Valor_Recebido) = $sqlTotal;

			$valorTotalSemDesconto = gCorrigeNumero($valorTotalSemDesconto);
			$T038_Valor_Titulo = gCorrigeNumero($T038_Valor_Titulo);
			$T038_Valor_Abatimento = gCorrigeNumero($T038_Valor_Abatimento);
			$T038_Valor_Desconto = gCorrigeNumero($T038_Valor_Desconto);
			$T038_Valor_Juros_Mora = gCorrigeNumero($T038_Valor_Juros_Mora);
			$T038_Valor_Multa = gCorrigeNumero($T038_Valor_Multa);
			$T038_Valor_Taxa_IOF = gCorrigeNumero($T038_Valor_Taxa_IOF);
			$T038_Valor_Outros_Recebimentos = gCorrigeNumero($T038_Valor_Outros_Recebimentos);
			$T038_Valor_Outras_Despesas = gCorrigeNumero($T038_Valor_Outras_Despesas);
			$T038_Valor_Recebido = gCorrigeNumero($T038_Valor_Recebido);

			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" size="5" value="{$numeroRegistros}" readonly></span>
					<span class="item" style="text-align:right">Valor<br><input type="text" class="whiteBg" size="5" value="{$T038_Valor_Titulo}" readonly></span>
					<span class="item" style="text-align:right">Abatimento<br><input type="text" class="whiteBg" size="5" value="{$T038_Valor_Abatimento}" readonly></span>
					<span class="item" style="text-align:right">Desconto<br><input type="text" class="whiteBg" size="5" value="{$T038_Valor_Desconto}" readonly></span>
					<span class="item" style="text-align:right">Juros mora<br><input type="text" class="whiteBg" size="5" value="{$T038_Valor_Juros_Mora}" readonly></span>
					<span class="item" style="text-align:right">Multa<br><input type="text" class="whiteBg" size="5" value="{$T038_Valor_Multa}" readonly></span>
					<span class="item" style="text-align:right">IOF<br><input type="text" class="whiteBg" size="5" value="{$T038_Valor_Taxa_IOF}" readonly></span>
					<span class="item" style="text-align:right">Outros valores<br><input type="text" class="whiteBg" size="5" value="{$T038_Valor_Outros_Recebimentos}" readonly></span>
					<span class="item" style="text-align:right">Tarifas<br><input type="text" class="whiteBg" size="5" value="{$T038_Valor_Outras_Despesas}" readonly></span>
					<span class="item" style="text-align:right">Recebido<br><input type="text" class="whiteBg" size="5" value="{$T038_Valor_Recebido}" readonly></span>
				    <div style="clear:both;"></div>
				</div>
EOT;
		}*/
		//fimTotais();
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); } 
    break;

	case 'fin001GridBoletosTitulo':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T117_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'T117_T035_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false), //se necessário usar o T002_T035_Id
			//array('colunaValor' => 'T117_Flag_Enviar_Email', 'callback' => 'campoMarcarBoletoEnvio', 'colunaTitulo' => 'Email', 'colunaEditarTodos' => 'S', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'T117_Flag_Atualizar', 'callback' => 'campoMarcarBoletoAtualizar', 'colunaTitulo' => 'Atualiz.', 'colunaEditarTodos' => 'S', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T117_Flag_Layout', 'colunaTitulo' => 'FlagLayout', 'colunaExibir' => false),
			array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Nosso Número'),
			array('colunaValor' => "group_concat(DISTINCT T002_Numero_Duplicata order by T002_Numero_Duplicata SEPARATOR ',') as T002_Numero_Duplicata", 'colunaTitulo' => 'Duplicata'),
			array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Banco'),
			array('colunaValor' => 'T117_D024_Id', 'colunaTitulo' => 'IdCliente','colunaExibir' => false),
			//array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'T117_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T117_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebido', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T117_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => 'T117_Flag_Cancelado', 'colunaTitulo' => 'Cancelado', 'colunaExibir' => false),
			array('colunaValor' => 'campoCancelar', 'colunaTipo' => 'livre', 'callback' => 'campoCancelarT117'),
			array('colunaValor' => 'campoAtualizarVencimento', 'colunaTipo' => 'livre', 'callback' => 'campoAtualizarVencimento'),
			//array('colunaValor' => 'T117_Data_Envio_Email', 'colunaTitulo' => 'eMail', 'callback' => 'campoeMail', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_E_Mail', 'colunaTitulo' => 'E-mail'),
			array('colunaValor' => 'T117_T002_Id', 'colunaExibir' => false),
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T117';
		$extra = <<<EOT
			left join D024 on D024_Id=T117_D024_Id
	 		left join T002 on T002_T117_Id=T117_Id OR T117_T002_Id=T002_Id
	 		left join D007 on D007_Id=T117_D007_Id
	 		where T002_Id = '{$r_acaoId}'
	 		GROUP BY T117_Id
EOT;
		function gridLinha($array) {
			global $g;
			$dataRecebimento = $array['todosCampos']['T002_Data_Recebimento'];
			$cancelado = $array['todosCampos']['T117_Flag_Cancelado'];
			if($cancelado == 'S'){
				return 'linhaVermelho';
			} else if($dataRecebimento != '0000-00-00'){
				return 'linhaCinza';
			} 
		}

		/**
         * Botões Almir
         */
        $botoes = array(
			);
		echo Botoes::processar($botoes);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor_Total_Geral', 'valor' => 'T117_Valor_Total', 'titulo' => 'Valor Total Geral'),
        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
	
		/**
		* Botões (Versão Acima do GRID)
		*/
		/*$valorRegistros = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, str_replace('GROUP BY T117_Id', '', $extra));
		$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T117_Valor_Total)", $from, str_replace('GROUP BY T117_Id', '', $extra)));
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Boletos<br><input type="text" value="{$valorRegistros}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Total Geral<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
			$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
				// (substituir pelo mesmo ID da chave primária do grid)
				var chavePrimaria = 'T117_Id';	
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var acaoId = id[0].replace(chavePrimaria + '-', '');
				// Ação: abrir nova janela do browser 
				// Alerta: função do sistema antigo, chama uma arquivo diretamente do sistema antigo
				$.getJSON('/fin/fin005/grid_func-ajax/gerarPDFboleto/?ajax=true&T117_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {
					if(request.code){
						var w = window.open('{$confUsuario['urlRaiz']}' + request.data,'_blank');
						w.addEventListener("load", function(event) {
							setTimeout(function(){
								$.getJSON('/fin/fin005/grid_func-ajax/fin005DeletarPDFBoleto/?ajax=true&T117_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {});
							}, 5000);
						});
					} else {
						dialogConfirm('Operação cancelada', request.data);
					}
				});
	        });
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;     

	case 'fin001GridDocumentos':
    	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(			
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T242_Id', $g['divId'])),
			array('colunaValor' => "T242_Id", 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => "T242_Data_Hora_Upload", 'colunaTitulo' => 'Upload', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => "T242_Nome_Arquivo", 'colunaTitulo' => 'Nome do Documento'),
			array('colunaValor' => "campoAbrirDocumento", 'colunaTitulo' => 'Arquivo', 'callback' => 'campoAbrirDocumento', 'colunaTipo' => 'livre', 'style' => 'width: 1%'),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T242';
		
		$extra = <<<EOT
			WHERE T242_T002_Id = '{$r_T002_Id}'
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );


		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

    case 'fin001GridAgendaCobranca':
    	$sqlD024 = mysql_query("SELECT T002_D024_Id FROM T002 WHERE T002_Id = '{$r_acaoId}'");
    	$rowD024 = mysql_fetch_array($sqlD024);
    	$D024_Id = $rowD024['T002_D024_Id'];
    	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D085_Id', $g['divId'])),
            array('colunaValor' => 'substr(C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Usuário', 'style' => 'width:1%'),
            array('colunaValor' => 'D085_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            //array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Empresa', 'style' => 'width:1%'),
            array('colunaValor' => 'CONCAT(D085_Data, " ", D085_Hora)', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
        	array('colunaValor' => 'IF(D085_Data_Previsao_Retorno = "0000-00-00", "", CONCAT(D085_Data_Previsao_Retorno, " ", D085_Hora_Previsao_Retorno))', 'colunaTitulo' => 'Retornar', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D085_T003_Id', 'colunaTitulo' => 'Orç', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Mensagem', 'colunaTitulo' => 'Mensagem', 'style' => 'max-width:250px;white-space:normal;'),
            array('colunaValor' => 'D085_C007_Id', 'colunaTitulo' => 'IdUsuarioAtual', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Entidade', 'colunaTitulo' => 'Entidade', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Id_Entidade', 'colunaTitulo' => 'ID Entidade', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Flag_Manual', 'colunaTitulo' => 'Manual', 'colunaExibir' => false),
        );

		/**
		*
		* Definição da SQL
		*/
		$from = 'D085';

		$extra = <<<EOT
			left join C007 on C007_Id=D085_C007_Id
			LEFT JOIN D024 on D024_Id=D085_D024_Id
			LEFT JOIN D021 ON D021_Id=D085_D021_Id
			where D085_T002_Id = '{$r_acaoId}'
			order by D085_Data desc,D085_Id desc
				
EOT;
		//Define ação do click de abertura (ao clicar em uma linha do grid)
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				//$('.gridOptTop').remove();
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D085_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Agenda', '/crm/crm001/content/crm001contentEditarAgenda/', '&D024_Id={$D024_Id}&acaoId=' + encodeURIComponent(id), [700,450]);
				});
			} </script></div>
EOT;
			
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="crm002IncluirAgendaCliente">Incluir</button>	
			</div>*/echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#crm002IncluirAgendaCliente').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Agenda', '/crm/crm001/content/crm001contentEditarAgenda/', '&D024_Id={$D024_Id}&T002_Id={$r_acaoId}&tab=geral', [700,450]);
				});
				} </script></div>
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'crm002IncluirAgendaCliente'),
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D085_Data' => array('titulo' => 'Data Inclusão', 'tipo' => 'data'),
			'D085_Retorno' => array('titulo' => 'Retorno', 'tipo' => 'texto'),
			'D085_Data_Retorno' => array('titulo' => 'Data Retorno', 'tipo' => 'data'),
			'D085_Data_Previsao_Retorno' => array('titulo' => 'Previsão Retorno', 'tipo' => 'data'),
			'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'D085_Mensagem' => array('titulo' => 'Mensagem', 'tipo' => 'texto'),
		);
		
		 // Gera o filtro e acrescenta ao $extra
		 $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		 //$extraTotalAberto = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalAberto, false, false, true);
		 // Imprime o filtro do GRID (normalmente executado acima do GRID)
		 echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		 /**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

    case 'fin001gridReparcelamentosTitulo':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T166_Id', $g['divId'])),
			array('colunaValor' => 'T166_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true),
			array('colunaValor' => 'T166_Tipo', 'colunaTitulo' => 'Tipo', 'callback'=>'validaCampoTipo'),
			array('colunaValor' => 'T166_D024_Id', 'colunaTitulo' => 'Cliente/Fornecedor', 'colunaExibir'=>false),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente/Fornecedor'),
			array('colunaValor' => 'T166_C007_Id', 'colunaTitulo' => 'Usuário', 'colunaExibir'=>false),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'T166_Data_Emissao', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T166_Valor_Total', 'colunaTitulo' => 'Valor Total','colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T166_Titulos_Geradores', 'colunaTitulo' => 'Títulos Geradores', 'callback'=>'formataGeradores'),
			array('colunaValor' => 'T166_Titulos_Gerados', 'colunaTitulo' => 'Títulos Gerados' , 'callback'=>'formataGerados'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T166';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
			left join C007 on C007_Id=T166_C007_Id
		    left join D024 on D024_Id=T166_D024_Id
		    left join T244 on T166_Id=T244_T166_Id
		    WHERE (T166_C004_Id='{$g['empresaAtual']}') 
		    AND (T244_T002_Id = '{$r_acaoId}')
EOT;
		
		// /**
		// * Botões (Versão Acima do GRID)
		// */
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="fin001EnviarEmailReparcelamento">Enviar Email</button>
// 				
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// $('#etc010FormUsuariosClienteBtnIncluir').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Usuários Clientes', '/etc/etc010/content/etc010ContentFormUsuariosClientesIncluir/', '&tab=geral', [400,300]);
				// });
			// } </script></div>
// EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);			
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente/Fornecedor', 'tipo' => 'texto'),
			'T166_Data_Emissao' => array('titulo' => 'Data Reparcelamento', 'tipo' => 'data'),
			'T166_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
			'T166_Titulos_Geradores' => array('titulo' => 'Títulos Geradores', 'tipo' => 'texto'),
			'T166_Titulos_Gerados' => array('titulo' => 'Títulos Gerados', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T166_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Reparcelamentos e Agrupamentos - ID: ' + acaoId, '/fin/fin001/content/fin001ContentReparcelar/', '&acaoId=' + encodeURIComponent(acaoId), [500,400]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

    case 'fin001GridCreditoAntecipado':
    	global $g;
    	//$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
    	mysql_query("CALL D024_Gravar_Totalizacao('{$r_D024_Id}')");

		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%; font-size:11px', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T002_Numero_Documento', 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center', 'callback' => 'gMostraMaisNotificacao', 'callbackParameter' => array('15', ','), 'style' => 'width:1%'),
            array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'T002_D024_Id', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'T002_Codigo_Cliente', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Empresa'),
            array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'N.Dupl.', 'callback' => 'validaCampoDuplicata', 'callbackParameter' => array('15', ',', 'T002_Observacao_Reparcelamento'), 'style' => 'width:1%;'),
            array('colunaValor' => 'concat(T002_Numero_Parcela,\'/\',T002_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prz', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'datediff(if(T002_Data_Recebimento=\'0000-00-00\',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) as Atraso', 'colunaTitulo' => 'Ats', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Data_Baixa', 'colunaTitulo' => 'Baixa', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Saldo_Credito', 'colunaTitulo' => 'Saldo Crédito', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => '1 as tipoTituloOrdem', 'colunaTitulo' => 'tipoTituloOrdem', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Id as idOrdem', 'colunaTitulo' => 'idOrdem', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Percentual_Comissao', 'colunaTitulo' => '%Com', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Comissao', 'colunaTitulo' => 'Comissão', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Nosso Número', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
            array('colunaValor' => 'D046_Nome', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
            array('colunaValor' => 'T002_T045_Id', 'colunaTitulo' => 'Lote', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Flag_Status', 'colunaTitulo' => 'Status', 'callback' => 'fin001CampoStatus', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Agrupamento', 'colunaTitulo' => 'Agrupamento', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
            array('colunaValor' => 'T117_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Flag_Cancelada', 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
            array('colunaValor' => 'T035_Data_Processamento', 'colunaTitulo' => 'Processamento', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Flag_Agrupado_Reparcelado', 'colunaTitulo' => 'Agrupado/Reparcelado', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Tipo portador', 'colunaExibir' => false),
            array('colunaValor' => 'T002_T002_Id_Agrupado', 'colunaTitulo' => 'T002_T002_Id_Agrupado', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Observacao_Reparcelamento', 'colunaTitulo' => 'T002_Observacao_Reparcelamento', 'colunaExibir' => false),
            array('colunaValor' => 'D027_Flag_Credito_Antecipado', 'colunaTitulo' => 'D027_Flag_Credito_Antecipado', 'colunaExibir' => false),
        );
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id=T002_D024_Id
			LEFT JOIN D027 ON D027_Id=T002_D027_Id
			LEFT JOIN T117 ON T117_Id=T002_T117_Id
			LEFT JOIN D014 ON D014_Id=T002_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			LEFT JOIN D032 ON D032_Id=T002_D032_Id
			LEFT JOIN D046 ON D046_Id=D024_D046_Id
			LEFT JOIN T007 ON T007_Id=T002_T007_Id
			LEFT JOIN T035 ON T035_Id=T002_T035_Id
			LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
			LEFT JOIN T103 ON T002_Id=T103_T002_Id
			LEFT JOIN D021 ON D021_Id=T002_D021_Id
			WHERE (T002_C004_Id={$g['empresaAtual']})
			AND T002_Flag_Cancelada != 'S'
			AND (D027_Flag_Credito_Antecipado = '1' OR D027_Flag_Credito_Antecipado = '3')
			AND D024_Id = '{$r_D024_Id}' 
                ORDER BY T002_Data_Emissao ASC, tipoTituloOrdem ASC, idOrdem ASC
EOT;
		$extraTotal = <<<EOT
			LEFT JOIN D024 ON D024_Id=T002_D024_Id
			LEFT JOIN D027 ON D027_Id=T002_D027_Id
			LEFT JOIN T117 ON T117_Id=T002_T117_Id
			LEFT JOIN D014 ON D014_Id=T002_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			LEFT JOIN D032 ON D032_Id=T002_D032_Id
			LEFT JOIN D046 ON D046_Id=D024_D046_Id
			LEFT JOIN T007 ON T007_Id=T002_T007_Id
			LEFT JOIN T035 ON T035_Id=T002_T035_Id
			LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
			LEFT JOIN T103 ON T002_Id=T103_T002_Id
			LEFT JOIN D021 ON D021_Id=T002_D021_Id
			WHERE (T002_C004_Id={$g['empresaAtual']})
			AND T002_Flag_Cancelada != 'S'
			AND D024_Id = '{$r_D024_Id}'
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['D027_Flag_Credito_Antecipado'] == '1'){
				return 'linhaVerde';
			}
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
            //'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
            'T002_Numero_Duplicata,T002_Observacao_Reparcelamento' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T002_Valor_Duplicata,T002_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
            'T002_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T002_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
            'T002_Data_Recebimento' => array('titulo' => 'Recebimento', 'tipo' => 'data'),
            //'T002_Data_Baixa' => array('titulo' => 'Baixa', 'tipo' => 'data'),
            //'D027_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
            //'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
            //'T117_Nosso_Numero' => array('titulo' => 'Nosso Numero', 'tipo' => 'texto'),
            //'C007_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            //'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            //'T002_T035_Id' => array('titulo' => 'Remessa', 'tipo' => 'numero'),
            /*'T002_Flag_Status' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Normal', 'value' => '1'),
                array('title' => 'Cobrança', 'value' => '3'),
                array('title' => 'Perdido', 'value' => '2'),
              ),
              'naoGerarExtra' => false),*/
            //'T002_Flag_Enviar_Banco' => array('titulo' => 'Marcaro Rm', 'tipo' => 'texto'),
            'T002_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
            //'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
            /*'T007_Flag_ACP' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
	                array('title' => 'Todos', 'value' => ''),
	                array('title' => 'NF-e', 'value' => '6'),
	                array('title' => 'CF', 'value' => '4'),
            	),
            ),*/
            'IF(T002_T002_Id_Agrupado > 0[[virgula]]"S"[[virgula]]"N")' => array('titulo' => 'Reparcelado', 'tipo' => 'select', 'select' => array(
	                array('title' => '...', 'value' => ''),
	                array('title' => 'SIM', 'value' => 'S'),
	                array('title' => 'NÃO', 'value' => 'N'),
            	),
            ),
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode(gRetirarOrderBy($extra)));
		$extra__ = base64_encode(gRetirarOrderBy($extra));

		/**
		* Botões (Versão Acima do GRID)
		*/
        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin001ProcessarSaldoCredito').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fin/fin001/grid_func-ajax/processarSaldoCliente/?ajax=true&extra={$extra_}&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}');
									var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Processar Saldo', 'Saldo processado com sucesso!', buttons);
								} else {
									var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Processar Saldo', request.data, buttons);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Processar Saldo', 'Você tem certeza? ', \$buttons);
				});
			} </script></div>
EOT;
        }

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		 /**
		 * Totalizadores
		 */
		$extra .= " AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)";

		if (!personalizacaoTotais()) {
			$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
			$valorCreditos = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(ifnull(T002_Valor_Duplicata,0)+ifnull(T002_Valor_Juros,0)-ifnull(T002_Valor_Desconto,0))", $from, gInsertExtraWhere($extraTotal, "D027_Flag_Credito_Antecipado = '1'")));
			$valorAbatimentos = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(ifnull(T002_Valor_Duplicata,0)+ifnull(T002_Valor_Juros,0)-ifnull(T002_Valor_Desconto,0))", $from, gInsertExtraWhere($extraTotal, "D027_Flag_Credito_Antecipado = '3'")));
			$sqlD024 = mysql_query("SELECT D024_Valor_Credito_Antecipado FROM D024 WHERE D024_Id = '{$r_D024_Id}'");
			$rowD024 = mysql_fetch_array($sqlD024);
			$valorSaldo = gCorrigeNumero($rowD024['D024_Valor_Credito_Antecipado']);
	 		/**
			* Botões (Versão Acima do GRID)
			*/

			$disabled = "";
			if($g['c029Ids'][239]){
				$disabled = "disabled";
			}

			echo $totais = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalTitulos}" size="6" readonly></span>
					<span class="item" style="text-align:right">Créditos<br><input type="text" class="whiteBg" id="inputValorTotal" value="{$valorCreditos}" size="11" readonly></span>
					<span class="item" style="text-align:right">Abatimentos<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorAbatimentos}" size="11" readonly></span>
					<span class="item" style="text-align:right">Saldo<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorSaldo}" size="11" readonly></span>
					<button type="button" style="margin-top: 5px" id="fin001ProcessarSaldoCredito" {$disabled}>Processar Saldo</button>
					<div id="fin001ExtraBtns" style="clear:both;"></div>
				</div>
EOT;
	  	}

		//<span class="item" style="text-align:right">Agrupar<br><input type="text" class="whiteBg" id="inputAgrupar" size="11" readonly></span>
		//carregaInputAgrupar();

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'});
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T002_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1050,540]);
				});
			} </script></div>
EOT;
		
		/**
		 * Geração: Grid
		 */		
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('totais', $totais);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); };
    break;

    case 'fin001GridCreditoTrocaCentral':
		if (!isset($g['c029Ids'][35]) && !gVerificaRoot()) {
			echo '<div class="msgError">Usuário sem acesso à opção Contas a Receber.</div>';
			break;
		}

		$clientePorEmpresa = '';
		if ($g['C031']['separarClientePorEmpresa'] == 'S') {
			$clientePorEmpresa = "AND (IFNULL(D024_C004_Id, 0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id, 0) = 0)";
		}

		$grid = array(
			array('colunaValor' => 'D024_Id as Selecionar_Credito_Troca', 'colunaTitulo' => 'Sel.', 'callback' => 'fin001SelecionarSaldoCreditoTroca', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
			array('colunaValor' => 'IF(D024_Cnpj != "", D024_Cnpj, D024_Cpf) as Documento', 'colunaTitulo' => 'CPF/CNPJ', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'IFNULL(CreditoTroca.Credito_Gerado, 0) as Credito_Gerado', 'colunaTitulo' => 'Crédito Gerado', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'IFNULL(CreditoTroca.Credito_Utilizado, 0) as Credito_Utilizado', 'colunaTitulo' => 'Utilizado', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => '(IFNULL(CreditoTroca.Credito_Gerado, 0) - IFNULL(CreditoTroca.Credito_Utilizado, 0)) as Saldo_Em_Aberto', 'colunaTitulo' => 'Saldo em Aberto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'D024_Id as Abater_Saldo_Credito_Troca', 'colunaTitulo' => 'Abater saldo', 'callback' => 'fin001SelecionarAbaterSaldoCreditoTroca', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
		);

		$from = 'D024';
		$extra = <<<EOT
			LEFT JOIN (
				SELECT Movimento.D024_Id AS Cliente_Id,
					SUM(IF(Movimento.Flag_Credito = '2', Movimento.Valor, 0)) AS Credito_Gerado,
					SUM(IF(Movimento.Flag_Credito = '4', Movimento.Valor, 0)) AS Credito_Utilizado
				FROM (
					SELECT T002_D024_Id AS D024_Id,
						D027_Flag_Credito_Antecipado AS Flag_Credito,
						(IFNULL(T002_Valor_Duplicata, 0) + IFNULL(T002_Valor_Juros, 0) - IFNULL(T002_Valor_Desconto, 0)) AS Valor
					FROM T002
					INNER JOIN D027 ON D027_Id = T002_D027_Id
						AND D027_Flag_Credito_Antecipado IN ('2', '4')
						AND T002_Flag_Cancelada != 'S'
						AND (T002_T002_Id_Agrupado = 0 OR T002_T002_Id_Agrupado = '' OR T002_T002_Id_Agrupado IS NULL)
					UNION ALL
					SELECT T015_D024_Id AS D024_Id,
						D031_Flag_Credito_Antecipado AS Flag_Credito,
						(IFNULL(T015_Valor_Duplicata, 0) + IFNULL(T015_Valor_Juros, 0) - IFNULL(T015_Valor_Desconto, 0)) AS Valor
					FROM T015
					INNER JOIN D031 ON D031_Id = T015_D031_Id
						AND D031_Flag_Credito_Antecipado IN ('2', '4')
						AND T015_Flag_Cancelada != 'S'
						AND (T015_T015_Id_Agrupado = 0 OR T015_T015_Id_Agrupado = '' OR T015_T015_Id_Agrupado IS NULL)
				) Movimento
				GROUP BY Movimento.D024_Id
			) CreditoTroca ON CreditoTroca.Cliente_Id = D024_Id
			WHERE D024_Flag_Ativo = 'S'
				AND D024_Flag_Cliente = 'S'
				{$clientePorEmpresa}
			ORDER BY D024_Nome_Empresa
EOT;

		$gridFiltro = array(
			'D024_Id' => array('titulo' => 'Código', 'tipo' => 'numero'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'D024_Cnpj,D024_Cpf' => array('titulo' => 'CPF/CNPJ', 'tipo' => 'texto'),
			'IFNULL(CreditoTroca.Credito_Gerado[[virgula]] 0)' => array('titulo' => 'Crédito Gerado', 'tipo' => 'moeda'),
			'IFNULL(CreditoTroca.Credito_Utilizado[[virgula]] 0)' => array('titulo' => 'Utilizado', 'tipo' => 'moeda'),
			'(IFNULL(CreditoTroca.Credito_Gerado[[virgula]] 0) - IFNULL(CreditoTroca.Credito_Utilizado[[virgula]] 0))' => array('titulo' => 'Saldo em Aberto', 'tipo' => 'moeda'),
		);

		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$creditoTrocaJustificativaId = 'fin001CreditoTrocaJustificativa_' . uniqid();
		echo <<<EOT
			<div class="buttonsBottomTop" style="padding:4px 0;">
				<button type="button" id="fin001CreditoTrocaAbaterSaldo">Abater saldo</button>
				<button type="button" id="fin001CreditoTrocaZerarPositivos">Zerar saldos positivos</button>
				<button type="button" id="fin001CreditoTrocaAjustarNegativos">Ajustar saldos negativos</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') { (function() {
				function fin001CreditoTrocaIdsSelecionados(tipo) {
					var ids = [];
					$('#{$g['divId']} .fin001CreditoTrocaSelecionar:checked').each(function() {
						if ($(this).attr('data-tipo') == tipo) {
							ids.push($(this).attr('data-cliente-id'));
						}
					});
					return ids;
				}

				function fin001CreditoTrocaEnviar(tipo, ids, justificativa) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin001/grid_func-ajax/processarSaldosCreditoTroca/?ajax=true&callback=?', {
						tipo: tipo,
						clientes: JSON.stringify(ids),
						justificativa: justificativa || ''
					}, function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}');
							var mensagem = request.data && request.data.mensagem ? request.data.mensagem : 'Saldos processados com sucesso.';
							dialogConfirm('Créditos Troca', mensagem, {
								'Fechar': function() { $(this).dialog('destroy').remove(); }
							});
						} else {
							dialogConfirm('Operação não realizada', request.data, {
								'Fechar': function() { $(this).dialog('destroy').remove(); }
							});
						}
					});
				}

				$('#{$g['divId']} #fin001CreditoTrocaAbaterSaldo').unbind('click').bind('click', function(data) {
					var ids = [];
					$('#{$g['divId']} .fin001CreditoTrocaSelecionarAbatimento:checked').each(function() {
						ids.push($(this).attr('data-cliente-id'));
					});
					if (ids.length !== 1) {
						dialogConfirm('Créditos Troca', 'Selecione exatamente um cliente com saldo positivo para escolher o título a receber.', {
							'Fechar': function() { $(this).dialog('destroy').remove(); }
						});
						return;
					}
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Abater saldo de Crédito Troca', '/fin/fin001/content/fin001ContentAbaterSaldoCreditoTroca/', '&D024_Id=' + encodeURIComponent(ids[0]) + '&divOrigem=' + encodeURIComponent('{$g['divId']}'), [1050,560]);
				});

				$('#{$g['divId']} #fin001CreditoTrocaZerarPositivos').unbind('click').bind('click', function(data) {
					var ids = fin001CreditoTrocaIdsSelecionados('abatimento');
					if (!ids.length) {
						dialogConfirm('Créditos Troca', 'Selecione ao menos um cliente com saldo positivo.', {
							'Fechar': function() { $(this).dialog('destroy').remove(); }
						});
						return;
					}
					dialogConfirm('Confirmar abatimento', 'Serão zerados os saldos positivos de ' + ids.length + ' cliente(s). Os valores serão recalculados antes do lançamento.', {
						'Sim': function() {
							$(this).dialog('destroy').remove();
							fin001CreditoTrocaEnviar('abatimento', ids, 'ZERAR SALDO POSITIVO DE CRÉDITO TROCA');
						},
						'Não': function() { $(this).dialog('destroy').remove(); }
					});
				});

				$('#{$g['divId']} #fin001CreditoTrocaAjustarNegativos').unbind('click').bind('click', function(data) {
					var ids = fin001CreditoTrocaIdsSelecionados('ajuste');
					if (!ids.length) {
						dialogConfirm('Créditos Troca', 'Selecione ao menos um cliente com saldo negativo.', {
							'Fechar': function() { $(this).dialog('destroy').remove(); }
						});
						return;
					}
					var texto = '<div style="margin-bottom:6px;">Informe a justificativa do ajuste legado para ' + ids.length + ' cliente(s):</div>' +
						'<textarea id="{$creditoTrocaJustificativaId}" maxlength="500" style="width:420px;height:70px;text-transform:none;"></textarea>';
					dialogConfirm('Confirmar ajuste de saldo negativo', texto, {
						'Confirmar': function() {
							var justificativa = $.trim($('#{$creditoTrocaJustificativaId}').val());
							if (justificativa.length < 10) {
								dialogConfirm('Justificativa obrigatória', 'Informe uma justificativa com pelo menos 10 caracteres.', {
									'Fechar': function() { $(this).dialog('destroy').remove(); }
								});
								return;
							}
							$(this).dialog('destroy').remove();
							fin001CreditoTrocaEnviar('ajuste', ids, justificativa);
						},
						'Cancelar': function() { $(this).dialog('destroy').remove(); }
					});
				});
			})(); } </script></div>
EOT;

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		$totais = array(
			array('nome' => 'Count', 'titulo' => 'Clientes', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Credito_Gerado', 'valor' => 'Credito_Gerado', 'titulo' => 'Crédito Gerado'),
			array('nome' => 'Credito_Utilizado', 'valor' => 'Credito_Utilizado', 'titulo' => 'Utilizado'),
			array('nome' => 'Saldo_Em_Aberto', 'valor' => 'Saldo_Em_Aberto', 'titulo' => 'Saldo em Aberto'),
		);

		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'});
				$('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D024_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Crédito Troca - Cliente ' + id, '/fin/fin001/content/fin001ContentCreditoTrocaCliente/', '&D024_Id=' + encodeURIComponent(id), [1100,600]);
				});
			} </script></div>
EOT;

		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('totais', $totais);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'fin001GridAbaterSaldoCreditoTroca':
		if (!isset($g['c029Ids'][35]) && !gVerificaRoot()) {
			echo '<div class="msgError">Usuário sem acesso à opção Contas a Receber.</div>';
			break;
		}

		$clienteId = (int)$r_D024_Id;
		if ($clienteId <= 0) {
			echo '<div class="msgError">Cliente inválido para abatimento de crédito troca.</div>';
			break;
		}
		mysql_query("CALL D024_Gravar_Totalizacao('{$clienteId}')");
		$sqlCliente = mysql_query("SELECT D024_Nome_Empresa, D024_Valor_Credito_Troca FROM D024 WHERE D024_Id = '{$clienteId}'");
		$cliente = $sqlCliente ? mysql_fetch_assoc($sqlCliente) : false;
		if (!$cliente || (float)$cliente['D024_Valor_Credito_Troca'] <= 0) {
			echo '<div class="msgError">O cliente não possui saldo positivo de crédito troca disponível.</div>';
			break;
		}

		$saldoDisponivel = gCorrigeNumero($cliente['D024_Valor_Credito_Troca']);
		echo '<div class="buttonsBottomTop" style="padding:4px 0;">'
			. '<span class="item" style="text-align:right">Saldo disponível<br><input type="text" class="whiteBg" value="' . $saldoDisponivel . '" size="11" readonly></span>'
			. '<button type="button" id="fin001ConfirmarAbaterSaldoCreditoTroca">Abater no título selecionado</button>'
			. '<span style="display:inline-block;margin:7px 0 0 10px;color:#9b1c1c;">Boletos com remessa bancária não são exibidos.</span>'
			. '</div>';

		$grid = array(
			array('colunaValor' => 'T002_Id as Selecionar_Abatimento_Credito_Troca', 'colunaTitulo' => 'Sel.', 'callback' => 'fin001SelecionarTituloAbaterCreditoTroca', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T002_Numero_Documento', 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'N.Dupl.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador'),
			array('colunaValor' => 'IF(IFNULL(T002_T117_Id, 0) > 0, "Boleto", "Título") as Tipo_Cobranca', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T002_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T002_Valor_Saldo', 'colunaTitulo' => 'Saldo em Aberto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
		);
		$from = 'T002';
		$extra = <<<EOT
			LEFT JOIN D027 ON D027_Id = T002_D027_Id
			WHERE T002_D024_Id = '{$clienteId}'
				AND T002_C004_Id = '{$g['empresaAtual']}'
				AND (T002_Data_Recebimento = '0000-00-00' OR T002_Data_Recebimento IS NULL)
				AND T002_Flag_Cancelada != 'S'
				AND (T002_T002_Id_Agrupado <= 0 OR T002_T002_Id_Agrupado IS NULL)
				AND IFNULL(T002_Valor_Saldo, 0) > 0
				AND IFNULL(T002_T035_Id, 0) <= 0
			ORDER BY T002_Data_Vencimento, T002_Id
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		$divOrigem = isset($r_divOrigem) ? $r_divOrigem : '';
		$divOrigemJson = json_encode($divOrigem);
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') { (function() {
				$('#{$g['divId']} #fin001ConfirmarAbaterSaldoCreditoTroca').unbind('click').bind('click', function() {
					var titulos = $('#{$g['divId']} .fin001TituloAbaterCreditoTroca:checked');
					if (titulos.length !== 1) {
						dialogConfirm('Créditos Troca', 'Selecione exatamente um título em aberto.', {'Fechar': function() { $(this).dialog('destroy').remove(); }});
						return;
					}
					var tituloId = titulos.first().attr('data-titulo-id');
					dialogConfirm('Confirmar abatimento', 'O crédito disponível será usado até o limite do saldo deste título. Deseja continuar?', {
						'Sim': function() {
							$(this).dialog('destroy').remove();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fin/fin001/grid_func-ajax/abaterSaldoCreditoTroca/?ajax=true&callback=?', {clienteId: '{$clienteId}', tituloId: tituloId}, function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}');
									if ({$divOrigemJson}) { divRefresh({$divOrigemJson}); }
									dialogConfirm('Créditos Troca', 'Abrir o título selecionado para conferir o abatimento ou gerar um novo boleto?', {
										'Sim': function() {
											$(this).dialog('destroy').remove();
											abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Receber - ID: ' + request.data.titulo, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(request.data.titulo) + '&tabela=T002', [1050,540]);
										},
										'Não': function() { $(this).dialog('destroy').remove(); }
									});
								} else {
									dialogConfirm('Operação não realizada', request.data, {'Fechar': function() { $(this).dialog('destroy').remove(); }});
								}
							});
						},
						'Não': function() { $(this).dialog('destroy').remove(); }
					});
				});
			})(); } </script></div>
EOT;
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('totais', array());
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

    case 'fin001GridCreditoTroca':

    	global $g;
    	//$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
    	mysql_query("CALL D024_Gravar_Totalizacao('{$r_D024_Id}')");

        $grids = array(
	        array(
	        	array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%; font-size:11px', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			    array('colunaValor' => 'T002_Numero_Documento', 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center', 'callback' => 'gMostraMaisNotificacao', 'callbackParameter' => array('15', ','), 'style' => 'width:1%'),
				array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'T002_D024_Id', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
			    array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaDimensao' => 'S'),
			    array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Empresa'),
			    array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'N.Dupl.', 'callback' => 'validaCampoDuplicata', 'callbackParameter' => array('15', ',', 'T002_Observacao_Reparcelamento'), 'style' => 'width:1%;'),
			    array('colunaValor' => 'concat(T002_Numero_Parcela,\'/\',T002_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc', 'style' => 'width:1%;'),			    
			    array('colunaValor' => 'T002_Data_Emissao as dataEmissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
			    array('colunaValor' => 'datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prz', 'style' => 'width:1%;'),
			    array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
			    array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
			    array('colunaValor' => 'T002_Data_Baixa', 'colunaTitulo' => 'Baixa', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
			    array('colunaValor' => 'T002_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
			    array('colunaValor' => 'T002_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
			    array('colunaValor' => 'T002_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
			    array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
			    array('colunaValor' => 'T002_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Saldo_Credito', 'colunaTitulo' => 'Saldo Crédito', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => '1 as tipoTituloOrdem', 'colunaTitulo' => 'tipoTituloOrdem', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Id as idOrdem', 'colunaTitulo' => 'idOrdem', 'colunaExibir' => false),
            array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaAlinhar' => 'left', 'style' => 'width:1%;'),
				array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa Crédito', 'colunaAlinhar' => 'left', 'style' => 'width:1%;'),
			    array('colunaValor' => 'T002_Observacao', 'colunaTitulo' => 'Observação'),
			    array('colunaValor' => 'D027_Flag_Credito_Antecipado', 'colunaTitulo' => 'Portador', 'colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaExibir' => false),
			),
			array(
				array('colunaValor' => 'CONCAT("T015=",T015_Id) as T015_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%; font-size:11px', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			    array('colunaValor' => 'T015_Numero_Documento', 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center', 'callback' => 'gMostraMaisNotificacao', 'callbackParameter' => array('15', ','), 'style' => 'width:1%'),
				array('colunaValor' => 'T015_D024_Id', 'colunaTitulo' => 'T015_D024_Id', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
			    array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaDimensao' => 'S'),
			    array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Empresa'),
			    array('colunaValor' => 'T015_Numero_Duplicata', 'colunaTitulo' => 'N.Dupli', 'callback' => 'gMostraMaisNotificacao', 'callbackParameter' => array('15', ','), 'style' => 'width:1%'),
	            array('colunaValor' => 'concat(T015_Numero_Parcela,\'/\',T015_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc', 'style' => 'width:1%'),
	            array('colunaValor' => 'T015_Data_Emissao as dataEmissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
	            array('colunaValor' => 'datediff(T015_Data_Vencimento,T015_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prazo', 'style' => 'width:1%'),
	            array('colunaValor' => 'T015_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
	            array('colunaValor' => 'T015_Data_Pagamento', 'colunaTitulo' => 'Pagamento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
	            array('colunaValor' => 'T015_Data_Baixa', 'colunaTitulo' => 'Baixa', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
	            array('colunaValor' => 'T015_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
	            array('colunaValor' => 'T015_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
	            array('colunaValor' => 'T015_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
	            array('colunaValor' => 'T015_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;width:1%'),
	            array('colunaValor' => 'T015_Valor_Pago', 'colunaTitulo' => 'Pago', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
                array('colunaValor' => 'T015_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;width:1%'),
                array('colunaValor' => 'T015_Valor_Saldo_Credito', 'colunaTitulo' => 'Saldo Crédito', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
                array('colunaValor' => '2 as tipoTituloOrdem', 'colunaTitulo' => 'tipoTituloOrdem', 'colunaExibir' => false),
                array('colunaValor' => 'T015_Id as idOrdem', 'colunaTitulo' => 'idOrdem', 'colunaExibir' => false),
                array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold;width:1%'),
				array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa Crédito', 'colunaAlinhar' => 'left', 'style' => 'width:1%;'),
				array('colunaValor' => 'T015_Observacao', 'colunaTitulo' => 'Observação'),
	            array('colunaValor' => 'D031_Flag_Credito_Antecipado', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			),
		);
		
		/**
		* Definição da SQL
		*/
		//$from = 'T002';
		$froms = array('T002', 'T015');
		
		$extras = array(
			"LEFT JOIN D024 ON D024_Id = T002_D024_Id 
			 LEFT JOIN D027 ON D027_Id = T002_D027_Id 
			 LEFT JOIN C004 ON C004_Id = T002_C004_Id
			 WHERE T002_Flag_Cancelada != 'S' 
			 AND (T002_T002_Id_Agrupado=0 OR T002_T002_Id_Agrupado='' OR T002_T002_Id_Agrupado IS NULL)
			 AND (D027_Flag_Credito_Antecipado = '2' OR D027_Flag_Credito_Antecipado = '4') AND T002_D024_Id = '{$r_D024_Id}'",
			
			"LEFT JOIN D024 ON D024_Id = T015_D024_Id 
			 LEFT JOIN D031 ON D031_Id = T015_D031_Id
			 LEFT JOIN C004 ON C004_Id = T015_C004_Id
			 WHERE T015_Flag_Cancelada != 'S' 
			 AND (T015_T015_Id_Agrupado=0 OR T015_T015_Id_Agrupado='' OR T015_T015_Id_Agrupado IS NULL)
			 AND (D031_Flag_Credito_Antecipado = '2' OR D031_Flag_Credito_Antecipado = '4') AND T015_D024_Id = '{$r_D024_Id}'",);
		
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['D027_Flag_Credito_Antecipado'] == '2'){
				return 'linhaVerde';
			}
		}

		/**
		* Filtro
		*/
		$gridFiltros = array(
			array(
	            'T002_Numero_Duplicata' => array('titulo' => 'Número', 'tipo' => 'texto'),
	            'T002_Valor_Duplicata,T002_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
	            'T002_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
	            'T002_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
	            'T002_Data_Recebimento' => array('titulo' => 'Recebimento', 'tipo' => 'data'),
	            'T002_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
	            'IF(T002_T002_Id_Agrupado > 0[[virgula]]"S"[[virgula]]"N")' => array('titulo' => 'Reparcelado', 'tipo' => 'select', 'select' => array(
		                array('title' => '...', 'value' => ''),
		                array('title' => 'SIM', 'value' => 'S'),
		                array('title' => 'NÃO', 'value' => 'N'),
	            	),
	            ),
	        ),
	        array(
	            'T015_Numero_Duplicata',
	            'T015_Valor_Duplicata,T015_Valor_Total',
	            'T015_Data_Emissao',
	            'T015_Data_Vencimento',
	            'T015_Data_Pagamento',
	            'T015_Observacao',
	            'IF(T015_T015_Id_Agrupado > 0[[virgula]]"S"[[virgula]]"N")'
	        )
        );

		// Gera o filtro e acrescenta ao $extra
		$extras = $g['ui']->multiGridFiltroExtra(true, $gridFiltros, $extras);
		$extra_ = urlencode(base64_encode("LEFT JOIN D024 ON D024_Id = T002_D024_Id WHERE D024_Id = '{$r_D024_Id}'"));

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin001ProcessarSaldoCreditoTroca').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fin/fin001/grid_func-ajax/processarSaldoCliente/?ajax=true&extra={$extra_}&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}');
									var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Processar Saldo', 'Saldo processado com sucesso!', buttons);
								} else {
									var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Processar Saldo', request.data, buttons);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Processar Saldo', 'Você tem certeza? ', \$buttons);
				});
			} </script></div>
EOT;
        }

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltros[0]);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGridMulti($grids, $froms, $extras, " ORDER BY dataEmissao ASC, tipoTituloOrdem ASC, idOrdem ASC");
		/**
		* Totalizadores
		*/
		if (!personalizacaoTotais()) {
			$sql = mysql_query($geradoSql);

			$totalTitulos = mysql_num_rows($sql);
			$valorCreditos = 0;
			$valorAbatimentos = 0;
			while($row = mysql_fetch_array($sql)){
				if($row['D027_Flag_Credito_Antecipado'] == '2'){
					$valorCreditos += $row['T002_Valor_Duplicata'] + $row['T002_Valor_Juros'] - $row['T002_Valor_Desconto'];
				} else if($row['D027_Flag_Credito_Antecipado'] == '4'){
					$valorAbatimentos += $row['T002_Valor_Duplicata'] + $row['T002_Valor_Juros'] - $row['T002_Valor_Desconto'];
				}
			}
			$valorCreditos = gCorrigeNumero($valorCreditos);
			$valorAbatimentos = gCorrigeNumero($valorAbatimentos);

			$sqlD024 = mysql_query("SELECT D024_Valor_Credito_Troca FROM D024 WHERE D024_Id = '{$r_D024_Id}'");
			$rowD024 = mysql_fetch_array($sqlD024);
			$valorSaldo = gCorrigeNumero($rowD024['D024_Valor_Credito_Troca']);

			$disabled = "";
			if($g['c029Ids'][239]){
				$disabled = "disabled";
			}

			echo $totais = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalTitulos}" size="6" readonly></span>
					<span class="item" style="text-align:right">Créditos<br><input type="text" class="whiteBg" id="inputValorTotal" value="{$valorCreditos}" size="11" readonly></span>
					<span class="item" style="text-align:right">Abatimentos<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorAbatimentos}" size="11" readonly></span>
					<span class="item" style="text-align:right">Saldo<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorSaldo}" size="11" readonly></span>
					<button type="button" style="margin-top: 5px" id="fin001ProcessarSaldoCreditoTroca" {$disabled}>Processar Saldo</button>
					<div id="fin001ExtraBtns" style="clear:both;"></div>
				</div>
EOT;
	  	}
		

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				function fin001AbrirTituloCreditoTroca(data, titulo, href, vars, tamanho) {
					// Este grid combina T002 e T015. A atualização parcial padrão usa o ID da
					// linha em todas as consultas do UNION e pode não retornar a linha aberta.
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', titulo, href, vars + '&naoAtualizarLinha=1', tamanho);
					setTimeout(function() {
						$('#{$novoIdTelaForm}').one('dialogclose.fin001GridCreditoTroca', function() {
							divRefresh('{$g['divId']}', true);
						});
					}, 150);
				}
				$('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'});
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T002_Id-/, '');
					if(id.substr(0,5) == 'T015='){
						id = id.replace(/T015=/, '');
						fin001AbrirTituloCreditoTroca(data, 'Título a Pagar - ID: ' + id, '/fin/fin002/content/fin002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela=T015', [800,500]);
					} else {
						fin001AbrirTituloCreditoTroca(data, 'Título a Receber - ID: ' + id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela=T002', [1050,540]);
					} 
				});
			} </script></div>
EOT;
		
		/**
		 * Geração: Grid
		 */		
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('totais', $totais);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); };
    break;

    case 'fin001GridTitulosVencidos':
		$grid = array(
            array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%; font-size:11px', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T002_Numero_Documento', 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center', 'callback' => 'gMostraMaisNotificacao', 'callbackParameter' => array('15', ','), 'style' => 'width:1%'),
            array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'T002_D024_Id', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'T002_Codigo_Cliente', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Empresa'),
            array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'N.Dupl.', 'callback' => 'validaCampoDuplicata', 'callbackParameter' => array('15', ',', 'T002_Observacao_Reparcelamento'), 'style' => 'width:1%;'),
            array('colunaValor' => 'concat(T002_Numero_Parcela,\'/\',T002_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prz', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'datediff(if(T002_Data_Recebimento=\'0000-00-00\',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) as Atraso', 'colunaTitulo' => 'Ats', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Data_Baixa', 'colunaTitulo' => 'Baixa', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;'),
            array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Nosso Número', 'style' => 'width:1%;'),
            array('colunaValor' => 'T002_Observacao', 'colunaTitulo' => 'Observação'),            
        );
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id=T002_D024_Id
			LEFT JOIN D027 ON D027_Id=T002_D027_Id
			LEFT JOIN T117 ON T117_Id=T002_T117_Id
			LEFT JOIN D014 ON D014_Id=T002_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			LEFT JOIN D032 ON D032_Id=T002_D032_Id
			LEFT JOIN D046 ON D046_Id=D024_D046_Id
			LEFT JOIN T007 ON T007_Id=T002_T007_Id
			LEFT JOIN T035 ON T035_Id=T002_T035_Id
			LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
			LEFT JOIN T103 ON T002_Id=T103_T002_Id
			LEFT JOIN D021 ON D021_Id=T002_D021_Id
			WHERE (T002_C004_Id={$g['empresaAtual']})
			AND T002_D024_Id = '{$r_D024_Id}'
			AND (T002_T002_Id_Agrupado <= 0 OR T002_T002_Id_Agrupado IS NULL)
			AND T002_Data_Vencimento < CURDATE() AND T002_Data_Recebimento = '0000-00-00' 
			AND T002_Flag_Cancelada != 'S'
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
            'T002_Numero_Duplicata,T002_Observacao_Reparcelamento' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T002_Valor_Duplicata,T002_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
            'T002_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T002_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
            'T002_Data_Recebimento' => array('titulo' => 'Recebimento', 'tipo' => 'data'),
            'T002_Data_Baixa' => array('titulo' => 'Baixa', 'tipo' => 'data'),
            'D027_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
            'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
            'T117_Nosso_Numero' => array('titulo' => 'Nosso Numero', 'tipo' => 'texto'),
            'C007_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            'T002_T035_Id' => array('titulo' => 'Remessa', 'tipo' => 'numero'),
            'T002_Flag_Status' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Normal', 'value' => '1'),
                array('title' => 'Cobrança', 'value' => '3'),
                array('title' => 'Perdido', 'value' => '2'),
              ),
              'naoGerarExtra' => false),
            'T002_Flag_Enviar_Banco' => array('titulo' => 'Marcaro Rm', 'tipo' => 'texto'),
            'T002_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
            'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
            'T007_Flag_ACP' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
	                array('title' => 'Todos', 'value' => ''),
	                array('title' => 'NF-e', 'value' => '6'),
	                array('title' => 'CF', 'value' => '4'),
	                array('title' => 'ACP', 'value' => '3'),
            	),
            ),
            'IF(T002_T002_Id_Agrupado > 0[[virgula]]"S"[[virgula]]"N")' => array('titulo' => 'Reparcelado', 'tipo' => 'select', 'select' => array(
	                array('title' => '...', 'value' => ''),
	                array('title' => 'SIM', 'value' => 'S'),
	                array('title' => 'NÃO', 'value' => 'N'),
            	),
            ),
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T002_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1050,540]);
				});
			} </script></div>
EOT;
		
		/**
		 * Geração: Grid
		 */		
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('totais', $totais);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); };
	break;

	case 'fin001GridCheque':

	$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T246_Id', $g['divId']), 'style' => 'width: 1%'),
			array('colunaValor' => 'T246_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'chavePrimaria'=>true),
			array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'Duplicata', 'colunaExibir' => true),
            array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vcto duplicata', 'colunaExibir' => true, 'callback'=>'gCorrigeData'),
            array('colunaValor' => 'T002_Data_Baixa', 'colunaTitulo' => 'Baixa duplicata', 'colunaExibir' => true, 'callback'=>'gCorrigeData'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Empresa', 'colunaExibir' => true), 
            array('colunaValor' => 'T246_Numero_Cheque', 'colunaTitulo' => 'Cheque', 'colunaExibir' => true),
            array('colunaValor' => 'T246_Data_Cheque', 'colunaTitulo' => 'Emissão', 'colunaExibir' => true, 'callback'=>'gCorrigeData'),
            array('colunaValor' => 'T246_Data_Vencimento', 'colunaTitulo' => 'Vcto cheque', 'colunaExibir' => true, 'callback'=>'gCorrigeData'),
            array('colunaValor' => 'T246_Data_Deposito', 'colunaTitulo' => 'Data deposito', 'colunaExibir' => true, 'callback'=>'gCorrigeData'),
            array('colunaValor' => 'T246_Valor', 'colunaTitulo' => 'Valor', 'colunaExibir' => true, 'style' => 'font-weight:bold;background-color: var(--celula-amarelo-claro);width:1%', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T246_Agencia_Cheque', 'colunaTitulo' => 'Agência', 'colunaExibir' => true),
            array('colunaValor' => 'T246_Conta_Corrente_Cheque', 'colunaTitulo' => 'Conta corrente', 'colunaExibir' => true),
            array('colunaValor' => 'T246_Banco_Cheque', 'colunaTitulo' => 'Banco', 'colunaExibir' => true),
            array('colunaValor' => 'T246_Nome_Cheque', 'colunaTitulo' => 'Nome', 'colunaExibir' => true),
            array('colunaValor' => 'T246_Cpf_Cnpj', 'colunaTitulo' => 'Cpf/Cnpj', 'colunaExibir' => true),
            array('colunaValor' => 'T246_D007_Id', 'colunaTitulo' => 'Banco deposito', 'colunaExibir' => true),
            array('colunaValor' => 'T246_Cidade_Cheque', 'colunaTitulo' => 'Cidade', 'colunaExibir' => true),
            array('colunaValor' => 'T246_Cheque_Repassado', 'colunaTitulo' => 'Repassado', 'colunaExibir' => true),
            array('colunaValor' => 'T246_Observacao_Cheque', 'colunaTitulo' => 'Obeservação Cheque', 'colunaExibir' => true),
        );
		
	function gridLinha($array) {
		if($array['todosCampos']['T246_Data_Deposito'] !='0000-00-00'){
			return 'linhaCinza';
		} 
	}
		/**
		*
		* Definição da SQL
		*/
		$from = 'T246';
		$extra = "LEFT JOIN T002 ON T002_Id=T246_T002_Id 
				  LEFT JOIN D024 ON D024_Id=T002_D024_Id
				  WHERE T246_T002_Id='{$r_acaoId}'";

		
			/*<div class="buttonsBottomTop">
				<button type="button" id="fin001IncluirCheque">Incluir</button>	
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin001IncluirCheque').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Cheque', '/fin/fin001/content/fin001contentCheque/', '&T246_Id={$T246_Id}&T002_Id={$r_acaoId}&tab=geral', [620,350]);
				});
				} </script></div>
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'fin001IncluirCheque'),
		);
		echo Botoes::processar($botoes);


		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'});
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						var id = $(this).closest('tr').attr('id').replace(/T246_Id-/, '');
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cheque: ' + id, '/fin/fin001/content/fin001contentCheque/', '&acaoId=' + encodeURIComponent(id) + '&tabela=T246', [620,350]);
					});
			} </script></div>
EOT;
		/*if (!personalizacaoTotais()) {
			$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
			$valorTotalTitulos= gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T246_Valor)", $from, $extra));
			$valorTotalRecebido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T246_Data_Deposito!='0000-00-00',T246_Valor,0))", $from, $extra));
			$valorAReceber = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T246_Data_Deposito='0000-00-00',T246_Valor,0))", $from, $extra));*/
	 		/**
			* Botões (Versão Acima do GRID)
			*/
			/*echo $totais = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalTitulos}" size="6" readonly></span>
					<span class="item" style="text-align:right">Total<br><input type="text" class="whiteBg" id="inputValorTotal" value="{$valorTotalTitulos}" size="11" readonly></span>
					<span class="item" style="text-align:right">Recebido<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorTotalRecebido}" size="11" readonly></span>
					<span class="item" style="text-align:right">A Receber<br><input type="text" class="whiteBg" id="inputAReceber" value="{$valorAReceber}" size="11" readonly></span>
					<div id="fin001ExtraBtns" style="clear:both;"></div>
				</div>
EOT;
	  	}*/
		
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Total', 'valor' => 'T246_Valor', 'titulo' => 'Total'),
			array('nome' => 'Recebido', 'valor' => "if(T246_Data_Deposito!='0000-00-00',T246_Valor,0)", 'titulo' => 'Recebido'),
			array('nome' => 'aReceber', 'valor' => "if(T246_Data_Deposito='0000-00-00',T246_Valor,0)", 'titulo' => 'A Receber'),
        );


		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

    break;

    case 'fin001gridListaCheques':
    	/**
		* Definição do Grid (Tabela)
		*/

		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T246_Id', $g['divId']), 'style' => 'width: 1%'),
			array('colunaValor' => 'T246_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'chavePrimaria'=>true),
			array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'Duplicata'),
            array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vcto duplicata', 'callback'=>'gCorrigeData'),
            array('colunaValor' => 'T002_Data_Baixa', 'colunaTitulo' => 'Baixa duplicata', 'callback'=>'gCorrigeData'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Empresa'), 
            array('colunaValor' => 'T246_Numero_Cheque', 'colunaTitulo' => 'Cheque'),
            array('colunaValor' => 'T246_Data_Cheque', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData'),
            array('colunaValor' => 'T246_Data_Vencimento', 'colunaTitulo' => 'Vcto cheque', 'callback'=>'gCorrigeData'),
            array('colunaValor' => 'DATEDIFF(T246_Data_Vencimento, T246_Data_Cheque) as Prz_Venc', 'colunaTitulo' => 'Prz venc', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T246_Data_Deposito', 'colunaTitulo' => 'Data deposito', 'callback'=>'gCorrigeData', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'DATEDIFF(if(T246_Data_Deposito="0000-00-00", T246_Data_Cheque, T246_Data_Deposito), T246_Data_Cheque) as Prz_Dep', 'colunaTitulo' => 'Prz dep', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T246_Valor', 'colunaTitulo' => 'Valor', 'style' => 'font-weight:bold;background-color: var(--celula-amarelo-claro);width:1%', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T246_Agencia_Cheque', 'colunaTitulo' => 'Agência'),
            array('colunaValor' => 'T246_Conta_Corrente_Cheque', 'colunaTitulo' => 'Conta corrente'),
            array('colunaValor' => 'T246_Banco_Cheque', 'colunaTitulo' => 'Banco'),
            array('colunaValor' => 'T246_Nome_Cheque', 'colunaTitulo' => 'Nome'),
            array('colunaValor' => 'T246_Cpf_Cnpj', 'colunaTitulo' => 'Cpf/Cnpj'),
            array('colunaValor' => 'T246_D007_Id', 'colunaTitulo' => 'Banco depósito', 'colunaEditarGeraSelect' => array('D007', 'D007_Id', 'D007_Nome_Banco','',true), 'colunaEditar' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D007_Nome_Banco'),
            array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Banco deposito', 'colunaExibir' => false),
            array('colunaValor' => 'T246_Cidade_Cheque', 'colunaTitulo' => 'Cidade', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T246_Cheque_Repassado', 'colunaTitulo' => 'Repassado', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T246_Observacao_Cheque', 'colunaTitulo' => 'Obeservação Cheque', 'style' => 'max-width:250px;min-width:250px;white-space:normal;'),
		);

		function gridLinha($array) {
			if($array['todosCampos']['T246_Data_Deposito'] !='0000-00-00'){
				return 'linhaCinza';
			}
		}
		/**
		* Definição da SQL
		*/
		$from = 'T246';
		$extra =<<<EOT
				LEFT JOIN D007 ON D007_Id = T246_D007_Id
				LEFT JOIN T002 ON T002_Id = T246_T002_Id
				LEFT JOIN D024 ON D024_Id = T002_D024_Id
EOT;
		/**
         * Botões Almir
		 * Botões estão sem função no momento.
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'fin001Incluir'),
			array('titulo' => 'Remessa', 'id' => 'fin001Remessa'),
			array('titulo' => 'Baixar', 'id' => 'fin001Baixar'),
			array('titulo' => 'Agrupar', 'id' => 'fin001Agrupar'),
			array('titulo' => 'Reparcelar', 'id' => 'fin001Reparcelar'),
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T002_Numero_Duplicata' => array('titulo' => 'Duplicata', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'T002_Data_Vencimento' => array('titulo' => 'Vcto duplicata', 'tipo' => 'data'),
			'T002_Data_Baixa' => array('titulo' => 'Baixa duplicata', 'tipo' => 'data'),
			'T246_Numero_Cheque' => array('titulo' => 'Cheque', 'tipo' => 'texto'),
			'T246_Data_Cheque' => array('titulo' => 'Emissão cheque', 'tipo' => 'data'),
			'T246_Agencia_Cheque' => array('titulo' => 'Agência', 'tipo' => 'texto'),
			'T246_Conta_Corrente_Cheque' => array('titulo' => 'Conta corrente', 'tipo' => 'texto'),
			'T246_Banco_Cheque' => array('titulo' => 'Banco', 'tipo' => 'texto'),
			'T246_Nome_Cheque' => array('titulo' => 'Nome', 'tipo' => 'texto'),
			'T246_Cpf_Cnpj' => array('titulo' => 'Cpf/Cnpj', 'tipo' => 'texto'),
			'T246_Cidade_Cheque' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'T246_Cheque_Repassado' => array('titulo' => 'Repassado', 'tipo' => 'texto'),
			'T246_Valor' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'T246_Data_Vencimento' => array('titulo' => 'Vcto cheque', 'tipo' => 'data'),
			'T246_Observacao_Cheque' => array('titulo' => 'Observação cheque', 'tipo' => 'moeda'),
			'T246_Data_Deposito' => array('titulo' => 'Data deposito', 'tipo' => 'data'),
			'D007_Nome_Banco' => array('titulo' => 'Banco deposito', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						var id = $(this).closest('tr').attr('id').replace(/T246_Id-/, '');
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cheque: ' + id, '/fin/fin001/content/fin001contentCheque/', '&acaoId=' + encodeURIComponent(id) + '&tabela=T246', [600,350]);
					});
			} </script></div>
EOT;

		/*echo <<<EOT
					<div class="buttonsBottomTop">
						<button type="button" id="fin001Incluir">Incluir</button>
						<button type="button" id="fin001Remessa">Remessa</button>
						<button type="button" id="fin001Baixar">Baixar</button>
						<button type="button" id="fin001Agrupar">Agrupar</button>
						<button type="button" id="fin001Reparcelar">Reparcelar</button>
						<!--<button type="button" id="fin001Vencimento">Vencimento</button>-->
						<!--<button type="button" id="fin001AgrupamentoAutomatico">Agrupamento Automático</button>-->
						<!--<button type="button" id="fin001Duplicar">Duplicar</button>-->
					</div>
EOT;*/
		/*if (!personalizacaoTotais()) {
			$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
			$valorTotalTitulos= gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T246_Valor)", $from, $extra));
			$valorTotalRecebido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T246_Data_Deposito!='0000-00-00',T246_Valor,0))", $from, $extra));
			$valorAReceber = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T246_Data_Deposito='0000-00-00',T246_Valor,0))", $from, $extra));
			$prazoMedioVencimento = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(DATEDIFF(T246_Data_Vencimento, T246_Data_Cheque))/COUNT(DISTINCT T246_Id)", $from, $extra),0);
			$prazoMedioDeposito = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(DATEDIFF(if(T246_Data_Deposito='0000-00-00', T246_Data_Cheque, T246_Data_Deposito), T246_Data_Cheque))/COUNT(DISTINCT T246_Id)", $from, $extra),0);*/
	 		/**
			* Botões (Versão Acima do GRID)
			*/
			/*echo $totais = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalTitulos}" size="6" readonly></span>
					<span class="item" style="text-align:right">Total<br><input type="text" class="whiteBg" id="inputValorTotal" value="{$valorTotalTitulos}" size="11" readonly></span>
					<span class="item" style="text-align:right">Recebido<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorTotalRecebido}" size="11" readonly></span>
					<span class="item" style="text-align:right">A Receber<br><input type="text" class="whiteBg" id="inputAReceber" value="{$valorAReceber}" size="11" readonly></span>
					<span class="item" style="text-align:right">Prz médio vencimen<br><input type="text" class="whiteBg" id="inputAReceber" value="{$prazoMedioVencimento}" size="11" readonly></span>
					<span class="item" style="text-align:right">Prz médio depósito<br><input type="text" class="whiteBg" id="inputAReceber" value="{$prazoMedioDeposito}" size="11" readonly></span>
					<div id="fin001ExtraBtns" style="clear:both;"></div>
				</div>
EOT;

	  		$totaisHTML = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right; padding:10px; background:#F4F4F4">Títulos<br>{$totalTitulos}</span>
					<span class="item" style="text-align:right; padding:10px; background:#F4F4F4">Total<br>{$valorTotalTitulos}</span>
					<span class="item" style="text-align:right; padding:10px; background:#F4F4F4">Recebido<br>{$valorTotalRecebido}</span>
					<span class="item" style="text-align:right; padding:10px; background:#F4F4F4">A Receber<br>{$valorAReceber}</span>
					<span class="item" style="text-align:right; padding:10px; background:#F4F4F4">Prz médio vencimento<br>{$prazoMedioVencimento}</span>
					<span class="item" style="text-align:right; padding:10px; background:#F4F4F4">Prz médio depósito<br>{$prazoMedioDeposito}</span>
					<div id="fin001ExtraBtns" style="clear:both;"></div>
				</div>
EOT;
	  	}*/

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Total', 'valor' => 'T246_Valor', 'titulo' => 'Total'),
			array('nome' => 'Recebido', 'valor' => "if(T246_Data_Deposito!='0000-00-00',T246_Valor,0)", 'titulo' => 'Recebido'),
			array('nome' => 'aReceber', 'valor' => "if(T246_Data_Deposito='0000-00-00',T246_Valor,0)", 'titulo' => 'A Receber'),
			array('nome' => 'Prz_Venc', 'valor' => "Prz_Venc", 'titulo' => 'Prz_Venc', 'exibir'=>false),
			array('nome' => 'Prz_Dep', 'valor' => "Prz_Dep", 'titulo' => 'Prz_Dep', 'exibir'=>false),
			array('nome' => 'Tot_Registros', 'valor' => "T246_Id", 'totalizador' => 'count', 'titulo' => 'Tot_Reg', 'exibir'=>false),
			array('nome' => 'Prz_Medio_Venc', 'tipo'=>'totais','valor' =>'{Prz_Venc}/{Tot_Registros}', 'titulo' => 'Prz_Medio_Venc'),
			array('nome' => 'Prz_Medio_Dep', 'tipo'=>'totais','valor' =>'ROUND({Prz_Dep}/{Tot_Registros})', 'titulo' => 'Prz_Medio_Dep'),
        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('totais', $totaisHTML);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

    break;

	case 'fin001GridOutrosPortadores':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'T002_D024_Id' , 'colunaExibir' => false),		
			array('colunaValor' => 'T002A_Flag_Enviar_Email', 'callback' => 'fin001campoMarcarTitulosEnviar', 'colunaTitulo' => '', 'colunaEditarTodos' => 'S', 'colunaAlinhar' => 'center', 'style'=>'width:1%;', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'D024_Cnpj', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
			array('colunaValor' => "group_concat(DISTINCT T002_Numero_Duplicata order by T002_Numero_Duplicata SEPARATOR ',') as T002_Numero_Duplicata", 'colunaTitulo' => 'Duplicata'),
			array('colunaValor' => 'concat(T002_Numero_Parcela,\'/\',T002_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc', 'style' => 'width:1%;'),
			array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento Título', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebido', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaTotal' => true),
	    	array('colunaValor' => 'T002_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaTotal' => true),
	        array('colunaValor' => 'T002_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaTotal' => true),
			array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
			array('colunaValor' => 'D024_Email_Cliente(D024_Id,"boleto")', 'colunaTitulo' => 'E-mail'),
			array('colunaValor' => 'T002A_Data_Envio_Email', 'colunaTitulo' => 'eMail', 'callback' => 'fin001CampoeMail', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
			array('colunaValor' => 'D024_Email_Cliente(D024_Id,"boleto")', 'colunaTitulo' => 'E-mail'),
			array('colunaValor' => 'T002_Flag_Status', 'colunaTitulo' => 'Status' , 'colunaExibir' => false),			

		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T002';
		$extra = <<<EOT
			left join D024 on D024_Id=T002_D024_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
			left join T002A on T002_Id=T002A_T002_Id
			left join D027 on D027_Id=T002_D027_Id
	 	  	    where (T002_C004_Id='{$g['empresaAtual']}')
				  and IFNULL(T002_T117_Id,0) = 0
			      and IF('{$g['C031']['exibirTitulosReceberAgrupados']}' = 'S',1=1,(T002_T002_Id_Agrupado <= 0 OR T002_T002_Id_Agrupado IS NULL))
			      and T002_Flag_Cancelada != 'S'
				  and D027_Flag_Boleto !='S'
	 		 group by T002_Id
EOT;


		function gridLinha($array) {
			global $g;
			$dataRecebimento = $array['todosCampos']['T002_Data_Recebimento'];
			$status 		 = $array['todosCampos']['T002_Flag_Status'];
			if($dataRecebimento != '0000-00-00'){
				return 'linhaCinza';
			} else if($status == '2'){
				return 'linhaVermelho';
			}else if($status == '3'){
				return 'linhaAzul';
			}
		}
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T002_Numero_Duplicata' => array('titulo' => 'Duplicata', 'tipo' => 'texto'),
            'T002_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T002_Data_Vencimento' => array('titulo' => 'Vencimento Título', 'tipo' => 'data'),
            'T002_Data_Recebimento' => array('titulo' => 'Recebido', 'tipo' => 'data'),
            'D024_E_Mail' => array('titulo' => 'E-mail', 'tipo' => 'texto'),
			'D024_Cnpj' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode(gRetirarOrderBy($extra)));
		$extra__ = base64_encode(gRetirarOrderBy($extra));

		/*echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="fin005GridPrincipalBtnIncluir">Incluir</button>
			<button type="button" id="fin005EnviarBoletosMarcados">Enviar Boletos Marcados</button>
			<button type="button" id="fin005AtualizarBoletosMarcados">Atualizar Boletos Marcados</button>
			<button type="button" id="fin005ImprimirBoletosMarcados">Imprimir Boletos Marcados</button>
		</div>
EOT;*/
		// Botões
		$botoes = array(
			array('titulo' => 'Enviar Títulos Marcados', 'id' => 'fin005EnviarTitulosMarcados'),
		);
		echo Botoes::processar($botoes);
		
        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		

				$('#fin005EnviarTitulosMarcados').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar Títulos por Email', '/fin/fin001/content/fin001ContentTitulosParaEnvio/', '&tab=geral&extra={$extra_}', [900,550]);
					$('#{$g['divId']}').hideLoading();
				});	

				function gridTheadCallback() {
					$('#{$g['divId']} .gridTheadTh > .ui-icon-pencil:eq(0)')
						.removeAttr('onclick')
						.click(function() {
							var \$buttons = {
								"Marcar Todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/fin/fin005/grid_func-ajax/fin005MarcarTodosBoletos/?ajax=true&extra=' + encodeURIComponent('{$extra__}') + '&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    		dialogConfirm('Ok', request.data, \$buttons);
											divRefresh('{$g['divIdParent']}', true);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    		dialogConfirm('Erro', request.data, \$buttons);
										}
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Desmarcar todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/fin/fin005/grid_func-ajax/fin005DesmarcarTodosBoletos/?ajax=true&extra=' + encodeURIComponent('{$extra__}') + '&callback=?', function(request) {
										if (request.code) {												
											$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
										}											
									});
									$(this).dialog("destroy").remove();
								} ,
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Marcar/Desmarcar Todos', 'Você deseja marcar ou desmarcar todos? ', \$buttons);
							return false;
					});
					$('#{$g['divId']} .gridTheadTh > .ui-icon-pencil:eq(1)')
						.removeAttr('onclick')
						.click(function() {
							var \$buttons = {
								"Marcar Todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/fin/fin005/grid_func-ajax/fin005MarcarTodosBoletosAtualizar/?ajax=true&extra=' + encodeURIComponent('{$extra__}') + '&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    		dialogConfirm('Ok', request.data, \$buttons);
											divRefresh('{$g['divIdParent']}', true);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    		dialogConfirm('Erro', request.data, \$buttons);
										}
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Desmarcar todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/fin/fin005/grid_func-ajax/fin005DesmarcarTodosBoletosAtualizar/?ajax=true&extra=' + encodeURIComponent('{$extra__}') + '&callback=?', function(request) {
										if (request.code) {												
											$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
										}											
									});
									$(this).dialog("destroy").remove();
								} ,
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Marcar/Desmarcar Todos', 'Você deseja marcar ou desmarcar todos? ', \$buttons);
							return false;
					});
				}
			} </script></div>
EOT;
        }

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var T002_Id = id[0].replace(/T002_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + T002_Id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(T002_Id) + '&tabela={$from}', [1050,540]);
				});
			} </script></div>
EOT;

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'fin001GridTitulosParaEnvio':
		$r_extra = base64_decode($r_extra);		
		$r_extra = gInsertExtraWhere($r_extra, "T002A_Flag_Enviar_Email = 'S' AND T002_Valor_Saldo > 0");


		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			//array('colunaValor' => 'T117_Flag_Atualizar', 'callback' => 'campoMarcarBoletoEnvio', 'colunaTitulo' => 'Email', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "group_concat(DISTINCT T002_Numero_Duplicata order by T002_Numero_Duplicata SEPARATOR ',') as T002_Numero_Duplicata", 'colunaTitulo' => 'Duplicata'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebido', 'callback' => 'gCorrigeData', 'colunaExibir' => false),
			array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaTotal' => true),
	    	array('colunaValor' => 'T002_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaTotal' => true),
	        array('colunaValor' => 'T002_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%;', 'colunaTotal' => true),
			array('colunaValor' => 'D024_Email_Cliente(D024_Id,"boleto")', 'colunaTitulo' => 'E-mail','colunaTipo' => 'banco'),
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T002';
		$extra = $r_extra;

		function gridLinha($array) {
			global $g;
		}

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*$valorRegistros = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, str_replace('GROUP BY T117_Id', '', $extra));
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Boletos<br><input type="text" value="{$valorRegistros}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );		

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'fin001GridDividir':
        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
        	array('colunaValor' => "T002_Numero_Documento", 'colunaTitulo' => 'Documento'),            
            array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'Duplicata'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'width:1%'),
            array('colunaValor' => "T002_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prazos'),
            array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
            array('colunaValor' => "T002_Valor_Duplicata", 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => 2, 'colunaExibir'=>false),
            // array('colunaValor' => "T002_Valor_Desconto", 'colunaTitulo' => 'Desconto'),
            // array('colunaValor' => "T002_Valor_Juros", 'colunaTitulo' => 'Acréscimo')
            array('colunaValor' => "T002_Valor_Total", 'colunaTitulo' => 'Total', 'colunaExibir'=>false, 'colunaCasasDecimais' => 2),
            array('colunaValor' => "T002_Valor_Saldo", 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => 2),
            array('colunaValor' => "D027_Portador", 'colunaTitulo' => 'Portador'),           
        );
        
        /**
        * Definição da SQL
        */
        $from = 'T002';
        
        $extra = <<<EOT
        	LEFT JOIN D024 ON D024_Id=T002_D024_Id
			LEFT JOIN D027 ON D027_Id=T002_D027_Id
			LEFT JOIN D014 ON D014_Id=T002_D014_Id
			LEFT JOIN D032 ON D032_Id=D014_D032_Id 
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			LEFT JOIN T007 ON T007_Id=T002_T007_Id
			LEFT JOIN C004 ON C004_Id=T002_C004_Id
			LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
        	WHERE
EOT;

        $extra .= gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "(T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) AND T002_Flag_Cancelada != 'S' AND T002_Flag_Reparcelar='S' AND T002_Data_Recebimento='0000-00-00'"));
        $extra .= "order by T002_Data_Vencimento";
        
        /**
        * Filtro
        */
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        //echo $g['ui']->gridFiltroPrint($grid);

        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
         
        // calcular o total
        $total=0;
        foreach ($gridTabela['tbody'] as $linha){
            $total+=@gCorrigeNumeroInverte($linha['dados'][4]);
        }
        // inserir o total na última linha da tabela
        array_push($gridTabela['tbody'], array('dados'=>array('','','','<b>Total</b>','<b>'.gCorrigeNumero($total).'</b>','')));
        
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

	case 'fin001GridDivisao':

        $queryT002 = <<<EOT
            SELECT 
            T002_Id,
            T002_Numero_Documento, 
            T002_Numero_Duplicata, 
            T002_Data_Emissao, 
            D024_Nome_Empresa,
            D024_Id, 
            T002_Data_Vencimento, 
            T002_Valor_Duplicata, 
            T002_Valor_Saldo 
            FROM T002 
            left join D024 on D024_Id=T002_D024_Id 
            left join D027 on D027_Id=T002_D027_Id 
            left join D014 on D014_Id=T002_D014_Id 
            left join D032 on D032_Id=D014_D032_Id 
            left join D046 on D046_Id=D024_D046_Id
            left join T007 on T007_Id=T002_T007_Id 
            left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno 
            LEFT JOIN C004 ON C004_Id=T002_C004_Id 
            WHERE 
EOT;
        $queryT002 .= gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "(T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) AND T002_Flag_Cancelada != 'S' AND T002_Flag_Reparcelar='S' AND T002_Data_Recebimento='0000-00-00'"));

        $T002 = mysql_query($queryT002);
        
        $valorSomado = 0;
        while($aT002 = mysql_fetch_array($T002)){
            $D024_Id = $aT002['D024_Id'];
            $valorSomado = ($valorSomado + $aT002['T002_Valor_Saldo']);
            $T002_Id = $aT002['T002_Id'];
        }

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'T317_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true, 'colunaExibir' => false),
           	array('colunaValor' => 'T317_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
            array('colunaValor' => 'T317_Numero_Documento', 'colunaTitulo' => 'Documento', 'colunaEditar'=>true),
            array('colunaValor' => 'T317_Numero_Duplicata', 'colunaTitulo' => 'Duplicata', 'colunaEditar'=>true),
	        array('colunaValor' => 'T317_C004_Id', 'colunaTitulo' => 'Empresa', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('C004', 'C004_Id', 'C004_Nome_Abreviado, C004_Nome_Empresa', 'ORDER BY C004_Id'), 'callback' => 'campoMostraValor', 'callbackParameter' => 'C004_Nome_Abreviado', 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T317_Prazos_Dias"),
	        array('colunaValor' => 'C004_Nome_Abreviado', 'colunaExibir' => false),
	        array('colunaValor' => 'D073_Nome', 'colunaExibir' => false),
	        array('colunaValor' => 'T317_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData'),
            array('colunaValor' => 'T317_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar' => true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T317_Prazos_Dias"),
            array('colunaValor' => 'T317_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T317_Prazos_Dias", 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T317_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaEditar' => true, 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T317_Prazos_Dias"),
            array('colunaValor' => 'T317_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true,'colunaEditarJSCallback' => "colunaEditarCallbackJS_T317_Prazos_Dias", 'colunaEditarGeraSelect' => array('D027', 'D027_Id', 'D027_Portador', 'ORDER BY D027_Portador', true), 'callback' => 'campoMostraValor', 'callbackParameter' => 'D027_Portador'),
	        array('colunaValor' => 'ven002_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "fin001_Dia_Semana"),
            array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
        );

        /**
        * Definição da SQL
        */
        $from = 'T317';

        $extra = <<<EOT
            LEFT JOIN D027 ON D027_Id=T317_D027_Id
            LEFT JOIN C004 ON C004_Id=T317_C004_Id
            LEFT JOIN D073 ON D073_Id=T317_D073_Id
            WHERE T317_C007_Id = {$g['usuarioAtual']}
            ORDER BY T317_Numero_Parcela
EOT;
        /*
        * Filtro
        
        // Gera o filtro e acrescenta ao $extra
        $gridFiltro = array(
            
        );
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint();
        */
        
        //Define ação do click de abertura (ao clicar em uma linha do grid)

        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T317_Id-/, '');
                divSetarAcaoId('{$r_divReload}', id);
                divRefresh('{$r_divReload}');
            });
            
            $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
                return false;
            });
            
            // Função JS executada como callback do editar campo (colunaEditarJSCallback)
            function colunaEditarCallbackJS_T317_Prazos_Dias(T317_Id, campoEditado, valorAntigo, novoValor) {
                T317_Id = T317_Id.replace(/T317_Id-/, '');
                if (campoEditado == 'T317_Prazos_Dias') {
                    $.getJSON('/fin/fin001/grid_func-ajax/fin001atualizarParcelasDivisao/?ajax=true&T317_Id='              + encodeURIComponent(T317_Id) + 
                                                                                              '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                              '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                              '&novoValor='            + encodeURIComponent(novoValor) +  
                                                                                              '&callback=?', function(request) {
                        if (request.code) {
                            divRefresh('{$g['divId']}');
                        } else {
                            dialogConfirm('Erro', request.data);
                        }
                    });
                } else if (campoEditado == 'T317_Data_Vencimento') {
                    $.getJSON('/fin/fin001/grid_func-ajax/fin001atualizarParcelasDivisao/?ajax=true&T317_Id='              + encodeURIComponent(T317_Id) + 
                                                                                              '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                              '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                              '&novoValor='            + encodeURIComponent(novoValor) +  
                                                                                              '&callback=?', function(request) {
                        if (request.code) {
                            divRefresh('{$g['divId']}');
                        } else {
                            dialogConfirm('Erro', request.data);
                        }
                    });
                } else if (campoEditado == 'T317_Valor_Parcela') {
                    $.getJSON('/fin/fin001/grid_func-ajax/fin001atualizarParcelasDivisao/?ajax=true&T317_Id='              + encodeURIComponent(T317_Id) + 
                                                                                              '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                              '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                              '&novoValor='            + encodeURIComponent(novoValor) +
                                                                                              '&valorTotal='        + encodeURIComponent('{$valorSomado}') +   
                                                                                              '&callback=?', function(request) {
                        if (request.code) {
                            divRefresh('{$g['divId']}');
                        } else {
                            dialogConfirm('Erro', request.data);
                        }
                    });
                } else if (campoEditado == 'T317_D027_Id' || campoEditado == 'T317_C004_Id' || campoEditado == 'T317_D073_Id') {
                    divRefresh('{$g['divId']}');                        
                }
            }
        } </script></div>
EOT;
            
        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
        * Geração: Grid
        */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
    	$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }            
    break;
}
