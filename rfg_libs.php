<?php
/*
   This file is part of the Responsive Flickr Gallery.

   Responsive Flickr Gallery is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Responsive Flickr Gallery is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Responsive Flickr Gallery.  If not, see <http://www.gnu.org/licenses/>.
 */
define('BASE_URL', plugins_url() . '/' . basename(dirname(__FILE__)));
define('SITE_URL', site_url());
define('DEBUG', false);
define('VERSION', '1.3.0');

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
    'disable' => 'Link to Flickr Photo',
    'flickr' => 'Link to Flickr Photo page',
    'none' => 'No Link',
);

/* Map for photo titles displayed on the gallery. */
$size_heading_map = array(
    '_q' => '0.7em',
    '_t' => '0.6em',
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
    '_q' => 'Square (Max 150px)',
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
    '99' => 'Max ',
);

$rfg_cache_ttl_map = array(
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
    '13' => '13 ',
    '14' => '14 ',
    '30' => '30 ',
    '60' => '60 ',
    '90' => '90 '
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
    " <div align=\"right\" style=\"margin-right:1%\">" .
       " &nbsp;Version: <b>" . VERSION . "</b>" .
    " </div>";
    return $return_str;
}

function rfg_generate_flickr_settings_table($photosets, $galleries, $groups)
{
    global $rfg_photo_source_map;
    $photosets = rfg_generate_options($photosets, '', false);
    $galleries = rfg_generate_options($galleries, '', false);
    $groups = rfg_generate_options($groups, '', false);
    return "
<div class=\"postbox\">
<div class=\"inside\">
    <h3>Flickr Settings</h3>
    <table class='form-table'>
        <tr>
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
</div>
</div>";
}

function rfg_generate_gallery_settings_table()
{
    global $rfg_photo_size_map,
           $rfg_on_off_map,
           $rfg_descr_map, 
           $rfg_columns_map,
           $rfg_bg_color_map,
           $rfg_photo_source_map, 
           $rfg_width_map,
           $rfg_yes_no_map,
           $rfg_sort_order_map,
           $rfg_slideshow_map,
           $rfg_cache_ttl_map;
    
    $photo_size = $rfg_photo_size_map[get_option('rfg_photo_size')];

    return "
        <div class=\"postbox\">
        <div class=\"inside\">
        <h3>Gallery Settings</h3>
        <table class='form-table'>

        <tr>
        <th scope='row'>Max Photos Per Page</th>
        <td style='width:28%'>
          <input type='checkbox' name='rfg_per_page_check' id='rfg_per_page_check' onclick='showHidePerPage()' value='default' checked='' style='vertical-align:top'> Default </input>
          <input name='rfg_per_page' disabled='true' id='rfg_per_page' type='text' size='3' maxlength='3' onblur='verifyBlank()' value='10'/> 
        </td>
        </tr>

        <tr>
        <th scope='row'>Sort order of Photos</th>
        <td><select name='rfg_sort_order' id='rfg_sort_order'>"
        . rfg_generate_options($rfg_sort_order_map, 'default', true, $rfg_sort_order_map[get_option('rfg_sort_order')]) . "
    </select>
            <td><font size='2'>Set the sort order of the photos as per your liking and forget about how photos are arranged on Flickr.</font></td>
            </td>
            </tr>

        <tr>
        <th scope='row'>Size of Photos</th>
        <td><select name='rfg_photo_size' id='rfg_photo_size' >
            " . rfg_generate_options($rfg_photo_size_map, 'default', true, $photo_size) . "
        </select></td>
        </tr>
        
        <tr>
        <th scope='row'>Photo Titles</th>
        <td><select name='rfg_captions' id='rfg_captions'>
            " . rfg_generate_options($rfg_on_off_map, 'default', true, $rfg_on_off_map[get_option('rfg_captions')]) . "
        </select></td>
        <td><font size='2'>Photo Titles overlay the image and should contain only a few words.</font></td>
        </tr>

        <tr>
        <th scope='row'>Photo Descriptions</th>
        <td><select name='rfg_descr' id='rfg_descr'>
            " . rfg_generate_options($rfg_descr_map, 'default', true, $rfg_descr_map[get_option('rfg_descr')]) . "
        </select></td>
        <td><font size='2'>Photo Descriptions will be shown when the mouse hovers over the photos. The text shouldn't be excessively long.</td>
        </tr>

        <tr>
        <th scope='row'>Number of Columns</th>
        <td><select name='rfg_columns' id='rfg_columns'>
            " . rfg_generate_options($rfg_columns_map, 'default', true, $rfg_columns_map[get_option('rfg_columns')]) . "
        </select></td>
         <td><font size='2'>
          Max. number of pictures in a row. Example: Set to 2 if you don't wont more than two photos in a row even if space would allow more. 
          Set to \"max\" to allow a as many photos as possible in row. For most cases \"max\" should be used.<br />
          </font></td>
        </tr>

        <tr>
        <th scope='row'>Click on Photo Behavior</th>
        <td><select name='rfg_slideshow_option' id='rfg_slideshow_option'>
        " . rfg_generate_options($rfg_slideshow_map, 'default', true, $rfg_slideshow_map[get_option('rfg_slideshow_option')]) . "
    </select></td>
            <td><font size='2'>
            If you use ColorBox to display photos in larger size you'll also have a slidehsow for all photos from a gallery.
            A slideshow contains all photos of a gallery - even if pagination is enabled.<br />
            </font></td>
            </tr>

        <tr>
        <th scope='row'>Background Color</th>
        <td><select name='rfg_bg_color' id='rfg_bg_color'>
            " . rfg_generate_options($rfg_bg_color_map, 'default', true, $rfg_bg_color_map[get_option('rfg_bg_color')]) . "
        </select></td>
        </tr>

        <tr>
        <th scope='row'>Gallery Width</th>
        <td><select name='rfg_width' id='rfg_width'>
        " . rfg_generate_options($rfg_width_map, 'default', true, $rfg_width_map[get_option('rfg_width')]) . "
        </select></td>
        <td><font size='2'>Width of the Gallery is relative to the width of the page where Gallery is being generated.  <i>Automatic</i> is 100% of page width.</font></td>
        </tr>

        <tr>
        <th scope='row'>Disable Pagination?</th>
        <td><select name='rfg_pagination' id='rfg_pagination'>
        " . rfg_generate_options($rfg_yes_no_map, 'default', true, $rfg_yes_no_map[get_option('rfg_pagination')]) . "
        </select></td>
        <td><font size='2'>Useful when displaying gallery in a sidebar widget where you want only few recent photos.</td>
        </tr>

        <tr>
        <th scope='row'>Cache TTL</th>
        <td><select name='rfg_cache_ttl' id='rfg_cache_ttl'>
            " . rfg_generate_options($rfg_cache_ttl_map, 'default', true, $rfg_cache_ttl_map[get_option('rfg_cache_ttl')]) . "
        </select></td>
        <td><font size='2'>
             Number of days the Flick API call results will be cached in the database.
             Calling the external API is \"expensive\" and makes the site slow.
             Set low if galleries on flickr change often.
             Set high if galleries don't change often to save \"expensive\" API calls 
             and speed up the galleries on your site.</font>
        </td>
        </tr>
    </table>
</div>
</div>";
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
        <div class=\"postbox\">
        <div class=\"inside\">
        <h3>$title</h3>
        <table class='form-table'>
        <td>$message</td>
        </table>
        </div>
        </div>
        ";
}

function rfg_usage_box($code)
{
    return <<<EOD
<div class="postbox">
  <div class="inside">
    <h3>Usage Instructions</h3>
    <strong>Insert $code in posts or pages to display the Flickr gallery.</strong>
  </div>
</div>
EOD;
}

function get_rfg_option($gallery, $var)
{
    if (isset($gallery[$var]) && $gallery[$var]) return $gallery[$var];
    else return get_option('rfg_' . $var);
}

function rfgDonateBox()
{
    return <<<EOD
<div class="postbox">
  <div class="inside">
    <h3>License keys</h3>
    A <a href="http://www.lars-schenk.com/product/responsive-flickr-gallery-license-yearly" target="_blank">license key is mandatory</a> for businesses and commercial sites. 
    For personal blogs the license is optional but should be considered to support further development and maintenance of this plugin.<br />
    <br />

    Found a bug or need a new feature?<br />
    Head to <a href="https://github.com/schenk/responsive-flickr-gallery/issues">github issues</a> for solutions.<br />
    Contributors and feature requests welcome. Bounties can speed up the development process.
  </div> 
</div> 
EOD;
}

function rfg_reference_box()
{
    $message = "Max Photos Per Page - <b>" . get_option('rfg_per_page') . "</b>";
    $size = get_option('rfg_photo_size');
    if ($size == '_q') $size = 'Square';
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
    $message .= "<br />Cache TTL - <b>" . get_option('rfg_cache_ttl') . "</b>";
    return rfg_box('Default Settings for Reference', $message);
}
