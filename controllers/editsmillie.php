<?php

/*id Smillies Edit*/
if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$code_smillie = ( isset ($PVARS['code_smillie']) ) ? $PVARS['code_smillie'] : array();
$del_smillie = ( isset ($PVARS['del_smillie']) ) ? $PVARS['del_smillie'] : array();
$smiley_image = ( isset ($PVARS['smiley_image']) ) ? $PVARS['smiley_image'] : array();

function is_empty ( $str ) { return empty ($str); }

$code_smillie = array_map ('single_line',
    array_map ('fn_trim', $code_smillie)
);
$smiley_image = array_map ('single_line',
    array_map ('fn_trim', $smiley_image)
);
$blank_smileys = array_filter ($code_smillie, 'is_empty');
if ( count ($blank_smileys) > 0 )
{
    trigger_error ('One or more smileys have an empty BBCode field.', E_USER_WARNING);
}

$delete_ids = array_keys ($del_smillie);
foreach ( $delete_ids as $smiley_id )
{
    unset ($code_smillie[$smiley_id]);
    unset ($smiley_image[$smiley_id]);
}

$smileys = array();
foreach ( $code_smillie as $rand_id => $code )
{
    $smileys[] = array (
        'smiley_id' => $rand_id,
        'bbcode' => $code,
        'image' => $smiley_image[$rand_id]
    );
}
update_smileys ($smileys);

$title = $lang['ind250'];
echo make_redirect ($lang['ind251'], '?id=smillies', $lang['ind335']);


?>