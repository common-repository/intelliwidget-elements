<?php
if ( !defined( 'ABSPATH' ) ) exit;
/*
    Plugin Name: IntelliWidget Elements Shortcodes
    Plugin URI: http://www.lilaeamedia.com/iwtemplates/
    Description: Core styles, scripts and shortcodes for IntelliWidget Framework
    Version: 1.0.0
    Author: Lilaea Media
    Author URI: http://www.lilaeamedia.com/
   
    This file and all accompanying files (C) 2014-2015 Lilaea Media LLC except where noted. See license for details.
*/


class IntelliWidgetShortCodeCore {
    
    static $shortcode_array = array();
    
    static function cleanup_cells( $content ){
        
        // autop creates havoc with cell shortcodes. Remove any p's and br's directly before or after open or close div tags
        $content = preg_replace( "%(<p>|<br[^>]*>)*\s*(</?div[^>]*?>)\s*(</p>|<br[^>]*>)*%s", " $2 ", $content );
        // Remove any p's before or after open or close a tags, leave brs alone
        $content = preg_replace( "%(<p>)*\s*(</?a[^>]*?>)\s*(</p>)*%s", " $2 ", $content );
        // cleanup weird edge case with textareas
        $content = preg_replace( "%(<p>)?(</?textarea[^>]*?>)(</p>)?%s", "$2", $content );
        // cleanup whitespace after link
        $content = preg_replace( "%\s*?(</a>)\s+([\.\?\!,;:])%s", "$1$2", $content );
        // also cleanup comments
        return preg_replace( "%(<p>|<br[^>]*>)*\s*(<!--.*?-->)\s*(</p>|<br[^>]*>)*%s", "$2", $content );
    }
 
    /**
     * Override core gallery shortcode to use unsorted lists,
     * add responsive behavior, pdf lists and other features.
     * Function requires a value in ul_class attribute
     * otherwise it falls back to core version.
     */
    static function gallery( $empty, $attr ) {

        // skip this filter if not using custom option
        if ( empty( $attr[ 'ul_class' ] ) && empty( $attr[ 'tag' ] ) )
            return '';
        return IntelliWidgetShortCodeGallery::gallery( $attr );
    }
    
    static function init() {
        /*
         * shortcodes
         */
        self::$shortcode_array = array(); 
        $counter = 0;
        // enable the IW shortcode
        if ( !shortcode_exists( 'intelliwidget' ) ):
            self::$shortcode_array[ 'intelliwidget' ] = ++$counter;
            add_shortcode( 'intelliwidget', 'IntelliWidgetShortCodeList::do_list' );
        endif;
        if ( !shortcode_exists( 'iwloop' ) ):
            self::$shortcode_array[ 'iwloop' ] = ++$counter;
            add_shortcode( 'iwloop', 'IntelliWidgetShortCodeLoop::loop' );
        endif;
        if ( ! shortcode_exists( 'table' ) ):
            self::$shortcode_array[ 'table' ] = ++$counter;
            add_shortcode( 'table', 'IntelliWidgetShortCodeTable::shortcode_table' );
        endif;
        if ( ! shortcode_exists( 'slide' ) ):
            add_shortcode( 'slide', 'IntelliWidgetShortCodeMisc::shortcode_post' );
        endif;
        // allow cell shortcodes up to CELLS_MAX_DEPTH deep
        for ( $i = CELLS_MAX_DEPTH; $i > 0; $i-- ):
            $sc = 'cell' . ( $i == 1 ? '' : $i );
            $sh = 'c' . ( $i == 1 ? '' : $i );
            if ( ! shortcode_exists( $sc ) ):
                self::$shortcode_array[ $sc ] = ++$counter;
                add_shortcode( $sc, 'IntelliWidgetShortCodeCell::cell' );
            endif;
            if ( ! shortcode_exists( $sh ) ):
                self::$shortcode_array[ $sh ] = ++$counter;
                add_shortcode( $sh, 'IntelliWidgetShortCodeCell::cell' );
            endif;
        endfor;
        if ( ! shortcode_exists( 'thumbnail' ) ):
            self::$shortcode_array[ 'thumbnail' ] = ++$counter;
            add_shortcode( 'thumbnail', 'IntelliWidgetShortCodeThumbnail::thumbnail' );
        endif;
        if ( ! shortcode_exists( 'datafield' ) ):
            self::$shortcode_array[ 'datafield' ] = ++$counter;
            add_shortcode( 'datafield', 'IntelliWidgetShortCodeDatafield::datafield' );
        endif;
        if ( ! shortcode_exists( 'templatepart' ) ):
            self::$shortcode_array[ 'templatepart' ] = ++$counter;
            add_shortcode( 'templatepart', 'IntelliWidgetShortCodeMisc::templatepart' );
        endif;
        if ( !shortcode_exists( 'if' ) ):
            self::$shortcode_array[ 'if' ] = ++$counter;
            add_shortcode( 'if', 'IntelliWidgetShortCodeIf::shortcode_if' );
        endif;
        if ( ! shortcode_exists( 'date' ) ):
            self::$shortcode_array[ 'date' ] = ++$counter;
            add_shortcode( 'date', 'IntelliWidgetShortCodeMisc::datenow' );
        endif;
        if ( ! shortcode_exists( 'searchbox' ) ):
            self::$shortcode_array[ 'searchbox' ] = ++$counter;
            add_shortcode( 'searchbox', 'IntelliWidgetShortCodeMisc::searchbox' );
        endif;
        if ( ! shortcode_exists( 'flexslider' ) ):
            self::$shortcode_array[ 'flexslider' ] = ++$counter;
            add_shortcode( 'flexslider', 'IntelliWidgetShortCodeSlider::flexslider' );
        endif;
        if ( ! shortcode_exists( 'iwcarousel' ) ):
            self::$shortcode_array[ 'iwcarousel' ] = ++$counter;
            add_shortcode( 'iwcarousel', 'IntelliWidgetShortCodeSlider::iwcarousel' );
        endif;
        if ( ! shortcode_exists( 'tabs' ) ):
            self::$shortcode_array[ 'tabs' ] = ++$counter;
            add_shortcode( 'tabs', 'IntelliWidgetShortCodeScripts::ui_tabs' );
        endif;
        if ( ! shortcode_exists( 'pinterest' ) ):
            self::$shortcode_array[ 'pinterest' ] = ++$counter;
            add_shortcode( 'pinterest', 'IntelliWidgetShortCodeScripts::pinterest' );
        endif;
        if ( ! shortcode_exists( 'thickbox' ) ):
            self::$shortcode_array[ 'thickbox' ] = ++$counter;
            add_shortcode( 'thickbox', 'IntelliWidgetShortCodeScripts::thickbox' );
        endif;
        if ( ! shortcode_exists( 'phone' ) ):
            self::$shortcode_array[ 'phone' ] = ++$counter;
            add_shortcode( 'phone', 'IntelliWidgetShortCodeLink::filter_phone' );
        endif;
        if ( ! shortcode_exists( 'email' ) ):
            self::$shortcode_array[ 'email' ] = ++$counter;
            add_shortcode( 'email', 'IntelliWidgetShortCodeLink::filter_email' );
        endif;
        if ( ! shortcode_exists( 'button' ) ):
            self::$shortcode_array[ 'button' ] = ++$counter;
            add_shortcode( 'button', 'IntelliWidgetShortCodeLink::filter_button' );
        endif;
        if ( ! shortcode_exists( 'term' ) ):
            self::$shortcode_array[ 'term' ] = ++$counter;
            add_shortcode( 'term', 'IntelliWidgetShortCodeDatafield::term' );
        endif;
        if ( ! shortcode_exists( 'author' ) ):
            self::$shortcode_array[ 'author' ] = ++$counter;
            add_shortcode( 'author', 'IntelliWidgetShortCodeDatafield::author' );
        endif;
        if ( ! shortcode_exists( 'bloginfo' ) ):
            self::$shortcode_array[ 'bloginfo' ] = ++$counter;
            add_shortcode( 'bloginfo', 'IntelliWidgetShortCodeMisc::bloginfo' );
        endif;

        // enable shortcodes in cf7 - low priority to avoid collisions with cf7 shortcodes
        add_filter( 'wpcf7_form_elements',          'do_shortcode', 99 );
                    
        //add_filter( 'post_gallery',                 'IntelliWidgetShortCodeCore::gallery', 10, 2 );
        //add_filter( 'query_vars',                   'IntelliWidgetShortCodeGallery::gallery_pagination_vars' );
        
        // insert html shortcodes before text filters in the_content hook
        add_filter( 'the_content',                  'IntelliWidgetShortCodeCore::run_priority_shortcodes', 5 );
        //add_image_size( 'hero', 650, 650 ); 
        add_filter( 'the_content',                  'IntelliWidgetShortCodeCore::cleanup_cells', 50 );
        add_filter( 'widget_text',                  'IntelliWidgetShortCodeCore::cleanup_cells', 50 );
    }

    /**
     * executes html shortcodes prior to text filters in the_content hook to prevent unwanted <p> tags.
     */
    static function run_priority_shortcodes( $content ){
        //$autop = has_filter( 'the_content', 'wpautop' );
        //remove_filter( 'the_content', 'wpautop' );
        // localize scope of shortcodes array
        global $shortcode_tags;
        // backup shortcodes array
        $shortcode_backup = $shortcode_tags;
        // create new array consisting of iwtemplates html shortcodes
        // reverse sort because shortcode uses a regex pattern and regex is tested right to left
        arsort( self::$shortcode_array );
        $shortcode_tags = array();
        
        foreach( self::$shortcode_array as $key => $order )
            if ( isset( $shortcode_backup[ $key ] ) )
                $shortcode_tags[ $key ] = $shortcode_backup[ $key ]; 
        // run just these shortcodes
        $content = do_shortcode( $content );
        // restore backup 
        $shortcode_tags = $shortcode_backup;
        // return modified content
        //if ( $autop )
        //    add_filter( 'the_content', 'wpautop' );
        return $content;
    }

    static function wrap_content( $content, $a, $post = NULL ){
        if ( !empty( $a[ 'link' ] ) ):
            if ( !$post )
                $post = get_iw_post();
        
            $classes = array();
            if ( !empty( $a[ 'linkc' ] ) )
                $classes[] = $a[ 'linkc' ];

            if ( !empty( $post->link_classes ) )
                $classes[] = $post->link_classes;

            $class = ' class="' . implode( ' ', $classes ) . '"';
            
            $target = empty( $post->link_target ) ? '' : ' target="' . $post->link_target . '"';
        
            $url = $a[ 'link' ];
        
            if ( '1' == $url )
                $url = empty( $post->external_url ) ? get_the_permalink( $post->ID ) : $post->external_url;
            $content = '<a href="' . $url . '"' . $class . $target . '>' . $content . '</a>';
        
        endif;
        if ( !empty( $a[ 'tag' ] ) ):
            $el = sanitize_html_class( $a[ 'tag' ] );
            $content = '<' . $el 
                . ( !empty( $a[ 'tagc' ] ) ? 
                    ' class="' . $a[ 'tagc' ] . '"' :
                    '' ) . '>'
                . $content . '</' . $el . '>';
        endif;
        return $content;
    }
}
