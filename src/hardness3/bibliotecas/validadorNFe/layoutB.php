<?php

//
// VALIDA CABEÇÃLHO DO XML - NÚMERO DA NOTA, DATA EMISSÃO, TIPO DOCUMENTO, ETC...
//

// B-6-B02   cUF     Código da UF do emitente do Documento Fiscal
$dados['EmpresaCodigoUF']= trim($dados['EmpresaCodigoUF']);
if(empty($dados['EmpresaCodigoUF'])){
    $erros .= "-CÓDIGO UF EMITENTE está em branco!\n";
}

// B-7-B03   cNF     Código Numérico que compõe a Chave de Acesso 
// NÃO ESTÁ SENDO VALIDADO 

// B-8-B04   natOp   Descrição da Natureza da Operação
if((strlen($cDescricao_CFOP) < 1) || strlen($cDescricao_CFOP) > 60){
    $erros .= "-DESCRIÇÃO CFOP ".$cDescricao_CFOP." tamanho deve ser entre 1-60\n"; 
}

// B-9-B05   indPag  Indicador da forma de pagamento  
// ----------------- ESTÁ SENDO SETADO MANUALMENTE NO ARQUIVO NFE3.CLASS - HARDNESS ----------------------------------------------//

// B-10-B06   mod     Código do Modelo do Documento Fiscal
// ------------------- O MODELO ESTÁ SENDO SETADO MANUALMENTE NO ARQUIVO NFE3.CLASS - HARDNESS -------------------- 


// B-16-B12  cMunFG   Código do Município de Ocorrência do Fato Gerador 
// Os seguintes Municípios possuem o DV inválido:
// 4305871 - Coronel Barros/RS
// 2201919 - Bom Princípio do Piauí/PI
// 2202251 - Canavieira /PI
// 2201988 - Brejo do Piauí/PI
// 2611533 - Quixaba/PE;3117836 - Cônego Marinho/MG
// 3152131 - Ponto Chique/MG;5203939 - Buriti de Goiás/GO
// 5203962 - Buritinópolis/GO;

$dados['CodMunicipioIBGE'] = trim($dados['CodMunicipioIBGE']);
if(strlen($dados['CodMunicipioIBGE']) !=7){
    $erros .= "-CÓDIGO MUNICÍPIO EMITENTE ".$dados['CodMunicipioIBGE']." deve conter 7 dígitos!\n";
}/*else {
    $dvCalculado = $this->calcula_dv_municipio($dados['CodMunicipioIBGE']);
    $dvInformado = substr($dados['CodMunicipioIBGE'], 6,1);
    if($dvInformado != $dvCalculado){
        if(($dados['CodMunicipioIBGE'] != 4305871) || ($dados['CodMunicipioIBGE'] != 2201919) || ($dados['CodMunicipioIBGE'] != 2202251) || ($dados['CodMunicipioIBGE'] != 2201988) || ($dados['CodMunicipioIBGE'] != 2611533) || ($dados['CodMunicipioIBGE'] != 3117836) || ($dados['CodMunicipioIBGE'] != 3152131) || ($dados['CodMunicipioIBGE'] != 5203939) || ($dados['CodMunicipioIBGE'] != 5203962)){
            $erros .= "-CÓDIGO MUNICÍPIO EMITENTE ".$dados['CodMunicipioIBGE']. " inválido!"; 
        }
    }
}*/

// B-2-B21  tpImp    Formato de Impressão do DANFE
// ----------------- ESTÁ SENDO SETADO MANUALMENTE NO ARQUIVO NFE3.CLASS - HARDNESS ----------------------------------------------//

// B-26-B22  tpEmis   Tipo de Emissão da NF-e

// B-27-B23  cDV      Dígito Verificador da Chave de Acesso da NF-e
// ------------------------------------ É CALCULADO NO ARQUIVO NFE3.CLASS - HARDNESS --------------------------------------------//

// B-28-B24  tpAmb    Identificação do Ambiente
// Validar se o ambiente é 1 ou 2. Porém não entendi  como recuperar este valor

// B-29d-B29  xJust    Justificativa da entrada em contingência
// ------------------------------ É CALCULADO NO ARQUIVO NFE3.CLASS - HARDNESS --------------------------------------------------//

// B-29.1-B25a indFinal Indica operação com Consumidor final
// Contribuinte: 0=Normal;1=Consumidor final;  
$dados['FlagContribuinte'] = trim($dados['FlagContribuinte']);
if(is_null($dados['FlagContribuinte'])){
    $erros .= "-TIPO CLIENTE ".$dados['FlagContribuinte']." incorreto, deve ser preenchido com:\n\t0-Não contribuinte;\n\t1-Consumidor final;\n\t2-Revenda;\n\t3-Produtor Rural;\n\t4-Simples;\n\t5-Industria.\n";
}else{
    if(($dados['FlagContribuinte'] < 0) || ($dados['FlagContribuinte'] > 5)){
        $erros .= "-TIPO CLIENTE ".$dados['FlagContribuinte']." incorreto, deve ser preenchido com:\n\t0-Não contribuinte;\n\t1-Consumidor final.\n\t2-Revenda\n\t3-Produtor Rural\n\t4-Simples\n\t5-Industria.\n";
    }else{
        $indFinal = ($dados['FlagContribuinte'] == '0' || $dados['FlagContribuinte'] == '1') ? '1' : '0';       
    }
}



// B-29.2-B25b indPres  Indicador de presença do comprador
// ----------------- NÃO FORAM FEITAS VALIDAÇÕES POIS ESTÁ SENDO SETADO MANUALMENTE NO ARQUIVO NFE3.CLASS - HARDNESS ------------//

// B-29a-B26  procEmi  Processo de emissão da NF-e
// ----------------- NÃO FORAM FEITAS VALIDAÇÕES POIS ESTÁ SENDO SETADO MANUALMENTE NO ARQUIVO NFE3.CLASS - HARDNESS ------------//

// B-29b-B27  verProc  Versão do Processo de emissão da NF-e
// ----------------- NÃO FORAM FEITAS VALIDAÇÕES POIS ESTÁ SENDO SETADO MANUALMENTE NO ARQUIVO NFE3.CLASS - HARDNESS ------------//

// VALIDAÇÕES SOMENTE PARA OS CAMPOS QUE SÃO NECESSÁRIOS NA TABELA T007
if($T007_Id > 0){
  // B-7B03   cNF     Código Numérico que compõe a Chave de Acesso 
  if(empty($dados['ChaveDeAcesso'])){
      $erros .= "CHAVE DE ACESSO está em branco!\n";
  }else if(strlen($dados['ChaveDeAcesso']) != 8){
       $erros .= "CHAVE DE ACESSO: ".$dados['ChaveDeAcesso']." deve conter 8 dígitos!\n";
  }

  

  // B-9-B05   indPag  Indicador da forma de pagamento
  // ------------------- O MODELO ESTÁ SENDO SETADO MANUALMENTE NO ARQUIVO NFE3.CLASS - HARDNESS -------------------- 

  // B-10-B06   mod     Código do Modelo do Documento Fiscal
  // IMPLEMENTADO NO ARQUIVO validadorNFe.php

  // B-11-B07   serie   Série do Documento Fiscal
  // Validar a nfe é avulsa pois a série é = 890-899 e se NFe emitida no SCAN série = 900-999?
  if($dados['Serie'] < 0){
      $erros .= "SÉRIE está vazio, caso o documento fiscal não possua série preencher com 000!\n";
  }

  // B-12-B08   nNF     Número do Documento Fiscal
  if((strlen($dados['NumeroNF']) < 1) || (strlen($dados['NumeroNF']) > 9 )){
      $erros .= "NÚMERO DO DOCUMENTO FISCAL ".$dados['NumeroNF']." deve conter no mínimo 1 e no máximo 9 digitos!\n";
  }

  // B-13-B09  dhEmi    Data e hora de emissão do Documento Fiscal
  if(empty($dados['DataEmissaoNF'])){
      $erros .= "A DATA DE EMISSÃO está vazio!\n";
  }

  // B-14-B10  dhSaiEnt Data e hora de Saída ou da Entrada
  if($dados['ModeloACP'] != '8'){
      if(empty($dados['DataSaidaNF'])){
          $erros .= "DATA SAÍDA está vazio!\n";
      }
      if(empty($dados['HoraSaidaNF'])){
          $erros .= "HORA SAÍDA está vazio!\n";
      }
  }

  // B-15-B11  tpNF     Tipo de Operação
  if(empty($dados['EntradaSaida'])){
      $erros .= "Selecione ENTRADA/SAÍDA!\n";
  }

  // B-15a-B11a idDest   Identificador de local de destino da operação
  if(empty($dados['FlagDestinoOperacao'])){
      $erros .= "Selecione TIPO OPERAÇÃO!\n";
  }

  // B-29-B25  finNFe   Finalidade de emissão da NF-e
  if(($dados['FinalidadeNFe'] < 1) || ($dados['FinalidadeNFe'] > 4)){
      $erros .= "FINALIDADE NF-e ".$dados['FinalidadeNFe']." incorreta. Deve ser preenchida com: \n\t1-NF-e normal;\n\t2-NF-e complementar;\n\t3-NF-e de ajuste;\n\t4-Devolução de mercadoria.\n";
  }
