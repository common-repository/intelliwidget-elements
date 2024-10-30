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
$custom_data = get_post_custom( $post->ID );
$fields = array();
foreach ( $this->get_fields( 'custom' ) as $field ):
    $key = 'intelliwidget_' . $field;
    $fields[ $key ] = empty( $custom_data[ $key ] ) ? '' : $custom_data[ $key ][ 0 ];
endforeach;
?>

<p>
  <label title="<?php echo $this->get_tip( 'alt_title' );?>" for="intelliwidget_alt_title"> <?php echo $this->get_label( 'alt_title' );?>:</label>
  <input class="intelliwidget-input" type="text" id="intelliwidget_alt_title" name="intelliwidget_alt_title" value="<?php echo $fields[ 'intelliwidget_alt_title' ] ?>" autocomplete="off" />
      <label style="clear:both;text-align:right;display:block" title="<?php echo $this->get_tip( 'all_titles' );?>">
        <input style="width:auto;float:none" class="intelliwidget-input" name="intelliwidget_all_titles" id="intelliwidget_all_titles" type="checkbox" <?php checked( $fields[ 'intelliwidget_all_titles' ], 1 ); ?> value="1" autocomplete="off" /><?php echo $this->get_label( 'all_titles' ); ?>
      </label>
</p>
<p>
  <label title="<?php echo $this->get_tip( 'external_url' );?>" for="intelliwidget_external_url"> <?php echo $this->get_label( 'external_url' );?>:</label>
  <input class="intelliwidget-input" type="text" id="intelliwidget_external_url" name="intelliwidget_external_url" value="<?php echo $fields[ 'intelliwidget_external_url' ] ?>" autocomplete="off" />
      <label style="clear:both;text-align:right;display:block" title="<?php echo $this->get_tip( 'all_links' );?>">
        <input style="width:auto;float:none" class="intelliwidget-input" name="intelliwidget_all_links" id="intelliwidget_all_links" type="checkbox" <?php checked( $fields[ 'intelliwidget_all_links' ], 1 ); ?> value="1" autocomplete="off" /><?php echo $this->get_label( 'all_links' ); ?>
      </label>
</p>
<p>
  <label title="<?php echo $this->get_tip( 'link_classes' );?>" for="intelliwidget_link_classes"> <?php echo $this->get_label( 'link_classes' );?>:</label>
  <input class="intelliwidget-input" type="text" id="intelliwidget_link_classes" name="intelliwidget_link_classes" value="<?php echo $fields[ 'intelliwidget_link_classes' ] ?>" autocomplete="off" />
</p>
<p>
  <label title="<?php echo $this->get_tip( 'link_target' );?>" for="intelliwidget_link_target"> <?php echo $this->get_label( 'link_target' );?>:</label>
  <select class="intelliwidget-input" id="intelliwidget_link_target" name="intelliwidget_link_target" autocomplete="off" >
    <?php foreach ( $this->get_menu( 'link_target' ) as $value => $label ): ?>
    <option value="<?php echo $value; ?>" <?php selected( $fields[ 'intelliwidget_link_target' ], $value ); ?>><?php echo $label; ?></option>
    <?php endforeach; ?>
  </select>
</p>
<div class="iw-cdf-container">
  <input name="save" class="iw-cdfsave button button-large" id="iw_cdfsave" value="<?php _e( 'Save Custom Fields', 'intelliwidget' );?>" type="button" style="float:right" />
  <span class="spinner" id="intelliwidget_cpt_spinner"></span> </div>
<?php wp_nonce_field( 'iwpage_' . $post->ID, 'iwpage_' . $post->ID ); ?>
<div style="clear:both"></div>
