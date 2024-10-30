(function($){
    'use strict';    
    function getSelectionText() {
        var text = "";
        if (window.getSelection) {
            text = window.getSelection().toString();
        } else if (document.selection && document.selection.type !== "Control") {
            text = document.selection.createRange().text;
        }
        return text;
    }
    getSelectionText($);
})(jQuery);


var frame,
selection = loadImages(images);

$('#stag_images_upload').on('click', function(e) {
    e.preventDefault();

var options = {
    title: '<?php _e("Create Featured Gallery", "stag"); ?>',
    state: 'gallery-edit',
    frame: 'post',
    selection: selection
};

frame = wp.media(options).open();

frame.menu.get('view').unset('cancel');
frame.menu.get('view').unset('separateCancel');
frame.menu.get('view').get('gallery-edit').el.innerHTML = '<?php _e("Edit Featured Gallery", "stag"); ?>';
frame.content.get('view').sidebar.unset('gallery'); // Hide Gallery Settings in sidebar

// when editing a gallery
overrideGalleryInsert();
frame.on( 'toolbar:render:gallery-edit', function() {
    overrideGalleryInsert();
});

frame.on( 'content:render:browse', function( browser ) {
    if ( !browser ) return;
    // Hide Gallery Settings in sidebar
    browser.sidebar.on('ready', function(){
        browser.sidebar.unset('gallery');
    });

    // Hide filter/search as they don't work
    browser.toolbar.on('ready', function(){
        if(browser.toolbar.controller._state == 'gallery-library'){
            browser.toolbar.$el.hide();
        }
    });
});

// All images removed
frame.state().get('library').on( 'remove', function() {
    var models = frame.state().get('library');
    if(models.length == 0){
        selection = false;
        $.post(ajaxurl, { ids: '', action: 'stag_save_images', post_id: stag_ajax.post_id, nonce: stag_ajax.nonce });
    }
});

function overrideGalleryInsert(){
    frame.toolbar.get('view').set({
        insert: {
            style: 'primary',
            text: '<?php _e("Save Featured Gallery", "stag"); ?>',
            click: function(){
                var models = frame.state().get('library'),
                    ids = '';

                models.each( function( attachment ) {
                    ids += attachment.id + ','
                });

                this.el.innerHTML = '<?php _e("Saving...", "stag"); ?>';

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: { 
                        ids: ids, 
                        action: 'stag_save_images', 
                        post_id: stag_ajax.post_id, 
                        nonce: stag_ajax.nonce 
                    },
                    success: function(){
                        selection = loadImages(ids);
                        $('#_stag_image_ids').val( ids );
                        frame.close();
                    },
                    dataType: 'html'
                }).done( function( data ) {
                    $('.stag-gallery-thumbs').html( data );
                    console.log(data);
                });
            }
        }
    });
}

function loadImages(images){
    if (images){
        var shortcode = new wp.shortcode({
            tag:      'gallery',
            attrs:    { ids: images },
            type:     'single'
        });

        var attachments = wp.media.gallery.attachments( shortcode );

        var selection = new wp.media.model.Selection( attachments.models, {
            props:    attachments.props.toJSON(),
            multiple: true
        });

        selection.gallery = attachments.gallery;

        selection.more().done( function() {
            // Break ties with the query.
            selection.props.set({ query: false });
            selection.unmirror();
            selection.props.unset('orderby');
        });

        return selection;
    }
    return false;
}

});

/* PHP

function stag_save_images(){
    if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
        return;
    }

    if ( !isset($_POST['ids']) || !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], 'stag-ajax' ) ){
        return;
    }

    if ( !current_user_can( 'edit_posts' ) ) return;

    $ids = strip_tags(rtrim($_POST['ids'], ','));
    update_post_meta($_POST['post_id'], '_stag_image_ids', $ids);

    $thumbs = explode(',', $ids);
    $thumbs_output = '';
    foreach( $thumbs as $thumb ) {
        $thumbs_output .= '<li>' . wp_get_attachment_image( $thumb, array(75,75) ) . '</li>';
    }

    echo $thumbs_output;

    die();
}
add_action( 'wp_ajax_stag_save_images', 'stag_save_images' );

function stag_metabox_scripts(){
    global $post;
    if( isset($post) ) {
        wp_localize_script( 'jquery', 'stag_ajax', array(
            'post_id' => $post->ID,
            'nonce' => wp_create_nonce( 'stag-ajax' )
        ) );
    }
}

add_action( 'admin_enqueue_scripts', 'stag_metabox_scripts' );

*/