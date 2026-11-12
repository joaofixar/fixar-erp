<?php
namespace hardness;

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

// Retira espaços em branco e acentos do nome do DashBoard
$g['r'][3] = preg_replace('/[^a-zA-Z0-9]/i', '', $g['r'][3]);

// A qual tabela este form está relacionada, e a chave primária da tabela
$form_tabela = 'D000';
$form_campo_pk = 'D000_Id';
// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
$enviar_btnId = 'content_' . $g['r'][3] . '_btn';
// Form que será executado ao clicar no botão
$enviar_acaoHref = '/etc/etc011/formDash/' . $g['r'][3] . '/';
// Editor

/*
switch($g['r'][3]){
    case 'etc011FormDashVendas':
        $grupo = '1';
    break;

    case 'etc011FormDashOrcamento':
        $form_editor = array('geral' => '{"FiltroGlobaletc011FormDashOrcamento":{"top":8,"left":12,"width":null,"hidden":0},"Refreshetc011FormDashOrcamento":{"top":8,"left":65,"width":null,"hidden":0}}');
        $grupo = '2';
    break;

    case 'etc011FormDashPainel':
        $form_editor = array('geral' => '{"FiltroGlobaletc011FormDashPainel":{"top":8,"left":12,"width":null,"hidden":0},"Refreshetc011FormDashPainel":{"top":8,"left":65,"width":null,"hidden":0}}');
        $grupo = '3';
    break;

    case 'etc011FormDashFinanceiro':
        $form_editor = array('geral' => '{"FiltroGlobaletc011FormDashFinanceiro":{"top":8,"left":12,"width":null,"hidden":0},"Refreshetc011FormDashFinanceiro":{"top":8,"left":65,"width":null,"hidden":0}}');
        $grupo = '4';
    break;
}
*/
// novo formulário
$form = new Formulario('form_' . $g['r'][3] . '_form');

$pesquisaGrid = array(
    'D132',                                                                                                         // tabela
    'D132_Id',                                                                                                      // chave primaria
    'D132_Descricao',                                                                                               // campos
    '',                                                                                                             // extras sql
    'Pesquisa de Relatórios/Gráficos',                                                                              // título da janela
    'Nome',                                                                                                         // descrição dos campos
    'adicionarItemDashboard',                                                                                       // callback js
    $g['divId'],                                                                                                    // divId
    $g['r'][3],                                                                                                     // parametro extra
    array(                                                                                                          // parametros para cada campo do grid
        array('colunaAlinhar' => 'left'),
    )
);

// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
$formArray = array(
    'campos' => array(
        'geral' => array(
            //'D132_Descricao' . $g['r'][3] => array('name' => 'D132_Descricao' . $g['r'][3], 'label' => 'Adicionar Relatório/Gráfico', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'reject' => true),
            'FiltroGlobal' . $g['r'][3] => array('name' => 'FiltroGlobal' . $g['r'][3], 'type' => 'button', 'value' => 'Filtrar', 'reject' => true),
            'Refresh' . $g['r'][3] => array('name' => 'Refresh' . $g['r'][3], 'type' => 'button', 'value' => 'Atualizar', 'reject' => true),
        )
    )
);

/**
* Cria os DIV com os relatórios/gráficos (esta informação está no $form_editor)
*/
// Arquivo que guarda a posição do formulário (caso seja formEditor.tpl)
//$dir = "{$g['pathDados']}personalizacaoForm/";
//is_dir($dir) or mkdir($dir, 0777, true);
//$arquivo = $dir . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}";
//$arquivoUsuario = $arquivo."-{$g['usuarioAtual']}";
//if(file_exists($arquivoUsuario)){
//  $arquivo = $arquivoUsuario;
//}
//
//if (isset($r_formEditorData)) {
//  $formEditorData = json_decode($r_formEditorData, true);
//  $formEditorUtilizado = true;
//  file_put_contents($arquivo, $r_formEditorData);
//} else {
//  // carregar!
//  $formEditorData = array();
//  $formEditorUtilizado = false;
//  if (file_exists($arquivo)) {
//    $formEditorData = json_decode(file_get_contents($arquivo), true);
//    $formEditorUtilizado = true;
//  }
//}
//
//// Adiciona cada campo como hidden
//foreach ($formEditorData as $key => $value) {
//    if (strpos($key, 'D132_Descricao') !== false) { continue; }
//    echo <<<EOT
//        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
//            setTimeout(function() {
//                adicionarItemDashboard('{$g['divId']}', '{$key}', '{$g['r'][3]}', 'N', '{$value['left']}', '{$value['top']}', '{$value['width']}', '{$value['height']}');
//            }, 100);
//        } </script></div>
//EOT;
//}
echo <<<EOT
    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
        //$('#{$g['divId']}').showLoading();
    } </script></div>
EOT;
/*
//-- Apresenta vídeo de melhoria do sistema
if (array_key_exists('C007_Id_Melhoria_Notificada', $g['C007'])) {
	$idMelhoria = $g['C007']['C007_Id_Melhoria_Notificada'] + 1;
	$idMelhoria = str_pad($idMelhoria, 5, "0", STR_PAD_LEFT);
    $arquivo = "static/melhorias/notificacao_{$idMelhoria}.php";
    if (file_exists($arquivo)) {
        require_once($arquivo);
        mysql_query("UPDATE C007 SET C007_Id_Melhoria_Notificada = '{$idMelhoria}' WHERE C007_Id = '{$g['usuarioAtual']}'");
    }
    echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#melhoriaMinimizar').unbind('click').unbind('click').bind('click', function(data) {
                $('#melhoriaVideo').toggle();
                if ($('#melhoriaMinimizar').html()=='-') {
                    $('#melhoriaMinimizar').html('+');
                } else {
                    $('#melhoriaMinimizar').html('-');
                }                
            });
        } </script></div>
EOT;
}
//--

//-- Apresenta avisos da frnquia ou avisos da empresa 
$avisosFranquia = avisos();
if (!empty($avisosFranquia)){
    echo <<<EOT
        $avisosFranquia
EOT;
}
*/
//--
$form_editor = array('geral' => '{"FiltroGlobal'.$g['r'][3].'":{"top":8,"left":5,"width":null,"hidden":0},"Refresh'.$g['r'][3].'":{"top":8,"left":60,"width":null,"hidden":0}}');
$tab_ = $g['r'][3];

$form->definirFormularioArray($formArray);

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

$ind = 1;
$sqlD132 = mysql_query("SELECT * FROM D132
                     LEFT JOIN D182 ON D182_D132_Id = D132_Id AND D182_C007_Id = '{$g['usuarioAtual']}'
                         WHERE D132_Grupo = '{$r_grupo}'
                           AND D132_Flag_Exibir_Dashboard = 'S'
                           AND D132_Flag_Exibir_Alcada <= '{$g['nivelAlcada']}'
                      ORDER BY D132_Tipo DESC, D132_Descricao ASC");
$qtdRelatorios = mysql_num_rows($sqlD132);
$D132Ids = array();
$D132Tipos = array();

echo <<<EOT
    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
        setTimeout(function() {
            $('#{$g['divId']} .formFormatoEditor').css('display','flex');
            $('#{$g['divId']} .formFormatoEditor').css('flex-wrap','wrap');
            $('#{$g['divId']} .formFormatoEditor').css('margin-left','5px');
            $('#{$g['divId']} .formFormatoEditor').css('margin-bottom','45px');
            $('#{$g['divId']} .formFormatoEditor').css('max-width','1300px');
        }, 50);
    } </script></div>
EOT;

if($qtdRelatorios > 0 && isset($g['c029Ids'][152])){
    $qtd=0;
    $primeiroItem = true;
    while($rowD132 = mysql_fetch_array($sqlD132)){
        $ind+=1;
        $qtd++;
        $hideLoading = ($qtd == $qtdRelatorios) ? 'S' : 'N';
        $uniqid = uniqid();
        // Break ao encontrar o primeiro item (que não seja indicador)
        if ($primeiroItem == true && $rowD132['D132_Tipo'] == 2) {
            $primeiroItem = false;
        }
        $primeiroItem_ = '0';
        if ($primeiroItem == false && $rowD132['D132_Tipo'] == 1) {
            $primeiroItem = true;
            $primeiroItem_ = '1';
        }
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                setTimeout(function() {
                    adicionarItemDashboard('{$g['divId']}', '{$rowD132['D132_Id']}', '{$g['r'][3]}', '{$hideLoading}', '{$rowD132['D132_Tipo']}', '{$primeiroItem_}');
                }, 50);
            } </script></div>
EOT;
        $D132Ids[] = $rowD132['D132_Id'];
        $D132Tipos[] = $rowD132['D132_Tipo'];
    }
    $D132Ids = json_encode($D132Ids);
    $D132Tipos = json_encode($D132Tipos);
    echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                setTimeout(function() {
                    carregarItemDashboard('{$g['divId']}', {$D132Ids}, '{$g['r'][3]}', '{$hideLoading}', {$D132Tipos});
                }, 50);
            } </script></div>
EOT;
} else {
    echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            //$('#{$g['divId']}').hideLoading();
        } </script></div>
EOT;

die();
}

// Somente para ser carregado do funcoesJS
echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    $('#FiltroGlobal{$g['r'][3]}').unbind('click').bind('click', function(data) {
        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Filtro Global', '/etc/etc011/content/etc011FiltroGlobal/', '&D132_Id=' + '&tab=' + encodeURIComponent('{$g['r'][3]}') + '&divIdReload=' + encodeURIComponent('{$g['divId']}'), [1200,350]);
    });
    $('#Refresh{$g['r'][3]}').unbind('click').bind('click', function(data) {
        divRefresh('{$g['divId']}', true);
    });
} </script></div>
EOT;

// Função criada para imprimir avisos que o cliente cadastrar.
function avisos(){
	global $g, $sys, $confUsuario;
	include_once("../../../configuracoes/sysConfig.php");
	
	$html = "";
	
	$servidor = (strpos($sys['urlEmpresa'], 'http') === false) ? 'http://'.$sys['urlEmpresa'] : $sys['urlEmpresa'];

    $html .= <<<EOT
        <section id="divBoleto" style="border: 0px; width: 97%; margin: 0px; margin-left: 8px; margin-right:5px; display: flex; flex-wrap: wrap">
EOT;

    //-- CONEXAO DIRETA COM O BANCO DE DADOS
    $con =@mysqli_connect($sys['dbHost'], $sys['dbUser'], $sys['dbPass'],$sys['dbDatabase']);
    // $dbs =@mysql_select_db($sys['dbDatabase'], $con) or die (mysql_error());
    $g['conexaoBanco'] = $con;

    $C032 = mysql_query("SELECT C032_Aviso, 
                                C032_Imagem 
                        FROM C032 
                        WHERE C032_Flag_Ativo='S'");

    while($mC032 = mysql_fetch_array($C032)) {
        // Tratar caracteres especiais
        $mC032['C032_Aviso'] = utf8_encode($mC032['C032_Aviso']);

        $mC032['C032_Aviso'] = str_replace(array('size="1"','size="2"','size="3"','size="4"','size="5"','size="6"'), 'style="font-size:13px;"', $mC032['C032_Aviso']);

        // impressão dos avisos cadastrados 
        $html .= <<<EOT
        <div style='border: 0px; width: 610px; max-width: 610px; margin: 5px; margin-left: 6px; margin-bottom:0px; height: 120px; padding: 10px; background: white; border-radius: 10px; display: grid; grid-template-columns: 1flr; background-image: url("http://{$sys['urlEmpresa']}{$g['pathWeb']}dados_usuarios/e001/imagens/avisos/{$mC032['C032_Imagem']}");background-repeat: no-repeat;background-size:auto;'>
            <div style='width:100%;'>
                {$mC032['C032_Aviso']}
            </div>
        </div>
EOT;
    
    }

    // Boletos
	if((isset($g['c029Ids'][35]) && $g['c029Ids'][35]) || (isset($g['c029Ids'][36]) && $g['c029Ids'][36]) || (isset($g['c029Ids'][37]) && $g['c029Ids'][37]) && !empty($g['usuarioAtual']) && ($g['nivelAlcada']==4)) {

		$Cnpj_Empresa_Usuaria = md5($g['C004']['C004_Cnpj']);
		$Local = 'S';
		$Usuario_Atual=63;
		$Gestao=true;

		if (strlen($Cnpj_Empresa_Usuaria)==14)
		{
			$Cnpj_Empresa_Usuaria=md5($Cnpj_Empresa_Usuaria);
		}

		$D024 = mysql_fetch_assoc(mysql_query("SELECT D024_Id FROM D024 WHERE md5(D024_Cnpj) = '{$Cnpj_Empresa_Usuaria}'"));
		$D024_Id = md5($D024['D024_Id']);

		$tem_boleto = 'N';
		if (!empty($Cnpj_Empresa_Usuaria)) {
			$sqlC004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '1'");
			$rowC004 = mysql_fetch_assoc($sqlC004);

			//Select dos boletos do cliente
			$cClausula  = " select ";
			$cClausula .= " T117_Id, ";
			$cClausula .= " T002_Data_Vencimento, ";
			$cClausula .= " D024_D046_Id, ";
			$cClausula .= " datediff(current_date(),T002_Data_Vencimento) dias ";
			$cClausula .= " from T117 ";
			$cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
			$cClausula .= " left join T002 on T002_T117_Id=T117_Id ";
			$cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
			$cClausula .= " where md5(D024_Cnpj)='".$Cnpj_Empresa_Usuaria."' ";
			$cClausula .= " and T117_Flag_Cancelado!='S'";
			$cClausula .= " AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)";
			$cClausula .= " order by T117_Data_Vencimento desc";

			$T117=mysql_query($cClausula);
			$mT117 = mysql_fetch_array($T117);
			$erroMysql = mysql_error();
			if(!empty($erroMysql)){
				$html .= "Erro select boletos(1):".$erroMysql;
				return $html;
			}

			//Verifica se os clientes tipo "NF-E" não possuem boleto ou tem boletos vencidos a mais de 366 dias
			if ((mysql_num_rows($T117) <= 0 || $mT117['dias'] > 366) && ($mT117['D024_D046_Id'] == '2')) {
				$html .= "<br><br><br><font style='font-size:18;font-weight:bold;color: var(--BGVermelho);background:white'>EXISTEM PENDENCIAS EM SEU CADASTRO, FAVOR ENTRAR EM CONTATO COM NOSSO SUPORTE PELO TELEFONE {$rowC004['C004_DDD_Telefone']} {$rowC004['C004_Telefone']}.</font>";
				return $html;
			}

			//Select dos boletos do cliente em abertos (vencidos e a vencer)
			$cClausula  = " SELECT ";
			$cClausula .= " T117_Id, ";
			$cClausula .= " T117_D024_Id, ";
			$cClausula .= " T117_Data_Emissao, ";
			$cClausula .= " T117_Data_Vencimento, ";
			$cClausula .= " T117_Valor_Total, ";
			$cClausula .= " T117_Nosso_Numero,";
			$cClausula .= " T117_Flag_Cancelado,";
			$cClausula .= " T002_Data_Vencimento, ";
			$cClausula .= " D007_Nome_Banco, ";
			$cClausula .= " DATEDIFF(T117_Data_Vencimento,CURRENT_DATE()) AS Dias_Vencer, ";
			$cClausula .= " DAYNAME(T117_Data_Vencimento) AS DiaSemanaNome, ";
			$cClausula .= " DATE_FORMAT(T117_Data_Vencimento,'%w') AS DiaSemana, ";
			$cClausula .= " DATE_FORMAT(CURRENT_DATE(),'%w') AS DiaSemanaHoje, ";
			$cClausula .= " DATEDIFF(CURRENT_DATE(),T002_Data_Vencimento) AS Dias_Vencido, ";
			$cClausula .= " DATEDIFF(T117_Data_Vencimento,T002_Data_Vencimento) AS Dias_Prorrogado ";
			$cClausula .= " FROM T117 ";
			$cClausula .= " LEFT JOIN D024 ON D024_Id=T117_D024_Id ";
			$cClausula .= " LEFT JOIN T002 ON T002_T117_Id=T117_Id ";
			$cClausula .= " LEFT JOIN D007 ON D007_Id=T117_D007_Id ";
			$cClausula .= " WHERE md5(D024_Cnpj)='".$Cnpj_Empresa_Usuaria."' ";
			$cClausula .= " AND T117_Flag_Cancelado='N' ";
			$cClausula .= " AND T002_Data_Recebimento='0000-00-00' ";
			$cClausula .= " AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)";
			$cClausula .= " ORDER BY T117_Data_Vencimento";
			$cSql_T117=mysql_query($cClausula);
			$erroMysql = mysql_error();
			if(!empty($erroMysql)){
				$html .= "Erro select boletos(2):".$erroMysql;
				return $html;
			}

			if (mysql_num_rows($cSql_T117) > 0){
				//Impressão dos boletos do cliente
				while ($mT117 = mysql_fetch_array($cSql_T117)) {
					//Caso Dias_Vencido<=0
					//Hoje seja segunda  e DiasSemana do vencimento for Sab/Dom)
					if(($mT117['Dias_Vencido']<=0) || (($mT117['DiaSemanaHoje']==1) && ($mT117['DiaSemana']==0 || $mT117['DiaSemana']==6))){
						$html .= <<<EOT
						<div id="divBoleto" style="background-color: white; border-radius: 10px; width: 628px; max-width: 628px; height: 138px; margin:5px; margin-right:10px; font-size: 13px; display:flex; flex-wrap: wrap;">
EOT;
						if ($mT117['Dias_Vencer'] == 0) {
							$dias_vencimento = " (hoje)";
						} elseif ($mT117['Dias_Vencer'] == 1) {
							$dias_vencimento = " (amanhã) ";
						} elseif ($mT117['Dias_Vencer'] > 2) {
							$dias_vencimento = " (daqui a {$mT117['Dias_Vencer']} dias)";
						}
						$path = base64_encode("http://".$sys['urlEmpresa'] . $g['pathWeb'] . "dados_usuarios/{$sys['dbDatabase']}/");
						$md5BoletoCliente = md5($mT117['T117_Id'].$mT117['T117_D024_Id']);
						// $vencimentoBoleto = troca_data_dma($mT117['T117_Data_Vencimento']);
						$vencimentoBoleto = gCorrigeData($mT117['T117_Data_Vencimento']);
						$urlBoleto = $servidor."/hardness/boleto/gerar_boleto_empresa.php?cQuery=".$md5BoletoCliente."&path=".$path."&buscarBoletoBanco=suporte";
						$html .= <<<EOT
								<div style='margin-top:10px; margin-left: 20px; width: 615px;'>
									<font style="font-family:arial,verdana;font-size:13px;">
										Olá! Já está disponível o boleto para pagamento.
									</font>
								</div>
								<div style='display:grid; grid-template-columns: 80px 520px;margin:5px;margin-left:15px;'>
									<div style='margin:5px'>
										<a target="_blank' href="{$urlBoleto}">
											<img src="$servidor/hardness/imagens/png/boleto.png" border=0 ><font color=blue>
										</a>
									</div>
									<div style='margin:5px'>
										<a target="_blank" href="{$urlBoleto}">
											Empresa: <b>{$g['C004']['C004_Nome_Abreviado']}</b><br>
											Vencimento em <b>{$vencimentoBoleto} {$dias_vencimento} </b><br>
											<font style="color:green;font-weight:bold">Clique aqui para imprimir o boleto</font>
										</a>
									</div>
								</div>
								<div style='margin-bottom: 10px; margin-left: 20px'>
									O pagamento pode ser realizado pela internet ou em qualquer agência bancária até vencimento.
								</div>
							</div>
EOT;
					} else {
						//Caso Dias_Vencido > 0
						//Caso Dias_Vencido>2 e DiasSemana vencimento igual Sab/Dom
						$html .= <<<EOT
						<div id="divBoleto" style="background-color: white; border-radius: 10px; width: 628px; max-width: 628px; height: 138px; margin:10px; margin-top: 6px; margin-right:0px; font-size: 14px; display:flex; flex-wrap: wrap;">
							<table class="tableBoletoHardness" width=512 border=0 cellpadding=5 bgcolor=white style="border: 1px dashed red;">
								<tr>
									<td colspan=2 style="border-bottom:0px solid silver">
										<font style="font-family:arial,verdana;font-size:12px;color: var(--BGVermelho);font-weight:bold;">
										Consta em nossa base a(s) pendendência(s) abaixo relacionada(s)
									</td>
								</tr>
EOT;
						if($mT117['Dias_Vencer'] < 0) {
							$dias_vencimento = " (".abs($mT117['Dias_Vencer'])." dias atrás)";
						} else {
							$dias_vencimento = "";
						}
						$dias = 10;
						if (isset($Gestao)) {
							$dias = 15;
						}

						$md5BoletoCliente = md5($mT117['T117_Id'].$mT117['T117_D024_Id']);
						$vencimentoBoleto = gCorrigeData($mT117['T117_Data_Vencimento']);
						$urlBoleto = $servidor."/hardness/boleto/gerar_boleto_empresa.php?cQuery=".$md5BoletoCliente."&buscarBoletoBanco=suporte";
						$html .= <<<EOT
							<tr>
								<td width=10 style='border-left:0px solid silver'>
									<a target="_blank" href="{$urlBoleto}">
										<img src='$servidor/hardness/imagens/png/boleto.png' border=0><font color=blue>
									</a>
								</td>
								<td class=fonte style='font-size:12px;border-right:0px solid silver'>
									<a target='_blank' href="{$servidor}/externo/cobranca/atualizarBoleto/?ajax=true&noEmp=true&a={$D024_Id}">
										Empresa:<b> {$g['C004']['C004_Nome_Abreviado']}</b><br>
										Vencimento em <b>{$vencimentoBoleto}<font style='color: var(--BGVermelho);'>{$dias_vencimento}</b><br>
										<font style='color:green;font-size:14px;font-weight:bold'>Clique aqui para gerar novo boleto</font><br>
									</a>
EOT;
						$html .= <<<EOT
								</td>
							</tr>
EOT;
						if(abs($mT117['Dias_Prorrogado']) >= 3) {
							if((abs($mT117['Dias_Vencer']) <= 0) || (abs($mT117['Dias_Prorrogado']) > $dias)) {
								/*echo <<<EOT
									<script>
										top.location.replace("{$servidor}/externo/cobranca/atualizarBoleto/?ajax=true&noEmp=true&a={$D024_Id}");
									</script>
EOT;*/
							}
						} else if ($dias-abs($mT117['Dias_Vencer']) <= 0) {
							$html .= <<<EOT
								<script>
									top.location.replace("{$servidor}/externo/cobranca/atualizarBoleto/?ajax=true&noEmp=true&a={$D024_Id}");
								</script>
EOT;
						}
						if(abs($mT117['Dias_Vencido']) >= $dias) {
							$html .= <<<EOT
								<script>
									top.location.replace("{$servidor}/externo/cobranca/atualizarBoleto/?ajax=true&noEmp=true&a={$D024_Id}");
								</script>
EOT;
						}
						if(abs($mT117['Dias_Vencido']) >= ($dias/2)) {
							$alteraFundo = true;
							$prazoSuspensao = $dias-abs($mT117['Dias_Vencido']);
							$prazoSuspensao .= ($prazoSuspensao > 1) ? ' dias' : ' dia';
							$html .= <<<EOT
								<tr>
									<td width=10 class=fonte colspan=2 style='font-size:12px; border-top:0px solid silver; color: var(--BGVermelho); font-weight: bold;'>
										ATENÇÃO! O acesso ao sistema será suspenso dentro de {$prazoSuspensao}.
									</td>
								</tr>
EOT;
						}
						
						if($alteraFundo){
							$html .= <<<EOT
								<style>
									.tableBoletoHardness {
										background-color: #FBECEC;
									}
								</style>
EOT;
						}

						$html .= <<<EOT
							<tr>
								<td width=10 class=fonte colspan=2 style='font-size:11px;border-top:0px solid silver''>
									Desconsiderar caso a(s) pendência(s) já tenha(m) sido quitada(s), assim que for acusado em nosso sistema a(s) mesma(s) será(ão) baixada(s).
								</td>
							</tr>
						</table></div>
EOT;
					}
				}
				$tem_boleto = 'S';
			}

			if ($tem_boleto == 'S') {
				//-- Encontra todos os a vencer
				$cClausula  = " select ";
				$cClausula .= " * ";
				$cClausula .= " from T135 ";
				$cClausula .= " left join D024 on D024_Id=T135_D024_Id ";
				$cClausula .= " where md5(D024_Cnpj)='".$Cnpj_Empresa_Usuaria."' ";
				$cClausula .= "   and T135_Data_Termino<=(Curdate() + interval 30 day) ";
				$cClausula .= "   and T135_Flag_Renovado!='S' ";
				$cClausula .= "   and T135_Flag_Renovado!='C' ";
				$cSql_T135=mysql_query($cClausula);
				$erroMysql = mysql_error();
				if(!empty($erroMysql)){
					$html .= "Erro select boletos(4):".$erroMysql;
					return $html;
				}

				if (mysql_num_rows($cSql_T135) > 0) {
					$mT135=mysql_fetch_array($cSql_T135);
					$html .= <<<EOT
						<table width=500 border=0 cellpadding=5 cellspacing=0 bgcolor=white style='border: 1px dashed green; border-top:0px'>";
							<tr>
								<td colspan=2 style='border-bottom:0px solid silver'>
									<font style='font-family:arial,verdana;font-size:12px;'>
										{$mT135['D024_Nome_Empresa']}, <b>agradecemos à confiança e parceria</b> durante estes últimos 12 meses! A Hardness Sistemas vem trabalhando constantemente para ofereca você, serviços e produtos que atendam melhor as suas necessidades e expectativas!<br>
								</td>
							</tr>
							<tr>
								<td class=fonte style='font-size:12px;border-right:0px solid silver'>
									Caso a sua empresa não queira renovar os serviços, basta nos enviar um e-mail pedindo o <font style='color: var(--BGVermelho)'>cancelamento</font> para {$rowC004['C004_E_Mail']}.<br>
								</td>
							</tr>
							<tr>
								<td width=10 class=fonte colspan=2 style='font-size:12px;border-top:0px solid silver''>
									<b>Obrigado!</b>
									<BR>
									<BR>
									Equipe {$rowC004['C004_Nome_Abreviado']}
								</td>
							</tr>
						</table>
EOT;
				}
			} else {
				//-- Encontra todos os a vencer
				$cClausula  = " select ";
				$cClausula .= " * ";
				$cClausula .= " from T135 ";
				$cClausula .= " left join D024 on D024_Id=T135_D024_Id ";
				$cClausula .= " where md5(D024_Cnpj)='".$Cnpj_Empresa_Usuaria."' ";
				$cClausula .= "   and T135_Data_Termino>Curdate() ";
				$cClausula .= "   and T135_Data_Termino<=(Curdate() + interval 30 day) ";
				$cClausula .= "   and T135_Flag_Renovado!='S' ";
				$cClausula .= "   and T135_Flag_Renovado!='C' ";
				$cSql_T135=mysql_query($cClausula);
				$erroMysql = mysql_error();
				if(!empty($erroMysql)){
					$html .= "Erro select boletos(5):".$erroMysql;
					return $html;
				}

				if(mysql_num_rows($cSql_T135) > 0) {
					$mT135=mysql_fetch_array($cSql_T135);
					$html .= <<<EOT
						<table width=500 border=0 cellpadding=5 cellspacing=0 bgcolor=#EAF7EA style='border: 2px solid green;'>
							<tr>
								<td colspan=2 style='border-bottom:0px solid silver' bgcolor=green>
									<font style='font-family:arial,verdana;font-size:12px;'>
									<font style='color:white;font-weight:bold;font-size:13px'>RENOVAÇÃO ANUAL</font>
								</td>
							</tr>
							<tr>
								<td colspan=2 style='border-bottom:0px solid silver'>
									<font style='font-family:arial,verdana;font-size:12px;'>
										{$mT135['D024_Nome_Empresa']}, <b>agradecemos à confiança e parceria</b> durante estes últimos 12 meses! A {$rowC004['C004_Nome_Abreviado']} vem trabalhando constantemente para oferecer a você, serviços e produtos que atendam melhor as suas necessidades e expectativas!<br>
								</td>
							</tr>
							<tr>
								<td class=fonte style='font-size:12px;border-right:0px solid silver'>
									Caso a sua empresa não queira renovar os serviços, basta nos enviar um e-mail pedindo o <font style='color: var(--BGVermelho)'>cancelamento</font> para {$rowC004['C004_E_Mail']}.<br>
								</td>
							</tr>
							<tr>
								<td width=10 class=fonte colspan=2 style='font-size:12px;border-top:0px solid silver''>
									<b>Obrigado!</b>
									<BR>
									<BR>
									Equipe {$rowC004['C004_Nome_Abreviado']}
								</td>
							</tr>
						</table>
EOT;
				}
			}
		}

	}
	// Fim boletos

    // Avisos locais

    //-- CONEXAO DIRETA COM O BANCO DE DADOS
    $con =@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
    // $dbs =@mysql_select_db($sys['dbDatabase'], $con) or die (mysql_error());
    $g['conexaoBanco'] = $con;

    if ($confUsuario['dbDatabase']!='e001') {

        $C032 = mysql_query("SELECT C032_Aviso, 
                                    C032_Imagem,
                                    C007_Nome
                            FROM C032 
                        LEFT JOIN C036 ON C036_C032_Id=C032_Id
                        LEFT JOIN C007 ON C007_Id='{$g['usuarioAtual']}'
                            WHERE C032_Flag_Ativo='S'
                                AND (C036_C021_Id='{$g['perfilUsuario']}' 
                                OR IFNULL(C036_C021_Id, 0) = 0) 
                                AND (C036_C004_Id='{$g['empresaAtual']}' 
                                OR IFNULL(C036_C004_Id, 0) = 0) 
                                AND (C036_C007_Id='{$g['usuarioAtual']}' 
                                OR IFNULL(C036_C007_Id, 0) = 0)
                                AND (C036_Data_Inicial>=curdate() 
                                OR IFNULL(C036_Data_Inicial,'0000-00-00') = '0000-00-00')
                                AND (curdate()<=C036_Data_Final
                                OR IFNULL(C036_Data_Final,'0000-00-00') = '0000-00-00')
                        GROUP BY C032_Id");

        while($mC032 = mysql_fetch_array($C032)) {
            // Tratar caracteres especiais
            $mC032['C032_Aviso'] = utf8_encode($mC032['C032_Aviso']);

            $mC032['C032_Aviso'] = str_replace(array('size="2"','size="3"','size="4"','size="5"','size="6"'), 'style="font-size:13px;"', $mC032['C032_Aviso']);

            $mC032['C032_Aviso'] = str_replace('[NOME_USUARIO]', $mC032['C007_Nome'], $mC032['C032_Aviso']);
            

            // impressão dos avisos cadastrados 
            $html .= <<<EOT
			<div style='border: 0px; width: 610px; max-width: 610px; margin: 5px; margin-left: 6px; margin-bottom:0px; height: 120px; padding: 10px; background: white; border-radius: 10px; display: grid; grid-template-columns: 1flr; background-image: url("http://{$sys['urlEmpresa']}{$g['pathWeb']}dados_usuarios/e001/imagens/avisos/{$mC032['C032_Imagem']}");background-repeat: no-repeat;background-size:auto;'>
				{$mC032['C032_Aviso']}
            </div>
EOT;
        
        }

    }


    $html .= <<<EOT
        </section>
EOT;

    return $html;
}

