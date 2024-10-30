<div class="notice-warning notice is-dismissible">
<p>
<?php printf( __( 'IntelliWidget Pro version %s requires IntelliWidget %s or later and has been deactivated. You can %s or download it %shere%s.', 'chld_thm_cfg_pro' ),
        IWELEMENTS_VERSION,
        INTELLIWIDGET_MIN_VERSION,
        '<a href="' . wp_nonce_url( ( is_multisite() 
            ? network_admin_url( 'update.php?action=' . $action . '-plugin&plugin=intelliwidget-per-page-featured-posts-and-menus' ) 
            : admin_url( 'update.php?action=' . $action . '-plugin&plugin=intelliwidget-per-page-featured-posts-and-menus' ) ), $action . '-plugin_intelliwidget-per-page-featured-posts-and-menus' ) 
            . '">' . ( 'upgrade' == $action ? __( 'upgrade it', 'intelliwidget' ) : __( 'install it', 'intelliwidget' ) ) . '</a>',
        '<a href="https://downloads.wordpress.org/plugin/intelliwidget-per-page-featured-posts-and-menus.' . INTELLIWIDGET_MIN_VERSION . '.zip" target="_blank">',
        '</a>'
); ?>
</p>
</div>