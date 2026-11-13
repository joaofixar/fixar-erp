<?php
namespace hardness;
include 'prog/sistema/funcoes/pesquisaGridCallback.php';
//@autor Luiz; 

switch($g['r'][3]){

  case 'consultaApiClientes':   
        
    $array = buscaCNPJ($r_CNPJ);
    $simples  = strtoupper(trim($array['array']['simples']));

    $resposta = array('code' => true, 'data' => array());
    

    //Dados Empresa
    $resposta['code'] = true;

    $resposta['data']['D024_Json_Sintegra'] = serialize($array['array']);
    $resposta['data']['D024_Data_Json_Sintegra'] = date('d/m/Y h:i:s');

    $resposta['data']['D024_Nome_Empresa']  = $array['array']['razao_social'];
    $resposta['data']['D024_Nome_Fantasia'] = $array['array'][estabelecimento]['nome_fantasia'];

      if (empty($resposta['data']['D024_Nome_Fantasia'])) {
       $resposta['data']['D024_Nome_Fantasia'] = $array['array']['razao_social'];
      }

      //Endereço Empresa
      $resposta['data']['D024_Endereco'] = $array['array'][estabelecimento] ['tipo_logradouro'] . ' ' . $array['array'][estabelecimento] ['logradouro'];
      $resposta['data']['D024_Cep'] = $array['array'][estabelecimento] ['cep'];
      $resposta['data']['D024_D030_Id'] = $array['D030_Id'];
      $D018_Id = $array['D018_Id'];
      $resposta['data']['D024_D018_Id'] = $D018_Id;

      /* Um CNPJ possui várias inscrições estaduais
      por isso é feito um loop para escolher a inscrição estadual que é a mesma UF origem do
      CNPJ*/
      foreach($array['array']['estabelecimento']['inscricoes_estaduais'] as $key => $value)
      {
        if($value['estado']['sigla'] === $array['array']['estabelecimento']['estado']['sigla'])
        {
          $resposta['data']['D024_Inscricao_Estadual'] = $array['array']['estabelecimento']['inscricoes_estaduais'][$key]['inscricao_estadual'];
          break;
        }
      }

      /*Por segurança depois do loop verifica se foi selecionado alguma inscrição estadual
      se não for preenche com a inscrição estadual [0] do array*/
      if(empty($resposta['data']['D024_Inscricao_Estadual']))
      {
        $resposta['data']['D024_Inscricao_Estadual'] = $array['array']['estabelecimento']['inscricoes_estaduais'][0]['inscricao_estadual'];
      }


      $resposta['data']['D024_D020_Id'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = '.$array['D018_Id'].' AND D020_Id='.$array['D020_Id'].' ORDER BY D020_Nome_Cidade'));
      $resposta['data']['D024_Bairro'] = $array['array'][estabelecimento] ['bairro'];
      $resposta['data']['D024_Numero'] = $array['array'][estabelecimento] ['numero'];
      $resposta['data']['D024_Complemento'] = $array['array'][estabelecimento] ['complemento'];
      $resposta['data']['D024_Data_Fundacao'] = gCorrigeData( $array['array'] [estabelecimento]['data_inicio_atividade']);
    

      //Contato Empresa
      $resposta['data']['D024_Telefone_1'] = $array['array'][estabelecimento] ['telefone1'];
      $resposta['data']['D024_DDD_Telefone_1'] = $array['array'][estabelecimento] ['ddd1'];
      $resposta['data']['D024_DDD_Telefone_2'] = $array['array'][estabelecimento] ['ddd2'];
      $resposta['data']['D024_Telefone_2'] = $array['array'][estabelecimento] ['telefone2'];
      $resposta['data']['D024_E_Mail'] = $array['array'][estabelecimento] ['email'];
      $resposta['data']['D024_DDD_Fax'] = $array['array'][estabelecimento] ['ddd_fax'];
      $resposta['data']['D024_Fax'] = $array['array'][estabelecimento] ['fax'];
      $resposta['data']['D024_Flag_Pessoa_Fisica_Juridica'] = 'J';      
      $resposta['data']['D024_Flag_Contribuinte'] = '...';    

      $simples = $array['array']['simples']['simples'];
      $resposta['data']['D024_Codigo_Regime_Tributario'] = 0;
      if ($simples == 'Sim') {
        $resposta['data']['D024_Codigo_Regime_Tributario'] = 1;
    } else if ($simples == 'Não') {
        $resposta['data']['D024_Codigo_Regime_Tributario'] = 3;
    }

    require_once('bibliotecas/classes/CAD001.php');
    $CAD001 = new CAD001();

    $CAD001->triggerAlteracaoConsultaApi($resposta, $r_CNPJ);

    echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";   
  break;

  case 'retornaSintegraCliente':   
    
     $array = buscaCNPJ($r_CNPJ);
     $resposta = array('code' => true, 'data' => array());
  
     //Dados Empresa
     $resposta['code'] = true;

     $D024_Json_Sintegra = serialize($array['array']);

     mysql_query("update D024 set D024_Json_Sintegra='$D024_Json_Sintegra', D024_Data_Json_Sintegra=now() where D024_Id='{$r_D024_Id}'");

     echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";   

  break;

  case 'consultaApiTransportadoras':
    
      $array     = buscaCNPJ($r_CNPJ);

      $simples   = strtoupper(trim($array['array']['simples']));

      $resposta  = array('code' => true, 'data' => array());

      //Dados Empresa
      $resposta['code'] = true;
      $resposta['data']['D022_Nome_Empresa'] = $array['array']['razao_social'];

      // $resposta['data']['D022_Inscricao_Estadual'] = $array['array'][estabelecimento][inscricoes_estaduais][0]['inscricao_estadual'];
      
      /* Um CNPJ possui várias inscrições estaduais
      por isso é feito um loop para escolher a inscrição estadual que é a mesma UF origem do
      CNPJ*/
      foreach($array['array']['estabelecimento']['inscricoes_estaduais'] as $key => $value)
      {
        if($value['estado']['sigla'] === $array['array']['estabelecimento']['estado']['sigla'])
        {
          $resposta['data']['D022_Inscricao_Estadual'] = $array['array']['estabelecimento']['inscricoes_estaduais'][$key]['inscricao_estadual'];
          break;
        }
      }

      /*Por segurança depois do loop verifica se foi selecionado alguma inscrição estadual
      se não for preenche com a inscrição estadual [0] do array*/
      if($resposta['data']['D022_Inscricao_Estadual'] == '')
      {
        $resposta['data']['D022_Inscricao_Estadual'] = $array['array']['estabelecimento']['inscricoes_estaduais'][0]['inscricao_estadual'];
      }

      //Endereço Empresa
      $resposta['data']['D022_Cep'] = $array['array'][estabelecimento] ['cep'];
      $resposta['data']['D022_Endereco'] = $array['array'][estabelecimento] ['tipo_logradouro'] . ' ' . $array['array'][estabelecimento] ['logradouro'];
      $resposta['data']['D022_Bairro'] = $array['array'][estabelecimento] ['bairro'];
      $resposta['data']['D022_D030_Id'] = $array['D030_Id'];
      $resposta['data']['D022_D018_Id'] = $array['D018_Id'];
      $resposta['data']['D022_D020_Id'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = '.$array['D018_Id'].' AND D020_Id='.$array['D020_Id'].' ORDER BY D020_Nome_Cidade'));
      $resposta['data']['D022_Numero'] = $array['array'][estabelecimento] ['numero'];
      $resposta['data']['D022_Data_Fundacao'] = gCorrigeData( $array [estabelecimento]['data_inicio_atividade']);
      $resposta['data']['D022_Complemento'] = $array['array'][estabelecimento] ['complemento'];

      //Contato Empresa
      $resposta['data']['D022_Telefone_1'] = $array['array'][estabelecimento] ['telefone1'];
      $resposta['data']['D022_DDD_Telefone_1'] = $array['array'][estabelecimento] ['ddd1'];
      $resposta['data']['D022_DDD_Telefone_2'] = $array['array'][estabelecimento] ['ddd2'];
      $resposta['data']['D022_Telefone_2'] = $array['array'][estabelecimento] ['telefone2'];
      $resposta['data']['D022_Fax'] = $array['array'][estabelecimento] ['email'];
      $resposta['data']['D022_Flag_Pessoa_Fisica_Juridica'] = 'J';        

      echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    
    break;

  case 'consultaApiRepresentantes':
    
      $array     = buscaCNPJ($r_CNPJ);

      $simples   = strtoupper(trim($array['array']['simples']));

      $resposta  = array('code' => true, 'data' => array());

      //Dados Empresa
      $resposta['code'] = true;
      $resposta['data']['D021_Nome_Empresa'] = $array['array']['razao_social'];
      $resposta['data']['D021_Inscricao_Estadual'] = $array['array'][estabelecimento][inscricoes_estaduais][0]['inscricao_estadual'];   
      $resposta['data']['D021_Flag_Pessoa_Fisica_Juridica'] = 'J';       
      //Endereço Empresa
      $resposta['data']['D021_Cep'] = $array['array'][estabelecimento] ['cep'];
      $resposta['data']['D021_Endereco'] = $array['array'][estabelecimento] ['tipo_logradouro'] . ' ' . $array['array'][estabelecimento] ['logradouro'];
      $resposta['data']['D021_Bairro'] = $array['array'][estabelecimento] ['bairro'];
      $resposta['data']['D021_D030_Id'] = $array['D030_Id'];
      $resposta['data']['D021_D018_Id'] = $array['D018_Id'];
      $resposta['data']['D021_D020_Id'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = '.$array['D018_Id'].' AND D020_Id='.$array['D020_Id'].' ORDER BY D020_Nome_Cidade'));
      $resposta['data']['D021_Numero'] = $array['array'][estabelecimento] ['numero'];
      $resposta['data']['D021_Complemento'] = $array['array'][estabelecimento] ['complemento'];

      //Contato Empresa
      $resposta['data']['D021_Telefone_1'] = $array['array'][estabelecimento] ['telefone1'];
      $resposta['data']['D021_DDD_Telefone_1'] = $array['array'][estabelecimento] ['ddd1'];
      $resposta['data']['D021_DDD_Fax'] = $array['array'][estabelecimento] ['ddd_fax'];
      $resposta['data']['D021_Fax'] = $array['array'][estabelecimento] ['fax'];
          

      echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

    break;
}

function buscaCNPJ($CNPJ){
  global $g;
  //Realiza uma pesquisa por uma expressão regular e a substitui.
  $CNPJ = trim(preg_replace('/[^0-9]/', '', $CNPJ));

  //Inicia o curl
  $curl = curl_init();
  //Define as configurações
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://publica.cnpj.ws/cnpj/'. $CNPJ,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'Cookie: JSESSIONID=0e410cf8f1a222e61c620e689abdb9b5b3ce8428'
    ),
  ));
  //Executa o curl
  $response = curl_exec($curl);
  //fecha
  curl_close($curl);
  //json
  $array = (array) json_decode($response, true);
  
  if ($array['array']['status']) {
      $resposta['code'] = false;
      $resposta['erro'] = $array['array']['detalhes'];
      die($_REQUEST['callback'] . "(" . json_encode($resposta) . ");");   
  }

  $cidade  = strtoupper(trim($array[estabelecimento][cidade]['ibge_id']));
  $uf      = strtoupper(trim($array[estabelecimento][estado]['ibge_id']));
  $pais    = strtoupper(trim($array[estabelecimento][pais]['id']));

  $D020 = mysql_query("SELECT D020_Id FROM D020 WHERE D020_Codigo_IBGE ='{$cidade}'");
  $mD020 = mysql_fetch_array($D020);
  
  $D018 = mysql_query("SELECT D018_Id FROM D018 WHERE D018_Codigo_IBGE ='{$uf}'");
  $mD018 = mysql_fetch_array($D018);
  
  $D030 = mysql_query("SELECT D030_Id FROM D030 WHERE D030_Codigo_Pais = '{$pais}'");
  $mD030 = mysql_fetch_array($D030);    

  return array('D030_Id' => $mD030['D030_Id'], 'D018_Id' => $mD018['D018_Id'], 'D020_Id' => $mD020['D020_Id'], 'array' => $array);
}

?>