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

function rfg_admin_enqueue_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('rfg_custom_css_js', BASE_URL . "/CodeMirror/lib/codemirror.js");
    wp_enqueue_script('rfg_custom_css_theme_js', BASE_URL . "/CodeMirror/mode/css/css.js");
    wp_enqueue_style('rfg_custom_css_style', BASE_URL . "/CodeMirror/lib/codemirror.css");
    wp_enqueue_style('rfg_custom_css_theme_css', BASE_URL . "/CodeMirror/theme/cobalt.css");
    wp_enqueue_style('rfg_custom_css_style', BASE_URL . "/CodeMirror/css/docs.css");
}

if (is_admin()) {
    add_action('admin_enqueue_scripts', 'rfg_admin_enqueue_scripts');
    add_action('admin_head', 'rfg_advanced_headers');
}

function rfg_advanced_headers()
{
    // echo '';
}

function rfg_advanced_settings_page()
{
    $url=$_SERVER['REQUEST_URI'];
    ?>
    <div class='wrap'>
    <h2><img src="<?php echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/></a>Advanced Settings | Reponsive Flickr Gallery</h2>

    <?php
    if (isset($_POST['rfg_advanced_save_changes']) && $_POST['rfg_advanced_save_changes']) {
        update_option('rfg_disable_slideshow', isset($_POST['rfg_disable_slideshow'])? $_POST['rfg_disable_slideshow']: '');
        update_option('rfg_slideshow_option', $_POST['rfg_slideshow_option']);
        update_option('rfg_custom_css', $_POST['rfg_custom_css']);
        echo "<div class='updated'><p><strong>Settings updated successfully.</strong></p></div>";
    }
    ?>
    <form method='post' action='<?php echo $url ?>'>
       <?php echo rfg_generate_version_line() ?>
       <div class="postbox-container" style="width:69%; margin-right:1%">
          <div id="poststuff">
             <div class="postbox" style='box-shadow:0 0 2px'>
                <h3>Custom CSS</h3>
                <div style="background-color:#FFFFE0; border-color:#E6DB55; maargin:5px 0 15px; border-radius:3px 3px 3px 3px; border-width: 1px; border-style: solid; padding: 8px 10px; line-height: 20px">
                  Check <a href='<?php echo BASE_URL . '/rfg.css';?>' target='_blank'>rfg.css</a> to see existing classes and properties for gallery which you can redefine here. Note that there is no validation applied to CSS Code entered here, so make sure that you enter valid CSS.
                </div><br/>
              <textarea id='rfg_custom_css' name='rfg_custom_css'><?php echo get_option('rfg_custom_css');?></textarea>
              <script type="text/javascript">var myCodeMirror = CodeMirror.fromTextArea(document.getElementById('rfg_custom_css'), {lineNumbers: true, indentUnit: 4, theme: "cobalt", matchBrackets: true} );</script>
          </div>
       </div>
       <input type="submit" name="rfg_advanced_save_changes" id="rfg_advanced_save_changes" class="button-primary" value="Save Changes" />
    </div>

    <div class="postbox-container" style="width: 29%;">
        <?php
        $message = "Settings on this page are global and hence apply to all your Galleries.";
        echo rfg_box('Help', $message);
        echo rfgDonateBox();
        echo rfg_share_box();
        ?>
    </div>
    </form>
    </div>
    <?php
}
