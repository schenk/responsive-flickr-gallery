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
require_once 'rfg_libs.php';
require_once 'rfg_edit_galleries.php';
require_once 'rfg_add_galleries.php';
require_once 'rfg_saved_galleries.php';
require_once 'rfg_advanced_settings.php';
require_once 'afgFlickr/afgFlickr.php';

add_action('admin_init', 'rfg_admin_init');
add_action('admin_init', 'rfg_auth_read');
add_action('admin_menu', 'rfg_admin_menu');
add_action('wp_ajax_rfg_gallery_auth', 'rfg_auth_init');

function rfg_admin_menu()
{
    add_menu_page('Responsive Flickr Gallery', 'Responsive Flickr Gallery', 'create_users', 'rfg_plugin_page', 'rfg_admin_html_page', BASE_URL . "/images/rfg_logo.png", 898);
    $rfg_main_page = add_submenu_page('rfg_plugin_page', 'Default Settings | Responsive Flickr Gallery', 'Default Settings', 'create_users', 'rfg_plugin_page', 'rfg_admin_html_page');
    $rfg_add_page = add_submenu_page('rfg_plugin_page', 'Add Gallery | Responsive Flickr Gallery', 'Add Gallery', 'moderate_comments', 'rfg_add_gallery_page', 'rfg_add_gallery');
    $rfg_saved_page = add_submenu_page('rfg_plugin_page', 'Saved Galleries | Responsive Flickr Gallery', 'Saved Galleries', 'moderate_comments', 'rfg_view_edit_galleries_page', 'rfg_view_delete_galleries');
    $rfg_edit_page = add_submenu_page('rfg_plugin_page', 'Edit Galleries | Responsive Flickr Gallery', 'Edit Galleries', 'moderate_comments', 'rfg_edit_galleries_page', 'rfg_edit_galleries');
    $rfg_advanced_page = add_submenu_page('rfg_plugin_page', 'Advanced Settings | Responsive Flickr Gallery', 'Advanced Settings', 'create_users', 'rfg_advanced_page', 'rfg_advanced_settings_page');
   
    add_action('admin_print_styles-' . $rfg_edit_page, 'rfg_edit_galleries_header');
    add_action('admin_print_styles-' . $rfg_add_page, 'rfg_edit_galleries_header');
    add_action('admin_print_styles-' . $rfg_saved_page, 'rfg_view_delete_galleries_header');
    add_action('admin_print_styles-' . $rfg_main_page, 'rfg_admin_settings_header');
    
    // adds "Settings" link to the plugin action page
    add_filter('plugin_action_links', 'rfg_add_settings_links', 10, 2);

    rfg_setup_options();
}

function rfg_add_settings_links( $links, $file )
{
    if ($file == plugin_basename(dirname(__FILE__)) . '/index.php') {
        $settings_link = '<a href="plugins.php?page=rfg_plugin_page">' . 'Settings</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

function rfg_admin_settings_header()
{
    wp_enqueue_script('admin-settings-script');
    add_action('admin_head', 'rfg_admin_headers');
}

function rfg_admin_headers()
{
    echo '';
}

function rfg_setup_options()
{
    if (get_option('rfg_descr') == '1') update_option('rfg_descr', 'on');
    if (get_option('rfg_descr') == '0') update_option('rfg_descr', 'off');
    if (get_option('rfg_captions') == '1') update_option('rfg_captions', 'on');
    if (get_option('rfg_captions') == '0') update_option('rfg_captions', 'off');
    if (get_option('rfg_credit_note') == '1' || get_option('rfg_credit_note') == 'Yes') update_option('rfg_credit_note', 'on');
    if (get_option('rfg_credit_note') == '0') update_option('rfg_credit_note', 'off');
    if (!get_option('rfg_pagination')) update_option('rfg_pagination', 'on');
    if (get_option('rfg_slideshow_option') == '') update_option('rfg_slideshow_option', 'colorbox');
    if (get_option('rfg_custom_css') == '') update_option('rfg_custom_css', '/* Start writing your custom CSS here */');
    if (get_option('rfg_disable_slideshow')) update_option('rfg_slideshow_option', 'disable');

    $galleries = get_option('rfg_galleries');
    if (!$galleries) {
        $galleries = array('0' =>
            array(
                'name' => 'My Photostream',
                'gallery_descr' => 'All photos from my Flickr Photostream with default settings.',
            )
        );
        update_option('rfg_galleries', $galleries);
    }

    if (!get_option('rfg_sort_order')) update_option('rfg_sort_order', 'flickr');

    update_option('rfg_version', VERSION);
}

/* Keep rfg_admin_init() and rfg_get_all_options() in sync all the time
 */

function rfg_admin_init()
{
    register_setting('rfg_settings_group', 'rfg_api_key');
    register_setting('rfg_settings_group', 'rfg_user_id');
    register_setting('rfg_settings_group', 'rfg_per_page');
    register_setting('rfg_settings_group', 'rfg_photo_size');
    register_setting('rfg_settings_group', 'rfg_captions');
    register_setting('rfg_settings_group', 'rfg_descr');
    register_setting('rfg_settings_group', 'rfg_columns');
    register_setting('rfg_settings_group', 'rfg_credit_note');
    register_setting('rfg_settings_group', 'rfg_bg_color');
    register_setting('rfg_settings_group', 'rfg_version');
    register_setting('rfg_settings_group', 'rfg_galleries');
    register_setting('rfg_settings_group', 'rfg_width');
    register_setting('rfg_settings_group', 'rfg_pagination');
    register_setting('rfg_settings_group', 'rfg_users');
    register_setting('rfg_settings_group', 'rfg_include_private');
    register_setting('rfg_settings_group', 'rfg_auth_token');
    register_setting('rfg_settings_group', 'rfg_disable_slideshow');
    register_setting('rfg_settings_group', 'rfg_slideshow_option');
    register_setting('rfg_settings_group', 'rfg_dismis_ss_msg');
    register_setting('rfg_settings_group', 'rfg_api_secret');
    register_setting('rfg_settings_group', 'rfg_flickr_token');
    register_setting('rfg_settings_group', 'rfg_custom_css');
    register_setting('rfg_settings_group', 'rfg_sort_order');
    register_setting('rfg_settings_group', 'rfg_cache_ttl');

    // Register javascripts
    wp_register_script('edit-galleries-script', BASE_URL . '/js/rfg_edit_galleries.js');
    wp_register_script('admin-settings-script', BASE_URL . '/js/rfg_admin_settings.js');
    wp_register_script('view-delete-galleries-script', BASE_URL . '/js/rfg_saved_galleries.js');
}

function rfg_get_all_options()
{
    return array(
        'rfg_api_key' => get_option('rfg_api_key'),
        'rfg_user_id' => get_option('rfg_user_id'),
        'rfg_photo_size' => get_option('rfg_photo_size'),
        'rfg_per_page' => get_option('rfg_per_page'),
        'rfg_sort_order' => get_option('rfg_sort_order'),
        'rfg_captions' => get_option('rfg_captions'),
        'rfg_descr' => get_option('rfg_descr'),
        'rfg_columns' => get_option('rfg_columns'),
        'rfg_credit_note' => get_option('rfg_credit_note'),
        'rfg_bg_color' => get_option('rfg_bg_color'),
        'rfg_width' => get_option('rfg_width'),
        'rfg_pagination' => get_option('rfg_pagination'),
        'rfg_api_secret' => get_option('rfg_api_secret'),
        'rfg_flickr_token' => get_option('rfg_flickr_token'),
        'rfg_slideshow_option' => get_option('rfg_slideshow_option'),
        'rfg_cache_ttl' => get_option('rfg_cache_ttl'),
    );
}

function print_all_options()
{
    $all_options = rfg_get_all_options();
    foreach ($all_options as $key => $value) {
        echo $key . ' => ' . $value . '<br />';
    }
}

function rfg_auth_init()
{
    session_start();
    global $pf;
    unset($_SESSION['afgFlickr_auth_token']);
    $pf->setToken('');
    $pf->auth('read', $_SERVER['HTTP_REFERER']);
    exit;
}

function rfg_auth_read()
{
    if ( isset($_GET['frob']) ) {
        global $pf;
        $auth = $pf->auth_getToken($_GET['frob']);
        update_option('rfg_flickr_token', $auth['token']['_content']);
        $pf->setToken($auth['token']['_content']);
        header('Location: ' . $_SESSION['afgFlickr_auth_redirect']);
        exit;
    }
}

create_afgFlickr_obj();

function rfg_admin_html_page()
{
    global $rfg_photo_size_map,
           $rfg_on_off_map,
           $rfg_descr_map, 
           $rfg_columns_map,
           $rfg_bg_color_map,
           $rfg_width_map, $pf,
           $rfg_sort_order_map,
           $rfg_slideshow_map,
           $rfg_cache_ttl_map;
    ?>
    <div class='wrap'>
    <h2><img src="<?php echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/>Responsive Flickr Gallery Settings</h2>

    <?php
    function upgradeHandler()
    {
        $galleries = get_option('rfg_galleries');
        foreach ($galleries as &$gallery) {
            if (!isset($gallery['slideshow_option']))
                $gallery['slideshow_option'] = 'colorbox';
        }
        update_option('rfg_galleries', $galleries);
        unset($gallery);
    }

    upgradeHandler();

    if ($_POST) {
        global $pf;

        if (isset($_POST['submit']) && $_POST['submit'] == 'Delete Cached Galleries') {
            delete_rfg_caches();
            echo "<div class='updated'><p><strong>Cached data deleted successfully.</strong></p></div>";
        } else if (isset($_POST['submit']) && $_POST['submit'] == 'Save Changes') {
            update_option('rfg_api_key', $_POST['rfg_api_key']);
            if (!$_POST['rfg_api_secret'] || $_POST['rfg_api_secret'] != get_option('rfg_api_secret'))
                update_option('rfg_flickr_token', '');
            update_option('rfg_api_secret', $_POST['rfg_api_secret']);
            update_option('rfg_user_id', $_POST['rfg_user_id']);
            if (ctype_digit($_POST['rfg_per_page']) && (int)$_POST['rfg_per_page']) {
                update_option('rfg_per_page', $_POST['rfg_per_page']);
            } else {
                update_option('rfg_per_page', 10);
                echo "<div class='updated'><p><strong>You entered invalid value for Per Page option.  It has been set to 10.</strong></p></div>";
            }
            update_option('rfg_sort_order', $_POST['rfg_sort_order']);
            update_option('rfg_photo_size', $_POST['rfg_photo_size']);
            update_option('rfg_captions', $_POST['rfg_captions']);
            update_option('rfg_descr', $_POST['rfg_descr']);
            update_option('rfg_columns', $_POST['rfg_columns']);
            update_option('rfg_slideshow_option', $_POST['rfg_slideshow_option']);
            update_option('rfg_width', $_POST['rfg_width']);
            update_option('rfg_bg_color', $_POST['rfg_bg_color']);
            update_option('rfg_cache_ttl', $_POST['rfg_cache_ttl']);

            if (isset($_POST['rfg_credit_note']) && $_POST['rfg_credit_note']) update_option('rfg_credit_note', 'on');
            else update_option('rfg_credit_note', 'off');

            if (isset($_POST['rfg_pagination']) && $_POST['rfg_pagination']) update_option('rfg_pagination', 'off');
            else update_option('rfg_pagination', 'on');

            echo "<div class='updated'><p><strong>Settings updated successfully.</br></br><font style='color:red'>Important Note:</font> If you have installed a caching plugin (like WP Super Cache or W3 Total Cache etc.), you may have to delete your cached pages for the settings to take effect.</strong></p></div>";
            if (get_option('rfg_api_secret') && !get_option('rfg_flickr_token')) {
                echo "<div class='updated'><p><strong>Click \"Grant Access\" button to authorize Responsive Flickr Gallery to access your private photos from Flickr.</strong></p></div>";
            }
        }
        create_afgFlickr_obj();
    }
    $url=$_SERVER['REQUEST_URI'];
    ?>
    <form method='post' action='<?php echo $url ?>'>
        <?php echo rfg_generate_version_line() ?>
               <div class="postbox-container" style="width:69%; margin-right:1%">
                  <div id="poststuff">
                     <div class="postbox" style='box-shadow:0 0 2px'>
                        <h3>Flickr Settings</h3>
                        <table class='form-table'>
                           <tr valign='top'>
                              <th scope='row'>Flickr API Key</th>
                              <td style='width:28%'><input type='text' name='rfg_api_key' size='30' value="<?php echo get_option('rfg_api_key'); ?>" ><font style='color:red; font-weight:bold'>*</font></input> </td>
                              <td><font size='2'>Don't have a Flickr API Key?  Get it from <a href="http://www.flickr.com/services/api/keys/" target='blank'>here.</a> Go through the <a href='http://www.flickr.com/services/api/tos/'>Flickr API Terms of Service.</a></font></td>
                           </tr>
                                <th scope='row'>Flickr API Secret</th>
                           <td style="vertical-align:top"><input type='text' name='rfg_api_secret' id='rfg_api_secret' value="<?php echo get_option('rfg_api_secret'); ?>"/>
                            <br /><br />
    <?php 
    if (get_option('rfg_api_secret')) {
        if (get_option('rfg_flickr_token')) {
            echo "<input type='button' class='button-secondary' value='Access Granted' disabled=''";
        } else {
            ?>
                <input type="button" class="button-primary" value="Grant Access" onClick="document.location.href='<?php echo get_admin_url() .  'admin-ajax.php?action=rfg_gallery_auth'; ?>';"/>
            <?php 
        }
    } else {
        echo "<input type='button' class='button-secondary' value='Grant Access' disabled=''";    
    }?>
                           </td>
                           <td style="vertical-align:top"><font size='2'><b>ONLY</b> If you want to include your <b>Private Photos</b> in your galleries, enter your Flickr API Secret here
                            and click Save Changes.</font>
                        </td>
                    </tr>

                           <tr valign='top'>
                              <th scope='row'>Flickr User ID</th>
                              <td><input type='text' name='rfg_user_id' size='30' value="<?php echo get_option('rfg_user_id'); ?>" /><font style='color:red; font-weight:bold'>*</font> </td>
                              <td><font size='2'>Don't know your Flickr User ID?  Get it from <a href="http://idgettr.com/" target='blank'>here.</a></font></td>
                           </tr>
                        </table>
                     </div>
                  </div>

                  <div id="poststuff">
                     <div class="postbox" style='box-shadow:0 0 2px'>
                        <h3>Gallery Settings</h3>
                        <table class='form-table'>

                           <tr valign='top'>
                              <th scope='row'>Max Photos Per Page</th>
                              <td style="width:28%">
                                  <input type='text' 
                                         name='rfg_per_page' 
                                         id='rfg_per_page' 
                                         onblur='verifyPerPageBlank()' size='3' maxlength='3' 
                                         value="<?php echo get_option('rfg_per_page')?get_option('rfg_per_page'):10;?>" />
                                         <font style='color:red; font-weight:bold'>*</font>
                              </td>
                           </tr>

                            <tr valign='top'>
                              <th scope='row'>Sort order of Photos</th>
                              <td><select type='text' name='rfg_sort_order' id='rfg_sort_order'>
                                    <?php echo rfg_generate_options($rfg_sort_order_map, get_option('rfg_sort_order', 'flickr')); ?>
                              </select>
                              <td><font size='2'>Set the sort order of the photos as per your liking and forget about how photos are arranged on Flickr.</font></td>
                              </td>
                           </tr>

                           <tr valign='top'>
                              <th scope='row'>Size of the Photos</th>
                              <td><select name='rfg_photo_size' id='rfg_photo_size'>
                                    <?php echo rfg_generate_options($rfg_photo_size_map, get_option('rfg_photo_size', '_m')); ?>
                              </select></td>
                           </tr>

                           <tr valign='top'>
                              <th scope='row'>Photo Titles</th>
                              <td><select name='rfg_captions'>
                                    <?php echo rfg_generate_options($rfg_on_off_map, get_option('rfg_captions', 'on')); ?>
                              </select></td>
                              <td><font size='2'>Photo Title setting applies only to Thumbnail (and above) size photos.</font></td>
                           </tr>

                           <tr valign='top'>
                              <th scope='row'>Photo Descriptions</th>
                              <td><select name='rfg_descr'>
                                    <?php echo rfg_generate_options($rfg_descr_map, get_option('rfg_descr', 'off')); ?>
                              </select></td>
                              <td><font size='2'>Photo Description setting applies only to Small and Medium size photos.</td>
                              </tr>

                              <tr valign='top'>
                                 <th scope='row'>Number of Columns</th>
                                 <td><select name='rfg_columns'>
                                       <?php echo rfg_generate_options($rfg_columns_map, get_option('rfg_columns', '2')); ?>
                                 </select></td>
                              </tr>

                              <tr valign='top'>
                                 <th scope='row'>Slideshow Behavior</th>
                                 <td><select name='rfg_slideshow_option'>
                                       <?php echo rfg_generate_options($rfg_slideshow_map, get_option('rfg_slideshow_option', 'colorbox')); ?>
                                 </select></td>
                                 <td><font size='2'>
                                  If you use ColorBox to display photos in larger size you'll also have a slidehsow for all photos from a gallery.
                                  A slideshow contains all photos of a gallery - even if pagination is enabled.<br />
                                  Be aware that page load times can suffer if you choose to use ColorBox slideshows for galleries containing many pictures.
                                  </font></td>
                              </tr>


                              <tr valign='top'>
                                 <th scope='row'>Background Color</th>
                                 <td><select name='rfg_bg_color'>
                                       <?php echo rfg_generate_options($rfg_bg_color_map, get_option('rfg_bg_color', 'Transparent')); ?>
                                 </select></td>
                              </tr>

                              <tr valign='top'>
                                 <th scope='row'>Gallery Width</th>
                                 <td><select name='rfg_width'>
                                       <?php echo rfg_generate_options($rfg_width_map, get_option('rfg_width', 'auto')); ?>
                                 </select></td>
                                 <td><font size='2'>Width of the Gallery is relative to the width of the page where Gallery is being generated.  <i>Automatic</i> is 100% of page width.</font></td>
                              </tr>

                              <tr valign='top'>
                                 <th scope='row'>Disable Pagination?</th>
                                 <td>
                                    <input type='checkbox' 
                                           name='rfg_pagination' 
                                           value='off'
                                           <?php if (get_option('rfg_pagination', 'off') == 'off') echo 'checked=\'\''; ?>
                                    />
                                 </td>
                                 <td><font size='2'>Useful when displaying gallery in a sidebar widget where you want only few recent photos.</td>
                              </tr>

                              <tr valign='top'>
                                 <th scope='row'>Cache TTL</th>
                                 <td><select name='rfg_cache_ttl'>
                                       <?php echo rfg_generate_options($rfg_cache_ttl_map, get_option('rfg_cache_ttl', '3')); ?>
                                 </select></td>
                                 <td><font size='2'>
                                     Number of days the Flick API call results will be cached in the database.
                                     Calling the external API is "expensive" and makes the site slow.
                                     Set low if galleries on flickr change often.
                                     Set high if galleries don't change often to save "expensive" API calls 
                                     and speed up the galleries on your site.</font></td>
                              </tr>
  
                              </table>
                        </div></div>
                        <input type="submit" name="submit" id="rfg_save_changes" class="button-primary" value="Save Changes" />
                        <br /><br />
                        <div id="poststuff">
                           <div class="postbox" style='box-shadow:0 0 2px'>
                              <h3>Your Photostream Preview</h3>
                              <table class='form-table'>
                                 <tr><th>If your Flickr Settings are correct, 5 of your recent photos from your Flickr photostream should appear here.</th></tr>
                                 <td>
    <?php
    global $pf;
    if (get_option('rfg_flickr_token')) {
            $rsp_obj = $pf->people_getPhotos(get_option('rfg_user_id'), array('per_page' => 5, 'page' => 1));
    } else {
        $rsp_obj = $pf->people_getPublicPhotos(get_option('rfg_user_id'), null, null, 5, 1);
    }
    if (!$rsp_obj) echo rfg_error();
    else {
        foreach ($rsp_obj['photos']['photo'] as $photo) {
            $photo_url = "http://farm{$photo['farm']}.static.flickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}_s.jpg";
            echo "<img src=\"$photo_url\"/>&nbsp;&nbsp;&nbsp;";
        }
    }
    ?>
                                    <br />
                                    Note:  This preview is based on the Flickr Settings only.  Gallery Settings 
                                    have no effect on this preview.  You will need to insert gallery code to a post 
                                    or page to actually see the Gallery.
                                 </td>
                           </table></div>
                            <input type="submit" name="submit" class="button-secondary" value="Delete Cached Galleries"/>
                        </div>
    <?php
    if (DEBUG) {
        print_all_options();
    }
    ?>
                     </div>
                     <div class="postbox-container" style="width: 29%;">
    <?php
    $message = "<b>What are Default Settings?</b> - Default Settings serve as a 
        template for the galleries.  When you create a new gallery, you can assign 
        <i>Use Default</i> to a setting.  Such a setting will reference the <b>Default 
        Settings</b> instead of a specific setting defined for that particular 
        gallery. <br /> <br />
        When you change any of <b>Default Settings</b>, all the settings in a gallery 
        referencing the <b>Default Settings</b> will inherit the new value.<br /><br />
        <font color='red'><b>Important Note about Private Photos:</b></font><br/>To access
        your private photos from Flickr, make sure that your App's authentication
        type is set to <b>Web Application</b> and the <b>Callback URL</b>
        points to <font color='blue'><i>" . get_admin_url() . "</i></font>
        ";
    echo rfg_box('Help', $message);

    $message = "Just insert the code <strong><font color='steelblue'>[RFG_gallery]</font></strong> in any of your posts or pages to display the Responsive Flickr Gallery.
        <br /><p style='text-align:center'><i>-- OR --</i></p>You can create a new Responsive Flickr Gallery with different settings on page <a href='{$_SERVER['PHP_SELF']}?page=rfg_add_gallery_page'>Add Galleries.";
    echo rfg_box('Usage Instructions', $message);

    echo rfgDonateBox(); 
    echo rfg_share_box();
    ?>
        </div>
            </form>
    <?php
}
