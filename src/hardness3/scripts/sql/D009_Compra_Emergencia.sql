CREATE FUNCTION `D009_Compra_Emergencia`(xEstoque_Minimo decimal(12,2), xEstoque_Liquido decimal(12,2), xQuantidade_OC decimal(12,2)) RETURNS decimal(12,2)
BEGIN
      
      RETURN IF((xEstoque_Minimo-(xEstoque_Liquido+xQuantidade_OC))<0,0,(xEstoque_Minimo-(xEstoque_Liquido+xQuantidade_OC)));

END;
