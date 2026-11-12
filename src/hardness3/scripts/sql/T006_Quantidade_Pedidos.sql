CREATE FUNCTION `T006_Quantidade_Pedidos`(xT206_Id int(10), xD169_Id int(10)) RETURNS varchar(10) CHARSET utf8
BEGIN

     DECLARE xQuantidade_Pedidos DECIMAL(10,2);
     DECLARE xD169_Sigla varchar(3);
     DECLARE xRetorno DECIMAL(10,2);

      SELECT D169_Sigla_Tipo_Produto
        INTO xD169_Sigla
        FROM D169
       WHERE D169_Id = xD169_Id;

      SELECT SUM(T006_Quantidade)
        INTO xQuantidade_Pedidos
        FROM T206   
   LEFT JOIN T006 ON T206_Id=T006_T206_Id  
   LEFT JOIN D169 ON D169_Id=T206_D169_Id 
   LEFT JOIN T005 ON T005_Id=T006_T005_Id  
       WHERE T006_T206_Id=xT206_Id
      	 AND D169_Sigla_Tipo_Produto=xD169_Sigla
         AND T005_Flag_Status!="8";

      IF xQuantidade_Pedidos>0 THEN
          SET xRetorno=xQuantidade_Pedidos;
      END IF;           
      	   	     
      IF xRetorno < 0 THEN
          SET xRetorno = 0;
      END IF;        

   RETURN xRetorno;
   
END;