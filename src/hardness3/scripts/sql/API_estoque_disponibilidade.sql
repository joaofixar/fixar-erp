 CREATE FUNCTION `API_estoque_disponibilidade`(xD009_Quantidade_Estoque DECIMAL(14,3)) RETURNS varchar(20) 
 BEGIN
    DECLARE disponibilidade varchar(20) default 'INDISPONIVEL';

      IF(xD009_Quantidade_Estoque>0) THEN
         SET disponibilidade = "DISPONIVEL";
      END IF;

   	RETURN disponibilidade;
END;

