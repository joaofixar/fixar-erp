CREATE FUNCTION `D009_IPV_ICMS`(xC004_Id INT(11), xD024_Id INT(11), xD009_Id INT(11), xD006_Id INT(11), xD018_Id INT(11), xFlag_Revenda_Consumidor VARCHAR(1), xComissao DECIMAL(10,2), xDocumento INT(1), xTipo INT(1)) RETURNS DECIMAL(10,4)
BEGIN
    DECLARE xFlag_IPV_Cliente VARCHAR(1);
    DECLARE xAliquota_ICMS DECIMAL(10,4);
    DECLARE xPercentual_Reducao_ICMS DECIMAL(10,4);
    DECLARE xAliquota_IPV_Cliente DECIMAL(10,4);
    DECLARE xAliquota_IPV_Utilizado DECIMAL(10,4);
    DECLARE xC004_Flag_Isento_ICMS INT(1);
    DECLARE xC004_UF VARCHAR(2);
    DECLARE xD009_Aliquota_ICMS_Venda DECIMAL(6,2);
    DECLARE xD009_Percentual_Reducao_ICMS_Venda DECIMAL(6,2);
    DECLARE xD009_Flag_Isento_ICMS_Venda INT(1);
    DECLARE xD006_Flag_Isento_ICMS VARCHAR(1);
    DECLARE xD006_Flag_Venda_Compra_Outros VARCHAR(1);
    DECLARE xD006_Percentual_Reducao_ICMS DECIMAL(6,2) DEFAULT 0;
    DECLARE xD024_Inscricao_Estadual VARCHAR(20);
    DECLARE xD024_Flag_Pessoa_Fisica_Juridica CHAR(1);
    DECLARE xD024_Valor_IPV DECIMAL(10,4);
    DECLARE xD024_Flag_Revenda_Consumidor VARCHAR(1);
    DECLARE xD018_UF VARCHAR(2);
    DECLARE xD018_Aliquota_ICMS_1 DECIMAL(5,2);
    DECLARE xD018_Percentual_Reducao_ICMS_1 DECIMAL(5,2);
    DECLARE xD018_Aliquota_ICMS_2 DECIMAL(5,2);
    DECLARE xD018_Percentual_Reducao_ICMS_2 DECIMAL(5,2);
    DECLARE xD018_Aliquota_ICMS_Revenda DECIMAL(5,2);
    DECLARE xD018_Percentual_Reducao_ICMS_Revenda DECIMAL(5,2);
    DECLARE xD018_Aliquota_ICMS_Consumidor DECIMAL(5,2);
    DECLARE xD018_Percentual_Reducao_ICMS_Consumidor DECIMAL(5,2);
    DECLARE xD020_Flag_ICMS VARCHAR(1);
    DECLARE xD019_IPV_Maximo DECIMAL(10,4);
    DECLARE xD024_Percentual_IPV_Consumidor DECIMAL(8,4);
    DECLARE xD024_Percentual_IPV_Fornecedor_1 DECIMAL(8,4);
    DECLARE xD024_Percentual_IPV_Fornecedor_2 DECIMAL(8,4);
    DECLARE xD024_Percentual_IPV_Fornecedor_3 DECIMAL(8,4);
    DECLARE xD024_Flag_Isento_ICMS INT(1);
    DECLARE xD024_Flag_Isento_ICMS_Venda ENUM('S','N');
    DECLARE xD024_Percentual_Reducao_ICMS DECIMAL(5,2);
    DECLARE xD024_Aliquota_ICMS DECIMAL(5,2);
    DECLARE xD019_Tipo_1 DECIMAL(10,2);
    DECLARE xD019_Tipo_2 DECIMAL(10,2);
    DECLARE xD019_Percentual_Desconto_ACP DECIMAL(10,2);
    DECLARE xTabelado INT(10);
    DECLARE xD049_Id INT(10);
    SET xAliquota_ICMS=0;
    SET xPercentual_Reducao_ICMS=0;
    SET xAliquota_IPV_Cliente=0;
    SET xAliquota_IPV_Utilizado=0;
    SET xD024_Inscricao_Estadual='';
    SET xD024_Flag_Pessoa_Fisica_Juridica='';
    SET xD024_Valor_IPV=0;
    SET xD024_Flag_Isento_ICMS='';
    SET xD024_Flag_Isento_ICMS_Venda='N';
    SET xD024_Percentual_Reducao_ICMS=0;
    SET xD024_Aliquota_ICMS=0;
    SET xD020_Flag_ICMS='';
    SET xD006_Flag_Isento_ICMS='';
    SET xD049_Id=0;
    SELECT C004_Uf,
           C004_Flag_Isento_ICMS
      INTO xC004_UF,
           xC004_Flag_Isento_ICMS
      FROM C004
     WHERE C004_Id=xC004_Id;
    IF xD024_Id>0 THEN
        SELECT D024_Inscricao_Estadual,
              D024_Flag_Pessoa_Fisica_Juridica,
              D024_Valor_IPV,
              D024_Flag_Revenda_Consumidor,
              D024_Flag_Isento_ICMS,
              D024_Flag_Isento_ICMS_Venda,
              D024_Percentual_Reducao_ICMS,
              D024_Aliquota_ICMS,
              D053_Aliquota_ICMS_1,
              D053_Percentual_Reducao_ICMS_1,
              D053_Aliquota_ICMS_2,
              D053_Percentual_Reducao_ICMS_2,
              D053_Aliquota_ICMS_Revenda,
              D053_Percentual_Reducao_ICMS_Revenda,
              D053_Aliquota_ICMS_Consumidor,
              D053_Percentual_Reducao_ICMS_Consumidor,
              D020_Flag_ICMS,
              D018_UF
         INTO xD024_Inscricao_Estadual,
              xD024_Flag_Pessoa_Fisica_Juridica,
              xD024_Valor_IPV,
              xD024_Flag_Revenda_Consumidor,
              xD024_Flag_Isento_ICMS,
              xD024_Flag_Isento_ICMS_Venda,
              xD024_Percentual_Reducao_ICMS,
              xD024_Aliquota_ICMS,
              xD018_Aliquota_ICMS_1,
              xD018_Percentual_Reducao_ICMS_1,
              xD018_Aliquota_ICMS_2,
              xD018_Percentual_Reducao_ICMS_2,
              xD018_Aliquota_ICMS_Revenda,
              xD018_Percentual_Reducao_ICMS_Revenda,
              xD018_Aliquota_ICMS_Consumidor,
              xD018_Percentual_Reducao_ICMS_Consumidor,
              xD020_Flag_ICMS,
              xD018_UF
              FROM D024
              LEFT JOIN D020 ON D020_Id=D024_D020_Id
              LEFT JOIN D018 ON D018_Id=D024_D018_Id
              LEFT JOIN D053 ON D053_D018_Id=D018_Id AND D053_C004_Id=xC004_Id
              WHERE D024_Id=xD024_Id LIMIT 1;
    ELSEIF xD018_Id>0 THEN
        SET xD024_Flag_Revenda_Consumidor=xFlag_Revenda_Consumidor;
        SELECT D053_Aliquota_ICMS_1,
              D053_Percentual_Reducao_ICMS_1,
              D053_Aliquota_ICMS_2,
              D053_Percentual_Reducao_ICMS_2,
              D053_Aliquota_ICMS_Revenda,
              D053_Percentual_Reducao_ICMS_Revenda,
              D053_Aliquota_ICMS_Consumidor,
              D053_Percentual_Reducao_ICMS_Consumidor,
              D018_UF
          INTO xD018_Aliquota_ICMS_1,
              xD018_Percentual_Reducao_ICMS_1,
              xD018_Aliquota_ICMS_2,
              xD018_Percentual_Reducao_ICMS_2,
              xD018_Aliquota_ICMS_Revenda,
              xD018_Percentual_Reducao_ICMS_Revenda,
              xD018_Aliquota_ICMS_Consumidor,
              xD018_Percentual_Reducao_ICMS_Consumidor,
              xD018_UF
              FROM D018
              LEFT JOIN D053 ON D053_D018_Id=D018_Id AND D053_C004_Id=xC004_Id
              WHERE D018_Id=xD018_Id
              LIMIT 1;
    END IF;
    IF xD006_Id>0 THEN
       SELECT D006_Flag_Isento_ICMS,
              D006_Flag_Venda_Compra_Outros,
              D006_Percentual_Reducao_ICMS
         INTO xD006_Flag_Isento_ICMS,
              xD006_Flag_Venda_Compra_Outros,
              xD006_Percentual_Reducao_ICMS
         FROM D006
        WHERE D006_Id=xD006_Id;
    END IF;
    IF xD009_Id>0 THEN
       SELECT D009_Aliquota_ICMS_Venda,
              D009_Percentual_Reducao_ICMS_Venda,
              D009_Flag_Isento_ICMS_Venda,
              D009_D049_Id
         INTO xD009_Aliquota_ICMS_Venda,
              xD009_Percentual_Reducao_ICMS_Venda,
              xD009_Flag_Isento_ICMS_Venda,
              xD049_Id
              FROM D049
              LEFT JOIN D009 ON D009_D049_Id=D049_Id
              WHERE D009_Id=xD009_Id
              AND D009_C004_Id=xC004_Id;
       IF xD006_Flag_Venda_Compra_Outros!='T' THEN
          SELECT T046_Id
            INTO xTabelado
            FROM T046
            LEFT JOIN T016 ON T016_Id=T046_T016_Id
            WHERE T046_D049_Id=xD049_Id
              AND CURRENT_DATE()>=T016_Data_Inicio
              AND CURRENT_DATE()<=T016_Data_Final
            LIMIT 1;
            IF xTabelado IS NULL THEN
              SELECT D041_Id
                      INTO xTabelado
                FROM D041
                WHERE D041_D049_Id=xD049_Id
                LIMIT 1;
            END IF;
        END IF;
    END IF;

       IF xC004_Flag_Isento_ICMS=1 OR xD006_Flag_Isento_ICMS='S' OR xD020_Flag_ICMS='S' OR xD009_Flag_Isento_ICMS_Venda=1 THEN
          SET xAliquota_ICMS=0;
          SET xPercentual_Reducao_ICMS=0;
       ELSEIF xD006_Flag_Venda_Compra_Outros='V' AND xD024_Flag_Isento_ICMS_Venda='S' THEN
          SET xAliquota_ICMS=0;
          SET xPercentual_Reducao_ICMS=0;
       ELSEIF xD006_Flag_Venda_Compra_Outros='C' AND xD024_Flag_Isento_ICMS='1' THEN
          SET xAliquota_ICMS=0;
          SET xPercentual_Reducao_ICMS=0;
       ELSEIF xD009_Aliquota_ICMS_Venda>0 THEN
          SET xAliquota_ICMS=xD009_Aliquota_ICMS_Venda;
          SET xPercentual_Reducao_ICMS=xD009_Percentual_Reducao_ICMS_Venda;
       ELSEIF xD024_Aliquota_ICMS>0 THEN
          SET xAliquota_ICMS=xD024_Aliquota_ICMS;
          SET xPercentual_Reducao_ICMS=xD024_Percentual_Reducao_ICMS;
       ELSEIF xD024_Flag_Revenda_Consumidor='1' AND xD018_Aliquota_ICMS_Revenda>0 THEN
          SET xAliquota_ICMS=xD018_Aliquota_ICMS_Revenda;
          SET xPercentual_Reducao_ICMS=xD018_Percentual_Reducao_ICMS_Revenda;
       ELSEIF xD024_Flag_Revenda_Consumidor!='1' AND xD018_Aliquota_ICMS_Consumidor>0 THEN
          SET xAliquota_ICMS=xD018_Aliquota_ICMS_Consumidor;
          SET xPercentual_Reducao_ICMS=xD018_Percentual_Reducao_ICMS_Consumidor;
       ELSEIF xD024_Id<=0 AND xD024_Flag_Revenda_Consumidor='1' THEN
          SET xAliquota_ICMS=xD018_Aliquota_ICMS_2;
          SET xPercentual_Reducao_ICMS=xD018_Percentual_Reducao_ICMS_1;
       ELSEIF xD024_Id<=0 AND xD024_Flag_Revenda_Consumidor!='1' THEN
          SET xAliquota_ICMS=xD018_Aliquota_ICMS_1;
          SET xPercentual_Reducao_ICMS=xD018_Percentual_Reducao_ICMS_2;
       ELSEIF xD024_Inscricao_Estadual='' OR xD024_Inscricao_Estadual='ISENTO' OR  xD024_Flag_Pessoa_Fisica_Juridica='F' THEN
          SET xAliquota_ICMS=xD018_Aliquota_ICMS_1;
          SET xPercentual_Reducao_ICMS=xD018_Percentual_Reducao_ICMS_2;
       ELSEIF (xD024_Inscricao_Estadual!='' AND xD024_Inscricao_Estadual!='ISENTO') OR  xD024_Flag_Pessoa_Fisica_Juridica='J' THEN
          SET xAliquota_ICMS=xD018_Aliquota_ICMS_2;
          SET xPercentual_Reducao_ICMS=xD018_Percentual_Reducao_ICMS_1;
       END IF;
       IF xD024_Valor_IPV>0 THEN
          SET xAliquota_IPV_Cliente=xD024_Valor_IPV;
          SET xFlag_IPV_Cliente='S';
          SELECT D019_Tipo_1,D019_Percentual_Desconto_ACP
            INTO xD019_Tipo_1,xD019_Percentual_Desconto_ACP
            FROM D019
           WHERE D019_Aliquota_ICMS=xAliquota_ICMS;
       ELSE
          IF xD024_Flag_Revenda_Consumidor!='1' THEN
             SELECT D019_IPV_Maximo,D019_Tipo_1,D019_Percentual_Desconto_ACP
               INTO xD019_IPV_Maximo,xD019_Tipo_1,xD019_Percentual_Desconto_ACP
               FROM D019
              WHERE D019_Aliquota_ICMS=0;
          ELSE
             SELECT D019_IPV_Maximo,D019_Tipo_1,D019_Percentual_Desconto_ACP
               INTO xD019_IPV_Maximo,xD019_Tipo_1,xD019_Percentual_Desconto_ACP
               FROM D019
              WHERE D019_Aliquota_ICMS=xAliquota_ICMS;
          END IF;
          SET xAliquota_IPV_Cliente=xD019_IPV_Maximo;
          SET xFlag_IPV_Cliente='N';
       END IF;
       IF xDocumento=3 THEN
          SET xAliquota_ICMS=0;
          SET xPercentual_Reducao_ICMS=0;
       END IF;
       IF xD009_Id>0 THEN
          SELECT D024_Percentual_IPV_Consumidor,
                 D024_Percentual_IPV_Fornecedor_1,
                 D024_Percentual_IPV_Fornecedor_2,
                 D024_Percentual_IPV_Fornecedor_3
            INTO xD024_Percentual_IPV_Consumidor,
                 xD024_Percentual_IPV_Fornecedor_1,
                 xD024_Percentual_IPV_Fornecedor_2,
                 xD024_Percentual_IPV_Fornecedor_3
                 FROM D009
                 LEFT JOIN D049 ON D009_D049_Id=D049_Id
                 LEFT JOIN D024 ON D049_D024_Id=D024_Id
                 WHERE D009_Id=xD009_Id
                 AND D009_C004_Id=xC004_Id;
          IF xFlag_IPV_Cliente='N' THEN
             IF xD024_Flag_Revenda_Consumidor!='1' THEN
                 SET xAliquota_IPV_Utilizado=xD024_Percentual_IPV_Consumidor;
             ELSEIF xAliquota_ICMS=7 THEN
                 SET xAliquota_IPV_Utilizado=xD024_Percentual_IPV_Fornecedor_1;
             ELSEIF xAliquota_ICMS=12 THEN
                 SET xAliquota_IPV_Utilizado=xD024_Percentual_IPV_Fornecedor_2;
             ELSEIF xAliquota_ICMS=17 OR xAliquota_ICMS=18 THEN
                 SET xAliquota_IPV_Utilizado=xD024_Percentual_IPV_Fornecedor_3;
             END IF;
          END IF;
          IF xAliquota_IPV_Utilizado=0 THEN
             SET xAliquota_IPV_Utilizado=xAliquota_IPV_Cliente;
          END IF;
       ELSE
           SET xAliquota_IPV_Utilizado=xAliquota_IPV_Cliente;
       END IF;
       IF xComissao>0 THEN
          SET xD019_Tipo_2 = xAliquota_IPV_Utilizado / ( 100 / (100 - xD019_Tipo_1) );
          SET xD019_Tipo_2 = ((xD019_Tipo_2 - 1) * 100) / xD019_Tipo_2;
          SET xD019_Tipo_2 = ( 100 / ( 100 - ( xD019_Tipo_2 + xComissao) ) );
          SET xD019_Tipo_1 = ( 100 / ( 100 - ( xD019_Tipo_1 ) ) );
          SET xAliquota_IPV_Utilizado = ROUND(xD019_Tipo_1 * xD019_Tipo_2,4);
       END IF;
       IF xDocumento=3 THEN
          SET xAliquota_IPV_Utilizado = xAliquota_IPV_Utilizado - (xAliquota_IPV_Utilizado * ((xD019_Percentual_Desconto_ACP*2)/100));
       ELSE
          IF xD006_Percentual_Reducao_ICMS>0 THEN
             SET xPercentual_Reducao_ICMS=xD006_Percentual_Reducao_ICMS;
          END IF;
          IF xC004_Flag_Isento_ICMS=1 OR xD006_Flag_Isento_ICMS='S' OR xD020_Flag_ICMS='S' OR xD009_Flag_Isento_ICMS_Venda=1 THEN
             SET xAliquota_ICMS=0;
             SET xPercentual_Reducao_ICMS=0;
          END IF;
          IF xTipo=1 AND xTabelado>0 THEN
              IF xD024_Flag_Revenda_Consumidor!='1' THEN
                SELECT D019_IPV_Maximo
                  INTO xAliquota_IPV_Utilizado
                  FROM D019
                  WHERE D019_Aliquota_ICMS=0;
              ELSE
                SELECT D019_IPV_Maximo
                  INTO xAliquota_IPV_Utilizado
                  FROM D019
                  WHERE D019_Aliquota_ICMS=xAliquota_ICMS;
              END IF;
          END IF;
       END IF;
       IF xTipo=1 THEN
           RETURN xAliquota_IPV_Utilizado;
       ELSEIF xTipo=2 THEN
          RETURN xAliquota_ICMS;
       ELSEIF xTipo=3 THEN
          RETURN xPercentual_Reducao_ICMS;
       END IF;
END;