CREATE PROCEDURE `T055_Gravar_Totalizacao`(xT055_Id int(11))
BEGIN
    DECLARE xT075_Valor_SISCOMEX DECIMAL(16,6);
    DECLARE xT075_Valor_Imposto_Importacao DECIMAL(16,6);
    DECLARE xT075_Valor_IPI DECIMAL(16,6);
    DECLARE xT075_Valor_PIS DECIMAL(16,6);
    DECLARE xT075_Valor_COFINS DECIMAL(16,6);
    DECLARE xT075_Valor_ICMS DECIMAL(16,6);
    DECLARE xT075_Valor_THC DECIMAL(16,6);
    DECLARE xT075_Valor_Frete_Incluso DECIMAL(16,6);
    DECLARE xT075_Valor_Frete DECIMAL(16,6);
    DECLARE xT055_Valor_Frete DECIMAL(16,6);
    DECLARE xT075_Valor_Seguro DECIMAL(16,6);
    DECLARE xT075_Valor_Despesa DECIMAL(16,6);
    DECLARE xT075_Valor_Total_Custo DECIMAL(16,6);
    DECLARE xT075_Valor_VMLE DECIMAL(16,6);
    DECLARE xT055_Valor_Total_Custo DECIMAL(16,6);
    DECLARE xPeso_Total DECIMAL(16,6);
    DECLARE xPeso_Item DECIMAL(16,6);
    DECLARE xT075_Id INT(10);
    DECLARE xValor_Base_IPI DECIMAL(19,6);
    DECLARE xValor_Base_II DECIMAL(19,6);
    DECLARE xValor_Base_PIS_COFINS DECIMAL(19,6);
    DECLARE xValor_Base_ICMS DECIMAL(19,6);
    DECLARE xValor_Despesas_Acessorias DECIMAL(19,4) DEFAULT 0;
    DECLARE xValor_Despesas_Aduaneiras DECIMAL(19,4) DEFAULT 0;
    DECLARE xT075_Cotacao_ME DECIMAL(10,4);
    DECLARE xFlag_Frete VARCHAR(1);
    DECLARE xT075_Valor_AFRMM DECIMAL(16,6);
    DECLARE xT055_Flag_Calculo_Manual CHAR(1);
    DECLARE xT075_Somar_PIS_Custo CHAR(1);
    DECLARE xT075_Somar_COFINS_Custo CHAR(1);
    DECLARE xT075_Somar_Seguro_Custo CHAR(1);
    DECLARE xT075_Somar_AFRMM_Custo CHAR(1);
    DECLARE xT075_Somar_ICMS_Pagar_Custo CHAR(1);
    DECLARE xT075_Somar_IPI_Custo CHAR(1);
    DECLARE xT075_Calculo_THC CHAR(1);
    DECLARE xT055_Valor_Total_Custo_CC DECIMAL(16,6);
    DECLARE xT075_Cotacao_CC DECIMAL(10,4);
    DECLARE xAliquota_ICMS DECIMAL(5,2);
    DECLARE xAliquota_ICMS_Normal DECIMAL(5,2);
    DECLARE xAliquota_ICMS_Presumido DECIMAL(5,2);
    DECLARE xAliquota_ICMS_PIS_COFINS DECIMAL(5,2);
    DECLARE xPercentual_Reducao_ICMS DECIMAL(19,4);
    DECLARE xPercentual_Reducao_ICMS_Presumido DECIMAL(19,4);
    DECLARE xT075_Modelo_Calculos CHAR(1);
    DECLARE xValor_Total_Custo_DI DECIMAL(19,6);
    DECLARE xT168_Flag_Soma_Base_IPI CHAR(1);
    DECLARE xT168_Flag_Soma_Base_ICMS CHAR(1);
    DECLARE xT168_Flag_Soma_Base_PIS CHAR(1);
    DECLARE xT168_Flag_Soma_Base_COFINS CHAR(1);
    DECLARE xT168_Flag_Soma_Base_II CHAR(1);
    DECLARE xT168_Flag_Soma_Custo CHAR(1);
    DECLARE xT168_Flag_Soma_Preco_NFe CHAR(1);
    DECLARE xT168_Tipo_Despesa CHAR(3);
    DECLARE xT168_Tipo CHAR(3);
    DECLARE xT168_Valor DECIMAL(16,6);
    DECLARE xT168_Valor_Confirmado DECIMAL(16,6);
    DECLARE xT168_Flag_ME CHAR(1);
    DECLARE xT168_Cotacao_ME DECIMAL(10,4);
    DECLARE xValor_Despesas DECIMAL(16,6);
    DECLARE xT168_Flag_Tipo_Rateio CHAR(1);
    DECLARE xT168_Flag_Base_Rateio CHAR(3);
    DECLARE xValor_Total_Custo_Produto DECIMAL(19,6);
    DECLARE xValor_Despesas_Preco_NFe DECIMAL(19,6);
    DECLARE xAdicao varchar(10);
    DECLARE xValor_ME_Adicao DECIMAL(19,6);
    DECLARE xPeso_Adicao DECIMAL(19,6);
    DECLARE xFimLoop INT DEFAULT 0;
    DECLARE xCursor CURSOR FOR SELECT T168_Flag_Soma_Base_IPI, 
                                      T168_Flag_Soma_Base_ICMS,
                                      T168_Flag_Soma_Base_PIS,
                                      T168_Flag_Soma_Base_COFINS,
                                      T168_Flag_Soma_Base_II,
                                      T168_Flag_Soma_Custo,
                                      T168_Flag_Soma_Preco_NFe,
                                      T168_Tipo_Despesa,
                                      T168_Tipo,
                                      T168_Valor_Confirmado,
                                      T168_Valor,
                                      T168_Flag_ME,
                                      T168_Cotacao_ME,
                                      T168_Flag_Tipo_Rateio,
                                      T168_Flag_Base_Rateio
                                 FROM T168
                                WHERE T168_T075_Id = (SELECT T055_T075_Id FROM T055 WHERE T055_Id = xT055_Id);
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET xFimLoop = 1;
    
    SELECT T055_T075_Id,
           T055_Valor_Custo_Unitario * T055_Quantidade,
           T055_Peso_Kg_Liquido_Total,
           T055_Flag_Frete,
           T055_Flag_Calculo_Manual,
           (T055_Aliquota_ICMS/100),
           (100-T055_Aliquota_ICMS),
           (T055_Aliquota_ICMS_Presumido/100),
           if(T055_Percentual_Reducao_ICMS>0,(T055_Percentual_Reducao_ICMS/100),1),
           if(ifnull(T055_Percentual_Reducao_ICMS_Presumido,0)>0,(T055_Percentual_Reducao_ICMS_Presumido/100),0),
           (T055_Aliquota_ICMS_PIS_COFINS/100),
           T055_Valor_Adicao
      INTO xT075_Id,
           xT055_Valor_Total_Custo,
           xPeso_Item,
           xFlag_Frete,
           xT055_Flag_Calculo_Manual,
           xAliquota_ICMS,
           xAliquota_ICMS_Normal,
           xAliquota_ICMS_Presumido,
           xPercentual_Reducao_ICMS,
           xPercentual_Reducao_ICMS_Presumido,
           xAliquota_ICMS_PIS_COFINS,
           xAdicao
      FROM T055
     WHERE T055_Id=xT055_Id;

    SELECT sum(T055_Peso_Kg_Liquido_Total),
           sum(T055_Valor_Total_Custo)
      INTO xPeso_Adicao,
           xValor_ME_Adicao 
      FROM T055
     WHERE T055_T075_Id=xT075_Id
       AND T055_Valor_Adicao = xAdicao;

    SELECT sum(T055_Peso_Kg_Liquido_Total)
      INTO xPeso_Total
      FROM T055
     WHERE T055_T075_Id=xT075_Id
       AND T055_Flag_Frete = 'S';
       
    SELECT T075_Valor_SISCOMEX,
           T075_Valor_Imposto_Importacao,
           T075_Valor_IPI,
           T075_Valor_PIS,
           T075_Valor_Cofins,
           T075_Valor_ICMS,
           T075_Valor_THC,
           T075_Valor_Frete_Incluso,
           T075_Valor_Frete,
           T075_Valor_Seguro,
           T075_Valor_AFRMM,
           T075_Valor_VMLE,
           T075_Cotacao_ME,
           T075_Somar_PIS_Custo,
           T075_Somar_COFINS_Custo,
           T075_Somar_Seguro_Custo,
           T075_Somar_AFRMM_Custo,
           T075_Somar_ICMS_Pagar_Custo,
           T075_Somar_IPI_Custo,
           T075_Calculo_THC,
           IFNULL(T075_Cotacao_CC,0),
           T075_Modelo_Calculos
      INTO xT075_Valor_SISCOMEX,
           xT075_Valor_Imposto_Importacao,
           xT075_Valor_IPI,
           xT075_Valor_PIS,
           xT075_Valor_Cofins,
           xT075_Valor_ICMS,
           xT075_Valor_THC,
           xT075_Valor_Frete_Incluso,
           xT075_Valor_Frete,
           xT075_Valor_Seguro,
           xT075_Valor_AFRMM,
           xT075_Valor_VMLE,
           xT075_Cotacao_ME,
           xT075_Somar_PIS_Custo,
           xT075_Somar_COFINS_Custo,
           xT075_Somar_Seguro_Custo,
           xT075_Somar_AFRMM_Custo,
           xT075_Somar_ICMS_Pagar_Custo,
           xT075_Somar_IPI_Custo,
           xT075_Calculo_THC,
           xT075_Cotacao_CC,
           xT075_Modelo_Calculos
      FROM T075
     WHERE T075_Id=xT075_Id;
    
    SET xT055_Valor_Total_Custo_CC = xT055_Valor_Total_Custo * IF(xT075_Cotacao_CC>0,xT075_Cotacao_CC,xT075_Cotacao_ME);
    SET xT055_Valor_Total_Custo    = xT055_Valor_Total_Custo * xT075_Cotacao_ME;
    SET xValor_ME_Adicao           = xValor_ME_Adicao * xT075_Cotacao_ME;
    SET xT075_Valor_VMLE           = xT075_Valor_VMLE * xT075_Cotacao_ME;

    IF xT075_Modelo_Calculos = '1' THEN

        SELECT IF(SUM(T168_Valor)=0 OR SUM(T168_Valor) IS NULL,0,SUM(T168_Valor)) 
          INTO xT075_Valor_Despesa
          FROM T168
         WHERE T168_T075_Id=xT075_Id;

        SET xT075_Valor_Frete  = xT075_Valor_Frete * xT075_Cotacao_ME;
        SET xT075_Valor_Seguro = xT075_Valor_Seguro * xT075_Cotacao_ME;

        IF xFlag_Frete = 'S' THEN
          SET xT055_Valor_Frete      = (xT075_Valor_Frete)*(xPeso_Item/xPeso_Total);
        ELSE
          SET xT055_Valor_Frete      = 0;
        END IF;
        
        IF xT075_Calculo_THC = '1' THEN
          SET xT075_Valor_THC        = (xT075_Valor_THC)*(xT055_Valor_Total_Custo/xT075_Valor_VMLE);
        ELSE
          SET xT075_Valor_THC        = (xT075_Valor_THC)*(xPeso_Item/xPeso_Total);
        END IF;

        SET xT075_Valor_Seguro           = (xT075_Valor_Seguro)*(xT055_Valor_Total_Custo/xT075_Valor_VMLE);
        SET xT075_Valor_SISCOMEX         = (xT075_Valor_SISCOMEX)*(xT055_Valor_Total_Custo/xT075_Valor_VMLE);
        SET xValor_Despesas_Aduaneiras   = xT075_Valor_SISCOMEX;

        SET xT075_Valor_Despesa          = (xT075_Valor_Despesa)*(xT055_Valor_Total_Custo/xT075_Valor_VMLE);
        SET xT075_Valor_AFRMM            = (xT075_Valor_AFRMM)*(xT055_Valor_Total_Custo/xT075_Valor_VMLE);

        SET xValor_Base_II               = xT075_Valor_Seguro + xT075_Valor_THC + xT055_Valor_Frete;    
        SET xValor_Base_IPI              = xT075_Valor_Seguro + xT075_Valor_THC + xT055_Valor_Frete;   
        SET xValor_Base_PIS_COFINS       = xT075_Valor_Seguro + xT075_Valor_THC + xT055_Valor_Frete;
        SET xValor_Base_ICMS             = xT075_Valor_Seguro + xT075_Valor_THC + xT055_Valor_Frete + xT075_Valor_SISCOMEX;  

    ELSE
               
        SET xValor_Base_IPI            = 0;
        SET xValor_Base_ICMS           = 0;
        SET xValor_Base_PIS_COFINS     = 0;
        SET xValor_Base_II             = 0;
        SET xT075_Valor_Seguro         = 0;
        SET xT075_Valor_SISCOMEX       = 0;
        SET xT075_Valor_Despesa        = 0;
        SET xT075_Valor_AFRMM          = 0;
        SET xT075_Valor_THC            = 0;
        SET xT055_Valor_Frete          = 0;
        SET xValor_Total_Custo_Produto = 0;
        SET xValor_Despesas_Preco_NFe   = 0;
        
        OPEN xCursor;

          the_loop: LOOP

            FETCH xCursor INTO xT168_Flag_Soma_Base_IPI, 
                               xT168_Flag_Soma_Base_ICMS, 
                               xT168_Flag_Soma_Base_PIS, 
                               xT168_Flag_Soma_Base_COFINS, 
                               xT168_Flag_Soma_Base_II, 
                               xT168_Flag_Soma_Custo, 
                               xT168_Flag_Soma_Preco_NFe, 
                               xT168_Tipo_Despesa, 
                               xT168_Tipo,
                               xT168_Valor_Confirmado,
                               xT168_Valor, 
                               xT168_Flag_ME,
                               xT168_Cotacao_ME,
                               xT168_Flag_Tipo_Rateio,
                               xT168_Flag_Base_Rateio;

            IF xFimLoop = 1 THEN
              LEAVE the_loop;
            END IF;

            IF xT168_Flag_Base_Rateio = '1' THEN
                IF xT168_Flag_Tipo_Rateio = 'C' THEN
                    SET xValor_Despesas = IF(xT168_Valor_Confirmado > 0, xT168_Valor_Confirmado, IF(xT168_Flag_ME = 'S', xT168_Valor * xT168_Cotacao_ME, xT168_Valor)) * (xT055_Valor_Total_Custo/xT075_Valor_VMLE);
                ELSE
                    SET xValor_Despesas = IF(xT168_Valor_Confirmado > 0, xT168_Valor_Confirmado, IF(xT168_Flag_ME = 'S', xT168_Valor * xT168_Cotacao_ME, xT168_Valor)) * (xPeso_Item/xPeso_Total);
                END IF;
            END IF;
            IF xT168_Flag_Base_Rateio = '2' THEN
                IF xT168_Flag_Tipo_Rateio = 'C' THEN
                    SET xValor_Despesas = (IF(xT168_Valor_Confirmado > 0, xT168_Valor_Confirmado, IF(xT168_Flag_ME = 'S', xT168_Valor * xT168_Cotacao_ME, xT168_Valor))*(xValor_ME_Adicao/xT075_Valor_VMLE)) * (xT055_Valor_Total_Custo/xValor_ME_Adicao);
                ELSE
                    SET xValor_Despesas = (IF(xT168_Valor_Confirmado > 0, xT168_Valor_Confirmado, IF(xT168_Flag_ME = 'S', xT168_Valor * xT168_Cotacao_ME, xT168_Valor))*(xValor_ME_Adicao/xT075_Valor_VMLE)) * (xPeso_Item/xPeso_Adicao);
                END IF;
            END IF;
            IF xT168_Flag_Base_Rateio = '3' THEN
                IF xT168_Flag_Tipo_Rateio = 'C' THEN
                    SET xValor_Despesas = (IF(xT168_Valor_Confirmado > 0, xT168_Valor_Confirmado, IF(xT168_Flag_ME = 'S', xT168_Valor * xT168_Cotacao_ME, xT168_Valor))*(xPeso_Adicao/xPeso_Total)) * (xT055_Valor_Total_Custo/xValor_ME_Adicao);
                ELSE
                    SET xValor_Despesas = (IF(xT168_Valor_Confirmado > 0, xT168_Valor_Confirmado, IF(xT168_Flag_ME = 'S', xT168_Valor * xT168_Cotacao_ME, xT168_Valor))*(xPeso_Adicao/xPeso_Total)) * (xPeso_Item/xPeso_Adicao);
                END IF;
            END IF;

            IF xT168_Flag_Soma_Base_IPI = 'S' THEN
                SET xValor_Base_IPI = xValor_Base_IPI + xValor_Despesas;
            END IF;

            IF xT168_Flag_Soma_Base_ICMS = 'S' THEN
                SET xValor_Base_ICMS = xValor_Base_ICMS + xValor_Despesas;
            END IF;

            IF xT168_Flag_Soma_Base_COFINS = 'S' OR xT168_Flag_Soma_Base_PIS = 'S' THEN
                SET xValor_Base_PIS_COFINS = xValor_Base_PIS_COFINS + xValor_Despesas;
            END IF;

            IF xT168_Flag_Soma_Base_II = 'S' THEN
                SET xValor_Base_II = xValor_Base_II + xValor_Despesas;
            END IF;

            IF xT168_Flag_Soma_Preco_NFe = 'S' THEN
                SET xValor_Despesas_Preco_NFe = xValor_Despesas_Preco_NFe + xValor_Despesas;
            END IF;
            
            IF xT168_Flag_Soma_Custo = 'S' THEN
                SET xValor_Total_Custo_Produto = xValor_Total_Custo_Produto + xValor_Despesas;
            END IF;

            IF xT168_Tipo_Despesa = '2' THEN
                SET xT075_Valor_THC = xT075_Valor_THC + xValor_Despesas;
            END IF;

            IF xT168_Tipo_Despesa = '3' THEN
                SET xT075_Valor_Seguro = xT075_Valor_Seguro + xValor_Despesas;
            END IF;
            
            IF xT168_Tipo_Despesa = '4' THEN
                SET xT075_Valor_SISCOMEX = xT075_Valor_SISCOMEX + xValor_Despesas;
            END IF;

            IF xT168_Tipo_Despesa = '5' THEN
                SET xT075_Valor_AFRMM = xT075_Valor_AFRMM + xValor_Despesas;
            END IF;

            IF xT168_Tipo_Despesa = '6' THEN
                SET xT055_Valor_Frete = xT055_Valor_Frete + xValor_Despesas;
            END IF;

            IF xT168_Tipo = '1' THEN
                SET xT075_Valor_Despesa = xT075_Valor_Despesa + xValor_Despesas;
            END IF;

            IF xT168_Tipo = '2' THEN
                SET xValor_Despesas_Aduaneiras = xValor_Despesas_Aduaneiras + xValor_Despesas;
            END IF;

            IF xT168_Tipo = '3' THEN
                SET xValor_Despesas_Acessorias = xValor_Despesas_Acessorias + xValor_Despesas;
            END IF;

          END LOOP the_loop;
        CLOSE xCursor;

    END IF;
      
    IF XT055_Flag_Calculo_Manual = 'N' THEN
        UPDATE T055 SET T055_Valor_Total_Custo                   = T055_Valor_Custo_Unitario * T055_Quantidade,
                        T055_Valor_Total_Despesas_Aduaneira      = xValor_Despesas_Aduaneiras,
                        T055_Valor_Despesas_Aduaneira            = T055_Valor_Total_Despesas_Aduaneira / T055_Quantidade,
                        T055_Valor_Total_Despesas                = xT075_Valor_Despesa,
                        T055_Valor_Despesas                      = T055_Valor_Total_Despesas / T055_Quantidade,
                        T055_Valor_Total_Frete                   = xT055_Valor_Frete,
                        T055_Valor_Frete                         = T055_Valor_Total_Frete  / T055_Quantidade,
                        T055_Valor_Total_Seguro                  = xT075_Valor_Seguro,
                        T055_Valor_Seguro                        = T055_Valor_Total_Seguro  / T055_Quantidade,
                        T055_Valor_Total_Aduaneira               = xT055_Valor_Total_Custo+T055_Valor_Total_Frete+T055_Valor_Total_Despesas_Aduaneira,
                        T055_Valor_Aduaneira                     = xT075_Valor_THC,
                        T055_Valor_Total_AFRMM                   = xT075_Valor_AFRMM,
                        T055_Valor_AFRMM                         = T055_Valor_Total_AFRMM / T055_Quantidade,
                        T055_Valor_Total_Siscomex                = xT075_Valor_Siscomex,
                        T055_Valor_Siscomex                      = T055_Valor_Total_Siscomex / T055_Quantidade,
                        T055_Valor_Total_THC                     = xT075_Valor_THC,
                        T055_Valor_THC                           = T055_Valor_Total_THC / T055_Quantidade,
                        T055_Valor_Base_Imposto_Importacao       = xT055_Valor_Total_Custo+xValor_Base_II,
                        T055_Valor_Imposto_Importacao            = T055_Valor_Base_Imposto_Importacao*(T055_Aliquota_Imposto_Importacao/100),
                        T055_Valor_Base_IPI                      = xT055_Valor_Total_Custo+xValor_Base_IPI+T055_Valor_Imposto_Importacao,
                        T055_Valor_IPI                           = T055_Valor_Base_IPI * ( T055_Aliquota_IPI / 100 ),
                        T055_Multiplicador_X                     = ( 1 + xAliquota_ICMS_PIS_COFINS * ( (T055_Aliquota_Imposto_Importacao/100) + (T055_Aliquota_IPI/100) * ( 1 + (T055_Aliquota_Imposto_Importacao/100) ) ) ) / ( ( 1 - (T055_Aliquota_PIS/100) - (T055_Aliquota_COFINS/100) ) * ( 1 - xAliquota_ICMS_PIS_COFINS ) ),
                        T055_Valor_Base_PIS                      = xT055_Valor_Total_Custo + xValor_Base_PIS_COFINS,
                        T055_Valor_PIS                           = ( T055_Aliquota_PIS/100 ) *  T055_Valor_Base_PIS,
                        T055_Valor_Base_COFINS                   = xT055_Valor_Total_Custo + xValor_Base_PIS_COFINS,
                        T055_Valor_COFINS                        = ( T055_Aliquota_COFINS/100 ) *  T055_Valor_Base_COFINS,
                        T055_Valor_Base_ICMS_Sem_Reducao         = xT055_Valor_Total_Custo + xValor_Base_ICMS + T055_Valor_PIS + T055_Valor_COFINS + T055_Valor_IPI + T055_Valor_Imposto_Importacao + T055_Valor_Total_AFRMM,
                        T055_Valor_Base_ICMS_Normal              = round( ( T055_Valor_Base_ICMS_Sem_Reducao * 100 ) / xAliquota_ICMS_Normal , 2),                                                                                                                           
                        T055_Valor_Base_ICMS_Presumido           = round( ( ( T055_Valor_Base_ICMS_Sem_Reducao * 100 ) / xAliquota_ICMS_Normal ) * IF(xPercentual_Reducao_ICMS_Presumido>0, xPercentual_Reducao_ICMS_Presumido, 1) , 2 ),
                        T055_Valor_ICMS                          = (T055_Valor_Base_ICMS_Normal * xAliquota_ICMS) - ((T055_Valor_Base_ICMS_Normal * xAliquota_ICMS) * xPercentual_Reducao_ICMS_Presumido),
                        T055_Valor_ICMS_Presumido                = T055_Valor_Base_ICMS_Presumido * xAliquota_ICMS_Presumido,
                        T055_Valor_ICMS_Pagar                    = T055_Valor_ICMS - T055_Valor_ICMS_Presumido,
                        T055_Peso_Kg_Liquido_Total               = T055_Peso_Kg_Liquido_Unitario * T055_Quantidade,
                        T055_Peso_Kg_Liquido_Total_Fatura        = T055_Peso_Kg_Liquido_Unitario_Fatura * T055_Quantidade,
                        T055_Peso_Lb_Liquido_Unitario            = T055_Peso_Kg_Liquido_Unitario * 2.2,
                        T055_Peso_Lb_Liquido_Total               = T055_Peso_Lb_Liquido_Unitario * T055_Quantidade,
                        T055_Aliquota_Extra                      = 0.00,
                        T055_Valor_Total_ICMS                    = T055_Valor_ICMS,
                        T055_Valor_Total_ICMS_Presumido          = T055_Valor_ICMS_Presumido,
                        T055_Valor_Total_ICMS_Pagar              = T055_Valor_ICMS_Pagar,
                        T055_Valor_Total_IPI                     = T055_Valor_IPI,
                        T055_Valor_Total_Imposto_Importacao      = T055_Valor_Imposto_Importacao,
                        T055_Valor_Total_COFINS                  = T055_Valor_COFINS,
                        T055_Valor_Total_PIS                     = T055_Valor_PIS,
                        T055_Valor_Total_Extra                   = T055_Valor_Extra,
                        T055_Valor_Total_Base_IPI                = T055_Valor_Base_IPI,
                        T055_Valor_Total_Base_ICMS_Sem_Reducao   = T055_Valor_Base_ICMS_Sem_Reducao,
                        T055_Valor_Total_Base_ICMS_Normal        = T055_Valor_Base_ICMS_Normal,
                        T055_Valor_Total_Base_ICMS_Presumido     = T055_Valor_Base_ICMS_Presumido,
                        T055_Valor_Total_Base_COFINS             = T055_Valor_Base_COFINS,
                        T055_Valor_Total_Base_PIS                = T055_Valor_Base_PIS,
                        T055_Valor_Total_Base_Imposto_Importacao = T055_Valor_Base_Imposto_Importacao,
                        #T055_Valor_Total_Preco_Nota_Fiscal      = xT055_Valor_Total_Custo + xValor_Base_II + T055_Valor_Imposto_Importacao + T055_Valor_PIS + T055_Valor_COFINS,
                        T055_Valor_Total_Preco_Nota_Fiscal       = T055_Valor_Total_Preco_Nota_Fiscal(xT055_Valor_Total_Custo, T055_Valor_Base_Imposto_Importacao, T055_Valor_Base_COFINS, T055_Valor_Base_PIS, T055_Valor_Base_IPI, T055_Valor_Total_Imposto_Importacao, T055_Valor_Total_PIS, T055_Valor_Total_COFINS, T055_Valor_Total_IPI, T055_Valor_Total_Frete, T055_Valor_Total_AFRMM, T055_Valor_Total_THC, T055_Valor_Total_Siscomex, T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal, T055_Valor_Total_Despesas_Aduaneira, T055_Valor_Total_Despesas, xValor_Despesas_Preco_NFe, T055_Valor_Total_Seguro), 
                        T055_Valor_Preco_Unitario_Nota_Fiscal    = T055_Valor_Total_Preco_Nota_Fiscal / T055_Quantidade,
                        T055_Valor_Total_Nota_Fiscal             = T055_Valor_Total_Nota_Fiscal(xT055_Valor_Total_Custo, T055_Valor_Total_Preco_Nota_Fiscal, T055_Valor_Base_Imposto_Importacao, T055_Valor_Base_COFINS, T055_Valor_Base_PIS, T055_Valor_Base_IPI, T055_Valor_Total_Imposto_Importacao, T055_Valor_Total_PIS, T055_Valor_Total_COFINS, T055_Valor_Total_IPI, T055_Valor_Total_Frete, T055_Valor_Total_AFRMM, T055_Valor_Total_THC, T055_Valor_Total_Siscomex, T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal, T055_Valor_Total_Despesas_Aduaneira, T055_Valor_Total_Despesas, T055_Valor_Total_Seguro), 
                        T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal = xValor_Despesas_Acessorias,
                        T055_Valor_Imposto_Importacao            = (( IFNULL(T055_Percentual_Acrescimo_Impostos,0)/100 ) *  T055_Valor_Imposto_Importacao) + T055_Valor_Imposto_Importacao,
                        T055_Valor_IPI                           = (( IFNULL(T055_Percentual_Acrescimo_Impostos,0)/100 ) *  T055_Valor_IPI) + T055_Valor_IPI,
                        T055_Valor_PIS                           = (( IFNULL(T055_Percentual_Acrescimo_Impostos,0)/100 ) *  T055_Valor_PIS) + T055_Valor_PIS,
                        T055_Valor_COFINS                        = (( IFNULL(T055_Percentual_Acrescimo_Impostos,0)/100 ) *  T055_Valor_COFINS) + T055_Valor_COFINS,
                        T055_Valor_Total_Custo_Final             = IF(T055_Cotacao_CC>0,(T055_Valor_Custo_Unitario * T055_Quantidade)*T055_Cotacao_CC,xT055_Valor_Total_Custo_CC) +
                                                                   IF(xT075_Modelo_Calculos = '2', xValor_Total_Custo_Produto,   
                                                                                                   T055_Valor_Total_Despesas_Aduaneira +
                                                                                                   T055_Valor_Aduaneira +
                                                                                                   T055_Valor_Total_Despesas +
                                                                                                   T055_Valor_Total_Frete + 
                                                                                                   IF(xT075_Somar_Seguro_Custo = 'S', xT075_Valor_Seguro, 0) +
                                                                                                   IF(xT075_Somar_AFRMM_Custo = 'S', T055_Valor_Total_AFRMM, 0)) +
                                                                   T055_Valor_Imposto_Importacao +
                                                                   IF(xT075_Somar_PIS_Custo = 'S', T055_Valor_PIS, 0) +
                                                                   IF(xT075_Somar_COFINS_Custo = 'S', T055_Valor_COFINS, 0) +                                                                 
                                                                   IF(xT075_Somar_ICMS_Pagar_Custo = 'S', T055_Valor_ICMS_Pagar, 0) +
                                                                   IF(xT075_Somar_IPI_Custo = 'S', T055_Valor_IPI, 0),
                        T055_Valor_Custo_Final_Unitario          = T055_Valor_Total_Custo_Final / T055_Quantidade
                  WHERE T055_Id=xT055_Id;
    ELSEIF XT055_Flag_Calculo_Manual = 'I' THEN
        UPDATE T055 SET T055_Valor_Total_Custo                   = T055_Valor_Custo_Unitario * T055_Quantidade,
                        T055_Valor_Total_Despesas_Aduaneira      = xValor_Despesas_Aduaneiras,
                        T055_Valor_Despesas_Aduaneira            = T055_Valor_Total_Despesas_Aduaneira / T055_Quantidade,
                        T055_Valor_Total_Despesas                = xT075_Valor_Despesa,
                        T055_Valor_Despesas                      = T055_Valor_Total_Despesas / T055_Quantidade,
                        T055_Valor_Total_Aduaneira               = xT055_Valor_Total_Custo + T055_Valor_Total_Frete + T055_Valor_Total_Despesas_Aduaneira,
                        T055_Valor_Aduaneira                     = xT075_Valor_THC,
                        T055_Valor_Total_AFRMM                   = xT075_Valor_AFRMM,
                        T055_Valor_AFRMM                         = T055_Valor_Total_AFRMM / T055_Quantidade,
                        T055_Valor_Total_Siscomex                = xT075_Valor_Siscomex,
                        T055_Valor_Siscomex                      = T055_Valor_Total_Siscomex / T055_Quantidade,
                        T055_Valor_Total_THC                     = xT075_Valor_THC,
                        T055_Valor_THC                           = T055_Valor_Total_THC / T055_Quantidade,
                        T055_Multiplicador_X                     = ( 1 + xAliquota_ICMS_PIS_COFINS * ( (T055_Aliquota_Imposto_Importacao/100) + (T055_Aliquota_IPI/100) * ( 1 + (T055_Aliquota_Imposto_Importacao/100) ) ) ) / ( ( 1 - (T055_Aliquota_PIS/100) - (T055_Aliquota_COFINS/100) ) * ( 1 - xAliquota_ICMS_PIS_COFINS ) ),
                        T055_Valor_Base_ICMS_Sem_Reducao         = IF(xAliquota_ICMS > 0 AND xAliquota_ICMS_Normal > 0,
                                                                   IFNULL(xT055_Valor_Total_Custo, 0) +
                                                                   IFNULL(xValor_Base_ICMS, 0) +
                                                                   IFNULL(T055_Valor_PIS, 0) +
                                                                   IFNULL(T055_Valor_COFINS, 0) +
                                                                   IFNULL(T055_Valor_IPI, 0) +
                                                                   IFNULL(T055_Valor_Imposto_Importacao, 0) +
                                                                   IFNULL(T055_Valor_Total_AFRMM, 0),
                                                                   T055_Valor_Base_ICMS_Sem_Reducao),
                        T055_Valor_Base_ICMS_Normal              = IF(xAliquota_ICMS > 0 AND xAliquota_ICMS_Normal > 0,
                                                                   ROUND((T055_Valor_Base_ICMS_Sem_Reducao * 100) / xAliquota_ICMS_Normal, 2),
                                                                   T055_Valor_Base_ICMS_Normal),
                        T055_Valor_Base_ICMS_Presumido           = IF(xAliquota_ICMS > 0 AND xAliquota_ICMS_Normal > 0,
                                                                   ROUND(((T055_Valor_Base_ICMS_Sem_Reducao * 100) / xAliquota_ICMS_Normal) *
                                                                   IF(xPercentual_Reducao_ICMS_Presumido > 0, xPercentual_Reducao_ICMS_Presumido, 1), 2),
                                                                   T055_Valor_Base_ICMS_Presumido),
                        T055_Valor_ICMS                          = IF(xAliquota_ICMS > 0 AND xAliquota_ICMS_Normal > 0,
                                                                   (T055_Valor_Base_ICMS_Normal * xAliquota_ICMS) -
                                                                   ((T055_Valor_Base_ICMS_Normal * xAliquota_ICMS) * xPercentual_Reducao_ICMS_Presumido),
                                                                   T055_Valor_ICMS),
                        T055_Valor_ICMS_Presumido                = IF(xAliquota_ICMS > 0 AND xAliquota_ICMS_Normal > 0,
                                                                   T055_Valor_Base_ICMS_Presumido * xAliquota_ICMS_Presumido,
                                                                   T055_Valor_ICMS_Presumido),
                        T055_Valor_ICMS_Pagar                    = IF(xAliquota_ICMS > 0 AND xAliquota_ICMS_Normal > 0,
                                                                   T055_Valor_ICMS - T055_Valor_ICMS_Presumido,
                                                                   T055_Valor_ICMS_Pagar),
                        T055_Peso_Kg_Liquido_Total               = T055_Peso_Kg_Liquido_Unitario * T055_Quantidade,
                        T055_Peso_Kg_Liquido_Total_Fatura        = T055_Peso_Kg_Liquido_Unitario_Fatura * T055_Quantidade,
                        T055_Peso_Lb_Liquido_Unitario            = T055_Peso_Kg_Liquido_Unitario * 2.2,
                        T055_Peso_Lb_Liquido_Total               = T055_Peso_Lb_Liquido_Unitario * T055_Quantidade,
                        T055_Aliquota_Extra                      = 0.00,
                        T055_Valor_Total_ICMS                    = T055_Valor_ICMS,
                        T055_Valor_Total_ICMS_Presumido          = T055_Valor_ICMS_Presumido,
                        T055_Valor_Total_ICMS_Pagar              = T055_Valor_ICMS_Pagar,
                        T055_Valor_Total_IPI                     = T055_Valor_IPI,
                        T055_Valor_Total_Imposto_Importacao      = T055_Valor_Imposto_Importacao,
                        T055_Valor_Total_COFINS                  = T055_Valor_COFINS,
                        T055_Valor_Total_PIS                     = T055_Valor_PIS,
                        T055_Valor_Total_Extra                   = T055_Valor_Extra,
                        T055_Valor_Total_Base_IPI                = T055_Valor_Base_IPI,
                        T055_Valor_Total_Base_ICMS_Sem_Reducao   = T055_Valor_Base_ICMS_Sem_Reducao,
                        T055_Valor_Total_Base_ICMS_Normal        = T055_Valor_Base_ICMS_Normal,
                        T055_Valor_Total_Base_ICMS_Presumido     = T055_Valor_Base_ICMS_Presumido,
                        T055_Valor_Total_Base_COFINS             = T055_Valor_Base_COFINS,
                        T055_Valor_Total_Base_PIS                = T055_Valor_Base_PIS,
                        T055_Valor_Total_Base_Imposto_Importacao = T055_Valor_Base_Imposto_Importacao,
                        T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal = xValor_Despesas_Acessorias,
                        T055_Valor_Total_Preco_Nota_Fiscal       = T055_Valor_Total_Preco_Nota_Fiscal(xT055_Valor_Total_Custo, T055_Valor_Base_Imposto_Importacao, T055_Valor_Base_COFINS, T055_Valor_Base_PIS, T055_Valor_Base_IPI, T055_Valor_Total_Imposto_Importacao, T055_Valor_Total_PIS, T055_Valor_Total_COFINS, T055_Valor_Total_IPI, T055_Valor_Total_Frete, T055_Valor_Total_AFRMM, T055_Valor_Total_THC, T055_Valor_Total_Siscomex, T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal, T055_Valor_Total_Despesas_Aduaneira, T055_Valor_Total_Despesas, xValor_Despesas_Preco_NFe, T055_Valor_Total_Seguro), 
                        T055_Valor_Preco_Unitario_Nota_Fiscal    = T055_Valor_Total_Preco_Nota_Fiscal / T055_Quantidade,
                        T055_Valor_Total_Nota_Fiscal             = T055_Valor_Total_Nota_Fiscal(xT055_Valor_Total_Custo, T055_Valor_Total_Preco_Nota_Fiscal, T055_Valor_Base_Imposto_Importacao, T055_Valor_Base_COFINS, T055_Valor_Base_PIS, T055_Valor_Base_IPI, T055_Valor_Total_Imposto_Importacao, T055_Valor_Total_PIS, T055_Valor_Total_COFINS, T055_Valor_Total_IPI, T055_Valor_Total_Frete, T055_Valor_Total_AFRMM, T055_Valor_Total_THC, T055_Valor_Total_Siscomex, T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal, T055_Valor_Total_Despesas_Aduaneira, T055_Valor_Total_Despesas, T055_Valor_Total_Seguro), 
                        T055_Valor_Total_Custo_Final             = IF(T055_Cotacao_CC>0,(T055_Valor_Custo_Unitario * T055_Quantidade)*T055_Cotacao_CC,xT055_Valor_Total_Custo_CC) +
                                                                   IF(xT075_Modelo_Calculos = '2', xValor_Total_Custo_Produto,   
                                                                                                   T055_Valor_Total_Despesas_Aduaneira +
                                                                                                   T055_Valor_Aduaneira +
                                                                                                   T055_Valor_Total_Despesas +
                                                                                                   T055_Valor_Total_Frete + 
                                                                                                   IF(xT075_Somar_Seguro_Custo = 'S', T055_Valor_Total_Seguro, 0) +
                                                                                                   IF(xT075_Somar_AFRMM_Custo = 'S', T055_Valor_Total_AFRMM, 0)) +
                                                                   T055_Valor_Imposto_Importacao +
                                                                   IF(xT075_Somar_PIS_Custo = 'S', T055_Valor_PIS, 0) +
                                                                   IF(xT075_Somar_COFINS_Custo = 'S', T055_Valor_COFINS, 0) +                                                                 
                                                                   IF(xT075_Somar_ICMS_Pagar_Custo = 'S', T055_Valor_ICMS_Pagar, 0) +
                                                                   IF(xT075_Somar_IPI_Custo = 'S', T055_Valor_IPI, 0),
                        T055_Valor_Custo_Final_Unitario          = T055_Valor_Total_Custo_Final / T055_Quantidade
                  WHERE T055_Id=xT055_Id;
    ELSE
        UPDATE T055 SET T055_Valor_Total_Custo                   = T055_Valor_Custo_Unitario * T055_Quantidade,
                        T055_Valor_Total_Despesas_Aduaneira      = xValor_Despesas_Aduaneiras,
                        T055_Valor_Despesas_Aduaneira            = T055_Valor_Total_Despesas_Aduaneira / T055_Quantidade,
                        T055_Valor_Total_Despesas                = xT075_Valor_Despesa,
                        T055_Valor_Despesas                      = T055_Valor_Total_Despesas / T055_Quantidade,
                        T055_Valor_Total_Frete                   = xT055_Valor_Frete,
                        T055_Valor_Frete                         = T055_Valor_Total_Frete  / T055_Quantidade,
                        T055_Valor_Total_Seguro                  = xT075_Valor_Seguro,
                        T055_Valor_Seguro                        = T055_Valor_Total_Seguro  / T055_Quantidade,
                        T055_Valor_Total_Aduaneira               = xT055_Valor_Total_Custo+T055_Valor_Total_Frete+T055_Valor_Total_Despesas_Aduaneira,
                        T055_Valor_Total_AFRMM                   = xT075_Valor_AFRMM,
                        T055_Valor_AFRMM                         = T055_Valor_Total_AFRMM / T055_Quantidade,
                        T055_Multiplicador_X                     = ( 1 + xAliquota_ICMS_PIS_COFINS * ( (T055_Aliquota_Imposto_Importacao/100) + (T055_Aliquota_IPI/100) * ( 1 + (T055_Aliquota_Imposto_Importacao/100) ) ) ) / ( ( 1 - (T055_Aliquota_PIS/100) - (T055_Aliquota_COFINS/100) ) * ( 1 - xAliquota_ICMS_PIS_COFINS ) ),
                        T055_Peso_Kg_Liquido_Total               = T055_Peso_Kg_Liquido_Unitario * T055_Quantidade,
                        T055_Peso_Kg_Liquido_Total_Fatura        = T055_Peso_Kg_Liquido_Unitario_Fatura * T055_Quantidade,
                        T055_Peso_Lb_Liquido_Unitario            = T055_Peso_Kg_Liquido_Unitario * 2.2,
                        T055_Peso_Lb_Liquido_Total               = T055_Peso_Lb_Liquido_Unitario * T055_Quantidade,
                        T055_Aliquota_Extra                      = 0.00,
                        T055_Valor_Total_ICMS                    = T055_Valor_ICMS,
                        T055_Valor_Total_ICMS_Presumido          = T055_Valor_ICMS_Presumido,
                        T055_Valor_Total_ICMS_Pagar              = T055_Valor_ICMS_Pagar,
                        T055_Valor_Total_IPI                     = T055_Valor_IPI,
                        T055_Valor_Total_Imposto_Importacao      = T055_Valor_Imposto_Importacao,
                        T055_Valor_Total_COFINS                  = T055_Valor_COFINS,
                        T055_Valor_Total_PIS                     = T055_Valor_PIS,
                        T055_Valor_Total_Extra                   = T055_Valor_Extra,
                        T055_Valor_Total_Base_IPI                = T055_Valor_Base_IPI,
                        T055_Valor_Total_Base_ICMS_Sem_Reducao   = T055_Valor_Base_ICMS_Sem_Reducao,
                        T055_Valor_Total_Base_ICMS_Normal        = T055_Valor_Base_ICMS_Normal,
                        T055_Valor_Total_Base_ICMS_Presumido     = T055_Valor_Base_ICMS_Presumido,
                        T055_Valor_Total_Base_COFINS             = T055_Valor_Base_COFINS,
                        T055_Valor_Total_Base_PIS                = T055_Valor_Base_PIS,
                        T055_Valor_Total_Base_Imposto_Importacao = T055_Valor_Base_Imposto_Importacao,
                        #T055_Valor_Total_Preco_Nota_Fiscal      = xT055_Valor_Total_Custo + xValor_Base_II + T055_Valor_Imposto_Importacao + T055_Valor_PIS + T055_Valor_COFINS,
                        T055_Valor_Total_Preco_Nota_Fiscal       = T055_Valor_Total_Preco_Nota_Fiscal(xT055_Valor_Total_Custo, xValor_Base_II, T055_Valor_Imposto_Importacao, T055_Valor_PIS, T055_Valor_COFINS), 
                        T055_Valor_Preco_Unitario_Nota_Fiscal    = T055_Valor_Total_Preco_Nota_Fiscal / T055_Quantidade,
                        T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal = xValor_Despesas_Acessorias,
                        T055_Valor_Total_Custo_Final             = IF(T055_Cotacao_CC>0,(T055_Valor_Custo_Unitario * T055_Quantidade)*T055_Cotacao_CC,xT055_Valor_Total_Custo_CC) +
                                                                   IF(xT075_Modelo_Calculos = '2', xValor_Total_Custo_Produto,   
                                                                                                   T055_Valor_Total_Despesas_Aduaneira +
                                                                                                   T055_Valor_Aduaneira +
                                                                                                   T055_Valor_Total_Despesas +
                                                                                                   T055_Valor_Total_Frete + 
                                                                                                   IF(xT075_Somar_Seguro_Custo = 'S', xT075_Valor_Seguro, 0) +
                                                                                                   IF(xT075_Somar_AFRMM_Custo = 'S', T055_Valor_Total_AFRMM, 0)) +
                                                                   T055_Valor_Imposto_Importacao +
                                                                   IF(xT075_Somar_PIS_Custo = 'S', T055_Valor_PIS, 0) +
                                                                   IF(xT075_Somar_COFINS_Custo = 'S', T055_Valor_COFINS, 0) +                                                                 
                                                                   IF(xT075_Somar_ICMS_Pagar_Custo = 'S', T055_Valor_ICMS_Pagar, 0) +
                                                                   IF(xT075_Somar_IPI_Custo = 'S', T055_Valor_IPI, 0),
                        T055_Valor_Custo_Final_Unitario          = T055_Valor_Total_Custo_Final / T055_Quantidade
                  WHERE T055_Id=xT055_Id;
    END IF;
END;
