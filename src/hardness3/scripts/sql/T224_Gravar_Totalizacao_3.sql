CREATE PROCEDURE `T224_Gravar_Totalizacao_3`(xT224_Id int(11))
BEGIN
    DECLARE xValor_Total_Custo DECIMAL(10,2);
    DECLARE xValor_Total_Preco DECIMAL(10,2);
    DECLARE xValor_Total_Preco_Sem_Desconto DECIMAL(10,2);
    DECLARE xValor_Total_Custo_Final DECIMAL(10,2);
    DECLARE xValor_Total_Desconto DECIMAL(10,2);
    DECLARE xValor_Total_Servicos DECIMAL(10,2);
    DECLARE xValor_Total_Trocas DECIMAL(10,2);
    DECLARE xValor_ICMS DECIMAL(10,2);
    DECLARE xValor_Base_ICMS DECIMAL(10,2);
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
    DECLARE xValor_Total_Comissao decimal(14,5) default 0;
    DECLARE xValorPendente decimal(14,5) default 0;
    DECLARE xValor_Total_Cubagem DECIMAL(10,3) default 0;
    DECLARE xValor_Trib_Transparencia DECIMAL(15,2) DEFAULT 0;
    DECLARE xT225A_Valor_Base_FCP_ST DECIMAL(15,2) DEFAULT 0;
    DECLARE xT225A_Valor_FCP_ST DECIMAL(15,2) DEFAULT 0;
    DECLARE xT225A_Valor_FCP DECIMAL(15,2) DEFAULT 0;
    DECLARE xT225A_Valor_Base_FCP DECIMAL(15,2) DEFAULT 0;

    SELECT sum(T225_Valor_Total_Custo),
           sum(T225_Valor_Total_Preco),
           sum(T225_Valor_Total_Preco_Sem_Desconto),
           sum(T225_Valor_Total_Custo_Final),
           sum(T225_Valor_Total_Desconto),
           sum(T225_Valor_ICMS),
           sum(T225_Valor_Base_ICMS),
           sum(T225_Valor_Total_Agregados),
           sum(T225_Valor_IPI),
           sum(T225_Valor_Base_IPI),
           sum(T225_Peso_Total),
           sum(T225_Peso_Bruto_Total),
           sum(T225_Valor_Base_ICMS_Substituicao),
           sum(T225_Valor_ICMS_Substituicao),
           sum(T225_Valor_ICMS_Substituicao_Retencao),
           sum(T225_Valor_PIS),
           sum(T225_Valor_COFINS),
           sum(T225_Valor_Preco_Bruto),
           sum(T225_Valor_Comissao),
           sum(T225_Total_Cubagem),
           sum(T225_Valor_Trib_Transparencia),
           sum(distinct IF(T225_Quantidade_Pendente_3(T225_Id) <= 0, 0, concat(T225_Valor_Preco_Unitario*T225_Quantidade_Pendente_3(T225_Id),T225_Id))),
           sum(T225A_Valor_Base_FCP_ST),
           sum(T225A_Valor_FCP_ST),
           sum(T225A_Valor_FCP),
           sum(T225A_Valor_Base_FCP)
      INTO xValor_Total_Custo,
           xValor_Total_Preco,
           xValor_Total_Preco_Sem_Desconto,
           xValor_Total_Custo_Final,
           xValor_Total_Desconto,
           xValor_ICMS,
           xValor_Base_ICMS,
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
           xValor_Total_Comissao,
           xValor_Total_Cubagem,
           xValor_Trib_Transparencia,
           xValorPendente,
           xT225A_Valor_Base_FCP_ST,
           xT225A_Valor_FCP_ST,
           xT225A_Valor_FCP,
           xT225A_Valor_Base_FCP
           FROM T225
      LEFT JOIN T225A ON T225_Id = T225A_T225_Id
           WHERE T225_T224_Id=xT224_Id
             AND T225_Flag_Cancelada!='S' ;

    SELECT SUM(T230_Valor_Unitario)
      INTO xValor_Total_Servicos
      FROM T230
     WHERE T230_T224_Id=xT224_Id;           
    SELECT SUM(T222_Valor_Preco_Unitario*T222_Quantidade)
      INTO xValor_Total_Trocas
      FROM T222
     WHERE T222_T224_Id=xT224_Id;  

    UPDATE T224 SET T224_Peso_Total=ifnull(xPeso_Total,0),
                    T224_Peso_Bruto_Total=ifnull(xPeso_Bruto_Total,0),
                    T224_Valor_Total_Servicos=ifnull(xValor_Total_Servicos,0),
                    T224_Valor_Troca=ifnull(xValor_Total_Trocas,0),
                    T224_Valor_Total_Agregados=ifnull(xValor_Total_Agregados,0),
                    T224_Valor_Total_Produtos=ifnull(xValor_Total_Preco,0),
                    T224_Valor_Total_Produtos_Sem_Desconto=ifnull(xValor_Total_Preco_Sem_Desconto,0),
                    T224_Valor_Total_Custo_Final=ifnull(xValor_Total_Custo_Final,0)-T224_Valor_Desconto,
                    T224_Valor_Total_Custo=ifnull(xValor_Total_Custo,0),
                    T224_Valor_Total_Base_ICMS=ifnull(xValor_Base_ICMS,0),
                    T224_Valor_Total_ICMS=ifnull(xValor_ICMS,0),
                    T224_Valor_Total_Base_IPI=ifnull(xValor_Base_IPI,0),
                    T224_Valor_Total_IPI=ifnull(xValor_IPI,0),
                    T224_Valor_Total_PIS=ifnull(xValor_Total_PIS,0),
                    T224_Valor_Total_COFINS=ifnull(xValor_Total_COFINS,0),
                    T224_Valor_ISS=if(T224_Aliquota_ISS>0,T224_Valor_Mao_Obra*(T224_Aliquota_ISS/100),0),
                    T224_Valor_Base_ICMS_Substituicao=ifnull(xValor_Base_ICMS_Substituicao,0),
                    T224_Valor_ICMS_Substituicao=ifnull(xValor_ICMS_Substituicao,0),
                    T224_Valor_ICMS_Substituicao_Retencao=ifnull(xValor_ICMS_Substituicao_Retencao,0),
                    T224_Valor_Total=if(T224_Flag_Status=11,0,
                                     ifnull(round(T224_Valor_Total_Produtos_Sem_Desconto,2),0)+
                                     ifnull(T224_Valor_Mao_Obra,0)+
                                     ifnull(if(xValor_Total_Preco_Bruto>0,0,T224_Valor_Total_Agregados),0)+
                                     ifnull(T224_Valor_Despesas_Acessorias,0)+
                                     ifnull(round(T224_Valor_ICMS_Substituicao_Retencao,2),0)+
                                     ifnull(round(T224_Valor_Total_IPI,2),0)+
                                     ifnull(T224_Valor_Total_Servicos,0)-
                                     ifnull(T224_Valor_Desconto_Manual,0)-
                                     ifnull(T224_Valor_Troca,0)+
                                     ifnull(xT225A_Valor_FCP_ST,0)+
                                     ifnull(if(xValor_Total_Preco_Bruto>0,0,T224_Valor_Frete),0)),
                    T224_IPV=ifnull(T224_IPV_Medio(xT224_Id,1),0),
                    T224_IPV_Normal=ifnull(T224_IPV_Medio(xT224_Id,2),0),
                    T224_Valor_Total_Comissao=ifnull(xValor_Total_Comissao,0),
                    T224_Total_Cubagem=IFNULL(xValor_Total_Cubagem,0),
                    T224_Valor_Total_Trib_Transparencia=xValor_Trib_Transparencia,
                    T224_Valor_Pendente=xValorPendente
              WHERE T224_Id=xT224_Id;

    UPDATE T224 SET T224_Peso_Liquido=ifnull(xPeso_Total,0),
                    T224_Peso_Bruto=ifnull(xPeso_Bruto_Total,0)
                WHERE T224_Id=xT224_Id AND T224_Flag_Totalizar_Peso = 'S';

       UPDATE T224A
          SET T224A_Valor_FCP_ST=xT225A_Valor_FCP_ST,
              T224A_Valor_Base_FCP_ST=xT225A_Valor_Base_FCP_ST,
              T224A_Valor_Base_FCP=xT225A_Valor_Base_FCP,
              T224A_Valor_FCP=xT225A_Valor_FCP
        WHERE T224A_T224_Id=xT224_Id;

    CALL T224_Gravar_Totalizacao_3_Reforma(xT224_Id);
END;
