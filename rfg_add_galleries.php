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
include_once('rfg_libs.php');

function rfg_add_gallery()
{
    global $rfg_photo_size_map, 
           $rfg_on_off_map,
           $rfg_descr_map,
           $rfg_columns_map,
           $rfg_bg_color_map,
           $rfg_photo_source_map,
           $rfg_cache_ttl_map,
           $pf;

    $user_id = get_option('rfg_user_id');

    $photosets_map = array();
    $groups_map = array();
    $galleries_map = array();
    $rsp_obj = $pf->photosets_getList($user_id);
    if (!$pf->error_code) {
        foreach ($rsp_obj['photoset'] as $photoset) {
            $photosets_map[$photoset['id']] = $photoset['title']['_content'];
        }
    }

    $rsp_obj = $pf->galleries_getList($user_id);
    if (!$pf->error_code) {
        foreach ($rsp_obj['galleries']['gallery'] as $gallery) {
            $galleries_map[$gallery['id']] = $gallery['title']['_content'];
        }
    }

    if (get_option('rfg_flickr_token')) {
        $rsp_obj = $pf->groups_pools_getGroups();
        if (!$pf->error_code) {
            foreach ($rsp_obj['group'] as $group) {
                $groups_map[$group['nsid']] = $group['name'];
            }
        }
    } else {
        $rsp_obj = $pf->people_getPublicGroups($user_id);
        if (!$pf->error_code) {
            foreach ($rsp_obj as $group) {
                $groups_map[$group['nsid']] = $group['name'];
            }
        }
    }
    ?>

    <div class='wrap'>
    <h2><img src="<?php echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/>Add Gallery | Responsive Flickr Gallery</h2>

    <?php
    if ($_POST && $_POST['rfg_add_gallery_name']) {
        if (isset($_POST['rfg_per_page_check']) && $_POST['rfg_per_page_check']) $_POST['rfg_per_page'] = '';
        else {
            if (!(ctype_digit($_POST['rfg_per_page']) && (int)$_POST['rfg_per_page'])) {
                $_POST['rfg_per_page'] = '';
                echo "<div class='updated'><p><strong>You entered invalid value for Per Page option.  It has been set to Default.</strong></p></div>";
            }
        }
        $gallery = array(
            'name' => $_POST['rfg_add_gallery_name'],
            'gallery_descr' => $_POST['rfg_add_gallery_descr'],
            'photo_source' => $_POST['rfg_photo_source_type'],
            'per_page' => rfg_filter($_POST['rfg_per_page']),
            'sort_order' => rfg_filter($_POST['rfg_sort_order']),
            'photo_size' => rfg_filter($_POST['rfg_photo_size']),
            'captions' => rfg_filter($_POST['rfg_captions']),
            'descr' => rfg_filter($_POST['rfg_descr']),
            'columns' => rfg_filter($_POST['rfg_columns']),
            'slideshow_option' => rfg_filter($_POST['rfg_slideshow_option']),
            'credit_note' => rfg_filter($_POST['rfg_credit_note']),
            'bg_color' => rfg_filter($_POST['rfg_bg_color']),
            'width' => rfg_filter($_POST['rfg_width']),
            'pagination' => rfg_filter($_POST['rfg_pagination']),
        );

        if ($_POST['rfg_photo_source_type'] == 'photoset')
            $gallery['photoset_id'] = $_POST['rfg_photosets_box'];
        else if ($_POST['rfg_photo_source_type'] == 'gallery')
            $gallery['gallery_id'] = $_POST['rfg_galleries_box'];
        else if ($_POST['rfg_photo_source_type'] == 'group')
            $gallery['group_id'] = $_POST['rfg_groups_box'];
        else if ($_POST['rfg_photo_source_type'] == 'tags')
            $gallery['tags'] = $_POST['rfg_tags'];

        $galleries = get_option('rfg_galleries');
        $galleries[] = $gallery;
        update_option('rfg_galleries', $galleries);
        end($galleries);
        $id = key($galleries);
        ?>
        <div class="updated"><p><strong>
              <?php echo "Gallery \"{$_POST['rfg_add_gallery_name']}\" created successfully.  Shortcode for this gallery is </strong>[RFG_gallery id='$id']"; ?>
           </p></div>

        <?php
    }

    echo rfg_generate_version_line();
    $url=$_SERVER['REQUEST_URI'];
    ?>

            <form method='post' action='<?php echo $url ?>'>
               <div class="postbox-container" style="width:69%; margin-right:1%">
                  <div id="poststuff">
                     <div class="postbox" style='box-shadow:0 0 2px'>
                        <h3>Gallery Parameters</h3>
                        <table class='form-table'>
                           <tr valign='top'>
                              <th scope='row'>Gallery Name</th>
                              <td><input maxlength='30' type='text' id='rfg_add_gallery_name' name='rfg_add_gallery_name' onblur='verifyBlank()' value='' /><font size='3' color='red'>*</font></td>
                           </tr>
                           <tr valign='top'>
                              <th scope='row'>Gallery Description</th>
                              <td><input maxlength='100' size='70%' type='text' id='rfg_add_gallery_descr' name='rfg_add_gallery_descr'" value="" /></td>
                           </tr>
                        </table>
                  </div></div>
    <?php
    echo rfg_generate_flickr_settings_table($photosets_map, $galleries_map, $groups_map);
    echo rfg_generate_gallery_settings_table();
    ?>
                  <input type="submit" disabled='true' id="rfg_save_changes" class="button-primary"
                  value="Add Gallery" />
               </div>
               <div class="postbox-container" style="width: 29%;">
    <?php
    $message = "<b>Gallery Description</b> - Provide a meaningful description of" .
        " your gallery for you to recognize it easily.<br /><br />" .
        " <b>Gallery Source</b> - Where do you want to fetch your photos from?" .
        " Your Flickr Photostream, a Photoset, a Gallery or a Group?<br /><br />" .
        " <b>What is <i>Default</i>?</b> - When you select <i>" .
        " Default</i> for a setting, it will be inherited from <a href=\"" .
        $_SERVER['PHP_SELF'] . "?page=rfg_plugin_page\"><i>Default" .
        " Settings</i></a>.  The setting here is stored as reference to the" .
        " setting on Default Settings page, so if you change the <i>Default" .
        " Settings</i>, the setting for this specific gallery will also change.";
    echo rfg_box('Help', $message);
    echo rfg_donate_box();
    echo rfg_share_box();
    ?>
               </div>
                </form>
    <?php
}
