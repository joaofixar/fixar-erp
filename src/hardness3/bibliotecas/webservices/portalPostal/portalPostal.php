<?php
namespace hardness;

definirClasse('PORTALPOSTAL');
class portalPostal_ {
    public function setPeso($x) {
        $this->peso=number_format($x,2,'.','');
    }
    public function getPeso() {
        return $this->peso;
    }

    public function setComprimento($x) {
        $this->comprimento=number_format($x,2,'.','');
    }
    public function getComprimento() {
        return $this->comprimento;
    }

    public function setAltura($x) {
        $this->altura=number_format($x,2,'.','');
    }
    public function getAltura() {
        return $this->altura;
    }

    public function setLargura($x) {
        $this->largura=number_format($x,2,'.','');
    }
    public function getLargura() {
        return $this->largura;
    }

    public function setMsgDetalhes($x) {
        $this->msgDetalhes=$x;
    }
    public function getMsgDetalhes() {
        return $this->msgDetalhes;
    }

    public function setMsgErro($x) {
        $this->msgErro=$x;
    }
    public function getMsgErro() {
        return $this->msgErro;
    }

    public function setCodigoObjeto($x) {
        $this->codigoObjeto=$x;
    }
    public function getCodigoObjeto() {
        return $this->codigoObjeto;
    }

    public function setSiglaTriagem($x) {
        $this->siglaTriagem=$x;
    }
    public function getSiglaTriagem() {
        return $this->siglaTriagem;
    }

    public function setConexaoCodigoAgencia($x) {
        $this->conexaoCodigoAgencia=$x;
    }
    public function getConexaoCodigoAgencia() {
        return $this->conexaoCodigoAgencia;
    }

    public function setConexaoLogin($x) {
        $this->conexaoLogin=$x;
    }
    public function getConexaoLogin() {
        return $this->conexaoLogin;
    }

    public function setConexaoSenha($x) {
        $this->conexaoSenha=$x;
    }
    public function getConexaoSenha() {
        return $this->conexaoSenha;
    }

    public function setAvisoRecebimento($x) {
        $this->avisoRecebimento = $x;
    }

    public function getAvisoRecebimento() {
        return $this->avisoRecebimento;
    }

    public function setMaoPropria($x) {
        $this->maoPropria = $x;
    }

    public function getMaoPropria() {
        return $this->maoPropria;
    }

    public function getServicoAdicional() {
        $this->servicoAdicional='';
        $tar = $this->getAvisoRecebimento();
        $mp  = $this->getMaoPropria();
        $tvd = $this->getValorDeclarado();
        if ($tar=='S') {
            $this->servicoAdicional .= "AR";
        }
        if ($tvd>0) {
            $this->servicoAdicional .= "VD";
        }
        if ($mp=='S') {
            $this->servicoAdicional .= "MP";
        }
        return $this->servicoAdicional;
    }

    public function setValorDeclarado($x) {
        $this->valorDeclarado = $x;
    }

    public function getValorDeclarado() {
        return $this->valorDeclarado;
    }

    public function setServico($x) {
        if(empty($x)){
            return "Serviço não pode ser vazio";
        }
        $this->servico = $x;
    }

    public function getServico() {
        return $this->servico;
    }

    public function setNF($x) {
        $this->nf = $x;
    }

    public function getNF() {
        return $this->nf;
    }

    public function setChaveNF($x) {
        $this->chaveNf = $x;
    }

    public function getChaveNF() {
        return $this->chaveNf;
    }

    public function setEstado($x) {
        if(empty($x)){
            return "Estado não pode ser vazio";
        }
        $this->estado = $x;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setCidade($x) {
        if(empty($x)){
            return "Cidade não pode ser vazia";
        }
        $this->cidade = $x;
    }

    public function getCidade() {
        return $this->cidade;
    }

    public function setBairro($x) {
        if(empty($x)){
            return "Bairro não pode ser vazio";
        }
        $this->bairro = $x;
    }

    public function getBairro() {
        return $this->bairro;
    }

    public function setComplemento($x) {
        $this->complemento = $x;
    }

    public function getComplemento() {
        return $this->complemento;
    }

    public function setEnderecoNumero($x) {
        if(empty($x)){
            return "Número não pode ser vazio";
        }
        $this->enderecoNumero = $x;
    }

    public function getEnderecoNumero() {
        return $this->enderecoNumero;
    }

    public function setEndereco($x) {
        if(empty($x)){
            return "Endereço não pode ser vazio";
        }
        $this->endereco = $x;
    }

    public function getEndereco() {
        return $this->endereco;
    }

    public function setCep($x) {
        if(empty($x)){
            return "Cep não pode ser vazio";
        }
        $this->cep = $x;
    }

    public function getCep() {
        return $this->cep;
    }

    public function setChave($x) {
        if(empty($x)){
            return "Chave não pode ser vazia";
        }
        $this->chave = $x;
    }

    public function getChave() {
        return $this->chave;
    }

    public function setDestinatario($x) {
        if(empty($x)){
            return "Destinatário não pode ser vazio";
        }
        $this->destinatario = $x;
    }

    public function getDestinatario() {
        return $this->destinatario;
    }

    public function setAosCuidados($x) {
        $this->aosCuidados = $x;
    }

    public function getAosCuidados() {
        return $this->aosCuidados;
    }

    public function setCentroCusto($x) {
        $this->centroCusto = $x;
    }

    public function getCentroCusto() {
        return $this->centroCusto;
    }

    public function criarConexao($Url_Destino) {
        $Uri_Destino = str_replace(basename($Url_Destino),'',$Url_Destino);

        if (empty($Uri_Destino)) {
            return "URL de destino não preenchida!.";
        }

        if(!@fopen("$Url_Destino", "r")) {
            if (strpos($Url_Destino,"http://")!==false) {
                return "A Url do WebService que foi informado não responde, verifique o endereço: $Url_Destino";
            } else {
                return "A Url: $Url_Destino, referente ao WebService, não responde, parece que você não adicionou o prefixo: http://";
            }
        }

        // passando o endereço do servidor
        $this->conexao_ws = new \SoapClient(null, array('location' => $Url_Destino, 'uri' => $Uri_Destino, 'trace' => 1));
    }

    public function gerarXml() {
        //-- Criando XML
        $oNovo = new \DOMDocument("1.0", "UTF-8");

        $oNovo->preserveWhiteSpace = false;

        $oNovo  ->formatOutput = false;

        //-- Tag Raiz
        $oPortal = $oNovo->createElement("portalpostal");
        $oNovo->appendChild($oPortal);

        $oPrePost = $oNovo->createElement("pre_postagem");
        $oPortal->appendChild($oPrePost);

        $oPrePost->appendChild($oNovo->createElement("chave",$this->getChave()));
        $oPrePost->appendChild($oNovo->createElement("nome",htmlspecialchars($this->getDestinatario())));
        $oPrePost->appendChild($oNovo->createElement("aos_cuidados",$this->getAosCuidados()));
        $oPrePost->appendChild($oNovo->createElement("cep",$this->getCep()));
        $oPrePost->appendChild($oNovo->createElement("endereco",$this->getEndereco()));
        $oPrePost->appendChild($oNovo->createElement("numero",$this->getEnderecoNumero()));
        $oPrePost->appendChild($oNovo->createElement("complemento",$this->getComplemento()));
        $oPrePost->appendChild($oNovo->createElement("bairro",$this->getBairro()));
        $oPrePost->appendChild($oNovo->createElement("cidade",$this->getCidade()));
        $oPrePost->appendChild($oNovo->createElement("estado",$this->getEstado()));
        $oPrePost->appendChild($oNovo->createElement("nota_fiscal",$this->getNF()));
        $oPrePost->appendChild($oNovo->createElement("chaveNotaFiscal",$this->getChaveNF()));
        $oPrePost->appendChild($oNovo->createElement("centro_custo",$this->getCentroCusto()));
        $oPrePost->appendChild($oNovo->createElement("servico",$this->getServico()));
        $oPrePost->appendChild($oNovo->createElement("servico_adicional",$this->getServicoAdicional()));
        $oPrePost->appendChild($oNovo->createElement("valor_declarado",$this->getValorDeclarado()));
        //$oPrePost->appendChild($oNovo->createElement("num_objeto",""));
        $oPrePost->appendChild($oNovo->createElement("peso",$this->getPeso()));
        $oPrePost->appendChild($oNovo->createElement("altura",$this->getAltura()));
        $oPrePost->appendChild($oNovo->createElement("largura",$this->getLargura()));
        $oPrePost->appendChild($oNovo->createElement("comprimento",$this->getComprimento()));

        $oNovo->normalize();

        return $oNovo->saveXML();
    }

    public function consumirWS() {
        global $cPasta_XML_Portal_Postal;
        $client = new \SoapClient('http://www.portalpostal.com.br/axis2/services/PrePostagemWS?wsdl');

        $function = 'PrePostagemXml';

        $cXml = $this->gerarXml();
        $cXml = str_replace(array(chr(13),chr(10),"\r","\n","\r\n")," ",$cXml);

        $cArqXml = $cPasta_XML_Portal_Postal . 'chave_' . $this->getChave().'.xml';

        file_put_contents($cArqXml, $cXml);

        $arguments = array('PrePostagemXml' => array(
                        'xml'   => $cXml,
                        'codAgencia' => $this->getConexaoCodigoAgencia(),
                        'login'      => $this->getConexaoLogin(),
                        'senha'      => $this->getConexaoSenha()
        ));

        $options = array('location' => 'http://www.portalpostal.com.br/axis2/services/PrePostagemWS', 'namespace' => 'http://postagem/xsd');

        try {
            $result = $client->__soapCall($function, $arguments, $options);
            //print_r($result);
            $this->tratarRetorno($result->return);
            return true;
        } catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function tratarRetorno($xmlRetorno) {
        $doc = new \DOMDocument();
        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($xmlRetorno,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

        $this->setMsgDetalhes($doc->getElementsByTagName('detalhes')->item(0)->nodeValue);
        $this->setMsgErro($doc->getElementsByTagName('erro')->item(0)->nodeValue);
        $this->setCodigoObjeto($doc->getElementsByTagName('codigo_rastreio')->item(0)->nodeValue);
        $this->setSiglaTriagem($doc->getElementsByTagName('sigla_triagem')->item(0)->nodeValue);
    }
}
