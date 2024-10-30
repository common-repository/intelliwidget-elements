<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
global $pagenow;
?>
<a id="condsets" href="<?php echo admin_url( $pagenow . '?page=iwelements-condsets' ); ?>" class="nav-tab<?php echo ('condsets' == $active_tab ? ' nav-tab-active' : '' ); ?>"><?php _e( 'Conditional Profiles', 'intelliwidget' ); ?></a>
