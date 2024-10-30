<div class="iw_conditions">
  <div class="iw-collapsible postbox">
    <h3><?php _e( 'Use Profiles below for...', 'intelliwidget' ); ?></h3>
    <div class="inside">
<?php
        $count = 0;
        foreach ( IntelliWidgetCondSetCore::get_conditions() as $condition => $label ):
            $clear = ( $count++ % 3 == 0 ? 'style="clear:both"' : '' );
            if ( !( $condition_option = IntelliWidgetCondSetCore::get_condition( $condition ) ) )
                $condition_option = FALSE;
?>
      <div class="iw_condition_input" <?php echo $clear; ?> >
        <label>
          <input id="intelliwidget_condition_<?php echo $condset_id; ?>_<?php echo $condition; ?>" type="checkbox" name="intelliwidget_condition_<?php echo $condset_id; ?>[]" value="<?php echo $condition; ?>" <?php checked( $condset_id, $condition_option ); ?> />
          <?php echo $label; ?></label>
      </div>
      <?php endforeach; ?>
      <div style="clear:both"></div>
    </div>
  </div>
  <p><strong>
    <?php _e( 'Important', 'intelliwidget' ); ?>
    :</strong>
    <?php _e( 'Conditions can only be applied to one set. Selecting conditions here will deselect them from other sets. For specific terms, create the IntelliWidgetMainfiles in the "Edit Term" admin page for the term itself.', 'intelliwidget' );?>
  </p>
</div>