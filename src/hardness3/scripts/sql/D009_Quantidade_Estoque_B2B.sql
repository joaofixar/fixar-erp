CREATE FUNCTION `D009_Quantidade_Estoque_B2B`(xD009_Id int(10), xD049_Id int(10), xEstoque decimal(10,3)) RETURNS decimal(10,3) 
BEGIN
      RETURN IFNULL((SELECT If(D009_Quantidade_Estoque_Liquido>0,D009_Quantidade_Estoque_Liquido,0) FROM D009 WHERE D009_D049_Id=xD049_Id AND D009_Id!=xD009_Id AND D009_C004_Id=1),0)+if(xEstoque>0,xEstoque,0);
END;