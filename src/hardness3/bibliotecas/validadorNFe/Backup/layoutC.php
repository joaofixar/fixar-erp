<?php
namespace hardness;
require_once('bibliotecas/classes/MYTCPDF.PHP');
require_once "old/lib/funcoes.php";
definirClasse('VEN002');
class VEN002_
{
	/*
        C. Identificação do Emitente da Nota Fiscal eletrônica
        #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação
        31  C02   CNPJ    CNPJ do Emitente                                 N       1-1         14             
        31a C02a  CPF     CPF do remetente                                 N       1-1         11       
        32  C03   xNome   Razão Social ou Nome do emitente                 C       1-1        2-60
        33  C04   xFant   Nome fantasia                                    C       0-1        1-60      
        35  C06   xLgr    Logradouro                                       C       1-1        2-60
        36  C07   nro     Número                                           C       1-1        1-60
        37  C08   xCpl    Complemento                                      C       0-1        1-60
        38  C09  xBairro  Bairro                                           C       1-1        2-60          
        39  C10   cMun    Código do município                              N       1-1          7             Utilizar a Tabela do IBGE (Anexo IX- Tabela de UF, Município e País).
        40  C11   xMun     Nome do município                               C       1-1        2-60
        41  C12    UF      Sigla da UF                                     C       1-1         2
        42  C13   CEP      Código do CEP                                   N       1-1         8              Informar os zeros não significativos. (NT 2011/004)
        46  C17    IE      Inscrição Estadual do Emitente                  C       1-1        2-14            Informar somente os algarismos, sem os caracteres de formatação (ponto, barra, hífen, etc.).Na emissão de NF-e Avulsa pode ser informado o literal “ISENTO” para os contribuintes do ICMS isentos de inscrição no Cadastro de Contribuintes de ICMS.
        48  C19    IM      Inscrição Municipal do Prestador de Serviço     C       1-1        1-15            Informado na emissão de NF-e conjugada, com itens de produtos sujeitos ao ICMS e itens de serviços sujeitos ao ISSQN
        49a C21   CRT      Código de Regime Tributário                     N       1-1         1              1=Simples Nacional;2=Simples Nacional, excesso sublimite de receita bruta;3=Regime Normal. (v2.0).
    */
        
    // 31  C02   CNPJ    CNPJ do Emitente                                 N       1-1         14             
    /*$dados['CNPJEmpresaUsuaria'] = trim($dados['CNPJEmpresaUsuaria']);
    $validarCNPJ = rValidarCnpj($dados['CNPJEmpresaUsuaria']);
    if(empty($dados['CNPJEmpresaUsuaria'])){
        $erros .= "-CNPJ EMITENTE está vazio!\n";
    }else if($validarCNPJ !== true){
        $erros .= "-".$validarCNPJ." ".$dados['CNPJEmpresaUsuaria'].".\n";
    }

    // 32  C03   xNome   Razão Social ou Nome do emitente                 C       1-1        2-60
    $dados['NomeEmpresaUsuaria'] = trim($dados['NomeEmpresaUsuaria']);
    if((strlen($dados['NomeEmpresaUsuaria']) < 2) || (strlen($dados['NomeEmpresaUsuaria']) > 60)){
        $erros .= "-NOME EMPRESA EMITENTE ".$dados['NomeEmpresaUsuaria']." deve conter entre 2-60 caracteres!\n";
    }

    // 35  C06   xLgr    Logradouro                                       C       1-1        2-60
    $dados['EnderecoEmpresaUsuaria'] = trim($dados['EnderecoEmpresaUsuaria']);
    if((strlen($dados['EnderecoEmpresaUsuaria']) < 2) || (strlen($dados['EnderecoEmpresaUsuaria']) > 60)){
        $erros .= "-ENDEREÇO EMPRESA EMITENTE ".$dados['EnderecoEmpresaUsuaria']." deve conter entre 2-60 caracteres!\n";
    }

     // 36  C07   nro     Número                                           C       1-1        1-60
    $dados['NumeroEmpresaUsuaria'] = trim($dados['NumeroEmpresaUsuaria']);
    if((strlen($dados['NumeroEmpresaUsuaria']) < 1) || (strlen($dados['NumeroEmpresaUsuaria']) > 60)){
        $erros .= "-NÚMERO EMPRESA EMITENTE ".$dados['NumeroEmpresaUsuaria']." deve conter entre 2-60 dígitos!\n";
    }

    // #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação
    // 38  C09  xBairro  Bairro                                           C       1-1        2-60
    $dados['BairroEmpresaUsuaria'] = trim($dados['BairroEmpresaUsuaria']);
    if((strlen($dados['BairroEmpresaUsuaria']) < 2) || (strlen($dados['BairroEmpresaUsuaria']) > 60)){
        $erros .= "-BAIRRO EMPRESA EMITENTE ".$dados['BairroEmpresaUsuaria']." deve conter entre 2-60 caracteres!\n";
    }

    // 39  C10   cMun    Código do município                              N       1-1          7             Utilizar a Tabela do IBGE (Anexo IX- Tabela de UF, Município e País).
    $dados['CodMunicipioIBGE'] = trim($dados['CodMunicipioIBGE']);
    if(strlen($dados['CodMunicipioIBGE']) !=7) {
        $erros .= "-CÓDIGO IBGE CIDADE EMPRESA EMITENTE ".$dados['CodMunicipioIBGE']." deve conter 7 dígitos!\n";
    }

    // 40  C11   xMun     Nome do município                               C       1-1        2-60
    $dados['CidadeEmpresaUsuaria'] = trim($dados['CidadeEmpresaUsuaria']);
    if((strlen($dados['CidadeEmpresaUsuaria']) < 2) || (strlen($dados['CidadeEmpresaUsuaria']) > 60)){
        $erros .= "-CIDADE EMPRESA EMITENTE ".$dados['CidadeEmpresaUsuaria']." deve conter entre 2-60 caracteres!\n";
    }

    // 41  C12    UF      Sigla da UF                                     C       1-1         2
    $dados['UFEmpresaUsuaria'] = trim($dados['UFEmpresaUsuaria']);
    if(strlen($dados['UFEmpresaUsuaria']) !=2) {
        $erros .= "-UF EMPRESA EMITENTE ".$dados['UFEmpresaUsuaria']." deve conter 2 caracteres!\n";
    }

    // 42  C13   CEP      Código do CEP                                   N       1-1         8              
    // Informar os zeros não significativos. (NT 2011/004)
    $dados['CEPEmpresaUsuaria'] = trim($dados['CEPEmpresaUsuaria']);
    if(strlen($dados['CEPEmpresaUsuaria']) !=8) {
        $erros .= "-CEP EMPRESA EMITENTE ".$dados['CEPEmpresaUsuaria']." deve conter 8 dígitos!\n";
    }

    // 46  C17    IE      Inscrição Estadual do Emitente                  C       1-1        2-14            
    // Informar somente os algarismos, sem os caracteres de formatação (ponto, barra, hífen, etc.).
    // Na emissão de NF-e Avulsa pode ser informado o literal “ISENTO” para os contribuintes do ICMS isentos de inscrição no Cadastro de Contribuintes de ICMS.
    $dados['IEEmpresaUsuaria'] = trim($dados['IEEmpresaUsuaria']);
    if((strlen($dados['IEEmpresaUsuaria']) < 2) || (strlen($dados['IEEmpresaUsuaria']) > 14)) {
        $erros .= "-INSCRIÇÃO ESTADUAL EMPRESA EMITENTE ".$dados['IEEmpresaUsuaria']." deve conter entre 2-14 caracteres!\n";
    }

    // 48  C19    IM      Inscrição Municipal do Prestador de Serviço     C       1-1        1-15            
    // Informado na emissão de NF-e conjugada, com itens de produtos sujeitos ao ICMS e itens de serviços sujeitos ao ISSQN
    // ---------------- o que é NFe conjugada, como fazer essa validação? Campo no T007 e C004 não contém nenhum dado ------------//
    //$dados['IMEmpresaUsuaria']

    // 49a C21   CRT      Código de Regime Tributário                     N       1-1         1              
    // 1=Simples Nacional;2=Simples Nacional, excesso sublimite de receita bruta;3=Regime Normal. (v2.0).
    /*  1=Simples Nacional; 
        2=Simples Nacional, excesso sublimite de receita bruta;
        3=Regime Normal. (v2.0).
    */
    /*$dados['CRTEmpresaUsuaria'] = trim($dados['CRTEmpresaUsuaria']);
    if(($dados['CRTEmpresaUsuaria'] < 1) OR ($dados['CRTEmpresaUsuaria'] > 3)){
        $erros .= "-CÓDIGO REGIME TRIBUTÁRIO ".$dados['CRTEmpresaUsuaria']." está incorreto. Valores válidos: \n\t1 - Simples Nacional,\n\t2 - Simples Nacional, excesso sublimite de receita bruta ou \n\t3 - Regime Normal.\n";
    }
}