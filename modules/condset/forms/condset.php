<form action="<?php echo admin_url( $pagenow . '?page=iwelements-condsets' ) ; ?>" method="post" id="intelliwidget_condset_form">
  <div class="submitbox" style="float:right"><span class="delete" style="line-height:2em;margin-right:20px"><a class="submitdelete" href="<?php echo wp_nonce_url( admin_url( $pagenow . '?page=iwelements-condsets&iwcondsetdel=' . $condset_id ), 'iwcondsetdel' ); ?>">
    <?php _e( 'Delete', 'intelliwidget' ); ?>
    </a></span>
    <input type="submit" class="button button-primary button-large" name="iwcondsetupd" value="<?php _e( 'Save Set', 'intelliwidget' ); ?>" />
  </div>
  <input id="condset_ID_<?php echo $condset_id; ?>" type="hidden" name="condset_ID" value="<?php echo $condset_id; ?>" />
  <p>
    <label>
      <?php _e( 'Name of Profile Set', 'intelliwidget' ); ?>
      : </label>
    <input id="intelliwidget_condset_name_<?php echo $condset_id; ?>" type="text" name="intelliwidget_condset_name_<?php echo $condset_id; ?>" value="<?php echo esc_attr( isset( $condset_name ) ? $condset_name : '' ); ?>" />
  </p>
  <?php
        include( $this->formpath( 'conditions' ) );
        include( $this->formpath( 'condset-metabox') ); ?>
</form>