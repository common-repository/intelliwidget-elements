<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * class-intelliwidget-profile.php
 *
 * @package IntelliWidgetMain
 * @subpackage includes
 * @author Jason C Fleming
 * @copyright 2014-2015 Lilaea Media LLC
 * @access public
 * 
 * The Profile Class is the equivalent of a Widget Class, but in the context of post types.
 * It is passed to the Controller to build a form interface ( admin ) or the rendered output ( front-end ).
 */
 
class IntelliWidgetMainProfile {

    var $post_id;
    var $box_id;
    var $title;
    
    function __construct( $post_id, $box_id ) {
        $this->post_id  = $post_id;
        $this->box_id   = $box_id;
    }
       
    function get_field_id( $field_name ) {
        return 'intelliwidget_' . $this->post_id . '_' . $this->box_id . '_' . $field_name;
    }
    
    function get_field_name( $field_name ) {
        return 'intelliwidget_' . $this->box_id . '_' . $field_name;
    }

    function set_title( $title ) {
        $this->title = $title;
    }
    
    function get_tab( $nickname = '' ) {
        return apply_filters( 'intelliwidget_tab', '<li id="iw_tab_' . $this->post_id . '_' . $this->box_id . '" class="iw-tab">
        <a href="#iw_profile_' . $this->post_id . '_' . $this->box_id . '" title="' . esc_attr( $this->title ) . '"><small>' . ( $nickname ? $nickname . ' (' . $this->box_id . ')' : $this->box_id ) . '</small></a></li>', $this->post_id, $this->box_id );
    }

    function begin_profile() {
        return apply_filters( 'intelliwidget_begin_profile', '<div id="iw_profile_' . $this->post_id . '_' . $this->box_id . '" class="iw-profile">' );
    }
    
    function end_profile() {
        return apply_filters( 'intelliwidget_end_profile', '</div>' );
    }    
}

