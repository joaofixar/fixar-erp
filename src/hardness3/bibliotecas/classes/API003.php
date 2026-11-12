<?php
namespace hardness;

definirClasse('API003');
class API003_
{
	/*
		CLASSE CRIADA PARA FUNÇÕES DA API DOS RELATÓRIOS
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

	
	public function auth($API_AUTH=false, $linkSistema = false){
		global $g, $confUsuario;

		header("Access-Control-Allow-Headers: Content-Type");
		header('Access-Control-Allow-Headers: "Origin, X-Requested-With, Content-Type, Accept"');
		header("Access-Control-Allow-Origin: *", false);
		header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD");
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: 0');

		// Se a autenticação não foi enviada
		if (empty($API_AUTH)){
		    header('WWW-Authenticate: Basic realm="API Hardness"');
		    header('HTTP/1.0 401 Unauthorized');
		    die("Acesso não autorizado. \n\nPara acessar é necessário enviar a Key e o Token, caso não possua contacte seu suporte.");
		// Se houve envio dos dados
		}else{
		    header('WWW-Authenticate: Basic realm="API Hardness"');
			header('Content-Type: application/json');
			
			// $retornoValidacao = $this->validandoUltimoAcesso();
			// if($retornoValidacao['retorno']===false){
			// 	header("HTTP/1.0 {$retornoValidacao['code']} {$retornoValidacao['message']}");
			// 	header('Retry-After: 25');
			// 	return $retornoValidacao;
			$horaAtual  = date('H:i:s');
			$diaSemana  = date('N');
			if($linkSistema && $horaAtual >= '08:00:00' && $horaAtual <= '18:00:00' && $diaSemana < 6){
				header('HTTP/1.0 400 As consultas estão indisponíveis neste horário. O serviço funciona das 18:00 às 08:00. Por favor, tente novamente fora do horário comercial.');
				$retorno['retorno'] = false;
				$retorno['message'] = "As consultas estão indisponíveis neste horário. O serviço funciona das 18:00 às 08:00. Por favor, tente novamente fora do horário comercial.";
				return $retorno;
			} else {
				$retorno = $this->autorizar($API_AUTH);
				return $retorno;
			}
		}
	}

	public function autorizar($API_AUTH){
		global $g, $confUsuario;
		$retorno = array();

		$sqlC007 = mysql_query("SELECT MIN(C019_C004_Id) as C019_C004_Id, C007_Login,C007_Senha
								  FROM C007
							 LEFT JOIN C019 ON C019_C007_Id=C007_Id
						     LEFT JOIN C021 ON C021_Id=C019_C021_Id
								 WHERE C007_Ativo='S'
								 AND MD5(CONCAT(C007_Login,':',C007_Senha))='{$API_AUTH}'");
		$C007 = mysql_fetch_assoc($sqlC007);
		
		if((mysql_num_rows($sqlC007)<=0) || empty($C007['C007_Login'])){
			header('HTTP/1.0 400 Credenciais incorretas, nao ha nenhum usuario com esse login ou senha ou este acesso esta inativo.');
			$retorno['code'] = 400;
			$retorno['retorno'] = false;
			$retorno['message'] = "Credenciais incorretas, não há nenhum usuário com esse login ou senha ou este acesso está inativo.";
			return $retorno;
		}else{
			header('HTTP/1.0 200 OK');
			return true;
		}
	}

	public function validandoUltimoAcesso(){
		global $g;

		$retorno = array();
		$linkAcessado = $g['r'][3];

		//Recupera o arquivo da memória
        $Dir = $g['pathDados'] . 'tmp/';
		is_dir($Dir) or mkdir($Dir, 0777, true);
        $arquivoNome = "{$Dir}DadosAcessoApiRelatorio.txt";
		$dadosAcesso = unserialize(file_get_contents($arquivoNome));

		if(array_key_exists($linkAcessado,$dadosAcesso)){
			$intervaloMinimoAcessoLink = 25;
			$ultimoAcessoLink = $dadosAcesso[$linkAcessado]['DataHoraUltimoAcesso'];
			$dataAtual = date("Y-m-d H:i:s");
			$HoraAtual = date("H");
			$DiaSemanaAtual = date("w");

			$horarioComercial = false;
			// if($HoraAtual>=8 && $HoraAtual<=12){
			// 	$horarioComercial = true;
			// }else if ($HoraAtual>=13 && $HoraAtual<=19){
			// 	$horarioComercial = true;
			// }

			//Dia da semana representado por numero na sequencia sendo 0 - domingo e 6 - sabado
			if(($DiaSemanaAtual>0 && $DiaSemanaAtual<6) && ($horarioComercial===true)){
				$retorno['code'] = 400;
				$retorno['retorno'] = false;
				$retorno['message'] = "Acesso a API nao permitido em horario comercial (Segunda a Sexta das 08:00 ao 12:00 - 13:00 as 19:00).";
			}else{
				$sqlTempo = mysql_query("SELECT TIMESTAMPDIFF(MINUTE, '{$ultimoAcessoLink}', '{$dataAtual}') as diferencaMinutosUltimoAcessoLink,
											TIMESTAMPDIFF(SECOND, '{$ultimoAcessoLink}', '{$dataAtual}') as diferencaSegundosUltimoAcessoLink");
				$tempoCalculado = mysql_fetch_assoc($sqlTempo);
			
				$diferencaMinutosUltimoAcessoLink = $tempoCalculado['diferencaMinutosUltimoAcessoLink'];

				$ultimoAcessoLink = gCorrigeData($dadosAcesso[$linkAcessado]['DataHoraUltimoAcesso']);

				if(($diferencaMinutosUltimoAcessoLink<$intervaloMinimoAcessoLink)){
					$retorno['code'] = 400;
					$retorno['retorno'] = false;
					$retorno['message'] = "Muitas requisicoes realizadas ao link '{$linkAcessado}', ultimo acesso realizado em {$ultimoAcessoLink}, aguarde {$intervaloMinimoAcessoLink} min para tentar novamente.";
				} else {
					$retorno['code'] = 200;
					$retorno['retorno'] = true;
				}
				$this->gravarDadosAcesso($retorno['code']);
			}			
		}else{
			$this->gravarDadosAcesso(200);
		}

		return $retorno;
	}

	public function gravarDadosAcesso($codigoUltimoAcesso){
		global $g;

		$linkAcessado = $g['r'][3];

		//Recupera o cache da memória
        $Dir = $g['pathDados'] . 'tmp/';
		is_dir($Dir) or mkdir($Dir, 0777, true);
        $arquivoNome = "{$Dir}DadosAcessoApiRelatorio.txt";
		$dadosAcesso = unserialize(file_get_contents($arquivoNome));

		$dadosAcesso[$linkAcessado]['DataHoraUltimoAcesso'] = date("Y-m-d H:i:s");
		$dadosAcesso[$linkAcessado]['CodigoUltimoAcesso'] = $codigoUltimoAcesso;
        $dadosAcesso['DataHoraUltimoAcesso'] = date("Y-m-d H:i:s");
		
		$h = fopen($arquivoNome, "w");
        $r = fwrite($h, serialize($dadosAcesso));
        fclose($h);

		return true;
	}
	

}
