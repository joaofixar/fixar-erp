CREATE PROCEDURE `T218_Gravar_Totalizacao_3`(xT218_Id int(11))
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
    DECLARE xT219A_Valor_FCP_ST DECIMAL(15,2) DEFAULT 0;
    DECLARE xT219A_Valor_Base_FCP_ST DECIMAL(15,2) DEFAULT 0;
    DECLARE xT219A_Valor_FCP DECIMAL(15,2) DEFAULT 0;
    DECLARE xT219A_Valor_Base_FCP DECIMAL(15,2) DEFAULT 0;


    SELECT sum(T219_Valor_Total_Custo),
           sum(T219_Valor_Total_Custo_Confirmado),
           sum(T219_Valor_Total_Preco),
           sum(T219_Valor_Total_Preco_Confirmado),
           sum(T219_Valor_Total_Preco_Sem_Desconto),
           sum(T219_Valor_Total_Preco_Sem_Desconto_Confirmado),
           sum(T219_Valor_Total_Custo_Final),
           sum(T219_Valor_Total_Custo_Final_Confirmado),
           sum(T219_Valor_ICMS),
           sum(T219_Valor_ICMS_Confirmado),
           sum(T219_Valor_Total_Agregados),
           sum(T219_Valor_Base_ICMS),
           sum(T219_Valor_Base_ICMS_Confirmado),
           sum(T219_Valor_IPI),
           sum(T219_Valor_IPI_Confirmado),
           sum(T219_Valor_Base_IPI),
           sum(T219_Valor_Base_IPI_Confirmado),
           sum(T219_Peso_Total),
           sum(T219_Peso_Bruto_Total),
           sum(T219_Peso_Total_Confirmado),
           sum(T219_Valor_Total_Desconto),
           sum(T219_Valor_Total_Desconto_Confirmado),
           sum(T219_Valor_Base_ST),
           sum(T219_Valor_ST),
           sum(T219_Valor_Base_ST_Confirmado),
           sum(T219_Valor_ST_Confirmado),
           sum(T219_Valor_Preco_Bruto),
           sum(T219_Total_Cubagem),
           sum(T219_Valor_Trib_Transparencia),
           sum(T219A_Valor_FCP_ST),
           sum(T219A_Valor_Base_FCP_ST),
           sum(T219A_Valor_FCP),
           sum(T219A_Valor_Base_FCP)
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
           xT219A_Valor_FCP_ST,
           xT219A_Valor_Base_FCP_ST,
           xT219A_Valor_FCP,
           xT219A_Valor_Base_FCP
           FROM T219
      LEFT JOIN T219A ON T219_Id = T219A_T219_Id
          WHERE T219_T218_Id=xT218_Id
            AND T219_Flag_Cancelado!='S';

    SELECT SUM(T223_Valor_Unitario)
      INTO xValor_Total_Servicos
      FROM T223
     WHERE T223_T218_Id=xT218_Id;           
    SELECT SUM(T222_Valor_Preco_Unitario*T222_Quantidade)
      INTO xValor_Total_Trocas
      FROM T222
     WHERE T222_T218_Id=xT218_Id;  

    UPDATE T218 SET T218_Valor_Total_Servicos=ifnull(xValor_Total_Servicos,0),
                    T218_Valor_Troca=ifnull(xValor_Total_Trocas,0),
                    T218_Valor_Total_Produtos=xValor_Total_Preco,
                    T218_Valor_Total_Produtos_Confirmado=xValor_Total_Preco_Confirmado,
                    T218_Valor_Total_Produtos_Sem_Desconto=xValor_Total_Preco_Sem_Desconto,
                    T218_Valor_Total_Produtos_Sem_Desconto_Confirmado=xValor_Total_Preco_Sem_Desconto_Confirmado,
                    T218_Valor_Total_Custo_Final=xValor_Total_Custo_Final,
                    T218_Valor_Total_Custo_Final_Confirmado=xValor_Total_Custo_Final_Confirmado,
                    T218_Valor_Total_Custo=xValor_Total_Custo,
                    T218_Valor_Total_Custo_Confirmado=xValor_Total_Custo_Confirmado,
                    T218_Valor_Total_Agregados=ifnull(xValor_Total_Agregados,0),
                    T218_Valor_Total_Base_ICMS=xValor_Base_ICMS,
                    T218_Valor_Total_Base_ICMS_Confirmado=xValor_Base_ICMS_Confirmado,
                    T218_Valor_Total_ICMS=xValor_ICMS,
                    T218_Valor_Total_ICMS_Confirmado=xValor_ICMS_Confirmado,
                    T218_Valor_Total_Base_IPI=xValor_Base_IPI,
                    T218_Valor_Total_Base_IPI_Confirmado=xValor_Base_IPI_Confirmado,
                    T218_Valor_Total_IPI=xValor_IPI,
                    T218_Valor_Total_IPI_Confirmado=xValor_IPI_Confirmado,
                    T218_Valor_ISS=if(T218_Aliquota_ISS>0,T218_Valor_Mao_Obra*(T218_Aliquota_ISS/100),0),
                    T218_Valor_Base_ST=xValor_Base_ST,
                    T218_Valor_ST=xValor_ST,
                    T218_Valor_Base_ST_Confirmado=xValor_Base_ST_Confirmado,
                    T218_Valor_Desconto=xValor_Total_Desconto,
                    T218_Valor_Desconto_Confirmado=xValor_Total_Desconto_Confirmado,
                    T218_Valor_ST_Confirmado=xValor_ST_Confirmado,
                    T218_Valor_Total=ifnull(T218_Valor_Total_Produtos,0)+
                                     ifnull(T218_Valor_Mao_Obra,0)+
                                     ifnull(if(xValor_Total_Preco_Bruto>0,0,T218_Valor_Total_Agregados),0)+
                                     ifnull(T218_Valor_Despesas_Acessorias,0)+
                                     ifnull(T218_Valor_Total_IPI,0)+
                                     ifnull(T218_Valor_ST,0)+
                                     ifnull(T218_Valor_Total_Servicos,0)-
                                     ifnull(T218_Valor_Desconto_Manual,0)-
                                     ifnull(T218_Valor_Troca,0)+
                                     ifnull(xT219A_Valor_FCP_ST,0)+
                                     ifnull(T218_Valor_Frete,0),
                    T218_Valor_Total_Confirmado=if(T218_Flag_Confirmar_Mao_Obra='S' or ifnull(xValor_Total_Preco_Confirmado,0)>0,
                                     ifnull(T218_Valor_Total_Produtos_Confirmado,0)+
                                     ifnull(T218_Valor_Mao_Obra,0)+
                                     ifnull(T218_Valor_Despesas_Acessorias,0)+
                                     ifnull(T218_Valor_Total_IPI_Confirmado,0)+
                                     ifnull(T218_Valor_ST_Confirmado,0)+
                                     ifnull(T218_Valor_Total_Servicos,0)-
                                     ifnull(T218_Valor_Desconto_Manual_Confirmado,0)-
                                     ifnull(T218_Valor_Troca,0)+
                                     ifnull(xT219A_Valor_FCP_ST,0)+
                                     ifnull(T218_Valor_Frete,0),0),
                    T218_IPV=if(T218_Valor_Total_Custo>0,T218_Valor_Total/T218_Valor_Total_Custo,0),
                    T218_IPV_Confirmado=if(T218_Valor_Total_Custo_Confirmado>0,T218_Valor_Total_Confirmado/T218_Valor_Total_Custo_Confirmado,0),
                    T218_IPV_Normal=ifnull(T218_IPV_Medio(xT218_Id,3),0),
                    T218_IPV_Normal_Confirmado=T218_IPV_Medio(xT218_Id,4),
                    T218_Peso_Total=ifnull(xPeso_Total,0),
                    T218_Peso_Bruto_Total=ifnull(xPeso_Bruto_Total,0),
                    T218_Peso_Total_Confirmado=ifnull(xPeso_Total_Confirmado,0),
                    T218_Total_Cubagem=IFNULL(xValor_Total_Cubagem,0),
                    T218_Valor_Total_Trib_Transparencia=xValor_Trib_Transparencia
              WHERE T218_Id=xT218_Id;

    UPDATE T218A
        SET T218A_Valor_FCP_ST=xT219A_Valor_FCP_ST,
            T218A_Valor_Base_FCP_ST=xT219A_Valor_Base_FCP_ST,
            T218A_Valor_Base_FCP=xT219A_Valor_Base_FCP,
            T218A_Valor_FCP=xT219A_Valor_FCP
      WHERE T218A_T218_Id=xT218_Id;
END;
