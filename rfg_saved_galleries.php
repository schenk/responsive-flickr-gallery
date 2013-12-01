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

function rfg_view_delete_galleries_header()
{
    $params = array(
        'galleries' => json_encode(get_option('rfg_galleries')),
    );
    wp_enqueue_script('view-delete-galleries-script');
    wp_localize_script('view-delete-galleries-script', 'genparams', $params);
}

function rfg_view_delete_galleries()
{
    ?>
    <div class='wrap'>
    <h2><img src="<?php echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/>Saved Galleries | Responsive Flickr Gallery</h2>

    <?php
    if (isset($_POST['submit']) && $_POST['submit'] == 'Delete Selected Galleries') {
        $galleries = get_option('rfg_galleries');
        foreach ($galleries as $id => $ginfo) {
            if ($id) {
                if (isset($_POST['delete_gallery_' . $id]) && $_POST['delete_gallery_' . $id] == 'on') {
                    unset($galleries[$id]);
                }
            }
        }
        update_option('rfg_galleries', $galleries);
        ?>
        <div class="updated"><p><strong><?php echo 'Galleries deleted successfully.' ?></strong></p></div> <?php
    }
    echo rfg_generate_version_line();
    $url=$_SERVER['REQUEST_URI'];
    ?>

      <form onsubmit="return verifySelectedGalleries()" method='post' action='<?php echo $url ?>'>
         <div class="postbox-container" style="width:69%; margin-right:1%">
            <div id="poststuff">
               <div class="postbox" style='box-shadow:0 0 2px'>
                  <h3>Saved Galleries</h3>
                  <table class='form-table' style='margin-top:0'>
                     <tr style='border:1px solid Gainsboro' valign='top'>
                        <th cope='row'><input type='checkbox' name='delete_all_galleries' id='delete_all_galleries'
                           onclick="CheckAllDeleteGalleries()"/></th>
                        <th scope='row'><strong>ID</strong></th>
                        <th scope='row'><strong>Name</strong></th>
                        <th scope='row'><strong>Gallery Code</strong></th>
                        <th scope='row'><strong>Description</strong></th>
                     </tr>
                     <?php
                     $galleries = get_option('rfg_galleries');
                     foreach ($galleries as $id => $ginfo) {
                        echo "<tr style='border:1px solid Gainsboro' valign='top'>";
                        if ($id)
                            echo "<td style='width:4%'><input type='checkbox' name='delete_gallery_$id' id='delete_gallery_$id' /></td>";
                        else
                            echo "<td style='width:4%'></td>";
                        echo "<td style='width:12%'>{$id}</td>";
                        if ($id) {
                            echo "<th style='width:22%'>
                                <a href=\"{$_SERVER['PHP_SELF']}?page=rfg_edit_galleries_page&gallery_id=$id\" title='Edit this gallery'>
                        {$ginfo['name']}</a></th>";
                            echo "<td style='width:22%; color:steelblue; font-size:110%;' onfocus='this.select()'>[RFG_gallery id='$id']</td>";
                        } else {
                            echo "<th style='width:22%'>{$ginfo['name']}</th>";
                            echo "<td style='width:22%; color:steelblue; font-size:110%;' onfocus='this.select()'>[RFG_gallery]</td>";
                        }
                        echo "<td>{$ginfo['gallery_descr']}</td>";
                        echo "</tr>";
                     }
                     ?>
                  </table>
            </div></div>
            <input type="submit" name="submit" class="button" value="Delete Selected Galleries" />
         </div>
         <div class="postbox-container" style="width: 29%;">
         <?php
         echo rfg_usage_box('the Gallery Code');
         echo rfgDonateBox();
         echo rfg_share_box();
         ?>
         </div>
      </form>
      <?php
}
