<?php
namespace hardness;

// funcionando, ignorando as variaveis globais
function Criar_Campo($cCampo,$cTipo)
{
    global $confUsuario;
    $cConfirmado='S';

    $cTabela = substr($cCampo,0,strpos($cCampo, '_'));

    $cClausula = "show columns from $cTabela like '$cCampo'";
    $cSql = mysql_query($cClausula);
    $cErro = mysql_error();
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
           $cSql = mysql_query($cClausula);
           $cErro = mysql_error();
           if (!empty($cErro))
           {
              echo "$cClausula<hr>$cErro";
//              exit;
           }
           apc_delete("cache{$cTabela}-{$confUsuario['dbDatabase']}");
       }
    }
}

/**
 * Cria várias colunas ausentes em uma única alteração de tabela.
 *
 * Mantém a idempotência de Criar_Campo(), mas evita uma reconstrução da
 * tabela para cada coluna em versões antigas do MySQL.
 *
 * @param string $cTabela Nome da tabela.
 * @param array $campos Mapa campo => definição SQL da coluna.
 */
function Criar_Campos($cTabela, $campos)
{
    global $confUsuario;

    if (!preg_match('/^[A-Za-z0-9_]+$/', $cTabela) || !is_array($campos) || empty($campos)) {
        return false;
    }

    $adicoes = array();
    foreach ($campos as $cCampo => $cTipo) {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $cCampo) || !is_string($cTipo) || trim($cTipo) === '') {
            continue;
        }

        $cCampoEscapado = mysql_real_escape_string($cCampo);
        $cClausula = "SHOW COLUMNS FROM `{$cTabela}` LIKE '{$cCampoEscapado}'";
        $cSql = mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            echo "$cClausula<hr>$cErro";
            return false;
        }

        if (mysql_num_rows($cSql) == 0) {
            $adicoes[] = "ADD `{$cCampo}` " . trim($cTipo);
        }
    }

    if (empty($adicoes)) {
        return true;
    }

    $cClausula = "ALTER TABLE `{$cTabela}` " . implode(', ', $adicoes);
    $cSql = mysql_query($cClausula);
    $cErro = mysql_error();
    if (!empty($cErro)) {
        echo "$cClausula<hr>$cErro";
        return false;
    }

    apc_delete("cache{$cTabela}-{$confUsuario['dbDatabase']}");
    return true;
}

function Alter_Query($query){
	mysql_query($query);
}

// não testada
function Criar_Indice($cCampo, $cTipo = '')
{    
    //global $mAlteracoes;
    $cConfirmado='S';

    $mCampo=explode(",,",$cCampo);
    if (count($mCampo)==0)
    {
        echo 'Não foi passado o paramento: '.$cCampo;
    }

    // $cTabela = substr($mCampo[0],0,4);
    $cTabela = substr($mCampo[0],0,strpos($mCampo[0], '_'));    
    $cIndice = "IDX_".implode("_",$mCampo);

    //-- CRIA O INDICE
    $cClausula = "SHOW INDEX FROM ".$cTabela." where Key_name = '".$cIndice."'";
    $cSql = mysql_query($cClausula);
    $cErro = mysql_error();
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
        	$cSql = mysql_query($cClausula);
	        $cErro = mysql_error();
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
            $cSql = mysql_query($cClausula);
            $cErro = mysql_error();
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
    //global $mAlteracoes;
    $cConfirmado='S';

    $mCampo=explode(",,",$cCampo);
    if (count($mCampo)==0)
    {
        echo 'Não foi passado o paramento: '.$cCampo;
    }

    //$cTabela = substr($mCampo[0],0,4);
    //$cIndice = "IDX_".implode("_",$mCampo);

    // -- CRIA O INDICE
    $cClausula = "SHOW INDEX FROM ".$cTabela." where Key_name = '".$cIndice."'";
    echo $cClausula . "<br>";
    $cSql = mysql_query($cClausula);
    $cErro = mysql_error();
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
            $cSql = mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro))
            {
               echo $cClausula.' '.$cErro;
            }
        }
    }
}


function xxxAlterar_Tipo_Campo($cCampo,$cTipo,$cDefault="")
{
    //global $mAlteracoes;
    //global $cConfirmado;

    $cTabela = substr($cCampo,0,4);
    $cClausula = "show columns from $cTabela  like '$cCampo'";
    $cSql = mysql_query($cClausula);
    $mSql = mysql_fetch_array($cSql);
    $cErro = mysql_error();
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
        $cSql=mysql_query($cClausula);

        $cErro = mysql_error();
        if (!empty($cErro))
        {
        	echo "ERRO: $cClausula\n<br />";
            //exit();
         } 
    }
}

function Alterar_Tipo_Campo($cCampo, $cTipo, $cDefault = "")
{
    // Obtém o nome da tabela a partir do campo
    $cCampo    = trim($cCampo);
    $cTabela   = explode('_', $cCampo)[0];
    $cClausula = "SHOW COLUMNS FROM $cTabela LIKE '$cCampo'";
    $cSql      = mysql_query($cClausula);
    $mSql      = mysql_fetch_array($cSql);    

    $cErro = mysql_error();
    if (!empty($cErro)) {
        echo "$cClausula<hr>$cErro";
        return; // Interrompe em caso de erro
    }

    // Remove espaços extras
    $cTipo = strtoupper(preg_replace('/\s+/', ' ', trim($cTipo)));

    // Obtém a versão do MySQL
    $versao      = mysql_query("SELECT VERSION() as versao");
    $mVersao     = mysql_fetch_array($versao);
    $versaoAtual = $mVersao['versao']; 

    if (version_compare($versaoAtual, '8.0', '>=') && preg_match('/^INT\(\d+\)/i', $cTipo)) {
        // Para INT, remove números entre parênteses, pois são ignorados no MySQL 8.0+
        // SE nçao remover ele sempre vai tentar alterar
        $cTipo = preg_replace('/^INT\(\d+\)/i', 'INT', $cTipo);        
    } 
    
    preg_match('/^(.*?)(?:\s+DEFAULT\s+(.*))?$/i', $cTipo, $matches); // Extrai o tipo e o DEFAULT do $cTipo
    $tipoDefinido = preg_replace('/\s+(NOT NULL|NULL|AUTO_INCREMENT)$/i', '', $matches[1]); // Tipo definido sem NULL e NOT NULL 
    $tipoDefinido = preg_replace('/\s*\(\s*/', '(', preg_replace('/\s*\)\s*/', ')', $tipoDefinido)); // Remove espaços ao redor de parênteses e converte para maiúscula

    $defaultDefinido = isset($matches[2]) ? trim($matches[2], "'\"") : 'NULL';

    // Tipo e Default retornados do banco
    $tipoBanco    = strtoupper($mSql['Type']);
    $defaultBanco = isset($mSql['Default']) ? strtoupper($mSql['Default']) : 'NULL';

    // Verifica se precisa ajustar o DEFAULT para tipos DECIMAL
    if ($tipoBanco === $tipoDefinido && $defaultBanco !== $defaultDefinido && stripos($tipoBanco, 'DECIMAL') !== false) {
        // Ajusta o DEFAULT definido apenas se for diferente
        // Nessa situação seria decimal(20,2) default 0 - No banco vai salvar 0.00, então na hora de validar daria diferença e tentaria atualizar
        preg_match('/DECIMAL\(\d+,\s*(\d+)\)/i', $tipoBanco, $decimalMatches);
        $casasDecimais = isset($decimalMatches[1]) ? (int)$decimalMatches[1] : 0;
        $defaultDefinido = number_format((float)$defaultDefinido, $casasDecimais, '.', '');
    }

    // Verifica se precisa alterar o campo
    if ($tipoBanco != $tipoDefinido || $defaultBanco != $defaultDefinido) {
        // Executa a alteração no banco
        $cClausula = "ALTER TABLE $cTabela CHANGE $cCampo $cCampo $cTipo $cDefault";
        $cSql = mysql_query($cClausula);

        $cErro = mysql_error();
        if (!empty($cErro)) {
            echo "ERRO: $cClausula\n<br />$cErro";
        }
    }
}

function Alterar_Campo($cCampo_Antigo,$cCampo_Novo,$cTipo='')
{
    //global $mAlteracoes;
    //global $cConfirmado;

    $cTabela = substr($cCampo_Antigo,0,4);
    $cClausula = "show columns from $cTabela like '$cCampo_Novo'";
    $cSql = mysql_query($cClausula);
    $cErro = mysql_error();
    if (!empty($cErro))
    {
       //msg_alerta("ERRO: $cClausula");
       echo "$cClausula<hr>$cErro";
    }
    if (mysql_num_rows($cSql)==0)
    {
       $mAlteracoes[]="Alterar campo <b>$cCampo_Antigo</b> para <b>$cCampo_Novo</b> na tabela $cTabela";
       $cClausula = "alter table $cTabela change $cCampo_Antigo $cCampo_Novo $cTipo";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
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
   // Nível PHP
   $cArquivo="scripts/sql/$cArquivo.sql";

   // Nível Desenvolvedor
   $cArquivoD = "ide/personalizacaoBanco/codigos/{$Tipo}-{$cNome_Funcao}.sql";
   if (file_exists($cArquivoD)) {
      $cArquivo = $cArquivoD;
   }  

   $cArquivoD = "ide/personalizacaoConf/{$cNome_Funcao}.sql-alterarSQL.php";
   if (file_exists($cArquivoD)) {
       $cArquivo = $cArquivoD;
   }   

    // Nível App
    $dir = Apps::getDirAtivos();
    $cArquivoD = "{$dir}personalizacaoBanco/codigos/{$Tipo}-{$cNome_Funcao}.sql";
    if (file_exists($cArquivoD)) {
        $cArquivo = $cArquivoD;
    }

    // Nível App Editor
    $dir = Apps::getDirEditor();
    $cArquivoD = "{$dir}personalizacaoBanco/codigos/{$Tipo}-{$cNome_Funcao}.sql";
    if ($dir && file_exists($cArquivoD)) {
        $cArquivo = $cArquivoD;
    }

   if (!file_exists($cArquivo))
   {
      echo "Arquivo não existe: $cArquivo";
      exit;
   }

   // Nível Personalização
   $cArquivoP = "{$g['pathDados']}personalizacaoConf/{$cNome_Funcao}.sql-alterarSQL.php";
   if (file_exists($cArquivoP)) {
      $cArquivo = $cArquivoP;
   }

   $mAlteracoes[]="Criar $Tipo $cNome_Funcao";

   if ($cConfirmado=='S')
   {
       $cClausula="drop $Tipo IF EXISTS $cNome_Funcao";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {       
          echo "$cClausula<BR>$cErro<hr>";
       }
       $cClausula = file_get_contents($cArquivo);

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<BR>$cErro<hr>";
       } 
   }
}

function adicionarConfGlobal($campo, $valorPadrao, $observacao)
{
  global $confUsuario;
  $campo = mysql_real_escape_string($campo);
  $valorPadrao = mysql_real_escape_string($valorPadrao);
  $observacao_ = $observacao;
  $observacao = mysql_real_escape_string($observacao);
  $sqlC004 = mysql_query("SELECT C004_Id FROM C004 GROUP BY C004_Id");
  while($rowC004 = mysql_fetch_array($sqlC004)){
      $sql = mysql_query("SELECT * FROM C031 WHERE C031_Campo = '{$campo}' AND C031_C004_Id = '{$rowC004['C004_Id']}'");
      if(mysql_num_rows($sql) <=  0){
          mysql_query("INSERT INTO C031 (C031_C004_Id, C031_Campo, C031_Valor_Padrao, C031_Valor, C031_Observacao) VALUES ('{$rowC004['C004_Id']}', '{$campo}', '{$valorPadrao}', '{$valorPadrao}', '{$observacao}')");
          $cErro = mysql_error();
          if (!empty($cErro)) {
              echo "$cClausula<BR>$cErro<hr>";
          }
          apc_delete("cacheC031-{$confUsuario['dbDatabase']}");
      } else {
          $row = mysql_fetch_array($sql);
          if(substr($row['C031_Observacao'], 0, 50) != substr($observacao_, 0, 50)){
              echo "Atualizou observação C031 do campo {$campo}<br>\n";
              mysql_query("UPDATE C031 SET C031_Observacao = '{$observacao}' WHERE C031_Id = '{$row['C031_Id']}'");
          }
      }
  }
}

function Alterar_Campo_Tabela($Tabela,$cCampo_Antigo,$cCampo_Novo,$cTipo='')
{
    //global $mAlteracoes;
    //global $cConfirmado;

    $cClausula = "show columns from $Tabela like '$cCampo_Novo'";
    $cSql = mysql_query($cClausula);
    $cErro = mysql_error();
    if (!empty($cErro))
    {
       //msg_alerta("ERRO: $cClausula");
       echo "$cClausula<hr>$cErro";
    }
    if (mysql_num_rows($cSql)==0)
    {
       $mAlteracoes[]="Alterar campo <b>$cCampo_Antigo</b> para <b>$cCampo_Novo</b> na tabela $Tabela";
       $cClausula = "alter table $Tabela change $cCampo_Antigo $cCampo_Novo $cTipo";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
            echo "$cClausula<hr>$cErro";       
    }
}


function Excluir_Campo($cTabela,$cCampo)
{
    $cClausula = "show columns from $cTabela like '$cCampo'";
    $cSql = mysql_query($cClausula);
    $cErro = mysql_error();
    if (!empty($cErro))
    {
       echo "$cClausula<hr>$cErro";
    }
    if (mysql_num_rows($cSql)>0)
    {
       $mAlteracoes[]="Excluir campo <b>$cCampo</b> na tabela $cTabela";
       $cClausula = "alter table $cTabela drop column $cCampo";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
            echo "$cClausula<hr>$cErro";       
    }
}

// Adicionando registros na tabela Q007
function insertQ007($campo, $valor, $observacao)
{
    $campo = mysql_real_escape_string($campo);
    $valor = mysql_real_escape_string($valor);
    $observacao_ = $observacao;
    $observacao = mysql_real_escape_string($observacao);
    $sql = mysql_query("SELECT * FROM global.Q007 WHERE Q007_Campo = '{$campo}'");
    if(mysql_num_rows($sql) <=  0){
        mysql_query("INSERT INTO global.Q007 (Q007_Campo, Q007_Valor, Q007_Observacao) VALUES ('{$campo}', '{$valor}',  '{$observacao}')");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            echo "Erro ao inserir registro na tabela Q007. Erro: <BR>$cErro<hr>";
        }
    } else {
        $row = mysql_fetch_array($sql);
        if(substr($row['Q007_Observacao'], 0, 50) != substr($observacao_, 0, 50)){
            echo "Atualizou observação Q007 do campo {$campo}<br>\n";
            mysql_query("UPDATE global.Q007 SET Q007_Observacao = '{$observacao}' WHERE Q007_Id = '{$row['Q007_Id']}'");
        }
    }
    
}

/**
 * Bloqueia a atualização de base em horário comercial (para evitar travar o sistema).
 * - Não bloqueia via CLI
 * - Permite liberar por host (ex: localhost)
 */
function bloquearAtualizacaoBaseHorarioComercial()
{
    $config = array(
        'ativar'          => true,
        'dias_semana'     => array(1, 2, 3, 4, 5), // 1=Seg ... 7=Dom (ISO-8601)
        'inicio'          => '06:00',
        'fim'             => '19:00',
        'hosts_liberados' => array('localhost'),
        'timezone'        => 'America/Sao_Paulo',
    );

    if (empty($config['ativar']) || PHP_SAPI === 'cli') {
        return;
    }

    // Host atual
    $host = isset($_SERVER['HTTP_HOST']) ? trim((string)$_SERVER['HTTP_HOST']) : '';

    // Normaliza host (remove porta e colchetes do IPv6)
    $hostSomente = preg_replace('/:\d+$/', '', $host);
    $hostSomente = trim((string)$hostSomente, "[] \t\n\r\0\x0B");

    $hostsLiberados = $config['hosts_liberados'] ?? array();
    $hostLiberado   = in_array($hostSomente, $hostsLiberados, true);

    // Data/hora (timezone fixo)
    $tz    = new \DateTimeZone((string)$config['timezone']);
    $agora = new \DateTime('now', $tz);

    $diaSemana = (int)$agora->format('N');
    $emDiaUtil = in_array($diaSemana, (array)$config['dias_semana'], true);

    // Comparação simples (suficiente para janelas no mesmo dia)
    $horaAtual = $agora->format('H:i');
    $emHorario = ($horaAtual >= $config['inicio']) && ($horaAtual <= $config['fim']);

    if ($emDiaUtil && $emHorario && !$hostLiberado) {

        $inicio = (string)$config['inicio'];
        $fim    = (string)$config['fim'];
        $agoraFmt = $agora->format('d/m/Y H:i:s');

        echo <<<HTML
            <div style="max-width:960px;margin:30px auto 10px auto;font-family:Arial,Helvetica,sans-serif;">
            <div style="
                background:#ffffff;
                border:1px solid #e5e7eb;
                border-left:6px solid #b00020;
                border-radius:10px;
                box-shadow:0 8px 22px rgba(0,0,0,.06);
                padding:22px 24px;
            ">

                <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:16px;">
                    <div style="
                        width:36px;
                        height:36px;
                        background:#b00020;
                        color:#fff;
                        border-radius:8px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-weight:bold;
                        font-size:18px;
                    ">!</div>

                    <div>
                        <div style="font-size:22px;font-weight:800;color:#b00020;margin-bottom:4px;">
                            Atualização de base bloqueada em horário comercial
                        </div>

                        <div style="font-size:14px;color:#555;">
                            Para evitar lentidão ou travamento durante o uso do sistema.
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:14px;flex-wrap:wrap;">
                    <div style="flex:1;min-width:260px;background:#f9fafb;border:1px solid #eef0f3;border-radius:8px;padding:12px 14px;">
                        <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                            Janela bloqueada
                        </div>
                        <div style="font-size:17px;font-weight:700;">
                            {$inicio} — {$fim}
                        </div>
                        <div style="font-size:13px;color:#6b7280;">
                            Dias úteis
                        </div>
                    </div>

                    <div style="flex:1;min-width:260px;background:#f9fafb;border:1px solid #eef0f3;border-radius:8px;padding:12px 14px;">
                        <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                            Agora
                        </div>
                        <div style="font-size:17px;font-weight:700;">
                            {$agoraFmt}
                        </div>
                    </div>
                </div>
            </div>
            </div>
HTML;

        exit;
    }
}
