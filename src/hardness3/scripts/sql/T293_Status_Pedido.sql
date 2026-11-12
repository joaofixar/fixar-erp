CREATE FUNCTION `T293_Status_Pedido`(xStatus int(2), xT293_Id INT(11)) RETURNS varchar(30)
BEGIN
    DECLARE xDescricao VARCHAR(25);
    DECLARE xPercentualGerado DECIMAL(5,2);
    
    SET xDescricao = '';
    
    IF xStatus=0 OR xStatus='' THEN
        SET xDescricao = 'Pendente';
    END IF;
    IF xStatus=1 THEN
        SET xDescricao = 'Analise';
    END IF;
    IF xStatus=2 THEN
        SET xDescricao = 'Negado';
    END IF;
    IF xStatus=3 THEN
        SET xDescricao = 'Aprovado';
    END IF;
    IF xStatus=4 THEN
        SELECT
            (SUM(T294_Quantidade_Saldo)/SUM(T294_Quantidade))*100
        INTO 
            xPercentualGerado
        FROM T293 
   LEFT JOIN T294 ON T293_Id=T294_T293_Id AND T294_Flag_Finalizar_Compra !='S'
       WHERE T293_Id = xT293_Id;
        SET xDescricao = CONCAT('Saldo ',IFNULL(xPercentualGerado,0),'%');
    END IF;
    IF xStatus=5 THEN
        SET xDescricao = 'Finalizado';
    END IF;    
    IF xDescricao = '' THEN
        SET xDescricao = ' - ';
    END IF;
    RETURN xDescricao;
END;

