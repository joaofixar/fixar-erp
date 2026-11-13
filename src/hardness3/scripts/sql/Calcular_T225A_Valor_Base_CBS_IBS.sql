CREATE FUNCTION `Calcular_T225A_Valor_Base_CBS_IBS`(xT225_Id bigint, xT225A_Id bigint, xPreco decimal(20,2), xFrete decimal(20,10), xDespesas decimal(20,10), xDesconto decimal(20,10), xPIS decimal(10,2), xCOFINS decimal(10,2), xICMS decimal(15,2), xDIFAL decimal(15,2), xFCP decimal(15,2), xFCPPartilha decimal(15,2), xCBS decimal(7,4), xIBSMunicipal decimal(7,4), xIBSUF decimal(7,4), xCBSRegular decimal(7,4), xIBSMunicipalRegular decimal(7,4), xIBSUFRegular decimal(7,4)) RETURNS decimal(15,2)
BEGIN
    IF IFNULL(xCBS,0)<=0 AND IFNULL(xIBSMunicipal,0)<=0 AND IFNULL(xIBSUF,0)<=0 AND IFNULL(xCBSRegular,0)<=0 AND IFNULL(xIBSMunicipalRegular,0)<=0 AND IFNULL(xIBSUFRegular,0)<=0 THEN RETURN 0; END IF;
    RETURN IFNULL(xPreco,0)+IFNULL(xFrete,0)+IFNULL(xDespesas,0)-IFNULL(xDesconto,0)-IFNULL(xPIS,0)-IFNULL(xCOFINS,0)-IFNULL(xICMS,0)-IFNULL(xDIFAL,0)-IFNULL(xFCP,0)-IFNULL(xFCPPartilha,0);
END
