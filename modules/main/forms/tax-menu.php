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
$this->render_profile_header( $widgetobj, 'taxmenusettings' );
endif; // end restrict
if ( $unr ):
?>
    <p>
      <label title="<?php echo $this->get_tip( 'taxonomy' );?>" for="<?php echo $widgetobj->get_field_id( 'taxonomy' ); ?>"> <?php echo $this->get_label( 'taxonomy' ); ?>: </label>
      <select id="<?php echo $widgetobj->get_field_id( 'taxonomy' ); ?>" name="<?php echo $widgetobj->get_field_name( 'taxonomy' ); ?>">
        <?php
            // Get menus
            foreach ( $this->get_tax_menu() as $value => $label ): ?>
        <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'taxonomy' ), $value ); ?>><?php echo $label; ?></option>
        <?php endforeach;?>
      </select>
    </p>
    <p>
      <label title="<?php echo $this->get_tip( 'sortby_terms' ); ?>"> <?php echo $this->get_label( 'sortby_terms' ); ?>: </label>
      <br/>
      <select name="<?php echo $widgetobj->get_field_name( 'sortby' ); ?>" id="<?php echo $widgetobj->get_field_id( 'sortby' ); ?>">
        <?php foreach ( $this->get_menu( 'tax_sortby' ) as $value => $label ): ?>
        <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'sortby' ), $value ); ?>><?php echo $label; ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    <p>
      <label title="<?php echo $this->get_tip( 'show_count' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'show_count' ); ?>" id="<?php echo $widgetobj->get_field_id( 'show_count' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'show_count' ), 1 ); ?> value="1" />
        &nbsp; <?php echo $this->get_label( 'show_count' ); ?> </label>
    </p>
    <p>
      <label title="<?php echo $this->get_tip( 'hierarchical' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'hierarchical' ); ?>" id="<?php echo $widgetobj->get_field_id( 'hierarchical' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'hierarchical' ), 1 ); ?> value="1" />
        &nbsp; <?php echo $this->get_label( 'hierarchical' ); ?> </label>
    </p>
    <p>
      <label title="<?php echo $this->get_tip( 'current_only_all' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'current_only' ); ?>" id="<?php echo $widgetobj->get_field_id( 'current_only_all' ); ?>" type="radio" <?php checked( iwinstance()->get( 'current_only' ), 0 ); ?> value="0" />
        &nbsp; <?php echo $this->get_label( 'current_only_all' ); ?> </label><br/>
      <label title="<?php echo $this->get_tip( 'current_only_cur' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'current_only' ); ?>" id="<?php echo $widgetobj->get_field_id( 'current_only_cur' ); ?>" type="radio" <?php checked( iwinstance()->get( 'current_only' ), 1 ); ?> value="1" />
        &nbsp; <?php echo $this->get_label( 'current_only_cur' ); ?> </label><br/>
      <label title="<?php echo $this->get_tip( 'current_only_sub' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'current_only' ); ?>" id="<?php echo $widgetobj->get_field_id( 'current_only_sub' ); ?>" type="radio" <?php checked( iwinstance()->get( 'current_only' ), 2 ); ?> value="2" />
        &nbsp; <?php echo $this->get_label( 'current_only_sub' ); ?> </label>
    </p>
    <p>
      <label title="<?php echo $this->get_tip( 'show_descr' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'show_descr' ); ?>" id="<?php echo $widgetobj->get_field_id( 'show_descr' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'show_descr' ), 1 ); ?> value="1" />
        &nbsp; <?php echo $this->get_label( 'show_descr' ); ?> </label>
    </p>
    <p>
      <label title="<?php echo $this->get_tip( 'hide_empty' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'hide_empty' ); ?>" id="<?php echo $widgetobj->get_field_id( 'hide_empty' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'hide_empty' ), 1 ); ?> value="1" />
        &nbsp; <?php echo $this->get_label( 'hide_empty' ); ?> </label>
    </p>
<?php else: // restrict 
    foreach ( array(
        'taxonomy',
        'sortby',
        'show_count',
        'hierarchical',
        'current_only',
        'show_descr',
        'hide_empty',
    ) as $hiddenfield ): ?>
  <input id="<?php echo $widgetobj->get_field_id( $hiddenfield ); ?>" name="<?php echo $widgetobj->get_field_name( $hiddenfield ); ?>" type="hidden" value="<?php echo esc_attr( iwinstance()->get( $hiddenfield ) ); ?>" />
    <?php endforeach;
endif; // end restrict ?>
<?php if ( $unr ):
$this->render_profile_footer();
endif; // end restrict;



