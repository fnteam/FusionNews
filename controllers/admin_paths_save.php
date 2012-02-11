<?php


	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($lang['ind19'], E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($lang['ind298'], E_USER_WARNING);
    }

    $site	= ( isset ($PVARS['site1']) ) ? $PVARS['site1'] : $config['site'];
    $furl	= ( isset ($PVARS['furl1']) ) ? $PVARS['furl1'] : $config['furl'];
    $hurl	= ( isset ($PVARS['url']) ) ? $PVARS['url'] : $config['hurl'];

    if ( !$site || !$furl || !$hurl )
    {
        trigger_error ($lang['error23'], E_USER_WARNING);
    }

    $config['site']	 = $site;
    $config['furl']	 = $furl;
    $config['hurl']	 = $hurl;

    save_config( $config );

    $title = $lang['ind21'];
    echo make_redirect ($lang['ind22']);


?>