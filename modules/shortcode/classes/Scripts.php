<?php
if ( !defined( 'ABSPATH' ) ) exit;
/*
    Plugin Name: IntelliWidget Pro Script Shortcodes
    Plugin URI: http://www.lilaeamedia.com/iwtemplates/
    Description: Core styles, scripts and shortcodes for IntelliWidget Framework
    Version: 1.0.0
    Author: Lilaea Media
    Author URI: http://www.lilaeamedia.com/
   
    This file and all accompanying files (C) 2014-2016 Lilaea Media LLC except where noted. See license for details.
*/


class IntelliWidgetShortCodeScripts {   
    
    /*
     * Sets up thickbox for post
     */
    static function thickbox() {
        wp_enqueue_script( 'thickbox' );
        add_thickbox();
    }

    /*
     * Sets up pinterest for post
     */
    static function pinterest() {
        wp_enqueue_script( 'pinterest', '//assets.pinterest.com/js/pinit.js' );
    }

    /*
     * sets up tabs content using jQuery UI tabs
     */
    static function ui_tabs() {
        wp_enqueue_script( 'jquery-ui-tabs' );
        add_action( 'wp_footer', 'IntelliWidgetShortCodeScripts::init_ui_tabs' );
    }
    
    static function init_ui_tabs() {
        ?><script>jQuery(document).ready(function($){$('.tab-panels').tabs();});</script><?php
    }

    
}
    
