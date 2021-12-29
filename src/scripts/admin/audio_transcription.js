jQuery(document).ready(function($){

    const $adminBar = $('#wpadminbar');
    const $adminMenu = $('#adminmenu');
    const $audio_wrapper = $('.tools_page_aht-transcription .audio-wrapper');
    // var top = $audio_wrapper.position().top;


    const scrollFunc = function() {

        const adminBottom = $adminBar.position().top + $adminBar.outerHeight(true);

        $audio_wrapper.css({
            "top": Math.max(adminBottom - this.window.scrollY, adminBottom)
        });

        $audio_wrapper.css({
            "left": $adminMenu.width()
        });
    }

    $(window).scroll(function(){
        scrollFunc();
    })

    $(window).resize(function(){
        scrollFunc();
    })

    //initialize
    scrollFunc();

});