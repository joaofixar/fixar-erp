CREATE FUNCTION  `D007_Tipo_Registro`(xD007_Flag_Tipo_Registro INT(1)) RETURNS VARCHAR(20)
BEGIN
    IF xD007_Flag_Tipo_Registro = 1 THEN
       RETURN 'COM REGISTRO';
    END IF;   
    IF xD007_Flag_Tipo_Registro = 2 THEN
       RETURN 'SEM REGISTRO';
    END IF;
    IF xD007_Flag_Tipo_Registro = 3 THEN
       RETURN 'DDA';
    END IF;
    RETURN '';   
END