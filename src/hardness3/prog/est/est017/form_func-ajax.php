<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());
require_once('bibliotecas/classes/EST017.php');
require_once('bibliotecas/classes/SMTEMAIL.php');
$EST017 = new EST017();
$SMTEMAIL = new SMTEMAIL_();

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'est017ProcessarEntradas':
		global $g;

		$C007_Id = $g['usuarioAtual'];
		$email   = array();

		$extra = base64_decode($r_extra);
		$dadosCertificado = $EST017->dadosCertificadosOP();
		log('$dadosCertificado: ' . json_encode($dadosCertificado));

		$retorno = $EST017->processarEntrada($extra);

		if ($retorno === true) {
			foreach ($dadosCertificado as $certificado) {
				$arrayDadosEmail = array('C007_Id'      => $C007_Id,
										 'assunto'      => $certificado['assunto'],
										 'corpo'        => $certificado['corpo'],
										 'destinatario' => $certificado['emails'],
										 'anexo'        => $certificado['certificado']);
		
				log('$arrayDadosEmail: ' . json_encode($arrayDadosEmail));

				if ((isset($arrayDadosEmail['anexo'])) && (!empty($arrayDadosEmail['anexo']))) {
					$enviaEmail = $SMTEMAIL->geraEmail($arrayDadosEmail);
				}

				if ($enviaEmail) {
					$updateOP  = " UPDATE T256a SET ";
					$updateOP  = "   T256A_Flag_Certificado_Qualidade = 'S' ";
					$updateOP  = " WHERE T256A_T256_Id = {$certificado['op']} ";

					mysql_query($updateOP);
				}

				$email = array('OP'    => $certificado['op'],
			                   'Envio' => $enviaEmail);

				array_push($certificado, $dadosEmail);
			}

			$resposta['email'] = $email;
			$resposta['code']  = true;
			$resposta['data']  = "Entrada de estoque realizada com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'processaProducaoEmEtapas':
		$extra = base64_decode($r_extra);
		$retorno = $EST017->validaProcessarEntradaEmEtapas($extra);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'afterProcessaProducaoEmEtapas':
		$dadosCertificado = $EST017->dadosCertificadosOP();
		log('dadosCertificado: ' . json_encode($dadosCertificado));

		foreach ($dadosCertificado as $certificado) {
			$arrayDadosEmail = [
				'C007_Id'      => $C007_Id,
				'assunto'      => $certificado['assunto'],
				'corpo'        => $certificado['corpo'],
				'destinatario' => $certificado['emails'],
				'anexo'        => $certificado['certificado']
			];
	
			log('arrayDadosEmail: ' . json_encode($arrayDadosEmail));
			if ((isset($arrayDadosEmail['anexo'])) && (!empty($arrayDadosEmail['anexo']))) {
				$enviaEmail = $SMTEMAIL->geraEmail($arrayDadosEmail);
			}

			if ($enviaEmail) {
				$updateOP  = "UPDATE T256A SET T256A_Flag_Certificado_Qualidade = 'S' WHERE T256A_T256_Id = {$certificado['op']}";
				mysql_query($updateOP);
			}

			$email = [
				'OP'    => $certificado['op'],
				'Envio' => $enviaEmail
			];
			array_push($certificado, $dadosEmail);
		}

		$resposta['email'] = $email;
		$resposta['code']  = true;
		// $resposta['data']  = "Entrada de estoque realizada com sucesso!";
		$resposta['data']  = null;
	break;

	case 'atualizarComposicaoProdutos':
		$retorno = $EST017->atualizarComponentes(false, $r_T255_Id);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'validacaoItemPorOP':
		$T255_Id = $r_T255_Id;
		$sql_dados2 = " SELECT count(T256_Id) AS QtdItens
						FROM T256
						LEFT JOIN D009 ON D009_Id = T256_D009_Id
						LEFT JOIN D049 ON D049_Id = D009_D049_Id
						LEFT JOIN D001 ON D001_Id = D049_D001_Id
						LEFT JOIN D037 ON D037_Id = D001_D037_Id
						LEFT JOIN T006 ON T006_Id = T256_T006_Id
						LEFT JOIN T005 ON T005_Id = T006_T005_Id
						LEFT JOIN D024 ON D024_Id = T005_D024_Id
						WHERE T256_T255_Id = ${T255_Id}
						GROUP BY T256_T255_Id
                      ";

		$sql_dados2 = mysql_query($sql_dados2);

		while ($fetDados = mysql_fetch_assoc($sql_dados2)) {
			$quantidadeItens = $fetDados['QtdItens'];
		}	
		$resposta['qtdadeItens'] = $quantidadeItens;
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
