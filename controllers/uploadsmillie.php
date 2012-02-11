<?php

/*id Smillies Upload*/
	if ( !has_access (NEWS_ADMIN) )
	{
		trigger_error ($lang['ind19'], E_USER_WARNING);
	}

    if ( !check_form_character() )
    {
        trigger_error ($lang['ind298'], E_USER_WARNING);
    }

    $title = $lang['ind246'];

    $upload_status = upload_file (-1, 'jpg|gif|jpeg|png|bmp', './smillies/');
    echo make_redirect ($upload_status, '?id=smillies', $lang['ind335']);


?>