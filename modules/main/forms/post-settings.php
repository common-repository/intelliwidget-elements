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

//if ( $unr ): -- we need the header to begin closed to trigger the multiselect plugin
$this->render_profile_header( $widgetobj, 'selection' );
//endif; // end restrict
?><span class="spinner <?php echo $widgetobj->get_field_id( 'selectionspinner' ); ?>"></span>
<?php if ( $unr ): ?>
    <p>
      <label title="<?php echo $this->get_tip( 'post_types' );?>" style="display:block">
        <?php echo $this->get_label( 'post_types' ); ?>:</label>
      <?php foreach ( IntelliWidgetMainCore::get_post_types() as $type ) : ?>
      <label style="white-space:nowrap;margin-right:10px" for="<?php echo $widgetobj->get_field_id( 'post_types_' . $type ); ?>">
        <input class="iw-<?php echo $this->is_widget( $widgetobj )? 'widget-' : ''; ?>control"  type="checkbox" id="<?php echo $widgetobj->get_field_id( 'post_types_' . $type ); ?>" name="<?php echo $widgetobj->get_field_name( 'post_types' ); ?>[]" value="<?php echo $type; ?>" <?php checked( in_array( $type, iwinstance()->get( 'post_types' ) ), 1 ); ?> />
        <?php echo ucfirst( $type ); ?></label>
      <?php endforeach; ?>
    </p>
<?php else: // restrict 
foreach ( IntelliWidgetMainCore::get_post_types() as $type ) : 
    if ( in_array( $type, iwinstance()->get( 'post_types' ) ) ): ?>
        <input type="hidden" id="<?php echo $widgetobj->get_field_id( 'post_types_' . $type ); ?>" name="<?php echo $widgetobj->get_field_name( 'post_types' ); ?>[]" value="<?php echo $type; ?>" />
<?php endif;
endforeach; ?>


<?php endif; // end restrict ?>
<strong><?php _e( 'Manual Edit', 'intelliwidget' );?> <?php echo ( !iwinstance()->get( 'listdata' ) ) ? __( 'OFF', 'intelliwidget' ) : __( 'ACTIVE', 'intelliwidget' ); ?></strong><br/>
<button title="<?php echo $this->get_tip( 'listdatabtn' );?>" type="button" id="<?php echo $widgetobj->get_field_id( 'listdatabtn' ); ?>" class="button intelliwidget-listdata" ><span class="dashicons dashicons-randomize" style="margin-top:2px"></span> <?php echo $this->get_label( 'listdatabtn' );?></button>
<button title="<?php echo $this->get_tip( 'clearlistdatabtn' );?>" type="button" id="<?php echo $widgetobj->get_field_id( 'clearlistdatabtn' ); ?>" class="button intelliwidget-clear-listdata" ><span class="dashicons dashicons-no" style="margin-top:2px"></span> <?php echo $this->get_label( 'clearlistdatabtn' );?></button>
<input class="iw-<?php echo $this->is_widget( $widgetobj )? 'widget-' : ''; ?>control" type="hidden" id="<?php echo $widgetobj->get_field_id( 'listdata' ); ?>" name="<?php echo $widgetobj->get_field_name( 'listdata' ); ?>" 
       value="<?php echo esc_attr( htmlentities( iwinstance()->get( 'listdata' ) ) ); ?>" />
<div id="<?php echo $widgetobj->get_field_id( 'menus' ); ?>">
<?php do_action( 'intelliwidget_post_selection_menus', $widgetobj ); ?>
    </div>
<?php if ( $unr ): ?>
<p>
  <label title="<?php echo $this->get_tip( 'items' );?>" for="<?php echo $widgetobj->get_field_id( 'items' ); ?>" class="aligned"> <?php echo $this->get_label( 'items' ); ?>: </label>
  <input id="<?php echo $widgetobj->get_field_id( 'items' ); ?>" name="<?php echo $widgetobj->get_field_name( 'items' ); ?>" size="3" type="text" value="<?php echo esc_attr( iwinstance()->get( 'items' ) ); ?>" />
</p>
        <p>
  <label title="<?php echo $this->get_tip( 'sortby' );?>" for="<?php echo $widgetobj->get_field_id( 'sortby' ); ?>" class="aligned"> <?php echo $this->get_label( 'sortby' ); ?>: </label>
  <select name="<?php echo $widgetobj->get_field_name( 'sortby' ); ?>" id="<?php echo $widgetobj->get_field_id( 'sortby' ); ?>" class="iw-sortby-menu">
    <?php foreach ( $this->get_menu( 'sortby' ) as $value => $label ): if ( 'selection' == $value ) continue; ?>
    <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'sortby' ), $value ); ?>><?php echo $label; ?></option>
    <?php endforeach; ?>
  </select>
            
  <label class="aligned">&nbsp;</label>
  <select name="<?php echo $widgetobj->get_field_name( 'sortorder' ); ?>" id="<?php echo $widgetobj->get_field_id( 'sortorder' ); ?>" class="iw-sortorder-menu">
    <option value="ASC"<?php selected( iwinstance()->get( 'sortorder' ), 'ASC' ); ?>>
    <?php _e( 'ASC', 'intelliwidget' ); ?>
    </option>
    <option value="DESC"<?php selected( iwinstance()->get( 'sortorder' ), 'DESC' ); ?>>
    <?php _e( 'DESC', 'intelliwidget' ); ?>
    </option>
  </select>
</p>

<div style="columns:2">
  <label style="display:block;padding-bottom:.5em;-webkit-column-break-inside: avoid; page-break-inside: avoid; break-inside: avoid;" title="<?php echo $this->get_tip( 'keep_title' );?>">
    <input name="<?php echo $widgetobj->get_field_name( 'keep_title' ); ?>" id="<?php echo $widgetobj->get_field_id( 'keep_title' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'keep_title' ), 1 ); ?> value="1" />
    <?php echo $this->get_label( 'keep_title' ); ?> </label>

    <label style="display:block;padding-bottom:.5em;-webkit-column-break-inside: avoid; page-break-inside: avoid; break-inside: avoid;" title="<?php echo $this->get_tip( 'daily' );?>">
    <input name="<?php echo $widgetobj->get_field_name( 'daily' ); ?>" id="<?php echo $widgetobj->get_field_id( 'daily' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'daily' ), 1 ); ?> value="1" />
    <?php echo $this->get_label( 'daily' ); ?> </label>

    <label style="display:block;padding-bottom:.5em;-webkit-column-break-inside: avoid; page-break-inside: avoid; break-inside: avoid;" title="<?php echo $this->get_tip( 'related' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'related' ); ?>" id="<?php echo $widgetobj->get_field_id( 'related' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'related' ), 1 ); ?> value="1" />
        <?php echo $this->get_label( 'related' ); ?>
      </label>


    <label style="display:block;padding-bottom:.5em;-webkit-column-break-inside: avoid; page-break-inside: avoid; break-inside: avoid;" title="<?php echo $this->get_tip( 'same_term' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'same_term' ); ?>" id="<?php echo $widgetobj->get_field_id( 'same_term' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'same_term' ), 1 ); ?> value="1" />
        <?php echo $this->get_label( 'same_term' ); ?>
      </label>

    <label style="display:block;padding-bottom:.5em;-webkit-column-break-inside: avoid; page-break-inside: avoid; break-inside: avoid;" title="<?php echo $this->get_tip( 'child_pages' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'child_pages' ); ?>" id="<?php echo $widgetobj->get_field_id( 'child_pages' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'child_pages' ), 1 ); ?> value="1" />
        <?php echo $this->get_label( 'child_pages' ); ?>
      </label>
<?php if ( current_user_can( 'read_private_posts' ) ): ?>
      <label style="display:block;padding-bottom:.5em;-webkit-column-break-inside: avoid; page-break-inside: avoid; break-inside: avoid;" title="<?php echo $this->get_tip( 'include_private' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'include_private' ); ?>" id="<?php echo $widgetobj->get_field_id( 'include_private' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'include_private' ), 1 ); ?> value="1" />
          <?php echo $this->get_label( 'include_private' ); ?>
      </label>
<?php endif; ?>
      <label style="display:block;padding-bottom:.5em;-webkit-column-break-inside: avoid; page-break-inside: avoid; break-inside: avoid;" title="<?php echo $this->get_tip( 'skip_post' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'skip_post' ); ?>" id="<?php echo $widgetobj->get_field_id( 'skip_post' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'skip_post' ), 1 ); ?> value="1" />
          <?php echo $this->get_label( 'skip_post' ); ?>
      </label>
<?php if ( is_multisite() ): // only using this on multisite for now ?>
      <label style="display:block;padding-bottom:.5em;-webkit-column-break-inside: avoid; page-break-inside: avoid; break-inside: avoid;" title="<?php echo $this->get_tip( 'paged' );?>">
        <input name="<?php echo $widgetobj->get_field_name( 'paged' ); ?>" id="<?php echo $widgetobj->get_field_id( 'paged' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'paged' ), 1 ); ?> value="1" />
          <?php echo $this->get_label( 'paged' ); ?>
      </label>
<?php endif; ?>
</div>

    <label title="<?php echo $this->get_tip( 'hide_no_posts' );?>"> <?php echo $this->get_label( 'hide_no_posts' ); ?>: </label>
    <textarea name="<?php echo $widgetobj->get_field_name( 'hide_no_posts' ); ?>" id="<?php echo $widgetobj->get_field_id( 'hide_no_posts' ); ?>">
<?php echo esc_textarea( iwinstance()->get( 'hide_no_posts' ) ); ?>
</textarea>

<div><h5>Include to 3 meta conditions:</h5><?php foreach( array( '1', '2', '3' ) as $metasuffix ): ?>
    <p>
      <label title="<?php echo $this->get_tip( 'metak' );?>" for="<?php echo $widgetobj->get_field_id( 'metak' . $metasuffix ); ?>" class="aligned">
        <?php echo $this->get_label( 'metak' ); ?>: </label>
      <input id="<?php echo $widgetobj->get_field_id( 'metak' . $metasuffix ); ?>" name="<?php echo $widgetobj->get_field_name( 'metak' . $metasuffix ); ?>" size="7" type="text" value="<?php echo esc_attr( iwinstance()->get( 'metak' . $metasuffix ) ); ?>" />
        <select name="<?php echo $widgetobj->get_field_name( 'metac' . $metasuffix ); ?>" id="<?php echo $widgetobj->get_field_id( 'metac' . $metasuffix ); ?>">
        <?php foreach ( $this->get_menu( 'meta_cond' ) as $value => $label ): ?>
        <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'metac' . $metasuffix ), $value ); ?>><?php echo $label; ?></option>
        <?php endforeach; ?>
      </select>
      <label title="<?php echo $this->get_tip( 'metav' );?>" for="<?php echo $widgetobj->get_field_id( 'metav' . $metasuffix ); ?>" class="aligned">
        <?php echo $this->get_label( 'metav' ); ?>: </label>
      <input id="<?php echo $widgetobj->get_field_id( 'metav' . $metasuffix ); ?>" name="<?php echo $widgetobj->get_field_name( 'metav' . $metasuffix ); ?>" size="7" type="text" value="<?php echo esc_attr( iwinstance()->get( 'metav' . $metasuffix ) ); ?>" />
    </p><?php endforeach; ?>

</div>

<?php else: //restrict 
        foreach ( array(
            'metak',
            'metac',
            'metav',
            'sortby',
            'sortorder',
            'items',
            'keep_title',
            'daily',
            'related',
            'hide_no_posts',
            'same_term',
            'child_pages',
            'include_private',
            'future_only',
            'active_only',
            'skip_expired',
            'skip_post',
        ) as $hiddenfield ): ?>
  <input id="<?php echo $widgetobj->get_field_id( $hiddenfield ); ?>" name="<?php echo $widgetobj->get_field_name( $hiddenfield ); ?>" type="hidden" value="<?php echo esc_attr( iwinstance()->get( $hiddenfield ) ); ?>" />
        <?php endforeach;
    endif; // end restrict
    // hidden input field with timestamp forces customizer to update widget form 
    if ( $this->is_widget( $widgetobj ) ):
        $time = 'iw' . time(); ?><input type="hidden" name="<?php echo $widgetobj->get_field_name( $time ); ?>" value="" id="<? echo $widgetobj->get_field_id( $time ); ?>" /><?php
    endif; ?>
<?php //if ( $unr ): -- closing divs because we are using header
    $this->render_profile_footer();
//endif; // end restrict; 
