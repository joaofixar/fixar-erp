CREATE PROCEDURE `T014_Gravar_Totalizacao`(xT014_Id int(11))
BEGIN
    DECLARE xT013_Id INT(11);
    DECLARE xValor_Total_Produtos DECIMAL(14,2);
    DECLARE xValor_Total_Preco DECIMAL(14,2);
    DECLARE xValor_Custo_Final_Unitario DECIMAL(10,2);
    DECLARE xValor_Total_Custo_Final DECIMAL(10,2);
    DECLARE xValor_Custo_Unitario DECIMAL(10,2);
    DECLARE xValor_Total_Custo DECIMAL(10,2);
    DECLARE xValor_Desconto_Produtos DECIMAL(12,6);
    DECLARE xValor_Frete DECIMAL(10,2);
    DECLARE xValor_Seguro DECIMAL(14,2);
    DECLARE xValor_IPI DECIMAL(14,3);
    DECLARE xValor_Despesas_Acessorias DECIMAL(14,2);
    DECLARE xFlag_Calcular_ICMS_Frete VARCHAR(1);
    DECLARE xFlag_Calcular_ICMS_IPI VARCHAR(1);
    DECLARE xFlag_Calcular_ICMS_Seguro VARCHAR(1);
    DECLARE xFlag_Calcular_ICMS_Despesas_Acessorias VARCHAR(1);
    DECLARE xFlag_Arredondar_Valor_IPI VARCHAR(1);
    DECLARE xFlag_Calcular_IPI_Frete VARCHAR(1);
    DECLARE xFlag_Calcular_IPI_Seguro VARCHAR(1);
    DECLARE xFlag_Calcular_IPI_Despesas_Acessorias VARCHAR(1);
    DECLARE xFlag_Venda_Compra_Outros VARCHAR(1);
    DECLARE xFlag_Credita_IPI VARCHAR(1);
    DECLARE xFlag_Substituicao_Tributaria VARCHAR(1);
    DECLARE xC004_Id INT(11);
    DECLARE xValor_Adicional_Base_ICMS DECIMAL(10,2);
    DECLARE xValor_Adicional_Base_IPI DECIMAL(10,2);
    DECLARE xValor_Desconto_Adicional DECIMAL(14,2);
    DECLARE xValor_Frete_Adicional DECIMAL(14,3);
    DECLARE xValor_Substituicao_Adicional DECIMAL(14,3);    
    DECLARE xValor_Seguro_Adicional DECIMAL(14,2);
    DECLARE xValor_Despesas_Acessorias_Adicional DECIMAL(14,2);
    DECLARE xPercentual_Reducao_ICMS DECIMAL(6,2);
    DECLARE xPercentual_Reducao_IPI DECIMAL(6,2);
    DECLARE xValor_Base_ICMS DECIMAL(10,2);
    DECLARE xValor_Base_IPI DECIMAL(10,2);
    DECLARE xAliquota_ICMS DECIMAL(5,2);
    DECLARE xAliquota_IPI DECIMAL(5,2);
    DECLARE xValor_Total_Desconto DECIMAL(10,5);
    DECLARE xValor_Total_Desp_Nota_Origem DECIMAL(20,2);
    DECLARE xValor_ICMS DECIMAL(12,5);
    DECLARE xValor_ICMS_Substituicao DECIMAL(12,5);
    DECLARE xValor_ICMS_Diferenca_Aliquotas DECIMAL(12,5);
    DECLARE xTotal_Produtos_ST DECIMAL(12,5);
    DECLARE xDecimais_Valor_ICMS INT(1);
    DECLARE xQuantidade DECIMAL(10,2);
    DECLARE xQuantidade_Total DECIMAL(10,2);
    DECLARE xFlag_Devolucao VARCHAR(1);
    DECLARE xSimples INT(1) DEFAULT 0;
    DECLARE xD006_Flag_Deb_Crd_ICMS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Substituicao_Tributaria VARCHAR(1) DEFAULT 'S';
    DECLARE xFlag_Calcular enum('S','N') default 'S';
    DECLARE xForma_Rateio_Frete VARCHAR(1);
    DECLARE xValor_Total_Custo_Frete DECIMAL(10,2);
    DECLARE xQuantidade_Frete DECIMAL(10,2);
    DECLARE xT014_Id_Duplicado INT(11);
    SET xDecimais_Valor_ICMS=2;
    SELECT T014_T013_Id,
           T014_Percentual_Reducao_ICMS,
           T014_Percentual_Reducao_IPI,
           T014_Aliquota_ICMS,
           T014_Aliquota_IPI,
           T014_Valor_Custo_Unitario,
           T014_Quantidade*T014_Valor_Custo_Unitario,
           T014_Quantidade*T014_Valor_Desconto_Unitario,
           T014_Quantidade*T014_Valor_Unitario_Desp_Nota_Origem,
           D006_Flag_Substituicao_Tributaria,
           T014_Quantidade,
           T014_Id_Duplicado
      INTO xT013_Id,
           xPercentual_Reducao_ICMS,
           xPercentual_Reducao_IPI,
           xAliquota_ICMS,
           xAliquota_IPI,
           xValor_Custo_Unitario,
           xValor_Total_Custo,
           xValor_Total_Desconto,
           xValor_Total_Desp_Nota_Origem,
           xD006_Flag_Substituicao_Tributaria,
           xQuantidade,
           xT014_Id_Duplicado
      FROM T014
      LEFT JOIN D006 ON D006_Id=T014_D006_Id
     WHERE T014_Id=xT014_Id;

    SELECT sum(T014_Valor_Total_Custo)
      INTO xTotal_Produtos_ST
      FROM T014
      LEFT JOIN D006 ON D006_Id=T014_D006_Id
     WHERE T014_T013_Id=xT013_Id
       AND D006_Flag_Substituicao_Tributaria='S';

    SELECT SUM(T014_Quantidade)
      INTO xQuantidade_Total
      FROM T014
     WHERE T014_T013_Id=xT013_Id;

   SELECT sum(T014_Valor_Total_Custo) 
      INTO xValor_Total_Preco
      FROM T014 
      WHERE T014_T013_Id = xT013_Id;

    SELECT T013_Valor_Total_Produtos+T013_Valor_Desconto_Produtos,
           T013_Valor_Desconto_Produtos+T013_Valor_Desconto,
           T013_Valor_Frete,
           T013_Valor_Seguro,
           T013_Valor_Despesas_Acessorias,
           T013_Flag_Calcular_ICMS_Frete,
           T013_Flag_Calcular_ICMS_IPI,
           T013_Flag_Calcular_ICMS_Seguro,
           T013_Flag_Calcular_ICMS_Despesas_Acessorias,
           T013_Flag_Calcular_IPI_Frete,
           T013_Flag_Calcular_IPI_Seguro,
           T013_Flag_Calcular_IPI_Despesas_Acessorias,
           T013_Flag_Arredondar_Valor_IPI,
           T013_Casas_Decimais_Valor_ICMS,
           D006_Flag_Venda_Compra_Outros,
           D006_Flag_Credita_IPI,
           D006_Flag_Deb_Crd_ICMS,
           D006_Flag_Substituicao_Tributaria,
           T013_C004_Id,
           T013_Flag_Devolucao,
           T013_Valor_ICMS_Substituicao,
           T013_Valor_ICMS_Diferenca_Aliquotas,
           C004_Flag_Empresa_Simples,
           T013_Flag_Calcular,
           T013_Flag_Rateio_Frete
      INTO xValor_Total_Produtos,
           xValor_Desconto_Produtos,
           xValor_Frete,
           xValor_Seguro,
           xValor_Despesas_Acessorias,
           xFlag_Calcular_ICMS_Frete,
           xFlag_Calcular_ICMS_IPI,
           xFlag_Calcular_ICMS_Seguro,
           xFlag_Calcular_ICMS_Despesas_Acessorias,
           xFlag_Calcular_IPI_Frete,
           xFlag_Calcular_IPI_Seguro,
           xFlag_Calcular_IPI_Despesas_Acessorias,
           xFlag_Arredondar_Valor_IPI,
           xDecimais_Valor_ICMS,
           xFlag_Venda_Compra_Outros,
           xFlag_Credita_IPI,
           xD006_Flag_Deb_Crd_ICMS,
           xFlag_Substituicao_Tributaria,
           xC004_Id,
           xFlag_Devolucao,
           xValor_ICMS_Substituicao,
           xValor_ICMS_Diferenca_Aliquotas,
           xSimples,
           xFlag_Calcular,
           xForma_Rateio_Frete
      FROM T013
 LEFT JOIN D006 on D006_Id=T013_D006_Id
 LEFT JOIN C004 on C004_Id=T013_C004_Id
    WHERE T013_Id=xT013_Id;

    IF xValor_ICMS_Substituicao>0 and xD006_Flag_Substituicao_Tributaria='S' THEN
       SET xValor_Substituicao_Adicional = round((xValor_ICMS_Substituicao / xTotal_Produtos_ST) * xValor_Total_Custo,3);
    ELSE
       SET xValor_Substituicao_Adicional = 0;
    END IF;
    IF xValor_Desconto_Produtos>0 THEN
       SET xValor_Total_Desconto= round((xValor_Desconto_Produtos / xValor_Total_Produtos) * xValor_Total_Custo,5);
    ELSE
       SET xValor_Total_Desconto = 0;
    END IF;
    IF xValor_ICMS_Diferenca_Aliquotas>0 THEN
       SET xValor_ICMS_Diferenca_Aliquotas = round((xValor_ICMS_Diferenca_Aliquotas / xValor_Total_Produtos) * xValor_Total_Custo,5);
    ELSE
       SET xValor_ICMS_Diferenca_Aliquotas = 0;
    END IF;
    SET xValor_Desconto_Adicional= xValor_Total_Desconto / xQuantidade;
   IF xValor_Frete>0 THEN
      SELECT SUM(T014_Quantidade) ,
             SUM(T014_Quantidade * T014_Valor_Custo_Unitario)
        INTO xQuantidade_Frete,
             xValor_Total_Custo_Frete
      FROM T014 
      WHERE T014_Id=xT014_Id OR T014_Id_Duplicado=xT014_Id OR T014_Id = xT014_Id_Duplicado OR T014_Id_Duplicado = xT014_Id_Duplicado;
             
      IF xForma_Rateio_Frete = '1' THEN
         SET xValor_Frete_Adicional = round((xValor_Frete / xValor_Total_Preco) * xValor_Total_Custo_Frete,3);
      ELSEIF xForma_Rateio_Frete = '2' THEN
         SET xValor_Frete_Adicional = round((xValor_Frete / xQuantidade_Total) * xQuantidade_Frete,3);
      END IF;
   ELSE
      SET xValor_Frete_Adicional = 0;
   END IF;
    IF xValor_Seguro>0 THEN
       SET xValor_Seguro_Adicional = round((xValor_Seguro / xValor_Total_Preco) * xValor_Total_Custo,2);
    ELSE
       SET xValor_Seguro_Adicional = 0;
    END IF;
    IF xValor_Despesas_Acessorias>0 THEN
       SET xValor_Despesas_Acessorias_Adicional = round((xValor_Despesas_Acessorias / xValor_Total_Preco) * xValor_Total_Custo,2);
    ELSE
       SET xValor_Despesas_Acessorias_Adicional = 0;
    END IF;
    SET xValor_Adicional_Base_ICMS = 0 - xValor_Total_Desconto;
    IF xFlag_Calcular_ICMS_Frete = 'S' THEN
       SET xValor_Adicional_Base_ICMS = xValor_Frete_Adicional;
    END IF;
    IF xFlag_Calcular_ICMS_Seguro='S' THEN
       SET xValor_Adicional_Base_ICMS = xValor_Adicional_Base_ICMS + xValor_Seguro_Adicional;
    END IF;
    IF xFlag_Calcular_ICMS_Despesas_Acessorias='S' THEN
       SET xValor_Adicional_Base_ICMS = xValor_Adicional_Base_ICMS + xValor_Despesas_Acessorias_Adicional;
    END IF;
    SET xValor_Adicional_Base_IPI = 0;
    IF xFlag_Calcular_IPI_Frete='S' THEN
       SET xValor_Adicional_Base_IPI = xValor_Frete_Adicional;
    END IF;
    IF xFlag_Calcular_IPI_Seguro='S' THEN
       SET xValor_Adicional_Base_IPI = xValor_Adicional_Base_IPI + xValor_Seguro_Adicional;
    END IF;
    IF xFlag_Calcular_IPI_Despesas_Acessorias='S' THEN
       SET xValor_Adicional_Base_IPI = xValor_Adicional_Base_IPI + xValor_Despesas_Acessorias_Adicional;
    END IF;
    IF xAliquota_IPI>0 THEN
       SET xValor_Base_IPI = (xValor_Total_Custo + xValor_Adicional_Base_IPI);
       IF xFlag_Devolucao = 'S' THEN
          SET xValor_Base_IPI = xValor_Base_IPI + xValor_Total_Desp_Nota_Origem;
       END IF;
    ELSE
       SET xValor_Base_IPI = 0;
    END IF;
    IF xPercentual_Reducao_IPI>0 and xAliquota_IPI>0 THEN
       SET xValor_Base_IPI = xValor_Base_IPI  * ( 100 - xPercentual_Reducao_IPI / 100 );
    END IF;
    SET xValor_IPI=if(xAliquota_IPI>0,xValor_Base_IPI*(xAliquota_IPI/100),0);
    IF xFlag_Arredondar_Valor_IPI='S' THEN
       SET xValor_IPI = ROUND(xValor_IPI,2);
    ELSE
       SET xValor_IPI = TRUNCATE(xValor_IPI,2);
    END IF;
    IF xFlag_Calcular_ICMS_IPI='S' THEN
       SET xValor_Adicional_Base_ICMS = xValor_Adicional_Base_ICMS + xValor_IPI;
    END IF;
    IF xAliquota_ICMS>0 THEN
       SET xValor_Base_ICMS = (xValor_Total_Custo + xValor_Adicional_Base_ICMS);
    ELSE
       SET xValor_Base_ICMS = 0;
    END IF;
    IF xPercentual_Reducao_ICMS>0 and xAliquota_ICMS>0 THEN
       SET xValor_Base_ICMS = (xValor_Total_Custo + xValor_Adicional_Base_ICMS)  * ((100 - xPercentual_Reducao_ICMS) / 100 );
    END IF;
    SET xValor_ICMS = 0;
    IF xAliquota_ICMS>0 THEN
       SET xValor_ICMS = round(xValor_Base_ICMS*(xAliquota_ICMS/100),5);
       IF xDecimais_Valor_ICMS = 2 THEN
          SET xValor_ICMS = round(xValor_ICMS,2);
       ELSEIF xDecimais_Valor_ICMS = 3 THEN
          SET xValor_ICMS = round(xValor_ICMS,3);
       ELSEIF xDecimais_Valor_ICMS = 4 THEN
          SET xValor_ICMS = round(xValor_ICMS,4);
       ELSEIF xDecimais_Valor_ICMS = 5 THEN
          SET xValor_ICMS = round(xValor_ICMS,5);
       END IF;
    END IF;
    
    if xFlag_Calcular='S' THEN
    # T014_Valor_ICMS_Substituicao    = xValor_Substituicao_Adicional,
    # T014_Valor_ICMS_Substituicao_Unitario = T014_Valor_ICMS_Substituicao / T014_Quantidade,

    # T014_Valor_Custo_Final_Unitario = ((T014_Valor_Total_Custo + T014_Valor_ICMS_Substituicao + T014_Valor_Frete + if(xFlag_Credita_IPI!='S',T014_Valor_IPI,0) + T014_Valor_Seguro + T014_Valor_Despesas_Acessorias - if(xSimples!=1 and xD006_Flag_Deb_Crd_ICMS='S',T014_Valor_ICMS,0) ) - T014_Valor_Total_Desconto)/T014_Quantidade,
    # T014_Valor_Total_Custo_Final    = T014_Valor_Custo_Final_Unitario * T014_Quantidade
      UPDATE T014 LEFT JOIN T013 ON T013_Id=T014_T013_Id
                SET T014_Valor_Total_Custo          = xValor_Total_Custo,
                    T014_Valor_Desconto_Unitario    = xValor_Desconto_Adicional,
                    T014_Valor_Total_Desconto       = xValor_Total_Desconto,
                    T014_Valor_Frete                = xValor_Frete_Adicional,
                    T014_Valor_ICMS_Diferenca_Aliquotas = xValor_ICMS_Diferenca_Aliquotas,
                    T014_Valor_Seguro               = xValor_Seguro_Adicional,
                    T014_Valor_Despesas_Acessorias  = xValor_Despesas_Acessorias_Adicional,
                    T014_Valor_Base_ICMS            = xValor_Base_ICMS,
                    T014_Valor_Base_IPI             = xValor_Base_IPI,
                    T014_Valor_ICMS                 = xValor_ICMS,
                    T014_Valor_IPI                  = xValor_IPI,
                    T014_Valor_Total_Desp_Nota_Origem = xValor_Total_Desp_Nota_Origem
        WHERE T014_Id=xT014_Id and T013_Flag_Travar_Nota!='S';
  
  ELSE
  
    # T014_Valor_Custo_Final_Unitario = ((T014_Valor_Total_Custo + T014_Valor_ICMS_Substituicao + T014_Valor_Frete + if(xFlag_Credita_IPI!='S',T014_Valor_IPI,0) + T014_Valor_Seguro + T014_Valor_Despesas_Acessorias - if(xSimples!=1 and xD006_Flag_Deb_Crd_ICMS='S',T014_Valor_ICMS,0)) - T014_Valor_Total_Desconto)/T014_Quantidade,
    # T014_Valor_Total_Custo_Final    = T014_Valor_Custo_Final_Unitario * T014_Quantidade

      SELECT (SELECT SUM(T014_Quantidade) FROM T014 T014B WHERE T014B.T014_Id=T014A.T014_Id OR T014B.T014_Id_Duplicado=T014A.T014_Id OR T014B.T014_Id=T014A.T014_Id_Duplicado OR T014B.T014_Id_Duplicado=T014A.T014_Id_Duplicado) into xQuantidade
        FROM T014 as T014A
        WHERE T014_Id=xT014_Id;

      UPDATE T014 AS T014A LEFT JOIN T013 ON T013_Id=T014_T013_Id
                SET T014_Valor_Total_Custo          = xValor_Total_Custo,
                    T014_Valor_Frete                = xValor_Frete_Adicional,
                    T014_Valor_Seguro               = xValor_Seguro_Adicional,
                    T014_Valor_Despesas_Acessorias  = xValor_Despesas_Acessorias_Adicional,
                    T014_Valor_ICMS_Diferenca_Aliquotas = xValor_ICMS_Diferenca_Aliquotas,
                    T014_Valor_Total_Desp_Nota_Origem = xValor_Total_Desp_Nota_Origem,
                    T014_Valor_ICMS_Substituicao_Unitario = (T014_Valor_ICMS_Substituicao / T014_Quantidade),
                    T014_Valor_Desconto_Unitario = T014_Valor_Total_Desconto / xQuantidade
        WHERE T014_Id=xT014_Id and T013_Flag_Travar_Nota!='S';
  
  END IF; 

END;


