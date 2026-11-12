<?php
namespace hardness;

use Exception;

definirClasse('SMTNFE');
class SMTNFE_{
  public function emitirNFe($nota) {
    try {
      $mensagem    = array();
      $termino     = false;
      $aprovada    = false;
      $gerada      = $this->gravarFila($nota);
      $dataGeracao = $this->getDateTime(true);
      $retornoEnvioXmlTransp = "";

      array_push($mensagem, "NF-e gravada na fila de emissão para a SEFAZ.");

      while (!$termino) {
        $dataAtual = $this->getDateTime();

        $nfEmitida = $this->validarNFEmitida($nota);

        if (isset($nfEmitida)) {
          if ($nfEmitida['T007_TX_Resposta_Codigo'] == 9) {
            array_push($mensagem, "NF-e aprovada na SEFAZ.");
            $aprovada = true;
          }

          if ($nfEmitida['T007_TX_Resposta_Codigo'] >= 100) {
            array_push($mensagem, "NF-e reprovada na SEFAZ.");
            $termino = true;
          }
        }

        if ($dataAtual > $dataGeracao) {
          array_push($mensagem, "Timeout na verificação da nota aprovada na SEFAZ.");
          $termino = true;
        }

        if ($aprovada) {
          $termino = true;
        }

        sleep(2);
      }

      if ($aprovada) {
        $retornoTray = $this->atualizarTray($nota['T007_Id']);
        array_push($mensagem, "NF-e enviada a Tray.");

        require_once('bibliotecas/classes/EST002.php');
        $EST002 = new EST002();
        $retornoEnvioXmlTransp = $EST002->enviarXMLEmailTransportadoraUsrRoot($nota['T007_Id']);
      }

      $retorno = array(
        "nfGerada"    => $gerada,
        "nfAprovada"  => $aprovada,
        'xmlTransp'   => $retornoEnvioXmlTransp,
        "retornoTray" => $retornoTray,
        "mensagem"    => $mensagem
      );

      return $retorno;
    } catch (Exception $expt) {
      return false;
    }
  }

  private function gravarFila($nota) {
    try {
      $queueNF = $this->carregarCampos($nota);

      $sql = "INSERT INTO global.Q001 (
                Q001_Database,
                Q001_T007_Id,
                Q001_C004_Id,
                Q001_Flag_Refazer,
                Q001_Flag_Scan,
                Q001_Url,
                Q001_Path,
                Q001_C007_Id
              ) VALUES (
                '{$queueNF['Q001_Database']}',
                '{$queueNF['Q001_T007_Id']}',
                '{$queueNF['Q001_C004_Id']}',
                '{$queueNF['Q001_Flag_Refazer']}',
                '{$queueNF['Q001_Flag_Scan']}',
                '{$queueNF['Q001_Url']}',
                '{$queueNF['Q001_Path']}',
                '{$queueNF['Q001_C007_Id']}'
              );";

      mysql_query($sql);

      $erro = mysql_error();
      if ($erro) {
        $sqlErro = "UPDATE T007 SET
                      T007_TX_Mensagem        = \"" . $erro . "\",
                      T007_TX_Resposta_Codigo = 122
                    WHERE T007_Id = '{$queueNF['Q001_C007_Id']}';";
        mysql_query($sqlErro);

        return true;
      }
    } catch (Exception $expt) {
      return false;
    }
  }

  private function carregarCampos($nota) {
    global $confUsuario;

    $queueNF = array(
      'Q001_Database'     => $confUsuario['dbDatabase'],
      'Q001_T007_Id'      => $nota['T007_Id'],
      'Q001_C004_Id'      => $nota['C004_Id'],
      'Q001_Flag_Refazer' => 'N',
      'Q001_Flag_Scan'    => 'N',
      'Q001_Url'          => $confUsuario['urlRaiz'],
      'Q001_Path'         => $confUsuario['pathRaiz'],
      'Q001_C007_Id'      => 0
    );

    return $queueNF;
  }

  private function validarNFEmitida($nota) {
    $sql = "SELECT T007_TX_Resposta_Codigo
              FROM T007
             WHERE T007_Id = '{$nota['T007_Id']}'";

    $result = mysql_fetch_assoc(mysql_query($sql));

    return $result;
  }

  private function atualizarTray($T007_Id) {

    require_once('bibliotecas/webservices/tray/envioDadosNFTray.php');
    $envioDadosNFTray = new EnvioDadosNFTray();
    $retornoTray = $envioDadosNFTray->enviarDados(0, $T007_Id);

    return $retornoTray;
  }

  private function getDateTime($addMinutes = false) {
    $dateTime = new \DateTime("now");

    if ($addMinutes) {
      $dateTime->add(new \DateInterval('PT30S'));
    }

    return $dateTime;
  }
}