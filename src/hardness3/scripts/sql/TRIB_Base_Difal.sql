CREATE FUNCTION `TRIB_Base_Difal`(xAliquota_ICMS decimal(20,2), 
                                  xValor_Base_ICMS decimal(20,2), 
                                  xTrib_Interna_UF_Dest_Reducao_Base decimal(20,2),
                                  xValor_ICMS decimal(10,2),                                 
                                  xST_ICMS_Interno decimal(5,2),
                                  xICMS_Inter_Estadual decimal(5,2),
                                  xPercentual_Reducao_ICMS decimal(5,2),
                                  xCalculo_Difal_Por_Dentro varchar(1),
                                  xD006_Flag_Somar_Fator_ICMS_Dif_Base varchar(1),
                                  xFator_ICMS decimal(19,10)) RETURNS decimal(15,2)
                                
BEGIN

        DECLARE baseDifal DECIMAL(15,2);

		IF(xCalculo_Difal_Por_Dentro = 'S') THEN
			
			SET baseDifal = If(xAliquota_ICMS>0, ((xValor_Base_ICMS - xValor_ICMS) * IF(xTrib_Interna_UF_Dest_Reducao_Base > 0,((100-xTrib_Interna_UF_Dest_Reducao_Base)/100),1) / ((xST_ICMS_Interno / 100-1) * -1)),0);
		
        ELSE

			SET baseDifal = If(xAliquota_ICMS>0, xValor_Base_ICMS * IF(xTrib_Interna_UF_Dest_Reducao_Base > 0,((100-xTrib_Interna_UF_Dest_Reducao_Base)/100),1),0);

		END IF;

        RETURN IF(xD006_Flag_Somar_Fator_ICMS_Dif_Base = 'S', (baseDifal / xFator_ICMS), baseDifal);

END
