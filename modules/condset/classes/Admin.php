<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) 
    exit;

if ( ! class_exists( 'IntelliWidgetCondSetAdmin' ) ):
class IntelliWidgetCondSetAdmin {
    var $moduledir = 'condset';
    
    function __construct(){
        
        add_action( 'init', array( $this, 'init' ) );
    }
        
    function add_condset(){
        
        if ( !iwctl()->validate_post( 'iwcondsetadd', '_wpnonce', FALSE ) ) 
            return FALSE;
        if ( ! ( $condsets = IntelliWidgetCondSetCore::get_condsets() ) )
            $condsets = array( '0' => '' );
        $maxid = max( array_keys( $condsets ) ) + 1;
        $this->set_option( 'condsets', __( 'Untitled Set', 'intelliwidget' ), $maxid );
        $this->save_options();
        if ( is_multisite() )
            switch_to_blog( get_main_site_id() );	
        update_option( 
            IWELEMENTS_CONDSET . '_' . $maxid, 
            array(
                'map'   => array()
            ) 
        );
        if ( is_multisite() && ms_is_switched() )
            restore_current_blog();    
        global $pagenow;
        wp_safe_redirect( admin_url( $pagenow . '?page=iwelements-condsets&updated=2&condset_id=' . $maxid ) );
        die();
    }
  
    function d( $log = '', $fn = '', $backtrace = TRUE ) {
        
    }
    
    function delete_condset(){
        
        if ( !iwctl()->validate_post( 'iwcondsetdel', '_wpnonce', FALSE ) ) 
            return FALSE;
        $id = sanitize_text_field( $_GET[ 'iwcondsetdel' ] );
        iwctl()->objid = $id;
        
            
            $this->set_option( 'condsets', NULL, $id );
            foreach ( IntelliWidgetCondSetCore::get_conditions() as $condition => $label )
                if ( IntelliWidgetCondSetCore::get_condition( $condition ) == $id ) 
                    $this->set_option( 'conditions', '', $condition );
            $this->save_options();
            delete_option( 'intelliwidget_data_condset_' . $id );            
        global $pagenow;
        wp_safe_redirect( admin_url( $pagenow . '?page=iwelements-condsets&updated=3' ) ); 
        die();
    }

    function enqueue_admin() {

        // layout interface styles
        wp_enqueue_style(
            'intelliwidget-template-options', 
            trailingslashit( IWELEMENTS_URL ) . 'css/layout.css', 
            FALSE, 
            IWELEMENTS_VERSION
        );
        
    }
    
    function formpath( $basename ){
        
        return implode( DIRECTORY_SEPARATOR, array( 
            IWELEMENTS_DIR,
            'modules',
            $this->moduledir,
            'forms',
            preg_replace( "/[^\w\-]/", '', $basename ) . '.php'
        ) );
    }
        
    function get_current_panel_id(){
        
        return $this->get_objid();
    }
    
    function get_objid(){
        
        return iwctl()->objid;
    }
    
    function init(){
        
        // term admin actions
        //if ( isset( $_GET[ 'taxonomy' ] ) && '' != $_GET[ 'taxonomy' ] )
        add_action( 'load-edit-tags.php',           array( $this, 'term_metabox_actions' ) );
        add_action( 'edit_term',                    array( $this, 'term_save_data' ), 1, 3 );
        

        // condset panel admin actions
        add_action( 'admin_menu',                   array( $this, 'options_page' ), 20 );
        add_action( 'intelliwidget_options_tab',    array( $this, 'options_tab' ), 20, 2 ); 
        add_filter( 'iwf_export_modules',    array( $this, 'export_module' ) ); 
        add_action( 'iwf_import_modules',    array( $this, 'import_module' ) );
        
    }
    
    function export_module( $array = array() ){
        if ( isset( IntelliWidgetCondSetCore::$options ) 
            && isset( IntelliWidgetCondSetCore::$options[ 'condsets' ] ) ):
        
            $profiles = array();

            if ( is_multisite() )
                switch_to_blog( get_main_site_id() );	

            foreach ( IntelliWidgetCondSetCore::$options[ 'condsets' ] as $condset_id => $condset )
                if ( $profile = get_option( 'intelliwidget_data_condset_' . $condset_id ) )
                    $profiles[ $condset_id ] = $profile;

            if ( is_multisite() && ms_is_switched() )
                restore_current_blog();    

            $array[ 'condset' ] = array(
                'options'   => IntelliWidgetCondSetCore::$options,
                'profiles'  => $profiles,
            );

        endif;
        return $array;
    }
    
    function import_module( $array = array() ){
        if ( isset( $_POST[ 'confirm_iwfimport' ] )
            && in_array( 'condset', $_POST[ 'confirm_iwfimport' ] )
            && isset( $array[ 'condset' ] ) 
            && is_array( $array[ 'condset' ] ) ):
            
            // replace condsets
            if ( $array[ 'condset' ][ 'options' ] ):
                IntelliWidgetCondSetCore::$options = $array[ 'condset' ][ 'options' ];
                $this->save_options();
            endif;
        
            //replace condset profiles
            if ( is_multisite() )
                switch_to_blog( get_main_site_id() );	
            if ( $array[ 'condset' ][ 'profiles' ] ):
                foreach( $array[ 'condset' ][ 'profiles' ] as $condset_id => $profile )
                    update_option( 'intelliwidget_data_condset_' . $condset_id, $profile );
            if ( is_multisite() && ms_is_switched() )
                restore_current_blog();
        
            endif;
            
        endif;
    }
    
    function options_init() {
        
        // migrate old options to this class

        // save conditional options and condsets
        iwctl()->objtype = 'condset';
        iwctl()->idfield = 'condset_ID';
        if ( isset( $_POST[ 'iwcondsetupd' ] ) )
            $this->update_condset();
        if ( isset( $_GET[ 'iwcondsetadd' ] ) )
            $this->add_condset();
        if ( isset( $_GET[ 'iwcondsetdel' ] ) )
            $this->delete_condset();
        add_action( 'admin_print_styles', array( $this, 'enqueue_admin' ) );
    }

    function options_page(){
        
        $hook = add_submenu_page(
            'iwelements', 
            __( 'Conditional Profiles', 'intelliwidget' ), 
            __( 'Conditional Profiles', 'intelliwidget' ), 
            'edit_theme_options', 
            'iwelements-condsets', 
            array( $this, 'options_panel' )
        );
        add_action( 'load-' . $hook, array( $this, 'options_init' ) );        
    }
    
    function options_panel( $active_tab ) {
        
        global $pagenow;
        include( $this->formpath( 'condsets-panel' ) );
    }
    
    function options_tab( $active_tab ) {
        
        include( $this->formpath( 'options-tab-condsets' ) );
    }
    
    function save_condset_options(){
        
        if ( !iwctl()->validate_post( 'iwpage_%s', 'iwpage_%s', FALSE ) || !( $objid = $this->get_objid() ) )
            return FALSE;
        $condfield  = 'intelliwidget_condition_' . $objid;
        $namefield  = 'intelliwidget_condset_name_' . $objid;
        $new_name   = isset( $_POST[ $namefield ] ) ?
            sanitize_text_field( $_POST[ $namefield ] ) :
            '';
        
        $new_conditions = isset( $_POST[ $condfield ] ) ? 
            ( array ) $_POST[ $condfield ] : 
            array();
        $this->set_option( 'condsets', $new_name, $objid );
        
        // set or unset conditions - checkboxes are such a pain in the arse
        foreach ( IntelliWidgetCondSetCore::get_conditions() as $condition => $label ):
            // set new option
            if ( in_array( $condition, $new_conditions ) ):
                $this->set_option( 'conditions', $objid, $condition );
            // unset existing option
            elseif ( ( $condid = IntelliWidgetCondSetCore::get_condition( $condition ) ) && $condid == $objid ):
                $this->set_option( 'conditions', NULL, $condition );
            endif;
        endforeach;
        $this->save_options();
    }
    
    /**
     *
     */
    function save_options() {
        
        if ( is_multisite() )
            switch_to_blog( get_main_site_id() );	
        $res = update_option(
            IWELEMENTS_CONDSET, IntelliWidgetCondSetCore::$options,
            FALSE
        );
        if ( is_multisite() && ms_is_switched() )
            restore_current_blog();    
        return $res;
    }
    
    function set_option( $name, $value, $key = NULL ) {
        //die( print_r( IntelliWidgetCondSetCore::$options, TRUE  ) );
        if ( empty( IntelliWidgetCondSetCore::$options[ $name ] ) )
            IntelliWidgetCondSetCore::$options[ $name ] = array();
        if ( !empty( $key ) ):
            if ( NULL === $value )
                unset( IntelliWidgetCondSetCore::$options[ $name ][ $key ] );
            else
                IntelliWidgetCondSetCore::$options[ $name ][ $key ] = $value;
            return;
        endif;
        if ( NULL === $value )
            unset( IntelliWidgetCondSetCore::$options[ $name ] );
        else
            IntelliWidgetCondSetCore::$options[ $name ] = $value;
    }
    
    function term_metabox_actions() {
        
            iwctl()->objtype = 'term';
            iwctl()->idfield = 'term_taxonomy_ID';
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
        add_action( sanitize_text_field( $_REQUEST[ 'taxonomy' ] ) . '_edit_form', array( $this, 'term_metabox_form' ), 10, 1 );
    }

    function term_metabox_form( $term ){
        
        iwctl()->objid = $term->term_taxonomy_id;
        
        
        include( $this->formpath( 'term-metabox' ) );
    }
    
    /**
     * Parse POST data and update page-specific data using custom fields
     * @param <integer> $id -- revision id
     * @param <object>  $post -- revision post data
     * @return  void
     */
    function term_save_data( $term_id, $tt_id, $taxonomy ) {
        
        global $pagenow;
        if ( empty( $tt_id ) || 'edit_tags.php' != $pagenow ) 
            return FALSE;

        iwctl()->objtype = 'term';
        iwctl()->idfield = 'term_taxonomy_ID';
        iwctl()->objid = $tt_id;

        iwctl()->save_data();
        // save copy page id ( i.e., "use settings from ..." ) if it exists
        iwctl()->save_copy_id();
    }

    function update_condset(){
        
        if ( !iwctl()->validate_post( 'iwpage_%s', 'iwpage_%s', FALSE ) ) 
            return FALSE;
        $this->save_condset_options();
        $this->msg = '1';
        global $pagenow;
        wp_safe_redirect( 
            admin_url( $pagenow . '?page=' 
                . 'iwelements' 
                . '-condsets&updated=1&condset_id=' 
                . $this->get_objid() ) 
        );
        die();
    }
    

}

endif;
