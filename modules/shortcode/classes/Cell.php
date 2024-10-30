<?php
if ( !defined( 'ABSPATH' ) ) exit;
/*
    Plugin Name: IntelliWidget Pro Cell
    Plugin URI: http://www.lilaeamedia.com/iwtemplates/
    Description: Core styles, scripts and shortcodes for IntelliWidget Framework
    Version: 1.0.0
    Author: Lilaea Media
    Author URI: http://www.lilaeamedia.com/
   
    This file and all accompanying files (C) 2014-2016 Lilaea Media LLC except where noted. See license for details.
*/

class IntelliWidgetShortCodeCell {    
    static function cell( $atts, $content = NULL ) {
        $class  = array();
        //$cell   = 'cell ';
        $style  = '';
        $id     = NULL;
        $tag    = 'div';
        $href   = NULL;
        if ( $atts ):
            foreach ( $atts as $name => $val ):
                if ( 'itemclass' === $val ):
                    $class[] = self::menu_class();
                    continue;
                elseif ( 'menuclass' === $val ):
                    if ( $mc = iwinstance()->get( 'nav_menu_classes' ) ):
                        $class[] = $mc;
                    endif;
                    continue;
                // do specialized class attributes first
                elseif ( $classname = IntelliWidgetMainStrings::get_style( $val ) ):
                    $class[] = $classname;
                elseif ( $val && ( $classname = IntelliWidgetMainStrings::get_style( $name ) ) ):
                    if ( 'id' == $name ): // s and sel are legacy attr
                        $id = $val; 
                    elseif ( 'i' == $name ): 
                        if ( preg_match( "/^[\-\w]+$/", $val ) // this thumnbnail handle
                            // assume thumbnail shortcode exists because this shortcode exists!
                            && ( $src = do_shortcode( '[thumbnail src="' . $val . '"]') )
                                // extract_src_from_img() function is in IW's template-tags.php
                                && preg_match( "/src=[\"'](.+?)[\"']/", $src, $match ) ):
                            $val = $match[ 1 ];
                        endif;
                        if ( $val ):
                            $style .= 'background-image:url(' . $val . ');';
                        endif;
                    elseif ( 't' == $name || 'tag' == $name ):
                        if ( in_array( $val, array( 'article', 'aside', 'footer', 'header', 'main', 'nav', 'p', 'section', 'button', 'a', 'span', 'ul', 'li', 'ol', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6') ) ):
                            $tag = $val;
                        endif;
                    elseif ( 'link' == $name ):
                        $tag = 'a';
                        if ( '1' == $val ):
                            $post = get_iw_post();
                            $href = get_the_permalink( $post->ID );
                        else:
                            $href = $val;
                        endif;
                    elseif ( 's' == $name || 'st' == $name ):
                        $style .= str_replace( '"', "'", $val ) . ';';
                    endif;
                else:
                    $class[] = sanitize_html_class( $val );
                endif;
            endforeach;
        
        endif;
        if ( !empty( $style ) )
            $style = ' style="' . preg_replace( "/;+/", ';', $style ) . '"';
        if ( $content ):
            $content = do_shortcode( $content );
            // list shorthand - replaces double backslash (or pipe) with li's if present
            if ( 'ul' == $tag || 'ol' == $tag ):
                $items = preg_replace( "{\|\||\\\\\\\\}", "</li>\n<li>", $content, -1, $itemcount );
                if ( $itemcount )
                    $content = '<li>' . $items  . '</li>';
            endif;
        endif;
        //if ( $autop ) add_filter( 'the_content', 'wpautop' );
        return '<' . $tag . ( $href ? ' href="' . esc_url( $href ) . '" ' : '' )  . ( $id ? ' id="' . $id . '" ' : ' ' ) . 'class="' . /*$cell .*/ implode( ' ', $class ) . '"' . $style . '>' . $content . '</' . $tag . '>';
    }    
    

    /*
     * Mimics menu item class behavior for manually generated menus by returning current_page_item or current_page_ancestor
     * @param $atts shortcode attributes: 'path' - permalink to menu item target
     * @return string
     */
    static function menu_class() {

        $test_post = get_iw_post();
        global $post;
        if ( !is_object( $post ) || !is_object( $test_post ) || empty( $post->ID ) || empty( $test_post->ID ) ) return;
        $classes = array( 'menu-item' );
        if ( $post->ID == $test_post->ID ) 
            $classes[] = 'current_page_item';
        if ( in_array( $test_post->ID, get_post_ancestors( $post->ID ) ) ) 
            $classes[] = 'current_page_ancestor';
        return implode( ' ', $classes );
    }



}
    
