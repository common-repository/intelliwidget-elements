<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )exit;
?>
<div class="wrap">
    <?php do_action( 'intelliwidget_options_header', 'condsets' );  ?>
    <div class="intelliwidget-option-panel-container">

        <div style="margin:10px 0;width:96%;text-align:right">
            <a href="<?php echo wp_nonce_url( admin_url( $pagenow . '?page=iwelements-condsets&iwcondsetadd=1' ), 'iwcondsetadd' ); ?>" class="iw-condset-add icon-add">
                <?php _e( 'New Profile Set', 'intelliwidget' ); ?>
            </a>
        </div>
        <div class="iw_extension_panels" style="display:none">
            <?php
            if ( $condsets = IntelliWidgetCondSetCore::get_condsets() ): ?>
            <ul>
                <?php
                $current_id = $this->get_current_panel_id();
                foreach ( $condsets as $condset_id => $condset_name ):
                    $active = ( $condset_id == $current_id );
                $id = 'condset_' . $condset_id;
                include( $this->formpath( 'condsettab' ) );
                endforeach;
                ?>
            </ul>
            <?php
            foreach ( $condsets as $condset_id => $condset_name ):
                ?>
            <div id="condset_<?php echo $condset_id; ?>" class="extension-panel">
                <?php 
                include( $this->formpath( 'condset' ) );
  ?>
            </div>
            <?php            
            endforeach;
        else:
    ?>
            <p>
                <?php _e( 'There are currently no Profile Sets configured. Click "New Profile Set" to create one.', 'intelliwidget' ); ?>
            </p>
            <?php
            endif;
            ?>
        </div>
    </div>
</div>