CREATE FUNCTION `T007_Primeira_Ultima_Compra`(xTipo INT(1), xD024_Id INTEGER) RETURNS VARCHAR(10)
BEGIN
	DECLARE xRetorno VARCHAR(10);
	IF xTipo=1 THEN
		SELECT T007_Data_Emissao
		INTO xRetorno
                     FROM T007
                LEFT JOIN D006 ON D006_Id=T007_D006_Id
                    WHERE T007_D024_Id = xD024_Id
                      AND D006_Flag_Entrada_Saida='S'
                      AND T007_Flag_Cancelada <> 'S'
                      AND D006_Flag_Gera_Contas='R'
                      AND D006_Flag_Devolucao<>'S'
                      AND D006_Flag_Venda_Compra_Outros = 'V'
                 ORDER BY T007_Data_Emissao ASC
		LIMIT 1;
		IF xRetorno IS NULL THEN
			SET xRetorno = '0000-00-00';
		END IF;
	ELSEIF xTipo=2 THEN
		SELECT T007_Data_Emissao
		INTO xRetorno
                     FROM T007
                LEFT JOIN D006 ON D006_Id=T007_D006_Id
                    WHERE T007_D024_Id = xD024_Id
                      AND D006_Flag_Entrada_Saida='S'
                      AND T007_Flag_Cancelada <> 'S'
                      AND D006_Flag_Gera_Contas='R'
                      AND D006_Flag_Devolucao<>'S'
                      AND D006_Flag_Venda_Compra_Outros = 'V'
                 ORDER BY T007_Data_Emissao DESC
		LIMIT 1;
		IF xRetorno IS NULL THEN
			SET xRetorno = '0000-00-00';
		END IF;
	ELSEIF xTipo=3 THEN
		SELECT T007_C004_Id
		INTO xRetorno
                     FROM T007
                LEFT JOIN D006 ON D006_Id=T007_D006_Id
                    WHERE T007_D024_Id = xD024_Id
                      AND D006_Flag_Entrada_Saida='S'
                      AND T007_Flag_Cancelada <> 'S'
                      AND D006_Flag_Gera_Contas='R'
                      AND D006_Flag_Devolucao<>'S'
                      AND D006_Flag_Venda_Compra_Outros = 'V'
                 ORDER BY T007_Data_Emissao DESC
		LIMIT 1;
		IF xRetorno IS NULL THEN
			SET xRetorno = '0';
		END IF;
	END IF;
     RETURN xRetorno;
END;