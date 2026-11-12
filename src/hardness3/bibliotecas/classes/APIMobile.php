<?php
namespace hardness;

definirClasse('API001');
class API001_
{
	/*
		CLASSE CRIADA PARA FUNÇÕES DA API DO MOBILE 
	*/
	//REALIZANDO CONEXÃO COM BANCO DE DADOS DO CLIENTE
	private $C004;
	private $D013_Id;
	private $D024_Id;
	private $D024VendedorInterno;
	private $EmailApp;
	private $Ecommerce;
	private $D179_Id;
	private $KeyFirebase="AAAAKRjNSPw:APA91bGRmmWfcdUOX6frwR4zP3G30AxiswsL4rLxh-p5CPLaG9gL-An7KhwnmVD7Cmy5YQvyKWznnClN9aZmRbHRGVF5E1GA5HmPfXKLTnrxhHdaTU4jXzHWweVBKofGvNCKtebS8U6z";

	public function getC004(){return $this->C004;}
    public function setC004($C004){$this->C004 = $C004;}
	public function getD013_Id(){return $this->D013_Id;}
	public function setD013_Id($D013_Id){$this->D013_Id = $D013_Id;}
	public function getD024_Id(){return $this->D024_Id;}
	public function setD024_Id($D024_Id){$this->D024_Id = $D024_Id;}
	public function getD024VendedorInterno(){return $this->D024VendedorInterno;}
	public function setD024VendedorInterno($D024VendedorInterno){$this->D024VendedorInterno = $D024VendedorInterno;}
	public function getEmailApp(){return $this->EmailApp;}
    public function setEmailApp($EmailApp){$this->EmailApp = $EmailApp;}
	public function getEcommerce(){return $this->Ecommerce;}
	public function setEcommerce($Ecommerce){$this->Ecommerce = $Ecommerce;}
	public function getD179_Id(){return $this->D179_Id;}
	public function setD179_Id($D179_Id){$this->D179_Id = $D179_Id;}
	public function getKeyFirebase(){return $this->KeyFirebase;}

	//metodo que funciona com o app e os metodos da api do cliente gp
	public function auth($API_AUTH=false){
		$key = "";
		$token = "";
		$mod = NULL;
		header("Access-Control-Allow-Headers: Content-Type");
		header('Access-Control-Allow-Headers: "Origin, X-Requested-With, Content-Type, Accept"');
		header("Access-Control-Allow-Origin: *", false);
		header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD");

		//método GET do APP
		if (isset($API_AUTH)){
			$API_AUTH = base64_decode($API_AUTH);
			list($key, $token,$contato) = explode(':',$API_AUTH);
			$acesso = 'APP';
			//Método Basic da API
		}

		// Se a autenticação não foi enviada
		if (empty($key) || empty($token)){

		    header('WWW-Authenticate: Basic realm="API Hardness"');
		    header('HTTP/1.0 401 Unauthorized');
		    die("Acesso não autorizado. \n\nPara acessar é necessário enviar a Key e o Token, caso não possua contacte seu suporte.");

		// Se houve envio dos dados
		}else{
		    header('WWW-Authenticate: Basic realm="API Hardness"');
			header('Content-Type: application/json');
			
			$retorno = $this->autorizar($key,$token,$contato);
		    return $retorno;
		}
	}

	public function autorizar($login,$senha,$contato=false){
		global $g;

		if(isset($contato)){
			$query = " AND D013_Flag_Ecommerce='S' AND D013_Id=".$contato;
		}
		$sqlUser = mysql_query("SELECT D013_Id,
		                               D024_Nome_Empresa,
									   D024_Cnpj,
									   D024_Id,
									   D024_C004_Id,
									   D024_C007_Vendedor_Interno,
									   D179_Senha,
									   D179_Id 
								  FROM D179
			    			 LEFT JOIN D013 ON D179_Login=D013_E_Mail
							 LEFT JOIN D024 ON D013_D024_Id=D024_Id
								 WHERE D179_Login='{$login}' {$query} LIMIT 1");
								   
		if(mysql_num_rows($sqlUser)>0){
			$D013 = mysql_fetch_array($sqlUser);
			if($senha!=$D013['D179_Senha']){
				header('HTTP/1.0 401 Unauthorized');
				$retorno['code'] = 401;
				$retorno['retorno'] = false;
				$retorno['message'] = "Login ou Senha inválida.";
				return $retorno;
			}
			$this->setEmailApp($login);
			if(isset($contato)){
				if($D013['D024_C004_Id']<=0){
					header('HTTP/1.0 401 Unauthorized');
					$retorno['code'] = 401;
					$retorno['retorno'] = false;
					$retorno['message'] = "Está faltando informações em seu cadastro.\n
							\nPor favor contate seu vendedor e informe a seguinte mensagem:\n
							\nEmpresa não definida no seu cadastro de cliente.\n
							\n{$D013['D024_Nome_Empresa']}\n
							\n{$D013['D024_Cnpj']}\n";
					return $retorno;
				}else if($D013['D024_C007_Vendedor_Interno']<=0){
					header('HTTP/1.0 401 Unauthorized');
					$retorno['code'] = 401;
					$retorno['retorno'] = false;
					$retorno['message'] = "Está faltando informações em seu cadastro.\n
							\nPor favor contate seu vendedor e informe a seguinte mensagem:\n
							\nNão foi definido vendedor no seu cadastro de cliente.\n
							\n{$D013['D024_Nome_Empresa']}\n
							\n{$D013['D024_Cnpj']}\n";
					return $retorno;
				}else{
					$g['clienteAtual'] = $D013['D024_Id'];
					$g['empresaAtual'] = $D013['D024_C004_Id'];
					$g['clienteAtualContato'] = $contato;
					$g['clienteAtualVendedor'] = $D013['D024_Vendedor_Interno'];
					$sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}';");
					while ($rowC031 = mysql_fetch_assoc($sqlC031)) {
						$g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
						$g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
					}

					$this->setD013_Id($contato);
					$this->setD024_Id($D013['D024_Id']);
					$this->setC004($D013['D024_C004_Id']);
					$this->setD024VendedorInterno($D013['D024_Vendedor_Interno']);
					$this->setEcommerce("S");
					$this->setD179_Id($D013['D179_Id']);
				}
			}
			header('HTTP/1.0 200 OK');
			return true;
		}else{
			header('HTTP/1.0 401 Unauthorized');
			$retorno['code'] = 401;
			$retorno['retorno'] = false;
			$retorno['message'] = "Login ou Senha inválida.";
		    return $retorno;
		}
	}

	public function inscreverTopicoFirebase($topico,$tokenDevice){
		$retorno = array();

		$url = 'https://iid.googleapis.com/iid/v1/' . $tokenDevice . '/rel/topics/' . $topico;
		$headers = array(
			'Authorization: key='.$this->getKeyFirebase(),
			'Content-Type: application/json',
			'Content-Length: 0'
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$data = curl_exec($ch);
		$respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if($respostaHttp==200){
			$retorno['code'] = 200;
			$retorno['message'] = "Token inscrito com sucesso!";
			return $retorno;
		}else{
			$retorno['code'] = $respostaHttp;
			$retorno['message'] = strip_tags($data);
		}

		return $retorno;
	}

	public function enviarNotificacaoTopico($D181_Id,$topico=false){
		$retorno = array();

		$url = 'https://fcm.googleapis.com/fcm/send';

		$topic = "notificacoesCliente";

		if(!empty($topico)){
			$topic = $topico;
		}

		$sqlD181 = mysql_query("SELECT D181_Id,D181_Titulo,D181_Subtitulo,D181_Texto  
							      FROM D181 
								 WHERE D181_Id='{$D181_Id}'");
		$D181 = mysql_fetch_assoc($sqlD181);

		if(mysql_num_rows($sqlD181)>0){
			$fields = array(
				'to' => '/topics/' . $topic,
				"notification" => array(
					"title" => $D181['D181_Titulo'],
					"body" => $D181['D181_Texto'],
					"subtitle" => $D181['D181_Subtitulo'],
				),
			);

			$fields = json_encode($fields);

			$headers = array(
				'Authorization: key=' . $this->getKeyFirebase(),
				'Content-Type: application/json',
			);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

			$data = curl_exec($ch);
			$respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			//COMENTADO PARA FINS DE TESTE
			// $respostaHttp = 200;

			if ($respostaHttp == 200) {
				mysql_query("UPDATE D181 SET D181_Data_Ultimo_Envio=CURRENT_TIMESTAMP() WHERE D181_Id='{$D181_Id}'");
				$retorno['code'] = 200;
				$retorno['message'] = "Notificacao enviada com sucesso!";
			} else {
				$retorno['code'] = $respostaHttp;
				$retorno['message'] = "Erro ao enviar a notificação:" . strip_tags($data);
			}
		}else{
			$retorno['code'] = 200;
			$retorno['message'] = "Não encontrado nenhuma notificação com esse id!";
		}
		return $retorno;
	}

	public function retorno404($rota){
		header('HTTP/1.0 404 Not Found');
		$retorno['code'] = 404;
		$retorno['message'] = "Rota {$rota} não encontrada.";
		echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
	}

	public function validaEmpresa($C004_Id){
		if(empty($C004_Id) || ($C004_Id<=0)){
			header('HTTP/1.0 406 Not Acceptable');
			$retorno['code'] = 406;
			$retorno['message'] = "Rota necessita de empresa vinculada para retornar dados.";
			print_r($retorno);
			die();
		}
	}

	public function verificaParametroObrigatorio($parametro,$nomeParametro){
		if((empty($parametro)) || !(isset($parametro))){
			header('HTTP/1.0 406 Not Acceptable');
			$retorno['code'] = 406;
			$retorno['mensagem'] = "Necessário passar o {$nomeParametro} como parametro.";
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}
	}

	public function montarWhere($where,$campo,$simbolo,$valor="",$condicao="AND"){
		if(!empty($valor)){
			$valor = trim($valor);
			if(empty($where)){
				$where = " WHERE {$campo} {$simbolo} '{$valor}'";
			}else{
				$where.=" {$condicao} {$campo} {$simbolo} '{$valor}'";
			}
		}else{
			if($simbolo===false){
				if(empty($where)){
					$where = " WHERE {$campo}";
				}else{
					$where.=" {$condicao} {$campo}";
				}
			}
		}
		return $where;
	}

	public function tratarRetornoVazio($arrayDados, $extra = ''){
		if(empty($arrayDados)){
			header('HTTP/1.0 406 Not Acceptable');
			$retorno['code'] = 406;
			$retorno['retorno'] = false;
			$retorno['message'] = "Sem retorno de informações.";
			$retorno['erro'] = $extra;
		    return $retorno;
		}
		header('HTTP/1.0 200 OK');
		return true;
	}

}
