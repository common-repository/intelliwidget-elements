<?php
if ( !defined( 'ABSPATH' ) ) exit;
/*
    Plugin Name: IntelliWidget Pro Link Shortcodes
    Plugin URI: http://www.lilaeamedia.com/iwtemplates/
    Description: Core styles, scripts and shortcodes for IntelliWidget Framework
    Version: 1.0.0
    Author: Lilaea Media
    Author URI: http://www.lilaeamedia.com/
   
    This file and all accompanying files (C) 2014-2016 Lilaea Media LLC except where noted. See license for details.
*/


class IntelliWidgetShortCodeLink {
    
    /* 
     * converts phone number to tel: link
     */
    static function filter_phone( $atts, $content = NULL ) {
        $content = do_shortcode( apply_filters( 'intelliwidget_shortcode_phone', $content ) );
        $a = shortcode_atts( array( 'phone' => $content, 'class' => 'contact-link' ), $atts );
        
        return '<a href="tel:' . preg_replace( "/\D/", '', $a[ 'phone' ] ) . '"' . ( empty( $a[ 'class' ] ) ? '' : ' class="' . $a[ 'class' ] . '"' ) . '>' . $content . '</a>';
    }
    
    /* 
     * obfuscates email address
     */
    static function filter_email( $atts, $content = NULL ) {
        $content = do_shortcode( apply_filters( 'intelliwidget_shortcode_email', $content ) );
        $a = shortcode_atts( array( 'to' => $content, 'class' => '' ), $atts );
        return '<a href="' . self::email_to_entities( 'mailto:' . $a[ 'to' ] ) . '"' . ( empty( $a[ 'class' ] ) ? '' : ' class="' . $a[ 'class' ] . '"' ) . '>' . ( $a[ 'to' ] == $content ? self::email_to_entities( $content ) : $content ) . '</a>';
    }
    
    /* 
     * callback for obfuscation
     */
    static function email_to_entities( $email ){
        return preg_replace_callback( "/([aeiou\W])/i", 'self::convert_email_chars', $email );
    }
    
    /* 
     * callback for preg_replace
     */
    static function convert_email_chars( $arr ) {
        return '&#' . ord( $arr[ 1 ] ) . ';';
    }
    
    static function filter_button( $atts, $content = NULL ) {
        $post = get_iw_post();
        $a = shortcode_atts( array( 'link' => 'permalink', 'class' => '', 'name' => '' ), $atts );
        if ( 'permalink' == $a[ 'link' ] && is_object( $post ) ):
            $url = get_permalink( $post->ID );
        else:
            $url = $a[ 'link' ];
        endif;
		if ( $a[ 'name' ] && ( $text = get_post_meta( $post->ID, $a[ 'name' ], TRUE ) ) )
			$content = $text;
        return '<a href="' . $url . '"' . ( empty( $a[ 'class' ] ) ? '' : ' class="' . $a[ 'class' ] . '"' ) . '>' . do_shortcode( $content ) . '</a>';
    }
    

    
}
    
