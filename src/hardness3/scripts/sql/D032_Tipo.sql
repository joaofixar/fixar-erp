CREATE FUNCTION `D032_Tipo`(xTipo int(2)) RETURNS varchar(30)
BEGIN
    DECLARE xDescricao VARCHAR(25);
    SET xDescricao = '';

    IF xTipo=1 THEN
        SET xDescricao = '01-RECEITAS';
    END IF;
    IF xTipo=10 THEN
        SET xDescricao = '10-FIXAS';
    END IF;
    IF xTipo=20 THEN
        SET xDescricao = '20-VARIAVEIS';
    END IF;
    IF xTipo=25 THEN
        SET xDescricao = '25-FORNECEDORES';
    END IF;
    IF xTipo=30 THEN
        SET xDescricao = '30-INVESTIMENTOS';
    END IF;
    IF xTipo=40 THEN
        SET xDescricao = '40-SOCIOS';
    END IF;
    IF xTipo=50 THEN
        SET xDescricao = '50-OUTROS';
    END IF;

    RETURN xDescricao;
END;

