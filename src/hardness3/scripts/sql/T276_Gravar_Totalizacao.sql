CREATE PROCEDURE `T276_Gravar_Totalizacao`(xT276_Id int(11))
BEGIN
    DECLARE xQtde_Notas int(10);
	DECLARE xQtde_Conhecimento int(10);
	DECLARE xPeso_Total DECIMAL(10,2);
	DECLARE xValor_Total DECIMAL(10,2);
	     SELECT sum(CASE WHEN T284_Flag_Tipo_Documento = 1 THEN 1 ELSE 0 END),
				sum(CASE WHEN T284_Flag_Tipo_Documento = 2 THEN 1 ELSE 0 END),
	            sum(T284_Peso_Bruto_Total),
	            sum(T284_Valor_Carga)
	       INTO xQtde_Conhecimento,
		     	xQtde_Notas,
	            xPeso_Total,
	            xValor_Total
           FROM T284      
      LEFT JOIN T007 ON T284_T007_Id=T007_Id
          WHERE T284_T276_Id=xT276_Id;

    UPDATE T276 SET T276_Quantidade_NFe = xQtde_Notas,
					T276_Quantidade_CTe = xQtde_Conhecimento,
				    T276_Peso_Bruto_Total_Carga = xPeso_Total,
				    T276_Valor_Carga = xValor_Total
              WHERE T276_Id=xT276_Id;
END;
