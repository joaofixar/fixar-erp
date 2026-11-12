CREATE PROCEDURE `T007_Gravar_Totalizacao_4`(xT007_Id int(11))
BEGIN
    DECLARE xValor_Total_Custo DECIMAL(20,2);
    DECLARE xValor_Total_Preco DECIMAL(20,2);
    DECLARE xValor_Total_Custo_Final DECIMAL(20,2);
    DECLARE xValor_Total_Produtos DECIMAL(20,2);
    DECLARE xValor_Total_Servicos DECIMAL(20,2);
    DECLARE xValor_Total_Produtos_Sem_Desconto DECIMAL(20,2);
    DECLARE xValor_Base_ICMS DECIMAL(20,2);
    DECLARE xValor_Base_ICMS_Sem_Reducao DECIMAL(20,2);
    DECLARE xValor_Base_ICMS_Diferido DECIMAL(20,2);
    DECLARE xValor_ICMS DECIMAL(20,2);
    DECLARE xValor_ICMS_Desonerado DECIMAL(20,2);
    DECLARE xValor_Base_IPI DECIMAL(20,2);
    DECLARE xValor_IPI DECIMAL(20,2);
    DECLARE xValor_Total_II DECIMAL(19,2) DEFAULT 0;
    DECLARE xValor_Base_ICMS_Impresso DECIMAL(20,2);
    DECLARE xValor_Base_ICMS_Substituicao DECIMAL(20,2) DEFAULT 0;
    DECLARE xValor_ICMS_Substituicao DECIMAL(20,2) DEFAULT 0;
    DECLARE xValor_ICMS_Substituicao_Retencao DECIMAL(20,2) DEFAULT 0;
    DECLARE xValor_PIS DECIMAL(10,2) DEFAULT 0;
    DECLARE xValor_COFINS DECIMAL(10,2) DEFAULT 0;
    DECLARE xValor_Total_PIS_Retido DECIMAL(20,2) DEFAULT 0;
    DECLARE xValor_Total_COFINS_Retido DECIMAL(20,2) DEFAULT 0;
    DECLARE xPeso_Total DECIMAL(20,3) DEFAULT 0;
    DECLARE xPeso_Total_Bruto DECIMAL(20,3) DEFAULT 0;
    DECLARE xValor_Total_Desconto DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_Total_Cubagem DECIMAL(10,3) DEFAULT 0;
    DECLARE xValor_Trib_Transparencia DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_Total_Trib_Federal DECIMAL(14,2) DEFAULT 0;
    DECLARE xValor_Total_Trib_Estadual DECIMAL(14,2) DEFAULT 0;
    DECLARE xValor_Total_Trib_Municipal DECIMAL(14,2) DEFAULT 0;
    DECLARE xValor_Base_ICMS_Interno DECIMAL(14,2) DEFAULT 0;
    DECLARE xValor_Base_ICMS_Inter_Estadual DECIMAL(14,2) DEFAULT 0;
    DECLARE xValor_ICMS_Interno DECIMAL(14,2) DEFAULT 0;
    DECLARE xValor_ICMS_Inter_Estadual DECIMAL(14,2) DEFAULT 0;
    DECLARE xC004_Id int(10);
    DECLARE xC031_Somar_ICMS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_PIS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_COFINS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_IRPJ_Preco_Venda varchar(1);
    DECLARE xC031_Somar_CSLL_Preco_Venda varchar(1);
    DECLARE xValor_IRPJ DECIMAL(10,2);
    DECLARE xValor_Base_IRPJ DECIMAL(10,2);
    DECLARE xValor_CSLL DECIMAL(10,2);
    DECLARE xValor_Base_CSLL DECIMAL(10,2);
    DECLARE xValor_ICMS_Credito DECIMAL(10,2);
    DECLARE xAliquota_ICMS_Credito DECIMAL(10,2);
    DECLARE xValor_Total_FCP_Partilha DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_Total_Partilha_ICMS_Destino DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_Total_Partilha_ICMS_Origem DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_Total_FCP DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_FCP_ST  DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_FCP_ST_Ret   DECIMAL(15,2) DEFAULT 0;
    DECLARE xValor_Total_IPI_Devolucao   DECIMAL(14,2) DEFAULT 0;
    DECLARE xValor_Prazo_Medio int(10);
    DECLARE xValor_ICMS_Diferido DECIMAL(15,2) DEFAULT 0;
    DECLARE xD006_Deduz_ICMS_Desonerado_Valor_Total VARCHAR (1) DEFAULT 'S';
    DECLARE xD006_Flag_Estado VARCHAR (1);
    DECLARE xT008_Valor_Total_Despesas_Aduaneira DECIMAL (14,2);

    SELECT SUM(T008_Valor_Total_Custo),
           SUM(T008_Valor_Total_Preco),
           SUM(T008_Valor_Total_Preco),
           SUM(round(T008_Valor_Preco_Original*T008_Quantidade,2)),
           SUM(T008_Valor_Total_Custo_Final),
           SUM(round(T008_Valor_Base_ICMS,2)),
           SUM(T008_Valor_Base_ICMS_Sem_Reducao),
           SUM(round(T008_Valor_Base_ICMS_Diferido,2)),
           SUM(round(T008_Valor_ICMS,2)),
           SUM(T008_Valor_ICMS_Desonerado),
           SUM(T008_Valor_Base_IPI),
           SUM(T008_Valor_IPI),
           SUM(round(T008_Valor_Base_ICMS_Impresso,2)),
           SUM(T008_Valor_Base_ICMS_Substituicao),
           SUM(T008_Valor_ICMS_Substituicao),
           SUM(T008_Valor_ICMS_Substituicao_Retencao),
           SUM(T008_Peso_Total),
           SUM(T008_Peso_Bruto_Total),
           SUM(T008_Valor_Total_Imposto_Importacao),
           SUM(T008_Valor_COFINS),
           SUM(T008_Valor_PIS),
           SUM(T008A_Valor_PIS_Retido),
           SUM(T008A_Valor_COFINS_Retido),
           SUM(T008_Valor_Total_Desconto),
           SUM(T008_Total_Cubagem),
           SUM(T008_Valor_Base_ICMS_Interno),
           SUM(T008_Valor_Base_ICMS_Inter_Estadual),
           SUM(T008_Valor_ICMS_Interno),
           SUM(T008_Valor_ICMS_Inter_Estadual),
           SUM(T008_Valor_Trib_Transparencia),
           SUM(T008_Valor_Trib_Federal),
           SUM(T008_Valor_Trib_Estadual),
           SUM(T008_Valor_Trib_Municipal),
           SUM(T008_Valor_IRPJ),
           SUM(T008_Valor_Base_IRPJ),
           SUM(T008_Valor_CSLL),
           SUM(T008_Valor_Base_CSLL),
           SUM(T008_Valor_ICMS_Credito),
           T008_Aliquota_ICMS_Credito,
           SUM(T008_Valor_FCP_Partilha),
           SUM(T008_Valor_Partilha_ICMS_Destino),
           SUM(T008_Valor_Partilha_ICMS_Origem),
           SUM(T008_Valor_FCP),
           SUM(T008_Valor_FCP_ST),
           SUM(T008_Valor_FCP_Retido),
           SUM(T008_Valor_IPI_Devolucao),
           SUM(T008_Valor_ICMS_Diferido),
           SUM(T008_Valor_Total_Despesas_Aduaneira),
           D006_Deduz_ICMS_Desonerado_Valor_Total,
           D006_Flag_Estado
      INTO xValor_Total_Custo,
           xValor_Total_Produtos,
           xValor_Total_Preco,
           xValor_Total_Produtos_Sem_Desconto,
           xValor_Total_Custo_Final,
           xValor_Base_ICMS,
           xValor_Base_ICMS_Sem_Reducao,
           xValor_Base_ICMS_Diferido,
           xValor_ICMS,
           xValor_ICMS_Desonerado,
           xValor_Base_IPI,
           xValor_IPI,
           xValor_Base_ICMS_Impresso,
           xValor_Base_ICMS_Substituicao,
           xValor_ICMS_Substituicao,
           xValor_ICMS_Substituicao_Retencao,
           xPeso_Total,
           xPeso_Total_Bruto,
           xValor_Total_II,
           xValor_COFINS,
           xValor_PIS,
           xValor_Total_PIS_Retido,
           xValor_Total_COFINS_Retido,
           xValor_Total_Desconto,
           xValor_Total_Cubagem,
           xValor_Base_ICMS_Interno,
           xValor_Base_ICMS_Inter_Estadual,
           xValor_ICMS_Interno,
           xValor_ICMS_Inter_Estadual,
           xValor_Trib_Transparencia,
           xValor_Total_Trib_Federal,
           xValor_Total_Trib_Estadual,
           xValor_Total_Trib_Municipal,
           xValor_IRPJ,
           xValor_Base_IRPJ,
           xValor_CSLL,
           xValor_Base_CSLL,
           xValor_ICMS_Credito,
           xAliquota_ICMS_Credito,
           xValor_Total_FCP_Partilha,
           xValor_Total_Partilha_ICMS_Destino,
           xValor_Total_Partilha_ICMS_Origem,
           xValor_Total_FCP,
           xValor_FCP_ST,
           xValor_FCP_ST_Ret,
           xValor_Total_IPI_Devolucao,
           xValor_ICMS_Diferido,
           xT008_Valor_Total_Despesas_Aduaneira,
           xD006_Deduz_ICMS_Desonerado_Valor_Total,
           xD006_Flag_Estado
           FROM T008
      LEFT JOIN T008A ON T008A_T008_Id=T008_Id
      LEFT JOIN D006 ON T008_D006_Id=D006_Id
          WHERE T008_T007_Id=xT007_Id;
      
    SELECT sum(T203_Valor_Unitario)
      INTO xValor_Total_Servicos
      FROM T203
      WHERE T203_T007_Id=xT007_Id;
    
    SELECT T007_C004_Id
      INTO xC004_Id     
      FROM T007
     WHERE T007_Id=xT007_Id;

    SELECT FORMAT((SUM(T118_Prazos_Dias)/COUNT(*)),0,"pt_BR")
      INTO xValor_Prazo_Medio     
      FROM T118
     WHERE T118_T007_Id=xT007_Id;

    SELECT C031_Valor INTO xC031_Somar_ICMS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarICMSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_PIS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarPISPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_COFINS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCOFINSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_IRPJ_Preco_Venda FROM C031 WHERE C031_Campo = 'somarIRPJPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_CSLL_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCSLLPrecoVenda' AND C031_C004_Id = xC004_Id;

    UPDATE T007 SET T007_Valor_Total_Custo=xValor_Total_Custo,
                    T007_Valor_Total_Produtos=xValor_Total_Produtos,
                    T007_Prazo_Medio=xValor_Prazo_Medio,
                    T007_Valor_Total_Servicos=xValor_Total_Servicos,
                    T007_Valor_Total_Produtos_Sem_Desconto = xValor_Total_Produtos_Sem_Desconto,
                    T007_Valor_Total_Preco=xValor_Total_Preco,
                    T007_Valor_Total_Custo_Final=xValor_Total_Custo_Final-T007_Valor_Desconto,
                    T007_Valor_Total_Base_ICMS_Sem_Reducao=xValor_Base_ICMS_Sem_Reducao,
                    T007_Valor_Total_Base_ICMS=xValor_Base_ICMS,
                    T007_Valor_Total_Base_ICMS_Diferido=xValor_Base_ICMS_Diferido,
                    T007_Valor_Total_ICMS_Diferido=xValor_ICMS_Diferido,
                    T007_Valor_Total_Base_ICMS_Impresso=xValor_Base_ICMS_Impresso,
                    T007_Valor_Total_ICMS=xValor_ICMS,
                    T007_Valor_Total_ICMS_Desonerado=IFNULL(xValor_ICMS_Desonerado,0),
                    T007_Valor_Total_Base_IPI=xValor_Base_IPI,
                    T007_Valor_Total_IPI=xValor_IPI,
                    T007_Valor_Total_Imposto_Importacao=xValor_Total_II,
                    T007_Valor_ISS=if(T007_Aliquota_ISS>0,T007_Valor_Mao_Obra*(T007_Aliquota_ISS/100),0),
                    
                    T007_IPV=VEN_Calculo_IPV(0, 0, 0, 0, xT007_Id, 0, xC031_Somar_ICMS_Preco_Venda, xC031_Somar_PIS_Preco_Venda, xC031_Somar_COFINS_Preco_Venda, xC031_Somar_IRPJ_Preco_Venda, xC031_Somar_CSLL_Preco_Venda, T007_Valor_Total_Produtos, T007_Valor_Total_Custo, 1, T007_Valor_Total_ICMS, xValor_PIS, xValor_COFINS, xValor_IRPJ, xValor_CSLL), 
                    #T007_IPV=if(T007_Valor_Total_Custo>0,(T007_Valor_Total_Produtos-
                    #                 IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T007_Valor_Total_ICMS,0), 0)-
                    #                 IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(xValor_PIS,0), 0)-
                    #                 IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(xValor_COFINS,0), 0)-
                    #                 IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(xValor_IRPJ,0), 0)-
                    #                IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(xValor_CSLL,0), 0))/T007_Valor_Total_Custo,0),
                    
                    T007_Percentual_Margem=VEN_Calculo_Margem(0, 0, 0, 0, xT007_Id, 0, xC031_Somar_ICMS_Preco_Venda, xC031_Somar_PIS_Preco_Venda, xC031_Somar_COFINS_Preco_Venda, xC031_Somar_IRPJ_Preco_Venda, xC031_Somar_CSLL_Preco_Venda, T007_Valor_Total_Produtos, T007_Valor_Total_Custo, 1, T007_Valor_Total_ICMS, xValor_PIS, xValor_COFINS, xValor_IRPJ, xValor_CSLL), 
                    #T007_Percentual_Margem=round((((T007_Valor_Total_Produtos-
                    #                        IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T007_Valor_Total_ICMS,0), 0)-
                    #                        IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(xValor_PIS,0), 0)-
                    #                        IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(xValor_COFINS,0), 0)-
                    #                        IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(xValor_IRPJ,0), 0)-
                    #                        IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(xValor_CSLL,0), 0))-T007_Valor_Total_Custo)/
                    #                      (T007_Valor_Total_Produtos-
                    #                        IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T007_Valor_Total_ICMS,0), 0)-
                    #                        IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(xValor_PIS,0), 0)-
                    #                        IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(xValor_COFINS,0), 0)-
                    #                        IF(xC031_Somar_IRPJ_Preco_Venda = 'S', IFNULL(xValor_IRPJ,0), 0)-
                    #                        IF(xC031_Somar_CSLL_Preco_Venda = 'S', IFNULL(xValor_CSLL,0), 0)))*100,2),
                    T007_Total_Peso=xPeso_Total,
                    T007_Total_Peso_Bruto=xPeso_Total_Bruto,
                    T007_IPV_sem_icms=T007_IPV_Medio(xT007_Id,2),
                    T007_Valor_Base_ICMS_Substituicao=xValor_Base_ICMS_Substituicao,
                    T007_Valor_ICMS_Substituicao=xValor_ICMS_Substituicao,
                    T007_Valor_ICMS_Substituicao_Retencao=ifnull(xValor_ICMS_Substituicao_Retencao,0.00),
                    T007_Valor_Base_ICMS_Interno=ifnull(xValor_Base_ICMS_Interno,0),
                    T007_Valor_Base_ICMS_Inter_Estadual=ifnull(xValor_Base_ICMS_Inter_Estadual,0),
                    T007_Valor_ICMS_Interno=ifnull(xValor_ICMS_Interno,0),
                    T007_Valor_ICMS_Inter_Estadual=ifnull(xValor_ICMS_Inter_Estadual,0),
                    T007_Valor_Total_ICMS_Credito=xValor_ICMS_Credito,
                    T007_Aliquota_ICMS_Credito=xAliquota_ICMS_Credito,
                    T007_Valor_Total_COFINS=xValor_COFINS,
                    T007_Valor_Total_PIS=xValor_PIS,
                    T007_Valor_Total_Base_IRPJ=xValor_Base_IRPJ,
                    T007_Valor_Total_IRPJ=xValor_IRPJ,
                    T007_Valor_Total_Base_CSLL=xValor_Base_CSLL,
                    T007_Valor_Total_CSLL=xValor_CSLL,
                    T007_Valor_Total_Trib_Transparencia=xValor_Trib_Transparencia,
                    T007_Valor_Total_Trib_Federal=xValor_Total_Trib_Federal,
                    T007_Valor_Total_Trib_Estadual=xValor_Total_Trib_Estadual,
                    T007_Valor_Total_Trib_Municipal=xValor_Total_Trib_Municipal,
                    T007_Valor_Total_FCP_Partilha=xValor_Total_FCP_Partilha,
                    T007_Valor_Total_Partilha_ICMS_Destino=xValor_Total_Partilha_ICMS_Destino,
                    T007_Valor_Total_Partilha_ICMS_Origem=xValor_Total_Partilha_ICMS_Origem,
                    T007_Valor_Total_IPI_Devolucao=xValor_Total_IPI_Devolucao,
                    T007_Total_Cubagem=IFNULL(xValor_Total_Cubagem,0), 
                    T007_Valor_Total_FCP=xValor_Total_FCP,
                    T007_Valor_FCP_ST=xValor_FCP_ST,
                    T007_Valor_FCP_ST_Ret=xValor_FCP_ST_Ret,
                    T007_Valor_Total=T007_Valor_Total(T007_Valor_Total_Produtos, T007_Valor_Total_Servicos, T007_Valor_Mao_Obra, T007_Valor_Despesas_Acessorias, T007_Valor_ICMS_Substituicao_Retencao, T007_Valor_Total_IPI, T007_Valor_Total_IPI_Devolucao, T007_Valor_Seguro, T007_Valor_FCP_ST, T007_Valor_Frete, T007_Valor_Desconto, T007_Valor_Total_ICMS_Desonerado, T007_Valor_Total_ICMS, T007_Valor_Total_Imposto_Importacao, T007_Valor_Total_FCP, xD006_Deduz_ICMS_Desonerado_Valor_Total, xD006_Flag_Estado, T007_Valor_Total_COFINS, T007_Valor_Total_PIS, xT008_Valor_Total_Despesas_Aduaneira)
              WHERE T007_Id=xT007_Id and T007_Flag_Calcular='S' AND T007_Flag_Cancelada!='S' AND T007_Flag_Inutilizado!='S' AND ifnull(T007_Numero_Protocolo_Nfe,'')='';

    UPDATE T007A
       SET T007A_Valor_Total_PIS_Retido=xValor_Total_PIS_Retido,
           T007A_Valor_Total_COFINS_Retido=xValor_Total_COFINS_Retido
     WHERE T007A_T007_Id=xT007_Id;
    -- SO FAZ OS CALCULOS DA NOTA SE FOR UMA NOTA NORMAL (T007_Flag_Finalidade_NFe=1)
    UPDATE T007 SET T007_Peso_Bruto = xPeso_Total_Bruto,
                    T007_Peso_Liquido = xPeso_Total
                WHERE T007_Id=xT007_Id AND T007_Flag_Totalizar_Peso = 'S' AND ifnull(T007_Numero_Protocolo_Nfe,'')='';

    -- [REFORMA TRIBUTÁRIA] Chama a PROCEDURE para calcular tudo da reforma tributária
    CALL T007_Gravar_Totalizacao_4_Reforma(xT007_Id);
END;
