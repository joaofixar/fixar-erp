CREATE PROCEDURE `D024_Gravar_Totalizacao`(xD024_Id int(11))
BEGIN
    DECLARE xValor_Credito_Antecipado DECIMAL(14,2);
    DECLARE xValor_Atual DECIMAL(14,2) DEFAULT '0.00';
    DECLARE xValor_Saldo_Atual DECIMAL(14,2) DEFAULT '0.00';
    DECLARE xValor_Saldo_Credito_Antecipado DECIMAL(14,2) DEFAULT '0.00';
	DECLARE xValor_Saldo_Credito_Troca DECIMAL(14,2) DEFAULT '0.00';
	DECLARE xValor_Abatimento_Credito_Antecipado DECIMAL(14,2);
	DECLARE xValor_Credito_Troca DECIMAL(14,2);
	DECLARE xValor_Abatimento_Credito_Troca DECIMAL(14,2);
    DECLARE done int DEFAULT FALSE;
    DECLARE xFlag_Credito_Antecipado char(1);
    DECLARE xT000_Id int(11);
    DECLARE xTipo_Titulo char(1);

    DECLARE xCursor CURSOR FOR 
        (SELECT T002_Id, 
                SUM(ifnull(T002_Valor_Duplicata,0)+ifnull(T002_Valor_Juros,0)-ifnull(T002_Valor_Desconto,0)), 
                D027_Flag_Credito_Antecipado, 
                T002_Valor_Saldo_Credito, 
                'R'
          FROM T002 
     LEFT JOIN D027 ON D027_Id = T002_D027_Id
         WHERE T002_D024_Id = xD024_Id AND IFNULL(D027_Flag_Credito_Antecipado,'') != ''
           AND (T002_T002_Id_Agrupado=0 OR T002_T002_Id_Agrupado='' OR T002_T002_Id_Agrupado IS NULL)
           AND T002_Flag_Cancelada	!= 'S'
      GROUP BY T002_Id ORDER BY T002_Id, T002_Data_Emissao)
    UNION
        (SELECT T015_Id,
                SUM(ifnull(T015_Valor_Duplicata,0)+ifnull(T015_Valor_Juros,0)-ifnull(T015_Valor_Desconto,0)), 
                D031_Flag_Credito_Antecipado, 
                T015_Valor_Saldo_Credito, 
                'P'
           FROM T015 
      LEFT JOIN D031 ON D031_Id = T015_D031_Id
          WHERE T015_D024_Id = xD024_Id AND IFNULL(D031_Flag_Credito_Antecipado,'') != ''
            AND (T015_T015_Id_Agrupado=0 OR T015_T015_Id_Agrupado='' OR T015_T015_Id_Agrupado IS NULL)
            AND T015_Flag_Cancelada != 'S'
       GROUP BY T015_Id ORDER BY T015_Id, T015_Data_Emissao);

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    SET xValor_Saldo_Credito_Antecipado = 0;
    SET xValor_Saldo_Credito_Troca = 0;
    OPEN xCursor;

    the_loop: LOOP

        FETCH xCursor INTO xT000_Id, xValor_Atual, xFlag_Credito_Antecipado, xValor_Saldo_Atual, xTipo_Titulo;
        IF done THEN
          LEAVE the_loop;
        END IF;

        IF xFlag_Credito_Antecipado = '1' THEN
            SET xValor_Saldo_Credito_Antecipado = xValor_Saldo_Credito_Antecipado + xValor_Atual;
        ELSEIF xFlag_Credito_Antecipado = '2' THEN
            SET xValor_Saldo_Credito_Troca = xValor_Saldo_Credito_Troca + xValor_Atual;
        ELSEIF xFlag_Credito_Antecipado = '3' THEN
            SET xValor_Saldo_Credito_Antecipado = xValor_Saldo_Credito_Antecipado - xValor_Atual;
        ELSEIF xFlag_Credito_Antecipado = '4' THEN
            SET xValor_Saldo_Credito_Troca = xValor_Saldo_Credito_Troca - xValor_Atual;
        END IF;

        IF xTipo_Titulo = 'R' THEN
            IF (xFlag_Credito_Antecipado = '1' OR xFlag_Credito_Antecipado = '3') AND xValor_Saldo_Credito_Antecipado != xValor_Saldo_Atual THEN
                UPDATE T002 SET T002_Valor_Saldo_Credito = xValor_Saldo_Credito_Antecipado WHERE T002_Id = xT000_Id;
            ELSEIF (xFlag_Credito_Antecipado = '2' OR xFlag_Credito_Antecipado = '4') AND xValor_Saldo_Credito_Troca != xValor_Saldo_Atual THEN
                UPDATE T002 SET T002_Valor_Saldo_Credito = xValor_Saldo_Credito_Troca WHERE T002_Id = xT000_Id;
            END IF;
        ELSEIF xTipo_Titulo = 'P' THEN
            IF (xFlag_Credito_Antecipado = '1' OR xFlag_Credito_Antecipado = '3') AND xValor_Saldo_Credito_Antecipado != xValor_Saldo_Atual THEN
                UPDATE T015 SET T015_Valor_Saldo_Credito = xValor_Saldo_Credito_Antecipado WHERE T015_Id = xT000_Id;
            ELSEIF (xFlag_Credito_Antecipado = '2' OR xFlag_Credito_Antecipado = '4') AND xValor_Saldo_Credito_Troca != xValor_Saldo_Atual THEN
                UPDATE T015 SET T015_Valor_Saldo_Credito = xValor_Saldo_Credito_Troca WHERE T015_Id = xT000_Id;
            END IF;
        END IF;

    END LOOP the_loop;
 
    CLOSE xCursor;        

    UPDATE D024 SET D024_Valor_Credito_Antecipado = xValor_Saldo_Credito_Antecipado, D024_Valor_Credito_Troca = xValor_Saldo_Credito_Troca WHERE D024_Id = xD024_Id;
END;
