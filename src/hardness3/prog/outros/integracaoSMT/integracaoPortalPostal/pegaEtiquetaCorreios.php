<?php

namespace hardness;

set_time_limit(60000);
require_once(__DIR__ . "/../base/BaseIntegracao.php");

class pegaEtiquetaCorreios extends BaseIntegracao
{

    public function integrar()
    {
        return $response = json_encode($this->getEtiquetaCorreio($this->paramFromGet("T268_Codigo_Rastreio"), $this->paramFromGet("codigoRastreio")));
    }

    public function getEtiquetaCorreio($T268_Codigo_Rastreio, $codigoRastreio)
    {
        global $g;
        global $confUsuario;
        
        $codAgencia = $g['C004']['C004_Codigo_Agencia_Correios'];
        $login = $g['C004']['C004_Login_Portal_Postal'];
        $senha = $g['C004']['C004_Senha_Portal_Postal'];
        $formato = 'ETQ_10x10';

        if($T268_Codigo_Rastreio == null){
            $codigoRastreio = $codigoRastreio;
        }else{
            $T268_Codigo_Rastreio = $T268_Codigo_Rastreio;
        }
        
        $compString = "PDF";

        log('Codigo vindo dos correios: </br>' . $codigoRastreio . 'Codigo vindo do Banco:' . $T268_Codigo_Rastreio);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.portalpostal.com.br/LinkImpressaoSRO?codAgencia={$codAgencia}&login={$login}&senha={$senha}&formato={$formato}&sro={$codigoRastreio}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/pdf'
            ),
        
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        if(\strpos($curl, $compString) !== false){
            $pathName = "{$g['pathDados']}etiquetas/correios/";

            $dataPdf           = $response;
            $arquivoNome       = $pathName . $codigoRastreio . '.pdf';

            try {
                if (!is_dir($pathName)) {
                    mkdir($pathName, 0777, true);
                }
            } catch (\Exception $e) {
                error_log(json_encode($e), 3, '/www/errorPathCorreios.log');
            }

            if (file_exists($arquivoNome)) {
                unlink($arquivoNome);
            }

            file_put_contents($arquivoNome, $dataPdf);

            $filePath = $confUsuario['urlRaiz'] . '/hardness3/' . $pathName . $codigoRastreio . '.pdf';
            return $filePath;
        }else{
            error_log(json_encode($curl), 3, '/www/errorEtiqCorreios.log');
            echo $curl;
        }

    }
}
