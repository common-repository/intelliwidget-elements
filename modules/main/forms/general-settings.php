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
 */
$unr = 'condset' == $this->objtype || !iwinstance()->get( 'restrict' );
//if ( $unr ):
$this->render_profile_header( $widgetobj, 'generalsettings' );
//endif; // end restrict
?>
<?php //if ( $unr ): ?>

    <p>
      <label title="<?php echo $this->get_tip( 'title' );?>" for="<?php echo $widgetobj->get_field_id( 'title' ); ?>"> <?php echo $this->get_label( 'title' ); ?>: </label><br/>
      <input id="<?php echo $widgetobj->get_field_id( 'title' ); ?>" name="<?php echo $widgetobj->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( iwinstance()->get( 'title' ) ); ?>" /><br/>
      <label title="<?php echo $this->get_tip( 'hide_title' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'hide_title' ); ?>" id="<?php echo $widgetobj->get_field_id( 'hide_title' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'hide_title' ), 1 ); ?> value="1" /><?php echo $this->get_label( 'hide_title' ); ?>
      </label>
    </p>
<?php if ( $unr ): ?>
    <p>
      <label title="<?php echo $this->get_tip( 'container_id' );?>" for="<?php echo $widgetobj->get_field_id( 'container_id' ); ?>">
        <?php echo $this->get_label( 'container_id' ); ?>: </label><br/>
      <input name="<?php echo $widgetobj->get_field_name( 'container_id' ); ?>" id="<?php echo $widgetobj->get_field_id( 'container_id' ); ?>" type="text" value="<?php echo esc_attr( iwinstance()->get( 'container_id' ) ); ?>" />
    </p><?php else: // restrict 
        foreach ( array(
            'container_id',
        ) as $hiddenfield ): ?>
  <input id="<?php echo $widgetobj->get_field_id( $hiddenfield ); ?>" name="<?php echo $widgetobj->get_field_name( $hiddenfield ); ?>" type="hidden" value="<?php echo esc_attr( iwinstance()->get( $hiddenfield ) ); ?>" />
        <?php endforeach;
        endif; // end restrict ?>
    <p>
      <label title="<?php echo $this->get_tip( 'classes' );?>" for="<?php echo $widgetobj->get_field_id( 'classes' ); ?>">
        <?php echo $this->get_label( 'classes' ); ?>: </label><br/>
      <input name="<?php echo $widgetobj->get_field_name( 'classes' ); ?>" id="<?php echo $widgetobj->get_field_id( 'classes' ); ?>" type="text" value="<?php echo esc_attr( iwinstance()->get( 'classes' ) ); ?>" />
    </p>
<p>
      <label title="<?php echo $this->get_tip( 'nav_menu_classes' );?>" for="<?php echo $widgetobj->get_field_id( 'nav_menu_classes' ); ?>" class="aligned">
        <?php echo $this->get_label( 'nav_menu_classes' ); ?>: </label>
      <input id="<?php echo $widgetobj->get_field_id( 'nav_menu_classes' ); ?>" name="<?php echo $widgetobj->get_field_name( 'nav_menu_classes' ); ?>" size="12" type="text" value="<?php echo esc_attr( iwinstance()->get( 'nav_menu_classes' ) ); ?>" />
</p>
<?php if ( is_multisite() ): ?>
<?php if ( $unr ): ?>
    <p>
    <label title="<?php echo $this->get_tip( 'site_id' );?>" for="<?php echo $widgetobj->get_field_id( 'site_id' ); ?>" >
        <?php echo $this->get_label( 'site_id' ); ?>:</label>
    <select name="<?php echo $widgetobj->get_field_name( 'site_id' ); ?>" id="<?php echo $widgetobj->get_field_id( 'site_id' ); ?>">
        <option value="all" <?php selected( iwinstance()->get( 'site_id' ), 'all' ); ?>><?php _e( 'All', 'intelliwidget'); ?></option>
        <option value="current" <?php selected( iwinstance()->get( 'site_id' ), 'current' ); ?>><?php _e( 'Current Site', 'intelliwidget'); ?></option>
        <?php foreach ( IntelliWidgetMainCore::get_sites() as $site_id => $name ) : ?>
        <option value="<?php echo $site_id; ?>" <?php selected( iwinstance()->get( 'site_id' ), $site_id ); ?>>
            <?php echo $name; ?>
        </option>
        <?php endforeach; ?>
    </select>
    </p>
<?php else: // restrict ?>
      <input name="<?php echo $widgetobj->get_field_name( 'site_id' ); ?>" id="<?php echo $widgetobj->get_field_id( 'site_id' ); ?>" type="hidden" value="<?php echo esc_attr( iwinstance()->get( 'site_id' ) ); ?>" />
<?php endif; // end restrict ?>
<?php endif; // end multisite ?><p>
<?php //if ( $unr ): 
$this->render_profile_footer(); 
//endif; // end restrict 
