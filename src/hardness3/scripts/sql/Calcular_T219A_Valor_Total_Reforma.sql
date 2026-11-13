CREATE FUNCTION `Calcular_T219A_Valor_Total_Reforma`(xT219_Id bigint, xT219A_Id bigint, xPreco decimal(20,2), xDesconto decimal(20,10), xDeduzDesonerado varchar(1), xICMSDesonerado decimal(15,2), xICMSRetido decimal(20,2), xFCPST decimal(15,2), xFrete decimal(20,10), xDespesas decimal(20,10), xIPI decimal(14,2), xPIS decimal(10,2), xCOFINS decimal(10,2), xNaoSomarReforma varchar(1), xIBS decimal(15,2), xCBS decimal(15,2)) RETURNS decimal(15,2)
BEGIN
    RETURN IFNULL(xPreco,0)-IFNULL(xDesconto,0)-IFNULL(IF(xDeduzDesonerado,xICMSDesonerado,0),0)+IFNULL(xICMSRetido,0)+IFNULL(xFCPST,0)+IFNULL(xFrete,0)+IFNULL(xDespesas,0)+IFNULL(xIPI,0)+IF(IFNULL(xNaoSomarReforma,'S')='N',0,IFNULL(xIBS,0)+IFNULL(xCBS,0));
END
