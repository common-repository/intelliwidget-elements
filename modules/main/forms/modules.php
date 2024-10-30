<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )exit;
?>
<div class="wrap">
    <?php do_action( 'intelliwidget_options_header', 'modules' );  ?>
    <div class="intelliwidget-option-panel-container">
        <div class="module-panel-left">
            <form id="iwelements_options_form" method="post" action="">
                <?php wp_nonce_field( 'iwelementsupd' ); ?>
                <p>
                    <label>
                        <?php _e( 'Update Key', 'intelliwidget' ); ?>
                    </label>
                    <input id="iwelements_update_key" name="iwelements_update_key" type="text" value="<?php echo esc_attr( $this->get_option( 'update_key' ) ); ?>" placeholder="<?php _e( 'Update Key', 'intelliwidget' ); ?>" autocomplete="off"/>
                </p>
                <p>
                    <label>
                        <input id="iwelements_use_condset" name="use_condset"  type="checkbox" 
            value="1" <?php checked( $this->get_option( 'use_condset' ), 1 ); ?> autocomplete="off" />
                        <?php _e( 'Conditional Profiles', 'intelliwidget' ); ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input id="iwelements_use_switcher" name="use_switcher"  type="checkbox" 
            value="1" <?php checked( $this->get_option( 'use_switcher' ), 1 ); ?> autocomplete="off" />
                        <?php _e( 'Post Type Switcher', 'intelliwidget' ); ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input id="iwelements_disable_emojis" name="disable_emojis"  type="checkbox" 
            value="1" <?php checked( $this->get_option( 'disable_emojis' ), 1 ); ?> autocomplete="off" />
                        <?php _e( 'Disable Emojis', 'intelliwidget' ); ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input id="iwelements_use_iwicons" name="use_iwicons"  type="checkbox" 
            value="1" <?php checked( $this->get_option( 'use_iwicons' ), 1 ); ?> autocomplete="off" />
                        <?php _e( 'Load Menu Icons', 'intelliwidget' ); ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input id="iwelements_use_bootstrap_full" name="use_bootstrap"  type="radio" 
            value="full" <?php checked( $this->get_option( 'use_bootstrap' ), 'full' ); ?> autocomplete="off" />
                        <?php _e( 'Load Bootstrap (all styles)', 'intelliwidget' ); ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input id="iwelements_use_bootstrap_lite" name="use_bootstrap"  type="radio" 
            value="lite" <?php checked( $this->get_option( 'use_bootstrap' ), 'lite' ); ?> autocomplete="off" />
                        <?php _e( 'Load Bootstrap Lite (minimal styles)', 'intelliwidget' ); ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input id="iwelements_use_bootstrap_none" name="use_bootstrap"  type="radio" 
            value="0" <?php checked( $this->get_option( 'use_bootstrap' ), 0 ); ?> autocomplete="off" />
                        <?php _e( 'Do not load Bootstrap', 'intelliwidget' ); ?>
                    </label>
                </p>
                <?php do_action( 'iwelements_module_options' ); ?>
                <p>
                    <input class="button" id="iwelements_save_plugin_options" name="iwelements_save_plugin_options" type="submit" value="<?php _e( 'Save Options', 'intelliwidget' ); ?>"/>
                </p>
            </form>
        </div>
        <div class="module-panel-right">
            <h3>
                <?php _e( 'Import/Export', 'intelliwidget' ); ?>
            </h3>
            <form id="form_import" method="post" action="<?php echo admin_url( $pagenow . '?page=iwelements' ); ?>" enctype="multipart/form-data">
                <div id="import_form_container" class="form-save-container">
                    <?php wp_nonce_field( 'iwfimport', '_wpnonce', TRUE, TRUE ); ?>
                    <?php if ( empty( $this->import_modules ) ): ?>
                    <p>
                        <input type="file" id="iwf_options_file" name="iwf_options_file" class="button button-large" disabled />
                        <input type="submit" id="iwf_import_submit" name="iwf_import_submit" value="<?php _e( 'Upload Options File', 'intelliwdget' ); ?>" class="button button-large" disabled />
                        <input type="hidden" id="iwf_import" name="iwf_import" value="1" />
                    </p>
                    <script>(function($){
        $( document ).ready(function(){
            $( '#form_import' ).on( 'submit', function( e ){
                e.preventDefault();
                if ( '' === $( '#iwf_options_file' ).val() 
                    || !( filename = document.getElementById( 'iwf_options_file' ).files[0].name )
                    || !filename.match( /^iwfexport_.+\.zip$/ ) ) {
                    alert( '<?php _e( 'Please select a valid options file.', 'intelliwidget' ); ?>' );
                    return false;
                }
                e.target.submit();
            }).find( 'input' ).removeAttr( 'disabled' );
        })
    })(jQuery);
</script>
                    <?php else: ?>
                    <p><strong>
                        <?php _e( 'Confirm  module data import by selecting boxes below:', 'intelliwidget' ); ?>
                        </strong></p>
                    <?php
                    $module_labels = IntelliWidgetMainStrings::get_menu( 'modules' );
                    foreach ( $this->import_modules as $module ): ?>
                    <p>
                        <label>
                            <input type="checkbox" id="confirm_iwfimport_<?php echo $module; ?>" name="confirm_iwfimport[]" value="<?php echo $module; ?>" autocomplete="off" />
                            <?php echo ( isset( $module_labels[ $module ] ) ? $module_labels[ $module ] : $module ); ?></label>
                    </p>
                    <?php endforeach; ?>
                    <p>
                        <input type="submit" id="iwf_import" name="iwf_import" value="<?php _e( 'Import Data', 'intelliwidget' ); ?>" class="button button-large"/>
                        <input type="submit" id="iwf_cancel_import" name="iwf_cancel_import" value="<?php _e( 'Cancel', 'intelliwdget' ); ?>" class="button button-large"/>
                    </p>
                    <?php endif; ?>
                </div>
            </form>
            <?php if ( empty( $this->import_modules ) ): ?>
            <form id="form_export" method="post" action="<?php echo admin_url( $pagenow . '?page=iwelements' ); ?>">
                <div class="form-save-container">
                    <?php wp_nonce_field( 'iwfexport', '_wpnonce', TRUE, TRUE ); ?>
                    <input type="submit" id="iwf_export" name="iwf_export" value="<?php _e( 'Export Options File', 'intelliwidget' ); ?>" class="button button-large"  />
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
