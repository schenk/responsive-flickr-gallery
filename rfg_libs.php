<?php

define('BASE_URL', plugins_url() . '/' . basename(dirname(__FILE__)));
define('SITE_URL', site_url());
define('DEBUG', false);
define('VERSION', '0.0.3');

$rfg_sort_order_map = array(
    'default' => 'Default',
    'flickr' => 'As per Flickr',
    'date_taken_cmp_newest' => 'By date taken (Newest first)',
    'date_taken_cmp_oldest' => 'By date taken (Oldest first)',
    'date_upload_cmp_newest' => 'By date uploaded (Newest first)',
    'date_upload_cmp_oldest' => 'By date uploaded (Oldest first)',
    'random' => 'Random',
);

$rfg_slideshow_map = array(
    'default' => 'Default',
    'colorbox' => 'Colorbox',
    'disable' => 'No Slideshow',
    'flickr' => 'Link to Flickr Photo page',
    'none' => 'No Slideshow and No Link',
);

/* Map for photo titles displayed on the gallery. */
$size_heading_map = array(
    '_s' => '',
    '_t' => '0.9em',
    '_m' => '1em',
    'NULL' => '1.2em',
);

$rfg_photo_source_map = array(
    'photostream' => 'Photostream',
    'gallery' => 'Gallery',
    'photoset' => 'Photoset',
    'group' => 'Group',
    'tags' => 'Tags',
    'popular' => 'My Popular Photos',
);

$rfg_width_map = array(
    'default' => 'Default',
    'auto' => 'Automatic',
    '10' => '10 %',
    '20' => '20 %',
    '30' => '30 %',
    '40' => '40 %',
    '50' => '50 %',
    '60' => '60 %',
    '70' => '70 %',
    '80' => '80 %',
    '90' => '90 %',
);

$rfg_photo_size_map = array(
    'default' => 'Default',
    '_s' => 'Square (Max 75px)',
    '_t' => 'Thumbnail (Max 100px)',
    '_m' => 'Small (Max 240px)',
    'NULL' => 'Medium (Max 500px)',
);

$rfg_on_off_map = array(
    'off' => 'Off  ',
    'on' => 'On  ',
    'default' => 'Default',
);

$rfg_yes_no_map = array(
    'off' => 'Yes  ',
    'on' => 'No  ',
    'default' => 'Default',
);

$rfg_descr_map = array(
    'off' => 'Off',
    'on' => 'On',
    'default' => 'Default',
);

$rfg_columns_map = array(
    'default' => 'Default',
    '1' => '1  ',
    '2' => '2  ',
    '3' => '3  ',
    '4' => '4  ',
    '5' => '5  ',
    '6' => '6  ',
    '7' => '7  ',
    '8' => '8  ',
    '9' => '9  ',
    '10' => '10 ',
    '11' => '11 ',
    '12' => '12 ',
);

$rfg_bg_color_map = array(
    'default' => 'Default',
    'Black' => 'Black',
    'White' => 'White',
    'Transparent' => 'Transparent',
);

$rfg_text_color_map = array(
    'Black' => 'White',
    'White' => 'Black',
);

function create_afgFlickr_obj()
{
    global $pf;
    unset($_SESSION['afgFlickr_auth_token']);
    $pf = new afgFlickr(get_option('rfg_api_key'), get_option('rfg_api_secret')? get_option('rfg_api_secret'): null);
    $pf->setToken(get_option('rfg_flickr_token'));
}

function rfg_error()
{
    global $pf;
    return "<h3>Responsive Flickr Gallery Error - $pf->error_msg</h3>";
}

function date_taken_cmp_newest($a, $b)
{
    return $a['datetaken'] < $b['datetaken'];
}

function date_taken_cmp_oldest($a, $b)
{
    return $a['datetaken'] > $b['datetaken'];
}

function date_upload_cmp_newest($a, $b)
{
    return $a['dateupload'] < $b['dateupload'];
}

function date_upload_cmp_oldest($a, $b)
{
    return $a['dateupload'] > $b['dateupload'];
}

function rfg_fb_like_box()
{
    return '';
}

function rfg_share_box()
{
    return "";
}

function rfg_gplus_box()
{
    return '';
}

function delete_rfg_caches()
{
    $galleries = get_option('rfg_galleries');
    foreach ($galleries as $id => $ginfo) {
        delete_transient('rfg_id_'. $id);
    }
}

function rfg_get_photo_url($farm, $server, $pid, $secret, $size)
{
    if ($size == 'NULL') {
        $size = '';
    }
    return "http://farm$farm.static.flickr.com/$server/{$pid}_$secret$size.jpg";
}

function rfg_get_photo_page_url($pid, $uid)
{
    return "http://www.flickr.com/photos/$uid/$pid";
}

function rfg_generate_version_line()
{
    if (isset($_POST['rfg_dismis_ss_msg']) && $_POST['rfg_dismis_ss_msg']) {
        update_option('rfg_dismis_ss_msg', true);
    }

    $return_str = "" .
    " <h4 align=\"right\" style=\"margin-right:0.5%\">" .
       " &nbsp;Version: <b>" . VERSION . "</b>" .
    " </h4>";
    return $return_str;
}

function rfg_generate_flickr_settings_table($photosets, $galleries, $groups)
{
    global $rfg_photo_source_map;
    $photosets = rfg_generate_options($photosets, '', false);
    $galleries = rfg_generate_options($galleries, '', false);
    $groups = rfg_generate_options($groups, '', false);
    return "
    <div id=\"poststuff\">
<div class=\"postbox\" style='box-shadow:0 0 2px'>
    <h3>Flickr Settings</h3>
    <table class='form-table'>
        <tr valign='top'>
        <th scope='row'>Gallery Source</th>
        <td><select name='rfg_photo_source_type' id='rfg_photo_source_type' onchange='getPhotoSourceType()' >" . rfg_generate_options($rfg_photo_source_map, 'photostream', false) . "
        </select></td>
        </tr>
        <tr>
        <th id='rfg_photo_source_label'></th>
        <td><select style='display:none' name='rfg_photosets_box' id='rfg_photosets_box'>$photosets
        </select>
        <select style='display:none' name='rfg_galleries_box' id='rfg_galleries_box'>$galleries
        </select>
        <select style='display:none' name='rfg_groups_box' id='rfg_groups_box'>$groups
        </select>
        <textarea rows='3' cols='30' name='rfg_tags' id='rfg_tags' style='display:none'></textarea>
        </td>
        <td id='rfg_source_help' style='display:none'><font size='2'>Enter tags separated by comma. For example: <b>tag1, tag2, tag3, tag4</b><br />Photos matching any of the given tags will be displayed.</font></td>
        </tr>
    </table>
</div></div>";
}

function rfg_generate_gallery_settings_table()
{
    global $rfg_photo_size_map, $rfg_on_off_map, $rfg_descr_map, 
        $rfg_columns_map, $rfg_bg_color_map, $rfg_photo_source_map, 
        $rfg_width_map, $rfg_yes_no_map, $rfg_sort_order_map, $rfg_slideshow_map;
    
    $photo_size = $rfg_photo_size_map[get_option('rfg_photo_size')];

    return "
    <div id=\"poststuff\">
        <div class=\"postbox\" style='box-shadow:0 0 2px'>
        <h3>Gallery Settings</h3>
        <table class='form-table'>

        <tr valign='top'>
        <th scope='row'>Max Photos Per Page</th>
        <td style='width:28%'><input type='checkbox' name='rfg_per_page_check' id='rfg_per_page_check' onclick='showHidePerPage()' value='default' checked='' style='vertical-align:top'> Default </input><input name='rfg_per_page' disabled='true' id='rfg_per_page' type='text' size='3' maxlength='3' onblur='verifyBlank()' value='10'/> 
        </td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Sort order of Photos</th>
        <td><select name='rfg_sort_order' id='rfg_sort_order'>"
        . rfg_generate_options($rfg_sort_order_map, 'default', true, $rfg_sort_order_map[get_option('rfg_sort_order')]) . "
    </select>
            <td><font size='2'>Set the sort order of the photos as per your liking and forget about how photos are arranged on Flickr.</font></td>
            </td>
            </tr>

        <tr valign='top'>
        <th scope='row'>Size of Photos</th>
        <td><select name='rfg_photo_size' id='rfg_photo_size' >
            " . rfg_generate_options($rfg_photo_size_map, 'default', true, $photo_size) . "
        </select></td>
        </tr>
        
        <tr valign='top'>
        <th scope='row'>Photo Titles</th>
        <td><select name='rfg_captions' id='rfg_captions'>
            " . rfg_generate_options($rfg_on_off_map, 'default', true, $rfg_on_off_map[get_option('rfg_captions')]) . "
        </select></td>
        <td><font size='2'>Photo Title setting applies only to Thumbnail (and above) size photos.</font></td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Photo Descriptions</th>
        <td><select name='rfg_descr' id='rfg_descr'>
            " . rfg_generate_options($rfg_descr_map, 'default', true, $rfg_descr_map[get_option('rfg_descr')]) . "
        </select></td>
        <td><font size='2'>Photo Description setting applies only to Small and Medium size photos.</td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Number of Columns</th>
        <td><select name='rfg_columns' id='rfg_columns'>
            " . rfg_generate_options($rfg_columns_map, 'default', true, $rfg_columns_map[get_option('rfg_columns')]) . "
        </select></td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Slideshow Behavior</th>
        <td><select name='rfg_slideshow_option' id='rfg_slideshow_option'>
        " . rfg_generate_options($rfg_slideshow_map, 'default', true, $rfg_slideshow_map[get_option('rfg_slideshow_option')]) . "
    </select></td>
            <td><font size='2'>
            If you use ColorBox to display photos in larger size you'll also have a slidehsow for all photos from a gallery.
            A slideshow contains all photos of a gallery - even if pagination is enabled.<br />
            Be aware that page load times can suffer if you choose to use ColorBox slideshows for galleries containing many pictures.
            </font></td>
            </tr>

        <tr valign='top'>
        <th scope='row'>Background Color</th>
        <td><select name='rfg_bg_color' id='rfg_bg_color'>
            " . rfg_generate_options($rfg_bg_color_map, 'default', true, $rfg_bg_color_map[get_option('rfg_bg_color')]) . "
        </select></td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Gallery Width</th>
        <td><select name='rfg_width' id='rfg_width'>
        " . rfg_generate_options($rfg_width_map, 'default', true, $rfg_width_map[get_option('rfg_width')]) . "
        </select></td>
        <td><font size='2'>Width of the Gallery is relative to the width of the page where Gallery is being generated.  <i>Automatic</i> is 100% of page width.</font></td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Disable Pagination?</th>
        <td><select name='rfg_pagination' id='rfg_pagination'>
        " . rfg_generate_options($rfg_yes_no_map, 'default', true, $rfg_yes_no_map[get_option('rfg_pagination')]) . "
        </select></td>
        <td><font size='2'>Useful when displaying gallery in a sidebar widget where you want only few recent photos.</td>
        </tr>

    </table>
</div></div>";
}

function rfg_generate_options($params, $selection, $show_default=False, $default_value=0)
{
    $str = '';
    foreach ($params as $key => $value) {
        if ($key == 'default' && !$show_default)
            continue;

        if ($selection == $key) {
            if ($selection == 'default') $value .= ' - ' . $default_value;
            $str .= "<option value=" . $key . " selected='selected'>" . $value . "</option>";
        }
        else
            $str .= "<option value=" . $key . ">" . $value . "</option>";
    }
    return $str;
}

function rfg_filter($param)
{
    if ($param == 'default') return "";
    else return $param;
}

function rfg_box($title, $message)
{
     return "
        <div id=\"poststuff\">
        <div class=\"postbox\" style='box-shadow:0 0 2px'>
        <h3>$title</h3>
        <table class='form-table'>
        <td>$message</td>
        </table>
        </div></div>
        ";
}

function rfg_usage_box($code)
{
    return "
        <div id=\"poststuff\">
        <div class=\"postbox\" style='box-shadow:0 0 2px'>
        <h3>Usage Instructions</h3>
        <table class='form-table'>
        <td>Just insert $code in any of the posts or page to display your Flickr gallery.</td>
        </table>
        </div></div>
        ";
}

function get_rfg_option($gallery, $var)
{
    if (isset($gallery[$var]) && $gallery[$var]) return $gallery[$var];
    else return get_option('rfg_' . $var);
}

function rfg_donate_box()
{
    $donate_button = "
        <form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" >
<div style=\"text-align:center\" class=\"paypal-donations\">
<input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">
<input type=\"hidden\" name=\"hosted_button_id\" value=\"G34C7BDW8499Q\">
<input type=\"image\" src=\"https://www.paypalobjects.com/en_US/DE/i/btn/btn_donateCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">
<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\">
</div></form>";
    return "
        <div id=\"poststuff\">
        <div class=\"postbox\" style='box-shadow:0 0 2px'>
        <h3>Support this plugin</h3>
        <table class='form-table'>
        <td>It takes time and effort to keep releasing new versions of this plugin.  If you like it, consider donating a few bucks <b>(especially if you are using this plugin on a commercial website)</b> to keep receiving new features.
        </form>$donate_button
        </td>
        </table>
        </div></div>";
}

function rfg_reference_box()
{
    $message = "Max Photos Per Page - <b>" . get_option('rfg_per_page') . "</b>";
    $size = get_option('rfg_photo_size');
    if ($size == '_s') $size = 'Square';
    else if ($size == '_t') $size = 'Thumbnail';
    else if ($size == '_m') $size = 'Small';
    else if ($size == 'NULL') $size = 'Medium';
    $message .= "<br />Size of Photos - <b>" . $size . "</b>";
    $message .= "<br />Photo Titles - <b>" . get_option('rfg_captions') . "</b>";
    $message .= "<br />Photo Descriptions - <b>" . get_option('rfg_descr') . "</b>";
    $message .= "<br />No of Columns - <b>" . get_option('rfg_columns') . "</b>";
    $message .= "<br />Background Color - <b>" . get_option('rfg_bg_color') . "</b>";
    $message .= "<br />Gallery Width - <b>" . ((get_option('rfg_width') == 'auto')?"Automatic":get_option('rfg_width') . "%") . "</b>";
    $message .= "<br />Pagination - <b>" . get_option('rfg_pagination') . "</b>";
    $message .= "<br />Credit Note - <b>" . get_option('rfg_credit_note') . "</b>";
    return rfg_box('Default Settings for Reference', $message);
}
