<?php

namespace hardness;
class Certificate {
     
    public $privateKey;
    public $publicKey;
    public $certX509;
    private $pathPublickey;
    private $pathCert;
    
     
    public function __construct($privateKey, $publicKey, $certX509)
    {                
        $this->privateKey = openssl_pkey_get_private($privateKey);
        $this->publicKey = openssl_pkey_get_public($publicKey);
        $this->certX509 = $certX509;

    }
     /**
     * carregaCertPfx
     * @param string $content conteudo do certificado
     * @param string $password senha do certificado
     */
    public static function carregaCertP12($content, $password)
    {
        $certs = [];
        if (!openssl_pkcs12_read($content, $certs, $password)) {
            throw new \InvalidArgumentException(); //não pode ler o certificado
        }

        
        $certX509 = '';
        $arrayCerts = explode("\n", $certs['cert']);
        foreach ($arrayCerts AS $propertyContent) {
            if (strncmp($propertyContent, '-----BEGIN CERTIFICATE', 22) != 0 && strncmp($propertyContent, '-----END CERTIFICATE', 20) != 0 ) {
                $certX509 .= trim($propertyContent);
            }
        }
                        
        return new static(
            $certs['pkey'],
            $certs['cert'],
            $certX509            
        );
    }

    public function getPathPublicKey() {
        // return $this->pkey;
        // "['C004_Localizacao_Certificados']['C004_Id']_key.pem
        // return './certs/1_key.pem';
        return $this->pathPublickey;
    }

    public function setPathPublicKey($path) {
        $this->pathPublickey = $path; 
    }

    public function getPathCert() {
        //retornar o caminho onde o certificado esta armazenado
        // "['C004_Localizacao_Certificados']['C004_Id']_cert.pem
        return $this->pathCert;
    }

    public function setPathCert($path) {
        $this->pathCert = $path; 
    }
}