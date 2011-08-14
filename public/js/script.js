function mySubmit(id, val) {
    setPageInactive();
    $('#'+id).val(val);
    $('#myForm').submit();
}
function resetOffset() {
    $('#offset').val(0);
}
function setOpacity(domElement, value) {
    $(domElement).css({ opacity: value });
}

function setPageInactive() {
    setOpacity("body", 0.3);
    $('#page-loader').show();
}
function setPageActive() {
    setOpacity("body", 1);
    $('#page-loader').hide();
}
$(document).ready(function () {
    $(window).bind('beforeunload', function() {
        setPageInactive();
    });
    setPageActive();
});

/** Mouse over functions for tabs, which don't use jquery.ui.tabs (static page requests without AJAX) */
function tabOver(selector)
{
    $(selector).addClass("ui-state-active");
}
function tabOut(selector)
{
    $(selector).removeClass("ui-state-active");
}

function increase(id) {
    var value = $('#' + id).html();
    value++;
    $('#' + id).html(value);
}

function decrease(id) {
    var value = $('#' + id).html();
    value--;
    $('#' + id).html(value);
}

function scrollToId(id){
    $('html,body').animate({scrollTop: $("#"+id).offset().top -40}, 100);
}



