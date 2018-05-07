//hide sidebar
$(window).on('load resize', function () {
    if ($(window).width() <= 768) {
        $("aside").hide();
        $("#content").attr('style', 'margin-left:0px');
        $("#content2").attr('style', 'margin-left:0px');
        $("#toggle-nav").toggle(
            function() {
                $("aside").show();
                $("aside").addClass("collapsed");
                $("aside").addClass("slideInDown");
                $("aside").attr('style', 'position: relative; width: 100%; height: auto');
            },
            function() {
                $("aside").hide();
                $("#content").attr('style', 'margin-left:0px');
                $("#content2").attr('style', 'margin-left:0px');
            }

        );

    }
    if($(window).width() > 768) {
        $("aside").show();
        $("#content").attr('style', 'margin-left:250px');
        $("#content2").attr('style', 'margin-left:250px');
         $("aside").attr('style', 'position: fixed;     width: 250px; height: 100%; text-align: left;');
    }
    });
    $('.dropdown-menu').click(function(event){
     event.stopPropagation();
    });
    jQuery(document).ready(function($) {
        $('.counter').counterUp({
            delay: 10,
            time: 800
        });
    });

//screen height boxes
$( document ).ready(function() {
var notifHeight = $(window).height() - 280;
    $("#notificationsBox").height(notifHeight);
});



//Open dropdown on current page

var pathurl = window.location.href.split('/').pop();
var IDs = [];
$(".list-unstyled").find("li").each(function () {
    IDs.push(this.id);
})
for (var i = 0; i < IDs.length; i++) {
    if (IDs[i] == pathurl) {
        $('#' + pathurl).addClass('current');
    } else {
        var get1par = 'li#' + pathurl;
        var get2par = $(get1par).parent(1);
        var final = $(get2par).parent();
        $(final).addClass('current');
    }
};



// Fake file upload
document.getElementById('fake-file-button-browse').addEventListener('click', function () {
    document.getElementById('image').click();
});
document.getElementById('image').addEventListener('change', function () {
    document.getElementById('fake-file-input-name').value = this.value;

    document.getElementById('fake-file-button-upload').removeAttribute('disabled');
});




