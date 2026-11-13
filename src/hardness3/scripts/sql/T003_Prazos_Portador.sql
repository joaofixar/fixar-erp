CREATE FUNCTION `T003_Prazos_Portador`(xT003_Id int(11)) RETURNS varchar(255) CHARSET latin1
BEGIN
    DECLARE xPortador VARCHAR(500) DEFAULT '';

    SELECT GROUP_CONCAT(distinct D027_Portador SEPARATOR ', ')
      INTO xPortador
      FROM T089
 LEFT JOIN D027 ON D027_Id=T089_D027_Id     
     WHERE T089_T003_Id=xT003_Id;

    RETURN xPortador;
END
