<?php

if ( !has_access (NEWS_EDITOR) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$num_words = ( isset ($PVARS['num_words']) ) ? $PVARS['num_words'] : 0;
$find = ( isset ($PVARS['find']) ) ? array_map ('single_line', $PVARS['find']) : array();
$replace = ( isset ($PVARS['replace']) ) ? array_map ('single_line', $PVARS['replace']) : array();
$case_sens = ( isset ($PVARS['case_sens']) ) ? $PVARS['case_sens'] : array();
$type = ( isset ($PVARS['type']) ) ? $PVARS['type'] : array();

for ( $i = 0; $i < $num_words; $i++ )
{
    if ( isset ($del[$i]) ) continue;
    
    if ( $type[$i] == 2 ) // regex
    {
        if ( @preg_match ($find[$i], '') === false )
        {
            trigger_error ($lang['ind421'], E_USER_WARNING);
        }
    }
}

$data = array();
for ( $i = 0; $i < 5; $i++ )
{
    if ( empty ($find[$i]) || empty ($replace[$i]) )
    {
        continue;
    }

    $data[] = array (
        'find' => $find[$i],
        'replace' => $replace[$i],
        'case_sensitive' => (isset ($case_sens[$i]) ? $case_sens[$i] : 0),
        'type' => $type[$i]
    );
}

add_badwords ($data);

$title = $lang['ind233'];
echo make_redirect ($lang['ind234'], '?id=badwordfilter', $lang['ind235']);


?>