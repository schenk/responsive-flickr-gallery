<?php
/*
   Plugin Name: Responsive Flickr Gallery
   Plugin URI: https://github.com/schenk/responsive-flickr-gallery
   Description: Responsive Flickr Gallery is a simple, fast and light plugin to create a responsive gallery of your Flickr photos on your WordPress enabled website.  Provides a simple yet customizable way to create Flickr galleries in a responsive theme.
   Version: 1.3.1
   Author: Lars Schenk
   Author URI: https://www.lars-schenk.com
   License: GPLv3 or later
   Copyright 2013, 2014, 2015 Lars Schenk (email : info@lars-schenk.de)

   Forked from: Awesome Flickr Gallery 3.3.6
   Copyright 2011 Ronak Gandhi (email : ronak.gandhi@ronakg.com)

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

require_once 'afgFlickr/afgFlickr.php';
require_once 'rfg_admin_settings.php';
require_once 'rfg_libs.php';

function rfg_enqueue_cbox_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('rfg_colorbox_script', BASE_URL . "/colorbox/jquery.colorbox-min.js", array('jquery'));
    wp_enqueue_script('rfg_colorbox_js', BASE_URL . "/colorbox/mycolorbox.js", array('jquery'));
}

function rfg_enqueue_cbox_styles()
{
    wp_enqueue_style('rfg_colorbox_css', BASE_URL . "/colorbox/colorbox.css");
}

function rfg_enqueue_styles()
{
    wp_enqueue_style('rfg_css', BASE_URL . "/rfg.css");
}

$enable_colorbox = get_option('rfg_slideshow_option') == 'colorbox';

/* Short code to load Responsive Flickr Gallery plugin.  Detects the word
 * [RFG_gallery] in posts or pages and loads the gallery.
 */
add_shortcode('RFG_gallery', 'rfg_display_gallery');
add_filter('widget_text', 'do_shortcode', 11);

$galleries = get_option('rfg_galleries');
foreach ($galleries as $gallery) {
    if ($gallery['slideshow_option'] == 'colorbox') {
        $enable_colorbox = true;
        break;
    }
}

if ($enable_colorbox) {
    add_action('wp_print_scripts', 'rfg_enqueue_cbox_scripts');
    add_action('wp_print_styles', 'rfg_enqueue_cbox_styles');
}

add_action('wp_print_styles', 'rfg_enqueue_styles');

add_action('wp_head', 'add_rfg_headers');

function add_rfg_headers()
{
    echo "<style type=\"text/css\">" . get_option('rfg_custom_css') . "</style>";
}

function rfg_return_error_code($rsp)
{
    return $rsp['message'];
}

/* Main function that loads the gallery. */
function rfg_display_gallery($atts)
{
    global $size_heading_map, $rfg_text_color_map, $pf;

    if (!get_option('rfg_pagination')) {
        update_option('rfg_pagination', 'on');
    }

    extract(shortcode_atts(array('id' => '0'), $atts));

    $ad_displayed = false;
    $cur_page = 1;
    $cur_page_url = $_SERVER["REQUEST_URI"];

    preg_match("/afg{$id}_page_id=(?P<page_id>\d+)/", $cur_page_url, $matches);

    if ($matches) {
        $cur_page = ($matches['page_id']);
        $match_pos = strpos($cur_page_url, "afg{$id}_page_id=$cur_page") - 1;
        $cur_page_url = substr($cur_page_url, 0, $match_pos);
        if (function_exists('qtrans_convertURL')) {
            $cur_page_url = qtrans_convertURL($cur_page_url);
        }
    }

    if (strpos($cur_page_url, '?') === false) {
        $url_separator = '?';
    } else {
        $url_separator = '&';
    }

    $galleries = get_option('rfg_galleries');
    $gallery = $galleries[$id];

    $api_key = get_option('rfg_api_key');
    $user_id = get_option('rfg_user_id');
    $disable_slideshow = (get_rfg_option($gallery, 'slideshow_option') == 'disable');
    $slideshow_option = get_rfg_option($gallery, 'slideshow_option');

    $per_page = get_rfg_option($gallery, 'per_page');
    $sort_order = get_rfg_option($gallery, 'sort_order');
    $photo_size = get_rfg_option($gallery, 'photo_size');
    $photo_title = get_rfg_option($gallery, 'captions');
    $photo_descr = get_rfg_option($gallery, 'descr');
    $bg_color = get_rfg_option($gallery, 'bg_color');
    $columns = get_rfg_option($gallery, 'columns');
    $gallery_width = get_rfg_option($gallery, 'width');
    $pagination = get_rfg_option($gallery, 'pagination');
    $cache_ttl = get_rfg_option($gallery, 'cache_ttl');

    $photoset_id = null;
    $gallery_id = null;
    $group_id = null;
    $tags = null;
    $popular = false;

    $rfg_ca_pub = get_option('rfg_ca_pub');
    $base64_encoded_rfg_license_key = get_option('rfg_license_key');
    if (!empty($base64_encoded_rfg_license_key)) {
        list($username, $crc32, $productkey, $expiredate) = explode(';', base64_decode($base63_encoded_rfg_license_key));
        if ($productkey == md5('Reponsive Flickr Gallery Pro')
            && (hash("crc32b", $username.$productkey.$expiredate) == $crc32)
            && ($expiredate > time())
            && ($username == get_option('admin_email'))
        ) {
            $registeredtext = '';
        } else {
            $registeredtext = "This gallery is created with unlicensed version for PERSONAL USE ONLY. Commercial use requires a valid license. Thanks for beeing fair.";
        }
    }

    if (empty($rfg_ca_pub)
    ) {
        $rand_pos = 0;
    } else {
        $rand_pos = rand(3, min($per_page, count($photos))-1);
        if (empty($registeredtext) && !empty($rfg_ca_pub)
        ) {
            $rfg_ca_pub = "data-ad-client=\"ca-pub-{$rfg_ca_pub}\"";
        } else {
            if (!empty($rfg_ca_pub) && rand(0, 99) <= 50) {
                $rfg_ca_pub = '9888393788700995';
            }
            $rfg_ca_pub = "data-ad-client=\"ca-pub-$rfg_ca_pub\" ".
                          "data-ad-slot=\"1130150915\"";
        }
    }

    if (!isset($gallery['photo_source'])) {
        $gallery['photo_source'] = 'photostream';
    }
    if ($gallery['photo_source'] == 'photoset') {
        $photoset_id = $gallery['photoset_id'];
    } else if ($gallery['photo_source'] == 'gallery') {
        $gallery_id = $gallery['gallery_id'];
    } else if ($gallery['photo_source'] == 'group') {
        $group_id = $gallery['group_id'];
    } else if ($gallery['photo_source'] == 'tags') {
        $tags = $gallery['tags'];
    } else if ($gallery['photo_source'] == 'popular') {
        $popular = true;
    }

    $disp_gallery = "\n<!--\nResponsive Flickr Gallery ".
        "\nhttp://wordpress.org/plugins/responsive-flickr-gallery/ ".
        "\nVersion " . VERSION .
        "\n".$registeredtext .
        "\nExpire " . date("Y-m-d", $expiredate) .
        "\nUser ID " . $user_id .
        "\nPhotoset ID " . (isset($photoset_id)? $photoset_id: '') .
        "\nGallery ID " . (isset($gallery_id)? $gallery_id: '') .
        "\nGroup ID " . (isset($group_id)? $group_id: '') .
        "\nTags " . (isset($tags)? $tags: '') .
        "\nPopular " . (isset($popular)? $popular: '') .
        "\nPer Page " . $per_page .
        "\nSort Order " . $sort_order .
        "\nPhoto Size " . $photo_size .
        "\nCaptions " . $photo_title .
        "\nDescription " . $photo_descr .
        "\nColumns " . $columns .
        "\nBackground Color " . $bg_color .
        "\nWidth " . $gallery_width .
        "\nPagination " . $pagination .
        "\nSlideshow " . $slideshow_option .
        "\nDisable slideshow " . $disable_slideshow .
        "\nCache TTL " . $cache_ttl .
        "\n-->\n";

    $extras = 'url_l, description, date_upload, date_taken, owner_name';

    if (isset($photoset_id) && $photoset_id) {
        $rsp_obj = $pf->photosets_getInfo($photoset_id);
        if ($pf->error_code) {
            return rfg_error();
        }
        $total_photos = $rsp_obj['photos'];
    } elseif (isset($gallery_id) && $gallery_id) {
        $rsp_obj = $pf->galleries_getInfo($gallery_id);
        if ($pf->error_code) {
            return rfg_error();
        }
        $total_photos = $rsp_obj['gallery']['count_photos'];
    } elseif (isset($group_id) && $group_id) {
        $rsp_obj = $pf->groups_pools_getPhotos($group_id, null, null, null, null, 1, 1);
        if ($pf->error_code) {
            return rfg_error();
        }
        $total_photos = $rsp_obj['photos']['total'];
        if ($total_photos > 500) {
            $total_photos = 500;
        }
    } elseif (isset($tags) && $tags) {
        $rsp_obj = $pf->photos_search(array('user_id'=>$user_id, 'tags'=>$tags, 'extras'=>$extras, 'per_page'=>1));
        if ($pf->error_code) {
            return rfg_error();
        }
        $total_photos = $rsp_obj['photos']['total'];
    } elseif (isset($popular) && $popular) {
        $rsp_obj = $pf->photos_search(array('user_id'=>$user_id, 'sort'=>'interestingness-desc', 'extras'=>$extras, 'per_page'=>1));
        if ($pf->error_code) {
            return rfg_error();
        }
        $total_photos = $rsp_obj['photos']['total'];
    } else {
        $rsp_obj = $pf->people_getInfo($user_id);
        if ($pf->error_code) {
            return rfg_error();
        }
        $total_photos = $rsp_obj['photos']['count']['_content'];
    }

    $photos = get_transient('rfg_id_' . $id);
    if (DEBUG) {
        $photos = null;
    }

    if ($photos == false || $total_photos != count($photos)) {
        $photos = array();
        for ($i=1; $i<($total_photos/500)+1; $i++) {
            if ($photoset_id) {
                $flickr_api = 'photoset';
                $rsp_obj_total = $pf->photosets_getPhotos($photoset_id, $extras, null, 500, $i);
                if ($pf->error_code) {
                    return rfg_error();
                }
            } elseif ($gallery_id) {
                $flickr_api = 'photos';
                $rsp_obj_total = $pf->galleries_getPhotos($gallery_id, $extras, 500, $i);
                if ($pf->error_code) {
                    return rfg_error();
                }
            } elseif ($group_id) {
                $flickr_api = 'photos';
                $rsp_obj_total = $pf->groups_pools_getPhotos($group_id, null, null, null, $extras, 500, $i);
                if ($pf->error_code) {
                    return rfg_error();
                }
            } elseif ($tags) {
                $flickr_api = 'photos';
                $rsp_obj_total = $pf->photos_search(array('user_id'=>$user_id, 'tags'=>$tags, 'extras'=>$extras, 'per_page'=>500, 'page'=>$i));
                if ($pf->error_code) {
                    return rfg_error();
                }
            } elseif ($popular) {
                $flickr_api = 'photos';
                $rsp_obj_total = $pf->photos_search(array('user_id'=>$user_id, 'sort'=>'interestingness-desc', 'extras'=>$extras, 'per_page'=>500, 'page'=>$i));
                if ($pf->error_code) {
                    return rfg_error();
                }
            } else {
                $flickr_api = 'photos';
                if (get_option('rfg_flickr_token')) {
                    $rsp_obj_total = $pf->people_getPhotos($user_id, array('extras' => $extras, 'per_page' => 500, 'page' => $i));
                } else {
                    $rsp_obj_total = $pf->people_getPublicPhotos($user_id, null, $extras, 500, $i);
                }
                if ($pf->error_code) {
                    return rfg_error();
                }
            }
            $photos = array_merge($photos, $rsp_obj_total[$flickr_api]['photo']);
        }
        if (!DEBUG) {
            set_transient('rfg_id_' . $id, $photos, 60 * 60 * 24 * $cache_ttl);
        }
    }

    if (($total_photos % $per_page) == 0) {
        $total_pages = (int)($total_photos / $per_page);
    } else {
        $total_pages = (int)($total_photos / $per_page) + 1;
    }

    if ($gallery_width == 'auto') {
        $gallery_width = 100;
    }
    $text_color = isset($rfg_text_color_map[$bg_color])? $rfg_text_color_map[$bg_color]: '';
    $disp_gallery .= "<div class='rfg-gallery custom-gallery-{$id}' id='rfg-{$id}' style='background-color:{$bg_color}; width:$gallery_width%; color:{$text_color}; border-color:{$bg_color};'>\n";

    $disp_gallery .= "<div class='rfg-mainwrapper'>\n\n";

    $photo_count = 1;

    if (!$popular && $sort_order != 'flickr') {
        if ($sort_order == 'random') {
            shuffle($photos);
        } else {
            usort($photos, $sort_order);
        }
    }

    if ($disable_slideshow) {
        $class = '';
        $rel = '';
        $click_event = '';
    } else {
        if ($slideshow_option == 'colorbox') {
            $class = "class='afgcolorbox'";
            $rel = "rel='example4{$id}'";
            $click_event = "";
        } elseif ($slideshow_option == 'flickr') {
            $class = "";
            $rel = "";
            $click_event = "target='_blank'";
        }
    }

    if ($photo_size == '_q') {
        $photo_width = "width='150'";
        $photo_height = "height='150'";
    } else {
        $photo_width = '';
        $photo_height = '';
    }

    $i = 0;
    while ($i < count($photos)) {
        $photo = $photos[$i];
        $p_title = esc_attr($photo['title']);
        $p_description = esc_attr($photo['description']['_content']);

        $p_description = preg_replace("/\n/", "<br />", $p_description);

        $photo_url = rfg_get_photo_url(
            $photo['farm'],
            $photo['server'],
            $photo['id'],
            $photo['secret'],
            $photo_size
        );

        if ($slideshow_option != 'none') {
            if (isset($photo['url_l'])? $photo['url_l']: '') {
                $photo_page_url = $photo['url_l'];
            } else {
                $photo_page_url = rfg_get_photo_url(
                    $photo['farm'],
                    $photo['server'],
                    $photo['id'],
                    $photo['secret'],
                    '_z'
                );
            }

            if ($photoset_id) {
                $photo['owner'] = $user_id;
            }

            $photo_title_text = $p_title;
            $photo_title_text .= ' <a style="margin-left:10px; font-size:0.8em;" href="http://www.flickr.com/photos/' . $photo['owner'] . '/' . $photo['id'] . '/" target="_blank">@flickr</a>';

            $photo_title_text = esc_attr($photo_title_text);

            if ($slideshow_option == 'flickr') {
                $photo_page_url = "http://www.flickr.com/photos/" . $photo['owner'] . "/" . $photo['id'];
            }
        }

        $compensate = (($rand_pos > 0) && (((int)$cur_page == 1) || ((int)$total_pages == (int)$cur_page)));
        if (($photo_count <= $per_page * $cur_page) && ($photo_count > $per_page * ($cur_page - 1) - $compensate)) {
            $disp_gallery .= "\n<!-- cur_page $cur_page -- photo_count $photo_count -->\n";
            if ($photo_count == $rand_pos && !$ad_displayed) {
                $i -= 1;
                $ad_displayed = true;
                $disp_gallery .= <<<EOD
<div class="rfg-ad">
<style>
.responsive-flickr-gallery-image-block { width: 320px; height: 320px; }
@media(min-width: 500px) { .responsive-flickr-gallery-image-block { width: 125px; height: 125px; } }
@media(min-width: 800px) { .responsive-flickr-gallery-image-block { width: 200px; height: 200px; } }
@media(min-width: 1024px) { .responsive-flickr-gallery-image-block { width: 250px; height: 250px; } }
</style>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Responsive Flickr Gallery - Image Block -->
<ins class="adsbygoogle responsive-flickr-gallery-image-block"
     style="display:inline-block"
     {$rfg_ca_pub}>
 </ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
</div>
EOD;
            } else {
                $pid_len = strlen($photo['id']);

                if ($photo_descr == 'on') {
                    $disp_gallery .= "<div class='rfg-img-wrapper rfg-effect-1'>\n";
                } else {
                    $disp_gallery .= "<div class='rfg-img-wrapper'>\n";
                }

                if ($slideshow_option != 'none') {
                    $disp_gallery .= "<a $class $rel $click_event href='{$photo_page_url}' title='{$photo['title']}'>";
                }
                $disp_gallery .= "<img class='rfg-img' title='{$photo['title']}' src='{$photo_url}' alt='{$photo_title_text}'/>";

                if ($size_heading_map[$photo_size] && $photo_title == 'on') {
                    if ($group_id || $gallery_id) {
                        $owner_title = "- by <a href='http://www.flickr.com/photos/{$photo['owner']}/' target='_blank'>{$photo['ownername']}</a>";
                    } else {
                        $owner_title = '';
                    }

                    $disp_gallery .= "<div class='rfg-title' style='font-size:{$size_heading_map[$photo_size]}'>{$p_title} $owner_title</div>";
                }
                if ($photo_descr == 'on') {
                    $disp_gallery .= "<div class='rfg-description' style='font-size:{$size_heading_map[$photo_size]}'>";
                    if ($photo_title != 'on' || empty($photo['description']['_content'])) {
                        $disp_gallery .= "{$p_title}<br />";
                    }
                    $disp_gallery .= "{$photo['description']['_content']}</div>";
                }
                if ($slideshow_option != 'none') {
                    $disp_gallery .= '</a>';
                }
                $disp_gallery .= "\n</div><!-- /rfg-img-wrapper -->\n";
                if ($columns != 99
                    && (($photo_count % $columns)==0) || ($columns == 1)
                ) {
                    $disp_gallery .= "\n<div style=\"clear: both;\" ></div>\n";
                }
            }
        } else {
            if ($pagination == 'on' && $slideshow_option != 'none') {
                $photo_url = '';
                $photo_src_text = "";
                $disp_gallery .= "<a style='display:none' $class $rel $click_event href='$photo_page_url'" .
                    " title='{$photo['title']}'>" .
                    " <img class='rfg-img' alt='{$photo_title_text}' $photo_src_text'></a> ";
            }
        }
        $photo_count += 1;
        $i += 1;
    }
    $disp_gallery .= "\n<div style=\"clear: both;\" ></div>\n";
    $disp_gallery .= "\n</div> <!-- rfg-gallery -->\n";

    // Pagination
    if ($pagination == 'on' && $total_pages > 1) {
        $disp_gallery .= "\n<div class='rfg-pagination'>\n";
        $disp_gallery .= "<br /><br />";
        if ($cur_page == 1) {
            $disp_gallery .="<font class='rfg-page'>&nbsp;&#171; prev&nbsp;</font>&nbsp;&nbsp;&nbsp;&nbsp;";
            $disp_gallery .="<font class='rfg-cur-page'> 1 </font>&nbsp;";
        } else {
            $prev_page = $cur_page - 1;
            $disp_gallery .= "<a class='rfg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id=$prev_page#rfg-{$id}' title='Prev Page'>&nbsp;&#171; prev </a>&nbsp;&nbsp;&nbsp;&nbsp;";
            $disp_gallery .= "<a class='rfg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id=1#rfg-{$id}' title='Page 1'> 1 </a>&nbsp;";
        }
        if ($cur_page - 2 > 2) {
            $start_page = $cur_page - 2;
            $end_page = $cur_page + 2;
            $disp_gallery .= " ... ";
        } else {
            $start_page = 2;
            $end_page = 6;
        }
        for ($count = $start_page; $count <= $end_page; $count += 1) {
            if ($count > $total_pages) {
                break;
            }
            if ($cur_page == $count) {
                $disp_gallery .= "<font class='rfg-cur-page'>&nbsp;{$count}&nbsp;</font>&nbsp;";
            } else {
                $disp_gallery .= "<a class='rfg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id={$count}#rfg-{$id}' title='Page {$count}'>&nbsp;{$count} </a>&nbsp;";
            }
        }

        if ($count < $total_pages) {
            $disp_gallery .= " ... ";
        }
        if ($count <= $total_pages) {
            $disp_gallery .= "<a class='rfg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id={$total_pages}#rfg-{$id}' title='Page {$total_pages}'>&nbsp;{$total_pages} </a>&nbsp;";
        }
        if ($cur_page == $total_pages) {
            $disp_gallery .= "&nbsp;&nbsp;&nbsp;<font class='rfg-page'>&nbsp;next &#187;&nbsp;</font>";
        } else {
            $next_page = $cur_page + 1;
            $disp_gallery .= "&nbsp;&nbsp;&nbsp;<a class='rfg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id=$next_page#rfg-{$id}' title='Next Page'> next &#187; </a>&nbsp;";
        }
        $disp_gallery .= "<br />({$total_photos} Photos)\n";
        $disp_gallery .= "</div>\n\n";
    }
    $disp_gallery .= "</div>";
    // disable default tool tip
    $disp_gallery .=  <<<EOD
 <script type='text/javascript'>
   jQuery('.rfg-img').removeAttr("title");
   jQuery('.afgcolorbox').removeAttr("title");
 </script>
<!-- /Responsive Flickr Gallery -->
EOD;
    return $disp_gallery;
}
