<?php
if ( !defined( 'ABSPATH' ) ) exit;
/*
    Plugin Name: IntelliWidget Pro Miscellaneous Shortcodes
    Plugin URI: http://www.lilaeamedia.com/iwtemplates/
    Description: Core styles, scripts and shortcodes for IntelliWidget Framework
    Version: 1.0.0
    Author: Lilaea Media
    Author URI: http://www.lilaeamedia.com/
   
    This file and all accompanying files (C) 2014-2016 Lilaea Media LLC except where noted. See license for details.
*/


class IntelliWidgetShortCodeMisc {   
    
    /*
     * Returns value from bloginfo array
     * @param $atts shortcode attributes: 'show' - value to show
     * @return string
     */
    static function bloginfo( $atts ) {
        $a = shortcode_atts( array( 'name' => 'url' ), $atts );
        return get_bloginfo( $a[ 'name' ] );
    }

    /*
     * Returns date string based on format
     * @param $atts shortcode attributes: 'format' - PHP date format string
     * @return string
     */
    static function datenow( $atts ) {
        $a = shortcode_atts( array( 
            'format' => 'M j, Y',
            'source' => 'now',
            'field'  => '',
            'expire' => '',
            'divider' => '-',
        ), $atts );
        $post = get_iw_post();
        switch( $a[ 'source' ] ):
        case 'publish':
            $time_adj = get_the_date( 'U', $post->ID );
            break;
        case 'meta':
            $time_adj = strtotime( 'U', get_post_meta( $post->ID, $a[ 'field' ], TRUE ) );
            break;
        case 'now':
        default:
            $time_adj = current_time( 'timestamp' );
        endswitch;
        $date = gmdate( $a[ 'format' ], $time_adj );
        if ( $a[ 'expire' ] ):
            $expire = strtotime( 'U', get_post_meta( $post->ID, $a[ 'expire' ], TRUE ) );
            $date .= ' ' . $a[ 'divider' ] . ' ' . $expire;
        endif;
        return $date;
    }

    /*
     * retreives and executes specified template part inside output buffer
     * @param $atts shortcode attributes: 'name' - filename of template part
     * @return string
     */
    static function templatepart( $atts ) {
        $a = shortcode_atts( array( 'name' => '' ), $atts );
        ob_start();
        get_template_part( $a[ 'name' ] );
        return ob_get_clean();
    }
    
    static function searchbox( $atts ){
        ob_start();
        get_search_form();
        return ob_get_clean();
    }
    
    static function shortcode_post( $atts ){
        $a = shortcode_atts( array( 'id' => '', 'slug' => '', 'type' => '', $filter => '1' ), $atts );
        if ( $a[ 'id'] ):
            $post = get_post( $a[ 'id' ] );
        elseif ( $a[ 'slug' ] ):
            $post = get_page_by_path( $a[ 'slug' ], 'OBJECT', $a[ 'type' ] );
        endif;
        $blocks = parse_blocks( $post->post_content );
        $content = '';
        foreach ( $blocks as $block )
            $content .= render_block( $block );
        return apply_filters( $a[ 'filter' ] ? 'the_content' : 'widget_text', $content );
    }
    
}
    
