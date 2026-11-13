CREATE FUNCTION `T005_Prazos_Portador`(xT005_Id int(11)) RETURNS varchar(255) CHARSET latin1
BEGIN
    DECLARE xPortador VARCHAR(500) DEFAULT '';

    SELECT GROUP_CONCAT(distinct D027_Portador SEPARATOR ', ')
      INTO xPortador
      FROM T090
 LEFT JOIN D027 ON D027_Id=T090_D027_Id     
     WHERE T090_T005_Id=xT005_Id;

    RETURN xPortador;
END
