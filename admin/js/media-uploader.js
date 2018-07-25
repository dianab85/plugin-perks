(function( $ ) {
    'use strict';

    /* global wp, console */

    var file_frame, image_data, galleryIndex, urlString, clicked;

    $(function() {
        $('.group').on( 'click', '.button-upload', function(e) {
            clicked = $(this);
            e.preventDefault();
            // galleryIndex = $(this).parents('.gallery-item').index();
            // console.log(galleryIndex);
            /**
             * If an instance of file_frame already exists, then we can open it
             * rather than creating a new instance.
             */
            // If the media frame already exists, reopen it.
            if ( file_frame ) {
                file_frame.open();
                return;
            }

            /**
             * If we're this far, then an instance does not exist, so we need to
             * create our own.
             *
             * Here, use the wp.media library to define the settings of the Media
             * Uploader implementation by setting the title and the upload button
             * text. We're also not allowing the user to select more than one image.
             */
            file_frame = wp.media.frames.file_frame = wp.media({
                title: "Insert Media",    // For production, this needs i18n.
                button: {
                    text: "Upload Image"     // For production, this needs i18n.
                },
                multiple: false
            });

            /**
             * Setup an event handler for what to do when an image has been
             * selected.
             */
            file_frame.on('select', function () {

                image_data = file_frame.state().get('selection').first().toJSON();
                urlString = image_data.url;

                //get relative path or URL
                if(urlString.indexOf('.dev/') > 1){
                    urlString = urlString.substring(urlString.lastIndexOf(".dev") + 4);
                } else if(urlString.indexOf('.ca/') > 1){
                    urlString = urlString.substring(urlString.lastIndexOf(".ca") + 3);
                } else if(urlString.indexOf('.com/') > 1){
                    urlString = urlString.substring(urlString.lastIndexOf(".com") + 4);
                }
                console.log(urlString);
                //$('input[data-target="gallery-item-' + galleryIndex + '"]').val(urlString);
                $(clicked).parents('.input-sect').find('input[type="text"]').val(urlString);
                $(clicked).parents('.input-sect').find('.img-preview').empty();
                $(clicked).parents('.input-sect').find('.img-preview').append('<img src="' + urlString + '" />');
            });

            // Now display the actual file_frame
            file_frame.open();

        });

        $('.group').on('click', '.button-clear', function(e){
            //$(this).parents('li.gallery-item').find('input[type="text"]').val('');
            $(this).parents('.input-sect').find('input[type="text"]').val('');
            $(this).parents('.input-sect').find('.img-preview').empty();
        });
    });
})( jQuery );