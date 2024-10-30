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
if ( $unr ):
$this->render_profile_header( $widgetobj, 'appearance' );
endif; // end restrict
if ( $unr ): ?>
<p>
  <label title="<?php echo $this->get_tip( 'length' );?>" for="<?php echo $widgetobj->get_field_id( 'length' ); ?>" class="aligned"> <?php echo $this->get_label( 'length' ); ?>: </label>
  <input id="<?php echo $widgetobj->get_field_id( 'length' ); ?>" name="<?php echo $widgetobj->get_field_name( 'length' ); ?>" size="3" type="text" value="<?php echo esc_attr( iwinstance()->get( 'length' ) ); ?>" />
</p>
<p>
  <label title="<?php echo $this->get_tip( 'allowed_tags' );?>" for="<?php echo $widgetobj->get_field_id( 'allowed_tags' ); ?>" class="aligned"> <?php echo $this->get_label( 'allowed_tags' ); ?>: </label>
  <input name="<?php echo $widgetobj->get_field_name( 'allowed_tags' ); ?>" id="<?php echo $widgetobj->get_field_id( 'allowed_tags' ); ?>" type="text" value="<?php echo esc_attr( iwinstance()->get( 'allowed_tags' ) ); ?>" />
</p>
<p>
  <label title="<?php echo $this->get_tip( 'link_text' );?>" for="<?php echo $widgetobj->get_field_id( 'link_text' ); ?>" class="aligned"> <?php echo $this->get_label( 'link_text' ); ?>: </label>
  <input name="<?php echo $widgetobj->get_field_name( 'link_text' ); ?>" id="<?php echo $widgetobj->get_field_id( 'link_text' ); ?>" type="text" value="<?php echo esc_attr( iwinstance()->get( 'link_text' ) ); ?>" />
</p>
<p>
  <label title="<?php echo $this->get_tip( 'imagealign' );?>" for="<?php print $widgetobj->get_field_id( 'imagealign' ); ?>" class="aligned"> <?php echo $this->get_label( 'imagealign' ); ?>: </label>
  <select name="<?php print $widgetobj->get_field_name( 'imagealign' ); ?>" id="<?php print $widgetobj->get_field_id( 'imagealign' ); ?>">
    <?php foreach ( $this->get_menu( 'imagealign' ) as $value => $label ): ?>
    <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'imagealign' ), $value ); ?>><?php echo $label; ?></option>
    <?php endforeach; ?>
  </select>
</p>
<p>
  <label title="<?php echo $this->get_tip( 'image_size' );?>" for="<?php print $widgetobj->get_field_id( 'image_size' ); ?>" class="aligned"> <?php echo $this->get_label( 'image_size' ); ?>: </label>
  <select id="<?php echo $widgetobj->get_field_id( 'image_size' ); ?>" name="<?php echo $widgetobj->get_field_name( 'image_size' ); ?>">
    <?php foreach ( $this->get_menu( 'image_size' ) as $value => $label ): ?>
    <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'image_size' ), $value ); ?>><?php echo $label; ?></option>
    <?php endforeach; ?>
  </select>
</p>
<p>
  <label title="<?php echo $this->get_tip( 'no_img_links' );?>">
    <input name="<?php echo $widgetobj->get_field_name( 'no_img_links' ); ?>" id="<?php echo $widgetobj->get_field_id( 'no_img_links' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'no_img_links' ), 1 ); ?> value="1" />
    <?php echo $this->get_label( 'no_img_links' ); ?> </label>
</p>
<?php 
else: // restrict 
        foreach ( array(
            'length',
            'allowed_tags',
            'link_text',
            'imagealign',
            'image_size',
            'no_img_links',
        ) as $hiddenfield ): ?>
  <input id="<?php echo $widgetobj->get_field_id( $hiddenfield ); ?>" name="<?php echo $widgetobj->get_field_name( $hiddenfield ); ?>" type="hidden" value="<?php echo esc_attr( iwinstance()->get( $hiddenfield ) ); ?>" />
        <?php endforeach;
endif; // end restrict; 
if ( $unr ):
    $this->render_profile_footer();
endif; // end restrict

