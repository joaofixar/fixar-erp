<?php
namespace hardness;

use Exception;

set_time_limit(60000);
require_once(__DIR__ . "/../base/BaseIntegracao.php");

class Documento extends BaseIntegracao {
	public function integrar() {
		require_once(__DIR__ . "/../diversos/JsonUtils.php");
		$JsonUtils = new JsonUtils();

    $json       = file_get_contents('php://input');
    $documentos = $JsonUtils->json2Array($json);
    $notas      = array();

    //valida se o corpo da requisição array de documentos.
    if (!is_array($documentos)) {
      http_response_code(400);
      return false;
    }

    //percorre todos os documentos
    foreach ($documentos as $documento) {
      $this->transaction_start();

      try {
        unset($nota);
        unset($notaGravada);

        $nota->ID    = $documento->ID;
        $nota->CHAVE = $documento->CHAVE;

        $nfe = $this->consultarNFe($documento->CHAVE);
        if (isset($nfe)) {
          //nota já existente, repassa o ID para que seja baixada no PAF
          $nota->ID_INTEGRACAO = $nfe['id'];
        } else {
          $notaGravada = $this->gravarNFe($documento);

          $nota->ID_INTEGRACAO = $notaGravada['id'];
          $nota->ITENS         = array();

          $this->gravarXML($documento);

          foreach ($documento->ITENS as $item) {
            unset($notaItem);
            unset($itemGravado);

            $notaItem->ID = $item->ID;

            try{
              $itemGravado = $this->gravarItemNFe($notaGravada, $documento, $item);

              $notaItem->ID_INTEGRACAO = $itemGravado['id'];
            } catch (Exception $expt) {
              $notaItem->DEBUG = $expt;
            }

            array_push($nota->ITENS, $notaItem);
          }

          $this->calcularNFe($notaGravada);

          $pagamentos = $this->gravarTitulo($documento, $notaGravada, $nota);

          $nota->PAGAMENTOS = $pagamentos;
        }

        array_push($notas, $nota);

        $this->transaction_close(true);
      } catch (Exception $expt) {
        $this->transaction_close(false);
        break;
      }
    }

    return $JsonUtils->array2Json($notas);
	}

  protected function consultarNFe($chaveNFe) {
    if (empty($chaveNFe)) {
      return null;
    }

    $sql = "SELECT t007.T007_Id
              FROM t007
             WHERE t007.T007_Chave_Acesso_Nfe = '{$chaveNFe}'
             ORDER BY T007.T007_Data_Emissao DESC
             LIMIT 1";

    $result = $this->fetch_row($this->execute_sql($sql));

    if (isset($result)) {
      $nfe = array('id' => $result['T007_Id']);
      return $nfe;
    } else {
      return null;
    }
  }

  protected function consultarProdutoEmpresa($documento, $item) {
    $sql = "SELECT d009.D009_Id,
                   d001.D001_D037_Id,
                   d001.D001_D005_Id,
                   d001.D001_Codigo_Produto,
                   d001.D001_Descricao_Produto,
                   d009.D009_Valor_Custo_Unitario,
                   d001a.D001A_D059_Id_PAF_CST_ICMS,
                   d001a.D001A_D006_Id_PAF_CFOP
              FROM d001, d001a, d009
             WHERE d001.D001_Id      = d001a.D001A_D001_Id
               AND d009.D009_D001_Id = d001.D001_Id
               AND d009.D009_D001_Id = '{$item->ID_PRODUTO_INTEGRACAO}'
               AND d009.D009_C004_Id = '{$documento->ID_EMPRESA}'
             LIMIT 1";

    $result = $this->fetch_row($this->execute_sql($sql));

    if (isset($result)) {
      return $result;
    } else {
      return null;
    }
  }

  protected function consultarEmpresa($ID_EMPRESA) {
    $sql = "SELECT c004.C004_Nome_Empresa       AS T007_Empresa_Usuaria_Nome,
                   c004.C004_Logradouro         AS T007_Empresa_Usuaria_Endereco,
                   c004.C004_Numero             AS T007_Empresa_Usuaria_Numero,
                   c004.C004_Bairro             AS T007_Empresa_Usuaria_Bairro,
                   c004.C004_Cep                AS T007_Empresa_Usuaria_Cep,
                   d020.D020_Nome_Cidade        AS T007_Empresa_Usuaria_Cidade,
                   c004.C004_Cidade             AS T007_Empresa_Usuaria_Codigo_Cidade_IBGE,
                   c004.C004_Estado             AS T007_Empresa_Usuaria_Uf,
                   d018.D018_Estado             AS T007_Empresa_Usuaria_Estado,
                   c004.C004_Uf                 AS T007_Empresa_Usuaria_Codigo_Estado_IBGE,
                   c004.C004_Telefone           AS T007_Empresa_Usuaria_Telefone,
                   c004.C004_Fax                AS T007_Empresa_Usuaria_Fax,
                   c004.C004_Cnpj               AS T007_Empresa_Usuaria_Cnpj,
                   c004.C004_Inscricao_Estadual AS T007_Empresa_Usuaria_Inscricao_Estadual,
                   c004.C004_CNAE_Fiscal        AS T007_Empresa_Usuaria_CNAE_Fiscal,
                   c004.C004_Versao_NFe         AS T007_Versao_NFe
              FROM c004, d020, d018
             WHERE c004.C004_D020_Id = d020.D020_Id
               AND c004.C004_D018_Id = d018.D018_Id
               AND c004.C004_Id      = '{$ID_EMPRESA}'";

    $result = $this->fetch_row($this->execute_sql($sql));

    if (isset($result)) {
      return $result;
    } else {
      return null;
    }
  }

  protected function gravarNFe($documento) {
    $nota = $this->getCamposNota($documento);

    $sql = <<<EOT
            INSERT INTO T007 (
              T007_C004_Id,
              T007_D024_Id,
              T007_D006_Id,
              T007_C007_Id_Vendedor_Interno,
              T007_T005_Id,
              T007_Numero_Nota_Fiscal,
              T007_Serie,
              T007_Data_Emissao,
              T007_Data_Saida,
              T007_Hora_Saida,
              T007_Valor_Total,
              T007_Valor_Total_Preco,
              T007_Valor_Total_Produtos,
              T007_Valor_Total_Produtos_Sem_Desconto,
              T007_Valor_Desconto,
              T007_Valor_Total_Base_ICMS,
              T007_Valor_Total_ICMS,
              T007_Aliquota_ICMS,
              T007_Valor_Total_Base_IPI,
              T007_Valor_Total_IPI,
              T007_Aliquota_IPI,
              T007_Valor_Total_PIS,
              T007_Valor_Total_COFINS,
              T007_Arquivo_XML,
              T007_Chave_Acesso_Nfe,
              T007_Ambiente_NFe,
              T007_Flag_Contingencia,
              T007_Flag_Finalidade_NFe,
              T007_Flag_Denegado,
              T007_Flag_Inutilizado,
              T007_Cliente_Razao,
              T007_Cliente_CNPJ_CPF,
              T007_Cliente_Inscricao_Estadual,
              T007_Cliente_Endereco,
              T007_Cliente_Endereco_Numero,
              T007_Cliente_Complemento,
              T007_Cliente_Bairro,
              T007_Cliente_Cidade,
              T007_Cliente_Estado,
              T007_Flag_Entrada_Saida,
              T007_Flag_ACP,
              T007_Casas_Decimais_Preco,
              T007_Flag_Calcular,
              T007_Flag_Cancelada,
              T007_Data_Hora_Protocolo_Nfe,
              T007_Numero_Protocolo_Nfe,
              T007_Empresa_Usuaria_Nome,
              T007_Empresa_Usuaria_Endereco,
              T007_Empresa_Usuaria_Numero,
              T007_Empresa_Usuaria_Bairro,
              T007_Empresa_Usuaria_Cep,
              T007_Empresa_Usuaria_Cidade,
              T007_Empresa_Usuaria_Codigo_Cidade_IBGE,
              T007_Empresa_Usuaria_Uf,
              T007_Empresa_Usuaria_Estado,
              T007_Empresa_Usuaria_Codigo_Estado_IBGE,
              T007_Empresa_Usuaria_Telefone,
              T007_Empresa_Usuaria_Fax,
              T007_Empresa_Usuaria_Cnpj,
              T007_Empresa_Usuaria_Inscricao_Estadual,
              T007_Empresa_Usuaria_CNAE_Fiscal,
              T007_Versao_NFe,
              T007_Data_Envio_XML,
              T007_TX_Timestamp_Envio_Sefaz,
              T007_Flag_Indicador_Presenca,
              T007_TX_Resposta_Codigo
            ) VALUES (
              '{$nota['T007_C004_Id']}',
              '{$nota['T007_D024_Id']}',
              '{$nota['T007_D006_Id']}',
              '{$nota['T007_C007_Id_Vendedor_Interno']}',
              '{$nota['T007_T005_Id']}',
              '{$nota['T007_Numero_Nota_Fiscal']}',
              '{$nota['T007_Serie']}',
              '{$nota['T007_Data_Emissao']}',
              '{$nota['T007_Data_Saida']}',
              '{$nota['T007_Hora_Saida']}',
              '{$nota['T007_Valor_Total']}',
              '{$nota['T007_Valor_Total_Preco']}',
              '{$nota['T007_Valor_Total_Produtos']}',
              '{$nota['T007_Valor_Total_Produtos_Sem_Desconto']}',
              '{$nota['T007_Valor_Desconto']}',
              '{$nota['T007_Valor_Total_Base_ICMS']}',
              '{$nota['T007_Valor_Total_ICMS']}',
              '{$nota['T007_Aliquota_ICMS']}',
              '{$nota['T007_Valor_Total_Base_IPI']}',
              '{$nota['T007_Valor_Total_IPI']}',
              '{$nota['T007_Aliquota_IPI']}',
              '{$nota['T007_Valor_Total_PIS']}',
              '{$nota['T007_Valor_Total_COFINS']}',
              '{$nota['T007_Arquivo_XML']}',
              '{$nota['T007_Chave_Acesso_Nfe']}',
              '{$nota['T007_Ambiente_NFe']}',
              '{$nota['T007_Flag_Contingencia']}',
              '{$nota['T007_Flag_Finalidade_NFe']}',
              '{$nota['T007_Flag_Denegado']}',
              '{$nota['T007_Flag_Inutilizado']}',
              '{$nota['T007_Cliente_Razao']}',
              '{$nota['T007_Cliente_CNPJ_CPF']}',
              '{$nota['T007_Cliente_Inscricao_Estadual']}',
              '{$nota['T007_Cliente_Endereco']}',
              '{$nota['T007_Cliente_Endereco_Numero']}',
              '{$nota['T007_Cliente_Complemento']}',
              '{$nota['T007_Cliente_Bairro']}',
              '{$nota['T007_Cliente_Cidade']}',
              '{$nota['T007_Cliente_Estado']}',
              '{$nota['T007_Flag_Entrada_Saida']}',
              '{$nota['T007_Flag_ACP']}',
              '{$nota['T007_Casas_Decimais_Preco']}',
              '{$nota['T007_Flag_Calcular']}',
              '{$nota['T007_Flag_Cancelada']}',
              '{$nota['T007_Data_Hora_Protocolo_Nfe']}',
              '{$nota['T007_Numero_Protocolo_Nfe']}',
              '{$nota['T007_Empresa_Usuaria_Nome']}',
              '{$nota['T007_Empresa_Usuaria_Endereco']}',
              '{$nota['T007_Empresa_Usuaria_Numero']}',
              '{$nota['T007_Empresa_Usuaria_Bairro']}',
              '{$nota['T007_Empresa_Usuaria_Cep']}',
              '{$nota['T007_Empresa_Usuaria_Cidade']}',
              '{$nota['T007_Empresa_Usuaria_Codigo_Cidade_IBGE']}',
              '{$nota['T007_Empresa_Usuaria_Uf']}',
              '{$nota['T007_Empresa_Usuaria_Estado']}',
              '{$nota['T007_Empresa_Usuaria_Codigo_Estado_IBGE']}',
              '{$nota['T007_Empresa_Usuaria_Telefone']}',
              '{$nota['T007_Empresa_Usuaria_Fax']}',
              '{$nota['T007_Empresa_Usuaria_Cnpj']}',
              '{$nota['T007_Empresa_Usuaria_Inscricao_Estadual']}',
              '{$nota['T007_Empresa_Usuaria_CNAE_Fiscal']}',
              '{$nota['T007_Versao_NFe']}',
              '{$nota['T007_Data_Envio_XML']}',
              '{$nota['T007_TX_Timestamp_Envio_Sefaz']}',
              '{$nota['T007_Flag_Indicador_Presenca']}',
              '{$nota['T007_TX_Resposta_Codigo']}'
            );
EOT;

    $this->execute_sql($sql);

    return $this->consultarNFe($documento->CHAVE);
  }

  protected function gravarItemNFe($notaGravada, $documento, $item) {
    global $g;
    $itemNFe = $this->getCamposItem($notaGravada, $documento, $item);

    $sql = <<<EOT
            INSERT INTO T008 (
              T008_T007_Id,
              T008_C004_Id,
              T008_D009_Id,
              T008_D037_Id,
              T008_D005_Id,
              T008_T006_Id,
              T008_D006_Id,
              T008_Codigo_Produto,
              T008_Descricao_Produto,
              T008_Quantidade,
              T008_Valor_Preco_Unitario,
              T008_Valor_Desconto_Unitario,
              T008_Percentual_Desconto,
              T008_Valor_Preco_Sem_Desconto_Unitario,
              T008_Valor_Base_ICMS,
              T008_Aliquota_ICMS,
              T008_Valor_ICMS,
              T008_D059_Id,
              T008_Valor_Base_IPI,
              T008_Aliquota_IPI,
              T008_Valor_IPI,
              T008_Situacao_Tributaria_IPI,
              T008_Valor_Base_PIS,
              T008_Aliquota_PIS,
              T008_Valor_PIS,
              T008_Situacao_Tributaria_PIS,
              T008_Valor_Base_COFINS,
              T008_Aliquota_COFINS,
              T008_Valor_COFINS,
              T008_Situacao_Tributaria_COFINS,
              T008_Valor_Custo_Unitario,
              T008_Valor_Preco_Original,
              T008_Flag_Calculo_Manual
            ) VALUES (
              '{$itemNFe['T008_T007_Id']}',
              '{$itemNFe['T008_C004_Id']}',
              '{$itemNFe['T008_D009_Id']}',
              '{$itemNFe['T008_D037_Id']}',
              '{$itemNFe['T008_D005_Id']}',
              '{$itemNFe['T008_T006_Id']}',
              '{$itemNFe['T008_D006_Id']}',
              '{$itemNFe['T008_Codigo_Produto']}',
              '{$itemNFe['T008_Descricao_Produto']}',
              '{$itemNFe['T008_Quantidade']}',
              '{$itemNFe['T008_Valor_Preco_Unitario']}',
              '{$itemNFe['T008_Valor_Desconto_Unitario']}',
              '{$itemNFe['T008_Percentual_Desconto']}',
              '{$itemNFe['T008_Valor_Preco_Sem_Desconto_Unitario']}',
              '{$itemNFe['T008_Valor_Base_ICMS']}',
              '{$itemNFe['T008_Aliquota_ICMS']}',
              '{$itemNFe['T008_Valor_ICMS']}',
              '{$itemNFe['T008_D059_Id']}',
              '{$itemNFe['T008_Valor_Base_IPI']}',
              '{$itemNFe['T008_Aliquota_IPI']}',
              '{$itemNFe['T008_Valor_IPI']}',
              '{$itemNFe['T008_Situacao_Tributaria_IPI']}',
              '{$itemNFe['T008_Valor_Base_PIS']}',
              '{$itemNFe['T008_Aliquota_PIS']}',
              '{$itemNFe['T008_Valor_PIS']}',
              '{$itemNFe['T008_Situacao_Tributaria_PIS']}',
              '{$itemNFe['T008_Valor_Base_COFINS']}',
              '{$itemNFe['T008_Aliquota_COFINS']}',
              '{$itemNFe['T008_Valor_COFINS']}',
              '{$itemNFe['T008_Situacao_Tributaria_COFINS']}',
              '{$itemNFe['T008_Valor_Custo_Unitario']}',
              '{$itemNFe['T008_Valor_Preco_Unitario']}',
              '{$itemNFe['T008_Flag_Calculo_Manual']}'
            );
EOT;

    $result = $this->execute_sql($sql);

    unset($itemGravado);
    $itemGravado = array('id' => $g['mysqlLastId']);

    $totalizacao = "call T008_Gravar_Totalizacao_4('{$itemGravado['id']}');";
    $this->execute_sql($totalizacao);

    $this->locacaoProduto($documento, $itemNFe);

    $this->atualizarPedido($itemNFe);

    return $itemGravado;
  }

  protected function locacaoProduto($documento, $itemNFe) {
    if (!empty($itemNFe['T008_T006_Id'])) {
      return null;
    }

    global $g;
    $locacao = $this->getCamposLocacao($documento, $itemNFe);

    $sql = <<<EOT
            INSERT INTO T001 (
                T001_D024_Id_Cliente,
                T001_C004_Id,
                T001_T008_Id,
                T001_T007_Id,
                T001_D009_Id,
                T001_Valor_Preco_Unitario,
                T001_Valor_Custo_Unitario,
                T001_Codigo_Produto,
                T001_Data_Lancamento,
                T001_Descricao_Operacao,
                T001_Quantidade,
                T001_Flag_Operacao,
                T001_Flag_MMF,
                T001_Flag_Atualiza_Custo,
                T001_T066_Id,
                T001_D004_Id
            ) VALUES (
              '{$locacao['T001_D024_Id_Cliente']},
              '{$locacao['T001_C004_Id']},
              '{$locacao['T001_T008_Id']},
              '{$locacao['T001_T007_Id']},
              '{$locacao['T001_D009_Id']},
              '{$locacao['T001_Valor_Preco_Unitario']},
              '{$locacao['T001_Valor_Custo_Unitario']},
              '{$locacao['T001_Codigo_Produto']},
              '{$locacao['T001_Data_Lancamento']},
              '{$locacao['T001_Descricao_Operacao']},
              '{$locacao['T001_Quantidade']},
              '{$locacao['T001_Flag_Operacao']},
              '{$locacao['T001_Flag_MMF']},
              '{$locacao['T001_Flag_Atualiza_Custo']},
              '{$locacao['T001_T066_Id']},
              '{$locacao['T001_D004_I']}
            );
EOT;

    $this->execute_sql($sql);

    unset($itemGravado);
    $itemGravado = array('id' => $g['mysqlLastId']);

    return $itemGravado;
  }

  protected function atualizarPedido($itemNFe) {
    if (empty($itemNFe['T008_T006_Id'])) {
      return null;
    }

    $sql = "UPDATE T006 SET
              T006_Flag_Pdv_Importado = 'S'
            WHERE T006_Id = '{$itemNFe['T008_T006_Id']}'";

    $this->execute_sql($sql);
  }

  protected function calcularNFe($notaGravada) {
    global $g;

    $totalizacao = "call T007_Gravar_Totalizacao_4('{$notaGravada['id']}');";
    $this->execute_sql($totalizacao);

    $sqlNota = "SELECT IFNULL((T007_Valor_Frete + T007_Valor_Despesas_Acessorias + T007_Valor_Seguro) / SUM(T008_Valor_Total_Preco), 0) AS DESPESA,
                       IFNULL(T007_Valor_Despesas_Acessorias / SUM(T008_Valor_Total_Preco), 0)                                          AS DESPESA_ACESSORIA,
                       IFNULL(T007_Valor_Seguro / SUM(T008_Valor_Total_Preco), 0)                                                       AS SEGURO,
                       IFNULL(T007_Valor_Frete / SUM(T008_Valor_Total_Preco), 0)                                                        AS FRETE
                  FROM T007
                  LEFT JOIN T008 ON T008_T007_Id = T007_Id
                 WHERE T007_Id = '{$notaGravada['id']}'
                 GROUP BY T007_Id";

    $camposNota = $this->fetch_row($this->execute_sql($sqlNota));

    $sqlItem = "SELECT D006_Flag_Somar_IPI_Base_ICMS,
                       D006_Flag_Somar_Despesas_Base_IPI,
                       D006_Flag_Forma_Calculo_COFINS,
                       D006_Flag_Forma_Calculo_PIS,
                       IFNULL(T134_Reducao_Base_ICMS, 0) AS T134_Reducao_Base_ICMS,
                       IFNULL(T134_Id, 0)                AS T134_Id,
                       D006_Flag_Isento_ICMS
                  FROM T008
                  LEFT JOIN T007 ON T007_Id=T008_T007_Id
                  LEFT JOIN D006 ON D006_Id=T008_D006_Id
                  LEFT JOIN T134 ON T134_Id=T007_T134_Id
                 WHERE T008_T007_Id = '{$notaGravada['id']}'
                 GROUP BY T008_Id";

    $query = $this->execute_sql($sqlItem);

    while ($item = $this->fetch_row($query)) {
      $updateItem = "UPDATE T008 SET
                       T008_Percentual_Desconto                = IF(T008_Valor_Preco_Original > 0, (1 - (T008_Valor_Preco_Sem_Desconto_Unitario / T008_Valor_Preco_Original)) * 100, 0),
                       T008_Valor_Total_Custo                  = T008_Valor_Custo_Unitario * T008_Quantidade,
                       T008_Valor_Total_Despesas               = T008_Valor_Total_Preco * {$camposNota['DESPESA']},
                       T008_Valor_Total_Despesas_Acessorias    = IF((T008_Valor_Total_Preco * {$camposNota['DESPESA_ACESSORIA']}) < 0.01 and {$camposNota['DESPESA_ACESSORIA']} > 0, 0.01, T008_Valor_Total_Preco * {$camposNota['DESPESA_ACESSORIA']}),
                       T008_Valor_Total_Frete                  = IF({$camposNota['FRETE']} > 0 and (T008_Valor_Total_Preco * {$camposNota['FRETE']}) <= 0.01, 0.01, T008_Valor_Total_Preco * {$camposNota['FRETE']}),
                       T008_Valor_Total_Seguro                 = T008_Valor_Total_Preco * {$camposNota['SEGURO']},
                       T008_Valor_Base_IPI                     = IF(T008_Aliquota_IPI > 0, (T008_Valor_Total_Preco +
                                                                                            T008_Valor_Total_Imposto_Importacao +
                                                                                            IF('{$item['D006_Flag_Somar_Despesas_Base_IPI']}' = 'S', T008_Valor_Total_Frete +
                                                                                                                                                     T008_Valor_Total_Seguro +
                                                                                                                                                     T008_Valor_Total_Despesas_Acessorias, 0)
                                                                                           ) * IF(T008_Percentual_Reducao_IPI > 0, (T008_Percentual_Reducao_IPI / 100), 1), 0),
                       T008_Valor_IPI                          = ROUND(T008_Valor_Base_IPI * (T008_Aliquota_IPI / 100), 2),
                       T008_Valor_Despesas_Unitario            = T008_Valor_Total_Despesas / T008_Quantidade,
                       T008_Valor_Despesas_Acessorias_Unitario = T008_Valor_Total_Despesas_Acessorias / T008_Quantidade,
                       T008_Valor_Frete_Unitario               = T008_Valor_Total_Frete / T008_Quantidade,
                       T008_Valor_Seguro_Unitario              = T008_Valor_Total_Seguro / T008_Quantidade,
                       T008_Peso_Total                         = T008_Quantidade * T008_Peso_Unitario,
                       T008_Peso_Bruto_Total                   = T008_Quantidade * T008_Peso_Bruto,
                       T008_Valor_Total_Custo_Final            = (T008_Valor_Total_Preco + T008_Valor_Total_Despesas + IF('{$item['D006_Flag_Somar_IPI_Base_ICMS']}' = 'S', T008_Valor_IPI, 0)),
                       T008_Valor_Custo_Final_Unitario         = T008_Valor_Total_Custo_Final / T008_Quantidade,
                       T008_Valor_Base_ICMS_Sem_Reducao        = IF(T008_Aliquota_ICMS > 0, T008_Valor_Total_Custo_Final + IF('{$item['D006_Flag_Somar_IPI_Base_ICMS']}' = 'S',T008_Valor_IPI, 0), 0),
                       T008_Valor_Base_ICMS                    = IF(T008_Aliquota_ICMS > 0, T008_Valor_Base_ICMS_Sem_Reducao * IF(T008_Percentual_Reducao_ICMS > 0, ((100 - T008_Percentual_Reducao_ICMS) / 100), 1), 0),
                       T008_Valor_Base_ICMS_Diferido           = T008_Valor_Base_ICMS_Sem_Reducao - T008_Valor_Base_ICMS,
                       T008_Valor_Base_ICMS_Desonerado         = IF(T008_Aliquota_ICMS_Oculto > 0 AND '{$item['D006_Flag_Isento_ICMS']}' = 'D', T008_Valor_Total_Custo_Final +
                                                                                                                                                IF('{$item['D006_Flag_Somar_IPI_Base_ICMS']}' = 'S', T008_Valor_IPI, 0) -
                                                                                                                                                IF(T008_Percentual_Reducao_ICMS > 0,T008_Valor_Base_ICMS, 0), 0),
                       T008_Valor_ICMS                         = T008_Valor_Base_ICMS * (T008_Aliquota_ICMS / 100),
                       T008_Valor_ICMS_Desonerado              = T008_Valor_Base_ICMS_Desonerado * (T008_Aliquota_ICMS_Oculto / 100),
                       T008_Valor_Lucro                        = ROUND((T008_Valor_Total_Custo * T008_IPV) - (T008_Valor_Total_Custo * T008_IPV_Sem_Lucro), 2),
                       T008_Valor_Base_ICMS_Substituicao       = IF(T008_Flag_ST = 'S', (T008_Valor_Total_Preco + T008_Valor_Total_Despesas + T008_Valor_IPI) * ((T008_ST_VA / 100) + 1), 0),
                       T008_Valor_ICMS_Substituicao            = (T008_Valor_Base_ICMS_Substituicao * (T008_ST_ICMS_Interno / 100)),
                       T008_Valor_ICMS_Substituicao_Retencao   = ((T008_Valor_Base_ICMS_Substituicao * IF(T008_Percentual_Reducao_ICMS_ST > 0, ((100 - T008_Percentual_Reducao_ICMS_ST) / 100), 1)) * (T008_ST_ICMS_Interno / 100)) -
                                                                 ((T008_Valor_Total_Custo_Final * IF(T008_Percentual_Reducao_ICMS > 0, ((100 - T008_Percentual_Reducao_ICMS) / 100), 1)) * T008_ICMS_Inter_Estadual / 100),
                       T008_Valor_ICMS_Substituicao_Retencao   = IF(T008_Valor_ICMS_Substituicao_Retencao < 0, 0, T008_Valor_ICMS_Substituicao_Retencao),
                       T008_Valor_Base_ICMS_Interno            = (((T008_Valor_Total_Preco + T008_Valor_Total_Despesas + T008_Valor_IPI) * ((T008_ST_VA / 100) + 1)) * IF(T008_Percentual_Reducao_ICMS_ST > 0, ((100 - T008_Percentual_Reducao_ICMS_ST) / 100), 1)),
                       T008_Valor_ICMS_Interno                 = (T008_Valor_Base_ICMS_Interno * (T008_ST_ICMS_Interno / 100)),
                       T008_Valor_Base_ICMS_Inter_Estadual     = (T008_Valor_Total_Custo_Final * IF(T008_Percentual_Reducao_ICMS > 0, ((100 - T008_Percentual_Reducao_ICMS) / 100), 1)),
                       T008_Valor_ICMS_Inter_Estadual          = ((T008_Valor_Base_ICMS_Inter_Estadual * T008_ICMS_Inter_Estadual) / 100),
                       T008_Valor_Base_ICMS_Impresso           = IF(SUBSTRING(T008_Flag_Situacao_Tributaria, 2, 2) = '51' OR SUBSTRING(T008_Flag_Situacao_Tributaria, 2 , 2) = '90', T008_Valor_Base_ICMS_Sem_Reducao, T008_Valor_Base_ICMS),
                       T008_Valor_Base_PIS                     = IF(T008_Flag_Isento_PIS = 'S', 0, IF('{$item['D006_Flag_Forma_Calculo_PIS']}' = 'L' , IF(T008_Valor_Total_Custo < T008_Valor_Total_Preco,T008_Valor_Total_Preco - T008_Valor_Total_Custo, 0), T008_Valor_Total_Preco)),
                       T008_Aliquota_PIS                       = IF(T008_Flag_Isento_PIS = 'S', 0, T008_Aliquota_PIS),
                       T008_Valor_PIS                          = IF(T008_Aliquota_PIS > 0, T008_Valor_Base_PIS * (T008_Aliquota_PIS / 100), 0),
                       T008_Valor_Base_COFINS                  = IF(T008_Flag_Isento_COFINS = 'S', 0, IF('{$item['D006_Flag_Forma_Calculo_COFINS']}' = 'L', IF(T008_Valor_Total_Custo < T008_Valor_Total_Preco, T008_Valor_Total_Preco - T008_Valor_Total_Custo, 0), T008_Valor_Total_Preco)),
                       T008_Aliquota_COFINS                    = IF(T008_Flag_Isento_COFINS = 'S', 0, T008_Aliquota_COFINS),
                       T008_Valor_COFINS                       = IF(T008_Aliquota_COFINS > 0, T008_Valor_Base_COFINS * (T008_Aliquota_COFINS / 100), 0),
                       T008_Valor_ICMS_Credito                 = IF('{$item['T134_Id']}' <= 0 OR '{$item['T134_Id']}' = '' OR IFNULL('{$item['T134_Id']}', 0) <= 0,
                                                                    T008_Valor_ICMS_Credito,
                                                                    IF(T008_Aliquota_ICMS_Credito > 0, (T008_Valor_Total_Custo_Final + IF('{$item['D006_Flag_Somar_IPI_Base_ICMS']}' = 'S', T008_Valor_IPI, 0)) -
                                                                                                       ((T008_Valor_Total_Custo_Final + IF('{$item['D006_Flag_Somar_IPI_Base_ICMS']}' = 'S', T008_Valor_IPI, 0)) *
                                                                                                        IF({$item['T134_Reducao_Base_ICMS']} > 0, ({$item['T134_Reducao_Base_ICMS']} / 100), 1)), 0) * (T008_Aliquota_ICMS_Credito / 100)),
                       T008_Aliquota_IRPJ                      = IF(T008_Flag_Isento_IRPJ = 'S', 0, T008_Aliquota_IRPJ),
                       T008_Aliquota_CSLL                      = IF(T008_Flag_Isento_CSLL = 'S', 0, T008_Aliquota_CSLL),
                       T008_Valor_Base_IRPJ                    = T008_Valor_Total_Preco,
                       T008_Valor_IRPJ                         = IF(T008_Aliquota_IRPJ > 0, T008_Valor_Base_IRPJ * (T008_Aliquota_IRPJ / 100), 0),
                       T008_Valor_Base_CSLL                    = T008_Valor_Total_Preco,
                       T008_Valor_CSLL                         = IF(T008_Aliquota_CSLL > 0, T008_Valor_Base_CSLL * (T008_Aliquota_CSLL / 100), 0),
                       T008_Total_Cubagem                      = IFNULL(T008_Cubagem_Unitaria, 0) * IFNULL(T008_Quantidade, 0),
                       T008_Valor_Trib_Transparencia           = IFNULL(T008_Valor_Total_Preco_Sem_Desconto,0)*(IFNULL(T008_Percentual_Trib_Transparencia,0)/100),
                       T008_IPV                                = (T008_Valor_Preco_Unitario -
                                                                 IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(T008_Valor_ICMS / T008_Quantidade, 0), 0) -
                                                                 IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(T008_Valor_PIS / T008_Quantidade, 0), 0) -
                                                                 IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(T008_Valor_COFINS / T008_Quantidade, 0), 0) -
                                                                 IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(T008_Valor_IRPJ / T008_Quantidade, 0), 0) -
                                                                 IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(T008_Valor_CSLL / T008_Quantidade, 0), 0)) / T008_Valor_Custo_Unitario,
                       T008_Percentual_Margem                  = ROUND((((T008_Valor_Preco_Unitario-
                                                                          IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                                                                          IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                                                                          IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                                                                          IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                                                                          IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0)) - T008_Valor_Custo_Unitario) /
                                                                         (T008_Valor_Preco_Unitario -
                                                                          IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(T008_Valor_ICMS /T008_Quantidade, 0), 0) -
                                                                          IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(T008_Valor_PIS / T008_Quantidade, 0), 0) -
                                                                          IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(T008_Valor_COFINS / T008_Quantidade, 0), 0) -
                                                                          IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(T008_Valor_IRPJ / T008_Quantidade, 0), 0) -
                                                                          IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(T008_Valor_CSLL / T008_Quantidade, 0), 0))) * 100, 2)
                     WHERE T008_Id = '{$item['T008_Id']}'";

      $this->execute_sql($updateItem);
    }

    $sqlSumItens = "SELECT IFNULL(SUM(T008_Valor_Total_Custo), 0)                  AS T008_Valor_Total_Custo,
                           IFNULL(SUM(ROUND(T008_Valor_Base_ICMS, 2)), 0)          AS T008_Valor_Base_ICMS,
                           IFNULL(SUM(T008_Valor_Base_ICMS_Sem_Reducao), 0)        AS T008_Valor_Base_ICMS_Sem_Reducao,
                           IFNULL(SUM(T008_Valor_Base_ICMS_Diferido), 0)           AS T008_Valor_Base_ICMS_Diferido,
                           IFNULL(SUM(ROUND(T008_Valor_ICMS, 2)), 0)               AS T008_Valor_ICMS,
                           IFNULL(SUM(T008_Valor_ICMS_Desonerado), 0)              AS T008_Valor_ICMS_Desonerado,
                           IFNULL(SUM(T008_Valor_Base_IPI), 0)                     AS T008_Valor_Base_IPI,
                           IFNULL(SUM(T008_Valor_IPI), 0)                          AS T008_Valor_IPI,
                           IFNULL(SUM(ROUND(T008_Valor_Base_ICMS_Impresso, 2)), 0) AS T008_Valor_Base_ICMS_Impresso,
                           IFNULL(SUM(T008_Valor_Base_ICMS_Substituicao), 0)       AS T008_Valor_Base_ICMS_Substituicao,
                           IFNULL(SUM(T008_Valor_ICMS_Substituicao), 0)            AS T008_Valor_ICMS_Substituicao,
                           IFNULL(SUM(T008_Valor_ICMS_Substituicao_Retencao), 0)   AS T008_Valor_ICMS_Substituicao_Retencao,
                           IFNULL(SUM(T008_Peso_Total), 0)                         AS T008_Peso_Total,
                           IFNULL(SUM(T008_Peso_Bruto_Total), 0)                   AS T008_Peso_Bruto_Total,
                           IFNULL(SUM(T008_Valor_Total_Imposto_Importacao), 0)     AS T008_Valor_Total_Imposto_Importacao,
                           IFNULL(SUM(T008_Valor_COFINS), 0)                       AS T008_Valor_COFINS,
                           IFNULL(SUM(T008_Valor_PIS), 0)                          AS T008_Valor_PIS,
                           IFNULL(SUM(T008_Total_Cubagem), 0)                      AS T008_Total_Cubagem,
                           IFNULL(SUM(T008_Valor_Base_ICMS_Interno), 0)            AS T008_Valor_Base_ICMS_Interno,
                           IFNULL(SUM(T008_Valor_Base_ICMS_Inter_Estadual), 0)     AS T008_Valor_Base_ICMS_Inter_Estadual,
                           IFNULL(SUM(T008_Valor_ICMS_Interno), 0)                 AS T008_Valor_ICMS_Interno,
                           IFNULL(SUM(T008_Valor_ICMS_Inter_Estadual), 0)          AS T008_Valor_ICMS_Inter_Estadual,
                           IFNULL(SUM(T008_Valor_Trib_Transparencia), 0)           AS T008_Valor_Trib_Transparencia,
                           IFNULL(SUM(T008_Valor_IRPJ), 0)                         AS T008_Valor_IRPJ,
                           IFNULL(SUM(T008_Valor_Base_IRPJ), 0)                    AS T008_Valor_Base_IRPJ,
                           IFNULL(SUM(T008_Valor_CSLL), 0)                         AS T008_Valor_CSLL,
                           IFNULL(SUM(T008_Valor_Base_CSLL), 0)                    AS T008_Valor_Base_CSLL,
                           IFNULL(SUM(T008_Valor_ICMS_Credito), 0)                 AS T008_Valor_ICMS_Credito,
                           T008_Aliquota_ICMS_Credito                              AS T008_Aliquota_ICMS_Credito
                      FROM T008
                     WHERE T008_T007_Id= '{$notaGravada['id']}'";

    $camposItem = $this->fetch_row($this->execute_sql($sqlSumItens));

    $updateCapa = "UPDATE T007 SET
                     T007_Valor_Total_Custo                 = {$camposItem['T008_Valor_Total_Custo']},
                     T007_Valor_Total_Base_ICMS_Sem_Reducao = {$camposItem['T008_Valor_Base_ICMS_Sem_Reducao']},
                     T007_Valor_Total_Base_ICMS             = {$camposItem['T008_Valor_Base_ICMS']},
                     T007_Valor_Total_Base_ICMS_Diferido    = {$camposItem['T008_Valor_Base_ICMS_Diferido']},
                     T007_Valor_Total_Base_ICMS_Impresso    = {$camposItem['T008_Valor_Base_ICMS_Impresso']},
                     T007_Valor_Total_ICMS                  = {$camposItem['T008_Valor_ICMS']},
                     T007_Valor_Total_ICMS_Desonerado       = IFNULL({$camposItem['T008_Valor_ICMS_Desonerado']}, 0),
                     T007_Valor_Total_Base_IPI              = {$camposItem['T008_Valor_Base_IPI']},
                     T007_Valor_Total_IPI                   = {$camposItem['T008_Valor_IPI']},
                     T007_Valor_Total_Imposto_Importacao    = {$camposItem['T008_Valor_Total_Imposto_Importacao']},
                     T007_Valor_ISS                         = IF(T007_Aliquota_ISS > 0, T007_Valor_Mao_Obra * (T007_Aliquota_ISS / 100), 0),
                     T007_Total_Peso                        = {$camposItem['T008_Peso_Total']},
                     T007_Total_Peso_Bruto                  = {$camposItem['T008_Peso_Bruto_Total']},
                     T007_IPV_sem_icms                      = T007_IPV_Medio(T007_Id, 2),
                     T007_Valor_Base_ICMS_Substituicao      = {$camposItem['T008_Valor_Base_ICMS_Substituicao']},
                     T007_Valor_ICMS_Substituicao           = {$camposItem['T008_Valor_ICMS_Substituicao']},
                     T007_Valor_ICMS_Substituicao_Retencao  = IFNULL({$camposItem['T008_Valor_ICMS_Substituicao_Retencao']}, 0.00),
                     T007_Valor_Base_ICMS_Interno           = IFNULL({$camposItem['T008_Valor_Base_ICMS_Interno']}, 0),
                     T007_Valor_Base_ICMS_Inter_Estadual    = IFNULL({$camposItem['T008_Valor_Base_ICMS_Inter_Estadual']}, 0),
                     T007_Valor_ICMS_Interno                = IFNULL({$camposItem['T008_Valor_ICMS_Interno']}, 0),
                     T007_Valor_ICMS_Inter_Estadual         = IFNULL({$camposItem['T008_Valor_ICMS_Inter_Estadual']}, 0),
                     T007_Valor_Total_ICMS_Credito          = {$camposItem['T008_Valor_ICMS_Credito']},
                     T007_Aliquota_ICMS_Credito             = {$camposItem['T008_Aliquota_ICMS_Credito']},
                     T007_Valor_Total_COFINS                = {$camposItem['T008_Valor_COFINS']},
                     T007_Valor_Total_PIS                   = {$camposItem['T008_Valor_PIS']},
                     T007_Valor_Total_Base_IRPJ             = {$camposItem['T008_Valor_Base_IRPJ']},
                     T007_Valor_Total_IRPJ                  = {$camposItem['T008_Valor_IRPJ']},
                     T007_Valor_Total_Base_CSLL             = {$camposItem['T008_Valor_Base_CSLL']},
                     T007_Valor_Total_CSLL                  = {$camposItem['T008_Valor_CSLL']},
                     T007_Valor_Total_Trib_Transparencia    = {$camposItem['T008_Valor_Trib_Transparencia']},
                     T007_Total_Cubagem                     = IFNULL({$camposItem['T008_Total_Cubagem']}, 0),
                     T007_IPV                               = IF(T007_Valor_Total_Custo > 0, (T007_Valor_Total_Produtos -
                                                                                              IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_ICMS, 0), 0) -
                                                                                              IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(T007_Valor_Total_PIS, 0), 0) -
                                                                                              IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(T007_Valor_Total_COFINS, 0), 0) -
                                                                                              IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_IRPJ, 0), 0) -
                                                                                              IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_CSLL, 0), 0)) / T007_Valor_Total_Custo, 0),
                     T007_Percentual_Margem                 = ROUND((((T007_Valor_Total_Produtos -
                                                                       IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_ICMS, 0), 0) -
                                                                       IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(T007_Valor_Total_PIS, 0), 0) -
                                                                       IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(T007_Valor_Total_COFINS, 0), 0) -
                                                                       IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_IRPJ, 0), 0) -
                                                                       IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_CSLL, 0), 0)) - T007_Valor_Total_Custo) /
                                                                     (T007_Valor_Total_Produtos -
                                                                       IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_ICMS, 0), 0) -
                                                                       IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(T007_Valor_Total_PIS, 0), 0) -
                                                                       IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(T007_Valor_Total_COFINS, 0), 0) -
                                                                       IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_IRPJ, 0), 0) -
                                                                       IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_CSLL, 0), 0))) * 100, 2)
                   WHERE T007_Id='{$notaGravada['id']}'";

    $this->execute_sql($updateCapa);
  }

  protected function gravarTitulo($documento, $notaGravada) {
    $pagamentosLista = array();

    $planoContas     = $this->getPlanoContas();
    $contasBancarias = $this->getContasBancarias();

    foreach ($documento->PAGAMENTOS as $pagamento) {
      unset($notaPagamento);
      unset($pagamentoGravado);

      $portador         = $this->getPortador($pagamento);
      $contasReceber    = $this->getContasReceber($pagamento, $documento, $notaGravada, $planoContas, $contasBancarias, $portador);
      $pagamentoGravado = $this->gravarContasReceber($contasReceber);

      $notaPagamento->ID            = $pagamento->ID;
      $notaPagamento->ID_INTEGRACAO = $pagamentoGravado['id'];

      array_push($pagamentosLista, $notaPagamento);

      if ($portador['D027_Flag_Baixa_Automatica'] == 'S') {
        $this->gravarLancamento($contasReceber, $pagamentoGravado);
      }
    }

    return $pagamentosLista;
  }

  protected function gravarContasReceber($contasReceber) {
    global $g;

    $sql = <<<EOT
            INSERT INTO T002 (
              T002_D024_Id,
              T002_D027_Id,
              T002_T007_Id,
              T002_D014_Id,
              T002_D032_Id,
              T002_C004_Id,
              T002_D062_Id,
              T002_D007_Id,
              T002_Nome_Cliente,
              T002_Numero_Duplicata,
              T002_Numero_Documento,
              T002_Data_Emissao,
              T002_Data_Vencimento,
              T002_Data_Vencimento_Original,
              T002_Data_Recebimento,
              T002_Valor_Duplicata,
              T002_Valor_Juros,
              T002_Valor_Desconto,
              T002_Flag_Enviar_Banco_Protestar,
              T002_Valor_Total,
              T002_Data_Lancamento,
              T002_Hora_Lancamento,
              T002_Numero_Parcela,
              T002_Valor_Recebido_Total
            ) VALUES (
              '{$contasReceber['T002_D024_Id']}',
              '{$contasReceber['T002_D027_Id']}',
              '{$contasReceber['T002_T007_Id']}',
              '{$contasReceber['T002_D014_Id']}',
              '{$contasReceber['T002_D032_Id']}',
              '{$contasReceber['T002_C004_Id']}',
              '{$contasReceber['T002_D062_Id']}',
              '{$contasReceber['T002_D007_Id']}',
              '{$contasReceber['T002_Nome_Cliente']}',
              '{$contasReceber['T002_Numero_Duplicata']}',
              '{$contasReceber['T002_Numero_Documento']}',
              '{$contasReceber['T002_Data_Emissao']}',
              '{$contasReceber['T002_Data_Vencimento']}',
              '{$contasReceber['T002_Data_Vencimento_Original']}',
              '{$contasReceber['T002_Data_Recebimento']}',
              '{$contasReceber['T002_Valor_Duplicata']}',
              '{$contasReceber['T002_Valor_Juros']}',
              '{$contasReceber['T002_Valor_Desconto']}',
              '{$contasReceber['T002_Flag_Enviar_Banco_Protestar']}',
              '{$contasReceber['T002_Valor_Total']}',
              '{$contasReceber['T002_Data_Lancamento']}',
              '{$contasReceber['T002_Hora_Lancamento']}',
              '{$contasReceber['T002_Numero_Parcela']}',
              '{$contasReceber['T002_Valor_Recebido_Total']}'
            );
EOT;

    $this->execute_sql($sql);
    unset($pagamentoGravado);
    $pagamentoGravado = array('id' => $g['mysqlLastId']);

    return $pagamentoGravado;
  }

  protected function gravarLancamento($contasReceber, $pagamentoGravado) {
    $lancamento = $this->getLancamento($contasReceber, $pagamentoGravado);

    $sql = <<<EOT
            INSERT INTO T019 (
              T019_T007_Id,
              T019_D007_Id,
              T019_D027_Id,
              T019_C004_Id,
              T019_D024_Id,
              T019_D014_Id,
              T019_Complemento,
              T019_Data_Lancamento,
              T019_Referente,
              T019_Valor_Lancamento,
              T019_T002_Id
            ) VALUES (
              '{$lancamento['T019_T007_Id']}',
              '{$lancamento['T019_D007_Id']}',
              '{$lancamento['T019_D027_Id']}',
              '{$lancamento['T019_C004_Id']}',
              '{$lancamento['T019_D024_Id']}',
              '{$lancamento['T019_D014_Id']}',
              '{$lancamento['T019_Complemento']}',
              '{$lancamento['T019_Data_Lancamento']}',
              '{$lancamento['T019_Referente']}',
              '{$lancamento['T019_Valor_Lancamento']}',
              '{$lancamento['T019_T002_Id']}'
            );
EOT;

    $this->execute_sql($sql);

    $totalizacao = "call T002_Gravar_Totalizacao('{$pagamentoGravado['id']}');";
    $this->execute_sql($totalizacao);

    $baixaTitulo = "call T002_Baixar_Titulo('{$pagamentoGravado['id']}', '{$lancamento['T019_Data_Lancamento']}');";
    $this->execute_sql($baixaTitulo);

    return $lancamento;
  }

  protected function getCamposNota($documento) {
    $empresa = $this->consultarEmpresa($documento->ID_EMPRESA);

    $nota = array(
      'T007_C004_Id'                            => $documento->ID_EMPRESA,
      'T007_D024_Id'                            => $documento->ID_PESSOA,
      'T007_D006_Id'                            => $documento->ID_CFOP,
      'T007_C007_Id_Vendedor_Interno'           => $documento->ID_VENDEDOR,
      'T007_T005_Id'                            => $documento->ID_PEDIDO,
      'T007_Numero_Nota_Fiscal'                 => $documento->NUMERO,
      'T007_Serie'                              => $documento->SERIE,
      'T007_Data_Emissao'                       => $this->getDataUTC($documento->EMISSAO_DATA),
      'T007_Data_Saida'                         => $this->getDataUTC($documento->EMISSAO_DATA),
      'T007_Hora_Saida'                         => $this->getHourUTC($documento->EMISSAO_DATA),
      'T007_Data_Envio_XML'                     => $this->getDataUTC($documento->EMISSAO_DATA),
      'T007_TX_Timestamp_Envio_Sefaz'           => $this->getTimeStampUTC($documento->EMISSAO_DATA),
      'T007_Valor_Total'                        => $documento->VALOR_PRODUTOS,
      'T007_Valor_Total_Preco'                  => floatval($documento->VALOR_PRODUTOS) - floatval($documento->VALOR_DESCONTO),
      'T007_Valor_Total_Produtos'               => floatval($documento->VALOR_PRODUTOS) - floatval($documento->VALOR_DESCONTO),
      'T007_Valor_Total_Produtos_Sem_Desconto'  => $documento->VALOR_PRODUTOS,
      'T007_Valor_Desconto'                     => $documento->VALOR_DESCONTO,
      'T007_Arquivo_XML'                        => $this->base64Decode($documento->ARQUIVO_XML),
      'T007_Chave_Acesso_Nfe'                   => $documento->CHAVE,
      'T007_Ambiente_NFe'                       => $this->getAmbienteNFe($documento->CHAVE),
      'T007_Flag_Contingencia'                  => $documento->TRANSMISSAO_STATUS == '6'  ? 'S' : 'N',
      'T007_Flag_Inutilizado'                   => $documento->TRANSMISSAO_STATUS == '9'  ? 'S' : 'N',
      'T007_Flag_Denegado'                      => $documento->TRANSMISSAO_STATUS == '12' ? 'S' : 'N',
      'T007_Valor_Total_Base_ICMS'              => $documento->VALOR_BASE_ICMS,
      'T007_Valor_Total_ICMS'                   => $documento->VALOR_ICMS,
      'T007_Aliquota_ICMS'                      => $this->getPorcentagem($documento->VALOR_BASE_ICMS, $documento->VALOR_ICMS),
      'T007_Valor_Total_Base_IPI'               => $documento->VALOR_BASE_IPI,
      'T007_Valor_Total_IPI'                    => $documento->VALOR_IPI,
      'T007_Aliquota_IPI'                       => $this->getPorcentagem($documento->VALOR_BASE_IPI, $documento->VALOR_IPI),
      'T007_Valor_Total_PIS'                    => $documento->VALOR_PIS,
      'T007_Valor_Total_COFINS'                 => $documento->VALOR_COFINS,
      'T007_Cliente_Razao'                      => $documento->PESSOA_NOME_RAZAO,
      'T007_Cliente_CNPJ_CPF'                   => $documento->PESSOA_CPF_CNPJ,
      'T007_Cliente_Inscricao_Estadual'         => $documento->PESSOA_RG_IE,
      'T007_Cliente_Endereco'                   => $documento->PESSOA_ENDERECO,
      'T007_Cliente_Endereco_Numero'            => $documento->PESSOA_NUMERO,
      'T007_Cliente_Complemento'                => $documento->PESSOA_COMPLEMENTO,
      'T007_Cliente_Bairro'                     => $documento->PESSOA_BAIRRO,
      'T007_Cliente_Cidade'                     => $documento->PESSOA_CIDADE,
      'T007_Cliente_Estado'                     => $documento->PESSOA_UF,
      'T007_Data_Hora_Protocolo_Nfe'            => $this->getTimeStampUTC($documento->NFE_RECEBIMENTO_DATA),
      'T007_Numero_Protocolo_Nfe'               => $documento->NFE_PROTOCOLO,
      'T007_Empresa_Usuaria_Nome'               => $empresa['T007_Empresa_Usuaria_Nome'],
      'T007_Empresa_Usuaria_Endereco'           => $empresa['T007_Empresa_Usuaria_Endereco'],
      'T007_Empresa_Usuaria_Numero'             => $empresa['T007_Empresa_Usuaria_Numero'],
      'T007_Empresa_Usuaria_Bairro'             => $empresa['T007_Empresa_Usuaria_Bairro'],
      'T007_Empresa_Usuaria_Cep'                => $empresa['T007_Empresa_Usuaria_Cep'],
      'T007_Empresa_Usuaria_Cidade'             => $empresa['T007_Empresa_Usuaria_Cidade'],
      'T007_Empresa_Usuaria_Codigo_Cidade_IBGE' => $empresa['T007_Empresa_Usuaria_Codigo_Cidade_IBGE'],
      'T007_Empresa_Usuaria_Uf'                 => $empresa['T007_Empresa_Usuaria_Uf'],
      'T007_Empresa_Usuaria_Estado'             => $empresa['T007_Empresa_Usuaria_Estado'],
      'T007_Empresa_Usuaria_Codigo_Estado_IBGE' => $empresa['T007_Empresa_Usuaria_Codigo_Estado_IBGE'],
      'T007_Empresa_Usuaria_Telefone'           => $empresa['T007_Empresa_Usuaria_Telefone'],
      'T007_Empresa_Usuaria_Fax'                => $empresa['T007_Empresa_Usuaria_Fax'],
      'T007_Empresa_Usuaria_Cnpj'               => $empresa['T007_Empresa_Usuaria_Cnpj'],
      'T007_Empresa_Usuaria_Inscricao_Estadual' => $empresa['T007_Empresa_Usuaria_Inscricao_Estadual'],
      'T007_Empresa_Usuaria_CNAE_Fiscal'        => $empresa['T007_Empresa_Usuaria_CNAE_Fiscal'],
      'T007_Versao_NFe'                         => $empresa['T007_Versao_NFe'],
      'T007_TX_Resposta_Codigo'                 => '9',
      'T007_Flag_Indicador_Presenca'            => '1',
      'T007_Flag_Entrada_Saida'                 => 'S',
      'T007_Flag_ACP'                           => '8',
      'T007_Casas_Decimais_Preco'               => '4',
      'T007_Flag_Calcular'                      => 'N',
      'T007_Flag_Cancelada'                     => 'N'
    );

    return $nota;
  }

  protected function getCamposItem($notaGravada, $documento, $item) {
    $produtoEmpresa = $this->consultarProdutoEmpresa($documento, $item);

    $itemNFe = array(
      'T008_T007_Id'                           => $notaGravada['id'],
      'T008_C004_Id'                           => $documento->ID_EMPRESA,
      'T008_D009_Id'                           => $produtoEmpresa['D009_Id'],
      'T008_D037_Id'                           => $produtoEmpresa['D001_D037_Id'],
      'T008_D005_Id'                           => $produtoEmpresa['D001_D005_Id'],
      'T008_D006_Id'                           => $produtoEmpresa['D001A_D006_Id_PAF_CFOP'],
      'T008_Codigo_Produto'                    => $produtoEmpresa['D001_Codigo_Produto'],
      'T008_Descricao_Produto'                 => $produtoEmpresa['D001_Descricao_Produto'],
      'T008_Valor_Custo_Unitario'              => $produtoEmpresa['D009_Valor_Custo_Unitario'],
      'T008_T006_Id'                           => $item->ID_ITEM_PEDIDO,
      'T008_Quantidade'                        => $item->QUANTIDADE,
      'T008_Valor_Preco_Sem_Desconto_Unitario' => $item->VALOR_UNITARIO,
      'T008_Valor_Preco_Unitario'              => floatval($item->VALOR_UNITARIO) - floatval($item->VALOR_DESCONTO),
      'T008_Valor_Desconto_Unitario'           => $item->VALOR_DESCONTO,
      'T008_Percentual_Desconto'               => $this->getPorcentagem($documento->VALOR_UNITARIO, $documento->VALOR_DESCONTO),
      'T008_Valor_Base_ICMS'                   => $item->VALOR_BASE_ICMS,
      'T008_Valor_ICMS'                        => $item->VALOR_ICMS,
      'T008_Aliquota_ICMS'                     => $this->getPorcentagem($documento->VALOR_BASE_ICMS, $documento->VALOR_ICMS),
      'T008_D059_Id'                           => $produtoEmpresa['D001A_D059_Id_PAF_CST_ICMS'],
      'T008_Valor_Base_IPI'                    => $item->VALOR_BASE_IPI,
      'T008_Valor_IPI'                         => $item->VALOR_IPI,
      'T008_Aliquota_IPI'                      => $this->getPorcentagem($documento->VALOR_BASE_IPI, $documento->VALOR_IPI),
      'T008_Situacao_Tributaria_IPI'           => $item->CST_IPI,
      'T008_Valor_Base_PIS'                    => $item->VALOR_BASE_PIS,
      'T008_Valor_PIS'                         => $item->VALOR_PIS,
      'T008_Aliquota_PIS'                      => $this->getPorcentagem($documento->VALOR_BASE_PIS, $documento->VALOR_PIS),
      'T008_Situacao_Tributaria_PIS'           => $item->CST_PIS,
      'T008_Valor_Base_COFINS'                 => $item->VALOR_BASE_COFINS,
      'T008_Valor_COFINS'                      => $item->VALOR_COFINS,
      'T008_Aliquota_COFINS'                   => $this->getPorcentagem($documento->VALOR_BASE_COFINS, $documento->VALOR_COFINS),
      'T008_Situacao_Tributaria_COFINS'        => $item->CST_COFINS,
      'T008_Flag_Calculo_Manual'               => 'S'
    );

    return $itemNFe;
  }

  protected function getCamposLocacao($documento, $itemNFe) {
    $locacao = array(
      'T001_D024_Id_Cliente'      => $documento->ID_PESSOA,
      'T001_C004_Id'              => $documento->ID_EMPRESA,
      'T001_T008_Id'              => $itemNFe['T008_T007_Id'],
      'T001_T007_Id'              => $itemNFe['id'],
      'T001_D009_Id'              => $itemNFe['T008_D009_Id'],
      'T001_Valor_Preco_Unitario' => $itemNFe['T008_Valor_Preco_Unitario'],
      'T001_Valor_Custo_Unitario' => $itemNFe['T008_Valor_Custo_Unitario'],
      'T001_Codigo_Produto'       => $itemNFe['T008_Codigo_Produto'],
      'T001_Data_Lancamento'      => $this->getDataUTC($documento->EMISSAO_DATA),
      'T001_Descricao_Operacao'   => 'PAF/NFCe - {$documento->NUMERO}/{$documento->SERIE}',
      'T001_Quantidade'           => $itemNFe['T008_Quantidade'],
      'T001_Flag_Operacao'        => 'S',
      'T001_Flag_MMF'             => 'S',
      'T001_Flag_Atualiza_Custo'  => 'N',
      'T001_T066_Id'              => null,
      'T001_D004_Id'              => null
    );

    return $locacao;
  }

  protected function getPlanoContas() {
    $sql = "SELECT d014.*
              FROM d014
             WHERE d014.D014_Flag_Emite_Cupom = 'S'
             LIMIT 1";

    $result = $this->fetch_row($this->execute_sql($sql));

    if (isset($result)) {
      return $result;
    } else {
      return null;
    }
  }

  protected function getContasBancarias() {
    $sql = "SELECT d007.*
              FROM d007
             WHERE d007.D007_Flag_Emite_Cupom = 'S'
             LIMIT 1";

    $result = $this->fetch_row($this->execute_sql($sql));

    if (isset($result)) {
      return $result;
    } else {
      return null;
    }
  }

  protected function getPortador($pagamento) {
    $sql = "SELECT d027.*
              FROM d027
             WHERE d027.D027_Id = '{$pagamento->ID_FORMA_PAGAMENTO_INTEGRACAO}'
             LIMIT 1";

    $result = $this->fetch_row($this->execute_sql($sql));

    if (isset($result)) {
      return $result;
    } else {
      return null;
    }
  }

  protected function getContasReceber($pagamento, $documento, $notaGravada, $planoContas, $contasBancarias, $portador) {
    $contasReceber = array(
      'T002_D024_Id'                     => $documento->ID_PESSOA,
      'T002_D027_Id'                     => $portador['D027_Id'],
      'T002_T007_Id'                     => $notaGravada['id'],
      'T002_D014_Id'                     => $planoContas['D014_Id'],
      'T002_D032_Id'                     => $planoContas['D014_D032_Id'],
      'T002_C004_Id'                     => $documento->ID_EMPRESA,
      'T002_D062_Id'                     => $contasBancarias['D007_D062_Id'],
      'T002_D007_Id'                     => $contasBancarias['D007_Id'],
      'T002_Nome_Cliente'                => $documento->PESSOA_NOME_RAZAO,
      'T002_Numero_Duplicata'            => $documento->NUMERO . '-' . $documento->SERIE . '/' . $pagamento->PARCELA,
      'T002_Numero_Documento'            => $documento->NUMERO . '-' . $documento->SERIE . '/' . $pagamento->PARCELA,
      'T002_Data_Emissao'                => $this->getDataUTC($documento->EMISSAO_DATA),
      'T002_Data_Vencimento'             => $this->getDataUTC($documento->EMISSAO_DATA),
      'T002_Data_Vencimento_Original'    => $this->getDataUTC($documento->EMISSAO_DATA),
      'T002_Data_Recebimento'            => '0000-00-00',
      'T002_Valor_Duplicata'             => $pagamento->VALOR,
      'T002_Valor_Juros'                 => 0,
      'T002_Valor_Desconto'              => 0,
      'T002_Flag_Enviar_Banco_Protestar' => 0,
      'T002_Valor_Total'                 => $pagamento->VALOR,
      'T002_Data_Lancamento'             => $this->getDataUTC($documento->EMISSAO_DATA),
      'T002_Hora_Lancamento'             => $this->getHourUTC($documento->EMISSAO_DATA),
      'T002_Numero_Parcela'              => 1,
      'T002_Valor_Recebido_Total'        => $pagamento->VALOR
    );

    return $contasReceber;
  }

  protected function getLancamento($contasReceber, $pagamentoGravado) {
    $lancamento = array(
      'T019_T007_Id'          => $contasReceber['T002_T007_Id'],
      'T019_D007_Id'          => $contasReceber['T002_D007_Id'],
      'T019_D027_Id'          => $contasReceber['T002_D027_Id'],
      'T019_C004_Id'          => $contasReceber['T002_C004_Id'],
      'T019_D024_Id'          => $contasReceber['T002_D024_Id'],
      'T019_D014_Id'          => $contasReceber['T002_D014_Id'],
      'T019_Complemento'      => $contasReceber['T002_Nome_Cliente'],
      'T019_Data_Lancamento'  => $contasReceber['T002_Data_Lancamento'],
      'T019_Referente'        => $contasReceber['T002_Numero_Duplicata'],
      'T019_Valor_Lancamento' => $contasReceber['T002_Valor_Duplicata'],
      'T019_T002_Id'          => $pagamentoGravado['id']
    );

    return $lancamento;
  }

  protected function gravarXML($documento) {
    global $g;

    $diretorioNFe      = $g['pathDadosAntigo'] . "/nfe/xml/autorizado/";
    $arquivoXML        = $this->base64Decode($documento->ARQUIVO_XML);
    $dataEmissao       = str_replace('/', '-', substr($this->getDataUTC($documento->EMISSAO_DATA), 0, 7));
    $protocolo         = $documento->NFE_PROTOCOLO;

    $arquivoDiretorio  = $diretorioNFe . $dataEmissao . '/';
    $arquivoNome       = $arquivoDiretorio . $protocolo . '.xml';

    if (!is_dir($arquivoDiretorio)) {
      mkdir($arquivoDiretorio, 0777, true);
    }

    if (file_exists($arquivoNome)) {
      unlink($arquivoNome);
    }

    file_put_contents($arquivoNome, $arquivoXML);
  }

  protected function getDataUTC($strData) {
    $dataArray = explode(' ', $strData);

    if (count($dataArray) > 0) {
      $data = explode('/', $dataArray[0]);
      if (count($data) > 2) {
        return $data[2] . '/' . $data[1] . '/' . $data[0];
      } else {
        return $dataArray[0];
      }
    } else {
      return null;
    }
  }

  protected function getHourUTC($strData) {
    $dataArray = explode(' ', $strData);

    if (count($dataArray) > 0) {
      return $dataArray[1];
    } else {
      return null;
    }
  }

  protected function getTimeStampUTC($strData) {
    $dataArray = explode(' ', $strData);

    if (count($dataArray) > 0) {
      $data = explode('/', $dataArray[0]);
      if (count($data) > 2) {
        return $data[2] . '/' . $data[1] . '/' . $data[0] . ' ' . $dataArray[1];
      } else {
        return $dataArray[0];
      }
    } else {
      return null;
    }
  }

  protected function getAmbienteNFe($chave) {
    if (!empty($chave)) {
      return substr($chave, 34, -9);
    } else {
      return 1;
    }
  }
}
