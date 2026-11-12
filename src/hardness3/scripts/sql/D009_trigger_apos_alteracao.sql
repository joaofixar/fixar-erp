CREATE TRIGGER `D009_trigger_apos_alteracao` AFTER UPDATE ON `D009` FOR EACH ROW BEGIN
    DECLARE xD001_Id INT DEFAULT NULL;
    DECLARE xD001A_Id INT DEFAULT NULL;
    DECLARE xQuantidade_Estoque DECIMAL(14,2) DEFAULT 0;
    DECLARE xQuantidade_Estoque_Liquido DECIMAL(14,2) DEFAULT 0;

    IF NOT (NEW.D009_Quantidade_Estoque <=> OLD.D009_Quantidade_Estoque)
       OR NOT (NEW.D009_Quantidade_Estoque_Liquido <=> OLD.D009_Quantidade_Estoque_Liquido) THEN
        SELECT D049_D001_Id, D001A_Id
          INTO xD001_Id, xD001A_Id
          FROM D049
     LEFT JOIN D001A ON D001A_D001_Id = D049_D001_Id
         WHERE D049_Id = NEW.D009_D049_Id
         LIMIT 1;

        IF xD001_Id IS NOT NULL THEN
            IF xD001A_Id IS NULL THEN
                INSERT INTO D001A (D001A_D001_Id)
                VALUES (xD001_Id);

                SET xD001A_Id = LAST_INSERT_ID();
            END IF;

            SELECT IFNULL(SUM(D009_Quantidade_Estoque), 0),
                   IFNULL(SUM(D009_Quantidade_Estoque_Liquido), 0)
              INTO xQuantidade_Estoque, xQuantidade_Estoque_Liquido
              FROM D009
         INNER JOIN D049 ON D049_Id = D009_D049_Id
             WHERE D049_D001_Id = xD001_Id;

            UPDATE D001A
               SET D001A_Quantidade_Estoque_Consolidado = xQuantidade_Estoque,
                   D001A_Quantidade_Estoque_Liquido_Consolidado = xQuantidade_Estoque_Liquido
             WHERE D001A_Id = xD001A_Id;
        END IF;
    END IF;
END;
