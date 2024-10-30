<?php
if ( !defined( 'ABSPATH' ) ) exit;
/*
    Plugin Name: IntelliWidget Pro Thumbnail
    Plugin URI: http://www.lilaeamedia.com/iwtemplates/
    Description: Core styles, scripts and shortcodes for IntelliWidget Framework
    Version: 1.0.0
    Author: Lilaea Media
    Author URI: http://www.lilaeamedia.com/
   
    This file and all accompanying files (C) 2014-2016 Lilaea Media LLC except where noted. See license for details.
*/


class IntelliWidgetShortCodeThumbnail {   
    
    static function thumbnail( $atts, $content = '' ) {
        if ( $content )
            $content = do_shortcode( $content );
        $a = shortcode_atts( array( 
            'size'  => 'full', 
            'bg'    => '', 
            'src'   => 'thumbnail', 
            'fb'    => '', 
            'class' => '',
            'tag'   => '',
            'tagc'  => '',
            'link'  => '',
            'linkc' => '',
        ), $atts );
        if ( $a[ 'class' ] ):
            $classes = array();
            foreach( explode( ' ', $a[ 'class' ] ) as $c )
                $classes[] = sanitize_html_class( $a[ 'class' ] );
            $a[ 'class' ] = implode( ' ', $classes );
        endif;
		$html = '';
        if ( $a[ 'bg' ] ):
            if ( !in_array( $a[ 'src' ], array( 'featured', 'thumbnail' ) ) ):
                // use related media
                $image = apply_filters( 'intelliwidget_post_thumbnail_src_' . $a[ 'src' ], '' );
        
        
            elseif ( in_iw_loop() ):
        
                if ( ( $thumbnail_id = get_post_thumbnail_id( iwquery()->post->ID ) )
                    && ( $thumb = wp_get_attachment_image_src( $thumbnail_id, $a[ 'size' ] ) ) ):
                    $image = current( $thumb );
                endif;
        
            else:
                $image = wp_get_attachment_image_src( NULL, $a[ 'size' ] );
            endif;
        
            if ( empty( $image )
                && $a[ 'fb' ]
                && ( $image = wp_get_attachment_image_src( intval( $a[ 'fb' ] ), $a[ 'size' ] ) ) ):
                // use fallback
            elseif ( empty( $image ) ):
                return $content;
            endif;
            if ( strstr( $a[ 'class' ], 'parallax') ):
                $html = '<div class="' . $a[ 'class' ] . '"><div class="bgcover" style="background-image:url( ' . $image . ' )">' . $content . '</div></div>' . PHP_EOL;
            else:
                $html = '<div class="' . ( $a[ 'class' ] ? $a[ 'class' ] . ' ' : '' ) . 'bgcover" style="background-image:url( ' . $image . ' )">' . $content . '</div>' . PHP_EOL;
            endif;
        else:
            // check for related media from types
            if ( !in_array( $a[ 'src' ], array( 'featured', 'thumbnail' ) ) ):
                // use related media
                $html = apply_filters( 'iwtemplates_post_thumbnail_' . $a[ 'src' ], '', $a[ 'size' ], $class );
        
            elseif ( in_iw_loop() ):
        
        
                    // use object thumbnail id if present because it may have been modified by a filter
                if ( $thumbnail_id = get_post_thumbnail_id( iwquery()->post->ID ) ):
                    $html = wp_get_attachment_image( 
                        $thumbnail_id, 
                        $a[ 'size' ], 
                        FALSE, 
                        array( 'class' => $a[ 'class' ] ) 
                    );
                    // otherwise retrieve using post id
                else:
                    $html = get_the_post_thumbnail(
                        iwquery()->post->ID, 
                        $a[ 'size' ], 
                        array(
                            'class' => $a[ 'class' ],
                        )
                    );
                endif;
        
            elseif ( is_singular() && ( $html = get_the_post_thumbnail( NULL, $a[ 'size' ] ) ) ):
                    // use post thumbnail
            elseif ( 
                    $a[ 'fb' ] 
                        && ( $html = wp_get_attachment_image( 
                            intval( $a[ 'fb' ] ), 
                            $a[ 'size' ], 
                            '', 
                            array( 'class' => $a[ 'class' ] )
                        ) ) 
                    ):
                    // use fallback thumbnail
            endif;
        endif;
        return IntelliWidgetShortCodeCore::wrap_content( $html, $a );
    }
}
    
