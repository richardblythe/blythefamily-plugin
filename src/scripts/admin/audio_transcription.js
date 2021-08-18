jQuery(document).ready(function($){

    var $audio_wrapper = $('.tools_page_aht-transcription .audio-wrapper');
    var top = $audio_wrapper.css('top');
    top = parseInt( top.substr(0, top.indexOf('px')) );

    if ( !( top > 0) ) {
        top = 46;
    }

    const scrollFunc = function() {
        $audio_wrapper.css({
            top: Math.max(top - this.window.scrollY, 0)
        });
    }

    $(window).scroll(function(){
        scrollFunc();
    })

    //initialize
    scrollFunc();

});