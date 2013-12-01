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
include_once 'rfg_libs.php';
$default_gallery_id = 0;
$warning = false;

if (isset($_POST['rfg_edit_gallery_name']) && $_POST['rfg_edit_gallery_name']) {
    global $default_gallery_id;
    global $warning;

    if ($_POST['rfg_per_page_check']) $_POST['rfg_per_page'] = '';
    else {
        if (!(ctype_digit($_POST['rfg_per_page']) && (int)$_POST['rfg_per_page'])) {
            $_POST['rfg_per_page'] = '';
            $warning = true;
        }
    }

    $gallery = array(
        'name' => stripslashes($_POST['rfg_edit_gallery_name']),
        'gallery_descr' => stripslashes($_POST['rfg_edit_gallery_descr']),
        'photo_source' => $_POST['rfg_photo_source_type'],
        'per_page' => rfg_filter($_POST['rfg_per_page']),
        'sort_order' => rfg_filter($_POST['rfg_sort_order']),
        'photo_size' => rfg_filter($_POST['rfg_photo_size']),
        'captions' => rfg_filter($_POST['rfg_captions']),
        'descr' => rfg_filter($_POST['rfg_descr']),
        'columns' => rfg_filter($_POST['rfg_columns']),
        'slideshow_option' => rfg_filter($_POST['rfg_slideshow_option']),
        'credit_note' => rfg_filter($_POST['rfg_credit_note']),
        'width' => rfg_filter($_POST['rfg_width']),
        'pagination' => rfg_filter($_POST['rfg_pagination']),
        'bg_color' => rfg_filter($_POST['rfg_bg_color']),
    );

    if ($_POST['rfg_photo_source_type'] == 'photoset') $gallery['photoset_id'] = $_POST['rfg_photosets_box'];
    else if ($_POST['rfg_photo_source_type'] == 'gallery') $gallery['gallery_id'] = $_POST['rfg_galleries_box'];
    else if ($_POST['rfg_photo_source_type'] == 'group') $gallery['group_id'] = $_POST['rfg_groups_box'];
    else if ($_POST['rfg_photo_source_type'] == 'tags') $gallery['tags'] = $_POST['rfg_tags'];

    $id = $_POST['rfg_photo_gallery'];

    $galleries = get_option('rfg_galleries');
    $galleries[$id] = $gallery;
    update_option('rfg_galleries', $galleries);
    $default_gallery_id = $id;
}

function rfg_edit_galleries_header()
{
    $params = array(
        'api_key' => get_option('rfg_api_key'),
        'user_id' => get_option('rfg_user_id'),
        'default_per_page' => get_option('rfg_per_page'),
        'galleries' => json_encode(get_option('rfg_galleries')),
    );
    wp_enqueue_script('edit-galleries-script');
    wp_localize_script('edit-galleries-script', 'genparams', $params);
}

function rfg_get_galleries($default='')
{
    $galleries = get_option('rfg_galleries');
    $gstr = "";
    foreach ($galleries as $id => $ginfo) {
        if ($id) {
            if ($id == $default)
                $gstr .= "<option value=\"$id\" selected>$id - {$ginfo['name']}</option>";
            else
                $gstr .= "<option value=\"$id\">$id - {$ginfo['name']}</option>";
        }
    }
    return $gstr;
}

function rfg_edit_galleries()
{
    global $rfg_photo_size_map,
           $rfg_on_off_map,
           $rfg_descr_map,
           $rfg_columns_map,
           $rfg_bg_color_map,
           $rfg_photo_source_map,
           $default_gallery_id,
           $rfg_cache_ttl_map,
           $pf;

    $user_id = get_option('rfg_user_id');

    $cur_page_url = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    preg_match('/\&gallery_id=(?P<gallery_id>\d+)/', $cur_page_url, $matches);
    if ($matches && !$default_gallery_id) {
        $default_gallery_id = $matches['gallery_id'];
        $match_pos = strpos($cur_page_url, "&gallery_id=$default_gallery_id");
        $cur_page_url = substr($cur_page_url, 0, $match_pos);
    }

    $photosets_map = array();
    $rsp_obj = $pf->photosets_getList($user_id);
    if (!$pf->error_code) {
        foreach ($rsp_obj['photoset'] as $photoset) {
            $photosets_map[$photoset['id']] = $photoset['title']['_content'];
        }
    }

    $galleries_map = array();
    $rsp_obj = $pf->galleries_getList($user_id);
    if (!$pf->error_code) {
        foreach ($rsp_obj['galleries']['gallery'] as $gallery) {
            $galleries_map[$gallery['id']] = $gallery['title']['_content'];
        }
    }

    $groups_map = array();
    if (get_option('rfg_flickr_token')) {
        $rsp_obj = $pf->groups_pools_getGroups();
        if (!$pf->error_code) {
            foreach ($rsp_obj['group'] as $group) {
                $groups_map[$group['nsid']] = $group['name'];
            }
        }
    } else {
        $rsp_obj = $pf->people_getPublicGroups($user_id, true);
        if (!$pf->error_code) {
            foreach ($rsp_obj as $group) {
                $groups_map[$group['nsid']] = $group['name'];
            }
        }
    }

    ?>
    <div class='wrap'>
    <h2><img src="<?php echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/>Edit Galleries | Responsive Flickr Gallery</h2>

    <?php
    if ($_POST && $_POST['rfg_edit_gallery_name']) {
        global $warning;
        if ($warning) {
            echo "<div class='updated'><p><strong>You entered invalid value for Per Page option.  It has been set to Default.</strong></p></div>";
            $warning = false;
        }
        echo "<div class='updated'><p><strong>Gallery updated successfully.</strong></p></div>";
    }
    echo rfg_generate_version_line();
    $url=$_SERVER['REQUEST_URI'];
    ?>

    <form method='post' action='<?php echo $url ?>'>
        <div class="postbox-container" style="width:69%; margin-right:1%">

           <div id="poststuff">
              <div class="postbox" style='box-shadow:0 0 2px'>
                 <h3>Saved Galleries</h3>
                 <table class='form-table'>
                    <tr valign='top'>
                       <th scope='row'>Select Gallery to Edit</th>
                       <td><select id='rfg_photo_gallery' name='rfg_photo_gallery' onchange='loadGallerySettings()'>
                             <?php echo rfg_get_galleries($default_gallery_id) ?>
                       </select></td>
                       <tr valign='top'>
                          <th scope='row'>Gallery Name</th>
                          <td><input maxlength='30' type='text' id='rfg_edit_gallery_name' name='rfg_edit_gallery_name' onblur='verifyEditBlank()' value="" /><font size='3' color='red'>*</font></td>
                       </tr>
                       <tr valign='top'>
                          <th scope='row'>Gallery Description</th>
                          <td><input maxlength='100' size='70%' type='text' id='rfg_edit_gallery_descr' name='rfg_edit_gallery_descr' value="" /></td>
                       </tr>
                    </table>
              </div>
           </div>

    <?php
    echo rfg_generate_flickr_settings_table($photosets_map, $galleries_map, $groups_map);
    echo rfg_generate_gallery_settings_table();
    $gals = get_option('rfg_galleries');
    if (sizeof($gals) == 1) $disable_submit = true;
    else $disable_submit = false;
    ?>

           <input type="submit" id="rfg_save_changes" class="button-primary"
           <?php if ($disable_submit) echo "disabled='yes'"; ?>
           value="Save Changes" />
           <br /><br />
           <div id="poststuff">
              <div class="postbox" style='box-shadow:0 0 2px'>
                 <h3>Gallery Code</h3>
                 <table class='form-table'>
                    <tr valign='top'>
                       <td>
                          <p id='rfg_flickr_gallery_code'>[RFG_gallery]</p>
                       </td>
                    </tr>
                 </table>
           </div>
         </div>
      </div>
    <div class="postbox-container" style="width: 29%;">
    <?php
    echo rfg_box('Usage Instructions', 'Insert the Gallery Code in any of your posts of pages to display your Flickr Gallery.');
    echo rfg_donate_box();
    echo rfg_share_box();
    ?>
    </div>
    </form>
    <?php
}
