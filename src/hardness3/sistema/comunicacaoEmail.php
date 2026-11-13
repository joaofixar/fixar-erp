<?php
namespace hardness;
/**
* Classe de comunica��o com o email e manuten��o das informa��es no banco de dados (T139)
*/
set_time_limit(0);
class comunicacaoEmail {
	public $host = 'localhost';
	public $port = 993;
	public $pasta = 'E';
	public $login;
	public $password;
	public $mailbox;
	public $mbox = false;
	public $debugTempo = null;
	public $debugTempoId = '';
	
	/**
	* Constructor
	* 
	* @param string $host
	* @param string $login
	* @param string $password
	* @param char $pasta				// E - Entrada / S - Sa�da
	* @param int $T139_Id				// Se fornecido, ir� modificar a variavel $pasta de acordo
	* @param int $porta					// Porta de conexao
	* @param int $outraConta			// Identifica se a conexao � para obter um e-mail de outro usu�rio
	* @return comunicacaoEmail
	*/
	public function __construct($host, $login, $password, $pasta, $T139_Id = false, $porta = 993, $outraConta = false) {
		if ($T139_Id) {
			$T139_Id = mysql_real_escape_string($T139_Id);
			$sql = "SELECT T139_Pasta FROM T139 WHERE T139_Id = {$T139_Id};";
			$retorno = mysql_query($sql);
			$row = mysql_fetch_assoc($retorno);
			$pasta = $row['T139_Pasta'];
		}
		$this->host = $host;
		$this->login = $login;
		$this->password = $password;
		$this->mailbox = 'INBOX';
		$this->pasta = $pasta;
		$this->port = empty($porta) ? 993 : $porta;
		$this->outraConta = $outraConta;
		return false;
	}

	private function tempoEmailHabilitado() {
		if ($this->debugTempo !== null) {
			return $this->debugTempo;
		}
		$this->debugTempo = (PHP_SAPI === 'cli' || empty($_SERVER['REMOTE_ADDR']));
		if (!$this->debugTempo && !empty($_REQUEST['debugTempoEmail'])) {
			$this->debugTempo = true;
		}
		return $this->debugTempo;
	}

	private function tempoEmailExecucaoId() {
		if (empty($this->debugTempoId)) {
			$this->debugTempoId = substr(md5(uniqid('', true)), 0, 4);
		}
		return $this->debugTempoId;
	}

	private function tempoEmailFormatar($tempo) {
		return number_format((double) $tempo, 4, '.', '') . 's';
	}

	private function registrarTempoDecorrido($etapa, $tempo, $extra = array()) {
		global $g;
		if (!$this->tempoEmailHabilitado()) {
			return;
		}

		$partes = array();
		$partes[] = '[email-timing]';
		$partes[] = '[' . $this->tempoEmailExecucaoId() . ']';
		$partes[] = '[' . $etapa . ']';
		$partes[] = $this->tempoEmailFormatar($tempo);
		if (!empty($g['usuarioAtual'])) {
			$partes[] = 'usuario=' . $g['usuarioAtual'];
		}
		foreach ($extra as $chave => $valor) {
			if (is_bool($valor)) {
				$valor = $valor ? '1' : '0';
			} else if (is_array($valor)) {
				$valor = json_encode($valor);
			}
			if ($valor === '' || $valor === null) {
				continue;
			}
			$valor = str_replace(array("\r", "\n", "\t"), ' ', (string) $valor);
			$partes[] = $chave . '=' . $valor;
		}

		echo (implode(' ', $partes)) . "\n";
	}

	private function registrarTempo($etapa, $inicio, $extra = array()) {
		$this->registrarTempoDecorrido($etapa, microtime(true) - $inicio, $extra);
	}

	/**
	* Estabelece uma conex�o com o servidor de e-mail caso ainda n�o exista ela
	*
	*/
	private function connect() {
		global $g;
		// ini_set('display_errors', 1);
    	// error_reporting(E_ALL);
		if (!$this->mbox) {
			$tempoConnectTotal = microtime(true);
			log("HOST:".$this->host);
			log("port:".$this->port);
			log("login:".$this->login);
			log("pasta:".$this->pasta);

			if(!function_exists('imap_open')){
				log("IMAP não está instalado no servidor.");
				return "Retorno IMAP:IMAP não está instalado no servidor.Contate seu suporte técnico.";
			}
			$informacoesTec = "<br><br><b>INFORMAÇÕES TÉCNICAS</b><br>Host: ".$this->host."<br>Porta: ".$this->port."<br>Email: ".$this->login."<br>Senha: *******";
			if ($this->port == 993) {
				$tempoImapOpenInicial = microtime(true);
				$this->mbox = @imap_open("{{$this->host}:{$this->port}/imap/ssl/novalidate-cert}", $this->login, $this->password);
				$this->registrarTempo('connect.imap_open_inicial', $tempoImapOpenInicial, array('ssl' => '1'));
				$informacoesTec .= "<br>Mailbox: {$this->host}:{$this->port}/imap/ssl/novalidate-cert";
				$informacoesTec .= "<br>Utilizando SSL: SIM";
			} else {
				$tempoImapOpenInicial = microtime(true);
				$this->mbox = @imap_open("{{$this->host}:{$this->port}/imap/novalidate-cert}", $this->login, $this->password);
				$this->registrarTempo('connect.imap_open_inicial', $tempoImapOpenInicial, array('ssl' => '0'));
				$informacoesTec .= "<br>Mailbox: {$this->host}:{$this->port}/imap/novalidate-cert";
				$informacoesTec = "<br>Utilizando SSL: NÃO";
			}
			if (!$this->mbox) {
				return "Retorno IMAP: ".imap_last_error()."<br><br>Entre em contato com o suporte técnico do seu provedor de email e revise as informações abaixo.".$informacoesTec."";
			}
			if ($this->pasta == 'E') {
				if (!empty($g['C007']['C007_Email_Pasta_Entrada']) && !$this->outraConta) {
					$this->mailbox = $g['C007']['C007_Email_Pasta_Entrada'];
				} else {
					$tempoImapList = microtime(true);
					$pastas = imap_list($this->mbox, "{{$this->host}}", "*");
					$this->registrarTempo('connect.imap_list', $tempoImapList, array('tipo' => 'entrada', 'qtd_pastas' => is_array($pastas) ? count($pastas) : 0));
					//print_r($pastas);
					//die();
					foreach ($pastas as $pasta) {
						$mailbox = explode('}', $pasta);
						if ($mailbox[1] == 'INBOX' || $mailbox[1] == 'inbox') {
							$this->mailbox = $mailbox[1];
						}
					}
				}
				if (empty($this->mailbox)) {
					$this->mailbox = $mailbox[1];
				}
			} else {
				if (!empty($g['C007']['C007_Email_Pasta_Saida']) && !$this->outraConta) {
					$this->mailbox = $g['C007']['C007_Email_Pasta_Saida'];
				} else {
					$tempoImapList = microtime(true);
					$pastas = imap_list($this->mbox, "{{$this->host}}", "*");
					$this->registrarTempo('connect.imap_list', $tempoImapList, array('tipo' => 'saida', 'qtd_pastas' => is_array($pastas) ? count($pastas) : 0));
					//print_r($pastas);
					//die();
					foreach ($pastas as $pasta) {
						if (stripos($pasta, 'sent') !== false || stripos($pasta, 'enviada') !== false) {
							$mailbox = explode('}', $pasta);
							$this->mailbox = $mailbox[1];
							continue;
						}
					}
				}
			}
			if ($this->port == 993) {
				$tempoImapOpenMailbox = microtime(true);
				$this->mbox = imap_open("{{$this->host}:{$this->port}/imap/ssl/novalidate-cert}{$this->mailbox}", $this->login, $this->password);
				$this->registrarTempo('connect.imap_open_mailbox', $tempoImapOpenMailbox, array('mailbox' => $this->mailbox, 'ssl' => '1'));
			} else {
				$tempoImapOpenMailbox = microtime(true);
				$this->mbox = imap_open("{{$this->host}:{$this->port}/imap/novalidate-cert}{$this->mailbox}", $this->login, $this->password);
				$this->registrarTempo('connect.imap_open_mailbox', $tempoImapOpenMailbox, array('mailbox' => $this->mailbox, 'ssl' => '0'));
			}
			$this->registrarTempo('connect.total', $tempoConnectTotal, array('mailbox' => $this->mailbox));
		}
		return $this->mbox;
	}
	
	/**
	* Tenta conectar e lista todas as pastas do e-mail
	* 
	*/
	public function listarPastas() {
		if ($this->port == 993) {
			$this->mbox = @imap_open("{{$this->host}:{$this->port}/imap/ssl/novalidate-cert}", $this->login, $this->password);
		} else {
			$this->mbox = @imap_open("{{$this->host}:{$this->port}/imap/novalidate-cert}", $this->login, $this->password);
		}
		//echo imap_last_error();
		if (!$this->mbox) { return false; }
		$pastas = imap_list($this->mbox, "{{$this->host}}", "*");
		return $pastas;
	}
	
	/**
	* Verifica se existe e-mails novos na $pasta definida e inclue os novos em uma base de dados T139
	*/
	public function atualizarEmails($incluirCorpo = true) {
		global $g;
		echo "conectando email usuario - {$g['C007']['C007_Id']}\n";
		$tempoAtualizarTotal = microtime(true);
		$retorno = $this->connect();
		echo "fim connect\n";
		if(!$this->mbox) { return $retorno;	}
		// imap
		$count = 0;
		$totalExistentes = 0;
		$totalAnalisados = 0;
		$tempoImapCheck = microtime(true);
		$check = imap_check($this->mbox);
		$this->registrarTempo('atualizar.imap_check', $tempoImapCheck, array('nmsgs' => isset($check->Nmsgs) ? $check->Nmsgs : 0));
		//$result = imap_fetch_overview($this->mbox, "1:{$check->Nmsgs}");
		$tmp = $check->Nmsgs - 100;
		if ($tmp < 1) { $tmp = 1; }
		$tempoImapFetchOverview = microtime(true);
		$result = imap_fetch_overview($this->mbox, "{$tmp}:{$check->Nmsgs}");
		$this->registrarTempo('atualizar.imap_fetch_overview', $tempoImapFetchOverview, array('inicio' => $tmp, 'fim' => isset($check->Nmsgs) ? $check->Nmsgs : 0, 'qtd' => is_array($result) ? count($result) : 0));
		$primeiraVez = false;
		for ($i = count($result)-1; $i >= 0; $i--) {
			// executa o cache apenas uma vez, se tiver email novos
			if (!$primeiraVez) {
				$tempoCache = microtime(true);
				$primeiraVez = true;
				$cache = array();
				$sql = "SELECT T139_Uid FROM T139 WHERE T139_C007_Id = '{$g['usuarioAtual']}';";
				$resultado = mysql_query($sql);
				while ($row = mysql_fetch_row($resultado)) {
					$cache[$row[0]] = true;
				}
				$this->registrarTempo('atualizar.cache_uids_banco', $tempoCache, array('qtd' => count($cache)));
			}
			//
			$overview = $result[$i];
			$totalAnalisados++;
			//if ($count > 10) { continue; }
			$overview->uid = (int) mysql_real_escape_string($overview->uid);
			$overview->subject = $this->decodeMimeString($overview->subject);
			$overview->subject = mysql_real_escape_string(@$overview->subject);
			!empty($overview->subject) or ($overview->subject = '(sem assunto)');
			$overview->from = mysql_real_escape_string($this->decodeMimeString(@$overview->from));
			$overview->to = mysql_real_escape_string($this->decodeMimeString(@$overview->to));
			$date = date_parse($overview->date);
			$date = "$date[year]-$date[month]-$date[day] $date[hour]:$date[minute]-$date[second]";
			$lastId = false;
			if (!isset($cache[$overview->uid])) {
				$tempoEmailTotal = microtime(true);
				$tempoCorpo = 0;
				$tempoInsert = 0;
				$tempoAnexos = 0;
				$tempoAssociar = 0;
			//if ($overview->uid == '31400') {
				$D024_Id = '';
				$T139_Flag_Spam = 'S';
				preg_match("/amento.*\[([0-9]+)\]/Uis", $overview->subject, $orcamento);
				$T003_Id = 'null';
				if (isset($orcamento[1]) && !empty($orcamento[1])) {
					$T003_Id = $orcamento[1];
					$sql = "SELECT T003_D024_Id FROM T003 WHERE T003_Id = '{$T003_Id}';";
					$res = mysql_query($sql);
					$row = mysql_fetch_assoc($res);
					$D024_Id = $row['T003_D024_Id'];
					if($D024_Id){
						$T139_Flag_Spam = 'N';
					}
				}
				preg_match("/edido.*\[([0-9]+)\]/Uis", $overview->subject, $pedido);
				$T005_Id = 'null';
				if (isset($pedido[1]) && !empty($pedido[1])) {
					$T005_Id = $pedido[1];
					$sql = "SELECT T005_D024_Id FROM T005 WHERE T005_Id = '{$T005_Id}';";
					$res = mysql_query($sql);
					$row = mysql_fetch_assoc($res);
					$D024_Id = $row['T005_D024_Id'];
					if($D024_Id){
						$T139_Flag_Spam = 'N';
					}
				}
				$corpo = '';
				if ($incluirCorpo) {
					$tempoCorpoInicio = microtime(true);
					//$this->pegarMensagemCorpo($lastId);
					list($corpo, $estrutura) = $this->pegarMensagemCorpo(false, $overview->uid);
					$estrutura = self::aplicarCabecalhosThreadEstrutura($estrutura, @$overview->message_id, @$overview->references, @$overview->in_reply_to);
					$codificacao = $this->pegarCodificacao($estrutura);
					$estrutura_ = $estrutura;
					// Associa��es
					$estrutura = mysql_real_escape_string(serialize($estrutura));
					preg_match("/(<style.*>.*)(body.*{.*})(.*<\/style>)/Uism", $corpo, $matches);
					$corpo = str_replace($matches[2], '', $corpo);
					preg_match("/(<style.*>.*)(td.*{.*})(.*<\/style>)/Uism", $corpo, $matches);
					$corpo = str_replace($matches[2], '', $corpo);
					$tempoCorpo = microtime(true) - $tempoCorpoInicio;
				}

				// Inserir e-mail
				/*$overview->subject = utf8_encode($overview->subject);
				$overview->from = utf8_encode($overview->from);
				$overview->to = utf8_encode($overview->to);*/
				if(strtolower($codificacao) != 'utf-8'){
					$corpo = utf8_encode($corpo);
				}
				$tempoInsertInicio = microtime(true);
				$sql = "INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_T003_Id, T139_C007_Id, T139_Mensagem_Corpo, T139_Estrutura, T139_T005_Id, T139_D024_Id, T139_Flag_Spam) VALUES ('$this->pasta', $overview->uid, '$overview->subject', '$overview->from', '$overview->to', '$date', '$T003_Id', '{$g['usuarioAtual']}', '{$corpo}', '{$estrutura}', '$T005_Id', '$D024_Id', '{$T139_Flag_Spam}');";
				log('SQL ALEXA:'.$sql);
				$result_ = mysql_query($sql);
				$tempoInsert = microtime(true) - $tempoInsertInicio;
				$cache[$overview->uid] = true;
				/*if (!$result_) {
    				die('Invalid query: ' . mysql_error());
				}*/
				$lastId = $g['mysqlLastId'];

				// Verifica se tem .XML nos anexos
				$tempoAnexosInicio = microtime(true);
				$anexos = $this->listaAnexos($lastId);
				$temXML = 'N';
				if ($anexos) {
					foreach ($anexos as $anexo) {
						if (preg_match('/\.xml$/i', $anexo)) {
							$temXML = 'S';
						}
					}
				}
				$sql = "UPDATE T139 SET T139_Flag_Tem_Xml = '{$temXML}' WHERE T139_Id = '{$lastId}';";
				mysql_query($sql);
				$tempoAnexos = microtime(true) - $tempoAnexosInicio;

				$count++;
				$tempoAssociarInicio = microtime(true);
				$this->associarEmails($lastId, false, false);
				$tempoAssociar = microtime(true) - $tempoAssociarInicio;
				$this->registrarTempo('atualizar.email_novo_total', $tempoEmailTotal, array(
					'uid' => $overview->uid,
					'T139_Id' => $lastId,
					'corpo' => $this->tempoEmailFormatar($tempoCorpo),
					'insert' => $this->tempoEmailFormatar($tempoInsert),
					'anexos' => $this->tempoEmailFormatar($tempoAnexos),
					'associar' => $this->tempoEmailFormatar($tempoAssociar)
				));
			} else {
				$totalExistentes++;
			}
		}
		echo "fim atualizarEmail\n";
		$this->registrarTempo('atualizar.total', $tempoAtualizarTotal, array('analisados' => $totalAnalisados, 'novos' => $count, 'existentes' => $totalExistentes));
		return $count;
	}

	/**
	* Estrutura do email (cache em banco)
	*
	* @param int $T139_Id
	*/
	public function pegarEstrutura($T139_Id, $T139_Uid = false) {
		$tempoPegarEstrutura = microtime(true);
		if (!empty($T139_Id)) {
			$T139_Id = mysql_real_escape_string($T139_Id);
			$sql = "SELECT T139_Uid, T139_Estrutura FROM T139 WHERE T139_Id = {$T139_Id};";
			$resultado = mysql_query($sql);
			$row = mysql_fetch_assoc($resultado);
			if (!empty($row['T139_Estrutura'])) {
				$this->registrarTempo('pegarEstrutura.cache_banco', $tempoPegarEstrutura, array('T139_Id' => $T139_Id));
				return unserialize($row['T139_Estrutura']);
			}
		} else {
			$row = array();
			$row['T139_Uid'] = $T139_Uid;
		}
		$this->connect();
		if (!$this->mbox) { return false; }
		$body_ = imap_fetchstructure($this->mbox, $row['T139_Uid'], FT_UID);
		if (!empty($T139_Id)) {
			$body = mysql_real_escape_string(serialize($body_));
			$sql = "UPDATE T139 SET T139_Estrutura = '{$body}' WHERE T139_Id = {$T139_Id};";
			mysql_query($sql);
		}
		$this->registrarTempo('pegarEstrutura.imap_fetchstructure', $tempoPegarEstrutura, array('T139_Id' => $T139_Id, 'uid' => $row['T139_Uid']));
		return $body_;
	}

	/**
	* Recupera o Corpo da Mensagem (o texto do e-mail)
	*
	* @param int $T139_Uid
	* @return string
	*/
	public function pegarMensagemCorpo($T139_Id, $T139_Uid = false) {
		$tempoPegarMensagemCorpo = microtime(true);
		if (!empty($T139_Id)) {
			$T139_Id = mysql_real_escape_string($T139_Id);
			$sql = "SELECT T139_Uid, T139_Mensagem_Corpo FROM T139 WHERE T139_Id = {$T139_Id};";
			$retorno = mysql_query($sql);
			$row = mysql_fetch_assoc($retorno);
			if (!empty($row['T139_Mensagem_Corpo'])) {
				$this->registrarTempo('pegarMensagemCorpo.cache_banco', $tempoPegarMensagemCorpo, array('T139_Id' => $T139_Id));
				return $row['T139_Mensagem_Corpo'];
			}
		} else {
			$row = array();
			$row['T139_Uid'] = $T139_Uid;
		}
		$this->connect();
		if (!$this->mbox) { return false; }
		$estrutura = (!empty($T139_Id)) ? $this->pegarEstrutura($T139_Id) : $this->pegarEstrutura(false, $T139_Uid);
		$tipoCorpo = 'html';
		$dataHtml = $this->get_part($this->mbox, $row['T139_Uid'], "TEXT/HTML", $estrutura);
		if (empty($dataHtml)) {
			$dataHtml = $this->get_part($this->mbox, $row['T139_Uid'], "TEXT/PLAIN", $estrutura);
			$dataHtml = $this->transformTextToHtml($dataHtml);
			$dataHtml = $this->transformHtml($dataHtml);
			$tipoCorpo = 'plain';
		} else {
			$dataHtml = $this->transformHtml($dataHtml);
		}
		$dataHtml_ = mysql_real_escape_string($dataHtml);
		!empty($dataHtml_) or ($dataHtml_ = '<br />');
		if (!empty($T139_Id)) {
			$sql = "UPDATE T139 SET T139_Mensagem_Corpo = '{$dataHtml_}' WHERE T139_Id = {$T139_Id};";
			mysql_query($sql);
			$this->registrarTempo('pegarMensagemCorpo.imap_fetchbody', $tempoPegarMensagemCorpo, array('T139_Id' => $T139_Id, 'uid' => $row['T139_Uid'], 'tipo' => $tipoCorpo));
			return $dataHtml;
		} else {
			$this->registrarTempo('pegarMensagemCorpo.imap_fetchbody', $tempoPegarMensagemCorpo, array('uid' => $row['T139_Uid'], 'tipo' => $tipoCorpo));
			return array($dataHtml_, $estrutura);
		}
	}

	/**
	* Retorna um array com uma lista dos anexos deste e-mail
	*
	* @param mixed $T139_Id
	*/
	public function listaAnexos($T139_Id) {
		$struct = $this->pegarEstrutura($T139_Id);
		if (!is_object($struct) && isset($struct['anexosManual'])) {
			return $struct['anexosManual'];
		}
		$contentParts = count(@$struct->parts);
		if ($contentParts >= 1) {
			$att = array();
			for ($i=1;$i<=$contentParts-1;$i++) {
				$att[$i-1] = $struct->parts[$i];
   			}
   			$selectBoxDisplay = array();
   			for ($k=0;$k<sizeof($att);$k++) {
   				if (is_array($att[$k]->parameters) || get_class($att[$k]->parameters) != 'stdClass') {
	   				if (strtolower($att[$k]->parameters[0]->attribute) == "charset") {
	   					if (@$att[$k]->parameters[1]->value != "" && strtolower($att[$k]->disposition) != 'inline') {
	   						$selectBoxDisplay[$k] = $this->decodeMimeString($att[$k]->parameters[1]->value);
	   					}
	   				} else {
	   					if(@$att[$k]->parameters[0]->value != "" && strtolower($att[$k]->disposition) != 'inline'){
	   						$selectBoxDisplay[$k] = $this->decodeMimeString($att[$k]->parameters[0]->value);
	   					}
	   				}
	   			} else if (is_array($att[$k]->dparameters) || get_class($att[$k]->dparameters) != 'stdClass') {
	   				if (strtolower($att[$k]->dparameters[0]->attribute) == "charset") {
	   					if (@$att[$k]->dparameters[1]->value != "" && strtolower($att[$k]->disposition) != 'inline') {
	   						$selectBoxDisplay[$k] = $this->decodeMimeString($att[$k]->dparameters[1]->value);
	   					}
	   				} else {
	   					if(@$att[$k]->dparameters[0]->value != "" && strtolower($att[$k]->disposition) != 'inline'){
	   						$selectBoxDisplay[$k] = $this->decodeMimeString($att[$k]->dparameters[0]->value);
	   					}
	   				}
	   			}
   			}
		} else {
	        if (isset($struct->disposition)){
	            if (strtolower($struct->disposition) == 'attachment'){
	                $selectBoxDisplay = array();
	                if($struct->dparameters[0]->value != ""){
	                	$selectBoxDisplay[] = $this->decodeMimeString($struct->dparameters[0]->value);
	                }
	            }
	        }
	    }
		return @$selectBoxDisplay;
	}

	/**
	* Faz o download de um anexo
	* Pego em parte de: http://www.linuxscope.net/articles/mailAttachmentsPHP.html
	*
	* @param mixed $T139_Id
	* @param mixed $file			// nr do anexo na estrutura
	*/
	public function downloadAnexo($T139_Id, $file, $return = false, $display = false) {
		$this->connect();
		if (!$this->mbox) { return false; }

		$struct = $this->pegarEstrutura($T139_Id);
		$contentParts = count($struct->parts);
		if ($contentParts >= 1) {
			for ($i=1;$i<=$contentParts-1;$i++) {
				//$att[$i-2] = imap_bodystruct($this->mbox, $this->getUid($T139_Id), $i);
				$att[$i-1] = $struct->parts[$i];
   			}
   			for ($k=0;$k<sizeof($att);$k++) {

   				if (is_array($att[$k]->parameters) || get_class($att[$k]->parameters) != 'stdClass') {
	   				if (strtolower($att[$k]->parameters[0]->attribute) == "charset") {
	   					if (@$att[$k]->parameters[1]->value != "") {
	   						$selectBoxDisplay[$k] = $this->decodeMimeString($att[$k]->parameters[1]->value);
	   					}
	   				} else {
	   					$selectBoxDisplay[$k] = $this->decodeMimeString($att[$k]->parameters[0]->value);
	   				}
	   			} else if (is_array($att[$k]->dparameters) || get_class($att[$k]->dparameters) != 'stdClass') {
	   				if (strtolower($att[$k]->dparameters[0]->attribute) == "charset") {
	   					if (@$att[$k]->dparameters[1]->value != "") {
	   						$selectBoxDisplay[$k] = $this->decodeMimeString($att[$k]->dparameters[1]->value);
	   					}
	   				} else {
	   					$selectBoxDisplay[$k] = $this->decodeMimeString($att[$k]->dparameters[0]->value);
	   				}
	   			}

   			}
   			$strFileName = $selectBoxDisplay[$file];
   			$file = $file + 2;
		} else {
	        if (isset($struct->disposition)){ 
	            if (strtolower($struct->disposition) == 'attachment'){ 
	                $selectBoxDisplay = array();
	                $selectBoxDisplay[] = $this->decodeMimeString($struct->dparameters[0]->value); 
	            }
	        }
		    $strFileName = $selectBoxDisplay[$file];
		    $file = '1';
	    }
   		$strFileType = strtolower(strrev(substr(strrev($strFileName),0,4)));
   		$fileContent = imap_fetchbody($this->mbox, $this->getUid($T139_Id), $file, FT_UID ^ FT_PEEK);

   		$ContentType = "application/octet-stream";
   		if ($strFileType == ".asf") 
   			$ContentType = "video/x-ms-asf";
   		if ($strFileType == ".avi")
   			$ContentType = "video/avi";
   		if ($strFileType == ".doc")
   			$ContentType = "application/msword";
   		if ($strFileType == ".zip")
   			$ContentType = "application/zip";
   		if ($strFileType == ".xls")
   			$ContentType = "application/vnd.ms-excel";
   		if ($strFileType == ".gif")
   			$ContentType = "image/gif";
   		if ($strFileType == ".jpg" || $strFileType == "jpeg")
   			$ContentType = "image/jpeg";
   		if ($strFileType == ".png")
   			$ContentType = "image/png";
   		if ($strFileType == ".wav")
   			$ContentType = "audio/wav";
   		if ($strFileType == ".mp3")
   			$ContentType = "audio/mpeg3";
   		if ($strFileType == ".mpg" || $strFileType == "mpeg")
   			$ContentType = "video/mpeg";
   		if ($strFileType == ".rtf")
   			$ContentType = "application/rtf";
   		if ($strFileType == ".htm" || $strFileType == "html")
   			$ContentType = "text/html";
   		if ($strFileType == ".xml") 
   			$ContentType = "text/xml";
   		if ($strFileType == ".xsl") 
   			$ContentType = "text/xsl";
   		if ($strFileType == ".css") 
   			$ContentType = "text/css";
   		if ($strFileType == ".php") 
   			$ContentType = "text/php";
   		if ($strFileType == ".asp") 
   			$ContentType = "text/asp";
   		if ($strFileType == ".pdf")
   			$ContentType = "application/pdf";
   		if ($strFileType == ".txt")
   			$ContentType = "text/plain";

   		if (!$return) {
			header ("Content-Type: $ContentType");
			header ("Content-Disposition: attachment; filename=$strFileName;"); // size=$fileSize;
		}
		if ($display) {
			header ("Content-Type: $ContentType");
			header ("Content-Disposition: inline; filename=$strFileName;"); // size=$fileSize;
		}
		// Updated oktober 29. 2005
		if (substr($ContentType,0,4) == "text" && $ContentType != 'text/xml' && $ContentType != 'text/plain') {
			if ($return) {
				return imap_qprint($fileContent);
			} else {
				echo imap_qprint($fileContent);
			}
		} else {
			$temp = imap_base64($fileContent);
			if (empty($temp)) {
				$temp = imap_qprint($fileContent);
			}
			if ($return) {
				return $temp;
			} else {
				echo $temp;
			}
		}
	}

	// Retorna o UID de um Email
	public function getUid($T139_Id) {
		$T139_Id = mysql_real_escape_string($T139_Id);
		$sql = "SELECT T139_Uid FROM T139 WHERE T139_Id = {$T139_Id};";
		$retorno = mysql_query($sql);
		$row = mysql_fetch_assoc($retorno);
		return $row['T139_Uid'];
	}

	/**
	* Recupera o Corpo da Mensagem (o texto do e-mail)
	*
	* @param int $T139_Uid
	* @return string
	*/
	public function pegarMensagemCabecalho($T139_Id) {
		$T139_Id = mysql_real_escape_string($T139_Id);
		$sql = "SELECT T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_Cc, T139_Cco FROM T139 WHERE T139_Id = {$T139_Id};";
		$retorno = mysql_query($sql);
		$row = mysql_fetch_assoc($retorno);
		return $row;
	}

	public function pegarCamposEmail($T139_Id) {
		$T139_Id = mysql_real_escape_string($T139_Id);
		$sql = "SELECT * FROM T139 WHERE T139_Id = {$T139_Id};";
		$retorno = mysql_query($sql);
		$row = mysql_fetch_assoc($retorno);
		return $row;
	}

	public function excluirEmail($T139_Id) {
		$T139_Id = mysql_real_escape_string($T139_Id);
		$sql = <<<EOT
UPDATE
T139
LEFT JOIN C007 ON C007_Id=T139_C007_Id
LEFT JOIN C019 ON C019_C007_Id = C007_Id
LEFT JOIN C021 ON C021_Id = C019_C021_Id
SET T139_Flag_Excluir = '1' WHERE {$extra}
EOT;
		mysql_query($sql);
		return true;
	}

	public function excluirMarcados($extra) {
		global $g;
		$extra = gPegarParteWhere(gInsertExtraWhere($extra, "(T139_C007_Id = '{$g['usuarioAtual']}' AND T139_Flag_Selecionado = 1 AND T139_Flag_Excluir != 1)"));
		$sql = "UPDATE T139 LEFT JOIN D085 ON D085_T139_Id=T139_Id OR IF(IFNULL(T139_T003_Id,0)>0 OR IFNULL(T139_T005_Id,0)>0, D085_T003_Id=T139_T003_Id OR D085_T005_Id=T139_T005_Id,'') SET T139_Flag_Excluir = '1' WHERE {$extra};";
		mysqli_query($sql);
		return mysql_affected_rows();
	}

	public function excluirSpamsMarcados($extra) {
		global $g;
		$extra = gPegarParteWhere(gInsertExtraWhere($extra, "(T139_C007_Id = '{$g['usuarioAtual']}' AND T139_Flag_Selecionado = 1 AND T139_Flag_Excluir != 1 AND T139_Flag_Spam = 'S')"));
		$res = mysql_query("SELECT T139_Id FROM T139 LEFT JOIN D085 ON D085_T139_Id=T139_Id OR IF(IFNULL(T139_T003_Id,0)>0 OR IFNULL(T139_T005_Id,0)>0, D085_T003_Id=T139_T003_Id OR D085_T005_Id=T139_T005_Id,'') WHERE {$extra}");
		$count = mysql_num_rows($res);
		while ($row = mysql_fetch_assoc($res)) {
			$arquivo = $dirAnexos . $row['T139_Id'] . '-0';
			if (file_exists($arquivo)) {
				shell_exec("rm -f {$dirAnexos}{$row['T139_Id']}-*");
				echo $arquivo . "\n";
			}
			mysql_query("DELETE FROM T139 WHERE T139_Id = '{$row['T139_Id']}'");
			//Remove o registro na agenda (D085) e verifica se a tarefa (T259) que est� inserido possui outros registros (D085), caso n�o tenha, removemos a tarefa
			$sqlD085 = mysql_query("SELECT D085_T259_Id FROM D085 WHERE D085_T139_Id = '{$row['T139_Id']}'");
			$rowD085 = mysql_fetch_assoc($sqlD085);
			mysql_query("DELETE FROM D085 WHERE D085_T139_Id = '{$row['T139_Id']}'");
			$sqlT259 = mysql_query("SELECT D085_Id FROM D085 WHERE D085_T259_Id = '{$rowD085['D085_T259_Id']}'");
			if(mysql_num_rows($sqlT259) <= 0){
				mysql_query("DELETE FROM T259 WHERE T259_Id = '{$rowD085['D085_T259_Id']}'");
			}
		}
		return $count;
	}

	public function excluirDesmarcados($extra) {
		global $g;
		$extra = gPegarParteWhere(gInsertExtraWhere($extra, "(T139_C007_Id = '{$g['usuarioAtual']}' AND ifnull(T139_Flag_Selecionado,0) = '0' AND T139_Flag_Excluir != '1')"));
		$sql = "UPDATE T139 SET T139_Flag_Excluir = '1' WHERE {$extra};";
		mysqli_query($sql);
		return mysql_affected_rows();
	}

	public function setarLido($T139_Id) {
		$T139_Id = mysql_real_escape_string($T139_Id);
		$sql = "UPDATE T139 SET T139_Flag_Lido = '1' WHERE T139_Id = '{$T139_Id}';";
		mysql_query($sql);
		return true;
	}

	public function setarSelecionado($T139_Id, $selecionado = 1) {
		$T139_Id = mysql_real_escape_string($T139_Id);
		$sql = "UPDATE T139 SET T139_Flag_Selecionado = '{$selecionado}' WHERE T139_Id = '{$T139_Id}';";
		mysql_query($sql);
		return true;
	}

	/**
	* Faz a associa��o de todos os e-mails ainda n�o associados
	*/
	public function associarEmails($T139_Id = false, $select = false, $registrarD085 = true) {
		global $g;
		$tempoAssociarEmailsTotal = microtime(true);
		$totalEmailsAssociados = 0;
		$retorno_ = '';
		if (!empty($T139_Id)) {
			$sql = "SELECT T139_Id, T139_D024_Id, T139_Pasta, T139_De, T139_Para, T139_D013_Id, T139_C007_Id, IFNULL(T139_T003_Id,0) T139_T003_Id, IFNULL(T139_T005_Id,0) T139_T005_Id, T139_Titulo, T139_Data_Hora FROM T139 WHERE T139_Id = '{$T139_Id}';";
		} else if(!empty($select)){
			$sql = str_replace("SELECT", "SELECT *,", base64_decode($select));
		} else {
			$sql = "SELECT T139_Id, T139_D024_Id, T139_Pasta, T139_De, T139_Para, T139_D013_Id, T139_C007_Id, IFNULL(T139_T003_Id,0) T139_T003_Id, IFNULL(T139_T005_Id,0) T139_T005_Id, T139_Titulo, T139_Data_Hora FROM T139;";
		}
		$retorno = mysql_query($sql);
		while ($row = mysql_fetch_assoc($retorno)) {
			$tempoAssociarEmail = microtime(true);
			$totalEmailsAssociados++;
			$spam = true;
			if ($row['T139_Pasta'] == 'E') {
				$email = $row['T139_De'];
			} else {
				$email = $row['T139_Para'];
			}
			if (strpos($email, '<') !== false) {
				$email = explode('<', $email);
				$email = explode('>', $email[1]);
				$email = $email[0];
			}
			$email = mysql_real_escape_string($email);

			preg_match("/amento.*\[([0-9]+)\]/Uis", $row['T139_Titulo'], $orcamento);
			$T003_Id = ($row['T139_T003_Id'] > 0) ? $row['T139_T003_Id'] : 'null';
			if (isset($orcamento[1]) && !empty($orcamento[1]) && $T003_Id == 'null') {
				$sql = mysql_query("SELECT T003_Id FROM T003 WHERE T003_Id = '{$orcamento[1]}'");
				if(mysql_num_rows($sql) > 0){
					$T003_Id = $orcamento[1];
				}
			}
			preg_match("/edido.*\[([0-9]+)\]/Uis", $row['T139_Titulo'], $pedido);
			$T005_Id = ($row['T139_T005_Id'] > 0) ? $row['T139_T005_Id'] : 'null';
			if (isset($pedido[1]) && !empty($pedido[1]) && $T005_Id == 'null') {
				$sql = mysql_query("SELECT T005_Id FROM T005 WHERE T005_Id = '{$pedido[1]}'");
				if(mysql_num_rows($sql) > 0){
					$T005_Id = $pedido[1];
				}
			}

			$D085_T259_Id = 0;
			if((is_numeric($T003_Id) && $T003_Id > 0) || (is_numeric($T005_Id) && $T005_Id > 0)){
				$sqlD085 = mysql_query("SELECT D085_T259_Id FROM D085 WHERE ((D085_T003_Id = '{$T003_Id}' AND D085_Entidade = 'T003') OR ( D085_T005_Id = '{$T005_Id}' AND D085_Entidade = 'T005')) AND IFNULL(D085_T259_Id,0) > 0");
				if(mysql_num_rows($sql) > 0){
					$rowD085 = mysql_fetch_assoc($sqlD085);
					$D085_T259_Id = $rowD085['D085_T259_Id'];
				}
			}
			/*
			// Cliente/Fornecedor
			if (empty($row['T139_D024_Id'])) {
				// T139_D024_Id
				$sql = "SELECT D024_Id FROM D024 WHERE (D024_E_Mail like '%{$email}%' OR D024_Observacoes like '%{$email}%') LIMIT 1;";
				$resultado = mysql_query($sql);
				$rowD024 = mysql_fetch_assoc($resultado);
				$T139_D024_Id = false;
				if (!empty($rowD024)) {
					$T139_D024_Id = $rowD024['D024_Id'];
					$sql = "UPDATE T139 SET T139_D024_Id = '{$T139_D024_Id}', T139_Flag_Spam = 'N' WHERE T139_Id = '{$row['T139_Id']}';";
					mysql_query($sql);
					//Insere registro na agenda
					if($spam){
						$sql = "SELECT D013_Id FROM D013 WHERE D013_E_Mail like '%{$email}%' LIMIT 1;";
						$resultado = mysql_query($sql);
						$rowD013 = mysql_fetch_assoc($resultado);
						if (!empty($rowD013)) {
							$T139_D013_Id = $rowD013['D013_Id'];
						}
						if ($row['T139_Pasta'] == 'E') {
							$data = date('Y-m-d');
							$hora = date('H:i:s');
							$sql = "INSERT INTO D085 (D085_Data, D085_Hora, D085_C007_Id, D085_D024_Id, D085_D013_Id, D085_T139_Id, D085_Flag_Tipo, D085_Mensagem, D085_T003_Id, D085_T005_Id, D085_Flag_Manual, D085_T259_Id) VALUES ('{$data}', '{$hora}', '{$row['T139_C007_Id']}', '{$T139_D024_Id}', '{$T139_D013_Id}', '{$row['T139_Id']}', '2', '{$row['T139_Titulo']}', $T003_Id, $T005_Id, 'N', '{$D085_T259_Id}')";
							mysql_query($sql);
							$spam = false;
						}
					}
				}
				if (!$T139_D024_Id) {
					$sql = "SELECT D013_Id, D013_D024_Id FROM D013 WHERE D013_E_Mail like '%{$email}%' LIMIT 1;";
					$resultado = mysql_query($sql);
					$rowD013 = mysql_fetch_assoc($resultado);
					if (!empty($rowD013) && !empty($rowD013['D013_D024_Id'])) {
						$T139_D024_Id = $rowD013['D013_D024_Id'];
						$T139_D013_Id = $rowD013['D013_Id'];
						$sql = "UPDATE T139 SET T139_D024_Id = '{$T139_D024_Id}', T139_D013_Id = '{$T139_D013_Id}', T139_Flag_Spam = 'N' WHERE T139_Id = '{$row['T139_Id']}';";
						mysql_query($sql);

						//Insere registro na agenda
						if($spam && $row['T139_Pasta'] == 'E'){
							$data = date('Y-m-d');
							$hora = date('H:i:s');
							$sql = "INSERT INTO D085 (D085_Data, D085_Hora, D085_C007_Id, D085_D024_Id, D085_D013_Id, D085_T139_Id, D085_Flag_Tipo, D085_Mensagem, D085_T003_Id, D085_T005_Id, D085_Flag_Manual, D085_T259_Id) VALUES ('{$data}', '{$hora}', '{$row['T139_C007_Id']}', '{$T139_D024_Id}', '{$T139_D013_Id}', '{$row['T139_Id']}', '2', '{$row['T139_Titulo']}', $T003_Id, $T005_Id, 'N', '{$D085_T259_Id}')";
							mysql_query($sql);
							$spam = false;
						}
					}
				}
			}*/
			//Verifica se j� existe um registro na agenda
			$sqlD085 = mysql_query("SELECT * FROM D085 WHERE D085_T139_Id = '{$row['T139_Id']}'");
			if(mysql_num_rows($sqlD085) > 0){
				$registrarD085 = false;
			}
			/*
			// Contato
			// T139_D013_Id
			$sql = "SELECT D013_Id, D013_D024_Id FROM D013 WHERE D013_E_Mail like '%$email%' LIMIT 1;";
			$resultado = mysql_query($sql);
			$rowD013 = mysql_fetch_assoc($resultado);
			$T139_D013_Id = false;
			if (!empty($rowD013)) {
				$retorno_ = $rowD013['D013_Id'];
				if (empty($row['T139_D013_Id'])) {
					$T139_D013_Id = $rowD013['D013_Id'];
					$T139_D024_Id = (empty($row['T139_D024_Id'])) ? $rowD013['D013_D024_Id'] : $row['T139_D024_Id'];
					$sql = "UPDATE T139 SET T139_D013_Id = '{$T139_D013_Id}', T139_D024_Id = '{$T139_D024_Id}', T139_Flag_Spam = 'N' WHERE T139_Id = '{$row['T139_Id']}';";
					mysql_query($sql);

					//Insere registro na agenda
					if($spam && $row['T139_Pasta'] == 'E' && $registrarD085){
						$data = date('Y-m-d');
						$hora = date('H:i:s');
						$sql = "INSERT INTO D085 (D085_Data, D085_Hora, D085_C007_Id, D085_D024_Id, D085_D013_Id, D085_T139_Id, D085_Flag_Tipo, D085_Mensagem, D085_T003_Id, D085_T005_Id, D085_Flag_Manual, D085_T259_Id) VALUES ('{$data}', '{$hora}', '{$row['T139_C007_Id']}', '{$T139_D024_Id}', '{$T139_D013_Id}', '{$row['T139_Id']}', '2', '{$row['T139_Titulo']}', $T003_Id, $T005_Id, 'N', '{$D085_T259_Id}')";
						mysql_query($sql);
						$spam = false;
					}
				}
			}
			*/
			//Removendo do spam os emails que foram associados a um cliente atrav�s do or�amento ou pedido
			//Este caso pode ocorrer quando � recebido um e-mail de transportadora por exemplo, e o assunto do e-mail vem com refer�ncia do or�amento
			if(!empty($row['T139_D024_Id']) && $registrarD085){
				$T139_D024_Id = (empty($T139_D024_Id)) ? $row['T139_D024_Id'] : $T139_D024_Id;
				mysql_query("UPDATE T139 SET T139_Flag_Spam = 'N' WHERE T139_Id = '{$row['T139_Id']}'");

				$sqlD085 = mysql_query("SELECT * FROM D085 WHERE D085_T139_Id = '{$row['T139_Id']}'");
				if(mysql_num_rows($sqlD085) <= 0){
					if ($row['T139_Pasta'] == 'E') {
						$data = date('Y-m-d');
						$hora = date('H:i:s');
						$sql = "INSERT INTO D085 (D085_Data, D085_Hora, D085_C007_Id, D085_D024_Id, D085_D013_Id, D085_T139_Id, D085_Flag_Tipo, D085_Mensagem, D085_T003_Id, D085_T005_Id, D085_Flag_Manual, D085_T259_Id) VALUES ('{$data}', '{$hora}', '{$row['T139_C007_Id']}', '{$T139_D024_Id}', '', '{$row['T139_Id']}', '2', '{$row['T139_Titulo']}', $T003_Id, $T005_Id, 'N', '{$D085_T259_Id}')";
						mysql_query($sql);
						if($T139_Id !== false){
							$retorno_ = $g['mysqlLastId'];
						}
						$spam = false;
					}
				}
			}

			// Caso o email recebido n�o seja identificado, verificamos na whitelist
			if($spam && $row['T139_Pasta'] == 'E'){
				//Se retornou true, o email foi encontrado na whitelist
				$tempoWhitelist = microtime(true);
				$encontradoWhitelist = ($this->verificarWhitelist($row, $registrarD085) === true);
				$this->registrarTempo('associarEmails.verificarWhitelist', $tempoWhitelist, array('T139_Id' => $row['T139_Id'], 'resultado' => $encontradoWhitelist));
			}
			$this->registrarTempo('associarEmails.email_total', $tempoAssociarEmail, array('T139_Id' => $row['T139_Id'], 'spam' => $spam));
		}
		$this->registrarTempo('associarEmails.total', $tempoAssociarEmailsTotal, array('emails' => $totalEmailsAssociados, 'T139_Id' => $T139_Id));
		return $retorno_;
	}

	/**
	*	Verifica se o email ou dominio est� na whitelist para n�o enviar para SPAM
	*/
	public function verificarWhitelist($rowT139, $registrarD085 = true)
	{
		$email = $rowT139['T139_De'];
		if (strpos($email, '<') !== false) {
			$email = explode('<', $email);
			$email = explode('>', $email[1]);
			$email = strtolower($email[0]);
		}
		$data = date('Y-m-d');
		$hora = date('H:i:s');

		preg_match("/amento.*\[([0-9]+)\]/Uis", $rowT139['T139_Titulo'], $orcamento);
		$T003_Id = 'null';
		if (isset($orcamento[1]) && !empty($orcamento[1])) {
			$sql = mysql_query("SELECT T003_Id FROM T003 WHERE T003_Id = '{$orcamento[1]}'");
			if(mysql_num_rows($sql) > 0){
				$T003_Id = $orcamento[1];
			}
		}
		preg_match("/edido.*\[([0-9]+)\]/Uis", $rowT139['T139_Titulo'], $pedido);
		$T005_Id = 'null';
		if (isset($pedido[1]) && !empty($pedido[1])) {
			$sql = mysql_query("SELECT T005_Id FROM T005 WHERE T005_Id = '{$pedido[1]}'");
			if(mysql_num_rows($sql) > 0){
				$T005_Id = $pedido[1];
			}
		}

		$D085_T259_Id = 0;
		if((is_numeric($T003_Id) && $T003_Id > 0) || (is_numeric($T005_Id) && $T005_Id > 0)){
			$sqlD085 = mysql_query("SELECT D085_T259_Id FROM D085 WHERE (D085_T003_Id = '{$T003_Id}' OR D085_T005_Id = '{$T005_Id}') AND IFNULL(D085_T259_Id,0) > 0");
			if(mysql_num_rows($sql) > 0){
				$rowD085 = mysql_fetch_assoc($sqlD085);
				$D085_T259_Id = $rowD085['D085_T259_Id'];
			}
		}

		//Verifica o email na whitelist
		$sqlT258 = mysql_query("SELECT * FROM T258 WHERE T258_Registro = '{$email}'");
		if(mysql_num_rows($sqlT258) > 0){
			$rowT258 = mysql_fetch_array($sqlT258);
			$sql = "UPDATE T139 SET T139_Flag_Spam = 'N', T139_D024_Id = '{$rowT258['T258_D024_Id']}' WHERE T139_Id = '{$rowT139['T139_Id']}';";
			mysql_query($sql);

			$D013_Id = $rowT258['T258_D013_Id'];
			$sql = "SELECT D013_Id FROM D013 WHERE D013_E_Mail like '%{$email}%' LIMIT 1;";
			$resultado = mysql_query($sql);
			$rowD013 = mysql_fetch_assoc($resultado);
			if (!empty($rowD013) && empty($D013_Id)) {
				$D013_Id = $rowD013['D013_Id'];
			}

			if($registrarD085){
				//Insere registro na agenda
				$sql = "INSERT INTO D085 (D085_Data, D085_Hora, D085_C007_Id, D085_D024_Id, D085_D013_Id, D085_T139_Id, D085_Flag_Tipo, D085_Mensagem, D085_T003_Id, D085_T005_Id, D085_Flag_Manual, D085_T259_Id, D085_T258_Id) VALUES ('{$data}', '{$hora}', '{$rowT139['T139_C007_Id']}', '{$rowT258['T258_D024_Id']}', '{$D013_Id}', '{$rowT139['T139_Id']}', '2', '{$rowT139['T139_Titulo']}', $T003_Id, $T005_Id, 'N', '{$D085_T259_Id}', '{$rowT258['T258_Id']}')";
				mysql_query($sql);
			}
			return true;
		}

		$dominio = explode("@", $email);
        $dominio = strtolower($dominio[1]);

        //Verifica o dominio na whitelist
        $sqlT258 = mysql_query("SELECT * FROM T258 WHERE T258_Registro = '{$dominio}'");
		if(mysql_num_rows($sqlT258) > 0){
			$rowT258 = mysql_fetch_array($sqlT258);
			$sql = "UPDATE T139 SET T139_Flag_Spam = 'N', T139_D024_Id = '{$rowT258['T258_D024_Id']}' WHERE T139_Id = '{$rowT139['T139_Id']}';";
			mysql_query($sql);

			$D013_Id = $rowT258['T258_D013_Id'];
			$sql = "SELECT D013_Id FROM D013 WHERE D013_E_Mail like '%{$email}%' LIMIT 1;";
			$resultado = mysql_query($sql);
			$rowD013 = mysql_fetch_assoc($resultado);
			if (!empty($rowD013) && empty($D013_Id)) {
				$D013_Id = $rowD013['D013_Id'];
			}

			if($registrarD085){
				//Insere registro na agenda
				$sql = "INSERT INTO D085 (D085_Data, D085_Hora, D085_C007_Id, D085_D024_Id, D085_D013_Id, D085_T139_Id, D085_Flag_Tipo, D085_Mensagem, D085_T003_Id, D085_T005_Id, D085_Flag_Manual, D085_T259_Id, D085_T258_Id) VALUES ('{$data}', '{$hora}', '{$rowT139['T139_C007_Id']}', '{$rowT258['T258_D024_Id']}', '{$D013_Id}', '{$rowT139['T139_Id']}', '2', '{$rowT139['T139_Titulo']}', $T003_Id, $T005_Id, 'N', '{$D085_T259_Id}', '{$rowT258['T258_Id']}')";
				mysql_query($sql);
			}
			return true;
		}
		if($rowT139['T139_D024_Id'] > 0){
			$sqlT258 = mysql_query("SELECT * FROM T258 WHERE T258_D024_Id = '{$rowT139['T139_D024_Id']}'");
			if(mysql_num_rows($sqlT258) > 0){
				//Remove de spam
				$sql = "UPDATE T139 SET T139_Flag_Spam = 'N' WHERE T139_Id = '{$rowT139['T139_Id']}';";
				mysql_query($sql);
				if($registrarD085){
					//Insere registro na agenda
					$sql = "INSERT INTO D085 (D085_Data, D085_Hora, D085_C007_Id, D085_D024_Id, D085_D013_Id, D085_T139_Id, D085_Flag_Tipo, D085_Mensagem, D085_T003_Id, D085_T005_Id, D085_Flag_Manual, D085_T259_Id) VALUES ('{$data}', '{$hora}', '{$rowT139['T139_C007_Id']}', '{$rowT139['T139_D024_Id']}', '{$D013_Id}', '{$rowT139['T139_Id']}', '2', '{$rowT139['T139_Titulo']}', $T003_Id, $T005_Id, 'N', '{$D085_T259_Id}')";
					mysql_query($sql);
				}
				return true;
			}
		}
		return false;
	}

	/*
	* Processa as imagens do corpo do email
	*/
	public function processarImagens($T139_Id, $gravarCorpo = false)
	{
		global $g;
		$tempoProcessarImagens = microtime(true);
		$this->connect();
		if (!$this->mbox) { return false; }
		$estrutura = $this->pegarEstrutura($T139_Id);
		$row = $this->pegarCamposEmail($T139_Id);
		$codificacao = $this->pegarCodificacao($estrutura);
		$corpo = addslashes($row['T139_Mensagem_Corpo']);
		if(strtolower($codificacao) != 'utf-8'){
			$corpo = utf8_decode($corpo);
		}
		$imagesFormats = array('png', 'gif', 'jpeg', 'jpg', 'bmp');
		$corpoModificado = false;
		$mod = '';
		$totalImagensInline = 0;
		$totalImagensGravadas = 0;

		//Se o subtype do primeiro objeto for RELATED j� temos a estrutura que lista as imagens, caso contrario precisamos navegar at� o objeto que o subtype � RELATED correto
		//as imagens s�o listadas no array PARTS do objeto, o atributo disposition deve ser 'inline'
		if($estrutura->subtype != 'RELATED'){
			list($mod, $estrutura) = $this->verificaAnexosInline($estrutura);
		}
		if($estrutura->subtype == 'RELATED'){
			foreach ($estrutura->parts as $key => $part) {
				$srcFile = trim(trim($part->id,'<'),'>');
				if(!is_array($part->parameters)){
					if(in_array(strtolower($part->subtype), $imagesFormats)){
						$strFileType = strtolower($part->subtype);
						$strFileType = ($strFileType == 'jpeg') ? '.jpg' : '.'.$strFileType;
					}
				} else {
					$strFileType = strtolower(strrev(substr(strrev($part->parameters[0]->value),0,4)));
					if(!in_array(substr($strFileType, 0, 3), $imagesFormats)){
						if(in_array(strtolower($part->subtype), $imagesFormats)){
							$strFileType = strtolower($part->subtype);
							$strFileType = ($strFileType == 'jpeg') ? '.jpg' : '.'.$strFileType;
						}
					}
				}
				if($srcFile != '' && (strtolower($part->disposition) == 'inline' || strpos($corpo, 'src=\"cid:'.$srcFile.'\"') !== false || strpos($corpo, 'src=cid:'.$srcFile) !== false || strpos($corpo, 'src="cid:'.$srcFile.'"') !== false)){
					$totalImagensInline++;
					$key = $key+1;
					$decode = imap_fetchbody($this->mbox, $this->getUid($T139_Id), "{$mod}{$key}", FT_UID ^ FT_PEEK);
					$decode = base64_decode($decode);
					if(strlen($decode) > 0){
						$totalImagensGravadas++;
						$corpoModificado = true;
						if(!$gravarCorpo){
							$dir = $g['pathDados'] . "arquivoEmail/tmp/";
						} else {
							$dir = $g['pathDados'] . "arquivoEmail/{$T139_Id}/";
						}
						is_dir($dir) or mkdir($dir, 0777, true);
						$file = $dir.$srcFile.$strFileType;
						file_put_contents($file, $decode);
						$corpo = str_replace('src=cid:'.$srcFile, 'src=\/hardness3\/'.$file, $corpo);
						$corpo = str_replace('src="cid:'.$srcFile.'"', 'src="\/hardness3\/'.$file.'"', $corpo);
						$corpo = str_replace('src=\"cid:'.$srcFile.'\"', 'src=\"\/hardness3\/'.$file.'\"', $corpo);
					}
				}
			}
		}
		if(strtolower($codificacao) != 'utf-8'){
			$corpo = utf8_encode($corpo);
		}
		if($corpoModificado && $gravarCorpo){
			mysql_query("UPDATE T139 SET T139_Mensagem_Corpo = '{$corpo}' WHERE T139_Id = '{$T139_Id}'");
		}
		$this->registrarTempo('processarImagens.total', $tempoProcessarImagens, array('T139_Id' => $T139_Id, 'inline' => $totalImagensInline, 'gravadas' => $totalImagensGravadas, 'corpo_modificado' => $corpoModificado));
		if(!$gravarCorpo){
			return $corpo;
		}
	}

	public function sincronizarCabecalhosThread($T139_Id) {
		$T139_Id = mysql_real_escape_string($T139_Id);
		$sql = "SELECT T139_Uid, T139_Estrutura FROM T139 WHERE T139_Id = {$T139_Id};";
		$retorno = mysql_query($sql);
		$row = mysql_fetch_assoc($retorno);
		$cabecalhosThread = self::pegarCabecalhosThreadEstruturaSerializada($row['T139_Estrutura']);

		if (!empty($cabecalhosThread['message_id']) || !empty($cabecalhosThread['in_reply_to']) || empty($row['T139_Uid'])) {
			return $cabecalhosThread;
		}

		$this->connect();
		if (!$this->mbox) {
			return $cabecalhosThread;
		}

		$overview = imap_fetch_overview($this->mbox, $row['T139_Uid'], FT_UID);
		if (!isset($overview[0])) {
			return $cabecalhosThread;
		}

		$estrutura = $this->pegarEstrutura($T139_Id);
		$estrutura = self::aplicarCabecalhosThreadEstrutura($estrutura, @$overview[0]->message_id, @$overview[0]->references, @$overview[0]->in_reply_to);
		$estruturaSql = mysql_real_escape_string(serialize($estrutura));
		mysql_query("UPDATE T139 SET T139_Estrutura = '{$estruturaSql}' WHERE T139_Id = {$T139_Id};");

		return self::pegarCabecalhosThreadEstrutura($estrutura);
	}

	public static function normalizarMessageId($messageId) {
		$messageId = trim((string) $messageId);
		if (empty($messageId)) {
			return '';
		}
		$messageId = preg_replace('/[\r\n\t]+/', '', $messageId);
		if (preg_match('/<([^>]+)>/', $messageId, $matches)) {
			$messageId = $matches[1];
		}
		$messageId = trim($messageId, " <>\t\n\r\0\x0B\"'");
		if (empty($messageId)) {
			return '';
		}
		return "<{$messageId}>";
	}

	public static function normalizarListaMessageIds($messageIds) {
		$messageIds = trim((string) $messageIds);
		if (empty($messageIds)) {
			return '';
		}

		$lista = array();
		if (preg_match_all('/<[^>]+>/', $messageIds, $matches) && !empty($matches[0])) {
			$lista = $matches[0];
		} else {
			$lista = preg_split('/[\s,;]+/', $messageIds, -1, PREG_SPLIT_NO_EMPTY);
		}

		$retorno = array();
		foreach ($lista as $messageId) {
			$messageId = self::normalizarMessageId($messageId);
			if (!empty($messageId) && !in_array($messageId, $retorno)) {
				$retorno[] = $messageId;
			}
		}

		return implode(' ', $retorno);
	}

	public static function montarCabecalhoReferences($references, $messageId = '') {
		$lista = self::normalizarListaMessageIds($references);
		$retorno = empty($lista) ? array() : explode(' ', $lista);
		$messageId = self::normalizarMessageId($messageId);
		if (!empty($messageId) && !in_array($messageId, $retorno)) {
			$retorno[] = $messageId;
		}
		return implode(' ', $retorno);
	}

	public static function gerarMessageId($emailRemetente = '') {
		$dominio = 'localhost.localdomain';
		if (preg_match('/@([^>\s]+)/', $emailRemetente, $matches)) {
			$dominio = strtolower(trim($matches[1]));
		} else if (!empty($_SERVER['SERVER_NAME'])) {
			$dominio = strtolower(trim($_SERVER['SERVER_NAME']));
		}
		$dominio = preg_replace('/[^a-z0-9\.\-]/i', '', $dominio);
		if (empty($dominio)) {
			$dominio = 'localhost.localdomain';
		}
		$uniqid = str_replace('.', '', uniqid('', true));
		return "<{$uniqid}@{$dominio}>";
	}

	public static function aplicarCabecalhosThreadEstrutura($estrutura, $messageId = '', $references = '', $inReplyTo = '') {
		$cabecalhosThread = array(
			'message_id' => self::normalizarMessageId($messageId),
			'references' => self::normalizarListaMessageIds($references),
			'in_reply_to' => self::normalizarMessageId($inReplyTo)
		);

		if (is_object($estrutura)) {
			$estrutura->hardnessThreadHeaders = $cabecalhosThread;
			return $estrutura;
		}

		if (!is_array($estrutura)) {
			$estrutura = array();
		}

		$estrutura['threadHeaders'] = $cabecalhosThread;
		return $estrutura;
	}

	public static function pegarCabecalhosThreadEstruturaSerializada($estruturaSerializada) {
		if (empty($estruturaSerializada)) {
			return array(
				'message_id' => '',
				'references' => '',
				'in_reply_to' => ''
			);
		}

		$estrutura = @unserialize($estruturaSerializada);
		return self::pegarCabecalhosThreadEstrutura($estrutura);
	}

	public static function pegarCabecalhosThreadEstrutura($estrutura) {
		$retorno = array(
			'message_id' => '',
			'references' => '',
			'in_reply_to' => ''
		);

		if (is_object($estrutura) && isset($estrutura->hardnessThreadHeaders) && is_array($estrutura->hardnessThreadHeaders)) {
			$retorno = array_merge($retorno, $estrutura->hardnessThreadHeaders);
		} else if (is_array($estrutura) && isset($estrutura['threadHeaders']) && is_array($estrutura['threadHeaders'])) {
			$retorno = array_merge($retorno, $estrutura['threadHeaders']);
		}

		$retorno['message_id'] = self::normalizarMessageId($retorno['message_id']);
		$retorno['references'] = self::normalizarListaMessageIds($retorno['references']);
		$retorno['in_reply_to'] = self::normalizarMessageId($retorno['in_reply_to']);

		return $retorno;
	}

	/**
	* Fun��es Internas
	*/

	// http://www.linuxscope.net/articles/mailAttachmentsPHP.html
	private function get_mime_type(&$structure) {
		$primary_mime_type = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", "AUDIO","IMAGE", "VIDEO", "OTHER");
		if($structure->subtype) {
			return $primary_mime_type[(int) $structure->type] . '/' .$structure->subtype;
		}
		return "TEXT/PLAIN";
	}
	// http://www.linuxscope.net/articles/mailAttachmentsPHP.html
	private function get_part($stream, $msg_number, $mime_type, $structure, $part_number    = false) {
		if($structure) {
			if($mime_type == $this->get_mime_type($structure)) {
   				if(!$part_number) {
   					$part_number = "1";
   				}
   				$text = imap_fetchbody($stream, $msg_number, $part_number, FT_UID ^ FT_PEEK);
   				if($structure->encoding == 3) {
   					return imap_base64($text);
   				} else if($structure->encoding == 4) {
   					return imap_qprint($text);
   				} else {
   					return $text;
				}
			}

			if($structure->type == 1) /* multipart */ {
				$prefix = '';
				while(list($index, $sub_structure) = each($structure->parts)) {
   					if($part_number) {
   						$prefix = $part_number . '.';
   					}
   					$data = $this->get_part($stream, $msg_number, $mime_type, $sub_structure,$prefix .    ($index + 1));
   					if($data) {
   						return $data;
   					}
				} // END OF WHILE
			} // END OF MULTIPART
		} // END OF STRUTURE
		return false;
	} // END OF FUNCTION

	// http://www.linuxscope.net/articles/mailAttachmentsPHP.html
	private function transformHtml($str) {
		return $str;
		if ((strpos($str,"<HTML") < 0) || (strpos($str,"<html")    < 0)) {
  			$makeHeader = "<html><head><meta http-equiv=\"Content-Type\"    content=\"text/html; charset=utf-8\"></head>\n";
   			if ((strpos($str,"<BODY") < 0) || (strpos($str,"<body")    < 0)) {
   				$makeBody = "\n<body>\n";
   				$str = $makeHeader . $makeBody . $str ."\n</body></html>";
   			} else {
   				$str = $makeHeader . $str ."\n</html>";
   			}
		} else {
   			$str = "<meta http-equiv=\"Content-Type\" content=\"text/html;    charset=utf-8\">\n". $str;
		}
   		return $str;
	}

	// http://www.linuxscope.net/articles/mailAttachmentsPHP.html
	private function transformTextToHtml($dataTxt) {
		$msgBody = str_replace("\n","<br>",$dataTxt);
		$msgBody = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i","$1http://$2",    $msgBody);
		$msgBody = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<A    TARGET=\"_blank\" HREF=\"$1\">$1</A>", $msgBody);
		$msgBody = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<A    HREF=\"mailto:$1\">$1</A>",$msgBody);
		return $msgBody;
	}

	// http://br.php.net/manual/en/function.imap-mime-header-decode.php
	private function decodeMimeString($mimeStr, $inputCharset='utf-8', $targetCharset='utf-8', $fallbackCharset='iso-8859-1') {
		$encodings=$this->mb_list_lowerencodings();
		$inputCharset=strtolower($inputCharset);
		$targetCharset=strtolower($targetCharset);
		$fallbackCharset=strtolower($fallbackCharset);

		$decodedStr='';
		$mimeStrs=imap_mime_header_decode($mimeStr);
		for ($n=sizeOf($mimeStrs), $i=0; $i<$n; $i++) {
			$mimeStr=$mimeStrs[$i];
			$mimeStr->charset=strtolower($mimeStr->charset);
			if ((@$mimeStr == 'default' && $inputCharset == $targetCharset) || @$mimStr->charset == $targetCharset) {
			  $decodedStr.=$mimStr->text;
				} else {
			  $decodedStr.=mb_convert_encoding(
			    $mimeStr->text, $targetCharset,
			    (in_array($mimeStr->charset, $encodings) ? $mimeStr->charset : $fallbackCharset));
				}
			}
				return $decodedStr;
	}
	// http://br.php.net/manual/en/function.imap-mime-header-decode.php
	private function mb_list_lowerencodings() {
		$r=mb_list_encodings(); for ($n=sizeOf($r); $n--; ) { $r[$n]=strtolower($r[$n]); } return $r;
	}

	public function pegarCodificacao($estrutura) {
		$estrutura = $estrutura->parts[0];
		if(!empty($estrutura->parts[0])){
			return $this->pegarCodificacao($estrutura);
		} else {
			if(is_array($estrutura->parameters)){
				return $estrutura->parameters[0]->value;
			}
			return false;
		}
	}

	private function verificaAnexosInline($estrutura) {
		$mod = '';
		if(count($estrutura->parts) <= 0){
			return array($mod, $estrutura);
		}
		foreach ($estrutura->parts as $key => $part) {
			$mod = $key+1;
			$mod = "{$mod}.";
			if($part->subtype != 'RELATED'){
				$mod = '';
				continue;
			} else {
				$estrutura = $part;
				break;
			}
		}
		return array($mod, $estrutura);
	}
}
