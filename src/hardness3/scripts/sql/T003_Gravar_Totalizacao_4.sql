CREATE PROCEDURE `T003_Gravar_Totalizacao_4`(xT003_Id int(11))
BEGIN
    DECLARE xValor_Total_Custo DECIMAL(10,2);
    DECLARE xValor_Total_Custo_Confirmado DECIMAL(10,2);
    DECLARE xValor_Total_Preco DECIMAL(10,2);
    DECLARE xValor_Total_Preco_Confirmado DECIMAL(10,2);
    DECLARE xValor_Total_Preco_Sem_Desconto DECIMAL(10,2);
    DECLARE xValor_Total_Preco_Sem_Desconto_Confirmado DECIMAL(10,2);
    DECLARE xValor_Total_Custo_Final DECIMAL(10,2);
    DECLARE xValor_Total_Custo_Final_Confirmado DECIMAL(10,2);
    DECLARE xValor_Total_Produtos DECIMAL(10,2);
    DECLARE xValor_Total_Produtos_Confirmado DECIMAL(10,2);
    DECLARE xValor_Total_Agregados DECIMAL(10,2);
    DECLARE xValor_Total_Servicos DECIMAL(10,2);
    DECLARE xValor_Total_Trocas DECIMAL(10,2);
    DECLARE xValor_Total_Preco_Bruto DECIMAL(10,2);
    DECLARE xValor_ICMS DECIMAL(10,2);
    DECLARE xValor_PIS DECIMAL(10,2);
    DECLARE xValor_COFINS DECIMAL(10,2);
    DECLARE xValor_ICMS_Desonerado DECIMAL(10,2);
    DECLARE xValor_ICMS_Confirmado DECIMAL(10,2);
    DECLARE xValor_Base_ICMS DECIMAL(10,2);
    DECLARE xValor_Base_ICMS_Confirmado DECIMAL(10,2);
    DECLARE xValor_IPI DECIMAL(10,2);
    DECLARE xValor_IPI_Confirmado DECIMAL(10,2);
    DECLARE xValor_Base_IPI DECIMAL(10,2);
    DECLARE xValor_Base_IPI_Confirmado DECIMAL(10,2);
    DECLARE xIPV DECIMAL(10,4);
    DECLARE xIPV_Confirmado DECIMAL(10,4);
    DECLARE xPeso_Total DECIMAL(10,3);
    DECLARE xPeso_Bruto_Total DECIMAL(10,3);
    DECLARE xPeso_Total_Confirmado DECIMAL(10,3);
    DECLARE xValor_Total_Desconto DECIMAL(10,2);
    DECLARE xValor_Total_Desconto_Confirmado DECIMAL(10,2);
    DECLARE xValor_Base_ST DECIMAL(10,2);
    DECLARE xValor_ST DECIMAL(10,2);
    DECLARE xValor_Base_ST_Confirmado DECIMAL(10,2);
    DECLARE xValor_ST_Confirmado DECIMAL(10,2);
    DECLARE xValor_Total_Cubagem DECIMAL(10,3) default 0;
    DECLARE xValor_Trib_Transparencia DECIMAL(15,2) DEFAULT 0;
    DECLARE xValorPendente DECIMAL(14,5) DEFAULT 0;
    DECLARE xC004_Id int(10);
    DECLARE xC031_Somar_ICMS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_PIS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_COFINS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_IRPJ_Preco_Venda varchar(1);
    DECLARE xC031_Somar_CSLL_Preco_Venda varchar(1);
    DECLARE xC031_Desabilitar_Cotacao_Emergencia varchar(1);
    DECLARE xC031_Exibir_Msg_Cotar_Orcamento varchar(1);
    DECLARE xC031_Ratear_Frete_Venda_No_Custo varchar(1);
    DECLARE xFlagItensPendenteOrcamento int(10);
    DECLARE xT003_Flag_Frete int(10);
    DECLARE xValor_IRPJ DECIMAL(10,2);
    DECLARE xValor_Base_IRPJ DECIMAL(10,2);
    DECLARE xValor_CSLL DECIMAL(10,2);
    DECLARE xValor_Base_CSLL DECIMAL(10,2);
    DECLARE xValor_ICMS_Credito DECIMAL(10,2);
    DECLARE xAliquota_ICMS_Credito DECIMAL(10,2);
    DECLARE xValor_Total_FCP_Partilha DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_Total_FCP DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_FCP_ST DECIMAL(15,2) DEFAULT 0;

    DECLARE done int DEFAULT FALSE;
    DECLARE xT003_Flag_Status_Cotacao char DEFAULT '';
    DECLARE xT004_Flag_Comprar varchar(1) DEFAULT 'N';
    DECLARE xT004_T219_Id int(10);
    DECLARE xT219_Id int(10);
    DECLARE xD009_Quantidade_Estoque DECIMAL(10,2) DEFAULT 0;
    DECLARE xD009_Quantidade_OC DECIMAL(15,5) DEFAULT 0;
    DECLARE xT004_Quantidade DECIMAL(12,5) DEFAULT 0;
    DECLARE xD009_Valor_Custo_Tabela DECIMAL(14,6) DEFAULT 0;
    DECLARE xD070_Data_Vencimento DATE DEFAULT '0000-00-00';
    DECLARE xT004_Flag_Transferencia enum('S','N') DEFAULT 'N';
    DECLARE cotando char(1) DEFAULT 'N';
    DECLARE retornouPreco char(1) DEFAULT 'N';
    DECLARE aguardandoCotacao char(1) DEFAULT 'N';
    DECLARE precoTabelaValido char(1) DEFAULT 'N';
    DECLARE xValor_Total_Partilha_ICMS_Destino DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_Total_Partilha_ICMS_Origem DECIMAL(15,2) DEFAULT 0;
    #DECLARE xValor_Base_ICMS_Diferido DECIMAL(15,2) DEFAULT 0;
    #DECLARE xValor_ICMS_Diferido DECIMAL(15,2) DEFAULT 0;
    #DECLARE xValor_Base_ICMS_Sem_Reducao DECIMAL(15,2) DEFAULT 0;
    DECLARE xT003A_Flag_Ratear_Frete_Custo VARCHAR(1) DEFAULT '';
    DECLARE xD006_Deduz_ICMS_Desonerado_Valor_Total VARCHAR (1) DEFAULT 'S';

    DECLARE xCursor CURSOR FOR SELECT T004_Flag_Comprar, IFNULL(T004_T219_Id,0) AS T004_T219_Id, IFNULL(T219_Id,0) AS T219_Id,
                                      D009_Quantidade_Estoque, D009_Quantidade_OC, T004_Quantidade, D009_Valor_Custo_Tabela, D070_Data_Vencimento, T004_Flag_Transferencia
                                FROM T004
                                LEFT JOIN D009 ON D009_Id = T004_D009_Id
                                LEFT JOIN D049 ON D049_Id = D009_D049_Id
                                LEFT JOIN T219 ON T004_Id = T219_T004_Id
                                LEFT JOIN D069 on D069_Id = D009_D069_Id AND D069_Flag_Tabela_Principal='S'
                                LEFT JOIN D070 on D070_Id = D069_D070_Id
                                WHERE T004_T003_Id = xT003_Id AND IF(T004_T219_Id > 0, T004_T219_Id = T219_Id, 1=1)
                                AND IFNULL(D049_Flag_Tipo,'') = 'CP'
                                GROUP BY T004_Id;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    SELECT T003_C004_Id,
           IFNULL(T003_Flag_Frete,0) AS T003_Flag_Frete,
           T003A_Flag_Ratear_Frete_Custo
      INTO xC004_Id,
           xT003_Flag_Frete,
           xT003A_Flag_Ratear_Frete_Custo
      FROM T003
 LEFT JOIN T003A ON T003_Id = T003A_T003_Id
     WHERE T003_Id=xT003_Id;

    SELECT C031_Valor INTO xC031_Exibir_Msg_Cotar_Orcamento FROM C031 WHERE C031_Campo = 'exibirMsgCotarOrcamento' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Ratear_Frete_Venda_No_Custo FROM C031 WHERE C031_Campo = 'ratearFreteVendaNoCusto' AND C031_C004_Id = xC004_Id;

    IF xT003A_Flag_Ratear_Frete_Custo != '' THEN
        SET xC031_Ratear_Frete_Venda_No_Custo = xT003A_Flag_Ratear_Frete_Custo;
    END IF;

    OPEN xCursor;

    the_loop: LOOP
        FETCH xCursor INTO xT004_Flag_Comprar, xT004_T219_Id, xT219_Id, xD009_Quantidade_Estoque, xD009_Quantidade_OC, xT004_Quantidade, xD009_Valor_Custo_Tabela, xD070_Data_Vencimento, xT004_Flag_Transferencia;
        IF done THEN
          LEAVE the_loop;
        END IF;

        IF (xD009_Quantidade_Estoque+xD009_Quantidade_OC) < xT004_Quantidade AND xT219_Id <= 0 AND xC031_Exibir_Msg_Cotar_Orcamento = 'S' AND xT004_Flag_Comprar != 'S' AND (xD009_Valor_Custo_Tabela <= 0 OR xD070_Data_Vencimento < CURRENT_DATE()) AND xT004_Flag_Transferencia != 'S' THEN
          SET xT003_Flag_Status_Cotacao = 'Z';
        ELSEIF xT219_Id <= 0 AND xT004_Flag_Comprar = 'S' THEN
          IF xD009_Valor_Custo_Tabela > 0 AND xD070_Data_Vencimento > CURRENT_DATE() THEN
            SET precoTabelaValido = 'S';
          ELSE
            SET aguardandoCotacao = 'S';
          END IF;
        ELSEIF xT004_T219_Id > 0 THEN
          SET retornouPreco = 'S';
        ELSEIF xT219_Id > 0 AND xT004_T219_Id <= 0 THEN
          SET cotando = 'S';
        END IF;
    END LOOP the_loop;
    CLOSE xCursor;

    IF xT003_Flag_Status_Cotacao = '' THEN
      IF aguardandoCotacao = 'S' THEN
        SET xT003_Flag_Status_Cotacao = 'A';
      ELSEIF precoTabelaValido = 'S' THEN
        SET xT003_Flag_Status_Cotacao = 'T';
      ELSEIF cotando = 'S' THEN
        SET xT003_Flag_Status_Cotacao = 'C';
      ELSEIF retornouPreco = 'S' AND cotando = 'N' THEN
        SET xT003_Flag_Status_Cotacao = 'P';
      END IF;
    END IF;

    SELECT sum(T004_Valor_Total_Custo),
           sum(T004_Valor_Total_Custo_Confirmado),
           sum(T004_Valor_Total_Preco),
           sum(T004_Valor_Total_Preco_Confirmado),
           sum(T004_Valor_Total_Preco_Sem_Desconto),
           sum(T004_Valor_Total_Preco_Sem_Desconto_Confirmado),
           sum(T004_Valor_Total_Custo_Final),
           sum(T004_Valor_Total_Custo_Final_Confirmado),
           sum(T004_Valor_ICMS),
           sum(T004_Valor_ICMS_Confirmado),
           sum(T004_Valor_ICMS_Desonerado),
           sum(T004_Valor_Total_Agregados),
           sum(T004_Valor_Base_ICMS),
           sum(T004_Valor_Base_ICMS_Confirmado),
           sum(T004_Valor_IPI),
           sum(T004_Valor_IPI_Confirmado),
           sum(T004_Valor_Base_IPI),
           sum(T004_Valor_Base_IPI_Confirmado),
           sum(T004_Peso_Total),
           sum(T004_Peso_Bruto_Total),
           sum(T004_Peso_Total_Confirmado),
           sum(T004_Valor_Total_Desconto),
           sum(T004_Valor_Total_Desconto_Confirmado),
           sum(T004_Valor_Base_ST),
           sum(T004_Valor_ST),
           sum(T004_Valor_Base_ST_Confirmado),
           sum(T004_Valor_ST_Confirmado),
           sum(T004_Valor_Preco_Bruto),
           sum(T004_Total_Cubagem),
           sum(T004_Valor_Trib_Transparencia),
           sum(T004_Valor_PIS),
           sum(T004_Valor_COFINS),
           sum(T004_Valor_IRPJ),
           sum(T004_Valor_Base_IRPJ),
           sum(T004_Valor_CSLL),
           sum(T004_Valor_Base_CSLL),
           sum(T004_Valor_ICMS_Credito),
           sum(T004_Valor_Partilha_ICMS_Destino),
           sum(T004_Valor_Partilha_ICMS_Origem),
           #sum(T004_Valor_Base_ICMS_Diferido),
           #sum(T004_Valor_ICMS_Diferido),
           #sum(T004_Valor_Base_ICMS_Sem_Reducao),
           T004_Aliquota_ICMS_Credito,
           D006_Deduz_ICMS_Desonerado_Valor_Total,
           sum(T004A_Valor_FCP_Partilha),
           sum(T004A_Valor_FCP),
           sum(T004A_Valor_FCP_ST)
      INTO xValor_Total_Custo,
           xValor_Total_Custo_Confirmado,
           xValor_Total_Preco,
           xValor_Total_Preco_Confirmado,
           xValor_Total_Preco_Sem_Desconto,
           xValor_Total_Preco_Sem_Desconto_Confirmado,
           xValor_Total_Custo_Final,
           xValor_Total_Custo_Final_Confirmado,
           xValor_ICMS,
           xValor_ICMS_Confirmado,
           xValor_ICMS_Desonerado,
           xValor_Total_Agregados,
           xValor_Base_ICMS,
           xValor_Base_ICMS_Confirmado,
           xValor_IPI,
           xValor_IPI_Confirmado,
           xValor_Base_IPI,
           xValor_Base_IPI_Confirmado,
           xPeso_Total,
           xPeso_Bruto_Total,
           xPeso_Total_Confirmado,
           xValor_Total_Desconto,
           xValor_Total_Desconto_Confirmado,
           xValor_Base_ST,
           xValor_ST,
           xValor_Base_ST_Confirmado,
           xValor_ST_Confirmado,
           xValor_Total_Preco_Bruto,
           xValor_Total_Cubagem,
           xValor_Trib_Transparencia,
           xValor_PIS,
           xValor_COFINS,
           xValor_IRPJ,
           xValor_Base_IRPJ,
           xValor_CSLL,
           xValor_Base_CSLL,
           xValor_ICMS_Credito,
           xValor_Total_Partilha_ICMS_Destino,
           xValor_Total_Partilha_ICMS_Origem,
           #xValor_Base_ICMS_Diferido,
           #xValor_ICMS_Diferido,
           #xValor_Base_ICMS_Sem_Reducao,
           xAliquota_ICMS_Credito,
           xD006_Deduz_ICMS_Desonerado_Valor_Total,
           xValor_Total_FCP_Partilha,
           xValor_Total_FCP,
           xValor_FCP_ST
           FROM T004
           LEFT JOIN T003 ON T003_Id=T004_T003_Id   
           LEFT JOIN D006 ON D006_Id=T004_D006_Id
           LEFT JOIN T004A ON T004_Id=T004A_T004_Id
           WHERE T004_T003_Id=xT003_Id
             AND (T004_Flag_Cancelado!='S' OR T003_Flag_Perdido='S');
    
    SELECT SUM(T145_Valor_Unitario)
      INTO xValor_Total_Servicos
      FROM T145
     WHERE T145_T003_Id=xT003_Id;

    
    SELECT SUM(T138_Valor_Preco_Unitario*T138_Quantidade)
      INTO xValor_Total_Trocas
      FROM T138
     WHERE T138_T003_Id=xT003_Id;
    
    SELECT SUM(if(T004_Valor_Total_Preco>ifnull(T006_Valor_Total_Preco,0),T004_Valor_Total_Preco-ifnull(T006_Valor_Total_Preco,0),0))
      INTO xValorPendente
      FROM T004
      LEFT JOIN T006 ON T006_T004_Id=T004_Id
      WHERE T004_T003_Id=xT003_Id;

    SET xFlagItensPendenteOrcamento = 0;

    SELECT C031_Valor INTO xC031_Somar_ICMS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarICMSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_PIS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarPISPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_COFINS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCOFINSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_IRPJ_Preco_Venda FROM C031 WHERE C031_Campo = 'somarIRPJPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_CSLL_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCSLLPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Desabilitar_Cotacao_Emergencia FROM C031 WHERE C031_Campo = 'desabilitarCotacaoEmergencia' AND C031_C004_Id = xC004_Id;

    SELECT IFNULL(COUNT(T004_Id),0)
      INTO xFlagItensPendenteOrcamento
      FROM T004
 LEFT JOIN D009 ON D009_Id=T004_D009_Id
 LEFT JOIN D049 ON D049_Id=D009_D049_Id
 LEFT JOIN D069 ON D069_Id=D009_D069_Id
 LEFT JOIN D070 ON D070_Id=D069_D070_Id
     WHERE (D009_Quantidade_Estoque+D009_Quantidade_OC) < T004_Quantidade 
       AND IFNULL(T004_T219_Id,0) <= 0 
       AND IFNULL(D049_Flag_Tipo,'') = 'CP' 
       AND T004_Flag_Transferencia != 'S'
       AND T004_T003_Id=xT003_Id
       AND xC031_Exibir_Msg_Cotar_Orcamento = 'S'
       AND (D009_Valor_Custo_Tabela <= 0 OR D070_Data_Vencimento < CURRENT_DATE());

    SET xFlagItensPendenteOrcamento = 0;

    IF xC031_Desabilitar_Cotacao_Emergencia = 'S' THEN
      SET xT003_Flag_Status_Cotacao = '';
    END IF;

    UPDATE T003A SET T003A_Valor_Total_FCP=xValor_Total_FCP,
                     T003A_Valor_FCP_ST=xValor_FCP_ST,
                     T003A_Valor_Total_FCP_Partilha=xValor_Total_FCP_Partilha
               WHERE T003A_T003_Id = xT003_Id;

    UPDATE T003 
                SET T003_Flag_Status_Cotacao=xT003_Flag_Status_Cotacao,
                    T003_Valor_Total_Servicos=ifnull(xValor_Total_Servicos,0),
                    T003_Valor_Troca=ifnull(xValor_Total_Trocas,0),
                    T003_Valor_Total_Produtos=xValor_Total_Preco,
                    T003_Valor_Total_Produtos_Confirmado=xValor_Total_Preco_Confirmado,
                    T003_Valor_Total_Produtos_Sem_Desconto=xValor_Total_Preco_Sem_Desconto,
                    T003_Valor_Total_Produtos_Sem_Desconto_Confirmado=xValor_Total_Preco_Sem_Desconto_Confirmado,
                    T003_Valor_Total_Custo_Final=xValor_Total_Custo_Final,
                    T003_Valor_Total_Custo_Final_Confirmado=xValor_Total_Custo_Final_Confirmado,
                    T003_Valor_Total_Custo=xValor_Total_Custo,
                    T003_Valor_Total_Custo_Confirmado=xValor_Total_Custo_Confirmado,
                    T003_Valor_Total_Agregados=ifnull(xValor_Total_Agregados,0),
                    #T003_Valor_Total_Base_ICMS_Sem_Reducao=xValor_Base_ICMS_Sem_Reducao,
                    #T003_Valor_Total_Base_ICMS_Diferido=xValor_Base_ICMS_Diferido,
                    #T003_Valor_Total_ICMS_Diferido=xValor_ICMS_Diferido,
                    T003_Valor_Total_Base_ICMS=xValor_Base_ICMS,
                    T003_Valor_Total_Base_ICMS_Confirmado=xValor_Base_ICMS_Confirmado,
                    T003_Valor_Total_ICMS=xValor_ICMS,
                    T003_Valor_Total_ICMS_Confirmado=xValor_ICMS_Confirmado,
                    T003_Valor_Total_ICMS_Desonerado=xValor_ICMS_Desonerado,
                    T003_Valor_Total_Base_IPI=xValor_Base_IPI,
                    T003_Valor_Total_Base_IPI_Confirmado=xValor_Base_IPI_Confirmado,
                    T003_Valor_Total_IPI=xValor_IPI,
                    T003_Valor_Total_IPI_Confirmado=xValor_IPI_Confirmado,
                    T003_Valor_ISS=if(T003_Aliquota_ISS>0,T003_Valor_Mao_Obra*(T003_Aliquota_ISS/100),0),
                    T003_Valor_Base_ST=xValor_Base_ST,
                    T003_Valor_ST=xValor_ST,
                    T003_Valor_Total_ICMS_Credito=xValor_ICMS_Credito,
                    T003_Aliquota_ICMS_Credito = xAliquota_ICMS_Credito,
                    T003_Valor_Base_ST_Confirmado=xValor_Base_ST_Confirmado,
                    T003_Valor_Desconto=IF(xC031_Desabilitar_Cotacao_Emergencia='S' OR xFlagItensPendenteOrcamento = 0,(T003_Valor_Total_Produtos_Sem_Desconto-T003_Valor_Total_Produtos),0),
                    T003_Valor_Desconto_Confirmado=IF(xC031_Desabilitar_Cotacao_Emergencia='S' OR xFlagItensPendenteOrcamento = 0,(T003_Valor_Total_Produtos_Sem_Desconto_Confirmado-T003_Valor_Total_Produtos_Confirmado),0),                   
                    T003_Valor_ST_Confirmado=xValor_ST_Confirmado,
                    T003_Valor_Total_Base_IRPJ=xValor_Base_IRPJ,
                    T003_Valor_Total_IRPJ=xValor_IRPJ,
                    T003_Valor_Total_Base_CSLL=xValor_Base_CSLL,
                    T003_Valor_Total_CSLL=xValor_CSLL,
                    T003_Valor_Total_PIS=xValor_PIS,
                    T003_Valor_Total_COFINS=xValor_COFINS,
                    T003_Valor_Total_Partilha_ICMS_Destino=xValor_Total_Partilha_ICMS_Destino,
                    T003_Valor_Total_Partilha_ICMS_Origem=xValor_Total_Partilha_ICMS_Origem,
                    T003_Valor_Total=ifnull(T003_Valor_Total_Produtos,0)+
                                     ifnull(T003_Valor_Mao_Obra,0)+
                                     ifnull(if(xValor_Total_Preco_Bruto>0,0,T003_Valor_Total_Agregados),0)+
                                     ifnull(T003_Valor_Despesas_Acessorias,0)+
                                     ifnull(T003_Valor_Total_IPI,0)+
                                     ifnull(T003_Valor_ST,0)+
                                     ifnull(xValor_FCP_ST,0)+
                                     ifnull(T003_Valor_Total_Servicos,0)-
                                     ifnull(T003_Valor_Desconto_Manual,0)-
                                     ifnull(T003_Valor_Troca,0)+
                                     if(xC031_Ratear_Frete_Venda_No_Custo='S' AND xT003_Flag_Frete = 0, 0, ifnull(T003_Valor_Frete,0))-
                                     ifnull(if(xD006_Deduz_ICMS_Desonerado_Valor_Total = 'S',T003_Valor_Total_ICMS_Desonerado,0),0),
                    T003_Valor_Total_Confirmado=if(T003_Flag_Confirmar_Mao_Obra='S' or ifnull(xValor_Total_Preco_Confirmado,0)>0,
                                     ifnull(T003_Valor_Total_Produtos_Confirmado,0)+
                                     ifnull(T003_Valor_Mao_Obra,0)+
                                     ifnull(T003_Valor_Despesas_Acessorias,0)+
                                     ifnull(T003_Valor_Total_IPI_Confirmado,0)+
                                     ifnull(T003_Valor_ST_Confirmado,0)+
                                     ifnull(T003_Valor_Total_Servicos,0)-
                                     ifnull(T003_Valor_Desconto_Manual_Confirmado,0)-
                                     ifnull(T003_Valor_Troca,0)+
                                     if(xC031_Ratear_Frete_Venda_No_Custo='S' AND xT003_Flag_Frete = 0, 0, ifnull(T003_Valor_Frete,0))-
                                     ifnull(if(xD006_Deduz_ICMS_Desonerado_Valor_Total = 'S',T003_Valor_Total_ICMS_Desonerado,0),0),0),
                    T003_IPV = VEN_Calculo_IPV(xT003_Id, 0, 0, 0, 0, 0, xC031_Somar_ICMS_Preco_Venda, xC031_Somar_PIS_Preco_Venda, xC031_Somar_COFINS_Preco_Venda, xC031_Somar_IRPJ_Preco_Venda, xC031_Somar_CSLL_Preco_Venda, T003_Valor_Total_Produtos, T003_Valor_Total_Custo, 1, T003_Valor_Total_ICMS, xValor_PIS, xValor_COFINS, xValor_IRPJ, xValor_CSLL), 
                    #T003_IPV=if(T003_Valor_Total_Custo>0,(T003_Valor_Total_Produtos-
                    #                 IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T003_Valor_Total_ICMS,0), 0)-
                    #                 IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(xValor_PIS,0), 0)-
                    #                IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(xValor_COFINS,0), 0)-
                    #                 IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(xValor_IRPJ,0), 0)-
                    #                 IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(xValor_CSLL,0), 0))/T003_Valor_Total_Custo,0),
                    
                    T003_Percentual_Margem = VEN_Calculo_Margem(xT003_Id, 0, 0, 0, 0, 0, xC031_Somar_ICMS_Preco_Venda, xC031_Somar_PIS_Preco_Venda, xC031_Somar_COFINS_Preco_Venda, xC031_Somar_IRPJ_Preco_Venda, xC031_Somar_CSLL_Preco_Venda, T003_Valor_Total_Produtos, T003_Valor_Total_Custo, 1, T003_Valor_Total_ICMS, xValor_PIS, xValor_COFINS, xValor_IRPJ, xValor_CSLL), 
                    #T003_Percentual_Margem=round((((T003_Valor_Total_Produtos-
                    #                        IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T003_Valor_Total_ICMS,0), 0)-
                    #                        IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(xValor_PIS,0), 0)-
                    #                        IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(xValor_COFINS,0), 0)-
                    #                        IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(xValor_IRPJ,0), 0)-
                    #                        IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(xValor_CSLL,0), 0))-T003_Valor_Total_Custo)/
                    #                      (T003_Valor_Total_Produtos-
                    #                        IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T003_Valor_Total_ICMS,0), 0)-
                    #                        IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(xValor_PIS,0), 0)-
                    #                        IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(xValor_COFINS,0), 0)-
                    #                        IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(xValor_IRPJ,0), 0)-
                    #                        IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(xValor_CSLL,0), 0)))*100,2),
                    T003_IPV_Confirmado=if(T003_Valor_Total_Custo_Confirmado>0,T003_Valor_Total_Produtos_Confirmado/T003_Valor_Total_Custo_Confirmado,0),
                    T003_IPV_Normal=ifnull(T003_IPV_Medio(xT003_Id,3),0),
                    T003_IPV_Normal_Confirmado=T003_IPV_Medio(xT003_Id,4),
                    T003_Peso_Total=ifnull(xPeso_Total,0),
                    T003_Peso_Bruto_Total=ifnull(xPeso_Bruto_Total,0),
                    T003_Peso_Total_Confirmado=ifnull(xPeso_Total_Confirmado,0),
                    T003_Total_Cubagem=IFNULL(xValor_Total_Cubagem,0),
                    T003_Valor_Total_Trib_Transparencia=xValor_Trib_Transparencia,
                    T003_Valor_Pendente=xValorPendente
              WHERE T003_Id=xT003_Id;
END;

