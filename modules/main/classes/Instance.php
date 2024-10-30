<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) 
    exit;

/**
 * 
 * The Instance class maintains a global reference to an IntelliWidget instance
 * 
 */

class IntelliWidgetMainInstance {

    static $instance;
    private $settings;
    /**
     * make me a global singleton
     */
    function __construct(){
        self::$instance = $this;
    }
    
    function get( $field ){
        if ( isset( $this->settings[ $field ] ) ):
            // need to return empty array if array items are empty
            if ( is_array( $this->settings[ $field ] )
               && !current( $this->settings[ $field ] ) )
                return array();
            return $this->settings[ $field ];
        endif;
        return FALSE;
    }
    
    function set( $field, $value ){
        $this->settings[ $field ] = $value;
    }
    
    function reset(){
        $this->settings = array();
    }
    /**
     * Widget Defaults
     * This will utilize an options form in a future release for customization
     */
    function defaults( $instance = array() ) {
      
        //if ( empty( $instance ) ) $instance = array();
        $defaults = apply_filters( 'intelliwidget_defaults', array(
        
            // these apply to all intelliwidgets
            'content'           => 'post_list', // this is the main control, determines hook to use
            'nav_menu'          => '',          // built-in extension, uses wordpress menu instead of post_list
            'title'             => '',
            'classes'           => '',
            'container_id'      => '',
            'custom_text'       => '',
            'text_position'     => '',
            'filter'            => 0,
            'hide_if_empty'     => 0,           // applies to site-wide intelliwidgets
            'replace_widget'    => 'none',      // applies to post-specific intelliwidgets
            'nocopy'            => 0,           // applies to post-specific intelliwidgets
            'hide_title'        => 0,
            'nickname'          => '',          // optional ID for profile
            
            // these apply to post_list intelliwidgets
            'post_types'        => array( 'page', 'post' ),
            'child_pages'       => '',
            'template'          => 'menu',
            'page'              => array(),      // misnomer, stores any post_type, not just pages
            'listdata'          => '',          // NEW: JSON encoded object for custom overrides
            //'category'          => -1,        // REMOVED: legacy value, convert to tax_id
            'terms'             => array(),
            'items'             => 5,
            'sortby'            => 'selection',
            'sortorder'         => 'ASC',
            //'skip_expired'      => 0,         // removed in favor of user-defined meta conditions
            'skip_post'         => 0,
            //'future_only'       => 0,         // removed in favor of user-defined meta conditions
            //'active_only'       => 0,         // removed in favor of user-defined meta conditions
            'include_private'   => 0,
            'allterms'          => 0,
            'same_term'         => 0,
            'hide_no_posts'     => '',
            'metak1'            => '',
            'metac1'            => '',
            'metav1'            => '',
            'metak2'            => '',
            'metac2'            => '',
            'metav2'            => '',
            'metak3'            => '',
            'metac3'            => '',
            'metav3'            => '',
            'metas'             => '',
            'related'           => 0,
            //'querystr'          => '',        // not using for now
            
            // these apply to post_list items
            'length'            => 15,
            'link_text'         => __( 'Read More', 'intelliwidget' ),
            'allowed_tags'      => '',
            'imagealign'        => 'none',
            'image_size'        => 'thumbnail',
            'no_img_links'      => 0,
            'keep_title'        => 0,
            'daily'     => 0,
            'link'      => 'none',
            'tag'       => 'list',
            'gclass'    => '',
            'iclass'    => '',
            'captions'  => 0,
            'site_id'   => 'current',
            'restrict'  => 0,
            'media'     => array( 'image', 'application' ),
            'paged'     => 0,
            
            // these apply to taxonomy menus
            'hide_empty'        => 1,
            'show_count'        => 0,
            'current_only'      => 0,
            'show_descr'        => 0,
            'taxonomy'          => '',
            'hierarchical'      => 1,
            'menu_location'     => '',
            'nav_menu_classes'  => '',

        ) );
        // backwards compatibility: add content=nav_menu if nav_menu param set
        if ( !iwinstance()->get( 'content' ) && iwinstance()->get( 'nav_menu' ) && '' != ( iwinstance()->get( 'nav_menu' ) ) ) 
            iwinstance()->set( 'content', 'nav_menu' );
        // convert shortcode and legacy scalar attributes to arrays
        foreach ( array( 'page', 'post_type', 'terms' ) as $att )
            if ( isset( $instance[ $att ] ) && is_scalar( $instance[ $att ] ) )
                $instance[ $att ] = preg_split( "/,\s*/", $instance[ $att ] );
        // standard WP function for merging argument lists
        $merged = wp_parse_args( $instance, $defaults );
        //echo "<!-- \n" . print_r( $merged, TRUE ) . "\n -->\n";
        // force attachment post type for gallery
        if ( 'gallery' == $merged[ 'content' ] ) 
            $merged[ 'post_types' ] = array( 'attachment' );
        $this->settings = $merged;
        return $this->settings;
    }
    

}
    