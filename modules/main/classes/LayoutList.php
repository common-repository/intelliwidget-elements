<?php
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * IntelliwidgetMainMenu.php - class for hierarchical custom menus
 *
 * @package IntelliWidget
 * @subpackage templates
 * @author Jason C Fleming
 * @copyright 2014-2015 Lilaea Media LLC
 * @access public
 */
class IntelliWidgetMainLayoutList extends Walker {
    
    public $db_fields = array( 'parent' => 'parent_id', 'id' => 'id' );
    public $itembegin;
    public $itemend;
    public $outerbegin;
    public $outerend;
    public $classmeta;
    public $template;
    
    function __construct( $template ){
        global $post;
        $this->template = $template;
        if ( $template->get_option( 'usetag' ) ):
            if ( $css = implode( ' ', $template->render_css( TRUE ) ) )
                $css = ' ' . $css;
            if ( $tagclass = $template->get_option( 'tagclass' ) )
                $css = ' ' . $tagclass . $css;
            $this->itembegin = '<' . $template->get_option( 'tag' ) . ' id="' . $template->get_id() . '%1$s" class="%2$s' . $css . '">' . "\n";
            $this->itemend = '</' . $template->get_option( 'tag' ) . ">\n";
        endif;
        if ( $classmeta = $template->get_option( 'classmeta' ) )
            $this->classmeta = $classmeta;
        if ( $template->get_option( 'useouter' ) ):
            // removing id from outer element
            //  id="' 
            //    . sprintf( $template->get_selector(), '_' . $post->ID ) 
            //    . '"
            $this->outerbegin = '<' . $template->get_option( 'outer' ) . ' class="' . $template->get_option( 'outerclass' ) 
                . ( !iwinstance()->get( 'nav_menu_classes' ) ? '' : esc_attr( iwinstance()->get( 'nav_menu_classes' ) ) ) 
                . "\">\n";
            $this->outerend = '</' . $template->get_option( 'outer' ) . ">\n";
        endif;
        
    }
    
    public function render( $posts ){
		iwquery()->in_the_loop = TRUE;
        global $intelliwidget_in_the_loop;
        $intelliwidget_in_the_loop = TRUE;
        do_action( 'intelliwidget_render_template_before', $this->instance );
        echo $this->outerbegin 
            . $this->walk( $posts, 0 ) 
            . $this->outerend;
        do_action( 'intelliwidget_render_template_after', $this->instance );
		iwquery()->in_the_loop = FALSE;
        $intelliwidget_in_the_loop = FALSE;
    }

    
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= $this->outerbegin . "\n";
    }

    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= $this->outerend . "\n";
    }

    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        //echo '<!-- ' . esc_html( print_r( $item, TRUE ) ) . ' -->';
        if ( is_multisite() ):
            $current_site_id = get_current_blog_id();
            if ( 'current' != $this->instance[ 'site_id' ] && $this->instance[ 'site_id' ] != $current_site_id ):
                switch_to_blog( $this->instance[ 'site_id' ] );
            endif;
        endif;

        iwquery()->convert_listdata( $item );
        
        
        // render template
        if ( $this->itembegin )
            $output .= sprintf( 
                $this->itembegin, 
                $this->template->get_post_selector(),
                iwquery()->post->link_classes
                ) . "\n";
        ob_start();
        $this->template->iterate( $this->instance );
        $output .= ob_get_clean() . "\n";
        
        if ( is_multisite() && ms_is_switched() )
            restore_current_blog();

    }

    public function end_el( &$output, $item, $depth = 0, $args = array() ) {
        if ( $this->itembegin )
            $output .= ( $this->itemend ? $this->itemend : '' ) . "\n";
    }

} // IntelliWidgetMainLayoutList

