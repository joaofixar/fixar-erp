CREATE FUNCTION `D009_Quantidade_DM`(xD009_Id int(10), xT206_Id int(10)) RETURNS varchar(10) CHARSET utf8
BEGIN

     DECLARE xQuantidade_DF DECIMAL(10,2);
     DECLARE xQuantidade_Pedidos DECIMAL(10,2);
     DECLARE xRetorno DECIMAL(10,2);
      
      SELECT T206_Quantidade
        INTO xQuantidade_DF
        FROM T206
       WHERE T206_Id=xT206_Id
  	     AND T206_D009_Id=xD009_Id
         AND T206_Flag_Tipo=2;
  	       	     
      SELECT SUM(T006_Quantidade)
        INTO xQuantidade_Pedidos
        FROM T006     
        LEFT JOIN D006 ON D006_Id=T006_D006_Id       
       WHERE T006_T206_Id=xT206_Id
         AND D006_Flag_Estoque!='N'
      	 AND T006_D009_Id=xD009_Id;
      
      SET xRetorno=xQuantidade_DF;	 
       IF xQuantidade_Pedidos>0 THEN
          SET xRetorno = xQuantidade_DF-xQuantidade_Pedidos;
       END IF;           
      	   	     
   RETURN xRetorno;
   
END;

