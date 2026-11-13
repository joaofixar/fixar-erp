<?php
namespace hardness;
require_once('bibliotecas/classes/MYTCPDF.PHP');
require_once "old/lib/funcoes.php";
definirClasse('layoutBA');
class layoutBA_
{
	if($T007_Id > 0){
		/*
            BA - Documento Fiscal Referenciado
            //----------------DÚVIDAS ------------------------//
            //Na tabela T005, existem alguns campos ...Origem_UF, Origem_Serie..., esses campos se enquadram na nota fiscal referenciada?
            //Ta tabela T003, não encontrei campos que tenham relação com a nota fiscal referenciada
                #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação
             29x.1 BA01   NFref   Informação de Documentos Fiscais referenciados          0-500                       Grupo com informações de Documentos Fiscais referenciados. Informação utilizada nas hipóteses previstas na legislação. (Ex.: Devolução de mercadorias, Substituição de NF cancelada, Complementação de NF, etc.).
             29x.2 BA02   refNFe  Chave de acesso da NF-e referenciada             N      1-1          44             Referencia uma NF-e (modelo 55) emitida anteriormente, vinculada a NF-e atual, ou uma NFC-e (modelo 65),
             29x.3 BA03   refNF   Informação da NF modelo 1/1A referenciada                            1-1
             29x.4 BA04    cUF    Código da UF do emitente                         N      1-1           2             Utilizar a Tabela do IBGE (Anexo IX - Tabela de UF, Município e País)
             29x.5 BA05   AAMM    Ano e Mês de emissão da NF-e                     N      1-1           4             AAMM da emissão da NF
             29x.6 BA06   CNPJ    CNPJ do emitente                                 N      1-1          14             Informar o CNPJ do emitente da NF   
             29x.7 BA07   mod     Modelo do Documento Fiscal                       N      1-1           2             01=modelo 01
             29x.8 BA08   serie   Série do Documento Fiscal                        N      1-1          1-3            Informar zero se não utilizada Série do documento fiscal.   
             29x.9 BA09   nNF     Número do Documento Fiscal                       N      1-1          1-9            Faixa: 1–999999999
            29x.10 BA10  refNFP   Informações da NF de produtor rural referenciada        1-1
            29x.11 BA11   cUF     Código da UF do emitente                         N      1-1           2             Utilizar a Tabela do IBGE (Anexo IX - Tabela de UF, Município e País) (v2.0)    
            29x.12 BA12   AAMM    Ano e Mês de emissão da NF-e                     N      1-1           4             AAMM da emissão da NF de produtor (v2.0)
            29x.13 BA13   CNPJ    CNPJ do emitente                                 N      1-1           14            Informar o CNPJ do emitente da NF de produtor (v2.0)    
            29x.14 BA14   CPF     CPF do emitente                                  N      1-1           11            Informar o CPF do emitente da NF de produtor (v2.0)    
            29x.15 BA15   IE      IE do emitente                                   N      1-1          2-14           Informar a IE do emitente da NF de Produtor ou o literal “ISENTO” (v2.0)
            29x.16 BA16   mod     Modelo do Documento Fiscal                       N      1-1           2             04=NF de Produtor;01=NF (v2.0)
            29x.17 BA17   serie   Série do Documento Fiscal                        N      1-1          1-3            Informar a série do documento fiscal (informar zero se inexistente) (v2.0).    
            29x.18 BA18   nNF     Número do Documento Fiscal                       N      1-1          1-9            Faixa: 1–999999999    
            29x.19 BA19  refCTe   Chave de acesso do CT-e referenciada             N      1-1           44            Utilizar esta TAG para referenciar um CT-e emitido anteriormente, vinculada a NF-e atual - (v2.0).    
            29x.20 BA20  refECF   Informações do Cupom Fiscal referenciado                1-1                         Grupo do Cupom Fiscal vinculado à NF-e (v2.0).    
            29x.21 BA21   mod     Modelo do Documento Fiscal                       C      1-1           2             "2B"=Cupom Fiscal emitido por máquina registradora (não ECF);"2C"=Cupom Fiscal PDV;"2D"=Cupom Fiscal (emitido por ECF) (v2.0).    
            29x.22 BA22  nECF     Número de ordem sequencial do ECF                N      1-1           3             Informar o número de ordem sequencial do ECF que emitiu o Cupom Fiscal vinculado à NF-e (v2.0).
            29x.23 BA23  nCOO     Número do Contador de Ordem de Operação - COO    N      1-1           6             Informar o Número do Contador de Ordem de Operação - COO vinculado à NF-e (v2.0).
        */
        if(mysql_num_rows($T132) > 0 ){
            
            // 29x.2 BA02   refNFe  Chave de acesso da NF-e referenciada             N      1-1          44             Referencia uma NF-e (modelo 55) emitida anteriormente, vinculada a NF-e atual, ou uma NFC-e (modelo 65),
            if(strlen($dados['chaveAcessoNFR']) != 44 ){
                $erros .= "-CHAVE DE ACESSO NF-e REFERENCIADA ".$dados['chaveAcessoNFR']." deve conter 44 dígitos!\n";
            }

            if ($dados['tipoNFR'] == 'NF'){
                $erros .= "\n";

                // 29x.4 BA04    cUF    Código da UF do emitente                         N      1-1           2             Utilizar a Tabela do IBGE (Anexo IX - Tabela de UF, Município e País)
                if(empty($dados['UFNFR'])){
                    $erros .= "-CÓDIGO UF EMITENTE NF Referenciada está vazio!\n";   
                }else if((($dados['UFNFR'] < 11) || ($dados['UFNFR'] > 17)) && (($dados['UFNFR'] < 21) || ($dados['UFNFR'] > 29)) && (($dados['UFNFR'] < 31) || ($dados['UFNFR'] > 35)) && (($dados['UFNFR'] < 41) || ($dados['UFNFR'] > 43)) && (($dados['UFNFR'] < 50) || ($dados['UFNFR'] > 53))) {
                    $erros .= "-CÓDIGO UF EMITENTE NF Referenciada ". $dados['UFNFR'] ." está incorreto! Valores possíveis:\n\t11-Rondônia, 12-Acre, 13-Amazonas, 14-Roraima, 15-Pará, 16-Amapá, 17-Tocantins;\n\t21-Maranhão, 22-Piauí, 23-Ceará, 24-Rio Grande do Norte, 25-Paraíba, 26-Pernambuco, 27-Alagoas, 28-Sergipe, 29-Bahia;\n\t31-Minas Gerais, 32-Espírito Santo, 33-Rio de Janeiro, 35-São Paulo;\n\t41-Paraná, 42-Santa Catarina, 43-Rio Grande do Sul;\n\t50-Mato Grosso do Sul, 51-Mato Grosso, 52-Goiás, 53-Distrito Federal. \n";
                }

                // 29x.5 BA05   AAMM    Ano e Mês de emissão da NF-e                     N      1-1           4             AAMM da emissão da NF
                if(strlen($dados['anoMesNFR']) != 4){
                    $erros .= "-ANO E MÊS EMISSÃO NF-E REFERENCIADA está vazio!";   
                }else {
                    $validaMes = substr($dados['anoMesNFR'], 2);
                    if(($validaMes < 01) || ($validaMes > 12)){
                       $erros .= "-ANO E MÊS EMISSÃO NF-E REFERENCIADA ".$dados['anoMesNFR']." mês deve ser entre 1 e 12!\n"; 
                    }
                }

                // 29x.6 BA06   CNPJ    CNPJ do emitente                                 N      1-1          14             Informar o CNPJ do emitente da NF   
                $dados['CNPJNFR'] = trim($dados['CNPJNFR']);
                if(empty($dados['CNPJNFR'])){
                    $erros .= "-CNPJ EMITENTE NF-E Referenciada está vazio";
                }else if(rValidarCnpj($dados['CNPJNFR']) !== true){
                    $erros .= "-CNPJ EMITENTE NF-E Referenciada ".$dados['CNPJNFR']." é inválido!\n";
                }

                // 29x.16 BA16   mod     Modelo do Documento Fiscal                       N      1-1           2             04=NF de Produtor;01=NF (v2.0)
                if($dados['modeloNFR'] != 01){
                    $erros .= "-MODELO NF REFERENCIADA ".$dados['modeloNFR']." deve ser 01!\n";
                }

                // 29x.8 BA08   serie   Série do Documento Fiscal                        N      1-1          1-3            Informar zero se não utilizada Série do documento fiscal.   
                if(empty($dados['serieNFR'])){
                    $erros .= "-SÉRIE NF REFERENCIADA está vazia! Informar zero se não utilizada Série do documento fiscal.\n";
                }else if((strlen($dados['serieNFR']) < 1) || (strlen($dados['serieNFR']) > 3)){
                    $erros .= "-SÉRIE NF REFERENCIADA ".$dados['serieNFR'] ." deve possuir entre 1-3 dígitos!\n";
                }

            }
            
            if($dados['tipoNFR'] == 'NF_PRODUTOR'){

            }

            if($dados['tipoNFR'] == 'CTe'){

            }

            if($dados['tipoNFR'] == 'CUPOM'){

            }


        }
            
	}

}