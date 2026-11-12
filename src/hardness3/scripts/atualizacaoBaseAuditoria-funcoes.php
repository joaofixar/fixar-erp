<?php
namespace hardness;

// funcionando, ignorando as variaveis globais
function Criar_Campo($cCampo,$cTipo)
{
    global $confUsuario,$g;
    $cConfirmado='S';

    $cTabela = substr($cCampo,0,strpos($cCampo, '_'));

    $cClausula = "show columns from $cTabela like '$cCampo'";
    $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
    $cErro = mysql_error($g['conexaoBancoAuditoria']);
    if (!empty($cErro))
    {
       echo "$cClausula<hr>$cErro";
    }
    if (mysql_num_rows($cSql)==0)
    {
       //$mAlteracoes[]="Adicionar campo: <b>$cCampo</b> <b>$cTipo</b>";
       if ($cConfirmado=='S')
       {
           $cClausula = "alter table $cTabela add $cCampo $cTipo";
           $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
           $cErro = mysql_error($g['conexaoBancoAuditoria']);
           if (!empty($cErro))
           {
              echo "$cClausula<hr>$cErro";
//              exit;
           }
           apc_delete("cache{$cTabela}-{$confUsuario['dbDatabase']}");
       }
    }
}

function Alter_Query($query){
    global $g;
	mysqli_query($query,$g['conexaoBancoAuditoria']);
}

// não testada
function Criar_Indice($cCampo, $cTipo = '')
{
    global $g;
    //global $mAlteracoes;
    $cConfirmado='S';

    $mCampo=explode(",,",$cCampo);
    if (count($mCampo)==0)
    {
        echo 'Não foi passado o paramento: '.$cCampo;
    }

    $cTabela = substr($mCampo[0],0,4);
    $cIndice = "IDX_".implode("_",$mCampo);

    //-- CRIA O INDICE
    $cClausula = "SHOW INDEX FROM ".$cTabela." where Key_name = '".$cIndice."'";
    $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
    $cErro = mysql_error($g['conexaoBancoAuditoria']);
    if (!empty($cErro))
    {
       echo $cClausula.' '.$cErro;
    }
    if (mysql_num_rows($cSql)==0)
    {
        // VERIFICA SE O CAMPO EXISTE PARA PODER CRIAR O INDICE
        for($x=0;$x<count($mCampo);$x++)
        {
        	$cClausula = "show columns from ".$cTabela." like '".$mCampo[$x]."'";
        	$cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
	        $cErro = mysql_error($g['conexaoBancoAuditoria']);
	        if (!empty($cErro))
	        {
	           echo $cClausula.' '.$cErro;
	        }
	        if (mysql_num_rows($cSql)==0)
	        {
	            echo "Campo: ".$mCampo[$x]." não existe na tabela: ".$cTabela;
	        }
        }
        echo "<br />Adicionar indice: <b>".$cIndice."</b> na tabela <b>".$cTabela."</b><br />";
        if ($cConfirmado=='S')
        {
            $cClausula = "alter table ".$cTabela." add ".$cTipo." INDEX ".$cIndice." (".implode(",",$mCampo).")";
            //echo $cClausula;
            $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
            $cErro = mysql_error($g['conexaoBancoAuditoria']);
            if (!empty($cErro))
            {
               echo $cClausula.' '.$cErro;
            }
        }
    }
}

// não testada
function Excluir_Indice($cTabela, $cIndice)
{
    global $g;
    //global $mAlteracoes;
    $cConfirmado='S';

    $mCampo=explode(",,",$cIndice);
    if (count($mCampo)==0)
    {
        echo 'Não foi passado o paramento: '.$cIndice;
    }

    //$cTabela = substr($mCampo[0],0,4);
    //$cIndice = "IDX_".implode("_",$mCampo);

    // -- CRIA O INDICE
    $cClausula = "SHOW INDEX FROM ".$cTabela." where Key_name = '".$cIndice."'";
    echo $cClausula . "<br>";
    $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
    $cErro = mysql_error($g['conexaoBancoAuditoria']);
    if (!empty($cErro))
    {
       echo $cClausula.' '.$cErro;
    }
    if (mysql_num_rows($cSql)>0)
    {
        echo "<br />Excluir indice: <b>".$cIndice."</b> na tabela <b>".$cTabela."</b><br />";
        if ($cConfirmado=='S')
        {
            $cClausula = "alter table ".$cTabela." drop index ".$cIndice;
            //echo $cClausula;
            $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
            $cErro = mysql_error($g['conexaoBancoAuditoria']);
            if (!empty($cErro))
            {
               echo $cClausula.' '.$cErro;
            }
        }
    }
}


function Alterar_Tipo_Campo($cCampo,$cTipo,$cDefault="")
{
    //global $mAlteracoes;
    //global $cConfirmado;
    global $g;

    $cTabela = substr($cCampo,0,4);
    $cClausula = "show columns from $cTabela  like '$cCampo'";
    $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
    $mSql = mysql_fetch_array($cSql);
    $cErro = mysql_error($g['conexaoBancoAuditoria']);
    if (!empty($cErro))
    {
       //msg_alerta("ERRO: $cClausula");
       echo "$cClausula<hr>$cErro";
    }
    $cTipo=strtoupper($cTipo);
    $mSql['Type']=strtoupper($mSql['Type']);
    if ($mSql['Type']!=$cTipo)
    {
    	$cClausula="alter table $cTabela  change $cCampo $cCampo $cTipo $cDefault";
        $cSql=mysqli_query($cClausula,$g['conexaoBancoAuditoria']);

        $cErro = mysql_error($g['conexaoBancoAuditoria']);
        if (!empty($cErro))
        {
        	echo "ERRO: $cClausula\n<br />";
            //exit();
         } 
    }
}

function Alterar_Campo($cCampo_Antigo,$cCampo_Novo,$cTipo='')
{
    //global $mAlteracoes;
    //global $cConfirmado;
    global $g;

    $cTabela = substr($cCampo_Antigo,0,4);
    $cClausula = "show columns from $cTabela like '$cCampo_Novo'";
    $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
    $cErro = mysql_error($g['conexaoBancoAuditoria']);
    if (!empty($cErro))
    {
       //msg_alerta("ERRO: $cClausula");
       echo "$cClausula<hr>$cErro";
    }
    if (mysql_num_rows($cSql)==0)
    {
       $mAlteracoes[]="Alterar campo <b>$cCampo_Antigo</b> para <b>$cCampo_Novo</b> na tabela $cTabela";
       $cClausula = "alter table $cTabela change $cCampo_Antigo $cCampo_Novo $cTipo";
       $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
       $cErro = mysql_error($g['conexaoBancoAuditoria']);
       if (!empty($cErro))
       		echo "$cClausula<hr>$cErro";       
    }
}

function Atualizar_Rotina($Tipo,$cNome_Funcao)
{
   global $mAlteracoes, $g;
   //global $cConfirmado;
   $cConfirmado='S';

   $cArquivo=$cNome_Funcao;
   if (empty($cArquivo))
   {
      echo "Nome do arquivo não foi definido para funcao Atualizar_Rotina";
      exit;
   }
   $cArquivo="scripts/sql/$cArquivo.sql";
   if (!file_exists($cArquivo))
   {
      echo "Arquivo não existe: $cArquivo";
      exit;
   }

   // Personalização
   $cArquivoP = "{$g['pathDados']}personalizacaoConf/{$cNome_Funcao}.sql-alterarSQL.php";
   if (file_exists($cArquivoP)) {
      $cArquivo = $cArquivoP;
   }

   $mAlteracoes[]="Criar $Tipo $cNome_Funcao";

   if ($cConfirmado=='S')
   {
       $cClausula="drop $Tipo IF EXISTS $cNome_Funcao";
       $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
       $cErro = mysql_error($g['conexaoBancoAuditoria']);
       if (!empty($cErro))
       {
          echo "$cClausula<BR>$cErro<hr>";
       }
       $cClausula = file_get_contents($cArquivo);

       $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
       $cErro = mysql_error($g['conexaoBancoAuditoria']);
       if (!empty($cErro))
       {
          echo "$cClausula<BR>$cErro<hr>";
       }
   }
}

function adicionarConfGlobal($campo, $valorPadrao, $observacao)
{
  global $confUsuario,$g;
  $campo = mysql_real_escape_string($campo,$g['conexaoBancoAuditoria']);
  $valorPadrao = mysql_real_escape_string($valorPadrao,$g['conexaoBancoAuditoria']);
  $observacao_ = $observacao;
  $observacao = mysql_real_escape_string($observacao,$g['conexaoBancoAuditoria']);
  $sqlC004 = mysqli_query("SELECT C004_Id FROM C004 GROUP BY C004_Id",$g['conexaoBancoAuditoria']);
  while($rowC004 = mysql_fetch_array($sqlC004)){
      $sql = mysqli_query("SELECT * FROM C031 WHERE C031_Campo = '{$campo}' AND C031_C004_Id = '{$rowC004['C004_Id']}'",$g['conexaoBancoAuditoria']);
      if(mysql_num_rows($sql) <=  0){
          mysqli_query("INSERT INTO C031 (C031_C004_Id, C031_Campo, C031_Valor_Padrao, C031_Valor, C031_Observacao) VALUES ('{$rowC004['C004_Id']}', '{$campo}', '{$valorPadrao}', '{$valorPadrao}', '{$observacao}')",$g['conexaoBancoAuditoria']);
          $cErro = mysql_error($g['conexaoBancoAuditoria']);
          if (!empty($cErro)) {
              echo "$cClausula<BR>$cErro<hr>";
          }
          apc_delete("cacheC031-{$confUsuario['dbDatabase']}");
      } else {
          $row = mysql_fetch_array($sql);
          if(substr($row['C031_Observacao'], 0, 50) != substr($observacao_, 0, 50)){
              echo "Atualizou observação C031 do campo {$campo}<br>\n";
              mysqli_query("UPDATE C031 SET C031_Observacao = '{$observacao}' WHERE C031_Id = '{$row['C031_Id']}'",$g['conexaoBancoAuditoria']);
          }
      }
  }
}

function Alterar_Campo_Tabela($Tabela,$cCampo_Antigo,$cCampo_Novo,$cTipo='')
{
    //global $mAlteracoes;
    //global $cConfirmado;
    global $g;
    $cClausula = "show columns from $Tabela like '$cCampo_Novo'";
    $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
    $cErro = mysql_error($g['conexaoBancoAuditoria']);
    if (!empty($cErro))
    {
       //msg_alerta("ERRO: $cClausula");
       echo "$cClausula<hr>$cErro";
    }
    if (mysql_num_rows($cSql)==0)
    {
       $mAlteracoes[]="Alterar campo <b>$cCampo_Antigo</b> para <b>$cCampo_Novo</b> na tabela $Tabela";
       $cClausula = "alter table $Tabela change $cCampo_Antigo $cCampo_Novo $cTipo";
       $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
       $cErro = mysql_error($g['conexaoBancoAuditoria']);
       if (!empty($cErro))
            echo "$cClausula<hr>$cErro";       
    }
}


function Excluir_Campo($cTabela,$cCampo)
{
    global $g;
    $cClausula = "show columns from $cTabela like '$cCampo'";
    $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
    $cErro = mysql_error($g['conexaoBancoAuditoria']);
    if (!empty($cErro))
    {
       echo "$cClausula<hr>$cErro";
    }
    if (mysql_num_rows($cSql)>0)
    {
       $mAlteracoes[]="Excluir campo <b>$cCampo</b> na tabela $cTabela";
       $cClausula = "alter table $cTabela drop column $cCampo";
       $cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
       $cErro = mysql_error($g['conexaoBancoAuditoria']);
       if (!empty($cErro))
            echo "$cClausula<hr>$cErro";       
    }
}

// Adicionando registros na tabela Q007
function insertQ007($campo, $valor, $observacao)
{
    global $g;
    $campo = mysql_real_escape_string($campo,$g['conexaoBancoAuditoria']);
    $valor = mysql_real_escape_string($valor,$g['conexaoBancoAuditoria']);
    $observacao_ = $observacao;
    $observacao = mysql_real_escape_string($observacao,$g['conexaoBancoAuditoria']);
    $sql = mysql_query("SELECT * FROM global.Q007 WHERE Q007_Campo = '{$campo}'");
    if(mysql_num_rows($sql) <=  0){
        mysqli_query("INSERT INTO global.Q007 (Q007_Campo, Q007_Valor, Q007_Observacao) VALUES ('{$campo}', '{$valor}',  '{$observacao}')",$g['conexaoBancoAuditoria']);
        $cErro = mysql_error($g['conexaoBancoAuditoria']);
        if (!empty($cErro)) {
            echo "Erro ao inserir registro na tabela Q007. Erro: <BR>$cErro<hr>";
        }
    } else {
        $row = mysql_fetch_array($sql);
        if(substr($row['Q007_Observacao'], 0, 50) != substr($observacao_, 0, 50)){
            echo "Atualizou observação Q007 do campo {$campo}<br>\n";
            mysqli_query("UPDATE global.Q007 SET Q007_Observacao = '{$observacao}' WHERE Q007_Id = '{$row['Q007_Id']}'",$g['conexaoBancoAuditoria']);
        }
    }
    
}