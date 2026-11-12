 CREATE FUNCTION `formata_cnpj`(cnpj varchar(20)) RETURNS varchar(18) 
 BEGIN  
 	DECLARE retorno varchar(18);  
 	-- Retirando possiveis formatacoes no valor recebido.  
 	SET retorno = cnpj;
 	SET retorno = REPLACE(retorno,'.','');  
 	SET retorno = REPLACE(retorno,'-','');  
 	SET retorno = REPLACE(retorno,'/','');  
 	SET retorno = REPLACE(retorno,' ','');  
 	SET retorno = TRIM(retorno);  
 	-- Armazenando o valor recebido em uma variavel interna  
 	IF LENGTH(retorno)<14 THEN
	    return '';
 	END IF;
 	-- Montando a formatacao  
 	SET retorno = concat(mid(retorno,1,2),'.',mid(retorno,3,3),'.',mid(retorno,6,3),'/',mid(retorno,9,4),'-',mid(retorno,13,2));  
 	-- Retornando o resultado  
 	return retorno;  
 END ;

