<?php
namespace hardness;

/* INTEGRAÇÃO E-COMMERCE Nuvemshop
 *
 * URL's da API utilizadas
 * https://api.commerce.Nuvemshop.com.br/auth 					 Gerar/atualizar access_token (Retorna access_token)
 */

class ConexaoNuvemshop {


    
    private $UrlApiNuvemShop;
    private $AccessTokenNuvemShop;
    private $UserAgentNuvemShop;

    private $tudoOk;
    private $msgErro;

    public function __construct() {

        $this->$msgErro = "";
        $this->$tudoOk = true;
        $this->carregarParametros();
    }

    public function isTudoOk() {
        return $this->$tudoOk;
    }

    public function getMsgErro() {
        return $this->$msgErro;
    }

    private function deuErro($msgErro) {
        $this->$msgErro = $msgErro;
        $this->tudoOk = true;
    }

    private function carregarParametros() {

        global $g;

        $this->UrlApiNuvemShop = $g['C031']['UrlApiNuvemShop'];
        $this->AccessTokenNuvemShop = $g['C031']['AccessTokenNuvemShop'];
        $this->UserAgentNuvemShop = $g['C031']['UserAgentNuvemShop'];
    }

    public function execPost($servico,$parametros) {

        $urlApiNuvemshop = $this->UrlApiNuvemShop;
        $UserAgentNuvemShop = $this->UserAgentNuvemShop;
        $AccessTokenNuvemShop = $this->AccessTokenNuvemShop;

        // Montar a URL da solicitação
        $urlPost = $urlApiNuvemshop . "/" . $UserAgentNuvemShop . "/" . $servico;

        // Converta os dados para JSON
        $paramsPost = "";
        $paramsPost = json_encode($parametros);

        // Cabeçalhos da solicitação
        $headers = array(
            'Authentication: bearer '.$AccessTokenNuvemShop,
            'User-Agent: Your App Name (9151)',
            'Content-Type: application/json'            
        );

        // Inicialize a sessão cURL
        $curl = curl_init();

        // Configurações da solicitação cURL
        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlPost,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $paramsPost,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true
        ));

        // Executar a solicitação e capturar a resposta
        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Fechar a sessão cURL
        ob_end_clean();

        curl_close($curl);

        if($code=="200" || $code=="201"){
            return (array) json_decode($response, true);
        } else {
            return $code;
        }                

    }

    public function execPut($servico, $parametros) {

    $urlApiNuvemshop = $this->UrlApiNuvemShop;
    $UserAgentNuvemShop = $this->UserAgentNuvemShop;
    $AccessTokenNuvemShop = $this->AccessTokenNuvemShop;

    // Montar a URL da solicitação
    $urlPut = $urlApiNuvemshop . "/" . $UserAgentNuvemShop . "/" . $servico;

    // Converta os dados para JSON
    $paramsPost = "";
    $paramsPost = json_encode($parametros);
        
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => $urlPut,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => $paramsPost,
    CURLOPT_HTTPHEADER => array(
        'Authentication: bearer '.$AccessTokenNuvemShop,
        'User-Agent: Your App Name (9151)',
        'Content-Type: application/json',
        'Cookie: __cf_bm=f7UABrKtufXuVhQldjVj6i9SD6H.jre.b19L_GYAqxg-1710418884-1.0.1.1-iHWBpfL5jIinbkTXuei__f76n9phli41ou50QIrAKHG79JpdM4dZGytbLCb0JsXFgPfr_Cyhzy5529ElG4futQ'
    ),
    ));

    // Executar a solicitação e capturar a resposta
    $response = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    // Fechar a sessão cURL
    ob_end_clean();
    curl_close($curl);

    //if($code == "200" || $code == "201"){
    //    return (array) json_decode($response, true);
    //} else {
        return $code;
    //} 

    //$response = curl_exec($curl);

    //curl_close($curl);


    }

    
    
}