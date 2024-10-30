(function($) {
    'use strict';
    
	// we create a copy of the WP inline edit post function
	var $wp_inline_edit = window.inlineEditPost.edit;

	// and then we overwrite the function with our own code
	window.inlineEditPost.edit = function( id ) {

		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		$wp_inline_edit.apply( this, arguments );

		// now we take care of our business

		// get the post ID
		var $post_id = 0;
		if ( typeof id === 'object' ) {
			$post_id = parseInt( this.getId( id ) );
		}

		if ( $post_id > 0 ) {
            $.each( window.intelliwidgetRelations, function( type, column ){
                console.log( type + ' ' + column );
                // define the edit row
                var $edit_row = $( '#edit-' + $post_id ),
                    $post_row = $( '#post-' + $post_id ),
                    relation,
                    relation_id;

                // get the data
                relation = $( '.column-' + column, $post_row ).html();
                console.log( relation );
                relation_id = $( relation ).data( 'id' );
                console.log( relation_id );
                // populate the data
                $( ':input[name="' + column + '"]', $edit_row ).val( relation_id );
            });
		}
	};

})(jQuery);