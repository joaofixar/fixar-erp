<?php
namespace hardness;

class PdfConsistencyValidator
{
    public function validate(PdfFieldInferenceResult $inference, array $context = array())
    {
        $result = new PdfConsistencyResult();
        $item = $inference->item;
        $raw = strtolower((string) $item->rawText);
        $desc = strtolower(trim((string) $item->description));

        if (PdfParserUtils::isStandaloneNumberLine($item->rawText)) {
            $result->ignored = true;
            $result->reason = 'Linha numerica isolada ignorada.';
            return $result;
        }

        if ($desc === '' && trim((string) $item->codeClient) === '' && $item->quantity === null) {
            $result->ignored = true;
            $result->reason = 'Linha sem sinais de produto.';
            $result->missingFields = array('description', 'quantity');
            return $result;
        }

        if ($item->quantity === null
            && trim((string) $item->unit) === ''
            && preg_match('/^\d{1,4}\s+[A-Za-zÀ-ÿ]{1,10}(?:\s+[A-Za-zÀ-ÿ]{1,10})?$/u', PdfParserUtils::normalizeLine($item->rawText))) {
            $result->ignored = true;
            $result->reason = 'Linha curta sem estrutura de item.';
            $result->missingFields = array('quantity', 'unit');
            return $result;
        }

        if (PdfParserUtils::containsAny($raw, array('@', 'total', 'frete', 'pagamento')) && !PdfParserUtils::isProductRowCandidate($item->rawText)) {
            $result->ignored = true;
            $result->reason = 'Linha pertence a contato ou totalizacao.';
            return $result;
        }

        if (PdfParserUtils::isEmailLine($item->rawText) || PdfParserUtils::isPhoneLine($item->rawText)) {
            $result->ignored = true;
            $result->reason = 'Linha de contato ignorada.';
            return $result;
        }

        $score = (int) $inference->score;
        if ($item->quantity !== null) {
            $score += 8;
        } else {
            $score -= 10;
            $result->missingFields[] = 'quantity';
        }

        if (trim((string) $item->unit) !== '') {
            $score += 6;
        } else {
            $result->missingFields[] = 'unit';
        }

        if (trim((string) $item->description) !== '') {
            $score += min(10, (int) floor(strlen($item->description) / 20));
        } else {
            $score -= 25;
            $result->missingFields[] = 'description';
        }

        if (trim((string) $item->codeClient) !== '') {
            $score += 5;
        }

        if (preg_match('/^\d{2}\s+\d{4,}/u', $item->rawText)) {
            $score -= 25;
        }

        $score = max(0, min(100, $score));
        $result->score = $score;
        $result->accepted = $score >= 40 && trim((string) $item->description) !== '';
        $result->reason = $result->accepted ? 'Linha aceita como item.' : 'Linha descartada por baixa consistencia.';
        if (!$result->accepted) {
            $result->ignored = true;
        }

        return $result;
    }
}
