<?php
namespace hardness;

/* INTEGRAÇÃO E-COMMERCE TRAY
 *
 * URL's da API utilizadas
 * https://api.commerce.tray.com.br/auth 					 Gerar/atualizar access_token (Retorna access_token)
 */

definirClasse('CONEXAOTRAY');
class ConexaoTray_ {

    private $Z001;
    private $consumerKey;
    private $consumerSecret;

    private $tudoOk;
    private $msgErro;

    public function __construct() {

        $this->$msgErro = "";
        $this->$tudoOk = true;
        $this->carregarParametros();
        $this->carregarZ001();
        $this->checarExpiracaoAcessToken();
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

        $this->consumerKey = $g['C031']['ConsumerKeyTray'];
        $this->consumerSecret = $g['C031']['ConsumerSecretTray'];
    }

    private function carregarZ001() {

        $sql = "SELECT Z001_Id, Z001_Db, Z001_Store, Z001_Code, Z001_Access_Token, 
                       Z001_Refresh_Token, Z001_Access_Token_Expiration, 
                       Z001_Refresh_Token_Expiration, Z001_Export_URL, Z001_API_Address
                FROM Z001
                LIMIT 1 ";

        $query = mysql_query($sql);
        $this->Z001 = mysql_fetch_array($query);
    }

    private function atualizarZ001() {

        $sql = " UPDATE Z001 SET
                    Z001_Access_Token = '{$this->Z001['Z001_Access_Token']}',
                    Z001_Refresh_Token = '{$this->Z001['Z001_Refresh_Token']}',
                    Z001_Access_Token_Expiration = '{$this->Z001['Z001_Access_Token_Expiration']}',
                    Z001_Refresh_Token_Expiration = '{$this->Z001['Z001_Refresh_Token_Expiration']}'
                 WHERE Z001_Store = '{$this->Z001['Z001_Store']}' ";

        mysql_query($sql);
    }

    private function checarExpiracaoAcessToken() {

        $agora = new \DateTime();
        $agora->add(new \DateInterval('PT5M')); // adicionando 5 minutos

        $acess_Token_Expiration = new \DateTime($this->Z001['Z001_Access_Token_Expiration']);
        $diffAcessToken = $agora->diff($acess_Token_Expiration);

        // Verificando se existe o acess token e se o mesmo está na validade, com 5 minutos de diferença por conta de eventuais delays de comunicação
        if(empty($this->Z001['Z001_Access_Token']) || $diffAcessToken->invert) {

            $refresh_Token_Expiration = new \DateTime($this->Z001['Z001_Refresh_Token_Expiration']);
            $diffRefreshToken = $agora->diff($refresh_Token_Expiration);

            //Se o refresh_token estiver preenchido e dentro da validade, apenas atualiza a access token
            if (!empty($this->Z001['Z001_Refresh_Token']) && !$diffRefreshToken->invert) {
                $retRequisicao = $this->execGet('auth', array('refresh_token' => $this->Z001['Z001_Refresh_Token']), false);
            } else {
                //Caso contrário precisará gerar uma nova Refresh token e consequentemente uma nova acess token
                $paramsRequisicao = array("consumer_key" => $this->consumerKey,
                                          "consumer_secret" => $this->consumerSecret,
                                          "code" => $this->Z001['Z001_Code']);

                $retRequisicao = $this->execPost("auth", $paramsRequisicao, false);
            }

            if (isset($retRequisicao) && !empty($retRequisicao)) {
                    if (isset($retRequisicao['access_token']) && isset($retRequisicao['refresh_token'])) {
                        $this->Z001['Z001_Access_Token']             = $retRequisicao['access_token'];
                        $this->Z001['Z001_Refresh_Token']            = $retRequisicao['refresh_token'];
                        $this->Z001['Z001_Access_Token_Expiration']  = $retRequisicao['date_expiration_access_token'];
                        $this->Z001['Z001_Refresh_Token_Expiration'] = $retRequisicao['date_expiration_refresh_token'];

                        $this->atualizarZ001();
                    } else {
                        $this->deuErro("Erro ao comunicar com Tray. Retorno: " . \json_encode($retRequisicao));
                    }
            } else {
                $this->deuErro("Falha ao obter acesso à tray!");
            }
        }
    }

    public function execGet($servico, $parametros, $incluirTagAcessToken) {

        $urlApiTray = $this->Z001['Z001_API_Address'];

        $urlGet = $urlApiTray . "/" . $servico;

        if ($incluirTagAcessToken) {
            $parametros['access_token'] = $this->Z001['Z001_Access_Token'];
        }

        if (!empty($parametros)) {
            $urlGet .= "?" . http_build_query($parametros);
        }

        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $urlGet);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        $agora = new \DateTime("now");
        $agora = $agora->format("d-m-Y H:i:s");
        log("<b>[{$agora}] - Enviando GET - Serviço:</b> {$servico}: <BR> <b>URL:</b> {$urlGet}");

        $jsonResult = curl_exec($curlSession);
        curl_close($curlSession);

        $agora = new \DateTime("now");
        $agora = $agora->format("d-m-Y H:i:s");
        log("<b>[{$agora}] - Retorno GET - Serviço:</b> {$servico}: - <BR> <b>Retorno:</b> {$jsonResult}");

        return (array) json_decode($jsonResult, true);
    }

    public function execPost($servico, $parametros, $incluirTagAcessToken) {

        $urlApiTray = $this->Z001['Z001_API_Address'];

        if ($incluirTagAcessToken) {
            $urlPost = $urlApiTray . "/" . $servico . "?access_token=" . $this->Z001['Z001_Access_Token'];
        } else {
            $urlPost = $urlApiTray . "/" . $servico;
        }

        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $urlPost);
        curl_setopt($curlSession, CURLOPT_POST, 1);
        $paramsPost = "";

        if (isset($parametros) && !empty($parametros)) {

            $paramsPost = json_encode($parametros);

            curl_setopt($curlSession, CURLOPT_POSTFIELDS, $paramsPost);
            curl_setopt($curlSession, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($paramsPost))
            );
        }

        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        $agora = new \DateTime("now");
        $agora = $agora->format("d-m-Y H:i:s");
        log("<b>[{$agora}] - Enviando POST - SERVIÇO:</b> {$servico} <BR> <b>URL:</b> {$urlPost} <BR> <b>PARÂMETROS:</b> {$paramsPost}");

        $jsonResult = curl_exec($curlSession);
        curl_close($curlSession);

        $agora = new \DateTime("now");
        $agora = $agora->format("d-m-Y H:i:s");
        log("<b>[{$agora}] - Retorno POST - SERVIÇO:</b> {$servico} <BR> <b>RETORNO:</b> {$jsonResult}");

        return (array) json_decode($jsonResult, true);
    }

    public function execPut($servico, $parametros) {

        $urlApiTray = $this->Z001['Z001_API_Address'];
        $urlPut = $urlApiTray . "/" . $servico . "?access_token=" . $this->Z001['Z001_Access_Token'];

        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $urlPut);
        curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, 'PUT');
        $paramsPut = "";

        if (isset($parametros) && !empty($parametros)) {

            $paramsPut = json_encode($parametros, JSON_UNESCAPED_SLASHES);

            curl_setopt($curlSession, CURLOPT_POSTFIELDS, $paramsPut);
            curl_setopt($curlSession, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($paramsPut))
            );
        }

        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        $agora = new \DateTime("now");
        $agora = $agora->format("d-m-Y H:i:s");
        log("<b>[{$agora}] - Enviando PUT - SERVIÇO:</b> {$servico} <BR> <b>URL:</b> {$urlPut} <BR> <b>PARÂMETROS:</b> {$paramsPut}");

        $jsonResult = curl_exec($curlSession);
        curl_close($curlSession);

        $agora = new \DateTime("now");
        $agora = $agora->format("d-m-Y H:i:s");
        log("<b>[{$agora}] - Retorno PUT - SERVIÇO:</b> {$servico} <BR> <b>RETORNO:</b> {$jsonResult}");

        return (array) json_decode($jsonResult, true);
    }

}