<?php
if ( !defined( 'ABSPATH' ) ) exit;

/* class-options
 *
 */
if ( ! class_exists( 'IntelliWidgetMainUpgrade' ) ):

class IntelliWidgetMainUpgrade {
    
    static $error;
    
    static function init(){
        
        if ( is_multisite() )
            switch_to_blog( get_main_site_id() );	
        
        self::convert_condset_options(); 
        
        if ( is_multisite() && ms_is_switched() )
            restore_current_blog();   
        
        self::delete_previous_versions();
        
        if ( empty( self::$error ) )
            $redir = self_admin_url( "plugins.php?updated=iwelements_complete" );
        else
            $redir = self_admin_url( "plugins.php?error=" . self::$error );
        wp_redirect( $redir );
    }
    
    static function deactivate(){
        deactivate_plugins( IntelliWidgetMainCore::$this_plugin, FALSE, is_network_admin() );
    }
    
    /**
     * deletes old plugins without removing option settings
     */
    static function delete_previous_versions() {
        if ( isset( $_REQUEST[ 'deleted' ] ) ) 
            return;
        // clean up hooks from < 2.2.1
        wp_clear_scheduled_hook( 'check_plugin_updates-intelliwidget-pro' );
        wp_clear_scheduled_hook( 'check_plugin_updates-intelliwidget-archive-taxonomy-ext' );
        // remove old Pro version
        if ( current_user_can( 'delete_plugins' ) ):
            $redir = NULL;

            // deactivate old Pro version
            deactivate_plugins( IntelliWidgetMainCore::$old_plugins, TRUE ); // do not fire deactivate hooks

            // remove uninstall hook so that options are preserved
            $uninstallable_plugins = (array) get_option( 'uninstall_plugins' );
            foreach( IntelliWidgetMainCore::$old_plugins as $pluginfile ):
                if ( isset( $uninstallable_plugins[ $pluginfile ] ) )
                    unset( $uninstallable_plugins[ $pluginfile ] );
            endforeach;
            update_option( 'uninstall_plugins', $uninstallable_plugins );
            unset( $uninstallable_plugins );

            // remove old versions
            $delete_result = delete_plugins( IntelliWidgetMainCore::$old_plugins );
            //Store the result in a cache rather than a URL param due to object type & length
            global $user_ID;
            set_transient( 'plugins_delete_result_' . $user_ID, $delete_result ); 
            // force plugin cache to reload
            wp_cache_delete( 'plugins', 'plugins' );

            // if this is two-step FTP authentication, redirect back to activated
            if ( $redir ):
                if ( is_wp_error( $delete_result ) ):
                    $redir = self_admin_url( "plugins.php?deleted=" . implode( ',', IntelliWidgetMainCore::$old_plugins ) );
                    wp_redirect( $redir );
                    exit;
                endif;
            endif;
        endif;
    }
    
    /**
     * Move legacy Pro options to separate global site record
     */
    static function convert_condset_options(){
        
        if ( !( $old_options = get_option( 'intelliwidgetpro_options' ) )
           || !isset( $old_options[ 'condsets'] ) )
            return;
        $new_options[ 'condsets' ] = $old_options[ 'condsets' ];

        foreach ( IntelliWidgetCondSetCore::get_conditions() as $key => $label ):
            if ( isset( $old_options[ $key ] ) )
                $new_options[ 'conditions' ][ $key ] = $old_options[ $key ];
        endforeach;
        update_site_option( IWELEMENTS_CONDSET, $new_options );
        delete_option( 'intelliwidgetpro_options' );
    }
    
    static function options(){
        // clear any other plugin requests
        unset( $_GET[ 'action' ] );
        unset( $_GET[ 'checked' ] );
        unset( $_GET[ 'verify-delete' ] );
        unset( $_REQUEST[ 'action' ] );
        unset( $_REQUEST[ 'checked' ] );
        unset( $_REQUEST[ 'verify-delete' ] );
        if ( isset( $_REQUEST[ 'iw_init_deactivate' ] ) 
            && current_user_can( 'activate_plugins' ) 
                && check_admin_referer( 'iw_install_option', 'iw_init_nonce' ) 
           ):
            add_action( 'admin_init', 'IntelliWidgetMainUpgrade::deactivate' );
        elseif ( isset( $_REQUEST[ 'iw_init_upgrade' ] ) 
            && is_super_admin()
                && check_admin_referer( 'iw_install_option', 'iw_init_nonce' ) 
               ):
            add_action( 'admin_init', 'IntelliWidgetMainUpgrade::init' );
        else:
            add_action( 'admin_notices', 'IntelliWidgetMainUpgrade::notice' );
        endif;
    }
    
    static function notice(){
        include( IWELEMENTS_DIR . '/modules/main/forms/upgrade.php' );
    }
    
    static function complete(){
        include( IWELEMENTS_DIR . '/modules/main/forms/complete.php' );
    }
    
    static function error(){
        include( IWELEMENTS_DIR . '/modules/main/forms/error.php' );
    }

    static function version_notice(){
        self::deactivate();
        unset( $_GET[ 'activate' ] );
        include( IWELEMENTS_DIR . '/modules/forms/version-notice.php' );
    }
}
endif;