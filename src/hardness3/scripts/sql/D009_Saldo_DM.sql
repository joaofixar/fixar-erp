CREATE FUNCTION `D009_Saldo_DM`(xD009_Id int(10)) RETURNS varchar(10) CHARSET utf8
BEGIN

     DECLARE xQuantidade_DF DECIMAL(10,2) DEFAULT 0;
     DECLARE xQuantidade_Pedidos DECIMAL(10,2) DEFAULT 0;
     DECLARE xRetorno DECIMAL(10,2) DEFAULT 0;
      
      SELECT SUM(T206_Quantidade)
        INTO xQuantidade_DF
        FROM T206
       WHERE T206_D009_Id=xD009_Id
       AND T206_Flag_Tipo=2;
  	       	     
      SELECT SUM(T006_Quantidade)
        INTO xQuantidade_Pedidos
        FROM T006        
        LEFT JOIN D006 ON D006_Id=T006_D006_Id
       WHERE T006_D009_Id=xD009_Id
       AND D006_Flag_Estoque!='N'
       AND T006_T206_Id > 0;
      
      SET xRetorno=xQuantidade_DF;	 
       IF xQuantidade_Pedidos>0 THEN
          SET xRetorno = xQuantidade_DF-xQuantidade_Pedidos;
       END IF;           
      	   	     
   RETURN ifnull(xRetorno,0);
   
END;

