<?php
namespace hardness;

require_once(__DIR__ . '/../segurancaSenha.php');

definirClasse('EST002');
class EST002_
{
    protected $usuarioImpressaoAutenticado = null;

    /**
     * Confirma no banco que uma tela ainda pode avançar o pedido.
     *
     * A tela pode permanecer aberta enquanto outro usuário altera o pedido.
     * Por isso, esta validação deve ocorrer no servidor imediatamente antes
     * da transição de status. O UPDATE correspondente também é condicional,
     * para cobrir alterações concorrentes entre esta consulta e a gravação.
     */
    protected function validarPedidoParaAvancar($T005_Id, $statusEsperado)
    {
        $T005_Id = (int) $T005_Id;
        $statusEsperado = (string) $statusEsperado;

        if ($T005_Id <= 0) {
            return 'Pedido inválido.';
        }

        $pedido = mysql_fetch_assoc(mysql_query("SELECT T005_Id,
                                                          T005_Flag_Status,
                                                          T005_Nome_Status,
                                                          T005_Flag_Pdv_Importado
                                                     FROM T005
                                                    WHERE T005_Id = '{$T005_Id}'"));
        if (empty($pedido['T005_Id'])) {
            return 'Pedido não encontrado.';
        }

        if ((string) $pedido['T005_Flag_Status'] !== $statusEsperado) {
            return "O pedido foi atualizado por outro usuário e está no status <b>{$pedido['T005_Nome_Status']}</b>. Atualize a tela antes de continuar.";
        }

        // A NFC-e importada pelo PDV pode continuar no fluxo de separação.
        // Qualquer NF-e comum (ou NFC-e sem vínculo de PDV no pedido) bloqueia.
        $condicaoNotaPdv = $pedido['T005_Flag_Pdv_Importado'] === 'S'
            ? " AND IFNULL(T007_Flag_PDV, 'N') != 'S'"
            : '';
        $notaAtiva = mysql_query("SELECT T007_Id
                                    FROM T007
                                   WHERE T007_T005_Id = '{$T005_Id}'
                                     AND T007_Flag_Cancelada != 'S'
                                     AND T007_Flag_Inutilizado != 'S'
                                     {$condicaoNotaPdv}
                                   LIMIT 1");
        if (mysql_num_rows($notaAtiva) > 0) {
            return 'Existe uma NF-e ativa para este pedido. Atualize a tela antes de continuar.';
        }

        return true;
    }

    /**
     * Confirma o estado persistido após uma transição condicional de status.
     * Não usa a contagem de linhas do driver, pois os triggers da aplicação
     * podem executar consultas adicionais na mesma conexão.
     */
    protected function pedidoFoiAvancado($T005_Id, $statusEsperado)
    {
        $T005_Id = (int) $T005_Id;
        $statusEsperado = mysql_real_escape_string((string) $statusEsperado);

        $pedido = mysql_fetch_assoc(mysql_query("SELECT T005_Flag_Status
                                                   FROM T005
                                                  WHERE T005_Id = '{$T005_Id}'
                                                    AND T005_Flag_Status = '{$statusEsperado}'
                                                    AND NOT EXISTS (
                                                        SELECT 1
                                                          FROM T007
                                                         WHERE T007_T005_Id = T005.T005_Id
                                                           AND T007_Flag_Cancelada != 'S'
                                                           AND T007_Flag_Inutilizado != 'S'
                                                           AND NOT (
                                                               IFNULL(T005.T005_Flag_Pdv_Importado, 'N') = 'S'
                                                               AND IFNULL(T007.T007_Flag_PDV, 'N') = 'S'
                                                           )
                                                    )"));

        return !empty($pedido['T005_Flag_Status']);
    }

    protected  function validarUsuarioSenha($login, $senha)
    {
        $login = mysql_real_escape_string((string) $login);
        $sql = mysql_query("SELECT * FROM C007 WHERE C007_Login='{$login}' AND C007_Ativo='S' AND C007_Login!='' AND C007_Senha!='' LIMIT 1");
        if (!$sql || mysql_num_rows($sql) === 0) return false;

        $usuario = mysql_fetch_array($sql);
        return SegurancaSenha::validarSenhaArmazenada((string) $senha, (string) $usuario['C007_Senha']) ? $usuario : false;
    }

    public function ImprimirPedidoUsuarioAutenticado($form)
    {
        global $g;

        $usuarioId = (int) ($g['usuarioAtual'] ?? 0);
        $empresaId = (int) ($g['empresaAtual'] ?? 0);
        if ($usuarioId <= 0 || $empresaId <= 0) return 'Não foi possível identificar o usuário autenticado.';

        $sql = mysql_query("SELECT C007.* FROM C007 INNER JOIN C019 ON C019_C007_Id=C007_Id INNER JOIN C004 ON C004_Id=C019_C004_Id WHERE C007_Id='{$usuarioId}' AND C007_Ativo='S' AND C019_C004_Id='{$empresaId}' AND C004_Flag_Ativo='S' LIMIT 1");
        if (!$sql || mysql_num_rows($sql) === 0) return 'Usuário sem acesso à empresa atual.';

        $this->usuarioImpressaoAutenticado = mysql_fetch_array($sql);
        try {
            return $this->ImprimirPedido($form);
        } finally {
            $this->usuarioImpressaoAutenticado = null;
        }
    }


    public function volumesParcialConferenciaGerar($T005_Id){
        return true;
    }

	/**
	 * ImprimirPedido
	 *
	 * @param Form $form
	 * @return true/string
	 */
    public function ImprimirPedido($form)
    {
        $T005_Id_Array = explode(';', $form->campoValorEnviado('impressao_id'));
        $Login   = $form->campoValorEnviado('Login');
        $Senha   = $form->campoValorEnviado('Senha');

        global $g;
        
        $res = $this->usuarioImpressaoAutenticado !== null ? $this->usuarioImpressaoAutenticado : $this->validarUsuarioSenha($Login, $Senha);
        if ($res === false)
        {
            return 'Login ou Senha Incorretos !!!';
        }
        
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();        
        $mensagemStatus = '';

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        foreach($T005_Id_Array as $T005_Id)
        {
            $verificaStatus = mysql_query("SELECT T005_Flag_Status,T005_Nome_Status FROM T005 WHERE T005_Id = '{$T005_Id}'");
            $verificaStatus = mysql_fetch_array($verificaStatus);

            if($verificaStatus['T005_Flag_Status'] > 1){
                mysql_query("ROLLBACK");
                $mensagemStatus = "Pedido {$T005_Id} já foi impresso, está com o status <b>{$verificaStatus['T005_Nome_Status']}</b>, favor verificar";
                return $mensagemStatus;
            }

            $sqlD009 = mysql_query("SELECT D009_Id,  (D009_Quantidade_Estoque-D009_Quantidade_Estoque_Loja) as Saldo FROM T006 LEFT JOIN D009 ON D009_Id=T006_D009_Id WHERE T006_T005_Id = '{$T005_Id}'");
            while($rowD009 = mysql_fetch_array($sqlD009)){
                $CAD002->processaEstoquePedidos($rowD009['D009_Id'], $rowD009['Saldo']);
            }        

            $sql  = mysql_query("SELECT T006_Id 
                                    FROM T006 
                                LEFT JOIN T005 ON T005_Id=T006_T005_Id
                                LEFT JOIN T055 ON T055_D009_Id=T006_D009_Id AND T055_T075_Id=T006_T075_Id 
                                    WHERE T006_T005_Id='{$T005_Id}' 
                                    AND (T006_Quantidade>IF(IFNULL(T006_T075_Id,0)>0,IFNULL(T055_Quantidade,0),IF(IFNULL(T006_T206_Id,0)>0,D009_Quantidade_DF(T006_D009_Id,T006_T206_Id, T006_Id),IFNULL(T006_Quantidade_Estoque,0))))
                                    AND '{$permitirVenda}'='N' 
                                    AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id");

            if (mysql_num_rows($sql) > 0 && $g['C031']['permitirFaturarSemEstoque'] == 'N') {
                return 'Existem produtos sem estoque. Favor verificar.';
            }
            // VERIFICA AS GLOBAIS DE EXPEDIÇÃO E DEFINE O STATUS
            if($g['C031']['desabilitarSeparacao']=='S' && $g['C031']['desabilitarConferencia']=='S'){
                $T005_Flag_Status='3';
                $fraseStatus = 'Enviou para emissão de NF';
            } else if($g['C031']['desabilitarSeparacao']=='S' && $g['C031']['desabilitarConferencia']=='N'){
                $T005_Flag_Status='10';
                $fraseStatus = 'Enviou para conferência';
            }else{
                $T005_Flag_Status='2';
                $fraseStatus = 'Enviou para separação';
            }
            
            //-- SE A SENHA FOR CORRETA
            //-- ALTERA O STATUS DO PEDIDO
            //-- SE O PEDIDO ESTIVER MARCADO PARA NAO SER FATURADO, ELE SER� COLOCADO COMO PENDENTE
            $sql = <<<EOT
                UPDATE
                    T005
                SET 
                    T005_Usuario_Pecas = '{$res['C007_Id']}',
                    T005_Data_Separacao_Pecas = now(),
                    T005_Flag_Status = '{$T005_Flag_Status}',
                    T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1),
                    T005_C007_Id_Separou = '{$res['C007_Id']}',
                    T005_Flag_Marcado = 'N'
                WHERE
                    T005_Id = '{$T005_Id}'
EOT;
            $sql  = mysql_query($sql);
            // Insere registros no histórico do pedido (T178)
            mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$fraseStatus}','')");  
        }

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");

        $this->triggerAposImpressaoPedido($T005_Id);

        return true;
    }
    
    /**
     * SeparacaoPedido
     *
     * @param Form $form
     * @return true/string
     */
    public function SeparacaoPedido($form)
    {
        global $g;
        
        $T005_Id          = $form->campoValorEnviado('T005_Id');
        $T005_Estoque_Box = $form->campoValorEnviado('T005_Estoque_Box');

        $validacaoPedido = $this->validarPedidoParaAvancar($T005_Id, '2');
        if ($validacaoPedido !== true) {
            return $validacaoPedido;
        }
        
        $T006_Restricao = mysql_query("SELECT T006_Codigo_Produto,
                                              T006_Quantidade,
                                              T006_Quantidade_Separacao 
                                         FROM T006 
                                    LEFT JOIN T005 ON T005_Id=T006_T005_Id 
                                        WHERE T006_T005_Id='{$T005_Id}'
                                          AND (T006_Quantidade<T006_Quantidade_Separacao) 
                                          AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id");
        if (mysql_num_rows($T006_Restricao) > 0) {
            $mensagem = '';
            while($mT006 = mysql_fetch_array($T006_Restricao)){
                $mT006['T006_Quantidade']           = gCorrigeNumero($mT006['T006_Quantidade']);
                $mT006['T006_Quantidade_Separacao'] = gCorrigeNumero($mT006['T006_Quantidade_Separacao']);
                $mensagem .= "<b>Código {$mT006['T006_Codigo_Produto']}</b> - Quantidade: {$mT006['T006_Quantidade']} / Separado: {$mT006['T006_Quantidade_Separacao']}<br>";
            }
            return "Itens abaixo com quantidade separada maior que a quantidade do pedido, necessário excluir e refazer a separação<br><br>".$mensagem;
        }

        $T006  = mysql_query("SELECT (sum(T006_Quantidade_Separacao)/sum(T006_Quantidade)*100) FROM T006 LEFT JOIN T005 ON T005_Id=T006_T005_Id WHERE T006_T005_Id='$T005_Id' AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id");
        $mT006 = mysql_fetch_array($T006);
        
        if ($mT006[0] < 100 && empty($T005_Estoque_Box) && $g['C031']['modeloExpedicao'] != 2) {
            return "Este pedido está apenas " . number_format($mT006[0], 2, ',', '.') . "% separado, você deverá informar um BOX";
        }

        if ($mT006[0] < 100 && !empty($T005_Estoque_Box) && $g['C031']['modeloExpedicao'] != 2) {
            $sql = <<<EOT
				UPDATE T006
				   SET T006_Flag_Divergencia='S'
				 WHERE T006_T005_Id='{$T005_Id}'
				   AND (T006_Quantidade>T006_Quantidade_Separacao OR T006_Quantidade_Separacao is null)
EOT;
            mysql_query($sql);

            mysql_query("update T005 set T005_Estoque_Box='{$T005_Estoque_Box}' where T005_Id='{$T005_Id}'");
            
            return "Este pedido está apenas " . number_format($mT006[0], 2, ',', '.') . "% separado, resolva as divergências";
        }
        
        
        /*
        // Comentado porque está errado - deve ser verificado apenas as locação na T238 
        // Não é mais necessário, porque não tem mais como adicionar uma locação em branco
        $sqlD004 = mysql_query("SELECT T066_Id FROM T006 LEFT JOIN T066 ON T066_D009_Id=T006_D009_Id WHERE T006_T005_Id='{$T005_Id}' and ifnull(T066_D004_Id,0)=0");
        if(mysql_num_rows($sqlD004) > 0) {
            return "Não foi informado a LOCAÇÃO de todos os produtos.<br />VERIFIQUE!";
        }
        */
        // VERIFICANDO
        if($g['C031']['desabilitarConferencia']=='S'){
            $T005_Flag_Status='3';
        } else {
            $T005_Flag_Status='10';
        }

        $triggerStatus = $this->triggerStatusPedido($T005_Id);
        if($triggerStatus !== true){
            $T005_Flag_Status = $triggerStatus;
        }

        mysql_query("UPDATE T005
                        SET T005_Flag_Status = '{$T005_Flag_Status}',
                            T005_Nome_Status = T005_Status_Pedido(T005_Flag_Status, 1),
                            T005_Estoque_Box = '{$T005_Estoque_Box}'
                      WHERE T005_Id = '{$T005_Id}'
                        AND T005_Flag_Status = '2'
                        AND NOT EXISTS (
                            SELECT 1
                              FROM T007
                             WHERE T007_T005_Id = T005.T005_Id
                               AND T007_Flag_Cancelada != 'S'
                               AND T007_Flag_Inutilizado != 'S'
                               AND NOT (
                                   IFNULL(T005.T005_Flag_Pdv_Importado, 'N') = 'S'
                                   AND IFNULL(T007.T007_Flag_PDV, 'N') = 'S'
                               )
                        )");
        if (!$this->pedidoFoiAvancado($T005_Id, $T005_Flag_Status)) {
            return 'O pedido foi atualizado por outro usuário ou já possui NF-e ativa. Atualize a tela antes de continuar.';
        }
        mysql_query("update T006 set T006_Flag_Divergencia='N' where T006_T005_Id='{$T005_Id}'");
        // Insere registros no histórico do pedido (T178)
        mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Enviou para conferência','')");
        
        $this->triggerAposSeparacaoPedido($T005_Id);

        return true;
        
    }
    
    /**
     * FaturamentoPedido
     *
     * @param Form $form
     * @return true/string
     */
    public function FaturamentoPedido($form)
    {
        global $g;
        $T005_Id                  = $form->campoValorEnviado('T005_Id');
        $Flag_ACP                 = $form->campoValorEnviado('T005_Flag_ACP');
        $T005_Estoque_Box         = $form->campoValorEnviado('T005_Estoque_Box');
        $T005_Quantidade_Volumes  = $form->campoValorEnviado('T005_Quantidade_Volumes');
        $T005_Especie             = $form->campoValorEnviado('T005_Especie');
        $T005_Flag_Totalizar_Peso = $form->campoValorEnviado('T005_Flag_Totalizar_Peso');
        $T005_Flag_Pdv_Importado  = $form->campoValorEnviado('T005_Flag_Pdv_Importado');
        $T005_Peso_Liquido        = gCorrigeNumeroInverte($form->campoValorEnviado('T005_Peso_Liquido'));
        $T005_Peso_Bruto          = gCorrigeNumeroInverte($form->campoValorEnviado('T005_Peso_Bruto'));
        $Login                    = $form->campoValorEnviado('Login');
        $Senha                    = $form->campoValorEnviado('Senha');

        $validacaoPedido = $this->validarPedidoParaAvancar($T005_Id, '10');
        if ($validacaoPedido !== true) {
            return $validacaoPedido;
        }
        
        // Não irá continuar sem todos os itens conferidos
        $sql = <<<EOT
			SELECT 
				T006_Id,
				D001_Codigo_Barras,
				T006_Quantidade,
				T006_Descricao_Produto,
				T006_Flag_Conf_Cega 
			FROM T006 
			left join T005 on T005_Id=T006_T005_Id
			left join D009 on D009_Id=T006_D009_Id 
			left join D049 on D049_Id=D009_D049_Id 
			left join D082 on D082_Id=D049_D082_Id 
			left join D001 on D049_D001_Id=D001_Id 
			WHERE
				T006_T005_Id='{$T005_Id}'
				AND T005_Flag_Status='10'
				AND (T006_T006_Id is null or T006_T006_Id<=0)
				AND (T006_Quantidade>0)
				AND T006_Flag_Conf_Cega = 'N'
                AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
EOT;
        $res = mysql_query($sql);
        if($g['C031']['conferenciaCegaVendas'] == 'S'){
            if (mysql_num_rows($res)) {
                return 'Existem um ou mais itens que não foram conferidos!';
            }
        }
        
        $res = $this->validarUsuarioSenha($Login, $Senha);
        if ($res === false) {
            return 'Login ou Senha Incorretos !!!';
        } else {
            if ($Flag_ACP == 2) {
                $cSql_T006 = mysql_query("select T006_D009_Id from T006 where T006_T005_Id='{$T005_Id}'");
                while ($mT006 = mysql_fetch_array($cSql_T006)) {
                    mysql_query("update D009 set D009_Quantidade_Estoque_Real='0' where D009_Id='{$mT006['T006_D009_Id']}'");
                }
                $Flag_Status = '3';
                return "Pedido não será faturado!";
            } else {
                if($g['C031']['modeloExpedicao'] == '1'){
                    $Flag_Status = '3';
                } else if($g['C031']['modeloExpedicao'] == '2'){
                    $Flag_Status = '22';
                }
            }
            $T005_Estoque_Box = trim($T005_Estoque_Box);
            if (!empty($T005_Estoque_Box)) {
                $Flag_Status = '10';
                return "Você deve limpar o box para prosseguir.";
            }
            // Cupom Fiscal
            $rowT005 = mysql_fetch_assoc(mysql_query("SELECT T005_Flag_ACP,
                                                             T007_Id, 
                                                             T005_Flag_Expedicao,
                                                             T005_Flag_Pdv_Importado 
                                                        FROM T005 
                                                   LEFT JOIN T007 ON T007_T005_Id = T005_Id 
                                                       WHERE T005_Id = '{$T005_Id}'"));
            /*if ($rowT005['T005_Flag_ACP'] == 4 && empty($rowT005['T007_Id'])) {
                $Flag_Status = '27'; // "Imprimir Cupom"
            } else*/
            if($g['C031']['modeloExpedicao'] == '1'){
                if ($rowT005['T005_Flag_ACP'] == '4' || $rowT005['T005_Flag_Expedicao'] == '2' || ($rowT005['T005_Flag_ACP'] == '8' && ($rowT005['T005_Flag_Pdv_Importado'] == 'S' || $T005_Flag_Pdv_Importado == 'S'))) {
                    $Flag_Status = '4'; // "Aguardando Coleta"
                }
            } else if($g['C031']['modeloExpedicao'] == '2'){
                $Flag_Status = '22';
            }
            
            $cClausula = "UPDATE T005 ";
            $cClausula .= "SET T005_Flag_Status     = '{$Flag_Status}', ";
            $cClausula .= "T005_Nome_Status         = T005_Status_Pedido (T005_Flag_Status, 1), ";
            $cClausula .= "T005_Flag_Situacao       = '', ";
            $cClausula .= "T005_Quantidade_Volumes  = '{$T005_Quantidade_Volumes}', ";
            $cClausula .= "T005_Especie             = '{$T005_Especie}', ";
            $cClausula .= "T005_Peso_Liquido        = '{$T005_Peso_Liquido}', ";
            $cClausula .= "T005_Peso_Bruto          = '{$T005_Peso_Bruto}', ";
            $cClausula .= "T005_Estoque_Box         = '{$T005_Estoque_Box}', ";
            $cClausula .= "T005_Flag_Totalizar_Peso = '{$T005_Flag_Totalizar_Peso}', ";
            $cClausula .= "T005_Flag_Pdv_Importado  = '{$T005_Flag_Pdv_Importado}', ";
            $cClausula .= "T005_C007_Id_Conferiu    = '{$res['C007_Id']}' ";
            $cClausula .= "WHERE T005_Id            = '{$T005_Id}' ";
            $cClausula .= "  AND T005_Flag_Status   = '10' ";
            $cClausula .= "  AND NOT EXISTS (SELECT 1 FROM T007 WHERE T007_T005_Id = T005.T005_Id AND T007_Flag_Cancelada != 'S' AND T007_Flag_Inutilizado != 'S' AND NOT (IFNULL(T005.T005_Flag_Pdv_Importado, 'N') = 'S' AND IFNULL(T007.T007_Flag_PDV, 'N') = 'S'))";
            mysql_query($cClausula);
            
            $erro = mysql_error();
            if (!empty($erro)) {
                return "erro: $cClausula";
            }

            if (!$this->pedidoFoiAvancado($T005_Id, $Flag_Status)) {
                return 'O pedido foi atualizado por outro usuário ou já possui NF-e ativa. Atualize a tela antes de continuar.';
            } else {
                if ($Flag_Status == '3') {
                    $Descricao = 'Enviou para emissão NF';
                } elseif ($Flag_Status == '10') {
                    $Descricao = 'Enviou para conferência';
                } elseif ($Flag_Status == '27') {
                    $Descricao = 'Enviou para imprimir cupom';
                } elseif ($Flag_Status == '4') {
                    $Descricao = 'Enviou para coleta';
                }
                //Insere registros no histórico do pedido (T178)
                mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$Descricao}','')");
            }
        }

        $this->triggerAposFaturamentoPedido($T005_Id);

        return true;
    }
    
    /**
     * confirmarEmpretismo
     *
     * @param string $senha
     * @param int $id
     * @return true/string
     */
    public function confirmarEmpretismo($senha, $id)
    {
        $sql    = "SELECT * FROM T175 WHERE T175_Id='{$id}'";
        $result = mysql_query($sql);
        $linha  = mysql_fetch_array($result);
        
        if (!empty($linha['T175_C007_Id'])) {
            $sql    = "SELECT * FROM C007 WHERE C007_Id='{$linha['T175_C007_Id']}' AND C007_Ativo='S' LIMIT 1";
            $result = mysql_query($sql);
            $usuario = $result ? mysql_fetch_array($result) : null;
            if ($usuario && SegurancaSenha::validarSenhaArmazenada($senha, $usuario['C007_Senha'])) {
                $sql    = "UPDATE T175 SET T175_Data_Retorno=CURRENT_TIMESTAMP(), T175_Flag_Status='C' WHERE T175_Id='{$id}'";
                $result = mysql_query($sql);
                if (mysql_error()) {
                    return mysql_error();
                } else {
                    return true;
                }
            } else {
                return "Senha inválida";
            }
        } else if (!empty($linha['T175_D109_Id'])) {
            $sql    = "SELECT * FROM D109 WHERE D109_Id='{$linha['T175_D109_Id']}' AND D109_Senha='{$senha}'";
            $result = mysql_query($sql);
            if (mysql_num_rows($result) > 0) {
                $sql    = "UPDATE T175 SET T175_Data_Retorno=CURRENT_TIMESTAMP(), T175_Flag_Status='C' WHERE T175_Id='{$id}'";
                $result = mysql_query($sql);
                if (mysql_error()) {
                    return mysql_error();
                } else {
                    return true;
                }
            } else {
                return "Senha inválida";
            }
        }
    }
    
    /**
     * validaInclusaoEmprestimo
     *
     * @param Form $form
     * @return true/string
     */
    public function validaInclusaoEmprestimo($form)
    {
        $T175_D109_Id = $form->campoValorEnviado('T175_D109_Id');
        $T175_C007_Id = $form->campoValorEnviado('T175_C007_Id');
        $T175_D009_Id = $form->campoValorEnviado('T175_D009_Id');
        $Senha        = $form->campoValorEnviado('Senha');
        $qnt          = $form->campoValorEnviado('quantidade');
        
        $erro = "";
        if (empty($T175_D109_Id) && empty($T175_C007_Id)) {
            $erro .= "É necessário preencher um funcionário ou usuário<br />";
        }
        if (empty($T175_D009_Id)) {
            $erro .= "É necessário preencher um produto<br />";
        }
        if (empty($qnt)) {
            $erro .= "É necessário preencher uma quantidade<br />";
        }
        if (empty($Senha)) {
            $erro .= "É necessário preencher uma senha<br />";
        }
        if (!empty($erro)) {
            return $erro;
        }
        
        
        if (!empty($T175_D109_Id)) {
            $sql    = "select D109_Senha from D109 WHERE D109_Id='{$T175_D109_Id}'";
            $result = mysql_query($sql);
            $linha  = mysql_fetch_array($result);
            if ($linha['D109_Senha'] != $Senha) {
                return "Senha inválida";
            } else {
                $sql = <<<EOT
					INSERT INTO T175 (
						T175_Id,
						T175_D009_Id,
						T175_C007_Id,
						T175_D109_Id,
						T175_Data_Saida,
						T175_Data_Retorno,
						T175_Quantidade,
						T175_Flag_Status,
						T175_Observacao
					) VALUES (
						'',
						'{$T175_D009_Id}',
						'',
						'{$T175_D109_Id}',
						CURRENT_TIMESTAMP(),
						'',
						'{$qnt}',
						'A',
						''
					)
EOT;
                $result = mysql_query($sql);
                if (mysql_error()) {
                    return mysql_error();
                } else {
                    return true;
                }
            }
        } elseif (!empty($T175_C007_Id)) {
            $sql    = "SELECT C007_Senha FROM C007 WHERE C007_Id='{$T175_C007_Id}' AND C007_Ativo='S' LIMIT 1";
            $result = mysql_query($sql);
            $linha  = mysql_fetch_array($result);

            if (!$linha || !SegurancaSenha::validarSenhaArmazenada($Senha, $linha['C007_Senha'])) {
                return "Senha inválida";
            } else {
                $sql = <<<EOT
					INSERT INTO T175 (
						T175_Id,
						T175_D009_Id,
						T175_C007_Id,
						T175_D109_Id,
						T175_Data_Saida,
						T175_Data_Retorno,
						T175_Quantidade,
						T175_Flag_Status,
						T175_Observacao
					) VALUES (
						'',
						'{$T175_D009_Id}',
						'{$T175_C007_Id}',
						'',
						CURRENT_TIMESTAMP(),
						'',
						'{$qnt}',
						'A',
						''
					)
EOT;
                $result = mysql_query($sql);
                if (mysql_error()) {
                    return mysql_error();
                } else {
                    return true;
                }
            }
        }
    }
    
    /**
     * confirmarEntregaInterna
     *
     * @param int $D109_Id
     * @return true/string
     */
    public function confirmarEntregaInterna($D109_Id)
    {
        global $g;
        // id do funcionário fixo apenas para testar
        $sql    = "select T006_Id, T006_Quantidade_Entregar_Temp from T006 WHERE T006_Quantidade_Entregar_Temp>0";
        $result = mysql_query($sql);
        if (mysql_num_rows($result) <= 0) {
            return 'não há produtos a serem entregues';
        }
        while ($mT006 = mysql_fetch_array($result)) {
        	$sql = <<<EOT
				INSERT INTO T179 (
					T179_T006_Id,
					T179_D109_Id_Recebeu,
					T179_C007_Id_Entregou,
					T179_Data,
					T179_Quantidade_Entregue
				) VALUES (
					'{$mT006['T006_Id']}',
					'{$D109_Id}',
					'{$g['usuarioAtual']}',
					now(),
					'{$mT006['T006_Quantidade_Entregar_Temp']}'
				)
EOT;
            mysql_query($sql);
            if (mysql_error()) {
                return 'Erro ao inserir registro na T179, favor contactar o suporte';
            }
            mysql_query("update T006 set T006_Quantidade_Entregar_Temp=0 where T006_Id='{$mT006['T006_Id']}'");
            if (mysql_error()) {
                return 'Erro ao atualizar registro da T006, favor contactar o suporte';
            }
        }
        return true;
    }

     /**
     * enviarXMLEmailTransportadora
     */
    public function enviarXMLEmailTransportadora($T007_Id,$C007_Id,$T007_D024_Id)
    {
        global $g;
        // return "Usuário está com as configurações de email incompletas.";
        require_once("bibliotecas/phpmailer/class.phpmailer.php");
        $mail = new \phpmailer;
        log("C007:".$C007_Id);
        log("Select C007:SELECT * FROM C007 WHERE C007_Id = '{$C007_Id}'");
        $C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$C007_Id}'");
        $C007 = mysql_fetch_assoc($C007);
        if (empty($C007) || empty($C007['C007_Email_SMTP_Servidor'])) {
            return "Usuário está com as configurações de email incompletas.";
        }
        
        $monta_ = $this->montarEmailTransportadoras($T007_Id);
        
        if(!is_array($monta_)){
            return $monta_;
        }
        if (empty($monta_['Para'])) {
            return "E-mail do remetente está vazio.";
        }
        if (!empty($C007['C007_Email_Assinatura']) && trim($C007['C007_Email_Assinatura']) != '<br>') {
            $monta_['Corpo'] .= "<br /><br />{$C007['C007_Email_Assinatura']}";
        }

        // Mail
        $mail->From     = $C007['C007_Email'];
        $mail->FromName = $C007['C007_Primeiro_Nome'];
        $mail->Host     = $C007['C007_Email_SMTP_Servidor'];
        $mail->Mailer   = "smtp";
        $mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
        $mail->Username = $C007['C007_Email_SMTP_Usuario'];
        $mail->Password = $C007['C007_Email_SMTP_Senha'];
        $mail->Subject  = '=?UTF-8?B?'.base64_encode($monta_['Assunto']).'?=';
        $mail->Body     = $monta_['Corpo'];
        $mail->AltBody  = "ALT BODY";
        $mail->CharSet  = 'utf-8';
        $mail->SetLanguage("br");
        $mail->IsHTML(true);
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.gmail.com")
        {
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465"; 
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.live.com")
        {
            $mail->SMTPSecure   = "ssl";
        }
        if (!empty($C007['C007_Email_SMTP_Porta']))
        {
            $mail->Port         = $C007['C007_Email_SMTP_Porta']; 
        }
        if (empty($monta_['Para']) && !empty($monta_['cc'])) {
            $monta_['Para'] = $monta_['cc'];
            $monta_['cc'] = '';
        }
        $todosEmailsEnviados = array();
        $mPara=preg_split("/[;,]+/",$monta_['Para']);
        for($x=0;$x<count($mPara);$x++)
        {
            $email = $mPara[$x];
            if (strpos($email, '<') !== false) {
                $email = explode('<', $email);
                $email = explode('>', $email[1]);
                $email = $email[0];
            }
            $todosEmailsEnviados[] = strtolower(trim($email));
            $mail->AddAddress($email);
        }
        if (isset($monta_['cc'])) {
            $mPara=preg_split("/[;,]+/",$monta_['cc']);
            for($x=0;$x<count($mPara);$x++)
            {
                $email = $mPara[$x];
                if (strpos($email, '<') !== false) {
                    $email = explode('<', $email);
                    $email = explode('>', $email[1]);
                    $email = $email[0];
                }
                $todosEmailsEnviados[] = strtolower(trim($email));
                $mail->AddCC($email);
            }
        }

        $anexosGravar = array();
        $T139_Estrutura = array('anexosManual' => array());
        if (isset($monta_['Anexos'])) {
            foreach ($monta_['Anexos'] as $path) {
                $anexosGravar[] = $path;
                $T139_Estrutura['anexosManual'][] = basename($path);
                $mail->AddAttachment($path,basename($path));
            }
        }
        // $mail->SMTPDebug = 2;// enables SMTP debug information (for testing)
        // $mail->SMTPDebug = 4;//show each and every step PHPMailer does
        if($mail->Send())
        {
            mysql_query("UPDATE T007 SET T007_Data_Envio_XML_Transportadora=CURRENT_TIMESTAMP(),T007_Flag_enviar_XML_Transportadora='N'  WHERE T007_Id='{$T007_Id}'");
             
            // Gravar como e-mail enviado
            $T139_De = mysql_real_escape_string("\"{$C007['C007_Primeiro_Nome']}\" <{$C007['C007_Email']}>");
            $T139_Estrutura = serialize($T139_Estrutura);
            $T139_Para = !empty($monta_['cc']) ? ($monta_['Para'] . '; ' . $monta_['cc']) : $monta_['Para'];
            $sql = "INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_Estrutura, T139_Mensagem_Corpo, T139_Flag_Lido, T139_D024_Id, T139_C007_Id,T139_T007_Id) VALUES ('S', 0, '".mysql_real_escape_string($monta_['Assunto'])."', '{$T139_De}', '".mysql_real_escape_string($T139_Para)."', '".date('Y-m-d H:i:s')."', '" . mysql_real_escape_string($T139_Estrutura) . "', '".mysql_real_escape_string($monta_['Corpo'])."', '1', '".mysql_real_escape_string($T007_D024_Id)."', '".mysql_real_escape_string($g['usuarioAtual'])."', '".mysql_real_escape_string($T007_Id)."');";
            mysql_query($sql);
            $g['mysqlLastId'] = mysql_insert_id();
            
            // Salvar anexos
            $dir = $g['pathDados'] . 'anexosEmail/';
            is_dir($dir) or mkdir($dir, 0777, true);
            foreach ($anexosGravar as $key => $anexo) {
                $arquivo = "{$dir}{$g['mysqlLastId']}-{$key}";
                copy($anexo, $arquivo);
            }
            return true;
        }else{
            return "Erro ao enviar o email, favor entrar em contato com a equipe técnica e informar o seguinte erro.<br>".$mail->ErrorInfo."<br>";
        }
    }

    /**
     * montarEmailTransportadoras
     *
     * @param String $T007_Id
     * @return array
     */
    public function montarEmailTransportadoras($T007_Id)
    {
        global $g, $confUsuario;

        $T007_Id = mysql_real_escape_string($T007_Id);
        $sql     = "SELECT D022_Fax,
                           T007_Numero_Nota_Fiscal,
                           D022_Id,
                           T007_Numero_Protocolo_Nfe,
                           T007_Numero_Protocolo_Cancelamento_Nfe,
                           T007_Chave_Acesso_Nfe,
                           T007_Empresa_Usuaria_Nome,
                           T007_Empresa_Usuaria_Cnpj,
                           T007_Chave_Acesso_Nfe,
                           D022_Nome_Empresa,
                           T007_Data_Emissao
                    FROM T007 LEFT JOIN D022 ON D022_Id=T007_D022_Id WHERE T007_Id='{$T007_Id}'";
        $T007    = mysqli_query($sql);
        $T007    = mysql_fetch_array($T007);

        $array            = array();
        $array['Para']    = $T007['D022_Fax'];
        $array['Assunto'] = "Nota Fiscal Nº[{$T007['T007_Numero_Nota_Fiscal']}]";
        $array['D022_Id'] = $T007['D022_Id'];
        $array['Anexos']  = array();

        $cAno_Mes_NFe  = substr($T007['T007_Data_Emissao'], 0, 7);
        $Diretorio_NFe = $g['pathDadosAntigo'] . 'nfe';
        //-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
        $cArquivo      = "$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/" . $T007['T007_Numero_Protocolo_Nfe'] . ".xml";

        if (file_exists($cArquivo)) {
            $array['Anexos'][] = $cArquivo;
        } else {
            return 'XML da nota fiscal não encontrado';
        }
        require_once('bibliotecas/classes/VEN003.php');
        $VEN003 = new VEN003;
        $retorno = $VEN003->gerarDanfe($T007_Id, $cArquivo);
        log($retorno . "<<<< <br />" . $cArquivo);
        if (file_exists($retorno)) {
            log("Anexou");
            $array['Anexos'][] = $retorno;
        } else {
            return 'Falha ao gerar DANFE';
        }

        // Verifica se tem cancelamentos
        if (!empty($T007['T007_Numero_Protocolo_Cancelamento_Nfe'])) {
            $cArquivoProtocolado = "$Diretorio_NFe/xml/autorizado/ID{$T007['T007_Numero_Protocolo_Cancelamento_Nfe']}.xml";
            // modelo novo, depois das alterações do evantoni
            if (!file_exists($cArquivoProtocolado)) {
                $zenSeqEvento        = str_pad(1, 2, "0", STR_PAD_LEFT);
                $tpEvento            = '110111';
                $cArquivoProtocolado = "$Diretorio_NFe/xml/cancelado/ID{$tpEvento}{$T007['T007_Chave_Acesso_Nfe']}{$zenSeqEvento}.xml";
            }
            if (file_exists($cArquivoProtocolado)) {
                $array['Anexos'][] = $cArquivoProtocolado;
            }
        }
        
        //Verifica se há cartas de correção para esta NFe
        $sqlCce = mysqli_query("SELECT T199_Id FROM T199 WHERE T199_T007_Id='{$T007_Id}' AND T199_Flag_Enviado='S'");
        while ($rowCce = mysql_fetch_array($sqlCce)) {
            $retornoCce        = $this->montarEmailPdfCce($rowCce['T199_Id']);
            $array['Anexos'][] = $retornoCce['Anexos'][0];
            $array['Anexos'][] = $retornoCce['Anexos'][1];
        }
        
        
        // Corpo
        $array['Corpo']         = $T007['D022_Nome_Empresa'] . ',';
        $Protocolo_Cancelamento = '';
        if (!empty($T007['T007_Numero_Protocolo_Cancelamento_Nfe'])) {
            $Protocolo_Cancelamento = "\n<br />Protocolo de Cancelamento: {$T007['T007_Numero_Protocolo_Cancelamento_Nfe']}";
        }
        
        if (mysql_num_rows($sqlCce) > 0) {
            $array['Corpo'] = "{$T007['D022_Nome_Empresa']},\n\n<br /><br />Segue em anexo arquivo XML, DANFE e Carta(s) de Correções referente a NF-e: $T007[T007_Numero_Nota_Fiscal]\n\n<br /><br />Empresa: $T007[T007_Empresa_Usuaria_Nome]\n<br />CNPJ: $T007[T007_Empresa_Usuaria_Cnpj]\n\n<br /><br />Chave de Acesso: $T007[T007_Chave_Acesso_Nfe]\n<br />Protocolo de Autorização: $T007[T007_Numero_Protocolo_Nfe] {$Protocolo_Cancelamento}";
        } else {
            $array['Corpo'] = "{$T007['D022_Nome_Empresa']},\n\n<br /><br />Segue em anexo arquivo XML e o DANFE referente a NF-e: $T007[T007_Numero_Nota_Fiscal]\n\n<br /><br />Empresa: $T007[T007_Empresa_Usuaria_Nome]\n<br />CNPJ: $T007[T007_Empresa_Usuaria_Cnpj]\n\n<br /><br />Chave de Acesso: $T007[T007_Chave_Acesso_Nfe]\n<br />Protocolo de Autorização: $T007[T007_Numero_Protocolo_Nfe] {$Protocolo_Cancelamento}";
        }
        //$array['Corpo'] = "Envio do XML via e-mail.<br/><br/>Debug: {$cArquivo}<br/>Debug2: " . '$cArquivo = "$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/$T007[T007_Numero_Protocolo_Nfe].xml";';
        
        return $array;
    }

       /**
     * gerarNumeroRomaneio
     *
     * @param string $extra
     * @return true/string
     */
    public function gerarNumeroRomaneio($extra)
    {
        global $g;
        $retorno = array();
        
        $sql = <<<EOT
            SELECT
                T007_Flag_Cancelada,
                T007_Flag_ACP,
                T007_Id,
                T007_Numero_Nota_Fiscal,
                T007_Numero_Protocolo_Nfe,
                T007_Protocolo_Inutilizacao,
                T005_Flag_Expedicao,
            T005_Id
            FROM T005
            {$extra}
EOT;
        $validacao = '';

        $res = mysql_query($sql);
        $quan = mysql_num_rows($res);

        if($quan<=0){
            $retorno[0] = false;
            $retorno[1] = "Nenhum pedido selecionada";
            return $retorno;
        }

        while($row = mysql_fetch_array($res)){
            include("validaColeta.php");
            $validacao.= $verifica;
        }

        if(!empty($validacao)){
            $retorno[0] = false;
            $retorno[1] = "Erro: ".$validacao;
            return $retorno;
        }

        if(empty($validacao)){

            mysql_query("INSERT INTO T275(T275_Data_emissao,T275_C007_Id) VALUES(CURRENT_TIMESTAMP(),'{$g['usuarioAtual']}')");
            $T275_Id = $g['mysqlLastId'];

            $sql = <<<EOT
                SELECT T005_Id FROM T005 {$extra}
EOT;
            $res = mysql_query($sql);
            while($row = mysql_fetch_assoc($res)){
                mysqli_query("UPDATE T005 SET T005_T275_Id='{$T275_Id}' WHERE T005_Id='{$row['T005_Id']}'");
            }
        }

        $retorno[0] = true;
        $retorno[1] = $T275_Id;

        return $retorno;
    }
    
    /**
     * verificarQteEntrega
     *
     * @param int $T006_Id
     * @param string $campoEditado
     * @param decimal $valorAntigo
     * @param decimal $novoValor
     * @return true/string
     */
    public function verificarQteEntrega($T006_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        $sql        = "select sum(T179_Quantidade_Entregue) from T179 WHERE T179_T006_Id='{$T006_Id}'";
        $result     = mysql_query($sql);
        $linha      = mysql_fetch_array($result);
        $entregue   = $linha['sum(T179_Quantidade_Entregue)'];
        $sql        = "select T006_Quantidade_Separacao from T006 WHERE T006_Id='{$T006_Id}'";
        $result     = mysql_query($sql);
        $linha      = mysql_fetch_array($result);
        $separado   = $linha['T006_Quantidade_Separacao'];
        $disponivel = $separado - $entregue;
        if ($novoValor > $disponivel) {
            mysql_query("update T006 set T006_Quantidade_Entregar_Temp='0' WHERE T006_Id='{$T006_Id}'");
            return "Há apenas $disponivel unidade(s) deste produto para entrega";
        } else {
            return true;
        }
    }

    public function imprimirEtiquetaCorreio($T268_Id = false,$codigosRastreio)
    {
        global $g;
        $campos = "";
        $retorno = array();
        //Dados de conexão do portal postal
        $codAgencia = $g['C004']['C004_Codigo_Agencia_Correios'];
        $login = $g['C004']['C004_Login_Portal_Postal'];
        $senha = $g['C004']['C004_Senha_Portal_Postal'];
        $formato = $g['C031']['formatoEtiquetaCorreio'];

        if(!(empty($g['C004']['C004_Codigo_Agencia_Correios'])) && !(empty($g['C004']['C004_Login_Portal_Postal']))  && !(empty($g['C031']['formatoEtiquetaCorreio'])) && !(empty($g['C004']['C004_Senha_Portal_Postal']))){
            $link = "http://www.portalpostal.com.br/LinkImpressaoSRO?codAgencia={$codAgencia}&login={$login}&senha={$senha}&sro={$codigosRastreio}&formato={$formato}";
            $retorno[0] = true;
            $retorno[1] =  "<script type='text/javascript'>window.open('{$link}','','width=800,height=920')</script>";
        }else{
            if(empty($g['C004']['C004_Codigo_Agencia_Correios'])){
                $campos .= "<br>Código da agência dos correios";
            }

            if(empty($g['C004']['C004_Login_Portal_Postal'])){
                $campos .= "<br>Login portal postal";
            }

            if(empty($g['C031']['formatoEtiquetaCorreio'])){
                $campos .= "<br>Formato da impressão da etiqueta";
            }

            if(empty($g['C004']['C004_Senha_Portal_Postal'])){
                $campos .= "<br>Senha portal postal";
            }

            $retorno[0] = false;
            $retorno[1] = "<b><font color='red'>Erro na impressão das etiquetas<br><br></font></b>Os seguintes campos de configuração estão em branco: ".$campos;
        }
       
        return $retorno;
    }

    /**
     * prePostagemPacote
     *
     * @param int $T268_Id
     * @return true/string
     */
    public function prePostagemPacote($T268_Id)
    {
        global $g, $cPasta_XML_Portal_Postal;
        $resposta = array();

        if(empty($T268_Id)){
            return 'Não foi informado a chave do pacote';
        }
        $dir = $g['pathDados'] . "portalPostal/";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cPasta_XML_Portal_Postal = $dir;


        require_once("bibliotecas/webservices/portalPostal/portalPostal.php");
        $prePostagem = new portalPostal();
        $prePostagem->criarConexao('http://www.portalpostal.com.br/axis2/services/PrePostagemWS?wsdl');


        $sqlT268 = mysql_query("SELECT * FROM T268
                                    LEFT JOIN D024 ON D024_Id=T268_D024_Id
                                    LEFT JOIN D148 ON D148_Id=T268_D148_Id
                                    LEFT JOIN D036 ON D036_Id=T268_D036_Id
                                    LEFT JOIN D018 ON IF(IFNULL(T268_D148_Id,0) > 0, D018_Id=D148_D018_Id, D018_Id=D024_D018_Id)
                                    LEFT JOIN D020 ON IF(IFNULL(T268_D148_Id,0) > 0, D020_Id=D148_D020_Id, D020_Id=D024_D020_Id)
                                    LEFT JOIN T007 ON T007_Id=T268_T007_Id
                                    LEFT JOIN C004 ON C004_Id=T268_C004_Id
                                        WHERE T268_Id = '{$T268_Id}'");
        $rowT268 = mysql_fetch_array($sqlT268);

        if (empty($rowT268['T007_Numero_Protocolo_Nfe'])) {
            $resposta[0] = false;
            $resposta[1] = "A nota fiscal não foi emitida, não é possível realizar a pré-postagem.";
            return $resposta;
        }

        //Dados de conexão do portal postal
        $prePostagem->setConexaoCodigoAgencia($rowT268['C004_Codigo_Agencia_Correios']);
        $prePostagem->setConexaoLogin($rowT268['C004_Login_Portal_Postal']);
        $prePostagem->setConexaoSenha($rowT268['C004_Senha_Portal_Postal']);

        $camposErros = array();
        //Dados do pacote
        $camposErros[] = $prePostagem->setChave($T268_Id);
        $camposErros[] = $prePostagem->setAosCuidados($rowT268['T268_Recebedor']);
        if(empty($rowT268['T268_D148_Id'])){
            $camposErros[] = $prePostagem->setDestinatario($rowT268['D024_Nome_Empresa']);
            $camposErros[] = $prePostagem->setCep($rowT268['D024_Cep']);
            $camposErros[] = $prePostagem->setEndereco($rowT268['D024_Endereco']);
            $camposErros[] = $prePostagem->setEnderecoNumero($rowT268['D024_Numero']);
            $camposErros[] = $prePostagem->setComplemento($rowT268['D024_Complemento']);
            $camposErros[] = $prePostagem->setBairro($rowT268['D024_Bairro']);
        } else {
            $camposErros[] = $prePostagem->setDestinatario($rowT268['D148_Nome']);
            $camposErros[] = $prePostagem->setCep($rowT268['D148_Cep']);
            $camposErros[] = $prePostagem->setEndereco($rowT268['D148_Rua']);
            $camposErros[] = $prePostagem->setEnderecoNumero($rowT268['D148_Numero']);
            $camposErros[] = $prePostagem->setComplemento($rowT268['D148_Complemento']);
            $camposErros[] = $prePostagem->setBairro($rowT268['D148_Bairro']);
        }
        $camposErros[] = $prePostagem->setCidade($rowT268['D020_Nome_Cidade']);
        $camposErros[] = $prePostagem->setEstado($rowT268['D018_UF']);
        $camposErros[] = $prePostagem->setNF($rowT268['T007_Numero_Nota_Fiscal']);
        $camposErros[] = $prePostagem->setChaveNF($rowT268['T007_Chave_Acesso_Nfe']);
        $camposErros[] = $prePostagem->setCentroCusto($rowT268['T268_Centro_Custo']);
        // $camposErros[] = $prePostagem->setServico($rowT268['T268_Servico']);
        $camposErros[] = $prePostagem->setServico($rowT268['D036_Tipo_Transporte']);
        $camposErros[] = $prePostagem->setAvisoRecebimento($rowT268['T268_Servico_Adicional_AR']);
        $camposErros[] = $prePostagem->setValorDeclarado($rowT268['T268_Valor_Declarado']);
        $camposErros[] = $prePostagem->setMaoPropria($rowT268['T268_Servico_Adicional_MP']);
        $camposErros[] = $prePostagem->setPeso($rowT268['T268_Peso']);
        $camposErros[] = $prePostagem->setAltura($rowT268['T268_Altura']);
        $camposErros[] = $prePostagem->setLargura($rowT268['T268_Largura']);
        $camposErros[] = $prePostagem->setComprimento($rowT268['T268_Comprimento']);

        $keys = array_keys($camposErros,"");
        foreach ($keys as $k) { unset($camposErros[$k]); }
        if(!empty($camposErros)){
            $resposta[0]=false;
            $resposta[1]='Campos em branco: ' . strtolower(implode(', ', $camposErros));
            return $resposta;
        }
        //Gera e transmite o XML
        $retorno = $prePostagem->consumirWS();
        // $retorno  = true;

        if($retorno === true){
            //Retorno da pré postagem
            $detalhes       = $prePostagem->getMsgDetalhes();
            $erro           = $prePostagem->getMsgErro();
            $codigoRastreio = $prePostagem->getCodigoObjeto();
            $siglaTriagem   = $prePostagem->getSiglaTriagem();

            if(!empty($erro)){
                mysql_query("UPDATE T268 SET T268_Detalhes_Postagem = '{$erro}' WHERE T268_Id = '{$T268_Id}'");
                $resposta[0]=false;
                $resposta[1]=$erro;
                return $resposta;
            } else {
                $status = ($detalhes == 'POSTADO') ? '10' : $rowT268['T268_Flag_Status'];
                if ($codigoRastreio=='erro') $codigoRastreio = '';
                mysql_query("UPDATE T268 SET T268_Codigo_Rastreio = '{$codigoRastreio}', T268_Sigla_Triagem = '{$siglaTriagem}', T268_C007_Id_Postagem = '{$g['usuarioAtual']}', T268_Flag_Status = '{$status}', T268_Detalhes_Postagem = '{$detalhes}', T268_Data_Hora_Postagem = CURRENT_TIMESTAMP() WHERE T268_Id = '{$T268_Id}'");

                mysql_query("UPDATE T007 SET T007_Codigo_Rastreio = '{$codigoRastreio}' WHERE T007_Id = '{$rowT268['T007_Id']}'");

                log("T007:".$rowT268['T268_T007_Id']."    T005:.".$rowT268['T007_T005_Id']."");   
                if($rowT268['T268_T007_Id']>0){
                    mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$rowT268['T007_T005_Id']},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Postagem correio. Código de Rastreio: {$codigoRastreio}','')");
                }

                $resposta[0] = true;
                $resposta[1] = $detalhes;
                $resposta[2] = $codigoRastreio;

                return $resposta;
            }
        } else {
            $resposta[0] = false;
            $resposta[1] = "Erro na comunicação com o portal postal - Erro: {$retorno}";
            // return  "Erro na comunicação com o portal postal - Erro: {$retorno}";
            return $resposta;
        }
    }

     /**
     * separarItemPedidoProdutoBox
     *
     * @param string $T005_Estoque_BOX
     * @param string $codBarras
     * @return true/string
     */
    public function separarItemPedidoProdutoBox($T005_Estoque_BOX, $codBarras)
    {
        global $g;
        $D001_Id = 0;
        $T006_Id = 0;
        $T066_Id = 0;

        $sql = " select D001_Id 
                 from D001 
                 where D001_Codigo_Barras = '{$codBarras}' ";

        $query = mysql_query($sql);
        
        if (mysql_num_rows($query) > 0) {
            $rowD001 = mysql_fetch_assoc($query);
            $D001_Id = $rowD001['D001_Id'];
            $Quantidade_Separacao = 1;
        } else {
            $sql = " select D083_D001_Id 
                     from D083
                     where D083_Codigo_Barras = '{$codBarras}' ";

            $query = mysql_query($sql);

            if (mysql_num_rows($query) > 0) {
                $rowD083 = mysql_fetch_assoc($query);
                $D001_Id = $rowD083['D083_D001_Id'];

                // pegar valor de novo campo a ser criado em D083? 
                // quando for buscar valor de D083 e for maior que 1
                // precisa verificar se não está separando a mais
                $Quantidade_Separacao = 1;
            }
        }

        if ($D001_Id == 0) {
            $msgErro = "Não foi encontrado um produto com o código de barras informado ({$codBarras}).";
            return array('sucesso' => false, 'msgErro' => $msgErro);
        }


        $query = mysql_query(" select D001_Codigo_Produto, D001_Descricao_Produto
                               from D001 
                               join D074 on D074_D001_Id = D001_Id
                               where D001_Id = {$D001_Id}");

        if (mysql_num_rows($query) > 0) {

            $rowD001 = mysql_fetch_assoc($query);

            $msgErro  = "O produto do código de barras informado possui um ou mais itens de engenharia.<br>";
            $msgErro .= "É necessário conferir seus itens ao invés deste produto diretamente!<br>";
            $msgErro .= "BOX: {$T005_Estoque_BOX}<br>";
            $msgErro .= "Cód barras: {$codBarras} <br> ";
            $msgErro .= "Cód Produto: {$rowD001['D001_Codigo_Produto']} <br> ";
            $msgErro .= "Descrição: {$rowD001['D001_Descricao_Produto']} ";

            return array('sucesso' => false, 'msgErro' => $msgErro);
        }

        // Verificando se usuário logado já está separando algum pedido do box
        // -- Versão do SQL que desconsidera pedido totalmente separado
        $sql = " select T005_Id 
                 from T005
                 join T006 on T006_T005_Id = T005_Id
                 where T005_C007_Id_Separou = {$g['usuarioAtual']}
                 and T005_C004_Id = {$g['empresaAtual']}
                 and T005_Flag_Status = '2'
                 and T005_Estoque_BOX = '{$T005_Estoque_BOX}' 
                 and T006_Quantidade > coalesce(T006_Quantidade_Separacao, 0) 
                 limit 1 ";

        // -- Versão do SQL que considera pedido mesmo já totalmente separado
        // $sql = " select T005_Id 
        //          from T005
        //          where T005_C007_Id_Separou = {$g['usuarioAtual']}
        //          and T005_C004_Id = {$g['empresaAtual']}
        //          and T005_Flag_Status = '2'
        //          and T005_Estoque_BOX = '{$T005_Estoque_BOX}' 
        //          limit 1 ";

        $query = mysql_query($sql);

        if (mysql_num_rows($query) > 0) {

            $rowT005 = mysql_fetch_assoc($query);
            $T005_Id = $rowT005['T005_Id'];

            $sql = " select T006_Id, T006_T066_Id, T005_C004_Id, T005_Id
                     from T006
                     join T005 on T006_T005_Id = T005_Id
                     join D009 on D009_Id = T006_D009_Id
                     join D049 on D049_Id = D009_D049_Id
                     where (T006_T006_Id is null or T006_T006_Id <= 0) 
                     and T006_Quantidade > 0 
                     and T006_Quantidade > coalesce(T006_Quantidade_Separacao, 0)
                     and IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
                     and T005_Id = {$T005_Id}
                     and D049_D001_Id = {$D001_Id}
                     limit 1 ";

            $query = mysql_query($sql);

            if (mysql_num_rows($query) == 0) {
                $sql = " select T006_Id, T006_T066_Id, T005_C004_Id, T005_Id, 
                                D074_Id, D074_Quantidade, T311_Id, T006_Quantidade_Separacao, T006_Quantidade
                         from T006
                         join T005 on T006_T005_Id = T005_Id
                         join D009 on D009_Id = T006_D009_Id
                         join D049 on D049_Id = D009_D049_Id
                         join D074 on D074_D001_Id = D049_D001_Id and D074_D001_Id_Composicao = {$D001_Id}
                         left join T311 on T311_T006_Id = T006_Id and T311_D074_Id = D074_Id
                         where (T006_T006_Id is null or T006_T006_Id <= 0) 
                         and T006_Quantidade > 0 
                         and T006_Quantidade > coalesce(T006_Quantidade_Separacao, 0)
                         and IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
                         and T005_Id = {$T005_Id}

                         and (coalesce(T311_Quantidade_Separada,0) = 0 OR 
                         coalesce(T311_Quantidade_Separada,0) < coalesce(T311_Quantidade_A_Separar,0))

                         limit 1 ";

                $query = mysql_query($sql);
            }

            if (mysql_num_rows($query) == 0) {

                $rowD001 = mysql_fetch_assoc(mysql_query(" select D001_Codigo_Produto, D001_Descricao_Produto from D001 where D001_Id = {$D001_Id}"));

                $msgErro =  "O produto do código de barras informado não existe ou não está pendente de separação no pedido.<br>";
                $msgErro .= "Pedido: {$T005_Id} <br> ";
                $msgErro .= "Cód barras: {$codBarras} <br> ";
                $msgErro .= "Cód Produto: {$rowD001['D001_Codigo_Produto']} <br> ";
                $msgErro .= "Descrição: {$rowD001['D001_Descricao_Produto']} ";

                return array('sucesso' => false, 'msgErro' => $msgErro);
            }
        } else {
            $sql = " select T006_Id, T006_T066_Id, T005_C004_Id, T005_Id
                     from T006
                     join T005 on T006_T005_Id = T005_Id
                     join D009 on D009_Id = T006_D009_Id
                     join D049 on D049_Id = D009_D049_Id
                     where (T006_T006_Id is null or T006_T006_Id <= 0) 
                     and T006_Quantidade > 0 
                     and T006_Quantidade > coalesce(T006_Quantidade_Separacao, 0)
                     and IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
                     and T005_Estoque_BOX = '{$T005_Estoque_BOX}'
                     and D049_D001_Id = {$D001_Id}
                     and T005_C004_Id = {$g['empresaAtual']}
                     and T005_Flag_Status = '2'
                     and coalesce(T005_C007_Id_Separou,0) = 0
                     order by T006_Id
                     limit 1 ";

            $query = mysql_query($sql);

            if (mysql_num_rows($query) == 0) {
                $sql = " select T006_Id, T006_T066_Id, T005_C004_Id, T005_Id, 
                                D074_Id, D074_Quantidade, T311_Id, T006_Quantidade_Separacao, T006_Quantidade
                         from T006
                         join T005 on T006_T005_Id = T005_Id
                         join D009 on D009_Id = T006_D009_Id
                         join D049 on D049_Id = D009_D049_Id
                         join D074 on D074_D001_Id = D049_D001_Id and D074_D001_Id_Composicao = {$D001_Id}
                         left join T311 on T311_T006_Id = T006_Id and T311_D074_Id = D074_Id
                         where (T006_T006_Id is null or T006_T006_Id <= 0) 
                         and T006_Quantidade > 0 
                         and T006_Quantidade > coalesce(T006_Quantidade_Separacao, 0)
                         and IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
                         and T005_Estoque_BOX = '{$T005_Estoque_BOX}'
                         and T005_C004_Id = {$g['empresaAtual']}
                         and T005_Flag_Status = '2'
                         and coalesce(T005_C007_Id_Separou,0) = 0

                         and (coalesce(T311_Quantidade_Separada,0) = 0 OR 
                              coalesce(T311_Quantidade_Separada,0) < coalesce(T311_Quantidade_A_Separar,0))

                         order by T006_Id
                         limit 1 ";

                log($sql);

                $query = mysql_query($sql);
            }

            if (mysql_num_rows($query) == 0) {

                $rowD001 = mysql_fetch_assoc(mysql_query(" select D001_Codigo_Produto, D001_Descricao_Produto from D001 where D001_Id = {$D001_Id}"));

                $msgErro  = "O produto do código de barras informado não existe no box ou não está pendente de separação no box.<br>";
                $msgErro .= "BOX: {$T005_Estoque_BOX}<br>";
                $msgErro .= "Cód barras: {$codBarras} <br> ";
                $msgErro .= "Cód Produto: {$rowD001['D001_Codigo_Produto']} <br> ";
                $msgErro .= "Descrição: {$rowD001['D001_Descricao_Produto']} ";

                return array('sucesso' => false, 'msgErro' => $msgErro);
            }
        }

        $row = mysql_fetch_assoc($query);
        $separouTodosD074 = false;

        if (!empty($row['D074_Id'])) {

            if (empty($row['T311_Id'])) {

                $T311_T006_Id = $row['T006_Id'];
                $T311_D074_Id = $row['D074_Id'];

                $T311_Quantidade_A_Separar = $row['T006_Quantidade'] * $row['D074_Quantidade'];

                $sql = " INSERT INTO T311 (T311_T006_Id, T311_D074_Id, T311_Quantidade_Separada, T311_Quantidade_A_Separar)
                         VALUES ({$T311_T006_Id}, {$T311_D074_Id}, {$Quantidade_Separacao}, {$T311_Quantidade_A_Separar})";
            } else {
                $T311_Id = $row['T311_Id'];

                $sql = " UPDATE T311 SET 
                            T311_Quantidade_Separada = T311_Quantidade_Separada + {$Quantidade_Separacao}
                         WHERE T311_Id = {$T311_Id} ";
            }

            mysql_query($sql);

            $T006_Id = $row['T006_Id'];

            $sql = " select D074_Id, T311_Quantidade_Separada, T006_Quantidade, D074_Quantidade
                     from T006
                     join D009 on D009_Id = T006_D009_Id
                     join D049 on D049_Id = D009_D049_Id
                     join D074 on D074_D001_Id = D049_D001_Id
                     left join T311 on T311_T006_Id = T006_Id and T311_D074_Id = D074_Id
                     where T006_Id = {$T006_Id}
                     and   coalesce(T311_Quantidade_Separada,0) < T006_Quantidade * D074_Quantidade ";

            $query = mysql_query($sql);

            if (mysql_num_rows($query) == 0) {
                $separouTodosD074 = true;
            }
        }

        if (empty($row['D074_Id']) || $separouTodosD074) {

            if ($separouTodosD074) {
                $T238_Quantidade_Separacao = $row['T006_Quantidade'];
            } else {
                $T238_Quantidade_Separacao = $Quantidade_Separacao;
            }

            $T238_T006_Id = $row['T006_Id'];
            $T238_T066_Id = $row['T006_T066_Id'];
            $T238_T005_Id = $row['T005_Id'];
            $T238_D004_Id = getD004($T238_T066_Id);
    
            $sql = " insert into T238 (T238_T006_Id, T238_T066_Id, T238_Quantidade_Separacao, T238_T005_Id, T238_D004_Id) 
                     values ({$T238_T006_Id}, {$T238_T066_Id}, {$T238_Quantidade_Separacao}, {$T238_T005_Id}, {$T238_D004_Id}) ";
    
            mysql_query($sql);
        }

        mysql_query(" update T005 set T005_C007_Id_Separou = {$g['usuarioAtual']} where T005_Id = {$row['T005_Id']} ");

        return array('sucesso' => true, 'T006_Id' => $row['T006_Id'], 'T005_Id' => $row['T005_Id']);
    }

    public function isPedidoSeparadoBoxLeitor($T005_Id) {

        $sql = " SELECT T006_Id
                 FROM T006 
                 JOIN T005 ON T005_Id = T006_T005_Id 
                 WHERE T006_T005_Id = '{$T005_Id}'
                 AND T006_Quantidade > coalesce(T006_Quantidade_Separacao, 0) ";

        $query = mysql_query($sql);
        return mysql_num_rows($query) == 0;
    }

    public function finalizarSeparacaoPedidoBoxLeitor($T005_Id, $T005_Quantidade_Volumes) {
        
		global $g;

		$T005_Flag_Status = "3";
		$T005_Especie = 'CX';
		$T005_Estoque_Box = 0;
		$T005_Flag_Totalizar_Peso = 'N';

		$sqlUpdT005 = " UPDATE T005 SET

                          /*
						  T005_Flag_Status = '{$T005_Flag_Status}',
						  T005_Nome_Status = T005_Status_Pedido(T005_Flag_Status, 1),
						  T005_Flag_Situacao = '',
                          */

						  T005_Quantidade_Volumes = '{$T005_Quantidade_Volumes}', 
						  T005_Especie = '{$T005_Especie}',

						  T005_Peso_Liquido = (select sum(D001_Peso_Unitario_Kg*T006_Quantidade) 
                                               from T006 
                                               join D009 on D009_Id=T006_D009_Id
                                               join D049 on D049_Id=D009_D049_Id
                                               join D001 on D049_D001_Id=D001_Id
                                               where T006_T005_Id = T005_Id),

						  T005_Peso_Bruto = (select sum(D001_Peso_Unitario_Bruto*T006_Quantidade) 
                                             from T006 
                                             join D009 on D009_Id=T006_D009_Id
                                             join D049 on D049_Id=D009_D049_Id
                                             join D001 on D049_D001_Id=D001_Id
                                             where T006_T005_Id = T005_Id)

					   /* T005_Estoque_Box = '{$T005_Estoque_Box}', 
						  T005_Flag_Totalizar_Peso = '{$T005_Flag_Totalizar_Peso}', */

		                WHERE T005_Id = '{$T005_Id}' ";

		mysql_query($sqlUpdT005);

        return true;
    }

    public function enviarXMLEmailTransportadoraUsrRoot($T007_Id) {

        $sqlT007 = " select T007_D024_Id, D022_Fax 
                     from T007
                     left join D022 ON D022_Id = T007_D022_Id 
                     where T007_Id = {$T007_Id} ";

        $qryT007 = mysql_query($sqlT007);
        $rowT007 = mysql_fetch_assoc($qryT007);

        if (empty($rowT007['D022_Fax'])) {
            return "Transportadora sem e-mail cadastrado para enviar XML!";
        }

        require_once('bibliotecas/classes/EST002.php');
        $EST002 = new EST002();
        $retornoProc = $this->enviarXMLEmailTransportadora($T007_Id, 63, $rowT007['T007_D024_Id']);

        return $retornoProc;
    }

    public function gravarQuantidadeConferida($dados) {
        global $g;

        $T313_Id    = $dados['T313_Id'];
        $T005_Id    = $dados['T005_Id'];
        $T006_Id    = $dados['T006_Id'];
        $D009_Id    = $dados['D009_Id'];
        $Quantidade = gCorrigeNumeroInverte($dados['Quantidade']);
        $Lote       = strtoupper($dados['T313_Lote']);
        $nrVolume   = $dados['novoValorNrVolume'];

        if(empty($nrVolume) or is_null($nrVolume) or $nrVolume == 0){
            $nrVolume = 1;
        }


        if (empty($T005_Id) || empty($T006_Id) || empty($D009_Id)) {
            return 'Erro ao alterar a quantidade, por favor entre em contato com o suporte técnico!';
        }

        if ($dados['T313_Id'] > 0) {
            return true;
        }

        $retorno = $this->validarSaldoPedido($dados);
        if (is_string($retorno)) {
            return $retorno;
        }
        

        if ($Quantidade > 0) {
            if (empty($T313_Id)) {
                $sqlInsert  = " INSERT INTO T313 ( ";
                $sqlInsert .=   " T313_T005_Id, ";
                $sqlInsert .=   " T313_T006_Id, ";
                $sqlInsert .=   " T313_D009_Id, ";
                $sqlInsert .=   " T313_C007_Id, ";
                $sqlInsert .=   " T313_Quantidade, ";
                $sqlInsert .=   " T313_Lote, ";
                $sqlInsert .=   " T313_Nr_Volume ";
                $sqlInsert .= " ) VALUES ( ";
                $sqlInsert .=   " '{$T005_Id}', ";
                $sqlInsert .=   " '{$T006_Id}', ";
                $sqlInsert .=   " '{$D009_Id}', ";
                $sqlInsert .=   " '{$g['usuarioAtual']}', ";
                $sqlInsert .=   " '{$Quantidade}', ";
                $sqlInsert .=   " '{$Lote}', ";
                $sqlInsert .=   " '{$nrVolume}'";
                $sqlInsert .= " ) ";

                mysql_query($sqlInsert);
            }
        }

        return true;
    }

    public function validarSaldoPedido($dados) {
        $T006_Id    = $dados['T006_Id'];
        $Quantidade = floatval($dados['Quantidade']);

        $sqlPedido  = " SELECT T006_Quantidade, ";
        $sqlPedido .=        " T006A_Quantidade_Conversao, ";
        $sqlPedido .=        " IFNULL((SELECT SUM(T238_Quantidade_Separacao) ";
        $sqlPedido .=                  " FROM T238 ";
        $sqlPedido .=                 " WHERE T238.T238_T006_Id = T006.T006_Id), 0) AS T238_Quantidade_Separacao, ";
        $sqlPedido .=        " IFNULL((SELECT SUM(T238A_Quantidade_Conversao_Separacao) ";
        $sqlPedido .=                  " FROM T238, T238A ";
        $sqlPedido .=                 " WHERE T238.T238_T006_Id   = T006.T006_Id ";
        $sqlPedido .=                   " AND T238A.T238A_T238_Id = T238.T238_Id), 0) AS T238A_Quantidade_Conversao_Separacao, ";
        $sqlPedido .=        " D002A_Utiliza_Conversao ";
        $sqlPedido .=   " FROM T006 ";
        $sqlPedido .=        " LEFT JOIN T006A ON T006_Id = T006A_T006_Id ";
        $sqlPedido .=        " LEFT JOIN D009  ON D009_Id = T006_D009_Id ";
        $sqlPedido .=        " LEFT JOIN D049  ON D049_Id = D009_D049_Id ";
        $sqlPedido .=        " LEFT JOIN D001  ON D001_Id = D049_D001_Id ";
        $sqlPedido .=        " LEFT JOIN D002  ON D002_Id = D001_D002_Id ";
        $sqlPedido .=        " LEFT JOIN D002A ON D002_Id = D002A_D002_Id ";
        $sqlPedido .=  " WHERE T006_Id = '{$T006_Id}' ";
        $queryPedido  = mysql_query($sqlPedido);
        $dadosPedido  = mysql_fetch_array($queryPedido);

        $usaConversao = $dadosPedido['D002A_Utiliza_Conversao'] === 'S';

        if ($usaConversao) {
            $qtdePedido    = $dadosPedido['T006A_Quantidade_Conversao'];
            $qtdeConferida = $dadosPedido['T238A_Quantidade_Conversao_Separacao'];
        } else {
            $qtdePedido    = $dadosPedido['T006_Quantidade'];
            $qtdeConferida = $dadosPedido['T238_Quantidade_Separacao'];
        }

        $saldo = floatval($qtdePedido) - floatval($qtdeConferida);
        if ($saldo < $Quantidade) {
            $mensagem  = "O saldo do pedido é menor que a quantidade conferida.<br><br>";
            $mensagem .= "<table>";
            $mensagem .=   "<tr>";
            $mensagem .=     "<td>Quantidade Conferida:</td>";
            $mensagem .=     "<td style='text-align:right'>". gCorrigeNumero($Quantidade) ."</td>";
            $mensagem .=   "</tr>";
            $mensagem .=   "<tr>";
            $mensagem .=     "<td>Quantidade Separada:</td>";
            $mensagem .=     "<td style='text-align:right'>". gCorrigeNumero($qtdeConferida) ."</td>";
            $mensagem .=   "</tr>"; 
            $mensagem .=   "<tr>";
            $mensagem .=     "<td>Quantidade Pedido:</td>";
            $mensagem .=     "<td style='text-align:right'>". gCorrigeNumero($qtdePedido) ."</td>";
            $mensagem .=   "</tr>";
            $mensagem .= "</table>";
            return $mensagem;
        } else {
            return true;
        }
    }

    public function volumesConferenciaReiniciarConferencia($T005_Id) {
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $deleteT315  = " DELETE FROM T315 ";
        $deleteT315 .= " WHERE T315_T005_Id = '{$T005_Id}' ";

        mysql_query($deleteT315);
        $erroT315 = mysql_error();
        if (!empty($erroT315)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Erro: $erroT315<br />Clausula: $deleteT315</div>";
        }

        $deleteT314  = " DELETE FROM T314 ";
        $deleteT314 .= " WHERE T314_T005_Id = '{$T005_Id}' ";

        mysql_query($deleteT314);
        $erroT314 = mysql_error();
        if (!empty($erroT314)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Erro: $erroT314<br />Clausula: $deleteT314</div>";
        }

        $deleteT313  = " DELETE FROM T313 ";
        $deleteT313 .= " WHERE T313_T005_Id = '{$T005_Id}' ";

        mysql_query($deleteT313);
        $erroT313 = mysql_error();
        if (!empty($erroT313)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Erro: $erroT313<br />Clausula: $deleteT313</div>";
        }
        
        $sql = "SELECT T238_ID FROM T238 WHERE T238_T005_Id = '{$T005_Id}'";
        $DADOST238 = mysql_query($sql);
        $varT238 = mysql_fetch_array($DADOST238);

        $deleteT238a  = " DELETE FROM T238A ";
        $deleteT238a .= " WHERE T238A_T238_Id in (SELECT T238_Id FROM T238 WHERE T238_T005_Id = '{$T005_Id}') ";

        mysql_query($deleteT238a);
        $erroT238a = mysql_error();
        if (!empty($erroT238a)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Erro: $erroT238a<br />Clausula: $deleteT238a</div>";
        }

        $deleteT238  = " DELETE FROM T238 ";
        $deleteT238 .= " WHERE T238_T005_Id = '{$T005_Id}' ";

        mysql_query($deleteT238);
        $erroT238 = mysql_error();
        if (!empty($erroT238)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Erro: $erroT238<br />Clausula: $erroT238</div>";
        }

        mysql_query("COMMIT");

        return true;
    }

    public function volumesConferenciaReiniciarSeparacao($T005_Id) {
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $deleteT313  = " DELETE FROM T313 ";
        $deleteT313 .= " WHERE T313_T005_Id = '{$T005_Id}' ";

        mysql_query($deleteT313);
        $erroT313 = mysql_error();
        if (!empty($erroT313)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Erro: $erroT313<br />Clausula: $deleteT313</div>";
        }

        mysql_query("COMMIT");

        return true;
    }

    public function volumesConferenciaGerar($T005_Id) {
        global $g;

        #region -- DADOS DA CONFERENCIA
        $sqlConferencia  = " SELECT T313_Id, ";
        $sqlConferencia .=        " T313_T005_Id, ";
        $sqlConferencia .=        " T313_T006_Id, ";
        $sqlConferencia .=        " T313_D009_Id, ";
        $sqlConferencia .=        " T313_C007_Id, ";
        $sqlConferencia .=        " T313_Quantidade, ";
        $sqlConferencia .=        " T313_D109_Id_Primeiro, ";
        $sqlConferencia .=        " T313_D109_Id_Segundo, ";
        $sqlConferencia .=        " T313_Lote, ";
        $sqlConferencia .=        " T313_Nr_Volume, ";
        $sqlConferencia .=        " T006_D004_Id, ";
        $sqlConferencia .=        " T006_T066_Id, ";
        $sqlConferencia .=        " D001_Peso_Unitario_Bruto, ";
        $sqlConferencia .=        " D001_Peso_Unitario_Kg, ";
        $sqlConferencia .=        " D002A_Utiliza_Conversao ";
        $sqlConferencia .=   " FROM T313 ";
        $sqlConferencia .=        " LEFT JOIN T006  ON T006_Id = T313_T006_Id ";
        $sqlConferencia .=        " LEFT JOIN D009  ON D009_Id = T313_D009_Id ";
        $sqlConferencia .=        " LEFT JOIN D049  ON D049_Id = D009_D049_Id ";
        $sqlConferencia .=        " LEFT JOIN D001  ON D001_Id = D049_D001_Id ";
        $sqlConferencia .=        " LEFT JOIN D002  ON D002_Id = D001_D002_Id ";
        $sqlConferencia .=        " LEFT JOIN D002A ON D002_Id = D002A_D002_Id ";
        $sqlConferencia .=  " WHERE T313_T005_Id = '{$T005_Id}' ";
        $sqlConferencia .=    " AND T313_Quantidade > 0 ";
        $queryConferencia  = mysql_query($sqlConferencia);
        #endregion -- DADOS DA CONFERENCIA

        #region -- VALIDACOES
        $registros = mysql_num_rows($queryConferencia);
        if ($registros == 0) {
            return "Não existem quantidades conferidas para gerar um volume.";
        }else if($registros > 1){
            $dadosNrVolumes = mysql_query("select 
                                                count(T313_Id) as n
                                           from 
                                                T313
                                           where
                                                T313_Nr_Volume > 1");
            $varNrVolumes = mysql_fetch_array($dadosNrVolumes);
            if($varNrVolumes['n'] > 0){
                return "Só é possível gerar 1 volume com produtos diferentes.";
            }
        }

        $D200_Id = $g['C031']['embalagemPadraoVolume'];

        if (empty($D200_Id)) {
            return "Não existe uma embalagem padrão configurada para gerar o volume, contate o suporte.";
        }
        #endregion -- VALIDACOES

        #region -- PROCESSA OS ITENS CONFERIDOS
        $itensVolume      = array();
        $pesoTotalBruto   = 0;
        $pesoTotalLiquido = 0;

        while ($dadosConferencia = mysql_fetch_array($queryConferencia)) {
            $usaConversao        = $dadosConferencia['D002A_Utiliza_Conversao'] === 'S';
            $quantidade          = $usaConversao ? floatval($dadosConferencia['T313_Quantidade']) * floatval($dadosConferencia['D001_Peso_Unitario_Kg']) : $dadosConferencia['T313_Quantidade'];
            $quantidadeConversao = $dadosConferencia['T313_Quantidade'];
            $lote                = $dadosConferencia['T313_Lote'];
            $nrVolume            = $dadosConferencia['T313_Nr_Volume'];
            $pesoBruto           = floatval($dadosConferencia['T313_Quantidade']) * floatval($dadosConferencia['D001_Peso_Unitario_Bruto']);
            $pesoLiquido         = floatval($dadosConferencia['T313_Quantidade']) * floatval($dadosConferencia['D001_Peso_Unitario_Kg']);
            $pesoTotalBruto      += $pesoBruto;
            $pesoTotalLiquido    += $pesoLiquido;

            $qtde = floatval($dadosConferencia['T313_Quantidade']);
            if($qtde%$nrVolume != 0){
                return "<div class='msgError'>Erro: Não é possível criar os volumes com essa quantidade.</div>";
            }

            $qtdeVolume          = $quantidade/$nrVolume;
            $qtdeVolumeConversao = $quantidadeConversao/$nrVolume;


            $item = array(
                'T238_D004_Id'              => $dadosConferencia['T006_D004_Id'],
                'T238_T066_Id'              => $dadosConferencia['T006_T066_Id'],
                'T238A_D109_Id_Primeiro'    => $dadosConferencia['T313_D109_Id_Primeiro'],
                'T238A_D109_Id_Segundo'     => $dadosConferencia['T313_D109_Id_Segundo'],
                'T315_T005_Id'              => $dadosConferencia['T313_T005_Id'],
                'T315_T006_Id'              => $dadosConferencia['T313_T006_Id'],
                'T315_D009_Id'              => $dadosConferencia['T313_D009_Id'],
                'T315_Quantidade'           => $qtdeVolume,
                'T315_Quantidade_Conversao' => $qtdeVolumeConversao,
                'T315_Lote'                 => $lote,
                'T315_Peso_Bruto'           => $pesoBruto,
                'T315_Peso_Liquido'         => $pesoLiquido
            );

            array_push($itensVolume, $item);
        }
        #endregion -- PROCESSA OS ITENS CONFERIDOS

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $n = 0;
        while($n < $nrVolume){

            #region -- REALIZA A GRAVACAO DO VOLUME
            $volume = $this->volumesGerarDados($T005_Id);

            $insertT314  = " INSERT INTO T314 ( ";
            $insertT314 .=   " T314_T005_Id, ";
            $insertT314 .=   " T314_C007_Id, ";
            $insertT314 .=   " T314_D200_Id, ";
            $insertT314 .=   " T314_Volume, ";
            $insertT314 .=   " T314_Identificador, ";
            $insertT314 .=   " T314_Sequencia, ";
            $insertT314 .=   " T314_Peso_Bruto, ";
            $insertT314 .=   " T314_Peso_Liquido ";
            $insertT314 .= " ) VALUES ( ";
            $insertT314 .=   " '{$T005_Id}', ";
            $insertT314 .=   " '{$g['usuarioAtual']}', ";
            $insertT314 .=   " '{$D200_Id}', ";
            $insertT314 .=   " '{$volume['T314_Volume']}', ";
            $insertT314 .=   " '{$volume['T314_Identificador']}', ";
            $insertT314 .=   " '{$volume['T314_Sequencia']}', ";
            $insertT314 .=   " '{$pesoTotalBruto}', ";
            $insertT314 .=   " '{$pesoTotalLiquido}' ";
            $insertT314 .= " ); ";
            mysql_query($insertT314);

            $erroT314 = mysql_error();
            if (!empty($erroT314)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $erroT314<br />Clausula: $insertT314</div>";
            }

            $T314_Id = $g['mysqlLastId'];
            #endregion -- REALIZA A GRAVACAO DO VOLUME

            foreach ($itensVolume as $item ) {
                #region -- REALIZA A GRAVACAO DO ITEM DO VOLUME
                $insertT315  = " INSERT INTO T315 ( ";
                $insertT315 .=   " T315_T314_Id, ";
                $insertT315 .=   " T315_T005_Id, ";
                $insertT315 .=   " T315_T006_Id, ";
                $insertT315 .=   " T315_D009_Id, ";
                $insertT315 .=   " T315_Quantidade, ";
                $insertT315 .=   " T315_Quantidade_Conversao, ";
                $insertT315 .=   " T315_Peso_Bruto, ";
                $insertT315 .=   " T315_Peso_Liquido, ";
                $insertT315 .=   " T315_Lote ";
                $insertT315 .= " ) VALUES ( ";
                $insertT315 .=   " '{$T314_Id}', ";
                $insertT315 .=   " '{$T005_Id}', ";
                $insertT315 .=   " '{$item['T315_T006_Id']}', ";
                $insertT315 .=   " '{$item['T315_D009_Id']}', ";
                $insertT315 .=   " '{$item['T315_Quantidade']}', ";
                $insertT315 .=   " '{$item['T315_Quantidade_Conversao']}', ";
                $insertT315 .=   " '{$item['T315_Peso_Bruto']}', ";
                $insertT315 .=   " '{$item['T315_Peso_Liquido']}', ";
                $insertT315 .=   " '{$item['T315_Lote']}' ";
                $insertT315 .= " ); ";
                mysql_query($insertT315);
        
                $erroT315 = mysql_error();
                if (!empty($erroT315)) {
                    mysql_query("ROLLBACK");
                    return "<div class='msgError'>Erro: $erroT315<br />Clausula: $insertT315</div>";
                }
                $T315_Id = $g['mysqlLastId'];
                #endregion -- REALIZA A GRAVACAO DO ITEM DO VOLUME

                #region -- REALIZA A GRAVACAO DA SEPARACAO DO PEDIDO
                $insertT238  = " INSERT INTO T238 ( ";
                $insertT238 .=   " T238_T005_Id, ";
                $insertT238 .=   " T238_T006_Id, ";
                $insertT238 .=   " T238_D004_Id, ";
                $insertT238 .=   " T238_Quantidade_Separacao, ";
                $insertT238 .=   " T238_T066_Id ";
                $insertT238 .= " ) VALUES ( ";
                $insertT238 .=   " '{$T005_Id}', ";
                $insertT238 .=   " '{$item['T315_T006_Id']}', ";
                $insertT238 .=   " '{$item['T238_D004_Id']}', ";
                $insertT238 .=   " '{$item['T315_Quantidade']}', ";
                $insertT238 .=   " '{$item['T238_T066_Id']}' ";
                $insertT238 .= " ); ";
                mysql_query($insertT238);

                $erroT238 = mysql_error();
                if (!empty($erroT238)) {
                    mysql_query("ROLLBACK");
                    return "<div class='msgError'>Erro: $erroT238<br />Clausula: $insertT238</div>";
                }
                $T238_Id = $g['mysqlLastId'];

                $insertT238A  = " INSERT INTO T238A ( ";
                $insertT238A .=   " T238A_T238_Id, ";
                $insertT238A .=   " T238A_T314_Id, ";
                $insertT238A .=   " T238A_T315_Id, ";
                $insertT238A .=   " T238A_D109_Id_Primeiro, ";
                $insertT238A .=   " T238A_D109_Id_Segundo, ";
                $insertT238A .=   " T238A_Quantidade_Conversao_Separacao ";
                $insertT238A .= " ) VALUES ( ";
                $insertT238A .=   " '{$T238_Id}', ";
                $insertT238A .=   " '{$T314_Id}', ";
                $insertT238A .=   " '{$T315_Id}', ";
                $insertT238A .=   " '{$item['T238A_D109_Id_Primeiro']}', ";
                $insertT238A .=   " '{$item['T238A_D109_Id_Segundo']}', ";
                $insertT238A .=   " '{$item['T315_Quantidade_Conversao']}' ";
                $insertT238A .= " ); ";
                mysql_query($insertT238A);

                $erroT238A = mysql_error();
                if (!empty($erroT238A)) {
                    mysql_query("ROLLBACK");
                    return "<div class='msgError'>Erro: $erroT238A<br />Clausula: $insertT238A</div>";
                }
                #endregion -- REALIZA A GRAVACAO DA SEPARACAO DO PEDIDO
            }

            $deleteT313  = " UPDATE T313 SET ";
            $deleteT313 .=   " T313_Quantidade = 0, ";
            $deleteT313 .=   " T313_Nr_Volume = 1, ";
            $deleteT313 .=   " T313_Lote = '' ";
            $deleteT313 .= " WHERE T313_T005_Id = '{$T005_Id}' ";

            mysql_query($deleteT313);
            $erroT313 = mysql_error();
            if (!empty($erroT313)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $erroT313<br />Clausula: $deleteT313</div>";
            }
            $n++;
        }

        mysql_query("COMMIT");

        return true;
    }

    public function volumesGerarDados($T005_Id) {
        $sql  = " SELECT IFNULL(MAX(T314_Sequencia), 0) + 1 AS T314_Sequencia ";
        $sql .=   " FROM T314 ";
        $sql .=  " WHERE T314_T005_Id = '{$T005_Id}' ";
        $query = mysql_query($sql);
        $dados = mysql_fetch_array($query);

        $sequencia = $dados['T314_Sequencia'];

        while (strlen($sequencia) < 2) {
            $sequencia = '0' . $sequencia;
        }

        $volume = array(
            'T314_Identificador' => '',
            'T314_Sequencia'     => $dados['T314_Sequencia'],
            'T314_Volume'        => $T005_Id . $sequencia,
        );

        return $volume;
    }

    public function volumesConferenciaCancelar($T314_Id) {
        $sql  = " SELECT T238_Id ";
        $sql .=   " FROM T238 ";
        $sql .=        " LEFT JOIN T238A ON T238_Id = T238A_T238_Id ";
        $sql .=  " WHERE T238A_T314_Id = '{$T314_Id}' ";
        $query  = mysql_query($sql);

        $T238_Id = '';
        while ($dados = mysql_fetch_array($query)) {
            if (!empty($T238_Id)) {
                $T238_Id .= ', ';
            }

            $T238_Id .= $dados['T238_Id'];
        }

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $deleteT315  = " DELETE FROM T315 ";
        $deleteT315 .= " WHERE T315_T314_Id = '{$T314_Id}' ";

        mysql_query($deleteT315);
        $erroT315 = mysql_error();
        if (!empty($erroT315)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Erro: $erroT315<br />Clausula: $deleteT315</div>";
        }

        $deleteT314  = " DELETE FROM T314 ";
        $deleteT314 .= " WHERE T314_Id = '{$T314_Id}' ";

        mysql_query($deleteT314);
        $erroT314 = mysql_error();
        if (!empty($erroT314)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Erro: $erroT314<br />Clausula: $deleteT314</div>";
        }


        $deleteT238A  = " DELETE FROM T238A ";
        $deleteT238A .= " WHERE T238A_T238_Id IN ({$T238_Id}) ";

        mysql_query($deleteT238A);
        $erroT238A = mysql_error();
        if (!empty($erroT238A)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Erro: $erroT238A<br />Clausula: $deleteT238A</div>";
        }

        $deleteT238  = " DELETE FROM T238 ";
        $deleteT238 .= " WHERE T238_Id IN ({$T238_Id}) ";

        mysql_query($deleteT238);
        $erroT238 = mysql_error();
        if (!empty($erroT238)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Erro: $erroT238<br />Clausula: $deleteT238</div>";
        }

        mysql_query("COMMIT");
        return true;
    }

    public function volumesConferenciaFinalizar($T005_Id, $D047_Id, $D109_Id_Primeiro, $D109_Id_Segundo) {
        require_once('bibliotecas/classes/VEN002.php');
        $VEN002 = new VEN002();

        $validacaoPedido = $this->validarPedidoParaAvancar($T005_Id, '10');
        if ($validacaoPedido !== true) {
            return $validacaoPedido;
        }

        $sqlVolume  = " SELECT COUNT(T314.T314_ID)                    AS VOLUMES, ";
        $sqlVolume .=        " IFNULL(SUM(T314.T314_PESO_BRUTO), 0)   AS PESO_BRUTO, ";
        $sqlVolume .=        " IFNULL(SUM(T314.T314_PESO_LIQUIDO), 0) AS PESO_LIQUIDO ";
        $sqlVolume .=   " FROM T314 ";
        $sqlVolume .=  " WHERE T314.T314_T005_ID = {$T005_Id} ";
        $queryVolume = mysql_query($sqlVolume);
        $dadosVolume = mysql_fetch_array($queryVolume);

        if ($dadosVolume['VOLUMES'] == '0') {
            return 'Não é possível finalizar a conferência pois nenhum volume foi gerado.';
        }

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $retorno = $VEN002->dividirPedidoSaldoConferido($T005_Id, $D047_Id);

        if ($retorno == true) {
            $retorno = $this->volumesRefazerSequencia($T005_Id);
        } else {
            return $retorno;
        }

        if ($retorno == true) {
            #region ATUALIZA OS DADOS NO PEDIDO
            $update  = " UPDATE T005 SET ";
            $update .=   " T005_Quantidade_Volumes = {$dadosVolume['VOLUMES']}, ";
            $update .=   " T005_Peso_Total         = {$dadosVolume['PESO_LIQUIDO']}, ";
            $update .=   " T005_Peso_Bruto_Total   = {$dadosVolume['PESO_BRUTO']}, ";
            $update .=   " T005_Flag_Status        = '3', ";
            $update .=   " T005_Nome_Status        = T005_Status_Pedido(T005_Flag_Status, 1) ";
            $update .= " WHERE T005_Id = {$T005_Id} ";
            $update .= "   AND T005_Flag_Status = '10' ";
            $update .= "   AND NOT EXISTS (SELECT 1 FROM T007 WHERE T007_T005_Id = T005.T005_Id AND T007_Flag_Cancelada != 'S' AND T007_Flag_Inutilizado != 'S' AND NOT (IFNULL(T005.T005_Flag_Pdv_Importado, 'N') = 'S' AND IFNULL(T007.T007_Flag_PDV, 'N') = 'S')) ";
            mysql_query($update);
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $erro<br />Clausula: $update</div>";
            }
            if (!$this->pedidoFoiAvancado($T005_Id, '3')) {
                mysql_query("ROLLBACK");
                return 'O pedido foi atualizado por outro usuário ou já possui NF-e ativa. Atualize a tela antes de continuar.';
            }
            #endregion ATUALIZA OS DADOS NO PEDIDO

            #region ATUALIZA OS SEPARADORES NAS CONFERENCIAS
            $updateSeparador1  = " UPDATE T238A ";
            $updateSeparador1 .=   " LEFT JOIN T238 ON (T238A_T238_Id = T238_Id) ";
            $updateSeparador1 .= " SET ";
            $updateSeparador1 .=   " T238A_D109_Id_Primeiro = {$D109_Id_Primeiro} ";
            $updateSeparador1 .= " WHERE T238_T005_Id = {$T005_Id} ";
            $updateSeparador1 .=   " AND IFNULL(T238A_D109_Id_Primeiro, 0) = 0 ";
            mysql_query($updateSeparador1);
            $erroSeparador1 = mysql_error();
            if (!empty($erroSeparador1)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $erroSeparador1<br />Clausula: $updateSeparador1</div>";
            }

            $updateSeparador2  = " UPDATE T238A ";
            $updateSeparador2 .=   " LEFT JOIN T238 ON (T238A_T238_Id = T238_Id) ";
            $updateSeparador2 .= " SET ";
            $updateSeparador2 .=   " T238A_D109_Id_Segundo = {$D109_Id_Segundo} ";
            $updateSeparador2 .= " WHERE T238_T005_Id = {$T005_Id} ";
            $updateSeparador2 .=   " AND IFNULL(T238A_D109_Id_Segundo, 0) = 0 ";
            mysql_query($updateSeparador2);
            $erroSeparador2 = mysql_error();
            if (!empty($erroSeparador2)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $erroSeparador2<br />Clausula: $updateSeparador2</div>";
            }
            #endregion ATUALIZA OS SEPARADORES NAS CONFERENCIAS

            #region REMOVE A CONFERENCIA TEMPORARIA
            $deleteT313  = " DELETE FROM T313 ";
            $deleteT313 .= " WHERE T313_T005_Id = '{$T005_Id}' ";
            mysql_query($deleteT313);
            $erroT313 = mysql_error();
            if (!empty($erroT313)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $erroT313<br />Clausula: $deleteT313</div>";
            }
            #endregion REMOVE A CONFERENCIA TEMPORARIA

            mysql_query("COMMIT");

            return true;
        } else {
            return $retorno;
        }
    }

    public function est002VolumesConferenciaValidar($T005_Id) {
        $retorno['code']    = true;
        $retorno['parcial'] = '0';
        $retorno['data']    = '';

        #region VALIDA SE EXISTE ALGUMA SEPARACAO EM ANDAMENTO
        $sqlSeparacao  = " SELECT IFNULL(SUM(T313.T313_Quantidade), 0) AS SEPARACAO ";
        $sqlSeparacao .=   " FROM T313 ";
        $sqlSeparacao .=  " WHERE T313.T313_T005_ID = {$T005_Id} ";
        $querySeparacao = mysql_query($sqlSeparacao);
        $dadosSeparacao = mysql_fetch_array($querySeparacao);

        if ($dadosSeparacao['SEPARACAO'] > '0') {
            $retorno['code'] = false;
            $retorno['data'] = 'Existe uma separação em andamento, para finalizar a conferência é necessário finalizar a geração do volume ou cancelar a separação atual.';
            return $retorno;
        }
        #endregion VALIDA SE EXISTE ALGUMA SEPARACAO EM ANDAMENTO

        #region VALIDA SE EXISTE ALGUM VOLUME GERADO
        $sqlVolume  = " SELECT T314_Id ";
        $sqlVolume .=   " FROM T314 ";
        $sqlVolume .=  " WHERE T314.T314_T005_ID = {$T005_Id} ";
        $queryVolume = mysql_query($sqlVolume);
        $dadosVolume = mysql_fetch_array($queryVolume);

        if (empty($dadosVolume['T314_Id'])) {
            $retorno['code'] = false;
            $retorno['data'] = 'Não existe nenhum volume gerado para essa conferência e nenhuma separação em andamento.<br><br>Não é possível finalizar uma conferência sem nenhum item conferido.';
            return $retorno;
        }
        #endregion VALIDA SE EXISTE ALGUM VOLUME GERADO

        #region VALIDA SE A CONFERENCIA É TOTAL OU PARCIAL
        $sqlPedido  = " SELECT T005.* ";
        $sqlPedido .=   " FROM T005 ";
        $sqlPedido .=  " WHERE T005.T005_Id = '{$T005_Id}' ";
        $sqlPedido .=    " AND EXISTS (SELECT T006.T006_T005_Id ";
        $sqlPedido .=                  " FROM T006, T006A ";
        $sqlPedido .=                 " WHERE T005.T005_Id = T006.T006_T005_Id ";
        $sqlPedido .=                   " AND T006.T006_Id = T006A.T006A_T006_Id ";
        $sqlPedido .=                   " AND ((T006.T006_Quantidade             > IFNULL(T006.T006_Quantidade_Separacao, 0)) ";
        $sqlPedido .=                   "   OR (T006A.T006A_Quantidade_Conversao > IFNULL(T006A.T006A_Quantidade_Conversao_Separacao, 0)))) ";
        $queryPedido = mysql_query($sqlPedido);
        $dadosPedido = mysql_fetch_assoc($queryPedido);

        if (!empty($dadosPedido['T005_Id'])) {
            $retorno['code']    = true;
            $retorno['parcial'] = '1';
            $retorno['data']    = '';

            return $retorno;
        }
        #endregion VALIDA SE A CONFERENCIA É TOTAL OU PARCIAL

        return $retorno;
    }

    public function volumesRefazerSequencia($T005_Id) {
        $sqlVolume  = " SELECT T314_Id ";
        $sqlVolume .=   " FROM T314 ";
        $sqlVolume .=  " WHERE T314_T005_Id = '{$T005_Id}' ";
        $sqlVolume .=  " ORDER BY T314_Sequencia ASC ";
        $queryVolume  = mysql_query($sqlVolume);

        $novaSequencia = 0;
        while ($volume = mysql_fetch_array($queryVolume)) {
            $novaSequencia += 1;
            $sequencia     = $novaSequencia;

            while (strlen($sequencia) < 2) {
                $sequencia = '0' . $sequencia;
            }

            $T314_Id     = $volume['T314_Id'];
            $T314_Volume = $T005_Id . $sequencia;

            $updateVolume  = " UPDATE T314 SET ";
            $updateVolume .=   " T314_Volume = '{$T314_Volume}', ";
            $updateVolume .=   " T314_Sequencia = '{$novaSequencia}' ";
            $updateVolume .= " WHERE T314_Id = '{$T314_Id}' ";
            mysql_query($updateVolume);
            $erroUpdate = mysql_error();
            if (!empty($erroUpdate)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $erroUpdate<br />Clausula: $updateVolume</div>";
            }
        }

        return true;
    }

    public function triggerStatusPedido($T005_Id) {
        return true;
    }

    public function triggerAposImpressaoPedido($T005_Id) {
        return true;
    }

    public function triggerAposSeparacaoPedido($T005_Id) {
        return true;
    }

    public function triggerAposFaturamentoPedido($T005_Id) {
        return true;
    }

    /**
     * retornarPedidoExpedicao
     *
     * Retorna o pedido para uma etapa anterior da expedição, limpando os dados
     * das etapas posteriores quando isso for necessário para manter a consistência.
     *
     * @param int $T005_Id
     * @param string|int $T005_Flag_Status
     * @return true/string
     */
    public function retornarPedidoExpedicao($T005_Id, $T005_Flag_Status) {
        global $g;

        // Mapeia a descrição e a ordem das etapas suportadas por esse retorno.
        $etapas = [
            '1'  => 'Impressão',
            '2'  => 'Separação',
            '10' => 'Conferência',
            '3'  => 'Gerar nota fiscal',
            '4'  => 'Aguardando coleta',
            '5'  => 'Coletados'
        ];
        $ordemEtapas = [
            '1'  => 0,
            '2'  => 1,
            '10' => 2,
            '3'  => 3,
            '4'  => 4,
            '5'  => 5
        ];

        // Valida a entrada antes de tocar em qualquer dado do pedido.
        if ($T005_Id <= 0) { return 'Pedido inválido.'; }

        if (!isset($ordemEtapas[$T005_Flag_Status]) || $T005_Flag_Status == '5') { 
            return 'Etapa de retorno inválida.'; 
        }

        // Busca o status atual para confirmar se o retorno realmente é para uma etapa anterior.
        $sqlPedido = "SELECT T005_Id, T005_Flag_Status FROM T005 WHERE T005_Id = '{$T005_Id}'";
        $pedido = mysql_fetch_assoc(mysql_query($sqlPedido));

        if (empty($pedido['T005_Id'])) { return 'Pedido não encontrado.'; }

        $statusAtual = (string)$pedido['T005_Flag_Status'];
        if (!isset($ordemEtapas[$statusAtual])) { 
            return 'O pedido não está em uma etapa suportada para retorno.'; 
        }

        if ($statusAtual == '8') { 
            return 'Pedido está cancelado, não pode ser alterado.'; 
        }

        if ($ordemEtapas[$T005_Flag_Status] >= $ordemEtapas[$statusAtual]) { return 'Selecione uma etapa anterior ao estágio atual do pedido.'; }

        // Impede voltar o pedido enquanto existir pedido de transporte ativo gerado em outra empresa.
        $sqlPedidoTransporte = mysql_query("SELECT T005_Id FROM T005 WHERE T005_T005_Id_Origem = '{$T005_Id}' AND T005_Flag_Status != 8 AND T005_T224_Id > 0");
        if (mysql_num_rows($sqlPedidoTransporte) > 0) {
            return 'Existem itens faturados por outra empresa, para retornar o pedido é necessário cancelar o pedido gerado na outra empresa.';
        }

        // Mantém a restrição antiga de NFC-e, pois esse tipo de documento não pode entrar no fluxo de retorno.
        $T007 = mysql_query("SELECT T006_Id FROM T006
                                LEFT JOIN T008 ON T006_Id = T008_T006_Id
                                LEFT JOIN T007 ON T007_Id = T008_T007_Id
                            WHERE T006_T005_Id = '{$T005_Id}'
                                AND T007_Flag_ACP = '8'
                                AND T007_Flag_Cancelada != 'S'
                                AND T007_Flag_Inutilizado != 'S'
                                AND (T007_Protocolo_Inutilizacao = '' OR T007_Protocolo_Inutilizacao IS NULL)");
        if (mysql_num_rows($T007) > 0) {
            return 'Este pedido está vinculado a uma NFC-e, não pode ser retornado.';
        }

        // Se existir NF ativa, o pedido só pode permanecer em etapas posteriores ao faturamento.
        if ($ordemEtapas[$T005_Flag_Status] < $ordemEtapas['4']) {
            
            $sqlNFAtiva = mysql_query("SELECT T007_Id FROM T007 WHERE T007_T005_Id = '{$T005_Id}' AND T007_Flag_Cancelada != 'S' AND T007_Flag_Inutilizado != 'S' LIMIT 1");
            if (mysql_num_rows($sqlNFAtiva) > 0) {
                return 'Existe nota fiscal ativa para esse pedido. Cancele a nota antes de retornar para uma etapa anterior ao faturamento.';
            }
        }

        // Inicia a transação para que o retorno do pedido seja aplicado como uma operação única.
        mysql_query("START TRANSACTION");

        // Ao voltar para antes da conferência, remove todos os registros temporários e finais criados nessa etapa.
        if ($ordemEtapas[$statusAtual] >= $ordemEtapas['10'] && $ordemEtapas[$T005_Flag_Status] < $ordemEtapas['10']) {
            $delT315 = "DELETE FROM T315 WHERE T315_T005_Id = '{$T005_Id}'";
            if (!mysql_query($delT315)) {
                mysql_query("ROLLBACK"); return 'Erro ao remover os volumes do pedido: <br><br>' . mysql_error();
            }

            $delT314 = "DELETE FROM T314 WHERE T314_T005_Id = '{$T005_Id}'";
            if (!mysql_query($delT314)) {
                mysql_query("ROLLBACK"); return 'Erro ao remover os volumes da conferência: <br><br>' . mysql_error();
            }

            $delT313 = "DELETE FROM T313 WHERE T313_T005_Id = '{$T005_Id}'";
            if (!mysql_query($delT313)) {
                mysql_query("ROLLBACK"); return 'Erro ao limpar a conferência em andamento.';
            }

            $delT238A = "DELETE FROM T238A WHERE T238A_T238_Id IN (SELECT T238_Id FROM T238 WHERE T238_T005_Id = '{$T005_Id}')";
            if (!mysql_query($delT238A)) {
                mysql_query("ROLLBACK"); return 'Erro ao limpar os itens conferidos: <br><br>' . mysql_error();
            }

            $updtT005 = "UPDATE T005 SET T005_C007_Id_Conferiu = NULL, T005_Quantidade_Volumes = 0, T005_Peso_Total = 0, T005_Peso_Bruto_Total = 0 WHERE T005_Id = '{$T005_Id}'";
            if (!mysql_query($updtT005)) {
                mysql_query("ROLLBACK"); return 'Erro ao limpar os dados da conferência do pedido. <br><br>' . mysql_error();
            }
        }

        // Ao voltar para impressão, também limpa a separação já gravada do pedido.
        if ($T005_Flag_Status == '1') {
            $delT238 = "DELETE FROM T238 WHERE T238_T005_Id = '{$T005_Id}'";
            if (!mysql_query($delT238)) {
                mysql_query("ROLLBACK"); return 'Erro ao remover as locações separadas do pedido. <br><br>' . mysql_error();
            }

            $updtT006 = "UPDATE T006 SET T006_Quantidade_Separacao = 0, T006_Flag_Divergencia = 'N' WHERE T006_T005_Id = '{$T005_Id}'";
            if (!mysql_query($updtT006)) {
                mysql_query("ROLLBACK"); return 'Erro ao limpar as quantidades separadas do pedido. <br><br>' . mysql_error();
            }

            $updtT005 = "UPDATE T005 SET T005_Estoque_Box = '', T005_C007_Id_Separou = NULL WHERE T005_Id = '{$T005_Id}'";
            if (!mysql_query($updtT005)) {
                mysql_query("ROLLBACK"); return 'Erro ao limpar os dados de separação do pedido. <br><br>' . mysql_error();
            }
        }

        // Atualiza o status principal do pedido para a etapa escolhida pelo usuário.
        $updtT005 = "UPDATE T005 SET 
                        T005_Flag_Travar_Pedido = 2,
                        T005_Flag_Status = '{$T005_Flag_Status}',
                        T005_Nome_Status = T005_Status_Pedido(T005_Flag_Status, 1),
                        T005_Flag_Reenviado_Almoxarifado = 'S',
                        T005_Flag_Reenviado_Almoxarifado_Lido = 'N',
                        T005_Flag_Separacao = 'N',
                        T005_Flag_Separacao_Liberar = 'N',
                        T005_Flag_Almoxarifado = 'N',
                        T005_Flag_Notificacao_Financeiro = ''
                    WHERE T005_Id = '{$T005_Id}'";
        if (!mysql_query($updtT005)) {
            mysql_query("ROLLBACK"); return 'Erro ao atualizar a etapa do pedido.<br><br>' . mysql_error();
        }

        // Registra no histórico a etapa para a qual o pedido retornou.
        $descricaoHistorico = mysql_real_escape_string('Retornou pedido para ' . $etapas[$T005_Flag_Status]);

        $insT178 = "INSERT INTO T178 (T178_T005_Id, T178_C007_Id, T178_Data, T178_Descricao, T178_Observacao) VALUES ({$T005_Id}, {$g['usuarioAtual']}, CURRENT_TIMESTAMP(), '{$descricaoHistorico}', '')";
        if (!mysql_query($insT178)) {
            mysql_query("ROLLBACK"); return 'Erro ao gravar o histórico do pedido.<br><br>' . mysql_error();
        }

        // Confirma a reversão somente depois que todas as limpezas e atualizações forem concluídas.
        mysql_query("COMMIT");
        return true;
    }
}
