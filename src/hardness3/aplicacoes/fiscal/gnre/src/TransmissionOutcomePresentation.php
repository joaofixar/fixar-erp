<?php
declare(strict_types=1);

/**
 * Public, reload-safe presentation of a reception attempt.  This is purposely
 * independent of UF and never returns transport diagnostics or SOAP content.
 */
final class GnreTransmissionOutcomePresentation
{
    /** @param array<string,mixed>|null $attempt @return array{state:string,message:string,code:string,description:string} */
    public static function fromAttempt(?array $attempt, string $guideStatus): array
    {
        if (!$attempt) return self::none();
        $state=(string)($attempt['GNRE004_Estado']??'');
        $code=trim((string)($attempt['GNRE004_Codigo_Retorno']??''));
        $description=trim((string)($attempt['GNRE004_Descricao_Retorno']??''));
        if ($state==='REJEITADA') {
            $detail=trim($code.($code!==''&&$description!==''?' — ':'').$description);
            return ['state'=>'reception_rejected','message'=>'A recepção foi rejeitada pelo Portal.'.($detail!==''?' '.$detail.'.':''),'code'=>$code,'description'=>$description];
        }
        if ($state==='INCONCLUSIVA') return ['state'=>'technical_error','message'=>self::technicalMessage(),'code'=>'','description'=>''];
        /* CRIADA with no receipt is not a fresh draft: a request was begun and
           the system cannot prove reception.  Keep it visible and block resend. */
        if ($state==='CRIADA' && trim((string)($attempt['GNRE004_Recibo']??''))==='') return ['state'=>'technical_error','message'=>self::technicalMessage(),'code'=>'','description'=>''];
        if ($guideStatus==='I') return ['state'=>'technical_error','message'=>self::technicalMessage(),'code'=>'','description'=>''];
        return self::none();
    }

    /** @return array{state:string,message:string,code:string,description:string} */
    private static function none(): array { return ['state'=>'','message'=>'','code'=>'','description'=>'']; }
    public static function technicalMessage(): string { return 'Não foi possível confirmar a recepção da guia. A transmissão foi encerrada sem retransmissão automática. Consulte o Histórico para análise.'; }
}
