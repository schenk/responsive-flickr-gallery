mjson = jQuery.noConflict();

mjson(document).ready(function(){
    loadGallerySettings();
    showHidePerPage();
    });

function verifyBlank() {
    var per_page = document.getElementById('rfg_per_page');
    var gname = document.getElementById("rfg_add_gallery_name");
    var submit_button = document.getElementById('rfg_save_changes');
    if (per_page.value == '') {
        alert('Per Page can not be blank.');
        submit_button.disabled = true;
        return;
    }
    else if (gname.value == '') {
        alert('Gallery Name can not be blank.');
        submit_button.disabled = true;
        return;
    }
    submit_button.disabled = false;
}

function showHidePerPage() {
    var per_page_check = document.getElementById('rfg_per_page_check');
    var per_page = document.getElementById('rfg_per_page');

    if (per_page_check.checked == true) {
        per_page.disabled = true;
        per_page.value = genparams.default_per_page;
    }
    else {
        per_page.disabled = false;
        var gallery = document.getElementById('rfg_photo_gallery');
        var galleries = genparams.galleries.replace(/&quot;/g, '"');
        var jgalleries = jQuery.parseJSON(galleries);
        active_gallery = jgalleries[gallery.value];
        per_page.value = active_gallery.per_page || genparams.default_per_page;
    }
}

function getPhotoSourceType() {
    var source_element = document.getElementById('rfg_photo_source_type');
    var photosets_box = document.getElementById('rfg_photosets_box');
    var galleries_box = document.getElementById('rfg_galleries_box');
    var groups_box = document.getElementById('rfg_groups_box');
    var tags_box = document.getElementById('rfg_tags');
    var source_label = document.getElementById('rfg_photo_source_label');
    var help_text = document.getElementById('rfg_source_help');

    if (source_element.value == 'photostream' || source_element.value == 'popular') {
        source_label.style.display = 'none';
        photosets_box.style.display = 'none';
        galleries_box.style.display = 'none';
        groups_box.style.display = 'none';
        tags_box.style.display = 'none';
        help_text.style.display = 'none';
    }
    else if (source_element.value == 'gallery') {
        if (!galleries_box.value) {
            alert('You have no galleries associated with your Flickr account.');
            source_element.value = 'photostream';
            getPhotoSourceType();
        }
        else {
            source_label.style.display = 'block';
            galleries_box.style.display = 'block';
            photosets_box.style.display = 'none';
            groups_box.style.display = 'none';
            tags_box.style.display = 'none';
            help_text.style.display = 'none';
            source_label.innerHTML = 'Select Gallery';
        }
    }
    else if (source_element.value == 'photoset') {
        if (!photosets_box.value) {
            alert('You have no photosets associated with your Flickr account.');
            source_element.value = 'photostream';
            getPhotoSourceType();
        }
        else {
            source_label.style.display = 'block';
            photosets_box.style.display = 'block';
            galleries_box.style.display = 'none';
            groups_box.style.display = 'none';
            tags_box.style.display = 'none';
            help_text.style.display = 'none';
            source_label.innerHTML = "Select Photoset";
        }
    }
    else if (source_element.value == 'group') {
        if (!groups_box.value) {
            alert('You have no groups associated with your Flickr account.');
            source_element.value = 'photostream';
            getPhotoSourceType();
        }
        else {
            source_label.style.display = 'block';
            photosets_box.style.display = 'none';
            galleries_box.style.display = 'none';
            groups_box.style.display = 'block';
            tags_box.style.display = 'none';
            help_text.style.display = 'none';
            source_label.innerHTML = "Select Group";
        }
    }
    else if (source_element.value == 'tags') {
        source_label.style.display = 'block';
        photosets_box.style.display = 'none';
        galleries_box.style.display = 'none';
        groups_box.style.display = 'none';
        tags_box.style.display = 'block';
        help_text.style.display = 'block';
        source_label.innerHTML = "Tags";
    }
}

function verifyEditBlank() {
    var gname = document.getElementById("rfg_edit_gallery_name");
    var submit_button = document.getElementById('rfg_save_changes');
    if (gname.value == "") {
        alert('Gallery Name can not be blank.');
        submit_button.disabled = true;
        return;
    }
    submit_button.disabled = false;
}
function loadGallerySettings() {
    var gallery = document.getElementById('rfg_photo_gallery');
    var gallery_name = document.getElementById('rfg_edit_gallery_name');
    var gallery_descr = document.getElementById('rfg_edit_gallery_descr');
    var photosets_box = document.getElementById('rfg_photosets_box');
    var galleries_box = document.getElementById('rfg_galleries_box');
    var groups_box = document.getElementById('rfg_groups_box');
    var tags_box = document.getElementById('rfg_tags');
    var source_label = document.getElementById('rfg_source_label');
    var per_page = document.getElementById('rfg_per_page');
    var sort_order = document.getElementById('rfg_sort_order');
    var per_page_check = document.getElementById('rfg_per_page_check');
    var photo_size = document.getElementById('rfg_photo_size');
    var captions = document.getElementById('rfg_captions');
    var descr = document.getElementById('rfg_descr');
    var columns = document.getElementById('rfg_columns');
    var cache_ttl = document.getElementById('rfg_cache_ttl');
    var slideshow_option = document.getElementById('rfg_slideshow_option');
    var credit_note = document.getElementById('rfg_credit_note');
    var bg_color = document.getElementById('rfg_bg_color');
    var width = document.getElementById('rfg_width');
    var pagination = document.getElementById('rfg_pagination');
    var gallery_code = document.getElementById('rfg_flickr_gallery_code');
    var rfg_custom_size = document.getElementById("rfg_custom_size");
    var rfg_custom_size_square = document.getElementById("rfg_custom_size_square");
    var rfg_custom_size_block = document.getElementById("rfg_custom_size_block");

    var galleries = genparams.galleries.replace(/&quot;/g, '"');
    var jgalleries = jQuery.parseJSON(galleries);
    active_gallery = jgalleries[gallery.value];

    source_element = document.getElementById('rfg_photo_source_type');
    source_element.value = active_gallery.photo_source;

    gallery_name.value = active_gallery.name;
    gallery_descr.value = active_gallery.gallery_descr;
    if (active_gallery.per_page) {
        per_page_check.checked = false;
        per_page.disabled = false;
    }
    else {
        per_page_check.checked = true;
        per_page.disabled = true;
    }
    per_page.value = active_gallery.per_page || genparams.default_per_page;
    sort_order.value = active_gallery.sort_order || 'default';
    photo_size.value = active_gallery.photo_size || 'default';
    captions.value = active_gallery.captions || 'default';
    descr.value = active_gallery.descr || 'default';
    columns.value = active_gallery.columns || 'default';
    cache_ttl.value = active_gallery.cache_ttl || 'default';
    slideshow_option.value = active_gallery.slideshow_option || 'default';
    bg_color.value = active_gallery.bg_color || 'default';
    width.value = active_gallery.width || 'default';
    pagination.value = active_gallery.pagination || 'default';
    credit_note.value = active_gallery.credit_note || 'default';
    gallery_code.innerHTML = '[rfg_gallery id=\'' + gallery.value + '\']';

    if (photo_size.value == "custom") {
        rfg_custom_size_block.style.display = "";
        rfg_custom_size.value = active_gallery.custom_size;
        if (active_gallery.custom_size_square == 'true')
            rfg_custom_size_square.checked = true;
        else
            rfg_custom_size_square.checked = false;
    }
    else
        rfg_custom_size_block.style.display = "none";

    getPhotoSourceType();

    if (source_element.value == 'photoset') {
        photosets_box.value = active_gallery.photoset_id;
        galleries_box.value = '';
        groups_box.value = '';
        tags_box.value = '';
    }
    else if (source_element.value == 'gallery') {
        galleries_box.value = active_gallery.gallery_id;
        photosets_box.value = '';
        groups_box.value = '';
        tags_box.value = '';
    }
    else if (source_element.value == 'group') {
        groups_box.value = active_gallery.group_id;
        photosets_box.value = '';
        galleries_box.value = '';
        tags_box.value = '';
    }
    else if (source_element.value == 'tags') {
        tags_box.value = active_gallery.tags;
        photosets_box.value = '';
        galleries_box.value = '';
        groups_box.value = '';
    }
}
