<?php
namespace hardness;

definirClasse('API002');
class API002_
{
	/*
		CLASSE CRIADA PARA FUNÇÕES DA API DO CLIENTE 
	*/
	private $C004;
	private $D013_Id;
	private $D024_Id;
	private $D024VendedorInterno;
	private $D179_Id;

	public function getC004(){return $this->C004;}
    public function setC004($C004){$this->C004 = $C004;}
	public function getD013_Id(){return $this->D013_Id;}
	public function setD013_Id($D013_Id){$this->D013_Id = $D013_Id;}
	public function getD024_Id(){return $this->D024_Id;}
	public function setD024_Id($D024_Id){$this->D024_Id = $D024_Id;}
	public function getD024VendedorInterno(){return $this->D024VendedorInterno;}
	public function setD024VendedorInterno($D024VendedorInterno){$this->D024VendedorInterno = $D024VendedorInterno;}
	public function getD179_Id(){return $this->D179_Id;}
	public function setD179_Id($D179_Id){$this->D179_Id = $D179_Id;}

	public function auth(){
		$key = "";
		$token = "";
		header("Access-Control-Allow-Headers: Content-Type");
		header('Access-Control-Allow-Headers: "Origin, X-Requested-With, Content-Type, Accept"');
		header("Access-Control-Allow-Origin: *", false);
		header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD");

		if (isset($_SERVER['PHP_AUTH_USER'])){
			$key = $_SERVER['PHP_AUTH_USER'];
		    $token = $_SERVER['PHP_AUTH_PW'];
		}else if (isset($_SERVER['HTTP_AUTHORIZATION'])){
		    if (preg_match('/^basic/i', $_SERVER['HTTP_AUTHORIZATION'])){
		      list($key, $token) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
		    }
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

			$retorno = $this->autorizar($key,$token);
		    return $retorno;
		}
	}

	public function autorizar($key,$token){
		global $g, $confUsuario;

		$sqlUser = mysql_query("SELECT D178_C004_Id,
									   D178_Flag_Ativo,
									   D178_Permissao_Produto,
									   D178_Permissao_Estoque_Disponibilidade,
									   D178_Permissao_Estoque_Quantidade
								FROM D178
								WHERE D178_Key='{$key}'
								AND D178_Token='{$token}'");

		$D178 = mysql_fetch_array($sqlUser);

		if((mysql_num_rows($sqlUser)>0) && ($D178['D178_Flag_Ativo']=="S")){
			$retornoAcesso=$this->permissaoAcessoMetodo($D178);
			$this->setC004($D178['D178_C004_Id']);
			return $retornoAcesso;
		}else{
			header('HTTP/1.0 401 Unauthorized');
			$retorno['code'] = 401;
			$retorno['retorno'] = false;
			$retorno['message'] = "Credenciais incorretas, não há nenhum usuário com essa Key ou Token ou este acesso está inativo.";
		    return $retorno;
		}
	}

	public function permissaoAcessoMetodo($D178){
		global $g;
		$parametros = parse_url($_SERVER['REQUEST_URI']);
		$array = explode("/",$parametros['path']);
		$metodoUrl = $array[3];
		$metodos = array('produtos'=>'D178_Permissao_Produto', 'estoqueDisponibilidade' => 'D178_Permissao_Estoque_Disponibilidade', 'estoqueQuantidade' => 'D178_Permissao_Estoque_Quantidade');

		$campoPermissao = $metodos[$metodoUrl];

		if($D178[$campoPermissao]=="S"){
			header('HTTP/1.0 200 OK');
			return true;
		}else{
			header('HTTP/1.0 403 Forbidden');
			$retorno['code'] = 403;
			$retorno['retorno'] = false;
			$retorno['message'] = "Seu usuário não tem permissão para utilizar o método {$array[3]}, contate o representante da API para mais informações.";
		    return $retorno;
		}
	}

}
