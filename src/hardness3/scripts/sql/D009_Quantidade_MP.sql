CREATE FUNCTION `D009_Quantidade_MP`(xD009_Id int(10), xT075_Id int(10)) RETURNS varchar(10) CHARSET utf8
BEGIN

     DECLARE xQuantidade_OC DECIMAL(10,2);
     DECLARE xQuantidade_Pedidos DECIMAL(10,2);
     DECLARE xRetorno DECIMAL(10,2);
      
      SELECT SUM(T055_Quantidade)
        INTO xQuantidade_OC
        FROM T055
      LEFT JOIN T075 ON T075_Id=T055_T075_Id
      LEFT JOIN D009 ON D009_Id=T055_D009_Id
       WHERE T055_T075_Id=xT075_Id
         AND T055_D009_Id=xD009_Id;
                 
      SELECT SUM(T006_Quantidade)
        INTO xQuantidade_Pedidos
        FROM T006
        LEFT JOIN D009 ON D009_Id=T006_D009_Id
        LEFT JOIN T075 ON T075_Id=T006_T075_Id        
       WHERE T006_T075_Id=xT075_Id
         AND T006_D009_Id=xD009_Id
         AND T006_Flag_Status!='3';
      
      SET xRetorno=xQuantidade_OC;   
       IF xQuantidade_Pedidos>0 THEN
          SET xRetorno = xQuantidade_OC-xQuantidade_Pedidos;
       END IF;           
                 
   RETURN xRetorno;
   
END;

