jQuery(document).ready( function($) {

    var $field = $('.acf-field-file[data-name="mp3"]');
    if ( $field.length ) {
        $('input[name="acf['+ $field.data('key') + '"]').on('change', function(e) {
            var mp3_url = $field.find('a[data-name="filename"]').attr('href')
            $('input[name="Powerpress[podcast][url]"]').val( mp3_url );
        });
    }



});