<?php
declare(strict_types=1);

$base=dirname(__DIR__);
$flow=(string)file_get_contents($base.'/public/guide-draft-flow.php');
$readonly=(string)file_get_contents($base.'/public/guide-readonly-items.php');
$ui=(string)file_get_contents($base.'/public/guide-form.js');
$index=(string)file_get_contents($base.'/public/index.php');
$has=static function(string $text,string $needle):bool{return strpos($text,$needle)!==false;};
$checks=[
    'non_draft_uses_readonly_view'=>$has($flow,"elseif(\$rules): require __DIR__.'/guide-readonly-items.php'; endif;"),
    'readonly_marker'=>$has($readonly,'data-guide-draft-readonly-items'),
    'same_snapshot_form_is_rendered'=>$has($readonly,'data-guide-draft-form')&&$has($readonly,'GnreFormDefinitionBuilder'),
    'readonly_fieldset_is_disabled'=>$has($readonly,'<fieldset disabled class="guide-draft-readonly-fields">'),
    'readonly_has_no_post_or_edit_action'=>!$has($readonly,'method="post"')&&!$has($readonly,'guide_draft_save_item'),
    'shows_guide_payment_and_item_values'=>$has($readonly,'GNRE001_Data_Pagamento')&&$has($readonly,"'saved'=>\$readonlyItem"),
    'uses_generic_snapshot_definitions'=>$has($readonly,"'definitions'=>\$readonlyDefs")&&$has($readonly,"'guideDefinitions'=>\$readonlyGuideDefs"),
    'recipient_uf_is_reconstructed_from_persisted_city'=>$has($readonly,'GnreMunicipality::cityUfForCode')&&$has($readonly,"'recipient_state'"),
    'value_choice_is_reconstructed_from_gnre006'=>$has($ui,"values=(data.saved||{}).GNRE006||[]")&&$has($ui,"types.some(function(type){return type.charAt(0)===option.value.charAt(0);})"),
    'linked_document_description_is_loaded'=>$has($index,'d.GNRE002_Descricao'),
];
foreach($checks as $name=>$ok){if(!$ok){fwrite(STDERR,"FAIL: $name\n");exit(1);}}
echo "Task 26.5 issued guide read-only presentation: OK\n";
