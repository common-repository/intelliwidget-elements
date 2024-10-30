<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
    exit;
/**
 * class-intelliwidget-form.php - Outputs widget form
 *
 * @package IntelliWidgetMain
 * @subpackage includes
 * @author Jason C Fleming
 * @copyright 2014-2015 Lilaea Media LLC
 * @access public
 */
include( $this->formpath( 'docslink' ) );
?>
<p>
    <label title="<?php echo $this->get_tip( 'widget_page_id' );?>" for="<?php echo 'intelliwidget_widget_page_id'; ?>">
        <?php echo $this->get_label( 'widget_page_id' ); ?>:
    </label>
    <select style="width:75%" name="intelliwidget_widget_page_id" id="intelliwidget_widget_page_id">
        <option value="">
            <?php _e( 'This form', 'intelliwidget' ); ?>
        </option>
        <?php 
switch( $this->objtype ):
    case 'post':
        iwinstance()->defaults( array( 'post_types' => IntelliWidgetMainCore::get_post_types(), 'page' => $this->copy_id ) );
        echo $this->get_posts_list( TRUE );
        break;
    case 'term':
        iwinstance()->defaults( array( 'post_types' => IntelliWidgetMainCore::get_post_types(), 'terms' => $this->copy_id ) );
        echo $this->get_terms_list();
        break;
endswitch; 
?>
    </select>
    <input name="save" class="iw-copy button button-large" id="iw_copy" value="<?php _e( 'Use', 'intelliwidget' ); ?>" type="button" style="max-width:24%;margin-top:4px"/>
</p>