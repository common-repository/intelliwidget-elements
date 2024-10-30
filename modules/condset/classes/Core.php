<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
    exit;

/**
 * class-intelliwidget-pro-conditions.php - Profile Set class
 *
 * @package IntelliWidget Pro
 * @subpackage includes
 * @author Lilaea Media
 * @copyright 2014 Lilaea Media LLC
 * @access public
  
 * This file and all accompanying files (C) 2014 Lilaea Media LLC except where noted. See license for details.
 */

class IntelliWidgetCondSetCore {
    
    static $conditions;
    static $options;
    static $current_term_tax_id;
    static $condset_id;
    static $override_data;
    static $template_slug;
    static $debug = '';

    static function ctl(){
        
        return iwctl();
    }
    
    static function d( $log = '', $fn = '', $backtrace = TRUE ) {
        //echo "<!-- \n" . $fn . "\n" . esc_html( $log ) . "\n -->\n";
        
    }

    static function get_condset_settings_data( $instance, $args ) {
        
        // if query matches condition set, check for child profiles
        if ( isset( self::$condset_id )
            && ( $profile_data = self::ctl()->get_settings_data( self::$condset_id, ( isset( $args[ 'widget_id' ] ) ? $args[ 'widget_id' ] : $args[ 'id' ] ), 'condset' ) ) ):
            //echo '<!-- ' . print_r( $profile_data, TRUE ) . ' -->' . PHP_EOL;
            $instance = $profile_data;
        endif;
        return $instance;
    }
    
    static function get_term_settings_data( $instance, $args ) {
        
        // if this is a term archive, check for child profiles
        if ( isset( self::$current_term_tax_id ) ):
            $other_term_id = self::ctl()->get_meta( self::$current_term_tax_id, '_intelliwidget_', 'term', 'widget_page_id' );
            if ( $profile_data = self::ctl()->get_settings_data( self::$current_term_tax_id, 
                ( isset( $args[ 'widget_id' ] ) ? $args[ 'widget_id' ] : $args[ 'id' ] ), 'term' ) ):
                $instance = $profile_data;
                // check for no-copy override
                if ( $other_term_id && empty( $profile_data[ 'nocopy' ] ) ):
                    // if this page is using another page's settings and they exist for this widget, use them
                    if ( $other_data = self::ctl()->get_settings_data( $other_term_id, ( isset( $args[ 'widget_id' ] ) ? $args[ 'widget_id' ] : $args[ 'id' ] ), 'term' ) )
                        $instance = $other_data;
                endif;
            endif;
        endif;
        return $instance; //iwinstance()->merge( $instance, $profile_data );
    }
    
    static function get_condition( $key ){
        
        if ( isset( self::$options[ 'conditions' ] ) 
            && isset( self::$options[ 'conditions' ][ $key ] ) )
            return self::$options[ 'conditions' ][ $key ];
        return FALSE;
    }
    
    static function get_selected_conditions(){
        if ( isset( self::$options[ 'conditions' ] ) )
            return (array) self::$options[ 'conditions' ];
        return array();
    }
    
    static function get_conditions() {
        
        if ( empty( self::$conditions ) ):
            self::$conditions = array();
            $post_types = IntelliWidgetMainCore::get_post_types();
            $templates = get_page_templates();
            $taxonomies = get_object_taxonomies( $post_types, 'objects' );
            foreach ( $templates as $name => $value )
                self::$conditions[ '_template_' . $value ] = sprintf(
                    __( '<strong><em>%s Template</em></strong>', 'intelliwidget' ), $name
                );
            foreach ( $post_types as $post_type ):
                if ( in_array( $post_type, array( 'post', 'page', 'attachment' ) ) )
                    continue;
                // post_type is ambiguous because it identifies archive, but needed for back compat
                self::$conditions[ '_post_type_' . $post_type ] = sprintf(
                    __( '%s Archive', 'intelliwidget' ),
                    ucwords( str_replace( '_', ' ', $post_type ) )
                );
                // using post_single to identify singular custom post type
                self::$conditions[ '_post_single_' . $post_type ] = sprintf(
                    __( '%s Single Post', 'intelliwidget' ),
                    ucwords( str_replace( '_', ' ', $post_type ) )
                );
            endforeach;
            foreach ( $taxonomies as $taxonomy ):
                if ( empty( $taxonomy->rewrite ) || 'post_format' == $taxonomy->name )
                    continue;
                self::$conditions[ '_taxonomy_' . $taxonomy->name ] = sprintf(
                    __( '%s Term Archive', 'intelliwidget' ),
                    ucwords( str_replace( '_', ' ', $taxonomy->name ) )
                );
            endforeach;
            self::$conditions = array_merge( self::$conditions, array(
                'is_404'                => __( 'File Not Found Page', 'intelliwidget' ),
                'is_front_page'         => __( 'Default Front Page', 'intelliwidget' ),
                'is_single'             => __( 'Any Single Post', 'intelliwidget' ),
                'is_page'               => __( 'Any Single Page', 'intelliwidget' ),
                'is_attachment'         => __( 'Any Single Attachment', 'intelliwidget' ),
                'is_singular'           => __( 'Any Single Post, Page or Attachment', 'intelliwidget' ),
                'is_search'             => __( 'Search Results Page', 'intelliwidget' ),
                'is_home'               => __( 'Default Posts (Blog) Archive', 'intelliwidget' ),
                'is_posts_page'         => __( 'Posts (Blog) Page', 'intelliwidget' ),
                'is_post_type_archive'  => __( 'Any Custom Post Type Archive', 'intelliwidget' ),
                'is_tax'                => __( 'Any Term Archive', 'intelliwidget' ),
                'is_year'               => __( 'Year Archive', 'intelliwidget' ),
                'is_month'              => __( 'Month Archive', 'intelliwidget' ),
                'is_day'                => __( 'Day Archive', 'intelliwidget' ),
                'is_time'               => __( 'Time Archive', 'intelliwidget' ),
                'is_date'               => __( 'Date Archive', 'intelliwidget' ),
                'is_author'             => __( 'Author Archive', 'intelliwidget' ),
                'is_archive'            => __( 'Any Archive', 'intelliwidget' ),
            ) );
        endif;
        
        //die( print_r( self::$conditions, TRUE ) );
        return self::$conditions;
    }

    static function get_condset( $key ){
        
        if ( isset( self::$options[ 'condsets' ] ) 
            && isset( self::$options[ 'condsets' ][ $key ] ) )
            return self::$options[ 'condsets' ][ $key ];
        return FALSE;
    }
    
    static function get_condsets(){
        
        if ( isset( self::$options[ 'condsets' ] ) )
            return (array) self::$options[ 'condsets' ];
        return FALSE;
    }
    
    static function get_query_conditions() {
        
        $tax = NULL;
        $queried_object = get_queried_object();
        if ( is_object( $queried_object ) &&
            isset( $queried_object->term_taxonomy_id ) ):
            self::$current_term_tax_id = $queried_object->term_taxonomy_id;
        endif;
        // FIXME: need priority for these
        foreach ( self::get_selected_conditions() as $condition => $condset_id ):
            if ( $condset_id && self::test_condition( $condition ) ):
                self::$debug .= "PUBLIC MATCH TO " . $condset_id . "\n";
                self::$condset_id = $condset_id;
                self::map_nicknames( self::ctl()->get_profile_data( $condset_id, 'condset' ) );
                break;
            endif;
        endforeach;
    }

    /**
     * returns the most specific condition set matching current post/page being edited
     */
    static function get_profile_override( $objtype, $type ){
        $matched = 0;
        if ( 'post' == $objtype ):
            global $post;
            // test for default landing pages
            if ( 'page' === get_option( 'show_on_front' ) ):
                if ( ( intval( get_option( 'page_on_front' ) ) === intval( $post->ID )
                    && ( $condset_id = self::get_condition( 'is_front_page' ) ) )
                    ||
                    ( intval( get_option( 'page_for_posts' ) ) === intval( $post->ID )
                    && ( $condset_id = self::get_condition( 'is_posts_page' ) ) ) 
                    ):
                    self::$debug .= "override:front page or post page\n";
                    $matched = 1;// matched
                endif;
            endif;
            // test post type
            if (
                ( 'post' == $type && ( $condset_id = self::get_condition( 'is_single' ) ) )
                    ||
                ( 'page' == $type && ( $condset_id = self::get_condition( 'is_page' ) ) )
                    ||
                ( 'attachment' == $type && ( $condset_id = self::get_condition( 'is_attachment' ) ) )
                    ||
                ( $condset_id = self::get_condition( '_post_single_' . $type ) )
                    ||
                ( $condset_id = self::get_condition( 'is_singular' ) ) 
                ):
                self::$debug .= "override:singular\n";
                $matched = 1;// matched
	        elseif ( ( self::$template_slug = apply_filters( 'iwtemplates_template_slug', $type, $post ) )
                && ( $condset_id = self::get_condition( '_template_' . self::$template_slug ) ) ):
                self::$debug .= "override:iwtemplate\n";
                $matched = 1;// matched
            // check for match to static templates
            elseif ( ( self::$template_slug = get_page_template_slug( $post->ID ) )
                && ( $condset_id = self::get_condition( '_template_' . self::$template_slug ) ) ):
                self::$debug .= "override:static template\n";
                $matched = 1;// matched
            // check for match to IW templates
            endif;
        // test term archive
        elseif ( 'term' == $objtype ):
            if (
                ( $condset_id = self::get_condition( '_taxonomy_' . $type ) )
                    ||
                ( $condset_id = self::get_condition( 'is_tax' ) )
                    ||
                ( $condset_id = self::get_condition( 'is_archive' ) )
                ):
                self::$debug .= "override:term\n";
                $matched = 1;// matched
            endif;
        endif;
        if ( $matched ):
            self::$debug .= "MATCHED TO " . $condset_id . "\n";
            self::$condset_id = $condset_id;
            self::map_nicknames();
        endif;
    }
    
    static function map_nicknames(){
        self::$debug .= 'in map_nicknames
        ';
        if ( $data = self::ctl()->get_profile_data( self::$condset_id, 'condset' ) ):
            if ( !empty( $data ) && !empty( $data[ 'box_map' ] ) && !empty( $data[ 'profiles' ] ) ):
                foreach ( $data[ 'box_map' ] as $box_id => $widget_id ):
                    $profile = $data[ 'profiles' ][ $box_id ];
                    if ( !empty( $profile[ 'restrict' ] ) && !empty( $profile[ 'nickname' ] ) ):
                        $profile[ 'widget_id' ] = $widget_id;
                        self::$override_data[ $profile[ 'nickname' ] ] = $profile;
                        self::$debug .= 'map_nicknames: ' . $profile[ 'nickname' ] . "\n";
                    endif;
                endforeach;
            endif;
        endif;
    }
    
    static function init() {
        // convert legacy pro options to new format
        if ( isset( IntelliWidgetMainCore::$options[ 'condsets' ] ) )
            self::convert_options();
        if ( is_multisite() )
            switch_to_blog( get_main_site_id() );	   
        if ( !( self::$options = get_option( IWELEMENTS_CONDSET ) ) ):
            //echo 'initializing IntelliWidgetCondSetCore::$options' . "\n";
            self::$options = array(
                'condsets' => array(
                    '1' => __( 'Untitled Set', 'intelliwidget' ),
                ),
                'conditions' => array(),
            );
        endif;
        if ( is_multisite() && ms_is_switched() )
            restore_current_blog();  
        
        add_filter( 'intelliwidget_extension_settings', 'IntelliWidgetCondSetCore::get_condset_settings_data', 10, 3 );
        add_filter( 'intelliwidget_extension_settings', 'IntelliWidgetCondSetCore::get_term_settings_data', 15, 3 );
        // restrict settings to conditional set profiles
        add_filter( 'intelliwidget_extension_settings', 'IntelliWidgetCondSetCore::merge_settings_data', 50, 3 );
        add_filter( 'intelliwidget_merge_profiles', 'IntelliWidgetCondSetCore::merge_profiles', 10, 3 );
        add_action( 'template_redirect', 'IntelliWidgetCondSetCore::get_query_conditions' );
        //add_action( 'admin_footer', 'IntelliWidgetCondSetCore::dump_wp_query' );
        //add_action( 'wp_footer', 'IntelliWidgetCondSetCore::dump_wp_query' );
        if ( is_admin() )
            new IntelliWidgetCondSetAdmin();
    }
    
    static function merge_profiles( $data, $objtype, $type = NULL ){
        self::$debug .= 'in merge_profiles
        ';
        // pass through if this is a master profile set
        if ( 'condset' == $objtype )
            return $data;

        // unset restriction if previously set. This keeps profiles from being uneditable if master profile changes.
        foreach( $data[ 'box_map' ] as $box_id => $widget_id )
            $data[ 'profiles' ][ $box_id ][ 'restrict' ] = '';

        self::get_profile_override( $objtype, $type );
        // get matching master profile and if restricted, override all but editable fields or add if necessary
        if ( isset( self::$override_data ) ):

            foreach ( self::$override_data as $nickname => $override_profile ):
                $profile_matched = 0;
                // merge existing profiles matching nickname
                foreach ( $data[ 'box_map' ] as $box_id => $widget_id ):
                    if ( !empty( $data[ 'profiles' ][ $box_id ][ 'nickname' ] )
                        && $data[ 'profiles' ][ $box_id ][ 'nickname' ] == $override_profile[ 'nickname' ] ):

                        $data[ 'profiles' ][ $box_id ] = self::merge_settings_data( $data[ 'profiles' ][ $box_id ], array() );
                        $profile_matched = 1;
                        break;
                    endif;
                endforeach;
                // add restricted profiles if they do not exist yet
                if ( !$profile_matched ):
                    $new_id = 1;
                    while ( array_key_exists( $new_id, $data[ 'box_map' ] ) ):
                        $new_id++;
                    endwhile;
                    $data[ 'box_map' ][ $new_id ] = $override_profile[ 'widget_id' ];
                    $data[ 'profiles' ][ $new_id ] = $override_profile;
                endif;
            endforeach;
        endif;
        return $data;
    }
    
    
    static function test_condition( $condition ) {
        global $wp_query, $post;
        if ( 'is_front_page' == $condition && is_front_page() ):
            
            return TRUE;
        elseif ( strpos( $condition, 'post_type_' ) > 0 &&
            ( $type = str_replace( '_post_type_', '', $condition ) ) &&
            is_post_type_archive( $type ) ):
            
            return TRUE;
        elseif ( strpos( $condition, 'post_single_' ) > 0 &&
            ( $type = str_replace( '_post_single_', '', $condition ) ) &&
            is_singular( $type ) ):
            
            return TRUE;
        elseif ( strpos( $condition, 'taxonomy_' ) > 0 &&
            ( $tax = str_replace( '_taxonomy_', '', $condition ) ) &&
            is_tax( $tax ) ):
            
            return TRUE;
        elseif ( isset( $wp_query->{$condition} ) && $wp_query->{$condition} ):
            
            return TRUE;
        // test if post is using specific IW template
        elseif ( is_object( $post ) && ( self::$template_slug = apply_filters( 'iwtemplates_template_slug', '', $post ) )
            && $condition = '_template_' . self::$template_slug ):
            self::$debug .= "override:IW template\n";
            return TRUE;
        // test if post is using specific STATIC template
        elseif ( is_object( $post ) && ( self::$template_slug = get_page_template_slug( $post->ID ) )
            && $condition = '_template_' . self::$template_slug ):
            self::$debug .= "override:static template\n";
            return TRUE;
        endif;
        return FALSE;
    }

    /**
     * merge_settings_data
     * filter permits master profile to restrict editable intelliwidget settings
     * a limited set.
     */
    static function merge_settings_data( $instance, $args ){
        if ( !empty( $instance[ 'nickname' ] ) 
            && isset( self::$override_data[ $instance[ 'nickname' ] ] ) ):
            $override_profile = self::$override_data[ $instance[ 'nickname' ] ];
            // copy merge fields from override instance into original instance
            foreach( apply_filters( 'intelliwidget_merge_fields', array(
                'title',
                'classes',
                'hide_title',
                'replace_widget',
                'nav_menu',
                'nav_menu_classes',
                'custom_text',
                'text_position',
                //'filter',
                'nocopy',
                'page',
                'terms',
                'allterms',
                //'site_id',
                'listdata'
                ) ) as $field ):
                if ( isset( $instance[ $field ] ) && !empty( $instance[ $field ] ) )
                    $override_profile[ $field ] = $instance[ $field ];
            endforeach;
            $instance = $override_profile;
        endif;
        //if ( $instance )
        //  echo "<!-- \merge_settings_data:\n" . print_r( $instance, TRUE ) . "\n -->\n";
        return $instance;
    }
            
    static function dump_wp_query(){
        global $wp_query,$post;
        echo '<textarea style="position:fixed;z-index:10000;bottom:0;left:25%;width:75%;height:100px;font-size:10px;font-family:monospace">
        debug: ' . self::$debug . "
        template: " . self::$template_slug . "
        condset_id: " . self::$condset_id . "
        override_data: " . print_r( self::$override_data, TRUE ) . "
        post: " . print_r( $post, TRUE ) 
            . '</textarea>';
    }
  
}