<?php
namespace hardness;
require_once('bibliotecas/classes/MYTCPDF.PHP');
require_once "old/lib/funcoes.php";
definirClasse('VEN002');
class VEN002_
{
    /*
        E - Identificação do Destinatário da Nota Fiscal eletrônica
        #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação
        63  E02   CNPJ    CNPJ do Destinatário                              N      1-1         14             Informar o CNPJ ou o CPF do destinatário, preenchendo os zeros não significativos. No caso de operação com o exterior, ou para comprador estrangeiro informar a tag "idEstrangeiro", com o número do passaporte ou outro documento legal para identificar pessoa estrangeira (campo aceita valor nulo).
        64  E03   CPF     CPF do destinatário                               N      1-1         11
        64a E03a  idEstr. Identificação comprador estrangeiro               C      1-1       0,5-20         
        65  E04   xNome   Razão Social ou nome do Destinatário              C      0-1        2-60            Tag obrigatória para a NF-e (modelo 55) e opcional para a NFC-e.
        67  E06   xLgr    Logradouro                                        C      1-1        2-60            
        68  E07   nro     Número                                            C      1-1        1-60          
        70  E09   xBairro Bairro                                            C      1-1        2-60
        71  E10   cMun    Código do município                               N      1-1         7              Utilizar a Tabela do IBGE (Anexo IX - Tabela de UF, Município e País).Informar ‘9999999 ‘para operações com o exterior.
        72  E11   xMun    Nome do município                                 C      1-1        2-60            Informar ‘EXTERIOR ‘para operações com o exterior.
        73  E12   UF      Sigla da UF                                       C      1-1         2              Informar ‘EX’ para operações com o exterior.
        74  E13   CEP     Código do CEP                                     N      0-1         8              Informar os zeros não significativos
        77a E16a  indIEDest Indicador da IE do Destinatário                 N      1-1         1              1=Contribuinte ICMS (informar a IE do destinatário);2=Contribuinte isento de Inscrição no cadastro de Contribuintes do ICMS;9=Não Contribuinte, que pode ou não possuir Inscrição Estadual no Cadastro de Contribuintes do ICMS.Nota 1: No caso de NFC-e informar indIEDest=9 e não informar a tag IE do destinatário;Nota 2: No caso de operação com o Exterior informar indIEDest=9 e não informar a tag IE do destinatário;Nota 3: No caso de Contribuinte Isento de Inscrição (indIEDest=2), não informar a tag IE do destinatário.
    */
    /*$erros .= "\n";
    // 63 e 64 - Valida o tamanho deve conter 14 ou 11 caracteres 
    $dados['CPFCNPJCliente'] = trim($dados['CPFCNPJCliente']);
    
    if($tipoPessoas == 'J'){
       $erroCNPJ = rValidarCnpj($dados['CPFCNPJCliente']);
        if($erroCNPJ !== true){
            $erros .= "-".$erroCNPJ." ".$dados['CPFCNPJCliente'].".\n";
        }
    }else if($tipoPessoas == 'F'){
        $erroCPF = rValidarCpf($dados['CPFCNPJCliente']);
        if($erroCPF !== true){
            $erros .= "-".$erroCPF." ".$dados['CPFCNPJCliente'].".\n";
        }
    }
    
    //64a - idEstrangeiro 
    // --------------------------Não é utilizado este valor, é setado como null no nf3.class - hasdness -----------------------//

    // 65 - Valida nome para NF diferentes de NFC-e e seu tamanho
    $dados['ModeloACP'] = trim($dados['ModeloACP']);
    if($dados['ModeloACP'] != 8){
        if(strlen($dados['NomeCliente']) < 2 || strlen($dados['NomeCliente']) > 60){
            $erros .= "-NOME CLIENTE ".$dados['NomeCliente']." deve conter entre 2-60 caracteres!\n";
        }
    }

    



    // 71 - Valida se cMUN contém 7 dígitos
    $dados['CodCidade'] = trim($dados['CodCidade']);
    if(strlen($dados['CodCidade']) != 7){
        $erros .= "-CÓDIGO MUNICÍPIO CLIENTE ".$dados['Bairro']." deve conter 7 dígitos. Para operações com o exterior, informar ‘9999999’ (sem aspas)!\n";   
    } 

    // 72-  Valida a cidade contém mais de 2 e menos de 60 caracteres.
    $dados['Cidade'] = trim($dados['Cidade']); 
    if((strlen($dados['Cidade']) < 2) || (strlen($dados['Cidade']) > 60)){
        $erros .= "-CIDADE CLIENTE ".$dados['Cidade']." deve conter entre 2-60 caracteres!. Informar ‘EXTERIOR’ para operações com o exterior!\n";
    }

    // 73 - Valida o tamanho da sigla do Estado
    $dados['UFCliente'] = trim($dados['UFCliente']); 
    if(strlen($dados['UFCliente']) != 2){
        $erros .= "-UF CLIENTE ".$dados['UFCliente']." deve conter 2 caracteres. Informar ‘EX’ para operações com o exterior! \n";
    }

    // 74 - Valida se o CEP do endereço tem 8 caracteres
    $dados['Cep'] = trim($dados['Cep']); 
    if(strlen($dados['Cep']) != 8){
        $erros .= "-CEP CLIENTE ".$dados['Cep']." deve conter 8 dígitos! \n";
    }

    // 77a - Indicador da IE do Destinatário
    // --------------------------- Está setado manualmente no  nf3.class - hasdness --------------------//
    */

}