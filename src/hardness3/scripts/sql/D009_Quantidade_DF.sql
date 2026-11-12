CREATE FUNCTION `D009_Quantidade_DF`(xD009_Id int(10), xT206_Id int(10), xT006_Id int(10)) RETURNS varchar(10) CHARSET utf8
BEGIN

     DECLARE xQuantidade_DF DECIMAL(10,2);
     DECLARE xQuantidade_Pedidos DECIMAL(10,2);
     DECLARE xRetorno DECIMAL(10,2);
      
      SELECT T206_Quantidade
        INTO xQuantidade_DF
        FROM T206
       WHERE T206_Id=xT206_Id
  	     AND T206_D009_Id=xD009_Id
         AND T206_Flag_Tipo=1;
  	       	     
      SELECT SUM(T006_Quantidade)
        INTO xQuantidade_Pedidos
        FROM T006     
        LEFT JOIN D006 ON D006_Id=T006_D006_Id       
       WHERE T006_T206_Id=xT206_Id
      	 AND T006_D009_Id=xD009_Id
         AND IF(xT006_Id > 0, T006_Id != xT006_Id, '1=1');
      
      SET xRetorno=xQuantidade_DF;	 
       IF xQuantidade_Pedidos>0 THEN
          SET xRetorno = xQuantidade_DF-xQuantidade_Pedidos;
       END IF;           
      	   	     
      IF xRetorno < 0 THEN
          SET xRetorno = 0;
      END IF;        

   RETURN xRetorno;
   
END;

