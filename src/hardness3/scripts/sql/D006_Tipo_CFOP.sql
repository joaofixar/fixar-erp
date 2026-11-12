CREATE FUNCTION `D006_Tipo_CFOP`(xTipo varchar(2)) RETURNS varchar(30) CHARSET utf8
BEGIN
    DECLARE xDescricao VARCHAR(30);
    SET xDescricao = '';
    IF xTipo='V' THEN
       SET xDescricao = 'Venda';
    END IF;
    IF xTipo='C' THEN
       SET xDescricao = 'Compra';
    END IF;
    IF xTipo='T' THEN
       SET xDescricao = 'Transferencia';
    END IF;
    IF xTipo='O' THEN
       SET xDescricao = 'Outros';
    END IF;
    IF xTipo='OS' THEN
       SET xDescricao = 'Outras saidas';
    END IF;
    IF xTipo='OE' THEN
       SET xDescricao = 'Outras entradas';
    END IF;
    IF xTipo='DV' THEN
       SET xDescricao = 'Devolucao venda';
    END IF;
    IF xTipo='DC' THEN
       SET xDescricao = 'Devolucao compra';
    END IF;
    IF xTipo='TR' THEN
       SET xDescricao = 'Transporte';
    END IF;
    RETURN xDescricao;
END;

