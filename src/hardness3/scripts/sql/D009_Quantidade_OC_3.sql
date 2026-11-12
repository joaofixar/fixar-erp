CREATE FUNCTION `D009_Quantidade_OC_3`(xD009_Id int(10), xT075_Id int(10)) RETURNS varchar(10) CHARSET utf8
BEGIN

     DECLARE xQuantidade_Pendente DECIMAL(10,3);
     DECLARE xQuantidade_Pendente_OC DECIMAL(10,3);
     DECLARE xQuantidade_Pendente_DI DECIMAL(10,3);
     DECLARE xQuantidade_Pendente_NF DECIMAL(10,3) DEFAULT 0;
     DECLARE xQuantidade_Pendente_PO DECIMAL(10,3) DEFAULT 0;


     SELECT SUM( T225_Quantidade_Pendente_Estoque(T225_Id) )
       INTO xQuantidade_Pendente_OC
       FROM T225
  LEFT JOIN T224 on T224_Id=T225_T224_Id
      WHERE T225_D009_Id=xD009_Id
        AND T225_Flag_Cancelada!='S'
        AND T224_Flag_Cancelada!='S'
        AND T224_Flag_Status!='12'
        AND T224_Flag_Status!='11'
        AND T224_Flag_Status!='2'
        AND T224_Flag_Status!='13';

      IF xQuantidade_Pendente_OC IS NULL or xQuantidade_Pendente_OC<0 THEN
        SET xQuantidade_Pendente_OC = 0;
      END IF;

      SELECT SUM(T055_Quantidade-(SELECT IFNULL(SUM(T006_Quantidade),0) FROM T006 WHERE T006_T075_Id=T075_Id AND T006_D009_Id=D009_Id AND T006_Flag_Status!='3')) 
        INTO xQuantidade_Pendente_DI
        FROM T055
   LEFT JOIN T075 ON T075_Id=T055_T075_Id
   LEFT JOIN D009 ON D009_Id=T055_D009_Id
   LEFT JOIN C031 ON C031_C004_Id=D009_C004_Id AND C031_Campo = 'diasMedioPrazo'
       WHERE T055_D009_Id=xD009_Id
         AND IFNULL(T075_Data_Estoque,'0000-00-00')='0000-00-00' 
         AND T075_Data_Previsao>0 
         AND T075_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL C031_Valor DAY));

      IF xQuantidade_Pendente_DI IS NULL or xQuantidade_Pendente_DI<0 THEN
        SET xQuantidade_Pendente_DI = 0;
      END IF;

      SELECT SUM(T296_Quantidade_Saldo) 
        INTO xQuantidade_Pendente_PO
        FROM T296
   LEFT JOIN T295 ON T295_Id=T296_T295_Id
   LEFT JOIN D009 ON D009_Id=T296_D009_Id
   LEFT JOIN C031 ON C031_C004_Id=D009_C004_Id AND C031_Campo = 'diasMedioPrazo'
       WHERE T296_D009_Id=xD009_Id
         AND (T295_Flag_Status = '3' OR T295_Flag_Status = '4')
         AND T296_Quantidade_Saldo>0
         AND T296_Flag_Finalizar_Compra !='S'
         AND T295_Data_Previsao>0 
         AND T295_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL C031_Valor DAY));

      IF xQuantidade_Pendente_PO IS NULL or xQuantidade_Pendente_PO<0 THEN
        SET xQuantidade_Pendente_PO = 0;
      END IF;

#      SELECT SUM(T008_Quantidade) 
#        INTO xQuantidade_Pendente_NF
#        FROM T008 
#   left join T007 on T007_Id=T008_T007_Id 
#   left join D009 on D009_Id=xD009_Id 
#   left join D049 on D049_Id=D009_D049_Id 
#   left join D001 on D001_Id=D049_D001_Id 
#   left join C004 on C004_Id=D009_C004_Id 
#       where T007_D024_Id=C004_D024_Id 
#         AND T007_C004_Id!=C004_Id 
#         AND T007_Flag_Emitida_Recebida!='R' 
#         AND T007_Flag_Cancelada!='S' 
#         AND T007_Numero_Nota_Fiscal!='' 
#         AND T007_Flag_Entrada_Saida='S' 
#         AND T007_Data_Emissao>=DATE(DATE_ADD(NOW(), INTERVAL -90 DAY))
#         AND T008_Codigo_Produto=D001_Codigo_Produto 
#         AND T007_Numero_Nota_Fiscal NOT IN (SELECT T013_Numero_Nota_Fiscal FROM T013 WHERE T013_Numero_Nota_Fiscal=T007_Numero_Nota_Fiscal and T013_C004_Id=C004_Id);
#
#      IF xQuantidade_Pendente_NF IS NULL or xQuantidade_Pendente_NF<0 THEN
#        SET xQuantidade_Pendente_NF = 0;
#      END IF;

      SET xQuantidade_Pendente = xQuantidade_Pendente_OC + xQuantidade_Pendente_DI + xQuantidade_Pendente_PO + xQuantidade_Pendente_NF;
      RETURN xQuantidade_Pendente;

END;