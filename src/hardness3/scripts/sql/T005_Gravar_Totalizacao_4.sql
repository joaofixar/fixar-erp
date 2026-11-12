CREATE PROCEDURE `T005_Gravar_Totalizacao_4`(xT005_Id int(11))
BEGIN
    DECLARE xValor_Total_Custo DECIMAL(10,2);
    DECLARE xValor_Total_Preco DECIMAL(10,2);
    DECLARE xValor_Total_Preco_Sem_Desconto DECIMAL(10,2);
    DECLARE xValor_Total_Custo_Final DECIMAL(10,2);
    DECLARE xValor_Total_Desconto DECIMAL(10,2);
    DECLARE xValor_Total_Servicos DECIMAL(10,2);
    DECLARE xValor_Total_Trocas DECIMAL(10,2);
    DECLARE xValor_ICMS DECIMAL(10,2);
    DECLARE xValor_ICMS_Desonerado DECIMAL(10,2);
    DECLARE xValor_Base_ICMS DECIMAL(10,2);
    DECLARE xValor_ICMS_Oculto DECIMAL(10,2);
    DECLARE xValor_Base_ICMS_Oculto DECIMAL(10,2);
    DECLARE xValor_Total_Agregados DECIMAL(10,2);
    DECLARE xValor_IPI DECIMAL(10,2);
    DECLARE xValor_Base_IPI DECIMAL(10,2);
    DECLARE xValor_Total_Preco_Bruto DECIMAL(10,2);
    DECLARE xIPV DECIMAL(10,4);
    DECLARE xPeso_Total DECIMAL(10,3);
    DECLARE xPeso_Bruto_Total DECIMAL(10,3);
    DECLARE xValor_Base_ICMS_Substituicao DECIMAL(10,2) DEFAULT 0;
    DECLARE xValor_ICMS_Substituicao DECIMAL(10,2) DEFAULT 0;
    DECLARE xValor_ICMS_Substituicao_Retencao DECIMAL(10,2) DEFAULT 0;
    DECLARE xValor_Total_PIS decimal(10,2) DEFAULT 0;
    DECLARE xValor_Total_COFINS decimal(10,2) default 0;
    DECLARE xValor_Total_Cubagem DECIMAL(10,3) default 0;
    DECLARE xValor_Trib_Transparencia DECIMAL(15,2) DEFAULT 0;
    DECLARE xPercentual_Estoque_Disponivel DECIMAL(15,2) DEFAULT 0;
    DECLARE xPercentual_Ordem_Compra DECIMAL(15,2) DEFAULT 0;
    DECLARE xPercentual_Estoque_Disponivel_E_Ordem_Compra DECIMAL(15,2) DEFAULT 0;
    DECLARE xC004_Id int(10);
    DECLARE xT005_Flag_Frete int(10);
    DECLARE xT005_Flag_Status int(10);
    DECLARE xC031_Somar_ICMS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_PIS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_COFINS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_IRPJ_Preco_Venda varchar(1);
    DECLARE xC031_Somar_CSLL_Preco_Venda varchar(1);
    DECLARE xC031_Desabilitar_Compra_Emergencia varchar(1);
    DECLARE xC031_Ratear_Frete_Venda_No_Custo varchar(1);
    DECLARE xFlagItensPendentePedido int(10);
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
    DECLARE xT005_Flag_Status_Cotacao char DEFAULT '';
    DECLARE xT006_T225_Id int(10);
    DECLARE xT006_T219_Id int(10);
    DECLARE xT219_Id int(10);
    DECLARE xT225_Id int(10);
    DECLARE xT006_Quantidade_Estoque DECIMAL(12,2) DEFAULT 0;
    DECLARE xT006_Quantidade DECIMAL(12,5) DEFAULT 0;
    DECLARE xT006_Quantidade_OC DECIMAL(12,5) DEFAULT 0;
    DECLARE xT006_Flag_Transferencia enum('S','N') DEFAULT 'N';
    DECLARE xD009_Valor_Custo_Tabela DECIMAL(14,6) DEFAULT 0;
    DECLARE xD070_Data_Vencimento DATE DEFAULT '0000-00-00';
    DECLARE retornouPreco char(1) DEFAULT 'N';
    DECLARE precoTabelaValido char(1) DEFAULT 'N';
    DECLARE xValor_Total_Partilha_ICMS_Destino DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_Total_Partilha_ICMS_Origem DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_Base_ICMS_Diferido DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_ICMS_Diferido DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_Base_ICMS_Sem_Reducao DECIMAL(15,2) DEFAULT 0;
    DECLARE xT005A_Flag_Ratear_Frete_Custo VARCHAR(1) DEFAULT '';
    DECLARE xD006_Deduz_ICMS_Desonerado_Valor_Total VARCHAR (1) DEFAULT 'S';
    DECLARE xC031_Preco_Padrao_Venda_Produto VARCHAR (10) DEFAULT 'liquido';


    DECLARE xCursor CURSOR FOR SELECT IFNULL(T006_T225_Id,0) AS T006_T225_Id, IFNULL(T006_T219_Id,0) AS T006_T219_Id, IFNULL(T219_Id,0) AS T219_Id, IFNULL(T225_Id,0) AS T225_Id,
           T006_Quantidade_Estoque, T006_Quantidade, T006_Quantidade_OC, T006_Flag_Transferencia, D009_Valor_Custo_Tabela, D070_Data_Vencimento
                              FROM T006
                              LEFT JOIN T004 ON T004_Id = T006_T004_Id
                              LEFT JOIN D009 ON D009_Id = T006_D009_Id
                              LEFT JOIN D069 on D069_Id = D009_D069_Id AND D069_Flag_Tabela_Principal='S'
                              LEFT JOIN D070 on D070_Id = D069_D070_Id
                              LEFT JOIN D049 ON D049_Id = D009_D049_Id
                              LEFT JOIN T219 ON T006_Id = T219_T006_Id
                              LEFT JOIN T225 ON T225_Id = T006_T225_Id
                              WHERE T006_T005_Id = xT005_Id
                              AND IFNULL(D049_Flag_Tipo,'') = 'CP' GROUP BY T006_Id;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    OPEN xCursor;

    the_loop: LOOP
        FETCH xCursor INTO xT006_T225_Id, xT006_T219_Id, xT219_Id, xT225_Id, xT006_Quantidade_Estoque, xT006_Quantidade, xT006_Quantidade_OC, xT006_Flag_Transferencia, xD009_Valor_Custo_Tabela, xD070_Data_Vencimento;
        IF done THEN
          LEAVE the_loop;
        END IF;

        IF (xT006_Quantidade_Estoque+xT006_Quantidade_OC) < xT006_Quantidade AND xT219_Id <= 0 AND xT225_Id <= 0 AND xT006_Flag_Transferencia != 'S' THEN
          IF xD009_Valor_Custo_Tabela > 0 AND xD070_Data_Vencimento >= CURRENT_DATE() THEN
            SET precoTabelaValido = 'S';
          ELSE
            SET xT005_Flag_Status_Cotacao = 'A';
          END IF;
        ELSEIF (xT219_Id > 0 OR xT225_Id > 0) AND xT006_T219_Id <= 0 AND xT006_T225_Id <= 0 THEN
          SET xT005_Flag_Status_Cotacao = 'C';
        ELSEIF xT006_T219_Id > 0 OR xT006_T225_Id > 0 THEN
          SET retornouPreco = 'S';
        END IF;


    END LOOP the_loop;
    CLOSE xCursor;

    IF xT005_Flag_Status_Cotacao = '' THEN
      IF precoTabelaValido = 'S' THEN
        SET xT005_Flag_Status_Cotacao = 'T';
      ELSEIF retornouPreco = 'S' THEN
        SET xT005_Flag_Status_Cotacao = 'P';
      END IF;
    END IF;

    SELECT sum(T006_Valor_Total_Custo),
           sum(T006_Valor_Total_Preco),
           sum(T006_Valor_Total_Preco_Sem_Desconto),
           sum(T006_Valor_Total_Custo_Final),
           sum(T006_Valor_Total_Desconto),
           sum(T006_Valor_ICMS),
           sum(T006_Valor_ICMS_Desonerado),
           sum(T006_Valor_Base_ICMS),
           sum(T006_Valor_ICMS_Oculto),
           sum(T006_Valor_Base_ICMS_Oculto),
           sum(T006_Valor_Total_Agregados),
           sum(T006_Valor_IPI),
           sum(T006_Valor_Base_IPI),
           sum(T006_Peso_Total),
           sum(T006_Peso_Bruto_Total),
           sum(T006_Valor_Base_ICMS_Substituicao),
           sum(T006_Valor_ICMS_Substituicao),
           sum(T006_Valor_ICMS_Substituicao_Retencao),
           sum(T006_Valor_PIS),
           sum(T006_Valor_COFINS),
           sum(T006_Valor_Preco_Bruto),
           sum(T006_Total_Cubagem),
           sum(T006_Valor_Trib_Transparencia),
           SUM(IF((IF(T006_Quantidade_Estoque < 0, 0, T006_Quantidade_Estoque)/T006_Quantidade)*100 > 100, 100, (IF(T006_Quantidade_Estoque < 0, 0, T006_Quantidade_Estoque)/T006_Quantidade)*100))/COUNT(T006_Id),
           SUM(IF((IF(T006_Quantidade_OC < 0, 0, T006_Quantidade_OC)/T006_Quantidade)*100 > 100, 100, (IF(T006_Quantidade_OC < 0, 0, T006_Quantidade_OC)/T006_Quantidade)*100))/COUNT(T006_Id),
           SUM(T006_Valor_IRPJ),
           SUM(T006_Valor_Base_IRPJ),
           SUM(T006_Valor_CSLL),
           SUM(T006_Valor_Base_CSLL),
           sum(T006_Valor_ICMS_Credito),
           sum(T006_Valor_Partilha_ICMS_Destino),
           sum(T006_Valor_Partilha_ICMS_Origem),
           sum(T006_Valor_Base_ICMS_Diferido),
           sum(T006_Valor_ICMS_Diferido),
           sum(T006_Valor_Base_ICMS_Sem_Reducao),
           T006_Aliquota_ICMS_Credito,
           D006_Deduz_ICMS_Desonerado_Valor_Total,
           sum(T006A_Valor_FCP_Partilha),
           sum(T006A_Valor_FCP),
           sum(T006A_Valor_FCP_ST)
      INTO xValor_Total_Custo,
           xValor_Total_Preco,
           xValor_Total_Preco_Sem_Desconto,
           xValor_Total_Custo_Final,
           xValor_Total_Desconto,
           xValor_ICMS,
           xValor_ICMS_Desonerado,
           xValor_Base_ICMS,
           xValor_ICMS_Oculto,
           xValor_Base_ICMS_Oculto,
           xValor_Total_Agregados,
           xValor_IPI,
           xValor_Base_IPI,
           xPeso_Total,
           xPeso_Bruto_Total,
           xValor_Base_ICMS_Substituicao,
           xValor_ICMS_Substituicao,
           xValor_ICMS_Substituicao_Retencao,
           xValor_Total_PIS,
           xValor_Total_COFINS,
           xValor_Total_Preco_Bruto,
           xValor_Total_Cubagem,
           xValor_Trib_Transparencia,
           xPercentual_Estoque_Disponivel,
           xPercentual_Ordem_Compra,
           xValor_IRPJ,
           xValor_Base_IRPJ,
           xValor_CSLL,
           xValor_Base_CSLL,
           xValor_ICMS_Credito,
           xValor_Total_Partilha_ICMS_Destino,
           xValor_Total_Partilha_ICMS_Origem,
           xValor_Base_ICMS_Diferido,
           xValor_ICMS_Diferido,
           xValor_Base_ICMS_Sem_Reducao,
           xAliquota_ICMS_Credito,
           xD006_Deduz_ICMS_Desonerado_Valor_Total,
           xValor_Total_FCP_Partilha,
           xValor_Total_FCP,
           xValor_FCP_ST
           FROM T006
      LEFT JOIN D006 ON T006_D006_Id=D006_Id
      LEFT JOIN T006A ON T006A_T006_Id=T006_Id
           WHERE T006_T005_Id=xT005_Id and T006_Flag_Status!=3;

    SELECT SUM(T147_Valor_Unitario)
      INTO xValor_Total_Servicos
      FROM T147
     WHERE T147_T005_Id=xT005_Id;

    SELECT SUM(T138_Valor_Preco_Unitario*T138_Quantidade)
      INTO xValor_Total_Trocas
      FROM T138
     WHERE T138_T005_Id=xT005_Id;

    SELECT T005_C004_Id,
           T005_Flag_Status,
           IFNULL(T005_Flag_Frete,0) AS T005_Flag_Frete,
           T005A_Flag_Ratear_Frete_Custo
      INTO xC004_Id,
           xT005_Flag_Status,
           xT005_Flag_Frete,
           xT005A_Flag_Ratear_Frete_Custo
      FROM T005
 LEFT JOIN T005A ON T005_Id = T005A_T005_Id
     WHERE T005_Id=xT005_Id;

    SELECT IFNULL(COUNT(T006_Id),0)
      INTO xFlagItensPendentePedido
      FROM T006
 LEFT JOIN D009 ON D009_Id=T006_D009_Id
 LEFT JOIN D049 ON D049_Id=D009_D049_Id      
 LEFT JOIN D069 on D069_Id=D009_D069_Id AND D069_Flag_Tabela_Principal='S'
 LEFT JOIN D070 on D070_Id=D069_D070_Id
     WHERE (T006_Quantidade_Estoque+T006_Quantidade_OC) < T006_Quantidade 
       AND IFNULL(T006_T219_Id,0) <= 0 
       AND IFNULL(T006_T225_Id,0) <= 0
       AND IFNULL(D049_Flag_Tipo,'') = 'CP'
       AND T006_Flag_Transferencia != 'S'
       AND D009_Valor_Custo_Tabela<0 
       AND ifnull(D070_Data_Vencimento,'0000-00-00')>=curdate()
       AND T006_T005_Id=xT005_Id;

    SELECT C031_Valor INTO xC031_Somar_ICMS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarICMSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_PIS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarPISPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_COFINS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCOFINSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_IRPJ_Preco_Venda FROM C031 WHERE C031_Campo = 'somarIRPJPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_CSLL_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCSLLPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Desabilitar_Compra_Emergencia FROM C031 WHERE C031_Campo = 'desabilitarCompraEmergencia' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Ratear_Frete_Venda_No_Custo FROM C031 WHERE C031_Campo = 'ratearFreteVendaNoCusto' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Preco_Padrao_Venda_Produto FROM C031 WHERE C031_Campo = 'precopadraoVendaProduto' AND C031_C004_Id = xC004_Id;

    IF xT005A_Flag_Ratear_Frete_Custo != '' THEN
        SET xC031_Ratear_Frete_Venda_No_Custo = xT005A_Flag_Ratear_Frete_Custo;
    END IF;

    IF xC031_Preco_Padrao_Venda_Produto = 'liquido' THEN
        SET xValor_Total_Preco_Bruto = 0;
    END IF;

    IF xC031_Desabilitar_Compra_Emergencia = 'S' OR xT005_Flag_Status = '5' OR xT005_Flag_Status = '8' THEN
      SET xT005_Flag_Status_Cotacao = '';
    END IF;

    UPDATE T005 SET T005_Flag_Status_Cotacao=xT005_Flag_Status_Cotacao,
                    T005_Peso_Total=ifnull(xPeso_Total,0),
                    T005_Peso_Bruto_Total=ifnull(xPeso_Bruto_Total,0),
                    T005_Valor_Total_Servicos=ifnull(xValor_Total_Servicos,0),
                    T005_Valor_Troca=ifnull(xValor_Total_Trocas,0),
                    T005_Valor_Total_Agregados=ifnull(xValor_Total_Agregados,0),
                    T005_Valor_Total_Produtos=ifnull(xValor_Total_Preco,0),
                    T005_Valor_Total_Produtos_Sem_Desconto=ifnull(xValor_Total_Preco_Sem_Desconto,0),
                    T005_Valor_Desconto=IF(xC031_Desabilitar_Compra_Emergencia='S' OR xFlagItensPendentePedido = 0,T005_Valor_Total_Produtos_Sem_Desconto-T005_Valor_Total_Produtos,0),
                    T005_Valor_Total_Custo_Final=ifnull(xValor_Total_Custo_Final,0)-T005_Valor_Desconto,
                    T005_Valor_Total_Custo=ifnull(xValor_Total_Custo,0),
                    T005_Valor_Total_Base_ICMS_Sem_Reducao=ifnull(xValor_Base_ICMS_Sem_Reducao,0),
                    T005_Valor_Total_Base_ICMS_Diferido=ifnull(xValor_Base_ICMS_Diferido,0),
                    T005_Valor_Total_ICMS_Diferido=ifnull(xValor_ICMS_Diferido,0),
                    T005_Valor_Total_Base_ICMS=ifnull(xValor_Base_ICMS,0),
                    T005_Valor_Total_ICMS=ifnull(xValor_ICMS,0),
                    T005_Valor_Total_ICMS_Desonerado=ifnull(xValor_ICMS_Desonerado,0),
                    T005_Valor_Total_Base_ICMS_Oculto=ifnull(xValor_Base_ICMS_Oculto,0),
                    T005_Valor_Total_ICMS_Oculto=ifnull(xValor_ICMS_Oculto,0),
                    T005_Valor_Total_Base_IPI=ifnull(xValor_Base_IPI,0),
                    T005_Valor_Total_IPI=ifnull(xValor_IPI,0),
                    T005_Valor_Total_PIS=ifnull(xValor_Total_PIS,0),
                    T005_Valor_Total_COFINS=ifnull(xValor_Total_COFINS,0),
                    T005_Valor_ISS=if(T005_Aliquota_ISS>0,T005_Valor_Mao_Obra*(T005_Aliquota_ISS/100),0),
                    T005_Valor_Base_ICMS_Substituicao=ifnull(xValor_Base_ICMS_Substituicao,0),
                    T005_Valor_ICMS_Substituicao=ifnull(xValor_ICMS_Substituicao,0),
                    T005_Valor_ICMS_Substituicao_Retencao=ifnull(xValor_ICMS_Substituicao_Retencao,0),
                    T005_Valor_Total_Base_IRPJ=xValor_Base_IRPJ,
                    T005_Valor_Total_IRPJ=xValor_IRPJ,
                    T005_Valor_Total_Base_CSLL=xValor_Base_CSLL,
                    T005_Valor_Total_CSLL=xValor_CSLL,
                    T005_Valor_Total_ICMS_Credito=xValor_ICMS_Credito,
                    T005_Aliquota_ICMS_Credito = xAliquota_ICMS_Credito,
                    T005_Valor_Total_Partilha_ICMS_Destino=xValor_Total_Partilha_ICMS_Destino,
                    T005_Valor_Total_Partilha_ICMS_Origem=xValor_Total_Partilha_ICMS_Origem,
                    T005_Valor_Total=if(T005_Flag_Status=8,0,
                                     ifnull(round(T005_Valor_Total_Produtos,2),0)+
                                     ifnull(T005_Valor_Mao_Obra,0)+
                                     ifnull(if(xValor_Total_Preco_Bruto>0,0,T005_Valor_Total_Agregados),0)+
                                     ifnull(T005_Valor_Despesas_Acessorias,0)+
                                     ifnull(round(T005_Valor_ICMS_Substituicao_Retencao,2),0)+
                                     ifnull(round(xValor_FCP_ST,2),0)+
                                     ifnull(round(T005_Valor_Total_IPI,2),0)+
                                     ifnull(T005_Valor_Total_Servicos,0)-
                                     ifnull(T005_Valor_Desconto_Manual,0)-
                                     ifnull(T005_Valor_Troca,0)+
                                     ifnull(if(xValor_Total_Preco_Bruto>0,0,IF(xC031_Ratear_Frete_Venda_No_Custo='S' AND xT005_Flag_Frete = '0', 0, T005_Valor_Frete)),0)-
                                     ifnull(if(xD006_Deduz_ICMS_Desonerado_Valor_Total = 'S',T005_Valor_Total_ICMS_Desonerado,0),0)),
                    T005_IPV=VEN_Calculo_IPV(0, 0, xT005_Id, 0, 0, 0, xC031_Somar_ICMS_Preco_Venda, xC031_Somar_PIS_Preco_Venda, xC031_Somar_COFINS_Preco_Venda, xC031_Somar_IRPJ_Preco_Venda, xC031_Somar_CSLL_Preco_Venda, T005_Valor_Total_Produtos, T005_Valor_Total_Custo, 1, T005_Valor_Total_ICMS, xValor_Total_PIS, xValor_Total_COFINS, xValor_IRPJ, xValor_CSLL), 
                    #T005_IPV=if(T005_Valor_Total_Custo>0,(T005_Valor_Total_Produtos-
                    #                 IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T005_Valor_Total_ICMS,0), 0)-
                    #                 IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(xValor_Total_PIS,0), 0)-
                    #                 IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(xValor_Total_COFINS,0), 0)-
                    #                 IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(xValor_IRPJ,0), 0)-
                    #                 IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(xValor_CSLL,0), 0))/T005_Valor_Total_Custo,0),

                    T005_Percentual_Margem = VEN_Calculo_Margem(0, 0, xT005_Id, 0, 0, 0, xC031_Somar_ICMS_Preco_Venda, xC031_Somar_PIS_Preco_Venda, xC031_Somar_COFINS_Preco_Venda, xC031_Somar_IRPJ_Preco_Venda, xC031_Somar_CSLL_Preco_Venda, T005_Valor_Total_Produtos, T005_Valor_Total_Custo, 1, T005_Valor_Total_ICMS, xValor_Total_PIS, xValor_Total_COFINS, xValor_IRPJ, xValor_CSLL), 
                    #T005_Percentual_Margem=round((((T005_Valor_Total_Produtos-
                    #                        IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T005_Valor_Total_ICMS,0), 0)-
                    #                        IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(xValor_Total_PIS,0), 0)-
                    #                        IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(xValor_Total_COFINS,0), 0)-
                    #                        IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(xValor_IRPJ,0), 0)-
                    #                        IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(xValor_CSLL,0), 0))-T005_Valor_Total_Custo)/
                    #                     (T005_Valor_Total_Produtos-
                    #                        IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T005_Valor_Total_ICMS,0), 0)-
                    #                        IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(xValor_Total_PIS,0), 0)-
                    #                        IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(xValor_Total_COFINS,0), 0)-
                    #                        IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(xValor_IRPJ,0), 0)-
                    #                        IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(xValor_CSLL,0), 0)))*100,2),
                    T005_IPV_Normal=ifnull(T005_IPV_Medio(xT005_Id,2),0),
                    T005_Total_Cubagem=IFNULL(xValor_Total_Cubagem,0),
                    T005_Valor_Total_Trib_Transparencia=xValor_Trib_Transparencia,
                    T005_Percentual_Estoque_Disponivel=xPercentual_Estoque_Disponivel
              WHERE T005_Id=xT005_Id;

    UPDATE T005 SET T005_Peso_Liquido=ifnull(xPeso_Total,0),
                    T005_Peso_Bruto=ifnull(xPeso_Bruto_Total,0)
              WHERE T005_Id=xT005_Id AND T005_Flag_Totalizar_Peso = 'S';

    UPDATE T005A 
 LEFT JOIN T005 ON T005_Id=T005A_T005_Id
       SET T005A_Percentual_Ordem_Compra=xPercentual_Ordem_Compra,
           T005A_Percentual_Estoque_Disponivel_E_Ordem_Compra=IF(T005A_Percentual_Ordem_Compra + T005_Percentual_Estoque_Disponivel >= 100 , 0, (100 - (T005A_Percentual_Ordem_Compra + T005_Percentual_Estoque_Disponivel))),
           T005A_Valor_Total_FCP=xValor_Total_FCP,
           T005A_Valor_FCP_ST=xValor_FCP_ST,
           T005A_Valor_Total_FCP_Partilha=xValor_Total_FCP_Partilha
     WHERE T005A_T005_Id=xT005_Id;
END;
