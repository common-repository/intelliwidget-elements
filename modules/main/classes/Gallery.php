<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'IntelliWidgetMainGallery' ) ):

    class IntelliWidgetMainGallery {
    
        static function render( $args = array(), $post_id = NULL ) {
            // skip to after widget content if this is custom text only
            if ( 'only' == iwinstance()->get( 'text_position' ) ) return;
            // do not render if no media selected
            if ( !iwinstance()->get( 'page' ) ) 
                iwinstance()->set( 'page', array( -1 ) );
            //echo '<h3>Pages ' . print_r( iwinstance()->get( 'page' ), TRUE ) . '</h3>';
            // fetch attachments
            iwquery()->get_posts();
            
            // thickbox support
            if ( 'tb' == iwinstance()->get( 'link' ) ):
                wp_enqueue_script( 'thickbox' );
                add_thickbox();
            endif;

            // static counter
            static $gallery_counter = 0;
            $gallery_counter++;
                        
            // variable dom element options
            switch ( iwinstance()->get( 'tag' ) ):
                case 'list':
                    $listtag = 'ul';
                    $itemtag = 'li';
                    $icontag = 'div';
                    $captiontag = 'p';
                    break;
                case 'html5':
                    $listtag = 'div';
                    $itemtag = 'fig';
                    $icontag = 'div';
                    $captiontag = 'figcaption';
                    break;
                default:
                    $listtag = 'div';
                    $itemtag = 'dl';
                    $icontag = 'dt';
                    $captiontag = 'dd';
                    break;
            endswitch;
            
            // render gallery container
            $selector = 'iw-gallery-' . $gallery_counter;
            $gallery_class = 'iw-gallery iw-galleryid-' . $args[ 'id' ] 
                . ' iw-gallery-size-' . iwinstance()->get( 'image_size' ) . ' clearfix ' . iwinstance()->get( 'gclass' );
            $output = '';
        
            // iterate attachments 
            $i = 0;
            if ( iwquery()->have_posts() ): 
				$output .= '<' . $listtag . ' id="' . $selector . '" class="' . $gallery_class . '">';
				while ( iwquery()->have_posts() ): iwquery()->the_post();
                
                // set Templates responsive behavior if multi column
                
                $id             = iwquery()->post->ID;
                $type           = iwquery()->post->post_mime_type;
                $size           = iwinstance()->get( 'image_size' );
                $cell_class     = iwctl()->post_classes( ++$i, array( 'iw-gallery-item', 'clearfix', iwinstance()->get( 'iclass' ) ) );
                
            
                $excerpt = empty( iwquery()->post->post_excerpt ) ?
                        iwquery()->post->post_content : 
                        iwquery()->post->post_excerpt;
                $image_caption = apply_filters( 'intelliwidget_trim_excerpt', $excerpt, 256, 'strong,em,br' );

            
                /*
                 * render gallery item based on type of attachment and gallery settings
                 */
                 
                // document
                //if ( strstr( $type, 'application' ) ):
                if ( preg_match( "/(application|audio|video)/", $type ) ):
                    $parts = pathinfo( iwquery()->post->guid );
                    $linkclass = substr( $parts[ 'extension' ], 0, 3 ) . 'link';
                    $output .= '<' . $itemtag . ' class="' . $cell_class . '">' 
                        . str_replace( '<a', '<a class="' . $linkclass . '"', wp_get_attachment_link( $id, $size, FALSE, FALSE ) ) 
                        . '</' . $itemtag . '>';
                // background
                elseif ( strstr( $cell_class, 'bgcover' ) ):
                    $image = wp_get_attachment_image_src( $id, 'full' );
                    $output .= '<' . $itemtag . ' class="' . $cell_class . '" style="background-image:url( ' . $image[ 0 ] . ' )"></' . $itemtag . '>' . PHP_EOL;
                    
                // standard image
                else:
                    // basic link to file
                    if ( ! empty( $attr[ 'link' ] ) && 'file' == iwinstance()->get( 'link' ) ):
                        $image_output = wp_get_attachment_link( $id, $size, FALSE, FALSE );
                    // link to attachment page
                    elseif ( iwinstance()->get( 'link' ) && 'post' === iwinstance()->get( 'link' ) ):
                        $image_output = wp_get_attachment_link( $id, $size, TRUE, FALSE );
                    // link to modal
                    elseif ( iwinstance()->get( 'link' ) && ( 'tb' == iwinstance()->get( 'link' ) || 'pp' == iwinstance()->get( 'link' ) ) ):
                        $image_title    = esc_attr( get_the_title( $id ) );
                        $image_link     = wp_get_attachment_url( $id );
                        $image          = wp_get_attachment_image( $id, $size, FALSE ); 
                        $image_class    = 'pp' === iwinstance()->get( 'link' ) ? 'woocommerce-main-image zoom' : 'thickbox';
                        $image_output   = sprintf( '<a href="%s" itemprop="image" class="%s" title="%s" ' 
                            . ( 'pp' == iwinstance()->get( 'link' ) ? 'data-rel="prettyPhoto[' . $selector . ']"' : '' ) 
                            . '>%s</a>', $image_link, $image_class, $image_caption, $image );
                    // no link
                    else:
                        $image_output = wp_get_attachment_image( $id, $size, FALSE );
                    endif;
                    // render gallery item container
                    $output .= '<' . $itemtag . ' class="' . $cell_class . '">';
                    // render inner gallery item container ( allows for borders, etc )
                    if ( $icontag ):
                        $image_meta  = wp_get_attachment_metadata( $id );
                
                        $orientation = '';
                        if ( isset( $image_meta[ 'height' ], $image_meta[ 'width' ] ) ) {
                            $orientation = ( $image_meta[ 'height' ] > $image_meta[ 'width' ] ) ? 'portrait' : 'landscape';
                        }
                        $output .= '<' . $icontag . ' class="iw-gallery-icon ' . $orientation . '">';
    
                    endif;
                    // render attachment
                    $output .= $image_output;
                    
                    // close inner gallery item container
                    if ( $icontag )
                        $output .= '</' . $icontag . '>'; 
                    // render caption, if present               
                    if ( iwinstance()->get( 'captions' ) && $image_caption )
                        $output .= '<' . $captiontag . ' class="wp-caption-text iw-gallery-caption" id="' . $selector . '-' . $id . '">' 
                            . wptexturize( $image_caption ) . '</' . $captiontag . '>';
                    // close gallery item container
                    $output .= '</' . $itemtag . '>';
                endif;
            endwhile; 
        
            // close gallery container
            $output .= '</' . $listtag . '>';
            endif;
            
            // to screen
            echo $output;
    
        }
    
    }
endif;