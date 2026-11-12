CREATE FUNCTION `D009_Saldo_OE`(xD009_Id int(10), xT006_Id int(10)) RETURNS varchar(10) CHARSET utf8
BEGIN

     DECLARE xQuantidade_OE DECIMAL(10,2);
     DECLARE xQuantidade_Pedidos DECIMAL(10,2);
     DECLARE xRetorno DECIMAL(10,2);
      
      SELECT SUM(T206_Quantidade)
        INTO xQuantidade_OE
        FROM T206
       WHERE T206_D009_Id=xD009_Id
         AND T206_Flag_Finalizado = "A";
                 
      SELECT SUM(T006_Quantidade)
        INTO xQuantidade_Pedidos
        FROM T006
        LEFT JOIN T206 ON T206_Id=T006_T206_Id
        LEFT JOIN T005 ON T005_Id=T006_T005_Id  
       WHERE T206_D009_Id=xD009_Id
         AND T206_Flag_Finalizado = "A"
         AND IF(xT006_Id > 0, T006_Id != xT006_Id, '1=1')
         AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)
         AND ((T005_Flag_Status != '8'
         AND T005_Flag_Status!='5'
         AND T005_Flag_Status!='7'
         AND T005_Flag_Status!='11'
         AND T005_Flag_Status!='8'
         AND T005_Flag_Status!='26')
         OR T005_Flag_Status = '');
      
      SET xRetorno=xQuantidade_OE;   
       IF xQuantidade_Pedidos>0 THEN
          SET xRetorno = xQuantidade_OE-xQuantidade_Pedidos;
       END IF;       

   RETURN IFNULL(xRetorno,0);
   
END;


