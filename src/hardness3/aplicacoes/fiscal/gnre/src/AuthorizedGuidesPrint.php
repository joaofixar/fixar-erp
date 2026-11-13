<?php
declare(strict_types=1);

/** Combina somente documentos oficiais já persistidos; nunca chama o Portal. */
final class GnreAuthorizedGuidesPrint
{
    /** @var callable */ private $loadDocument;

    public function __construct(?callable $loadDocument = null)
    {
        $this->loadDocument = $loadDocument ?: static function (int $companyId, int $guideId, int $documentId): ?array {
            return GnreOfficialPdfService::download($companyId, $guideId, $documentId);
        };
    }

    /** @param array<int,array{guide_id:int,document_id:int}> $documents */
    public function render(int $companyId, array $documents): string
    {
        if (!$documents) throw new RuntimeException('Selecione ao menos uma guia autorizada para impressão.');
        $paths=[];
        try {
            foreach ($documents as $entry) {
                $guideId=(int)($entry['guide_id']??0);$documentId=(int)($entry['document_id']??0);
                if($guideId<1||$documentId<1) throw new RuntimeException('Não foi possível preparar a impressão de uma guia selecionada.');
                $document=call_user_func($this->loadDocument,$companyId,$guideId,$documentId);
                $content=(string)($document['GNRE008_Conteudo']??'');
                if(!$document||substr($content,0,5)!=='%PDF-') throw new RuntimeException('Não foi possível preparar a impressão da guia #'.$guideId.'.');
                $path=tempnam(sys_get_temp_dir(),'gnre-print-');
                if($path===false||file_put_contents($path,$content)===false){if($path!==false)@unlink($path);throw new RuntimeException('Não foi possível preparar a impressão da guia #'.$guideId.'.');}
                $paths[]=['guide_id'=>$guideId,'path'=>$path];
            }
            $root=GNRE_ROOT.'/bibliotecas/PDFMerger';
            set_include_path($root.'/fpdi'.PATH_SEPARATOR.$root.'/fpdf'.PATH_SEPARATOR.get_include_path());
            require_once $root.'/fpdf/fpdf.php';
            require_once $root.'/fpdi/fpdi.php';
            $pdf=new FPDI();
            foreach($paths as $entry){
                try {$count=$pdf->setSourceFile($entry['path']);for($page=1;$page<=$count;$page++){$template=$pdf->importPage($page);$size=$pdf->getTemplateSize($template);$pdf->AddPage('P',[$size['w'],$size['h']]);$pdf->useTemplate($template);}}
                catch(Throwable $e){throw new RuntimeException('Não foi possível preparar a impressão da guia #'.$entry['guide_id'].'.');}
            }
            $merged=(string)$pdf->Output('impressao-guias-autorizadas.pdf','S');
            if(substr($merged,0,5)!=='%PDF-') throw new RuntimeException('Não foi possível preparar a impressão das guias autorizadas.');
            return $merged;
        } finally { foreach($paths as $entry)@unlink($entry['path']); }
    }
}
