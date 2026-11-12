<?php

namespace hardness;

set_time_limit(60000);
require_once(__DIR__ . "/../base/BaseIntegracao.php");

//Isso serve para o str_contains funcionar
if (!function_exists('str_contains')) {
	function str_contains($saa, $val)
	{
		return $val !== '' && mb_strpos($saa, $val) !== false;
	}
}

class etiquetasML extends BaseIntegracao
{
	public function integrar()
	{
		return $response = json_encode($this->getEtiqueta($this->paramFromGet("T005_Id_Pedido_Externo")));
	}

	public function getEtiqueta($T005_Id_Pedido_Externo)
	{
		global $g;

		$termino     = false;
		$dataGeracao = $this->getDateTime(true);
		$tempoEstourado = false;
		$response =  array();
		$response['semEtiqueta'] = "Etiqueta não encontrada no sistema do Mercado Livre";

		require_once('bibliotecas/webservices/tray/conexaoTray.php');
		$conexaoTray = new ConexaoTray();

		while (!$termino) {
			$dataAtual = $this->getDateTime();

			if ($conexaoTray->isTudoOk()) {
				$etiquetas = $conexaoTray->execGet('meli/tracking_labels', array('orders' => $T005_Id_Pedido_Externo), true);
			}

			$response['status'] = $etiquetas['code'];

			if (is_array($etiquetas) != 1) {
				return $response;
			}

			//Curl para pegar a etiqueta usando link do ML
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $etiquetas['print_url']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$contentDispositionHeader = '';
			curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $headerLine) use (&$contentDispositionHeader) {
				$len = strlen($headerLine);
				$headerLine = trim($headerLine);

				$parts = explode(':', $headerLine, 2);

				if (count($parts) === 2) {
					$headerName = strtolower(trim($parts[0]));
					$headerValue = trim($parts[1]);

					if ($headerName === 'content-disposition') {
						$contentDispositionHeader = $headerValue;
					}
				}

				return $len;
			});

			$retorno = curl_exec($ch);
			$curlData = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			curl_close($ch);

			if ($curlData == 'application/pdf' || strpos($contentDispositionHeader, 'etiquetas.pdf') !== false) {
				$termino = true;
			} else {
				log("Aguardando - curlData: [{$curlData}] - contentDispositionHeader: [{$contentDispositionHeader}]");

				if ($dataAtual > $dataGeracao) {
					$termino = true;
					$tempoEstourado = true;
				} else {
					sleep(10);
				}
			}
		}

		if ($tempoEstourado) {
			return $response;
		}

		$response['semEtiqueta'] = '';

		$pathName          = "{$g['pathDados']}etiquetas/ML/";
		$arquivoNome       = $T005_Id_Pedido_Externo . '.pdf';

		try {
			if (!is_dir($pathName)) {
				mkdir($pathName, 0777, true);
			}
		} catch (\Exception $e) {
			error_log(json_encode($e), 3, '/www/errorPathML.log');
		}

		if (file_exists($arquivoNome)) {
			unlink($arquivoNome);
		}

		//Curl para salvar a etiqueta
		$url = $etiquetas['print_url'];
		$ch = curl_init($url);

		$dir = $pathName;
		$file_name = basename($arquivoNome);
		$save_file_loc = $dir . $file_name;

		$fp = fopen($save_file_loc, 'wb');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);

		$caminhoEtiquetasMl = getcwd()."/".$g['pathDados']."etiquetas/ML/";

		$caminhoPdf = $caminhoEtiquetasMl."{$T005_Id_Pedido_Externo}.pdf";
		$caminhoPdfOutput = $caminhoEtiquetasMl."{$T005_Id_Pedido_Externo}-resized.pdf";

		$rRedimensionar = gRedimensionarPDF($caminhoPdf, $caminhoPdfOutput, 'P', array("x" => 106, "y" => 163));
		if(!$rRedimensionar['code']){
			$erro = $rRedimensionar['data'];
			error_log($erro);
			log($erro);
			$response['semEtiqueta'] = "Erro ao redimensionar a etiqueta. Favor contatar o suporte para checar o log.";
			return $response;
		}

		$nomeArquivo = pathinfo($caminhoPdfOutput)['filename'];
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
		$arquivoPath = $actual_link . "/hardness3/{$g['pathDados']}etiquetas/ML/" . $nomeArquivo;

		$response['etiqueta'] = $arquivoPath;
		return $response;
	}

	private function getDateTime($addMinutes = false)
	{
		$dateTime = new \DateTime("now");

		if ($addMinutes) {
			$dateTime->add(new \DateInterval('PT10S'));
		}

		return $dateTime;
	}
}
