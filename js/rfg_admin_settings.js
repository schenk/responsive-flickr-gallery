mjson = jQuery.noConflict();

mjson(document).ready(function(){
    });

function verifyPerPageBlank() {
    var per_page = document.getElementById("rfg_per_page");
    var submit_button = document.getElementById('rfg_save_changes');
    if (per_page.value == "") {
        alert('Per Page can not be blank.');
        submit_button.disabled = true;
        per_page.focus();
        return;
    }
    submit_button.disabled = false;
}
