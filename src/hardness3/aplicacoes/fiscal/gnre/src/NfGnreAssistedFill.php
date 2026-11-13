<?php
declare(strict_types=1);

/**
 * Projection restricted to facts frozen in the authorized NF and to the
 * linked GNRE007 rules.  It deliberately has no customer-master lookup.
 */
final class GnreNfGnreAssistedFill
{
    private static function digits($value): string { return preg_replace('/\D/', '', (string)$value) ?? ''; }
    private static function date($value): string { $value=trim((string)$value); return preg_match('/^\d{4}-\d{2}-\d{2}$/',$value)?$value:''; }
    private static function emissionExtras(array $rule,string $emission): array
    {
        if($emission==='')return [];$matches=[];foreach((array)($rule['extras']??[]) as $extra){$title=mb_strtolower(trim((string)($extra['title']??'')),'UTF-8');$title=strtr($title,['á'=>'a','à'=>'a','â'=>'a','ã'=>'a','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u','ç'=>'c']);$code=(string)($extra['code']??'');if($code!==''&&strpos($title,'data de emissao')!==false)$matches[]=['codigo'=>(int)$code,'valor'=>$emission];}return count($matches)===1?$matches:[];
    }
    private static function nfeDocumentType(array $rule,array $note): string
    {
        /* The option code is never guessed: retain the code carried by the
           immutable snapshot only when its official description identifies NF-e
           and the persisted model/key is compatible. */
        $key=self::digits($note['T007_Chave_Acesso_Nfe']??'');
        $model=trim((string)($note['T007_Origem_Modelo']??''));
        if(strlen($key)!==44 || ($model!=='' && $model!=='55')) return '';
        $matches=[];foreach((array)($rule['document_types']??[]) as $option) {
            $description=mb_strtolower(trim((string)($option['description']??'')),'UTF-8');
            $code=trim((string)($option['code']??''));
            if($code!==''&&(strpos($description,'nota fiscal')!==false||strpos($description,'chave da nfe')!==false||strpos($description,'chave nfe')!==false||strpos($description,'chave do dfe')!==false))$matches[$code]=true;
        }
        return count($matches)===1?(string)array_key_first($matches):'';
    }
    /** Pure mapping used both by persistence and behavioral tests. */
    public static function project(array $note,array $rule,string $today): array
    {
        $fields=(array)($rule['fields']??[]); $emission=self::date($note['T007_Data_Emissao']??'');
        $key=self::digits($note['T007_Chave_Acesso_Nfe']??'');
        $documentType=($fields['document']??'N')==='S' ? self::nfeDocumentType($rule,$note) : '';
        $id=self::digits($note['T007_Cliente_CNPJ_CPF']??'');
        $recipientType=strlen($id)===14?'CNPJ':(strlen($id)===11?'CPF':'');
        $uf=strtoupper(trim((string)($note['T007_Cliente_Estado']??'')));
        $ibge=self::digits($note['T007_Cliente_Codigo_Cidade_IBGE']??'');
        $recipient=[];
        if(($fields['recipient']??'N')!=='N' && $recipientType!=='' && preg_match('/^[A-Z]{2}$/',$uf) && preg_match('/^\d{7}$/',$ibge)) {
            $recipient=['type'=>$recipientType,'id'=>$id,'name'=>mb_substr(trim((string)($note['T007_Cliente_Razao']??'')),0,60,'UTF-8'),'ibge'=>$ibge,'city'=>GnreMunicipality::gnreCode($ibge)];
            if($recipient['name']==='' || $recipient['city']==='') $recipient=[];
        }
        return [
            'payment_date'=>($fields['payment_date']??'N')==='S'?$today:'',
            'document_type'=>$documentType,
            'document_number'=>$documentType!==''?$key:'',
            'fecp_allowed'=>($fields['fecp']??'N')!=='N',
            'month'=>(($fields['reference']??'N')==='S'&&$emission!=='')?substr($emission,5,2):'',
            'year'=>(($fields['reference']??'N')==='S'&&$emission!=='')?substr($emission,0,4):'',
            'recipient'=>$recipient,
            'extras'=>self::emissionExtras($rule,$emission)
        ];
    }

    /** Reads only the locked NF and the snapshot already linked to this guide. */
    public static function projectionForGuide(mysqli $db,int $guideId): array
    {
        $q=mysqli_prepare($db,'SELECT n.*,g.GNRE001_Codigo_Receita,s.GNRE007_Regras_JSON FROM GNRE001 g INNER JOIN GNRE002 d ON d.GNRE002_GNRE001_Id=g.GNRE001_Id INNER JOIN T007 n ON n.T007_Id=d.GNRE002_T007_Id INNER JOIN GNRE007 s ON s.GNRE007_Id=g.GNRE001_GNRE007_Id WHERE g.GNRE001_Id=? AND d.GNRE002_T007_Id IS NOT NULL LIMIT 1');
        if(!$q)throw new RuntimeException('Não foi possível reler os fatos da NF para preenchimento assistido.');mysqli_stmt_bind_param($q,'i',$guideId);mysqli_stmt_execute($q);$r=mysqli_stmt_get_result($q);$row=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($q);if(!$row)throw new RuntimeException('NF vinculada não encontrada durante preenchimento assistido.');
        try{$rules=json_decode((string)$row['GNRE007_Regras_JSON'],true,512,JSON_THROW_ON_ERROR);}catch(Throwable $e){throw new RuntimeException('Snapshot oficial inválido durante preenchimento assistido.');}
        $rule=GnreConfigUfRules::receipt((array)$rules,(string)$row['GNRE001_Codigo_Receita']);if(!$rule)throw new RuntimeException('Receita ausente no snapshot oficial vinculado.');
        return self::project($row,$rule,(new DateTimeImmutable('today',new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d'));
    }

    /** Applies only to NF-linked assistant drafts, still inside their transaction. */
    public static function apply(mysqli $db,int $guideId): void
    {
        $q=mysqli_prepare($db,'SELECT n.*,g.GNRE001_Codigo_Receita,s.GNRE007_Regras_JSON FROM GNRE001 g INNER JOIN GNRE002 d ON d.GNRE002_GNRE001_Id=g.GNRE001_Id INNER JOIN T007 n ON n.T007_Id=d.GNRE002_T007_Id INNER JOIN GNRE007 s ON s.GNRE007_Id=g.GNRE001_GNRE007_Id WHERE g.GNRE001_Id=? AND d.GNRE002_T007_Id IS NOT NULL LIMIT 1');
        if(!$q)throw new RuntimeException('Não foi possível reler os fatos da NF para preenchimento assistido.');mysqli_stmt_bind_param($q,'i',$guideId);mysqli_stmt_execute($q);$r=mysqli_stmt_get_result($q);$row=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($q);if(!$row)return;
        try{$rules=json_decode((string)$row['GNRE007_Regras_JSON'],true,512,JSON_THROW_ON_ERROR);}catch(Throwable $e){throw new RuntimeException('Snapshot oficial inválido durante preenchimento assistido.');}
        $receipt=(string)$row['GNRE001_Codigo_Receita'];$rule=GnreConfigUfRules::receipt((array)$rules,$receipt);if(!$rule)throw new RuntimeException('Receita ausente no snapshot oficial vinculado.');
        $today=(new DateTimeImmutable('today',new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d');$fill=self::project($row,$rule,$today);$recipient=(array)$fill['recipient'];$rtype=(string)($recipient['type']??'');$rid=(string)($recipient['id']??'');$name=(string)($recipient['name']??'');$city=(string)($recipient['city']??'');$extras=$recipient?GnreMunicipality::encodeExtras([], (string)$recipient['ibge']):'';
        $payment=(string)$fill['payment_date'];$q=mysqli_prepare($db,"UPDATE GNRE001 SET GNRE001_Data_Pagamento=NULLIF(?, '') WHERE GNRE001_Id=?");if(!$q)throw new RuntimeException('Não foi possível preparar a data de pagamento.');mysqli_stmt_bind_param($q,'si',$payment,$guideId);if(!mysqli_stmt_execute($q))throw new RuntimeException('Não foi possível gravar a data de pagamento.');mysqli_stmt_close($q);
        $docNumber=(string)$fill['document_number'];$q=mysqli_prepare($db,"UPDATE GNRE002 SET GNRE002_Identificacao=NULLIF(?, '') WHERE GNRE002_GNRE001_Id=? AND GNRE002_T007_Id IS NOT NULL");if(!$q)throw new RuntimeException('Não foi possível preparar o documento de origem.');mysqli_stmt_bind_param($q,'si',$docNumber,$guideId);if(!mysqli_stmt_execute($q))throw new RuntimeException('Não foi possível gravar o documento de origem.');mysqli_stmt_close($q);
        $docType=(string)$fill['document_type'];$month=(string)$fill['month'];$year=(string)$fill['year'];$q=mysqli_prepare($db,"UPDATE GNRE003 SET GNRE003_Tipo_Documento_Origem=NULLIF(?,''),GNRE003_Referencia_Mes=NULLIF(?,''),GNRE003_Referencia_Ano=NULLIF(?,''),GNRE003_Destinatario_Tipo_Identificacao=NULLIF(?,''),GNRE003_Destinatario_Identificacao=NULLIF(?,''),GNRE003_Destinatario_Razao_Social=NULLIF(?,''),GNRE003_Destinatario_Codigo_Municipio=NULLIF(?,''),GNRE003_Campos_Extras_JSON=NULLIF(?, '') WHERE GNRE003_GNRE001_Id=?");if(!$q)throw new RuntimeException('Não foi possível preparar os campos assistidos.');mysqli_stmt_bind_param($q,'ssssssssi',$docType,$month,$year,$rtype,$rid,$name,$city,$extras,$guideId);if(!mysqli_stmt_execute($q))throw new RuntimeException('Não foi possível gravar os campos assistidos.');mysqli_stmt_close($q);
    }
}
