CREATE FUNCTION `T003_Status_Orcamento`(xStatus char(1)) RETURNS varchar(30)
BEGIN
    DECLARE xDescricao VARCHAR(25);
    SET xDescricao = '';

     IF xStatus='P' THEN
        SET xDescricao = 'Pendente';
     END IF;
     IF xStatus='F' THEN
        SET xDescricao = 'Finalizado';
     END IF;
     IF xStatus='C' THEN
        SET xDescricao = 'Cancelado';
     END IF;
     IF xStatus='V' THEN
        SET xDescricao = 'Vencido';
     END IF;
     IF xStatus='A' THEN
        SET xDescricao = 'Aprovado';
     END IF;
     IF xStatus='N' THEN
        SET xDescricao = 'Negado';
     END IF;
     IF xStatus='L' THEN
        SET xDescricao = 'Análise';
     END IF;

    IF xDescricao = '' THEN
       SET xDescricao = ' - ';
    END IF;
    RETURN xDescricao;
END;
