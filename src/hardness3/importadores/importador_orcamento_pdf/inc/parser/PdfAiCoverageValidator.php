<?php
namespace hardness;

class PdfAiCoverageValidator
{
    public function validate(array $coverageSnapshot, array $aiResult, array $metadata = array())
    {
        $items = isset($aiResult['items']) && is_array($aiResult['items']) ? $aiResult['items'] : array();
        $itemsReturned = count($items);
        $enumerationEstimate = isset($coverageSnapshot['enumeration_estimate']) ? $coverageSnapshot['enumeration_estimate'] : null;
        $candidateLinesEstimate = isset($coverageSnapshot['candidate_lines_estimate']) ? (int) $coverageSnapshot['candidate_lines_estimate'] : null;

        $emptyDescriptionCount = 0;
        $nullQuantityCount = 0;
        $emptyUnitCount = 0;
        $duplicateExactCount = 0;
        $consecutiveDuplicateRun = 1;
        $maxConsecutiveDuplicateRun = 1;
        $lastSignature = null;

        foreach ($items as $item) {
            $description = trim((string) (isset($item['description']) ? $item['description'] : ''));
            $quantity = isset($item['quantity']) ? $item['quantity'] : null;
            $unit = trim((string) (isset($item['unit']) ? $item['unit'] : ''));
            $signature = md5($description . '|' . ($quantity === null ? 'null' : (string) $quantity) . '|' . $unit);

            if ($description === '') {
                $emptyDescriptionCount++;
            }
            if ($quantity === null || $quantity === '') {
                $nullQuantityCount++;
            }
            if ($unit === '') {
                $emptyUnitCount++;
            }

            if ($lastSignature !== null && $signature === $lastSignature) {
                $duplicateExactCount++;
                $consecutiveDuplicateRun++;
                if ($consecutiveDuplicateRun > $maxConsecutiveDuplicateRun) {
                    $maxConsecutiveDuplicateRun = $consecutiveDuplicateRun;
                }
            } else {
                $consecutiveDuplicateRun = 1;
            }

            $lastSignature = $signature;
        }

        $reasons = array();
        $warnings = array();

        if ($itemsReturned === 0) {
            $reasons[] = 'items_returned_zero';
        }

        if ($itemsReturned > 0 && ($emptyDescriptionCount / $itemsReturned) > 0.10) {
            $reasons[] = 'empty_description_ratio_gt_10_percent';
        }

        if ($itemsReturned > 0 && ($nullQuantityCount / $itemsReturned) > 0.50) {
            $reasons[] = 'null_quantity_ratio_gt_50_percent';
        } elseif ($itemsReturned > 0 && ($nullQuantityCount / $itemsReturned) > 0.30) {
            $warnings[] = 'null_quantity_ratio_gt_30_percent';
        }

        if ($itemsReturned > 0 && ($emptyUnitCount / $itemsReturned) > 0.70) {
            $warnings[] = 'empty_unit_ratio_gt_70_percent';
        }

        if ($maxConsecutiveDuplicateRun >= 3) {
            $reasons[] = 'consecutive_duplicate_run_gte_3';
        } elseif ($itemsReturned > 0 && ($duplicateExactCount / $itemsReturned) > 0.20) {
            $reasons[] = 'duplicate_exact_ratio_gt_20_percent';
        }

        if ($this->hasStrongEnumerationMismatch($itemsReturned, $enumerationEstimate)) {
            $reasons[] = 'enumeration_mismatch_strong';
        }

        if ($this->hasStrongCandidateLinesMismatch($itemsReturned, $candidateLinesEstimate)) {
            $reasons[] = 'candidate_lines_mismatch_strong';
        }

        if (!empty($metadata['total_items_detected']) && $itemsReturned > 0 && (int) $metadata['total_items_detected'] > $itemsReturned) {
            $warnings[] = 'ai_total_items_detected_gt_items_returned';
        }
        if (isset($metadata['extraction_complete']) && !$metadata['extraction_complete']) {
            $warnings[] = 'ai_extraction_complete_false';
        }

        $decision = empty($reasons) ? 'accept' : 'fallback_block';

        return array(
            'decision' => $decision,
            'reasons' => array_values(array_unique($reasons)),
            'metrics' => array(
                'items_returned' => $itemsReturned,
                'enumeration_estimate' => $enumerationEstimate,
                'candidate_lines_estimate' => $candidateLinesEstimate,
                'empty_description_count' => $emptyDescriptionCount,
                'null_quantity_count' => $nullQuantityCount,
                'empty_unit_count' => $emptyUnitCount,
                'duplicate_exact_count' => $duplicateExactCount,
                'max_consecutive_duplicate_run' => $maxConsecutiveDuplicateRun,
                'coverage_ratio_vs_enumeration' => $this->safeRatio($itemsReturned, $enumerationEstimate),
                'coverage_ratio_vs_candidates' => $this->safeRatio($itemsReturned, $candidateLinesEstimate),
            ),
            'warnings' => array_values(array_unique($warnings)),
        );
    }

    protected function hasStrongEnumerationMismatch($itemsReturned, $enumerationEstimate)
    {
        if ($enumerationEstimate === null || $enumerationEstimate <= 0) {
            return false;
        }

        return $itemsReturned < ($enumerationEstimate * 0.70)
            && (($enumerationEstimate - $itemsReturned) >= 5);
    }

    protected function hasStrongCandidateLinesMismatch($itemsReturned, $candidateLinesEstimate)
    {
        if ($candidateLinesEstimate === null || $candidateLinesEstimate <= 0) {
            return false;
        }

        return $itemsReturned < ($candidateLinesEstimate * 0.60)
            && (($candidateLinesEstimate - $itemsReturned) >= 5);
    }

    protected function safeRatio($numerator, $denominator)
    {
        if ($denominator === null || (float) $denominator <= 0.0) {
            return null;
        }

        return round(((float) $numerator / (float) $denominator), 4);
    }
}
