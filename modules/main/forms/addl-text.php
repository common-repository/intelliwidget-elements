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
$this->render_profile_header( $widgetobj, 'addltext' );
//endif; // end restrict
?>
    <p>
      <label title="<?php echo $this->get_tip( 'text_position' );?>" for="<?php echo $widgetobj->get_field_id( 'text_position' ); ?>">
        <?php echo $this->get_label( 'text_position' ); ?>: </label>
      <select name="<?php echo $widgetobj->get_field_name( 'text_position' ); ?>" id="<?php echo $widgetobj->get_field_id( 'text_position' ); ?>">
        <?php foreach ( $this->get_menu( 'text_position' ) as $value => $label ): ?>
        <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'text_position' ), $value ); ?>><?php echo $label; ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    <p><button type="button" id="insert_media_<?php echo $widgetobj->get_field_id( 'custom_text' ); ?>" class="button insert-media add_media button-small" data-editor="<?php echo $widgetobj->get_field_id( 'custom_text' ); ?>">Add Media</button>
      <textarea class="widefat" rows="3" cols="20" id="<?php echo $widgetobj->get_field_id( 'custom_text' ); ?>" 
name="<?php echo $widgetobj->get_field_name( 'custom_text' ); ?>"><?php echo esc_textarea( iwinstance()->get( 'custom_text' ) ); ?></textarea>
    </p>
<?php if ( $unr ): ?>
    <p>
      <label title="<?php echo $this->get_tip( 'filter' );?>">
        <input id="<?php echo $widgetobj->get_field_id( 'filter' ); ?>" name="<?php echo $widgetobj->get_field_name( 'filter' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'filter' ), 1 ); ?> value="1" />
        &nbsp;
        <?php echo $this->get_label( 'filter' ); ?>
      </label>
    </p>
<?php else: // restrict ?>
  <input id="<?php echo $widgetobj->get_field_id( 'filter' ); ?>" name="<?php echo $widgetobj->get_field_name( 'filter' ); ?>" type="hidden" value="<?php echo esc_attr( iwinstance()->get( 'filter' ) ); ?>" />
<?php endif; // end restrict; ?>
<?php //if ( $unr ):
$this->render_profile_footer();
//endif; // end restrict
