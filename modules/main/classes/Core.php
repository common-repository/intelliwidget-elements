<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) 
    exit;

/**
 * 
 * The Core Class contains functions that are used to initialize the plugin globally
 * and that are common to all module classes.
 *
 */

class IntelliWidgetMainCore {
    static $instance;
    static $options;
    static $_clone;
    static $old_plugins = array(
        'intelliwidget-per-page-featured-posts-and-menus/intelliwidget.php',
        'intelliwidget-pro/intelliwidget-pro.php',
        'intelliwidget-archive-taxonomy-ext/intelliwidget-archive-taxonomy-ext.php', // really old version
    );
    static $this_plugin = 'intelliwidget-elements/intelliwidget.php';

    /**
     * _clone is a semaphore flag that is set when an item is being 
     * duplicated in the layouts admin. If the flag is not set
     * then the _clone functions are skipped. This is necessary because
     * objects are cloned before saving to the WordPress Options API and
     * not doing so would cause all manner of havoc with the object data.
     */
    static function clonechk(){
        return empty( self::$_clone );
    }
    
    static function cloneset( $on = 1 ){
        self::$_clone = $on;
    }

    /**
     * Ensure that "post-thumbnails" support is available for those themes that don't register it.
     */
    static function ensure_post_thumbnails_support () {
        if ( ! current_theme_supports( 'post-thumbnails' ) ) { add_theme_support( 'post-thumbnails' ); }
    } 

    static function get_option( $name, $key = NULL, $all = FALSE ) {
        if ( !isset( self::$options ) ):
            self::$options = (array) get_site_option( IWELEMENTS_OPTIONS );
            if ( !isset( self::$options[ 'use_condset' ] ) )
                self::$options[ 'use_condset' ] = 1;
            if ( !isset( self::$options[ 'use_bootstrap' ] ) )
                self::$options[ 'use_bootstrap' ] = 1;
            if ( !isset( self::$options[ 'use_iwicons' ] ) )
                self::$options[ 'use_iwicons' ] = 1;
            if ( !isset( self::$options[ 'use_switcher' ] ) )
                self::$options[ 'use_switcher' ] = 1;
        endif;
        if ( $all )
            return self::$options;
        if ( isset( self::$options[ $name ] ) ):
            if ( !empty ( $key ) && is_array( self::$options[ $name ] ) )
                return self::$options[ $name ][ $key ];
            return self::$options[ $name ];
        endif;
        return FALSE;
    }

    static function get_post_types( $queryable_only = FALSE, $custom_only = FALSE ){
        
        if ( function_exists( 'get_post_types' ) ):
            $args = array( 'public' => TRUE );
            if ( $queryable_only ) $args[ 'publicly_queryable' ] = TRUE;
            $types  = get_post_types( $args );
        else:
            $types  = array( 'post', 'page' );
        endif;
        if ( $custom_only ):
            foreach( $types as $type ):
                if ( post_type_supports( $type, 'custom-fields' ) )
                    $arr[] = $type;
            endforeach;
        else:
            $arr = $types;
        endif;
        return apply_filters( 'intelliwidget_post_types', $arr );
    }
    
    static function get_sites(){
        $sites = array();
        foreach ( get_sites() as $site )
            $sites[ $site->blog_id ] = get_blog_option( $site->blog_id, 'blogname' );
        return $sites;
    }
    

    static function use_global_terms(){
        global $wpdb;
        //change terms table to use main site's
        $wpdb->terms = $wpdb->base_prefix . 'terms';
        //change taxonomy table to use main site's taxonomy table
        $wpdb->term_taxonomy = $wpdb->base_prefix . 'term_taxonomy';
        //change taxonomy table to use main site's term meta table
        $wpdb->termmeta = $wpdb->base_prefix . 'termmeta';
    }
    
    /**
     * Load Plugin if correct WP/IW versions
     */
    static function init() {
        // Load language support
        self::load_lang();
        
        // verify WP version support
        global $wp_version;
        if ( version_compare( $wp_version, IWELEMENTS_MIN_WP_VERSION, '<' ) ):
            add_action( 'admin_notices', 'IntelliWidgetMainUpgrade::version_notice' );
            return FALSE;
        endif;
                
        
        /**
         * check for earlier versions of IW or IW Pro and show or perform options
         */
        foreach ( self::$old_plugins as $p ):
            if ( file_exists( dirname( IWELEMENTS_DIR ) . '/' . $p ) ):
                add_action( 'init', 'IntelliWidgetMainUpgrade::options' );
                return FALSE;
            endif;
        endforeach;
        // check for responses
        if ( isset( $_REQUEST[ 'updated' ] ) && 'iwelements_complete' == $_REQUEST[ 'updated' ] )
            add_action( 'admin_notices', 'IntelliWidgetMainUpgrade::complete' );
        if ( isset( $_REQUEST[ 'error' ] ) && 'iwelements_error' == $_REQUEST[ 'error' ] )
            add_action( 'admin_notices', 'IntelliWidgetMainUpgrade::error' );
        /**
         * end prior version check
         */
        
        
        // Enable thumbnails
        self::ensure_post_thumbnails_support();
        
        /**
         * single controller class, no longer using Admin.php
         */
        // load primary controller
        self::$instance = new IntelliWidgetMainController();
        
        // initialize Shortcodes
        IntelliWidgetShortCodeCore::init();
        // initialize Conditional Profiles and Condition Sets
        if ( self::get_option( 'use_condset' ) )
            IntelliWidgetCondSetCore::init();
        // initialize Type Switcher
        if ( self::get_option( 'use_switcher' ) )
            new IntelliWidgetMainTypeSwitcher();
            
        // launch the link update queue
        //new IntelliWidgetMainLinkQueue(); // FIXME - inserting random junk in links
        
        // before any taxonomy/terms are initialized, point the terms to main site
        add_action( 'init', 'IntelliWidgetMainCore::use_global_terms', 0 );
        // on blog switching, set it again, so it does not use current blog's tax/terms
        // works both on switch/restore blog
        add_action( 'switch_blog', 'IntelliWidgetMainCore::use_global_terms', 0 );        


        // disable emojis
        if ( self::get_option( 'disable_emojis' ) )
            add_action( 'init', 'IntelliWidgetMainCore::disable_emojis' );

        // register the Widget class
        add_action( 'widgets_init', 'IntelliWidgetMainCore::register_widget' );
                
        // don't let ACF hijack custom fields
        add_filter( 'acf/settings/remove_wp_meta_box', 'IntelliWidgetMainCore::acf_remove_cdf_hijack' );

        // update all links when there are changes to post type or slug
        //add_action( 'pre_post_update', 'IntelliWidgetMainCore::apply_permalink_update', 10, 2 ); // FIXME - inserting random junk in links

    }
    
    /**
     * FIXME
     * this works in theory but in production it is generating invalid links
     
    static function apply_permalink_update( $id, $data ){
        // grab the permalink state before the update for find
        $update_before = get_permalink( $id );
        // background process will get newest version of permalink for replace
        $item = array(
            'id'        => $id,
            'url'       => $update_before,
            'method'    => 'find',
        );
        file_put_contents( IWELEMENTS_DIR . '/linkqueue.log.txt', __METHOD__ . "\n" . print_r( $item, TRUE ) . "\n", FILE_APPEND );
        $sl = new IntelliWidgetMainLinkQueue();
        $sl->push_to_queue( $item ); 
        $sl->save()->dispatch();
    }
    */

    static function acf_remove_cdf_hijack() {
        return 0;
    }

    static function load_lang() {
        load_plugin_textdomain( 'intelliwidget', FALSE, basename( IWELEMENTS_DIR ) . '/lang' );
    }
    
    /**
     * Disable emoji's
     */
    static function disable_emojis() {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        add_filter( 'tiny_mce_plugins', 'IntelliWidgetMainCore::iwtemplates_disable_emojis_tinymce' );
    }

    /**
     * Filter function used to remove the tinymce emoji plugin.
     * 
     * @param    array  $plugins  
     * @return   array             Difference betwen the two arrays
     */
    static function iwtemplates_disable_emojis_tinymce( $plugins ) {
        if ( is_array( $plugins ) )
            return array_diff( $plugins, array( 'wpemoji' ) );
        else
            return array();
    }
    
    static function register_widget() {
        
        // only register for admin on main site.
        if ( is_multisite() && is_admin() && !is_main_site() )
            return;
        register_widget( "IntelliWidgetMainWidget" );
    }
    
}
