CREATE FUNCTION `TRIB_ST_VA_Ajustada`(xD006_Flag_Ajustar_MVA CHAR(1), xST_VA DECIMAL(10,2), xICMS_Inter_Estadual DECIMAL(5,2), xST_ICMS_Interno DECIMAL(5,2), xPercentual_Reducao_ICMS_ST DECIMAL(5,2), xflagTipo VARCHAR(1), xPercentual_Reducao_ICMS DECIMAL(5,2), xPercentual_Reducao_ICMS_IE_ST DECIMAL(10,2), xPercentual_Reducao_ICMS_INT_ST DECIMAL(10,2), xT219_Flag_Origem_Produto VARCHAR(1), xT219_Flag_Destino_Produto VARCHAR(1), xC004_D018_Id INT(11), xD024_D018_Id INT(11)) RETURNS DECIMAL(12,4)
BEGIN

        DECLARE xReducaoAliquotaIE DECIMAL(10,6) DEFAULT 0;
        DECLARE xReducaoAliquotaINT DECIMAL(10,6) DEFAULT 0;
        DECLARE xFatorAliquotaIE DECIMAL(15,6) DEFAULT 0;
        DECLARE xFatorAliquotaINT DECIMAL(15,6) DEFAULT 0;
        DECLARE xFatorMVA DECIMAL(15,6) DEFAULT 0;

        IF xD006_Flag_Ajustar_MVA = 'S' THEN

            # Fator MVA -------------------------------------------------------
            SET xFatorMVA = 1 + (xST_VA / 100);
            #------------------------------------------------------------------

            # Fator Aliquota ICM Inter-Estadual -------------------------------
            IF xPercentual_Reducao_ICMS_IE_ST>0 THEN
            
                SET xReducaoAliquotaIE = (100-xPercentual_Reducao_ICMS_IE_ST)/100;
                SET xICMS_Inter_Estadual = xICMS_Inter_Estadual * xReducaoAliquotaIE;
            
            ELSEIF xPercentual_Reducao_ICMS>0 THEN

                SET xReducaoAliquotaIE = (100-xPercentual_Reducao_ICMS)/100;
                SET xICMS_Inter_Estadual = xICMS_Inter_Estadual * xReducaoAliquotaIE;

            END IF;

            SET xFatorAliquotaIE = 1 - ( xICMS_Inter_Estadual / 100 ) ;
            #-------------------------------------------------------------------

            # Fator aliquota ICM Interno ---------------------------------------
            IF xPercentual_Reducao_ICMS_INT_ST>0 THEN

                SET xReducaoAliquotaINT = (100-xPercentual_Reducao_ICMS_INT_ST)/100;
                SET xST_ICMS_Interno = xST_ICMS_Interno * xReducaoAliquotaINT;

            END IF;
            #--------------------------------------------------------------------

            SET xFatorAliquotaINT = 1 - ( xST_ICMS_Interno / 100 ) ;

            # Fórmula ajuste MVA -------------------------------------------------
            RETURN ( ( xFatorMVA * ( xFatorAliquotaIE / xFatorAliquotaINT ) ) - 1 ) * 100;
            #---------------------------------------------------------------------
            
        ELSE

            # MVA sem ajuste -----------------------------------------------------
            RETURN xST_VA;
            #---------------------------------------------------------------------

        END IF;

END;
