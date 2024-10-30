<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )exit;
/**
 * main.php - Outputs widget form
 *
 * @package IntelliWidgetMain
 * @subpackage includes
 * @author Jason C Fleming
 * @copyright 2014-2015 Lilaea Media LLC
 * @access public
 */
$is_widget = $this->is_widget( $widgetobj );
$unr = 'condset' == $this->objtype || !iwinstance()->get( 'restrict' );
?>
<?php if ( $is_widget ): ?><div class="intelliwidget-form-container"><?php endif; // end is widget ?>
<?php if ( !$is_widget && !empty( $this->copy_id ) ): ?>
<p>
  <label title="<?php echo $this->get_tip( 'nocopy' ); ?>">
    <input id="<?php echo $widgetobj->get_field_id( 'nocopy' ); ?>" name="<?php echo $widgetobj->get_field_name( 'nocopy' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'nocopy' ), 1 ); ?> value="1"/><?php echo $this->get_label( 'nocopy' ); ?>
  </label>
</p>
<?php endif; // end is not widget 
    if ( $unr ): ?>
      <p><label title="<?php echo $this->get_tip( 'nickname' );?>" for="<?php echo $widgetobj->get_field_id( 'nickname' ); ?>"> <?php echo $this->get_label( 'nickname' ); ?>: </label>
    <input id="<?php echo $widgetobj->get_field_id( 'nickname' ); ?>" name="<?php echo $widgetobj->get_field_name( 'nickname' ); ?>" type="text" value="<?php echo esc_attr( iwinstance()->get( 'nickname' ) ); ?>" /></p>
    <?php else: // restrict ?>
    <label><?php _e( 'Type:' ); ?>
        <?php
        foreach ( $this->get_menu( 'content' ) as $value => $label ):
            if ( $value == iwinstance()->get( 'content' ) ):
                echo '<strong>' . $label . '</strong>';
                break;
            endif;
        endforeach; ?>
    </label>
    <?php if ( iwinstance()->get( 'title' ) ): ?>
    <br/><label><?php _e( 'Title:' ); ?>
    <strong><?php echo esc_attr( iwinstance()->get( 'title' ) ); ?></strong></label>
    <?php endif;
        foreach ( array(
            'nickname',
            'content',
            'template',
        ) as $hiddenfield ): ?>
  <input id="<?php echo $widgetobj->get_field_id( $hiddenfield ); ?>" name="<?php echo $widgetobj->get_field_name( $hiddenfield ); ?>" type="hidden" value="<?php echo esc_attr( iwinstance()->get( $hiddenfield ) ); ?>" />
        <?php endforeach;    ?>
    <?php endif; // end restrict ?>
<p>
<?php if ( $is_widget ): 
            include( $this->formpath( 'docslink' ) ); ?>
    <label title="<?php echo $this->get_tip( 'hide_if_empty' ); ?>">
    <input class="iw-widget-control" name="<?php echo $widgetobj->get_field_name( 'hide_if_empty' ); ?>" id="<?php echo $widgetobj->get_field_id( 'hide_if_empty' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'hide_if_empty' ), 1 ); ?> value="1"/><?php echo $this->get_label( 'hide_if_empty' ); ?>  </label>

    <?php 
    /**
     * important note: get_intelliwidgets() will cause the widgets admin to fail because of the way
     * we dereference the widget object (bypassing the customizer manager framework)
     * make sure it is only called on post, term or condset objtype, never when is_widget == true!
     */
    else: ?>
    <input type="hidden" id="<?php echo $widgetobj->get_field_id( 'box_id' ); ?>" name="<?php echo $widgetobj->get_field_name( 'box_id' ); ?>" value="<?php echo $widgetobj->box_id; ?>"/>

    <label title="<?php echo $this->get_tip( 'replace_widget' ); ?>" for="<?php echo $widgetobj->get_field_id( 'box_id' ); ?>">
        <?php echo $this->get_label( 'replace_widget' ); ?>: </label>
    <select name="<?php echo $widgetobj->get_field_name( 'replace_widget' ); ?>" id="<?php echo $widgetobj->get_field_id( 'replace_widget' ); ?>">
        <?php foreach ( $this->get_intelliwidgets() as $value => $label ): ?>
        <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'replace_widget' ), $value ); ?>>
            <?php echo $label; ?>
        </option>
        <?php endforeach; ?>
    </select>
    <?php endif; // end is widget ?>
</p>

<?php 
    if ( 'condset' == $this->objtype ): // add restrict option for singlular condsets ?>
    <label title="<?php echo $this->get_tip( 'restrict' ); ?>">
    <input class="iw-widget-control" name="<?php echo $widgetobj->get_field_name( 'restrict' ); ?>" id="<?php echo $widgetobj->get_field_id( 'restrict' ); ?>" type="checkbox" <?php checked( iwinstance()->get( 'restrict' ), 1 ); ?> value="1"/><?php echo $this->get_label( 'restrict' ); ?>  </label>
<?php else: // condset ?>
  <input id="<?php echo $widgetobj->get_field_id( 'restrict' ); ?>" name="<?php echo $widgetobj->get_field_name( 'restrict' ); ?>" type="hidden" value="<?php echo iwinstance()->get( 'restrict' ); ?>" />
<?php endif; // end condset
if ( !iwinstance()->get( 'hide_if_empty' ) ): ?>
    <?php if ( $unr ): ?>
    <p>
        <label title="<?php echo $this->get_tip( 'content' );?>" for="<?php echo $widgetobj->get_field_id( 'content' ); ?>">
            <?php echo $this->get_label( 'content' ) ?>: </label><br/>
        <select class="iw-<?php echo $is_widget ? 'widget-' : ''; ?>control" id="<?php echo $widgetobj->get_field_id( 'content' ); ?>" name="<?php echo $widgetobj->get_field_name( 'content' ); ?>" autocomplete="off">
            <?php foreach ( $this->get_menu( 'content' ) as $value => $label ): ?>
            <option value="<?php echo $value; ?>" <?php selected( iwinstance()->get( 'content' ), $value ); ?>>
                <?php echo $label; ?>
            </option>
            <?php endforeach; ?>
        </select>
        <?php if ( !$is_widget ): ?><span class="spinner <?php echo $widgetobj->get_field_id( 'spinner' ); ?>"></span>
        <?php endif; // end is widget?>
    </p>
        <?php endif; // end restrict ?>
    <?php if ( 'post_list' == iwinstance()->get( 'content' ) ): ?>
    <?php if ( $unr ): ?>
<p>
    <label title="<?php echo $this->get_tip( 'template' );?>" for="<?php echo $widgetobj->get_field_id( 'template' ); ?>" class="aligned">
        <?php echo $this->get_label( 'template' ); ?>:</label>
    <select name="<?php echo $widgetobj->get_field_name( 'template' ); ?>" id="<?php echo $widgetobj->get_field_id( 'template' ); ?>">
        <?php foreach ( $this->get_widget_templates() as $template => $name ) : ?>
        <option value="<?php echo $template; ?>" <?php selected( iwinstance()->get( 'template' ), $template ); ?>>
            <?php echo $name; ?>
        </option>
        <?php endforeach; ?>
    </select>
</p>

    <?php endif; // end restrict ?>
    <?php endif; // end post list
do_action( 'intelliwidget_form_all_before', $widgetobj );
do_action( 'intelliwidget_form_' . iwinstance()->get( 'content' ), $widgetobj );
do_action( 'intelliwidget_form_all_after', $widgetobj );
endif; // end hide if empty ( placeholder widget )
if ( !$is_widget ): 
    if ( $unr ): ?>
        <span class="submitbox" style="float:left;"><a href="<?php echo $this->get_nonce_url( $widgetobj->post_id, 'delete', $widgetobj->box_id ); ?>" id="iw_delete_<?php echo $widgetobj->post_id . '_' . $widgetobj->box_id; ?>" class="iw-delete submitdelete">
<?php _e( 'Delete', 'intelliwidget' ); ?>
</a></span>
    
<?php endif; // end restrict ?>
        <div class="iw-save-container" style="float:right"><input name="save" class="button button-large iw-save" id="<?php echo $widgetobj->get_field_id( 'save' ); ?>" value="<?php _e( 'Save Settings', 'intelliwidget' ); ?>" type="button" autocomplete="off"/>
        </div>
    <span class="spinner <?php echo $widgetobj->get_field_id( 'spinner' ); ?>"></span>

    <div style="clear:both"></div>
    <?php
endif; // end is widget
if ( $is_widget ): ?>
</div><?php
endif; // end is widget
