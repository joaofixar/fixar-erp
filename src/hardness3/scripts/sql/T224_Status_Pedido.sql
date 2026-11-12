CREATE FUNCTION `T224_Status_Pedido`(xStatus int(2), xT224_Id INT(11)) RETURNS varchar(30)
BEGIN
    DECLARE xDescricao VARCHAR(25);
    DECLARE xPercentualRecebido DECIMAL(5,2);
    
    SET xDescricao = '';
    
    IF xStatus=0 OR xStatus='' THEN
        SET xDescricao = 'Pendente';
    END IF;
    IF xStatus=1 THEN
        SET xDescricao = 'Analise';
    END IF;
    IF xStatus=2 THEN
        SET xDescricao = 'NEGADO';
    END IF;
    IF xStatus=3 THEN
        SET xDescricao = 'APROVADO';
    END IF;
    IF xStatus=4 THEN
        SET xDescricao = 'Financeiro';
    END IF;
    IF xStatus=5 THEN
        SET xDescricao = 'Aguard.Coleta';
    END IF;
    IF xStatus=6 THEN
        SET xDescricao = 'Coletando';
    END IF;
    IF xStatus=7 THEN
        SET xDescricao = 'Coletado';
    END IF;
    IF xStatus=8 THEN
        SET xDescricao = 'Aguard.Mercadoria';
    END IF;
    IF xStatus=9 THEN
        SELECT
            (SUM(DISTINCT CONCAT(IFNULL(T014_Quantidade,'0.00000'),T014_Id))/SUM(DISTINCT CONCAT(T225_Quantidade,T225_Id)))*100
        INTO 
            xPercentualRecebido
        FROM T224 
        LEFT JOIN T225 ON T225_T224_Id = T224_Id AND T225_Flag_Cancelada != 'S'
        LEFT JOIN T014 ON T014_T225_Id = T225_Id AND T014_Flag_Cancelada != 'S' AND ifnull((SELECT T013_Flag_Estoque_Finalizado FROM T013 WHERE T014_T013_Id = T013_Id),'N') = 'S'
        LEFT JOIN T013 ON T014_T013_Id = T013_Id AND T013_Flag_Travar_Nota = 'S' AND T013_Flag_Cancelada != 'S' AND T013_Flag_Estoque_Finalizado = 'S'
        WHERE T224_Id = xT224_Id;

        SET xDescricao = CONCAT('Recebido ',IFNULL(xPercentualRecebido,0),'%');
    END IF;
    IF xStatus=10 THEN
        SET xDescricao = 'Finalizado';
    END IF;
    IF xStatus=11 THEN
        SET xDescricao = 'Cancelado';
    END IF;
    IF xStatus=12 THEN
        SET xDescricao = 'Vencido';
    END IF;
    IF xStatus=13 THEN
        SET xDescricao = 'Finalizado manual';
    END IF;

    IF xDescricao = '' THEN
        SET xDescricao = ' - ';
    END IF;
    RETURN xDescricao;
END;

