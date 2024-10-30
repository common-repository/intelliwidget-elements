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
$this->render_profile_header( $widgetobj, 'gallerysettings' );
endif; // end restrict
?>

<input type="hidden" id="<?php echo $widgetobj->get_field_id( 'page' ); ?>" name="<?php echo $widgetobj->get_field_name( 'page' ); ?>" value="<?php echo implode( ',', ( array ) iwinstance()->get( 'page' ) ); ?>" />
<input type="hidden" id="<?php echo $widgetobj->get_field_id( 'post_type' ); ?>" name="<?php echo $widgetobj->get_field_name( 'post_types' ); ?>[]" value="attachment" />
<input type="hidden" id="<?php echo $widgetobj->get_field_id( 'items' ); ?>" name="<?php echo $widgetobj->get_field_name( 'items' ); ?>" value="0" />
<p>
  <label class="aligned" title="<?php echo $this->get_tip( 'gallery' );?>"><?php echo $this->get_label( 'gallery' );?></label>
  <button type="button" id="<?php echo $widgetobj->get_field_id( 'gallerybtn' ); ?>" class="button intelliwidget-media" ><span class="wp-media-buttons-icon"></span> <?php echo $this->get_label( 'gallerybtn' );?></button>
</p>
<?php if ( $unr ): ?>
<p>
  <label title="<?php echo $this->get_tip( 'captions' );?>">
    <input name="<?php echo $widgetobj->get_field_name( 'captions' ); ?>" id="<?php echo $widgetobj->get_field_id( 'captions' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'captions' ), 1 ); ?> value="1" />
    <?php echo $this->get_label( 'captions' ); ?> </label>
</p>
<p>
  <label title="<?php echo $this->get_tip( 'sortby' );?>" for="<?php echo $widgetobj->get_field_id( 'sortby' ); ?>" class="aligned"> <?php echo $this->get_label( 'sortby' ); ?>: </label>
  <select name="<?php echo $widgetobj->get_field_name( 'sortby' ); ?>" id="<?php echo $widgetobj->get_field_id( 'sortby' ); ?>" class="iw-sortby-menu">
    <?php foreach ( $this->get_menu( 'sortby' ) as $value => $label ): ?>
    <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'sortby' ), $value ); ?>><?php echo $label; ?></option>
    <?php endforeach; ?>
  </select>
  <label class="aligned">&nbsp;</label>
  <select name="<?php echo $widgetobj->get_field_name( 'sortorder' ); ?>" id="<?php echo $widgetobj->get_field_id( 'sortorder' ); ?>" class="iw-sortorder-menu" <?php if ( in_array( iwinstance()->get( 'sortby' ), array( 'selection', 'rand' ) ) ): ?>style="display:none"<?php endif; ?> >
    <option value="ASC"<?php selected( iwinstance()->get( 'sortorder' ), 'ASC' ); ?>>
    <?php _e( 'ASC', 'intelliwidget' ); ?>
    </option>
    <option value="DESC"<?php selected( iwinstance()->get( 'sortorder' ), 'DESC' ); ?>>
    <?php _e( 'DESC', 'intelliwidget' ); ?>
    </option>
  </select>
</p>
<p>
  <label title="<?php echo $this->get_tip( 'link' );?>" for="<?php echo $widgetobj->get_field_id( 'link' ); ?>" class="aligned"> <?php echo $this->get_label( 'link' ); ?>: </label>
  <select name="<?php echo $widgetobj->get_field_name( 'link' ); ?>" id="<?php echo $widgetobj->get_field_id( 'link' ); ?>" class="iw-sortby-menu">
    <?php foreach ( $this->get_menu( 'link' ) as $value => $label ): ?>
    <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'link' ), $value ); ?>><?php echo $label; ?></option>
    <?php endforeach; ?>
  </select>
</p>
<p>
  <label title="<?php echo $this->get_tip( 'image_size' );?>" for="<?php print $widgetobj->get_field_id( 'image_size' ); ?>" class="aligned"> <?php echo $this->get_label( 'image_size' ); ?>: </label>
  <select id="<?php echo $widgetobj->get_field_id( 'image_size' ); ?>" name="<?php echo $widgetobj->get_field_name( 'image_size' ); ?>">
    <?php foreach ( $this->get_menu( 'image_size' ) as $value => $label ): if ( 'none' == $value ) continue;?>
    <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'image_size' ), $value ); ?>><?php echo $label; ?></option>
    <?php endforeach; ?>
  </select>
</p>
<p>
  <label title="<?php echo $this->get_tip( 'tag' );?>" for="<?php echo $widgetobj->get_field_id( 'tag' ); ?>" class="aligned"> <?php echo $this->get_label( 'tag' ); ?>: </label>
  <select name="<?php echo $widgetobj->get_field_name( 'tag' ); ?>" id="<?php echo $widgetobj->get_field_id( 'tag' ); ?>" class="iw-sortby-menu">
    <?php foreach ( $this->get_menu( 'tag' ) as $value => $label ): ?>
    <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'tag' ), $value ); ?>><?php echo $label; ?></option>
    <?php endforeach; ?>
  </select>
</p>
<p>
  <label title="<?php echo $this->get_tip( 'gclass' );?>" for="<?php echo $widgetobj->get_field_id( 'gclass' ); ?>" class="aligned"> <?php echo $this->get_label( 'gclass' ); ?>: </label>
  <input id="<?php echo $widgetobj->get_field_id( 'gclass' ); ?>" name="<?php echo $widgetobj->get_field_name( 'gclass' ); ?>" type="text" value="<?php echo esc_attr( iwinstance()->get( 'gclass' ) ); ?>" />
</p>
<p>
  <label title="<?php echo $this->get_tip( 'iclass' );?>" for="<?php echo $widgetobj->get_field_id( 'iclass' ); ?>" class="aligned"> <?php echo $this->get_label( 'iclass' ); ?>: </label>
  <input id="<?php echo $widgetobj->get_field_id( 'iclass' ); ?>" name="<?php echo $widgetobj->get_field_name( 'iclass' ); ?>" type="text" value="<?php echo esc_attr( iwinstance()->get( 'iclass' ) ); ?>" />
</p>
    <label title="<?php echo $this->get_tip( 'hide_no_posts' );?>"> <?php echo $this->get_label( 'hide_no_posts' ); ?>: </label>
    <textarea name="<?php echo $widgetobj->get_field_name( 'hide_no_posts' ); ?>" id="<?php echo $widgetobj->get_field_id( 'hide_no_posts' ); ?>">
<?php echo esc_textarea( iwinstance()->get( 'hide_no_posts' ) ); ?>
</textarea>
<?php else: // restrict 
        foreach ( array(
            'hide_no_posts',
            'captions',
            'sortby',
            'sortorder',
            'link',
            'image_size',
            'tag',
            'gclass',
            'iclass',
        ) as $hiddenfield ): ?>
  <input id="<?php echo $widgetobj->get_field_id( $hiddenfield ); ?>" name="<?php echo $widgetobj->get_field_name( $hiddenfield ); ?>" type="hidden" value="<?php echo esc_attr( iwinstance()->get( $hiddenfield ) ); ?>" />
        <?php endforeach;
endif; // end restrict ?>
<div id="<?php echo $widgetobj->get_field_id( 'menus' ); ?>">
<?php 
    do_action( 'intelliwidget_post_selection_menus', $widgetobj ); ?>
</div>
<?php if ( $unr ):
$this->render_profile_footer();
endif; // end restrict





