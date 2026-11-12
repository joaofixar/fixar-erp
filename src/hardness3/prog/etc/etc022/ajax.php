<?php

namespace hardness;

$resposta = array('code' => true, 'data' => array());

switch($g['r'][3]) { 

    case 'etc022CodigoOrigem':

        require_once('bibliotecas/classes/CAD002.php');  
        $CAD002 = new CAD002();        
        
        $locacaoOrigem = isset($r_origemId) ? $r_origemId : 'INDEFINIDO';
        $origin = isset($r_originValue) ? $r_originValue : 0;

        $empresaAtual = $g['empresaAtual'];
        $cod = ltrim($r_codbarra, '0');

        $D004  = mysql_query("SELECT D004_Id as id, D004_Local as local, D004_JSON FROM D004 WHERE D004_Id = '{$cod}' AND D004_C004_Id = '{$empresaAtual}'");
        $mD004 = mysql_fetch_assoc($D004);
        
        if ($origin == "0" || $origin == "2") {
            
            $resposta['info'] = "Vincule uma Locação de Origem!!";
            $resposta['status'] = "2";
            $resposta['code'] = false;
        
        } else {
            
            $D001 = mysql_query("
                SELECT
                 D001_Id, T066_Id, D009_Id,
                 D082_Marca as MARCA, 
                 D001_Codigo_Barras as EAN,
                 D001_Codigo_Produto as SKU, 
                 D083_Codigo_Barras as EANFORN,
                 T066_Quantidade_Estoque as QTDEST,
                 D083_Codigo_Produto_Fornecedor as CODFORN,
                 D001_Descricao_Produto as DESCRICAO 
                FROM D001 
                 LEFT JOIN D049 ON D049_D001_Id = D001_Id
                 LEFT JOIN D009 ON D009_D049_Id = D049_Id
                 LEFT JOIN T066 ON T066_D009_Id = D009_Id
                 LEFT JOIN D004 ON T066_D004_Id = D004_Id
                 LEFT JOIN D082 ON D049_D082_Id = D082_Id
                 LEFT JOIN D083 ON D083_D001_Id = D001_Id
                 LEFT JOIN D024 ON D083_D024_Id = D024_Id
                 LEFT JOIN T013 ON T013_D024_Id = D024_Id 
                 LEFT JOIN T014 ON T014_T013_Id = T013_Id
                WHERE D001_Codigo_Produto = '{$cod}'
                 AND D009_C004_Id = '{$empresaAtual}'
                 AND T066_D004_Id = '{$locacaoOrigem}'
                 AND D001_Flag_Ativo = 'S'
            ");

            $mD001 = mysql_fetch_assoc($D001);
        } 

        if (!empty($mD004['local'])) {

            $tipoLocacaoJson = json_decode($mD004['D004_JSON']);
            $tipoLocacao = $tipoLocacaoJson->tipo_locacao;

            $resposta['locacaoTipo'] = $tipoLocacao;
            $resposta['locacao'] = $mD004['local'];
            $resposta['locacaoId'] = $mD004['id'];

            if ($origin == 0) { 
                $resposta['info'] = "Origem Vinculada!!";
            } else { 
                $resposta['info'] = "Destino Vinculado!!";
            }
            
            $resposta['status'] = "1";
            $resposta['code'] = false;

        } else if (!empty($mD001['D001_Id'])) {

            $foto = $CAD002->cad002FotoProduto($mD001['D001_Id'], 40);

            $resposta['info'] = "Produto Vinculado!!";
            $resposta['produtos'] = $mD001;
            $resposta['img'] = $foto;
            $resposta['code'] = false;
        } else {
            $resposta['code'] = true;
        }

    break;

    case 'etc022InsertMovimentacao':

        require_once('bibliotecas/classes/EST001.php');
        $EST001 = new EST001();

        $produtos = json_decode($r_produtos, true);	

        $origem   = isset($r_origem)   ? (string) mysql_real_escape_string($r_origem)   : '';
        $destino  = isset($r_destino)  ? (string) mysql_real_escape_string($r_destino)  : ''; 
        $qtdTotal = isset($r_qtdTotal) ? (string) mysql_real_escape_string($r_qtdTotal) : '';

        $tipoOrigem  = isset($r_tipoOrigem)  ? $r_tipoOrigem  : '-'; 
        $tipoDestino = isset($r_tipoDestino) ? $r_tipoDestino : '-';     

        if ($origem === 'INDEFINIDA') {
            $resposta['error'] = "Origem não definida";
            $resposta['code'] = false;
        } else if ($destino === 'INDEFINIDO') {
            $resposta['error'] = "Destino não definido";
            $resposta['code'] = false;
        } else if ($produtos === null || empty($produtos)) {
            $resposta['error'] = "Produtos não definidos";
            $resposta['code'] = false;
        } else {

            $empresa = $g['empresaAtual'];
            $usuario = $g['usuarioAtual'];

            $sqlUser = mysql_query("SELECT C007_Primeiro_Nome FROM C007 WHERE C007_Id = '{$usuario}'");
            $user = mysql_fetch_assoc($sqlUser);

            $conferente = $user['C007_Primeiro_Nome'];

            $GMP004 = mysql_query("
                INSERT INTO GMP004 ( 
                    GMP004_locacao, GMP004_Quantidade, GMP004_Destino, 
                    GMP004_Data_Insercao, GMP004_C004_Id, GMP004_Conferente,
                    GMP004_JSON
                ) 
                VALUES ( 
                    '$origem', '$qtdTotal', '$destino', 
                    NOW(), '$empresa', '$conferente',
                    JSON_SET(IFNULL(GMP004_JSON, '{}'), 
                        '$.tipo_origem', '$tipoOrigem',
                        '$.tipo_destino', '$tipoDestino'
                    )
                )
            ");

            $id = (int)$g['mysqlLastId'];

            foreach ($produtos as $item) {

                $part = explode(" | ", $item['item']);
                
                $sku            = $part[0];
                $ean_fornecedor = $part[1]; 
                $descricao      = $part[2];
                $marca          = $part[3];
                $qtd_estoque    = $part[4];
                $quantidade     = $part[6];
                $ean            = $part[7];
                $cod_fornecedor = $part[8];
                $id_fk          = $part[9];
                $id_t066        = $part[10];
                $id_d009        = $part[11];
                
                $GMP005 = mysql_query("
                    INSERT INTO GMP005 (
                        GMP005_GMP004_Id, GMP005_D001_Id, GMP005_sku, GMP005_Descricao, GMP005_Quantidade, GMP005_Marca, 
                        GMP005_Qtd_Estoque, GMP005_Ean_Fornecedor, GMP005_Ean, GMP005_Cod_fornecedor, GMP005_T066_Id,
                        GMP005_D009_Id 
                    ) VALUES (
                        '$id', '$id_fk', '$sku', '$descricao', '$quantidade', '$marca', 
                        '$qtd_estoque', '$ean_fornecedor', '$ean', '$cod_fornecedor',
                        '$id_t066', '$id_d009'
                    )
                ");
            }

            $movimentacao = $EST001->movimentaLocacao($id);

            $resposta['data'] = "Conferência Realizada com Sucesso!!"; 
            $resposta['code'] = true;
        }

    break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";