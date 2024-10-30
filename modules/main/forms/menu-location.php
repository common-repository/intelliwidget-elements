<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

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
        <?php endforeach;?>
  </select>
</p>