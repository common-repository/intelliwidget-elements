<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) 
    exit;

/*
Plugin Name: IntelliWidget Elements
Plugin URI: http://www.intelliwidget.com
Description: Create content blocks and menus quickly using shortcodes instead of block editor. This plugin is one part of a larger framework of WordPress features. 
Version: 2.2.7
Author: Lilaea Media
Author URI: http://www.lilaeamedia.com/
Text Domain: intelliwidget
Domain Path: /lang

This file and all accompanying files (C) 2014-2019 Lilaea Media LLC except where noted. See license for details.
*/
    
/**
 * autoloader
 */
function intelliwidget_autoload( $class ) {
    if ( preg_match( "/^IntelliWidget(Main|Login|ShortCode|Templates|Types|CondSet|Search|Calendar|SideLoader)(\w+)$/", $class, $matches ) ): 
        // load from inside plugin
        if ( ( $path = dirname( __FILE__ ) . '/modules/' . strtolower( $matches[ 1 ] ) . '/classes/' . $matches[ 2 ] . '.php' )
            && file_exists( $path ) )
            include_once( $path );
        // load from additional plugins 
        elseif ( ( $path = dirname( dirname( __FILE__ ) ) . '/intelliwidget-' . strtolower( $matches[ 1 ] ) . '/classes/' . $matches[ 2 ] . '.php' )
            && file_exists( $path ) )
            include_once( $path );
    endif;
}
// backward compatibility with original IWE
if ( get_option( 'iwel_settings' ) ):
    include_once( IWELEMENTS_DIR . '/iwe-legacy.php' );
else:
    // define constants
    define( 'IWELEMENTS_VERSION',           '2.2.6' );
    define( 'IWELEMENTS_MIN_WP_VERSION',    '5.3' );
    define( 'IWELEMENTS_DIR',               dirname( __FILE__ ) );
    define( 'IWELEMENTS_URL',               plugin_dir_url( __FILE__ ) );
    define( 'IWELEMENTS_OPTIONS',           'iwelements_options' );
    define( 'IWELEMENTS_CONDSET',           'iwelements_condset' );

    define( 'IWELEMENTS_MAX_MENU_POSTS',    500 );
    define( 'IWELEMENTS_IMPORT_FILE',       'iwf_import_file.zip' );

    defined( 'LILAEAMEDIA_URL' ) 
        or define( 'LILAEAMEDIA_URL', 'https://www.lilaeamedia.com' );
    defined( 'CELLS_MAX_DEPTH') 
        or define( 'CELLS_MAX_DEPTH', 6 );

    // register autoloader
    spl_autoload_register( 'intelliwidget_autoload' );// initialize IntelliWidget

    // initialize IntelliWidget
    add_action( 'plugins_loaded', 'IntelliWidgetMainCore::init' );

    // global helper functions
    if ( !function_exists( 'get_iw_post' ) ):
    function get_iw_post(){
        if ( in_iw_loop() ):
            return iwquery()->post;
        endif;
        global $post;
        return $post;
    }
    endif;
    if ( !function_exists( 'in_iw_loop' ) ):
    function in_iw_loop(){
        return iwquery()->in_the_loop;
    }
    endif;
    if ( !function_exists( 'iwquery' ) ):
    function iwquery(){
        if ( !isset( IntelliWidgetMainQuery::$instance ) )
            new IntelliWidgetMainQuery();
        return IntelliWidgetMainQuery::$instance;
    }
    endif;
    if ( !function_exists( 'iwinstance' ) ):
    function iwinstance(){
        if ( !isset( IntelliWidgetMainInstance::$instance ) )
            new IntelliWidgetMainInstance();
        return IntelliWidgetMainInstance::$instance;
    }
    endif;
    if ( !function_exists( 'iwctl' ) ):
    function iwctl(){
        return IntelliWidgetMainCore::$instance;
    }
    endif;

    // prevent legacy functions from breaking completely
    include_once( IWELEMENTS_DIR . '/deprecated.php' );
endif;
