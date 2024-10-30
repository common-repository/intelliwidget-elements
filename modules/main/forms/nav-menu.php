<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * class-intelliwidget-form.php - Outputs widget form
 *
 * @package IntelliWidgetMain
 * @subpackage includes
 * @author Jason C Fleming
 * @copyright 2014-2015 Lilaea Media LLC
 * @access public
 */
$unr = 'condset' == $this->objtype || !iwinstance()->get( 'restrict' );
if ( $unr ):
$this->render_profile_header( $widgetobj, 'navmenusettings' );
if ( !$this->is_widget( $widgetobj ) ): // only show menu locations on post profiles
?><p>
  <label title="<?php echo $this->get_tip( 'menu_location' );?>" for="<?php echo $widgetobj->get_field_id( 'menu_location' ); ?>">
    <?php echo $this->get_label( 'menu_location' ); ?>
    : </label>
  <select id="<?php echo $widgetobj->get_field_id( 'menu_location' ); ?>" name="<?php echo $widgetobj->get_field_name( 'menu_location' ); ?>">
        <option value=""><?php _e( 'Use Widget Location', 'chld_thm_cfg_plugins' ); ?></option>
    <?php

    // Get menu locations
    foreach ( get_registered_nav_menus() as $value => $label ): ?>
        <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'menu_location' ), $value ); ?>><?php echo $label; ?></option>
    <?php 
    endforeach;?>
  </select>
</p><?php 
endif;
else: ?>
  <input type="hidden" id="<?php echo $widgetobj->get_field_id( 'menu_location' ); ?>"
         name="<?php echo $widgetobj->get_field_name( 'menu_location' ); ?>"
         value="<?php echo esc_attr( iwinstance()->get( 'menu_location' ) ); ?>" />
<?php endif; // end restrict
?>
<p>
  <label title="<?php echo $this->get_tip( 'nav_menu' );?>" for="<?php echo $widgetobj->get_field_id( 'nav_menu' ); ?>">
    <?php echo $this->get_label( 'nav_menu' ); ?>: </label>
  <select id="<?php echo $widgetobj->get_field_id( 'nav_menu' ); ?>" name="<?php echo $widgetobj->get_field_name( 'nav_menu' ); ?>">
    <?php
    // Get menus
    foreach ( $this->get_nav_menus() as $value => $label ): ?>
        <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'nav_menu' ), $value ); ?>><?php echo $label; ?></option>
    <?php 
    endforeach;?>
  </select>
</p>
<?php if ( $unr ):
$this->render_profile_footer();
endif; //end restrict
