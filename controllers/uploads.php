<?php

if ( !has_access (NEWS_EDITOR) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$title = $lang['ind204'];

if ( !file_uploads_enabled() )
{
    if ( $config['uploads_active'] )
    {
        $config['uploads_active'] = 0;
        save_config ($config);
    }
    
    echo $lang['ind105'];
}
else
{
    $directory_size = 0;
    $images = get_uploaded_images_all ($config['uploads_ext']);
    $image_details = array();
    foreach ( $images as $file )
    {
        $image_path = FNEWS_ROOT_PATH . 'uploads/' . $file;
        $image_url = 'uploads/' . $file;

        $image_size = getimagesize ($image_path);
        $filesize = filesize ($image_path);
        $image_details[$file] = array (
            'url'       => $image_url,
            'filesize'  => $filesize,
            'width'     => $image_size[0],
            'height'    => $image_size[1],
            'timestamp' => filemtime ($image_path)
        );

        $directory_size += $filesize;
    }

?>
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery.lightbox-0.5.min.js"></script>
<script type="text/javascript">
//<![CDATA[

function show_image ( url, image, width, height )
{
    window.open (
        url,
        image,
        'height=' + height + ', width=' + width + ', toolbar=no, menubar=no, scrollbars=yes, resizable=yes'
    );
    
    return false;
}

//]]>
</script>
<form id="uploaded_images" method="post" action="?id=deluploads">
<?php echo get_form_security(); ?>
<table class="adminpanel">
    <thead>
        <tr>
            <th style="width:5%">&nbsp;</th>
            <th style="width:45%"><?php echo $lang['ind226']; ?></th>
            <th style="width:15%"><?php echo $lang['ind227']; ?></th>
            <th style="width:35%"><?php echo $lang['ind228']; ?></th>
        </tr>
    </thead>
<?php if ( sizeof ($image_details) > 0 ): ?>
    <tfoot>
        <tr>
            <td colspan="2"><b><?php echo $lang['ind264']; ?></b></td>
            <td><b><?php echo calc_size ($directory_size); ?></b></td>
            <td>&nbsp;</td>
        </tr>
    </tfoot>
    <tbody>
    <?php foreach ( $image_details as $image => $details ): ?>
        <tr>
            <td class="centered">
                <input class="post" type="checkbox" id="del_files_<?php echo $image; ?>" name="del_files[<?php echo $image; ?>]" value="<?php echo $image; ?>" onclick="javascript:check_if_selected('uploaded_images')" />
            </td>
            <td>
                <a href="<?php echo $details['url']; ?>" class="lightbox"><?php echo $image; ?></a>
            </td>
            <td><?php echo calc_size ($details['filesize']); ?></td>
            <td><?php echo date ('Y-m-d H:i:s T', $details['timestamp']); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
<?php else: ?>
    <tbody>
        <tr>
            <td colspan="4" class="centered"><?php echo $lang['ind300']; ?></td>
        </tr>
    </tbody>
<?php endif; ?>
</table>
<p>
    <a href="javascript:un_check_all ('uploaded_images', true)"><?php echo $lang['ind44']; ?></a> | <a href="javascript:un_check_all ('uploaded_images', false)"><?php echo $lang['ind44a']; ?></a>
</p>
<p>
    <input class="mainoption" id="delete" disabled="disabled" type="submit" value="<?php echo $lang['ind97']; ?>" />
</p>
</form>
<script type="text/javascript">
//<![CDATA[

$("a.lightbox").lightBox();

//]]>
</script>
<?php

} // For the if file uploads enabled..

?>