 CREATE FUNCTION `formata_cpf`(cpf varchar(20)) RETURNS varchar(14) 
 BEGIN  
 	DECLARE retorno varchar(14);  
 	-- Retirando possiveis formatacoes no valor recebido.  
 	SET retorno = cpf;
 	SET retorno = REPLACE(retorno,'.','');  
 	SET retorno = REPLACE(retorno,'-','');  
 	SET retorno = REPLACE(retorno,' ','');  
 	SET retorno = TRIM(retorno);  
 	-- Armazenando o valor recebido em uma variavel interna  
 	IF LENGTH(retorno)>11 or LENGTH(retorno)<11 THEN
	    return '';
 	END IF;
 	-- Montando a formatacao  
 	SET retorno = concat(mid(retorno,1,3),'.',mid(retorno,4,3),'.',mid(retorno,7,3),'-',mid(retorno,10,2));  
 	-- Retornando o resultado  
 	return retorno;  
 END ;

