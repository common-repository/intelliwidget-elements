<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * class-widget-intelliwidget.php - IntelliWidgetMain Widget Class
 *
 * @package IntelliWidgetMain
 * @subpackage includes
 * @author Jason C Fleming
 * @copyright 2014-2015 Lilaea Media LLC
 * @access public
 */
class IntelliWidgetMainWidget extends WP_Widget {

    /**
     * Constructor
     */
    function __construct() {
        $widget_ops          = array( 'description' => __( 'Menus, Featured Posts, HTML and more, customized per page or site-wide.', 'intelliwidget' ) );
        $control_ops         = array( 'width' => 400, 'height' => 350 );
        parent::__construct( 'intelliwidget', __( 'IntelliWidget', 'intelliwidget' ), $widget_ops, $control_ops );
    }
    
    /**
     * intelliwidget_extension_settings filter allows widget instance to be replaced by post/term/condset profiles
     */
    function widget( $args, $instance ) {
        // should we hide?
        // applies post/term/condset-specific settings to this widget instance
        $instance = apply_filters( 'intelliwidget_extension_settings', $instance, $args );
        iwinstance()->defaults( $instance );
        if ( iwinstance()->get( 'hide_if_empty' ) )
            return;
        // renders the widget instance
        echo iwctl()->build_list( $args );
    }
    
    /**
     * Widget Update method
     */
    function update( $new_instance, $old_instance ) {
        return apply_filters( 'intelliwidget_update_profile_data', $old_instance, $new_instance );
    }
                       
    /**
     * Output Widget form
     */
    function form( $instance ) {
        iwinstance()->defaults( $instance );
        iwctl()->render_profile_form( $this );
    }
    
    /**
     * Override function to switch to main site for data
     */
    function get_settings(){
        if ( is_multisite() && !is_main_site() )
            switch_to_blog( get_main_site_id() );	   
		$settings = get_option( $this->option_name );

		if ( false === $settings ) {
			if ( isset( $this->alt_option_name ) ) {
				$settings = get_option( $this->alt_option_name );
			} else {
				// Save an option so it can be autoloaded next time.
				$this->save_settings( array() );
			}
		}

		if ( ! is_array( $settings ) && ! ( $settings instanceof ArrayObject || $settings instanceof ArrayIterator ) ) {
			$settings = array();
		}

		if ( ! empty( $settings ) && ! isset( $settings['_multiwidget'] ) ) {
			// Old format, convert if single widget.
			$settings = wp_convert_widget_settings( $this->id_base, $this->option_name, $settings );
		}

		unset( $settings['_multiwidget'], $settings['__i__'] );
        if ( is_multisite() && ms_is_switched() )
            restore_current_blog();  
		return $settings;        
    }

}


