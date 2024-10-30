<div id="intelliwidget_main_meta_box_<?php echo $condset_id; ?>" class="main-meta-box postbox" style="clear:both">
  <h2><?php _e( 'Conditional Profiles', 'intelliwidget' ); ?></h2>
  <div class="inside">
    <!-- input type="hidden" name="condset_ID" id="condset_ID_<?php echo $condset_id; ?>" value="<?php echo $condset_id; ?>" / -->
    <?php
        iwctl()->render_profiles( $condset_id ); ?>
  </div>
</div>
