<?php
if ( !defined( 'ABSPATH' ) ) exit;
/*
    Plugin Name: IntelliWidget List Shortcode
    Plugin URI: http://www.lilaeamedia.com/iwtemplates/
    Description: IntelliWidget Shortcode for Framework
    Version: 1.0.0
    Author: Lilaea Media
    Author URI: http://www.lilaeamedia.com/
   
    This file and all accompanying files (C) 2014-2016 Lilaea Media LLC except where noted. See license for details.
*/


class IntelliWidgetShortCodeList {
    static $shortcode_id;
    /**
     * Shortcode handler
     */
    static function do_list( $atts, $layout = NULL ) {
        ++self::$shortcode_id;
        global $post;
		
        // prevent recursion
        if ( in_iw_loop() && is_object( $post ) && is_object( iwquery()->post ) && $post->ID == iwquery()->post->ID ):
            //file_put_contents( get_stylesheet_directory() . '/debug.txt', __METHOD__ . "\n" . $post->ID . "\n" . iwquery()->post->ID . "\n", FILE_APPEND );
			return;
        endif;
        // preserve current iwquery
        $save_iwquery = iwquery();

        // profile ( old section ) parameter lets us use page-specific IntelliWidgets in shortcode without all the params
        $settings = array();
        if ( empty( $atts[ 'profile' ] ) ):
            $settings[ 'profile' ] = '';
            $widget_id = self::$shortcode_id;
            $domid = self::$shortcode_id;
        else:
            $settings[ 'profile' ] = $atts[ 'profile' ];
            $widget_id = $atts[ 'profile' ];
            $domid = $atts[ 'profile' ] . '_' . self::$shortcode_id;
        endif;
        $args = array(
            'id'            => self::$shortcode_id,
            'widget_id'     => $widget_id,
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
            'before_widget' => empty( $atts[ 'nav_menu' ] ) ? '<div id="' . $domid . '" class="widget_intelliwidget">' : '',
            'after_widget'  => empty( $atts[ 'nav_menu' ] ) ? '</div>' : '',
        );
        // get settings from IW Profile if passed
        if ( !empty( $settings[ 'profile' ] ) ):
            $settings = apply_filters( 'intelliwidget_extension_settings', $settings, $args );
            //file_put_contents( get_stylesheet_directory() . '/debug.txt', __METHOD__ . "\n" . $domid . "\n", FILE_APPEND );
        endif;
        // override profile settings with passed settings
        foreach ( (array) $atts as $name => $value ):
            // remove custom text in passed shortcode
            if ( 'custom_text' == $name || 'text_position' == $name )
                continue;
            // strip tags in passed title or link text
            if ( 'title' == $name || 'link_text' == $name ):
                $settings[ $name ] = strip_tags( $value );
                continue;
            endif;
            // set content type to nav menu if menu slug is passed
            if ( 'name_menu' == $name ):
                $settings[ 'content' ] = 'nav_menu';
                continue;
            endif;
            // change "category" to "terms" if passed
            if ( 'category' == $name ):
                $settings[ 'terms' ] = $value;
                continue;
            endif;
            // otherwise just override
            $settings[ $name ] = $value;
        endforeach;
        $content = '';

		iwinstance()->defaults( $settings );
        if ( $layout ):
			//echo ' adding iwloop... ' . "\n";
            // auto-generate loop if not in layout string
            if ( FALSE === strpos( $layout, 'iwloop' ) )
                $layout = '[iwloop]' . $layout . '[/iwloop]';
            iwinstance()->set( 'layout', $layout );
        endif;
        // generate widget from arguments
        $content = iwctl()->build_list( $args );
        // restore original iwquery object
        IntelliWidgetMainQuery::$instance = $save_iwquery;
        // return widget content
        return $content;
    }
    
}
    
