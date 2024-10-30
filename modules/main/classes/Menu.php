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
class IntelliWidgetMainMenu extends Walker {
    
    public $db_fields = array( 'parent' => 'parent_id', 'id' => 'id' );
    public $current_post_id;
    public $current_ancestors;
    public $current_parent;
    
    function __construct( $template = NULL ){
        global $post;
        $this->current_post_id    = is_object( $post ) ? $post->ID : NULL;
        $this->current_ancestors  = $this->current_post_id ? get_post_ancestors( $this->current_post_id ) : array();
        $this->current_parent     = current( $this->current_ancestors );
    }
    
    public function render( $posts ){
        echo '<ul class="iw-custom-menu ' . iwinstance()->get( 'nav_menu_classes' )  . '">' . "\n" 
            . $this->walk( $posts, 0 ) 
            . "\n</ul>\n";
        
    }
    
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $t = "    ";
        $n = "\n";
        $indent = str_repeat( $t, $depth );

        $class_names = ' class="sub-menu"';

        $output .= "{$n}{$indent}<ul$class_names>{$n}";
    }

    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $t = "    ";
        $n = "\n";
        $indent = str_repeat( $t, $depth );
        $output .= "$indent</ul>{$n}";
    }

    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $t = "    ";
        $n = "\n";
        $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';
        // convert legacy data
        if ( !empty( $item->obj_id ) )
            $item->id = intval( $item->obj_id );

        // get item classes
        $classes = empty( $item->class ) ? array() : (array) $item->class;
        
        $classes[] = 'menu-item';
        $classes[] = 'intelliwidget-menu-item';
        $classes[] = 'menu-item-' . $item->id;

        if ( $item->id == $this->current_post_id )
            $classes[] = 'current-menu-item';
        if ( $item->id == $this->current_parent )
            $classes[] = 'current-menu-parent';
        if ( in_array( $item->id, $this->current_ancestors ) )
            $classes[] = 'current-menu-ancestor';

        if ( $this->has_children )
            $classes[] = 'menu-item-has-children';
        $class_names = join( ' ', $classes );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $output .= $indent . '<li' . $class_names .'>';

        $src = '';
        $atts = array();
        $atts['target'] = empty( $item->target ) ? '' : $item->target;
        
        // handle legacy data
        if ( !isset( $item->type ) ):
            if ( preg_match( "/^00/", $item->id ) ): 
                $item->type = 'custom';
            else:
                $item->type = 'post';
            endif;
        endif;
        
        // get link
        if ( $item->url ):
            $atts[ 'href' ] = html_entity_decode( $item->url );
        elseif ( 'post' == $item->type ):
            $atts[ 'href' ] = get_permalink( $item->id ) ;
        elseif ( 'term' == $item->type ):
            // IntelliWidget uses term_taxonomy_id for terms so we need to lookup the term_id
            // note that the second argument (taxonomy) is arbitrary because the true taxonomy is determined
            // by the term_taxonomy_id
            if ( $term = get_term_by( 'term_taxonomy_id', $item->id, 'blah' ) )
                $atts[ 'href' ] = get_term_link( $term->term_id );
            // switch to main site to get term images
            if ( is_multisite() )
                    switch_to_blog( get_main_site_id() );
        endif;
        // failsafe for empty object
        if ( isset( $atts[ 'href' ] ) && is_wp_error( $atts[ 'href' ] ) )
            $atts[ 'href' ] = '#';

        // get thumbnail
        if ( isset( $item->image ) && $item->image ):
            if ( 'featured' == $item->image )
                $key = '_thumbnail_id';
            else
                $key = '_intelliwidget_' . $item->image . '_id';
            // for posts
            if ( 
                ( 'post' == $item->type 
                && ( $thumbnail_id = get_post_meta( $item->id, $key, TRUE ) ) )
                ||
                ( 'term' == $item->type && isset( $term ) 
                && ( $thumbnail_id = get_term_meta( $term->term_id, $key, TRUE ) ) ) ):
                // failsafe for empty object
                if ( !is_wp_error( $thumbnail_id )
                    && ( $image = wp_get_attachment_image_src( $thumbnail_id, 'full' ) ) )
                    $src = array_shift( $image );
            endif;
        endif;

        // switch back to original blog
        if ( 'term' == $item->type && is_multisite() && ms_is_switched() )
            restore_current_blog();
        
        
        $attributes = '';
        foreach ( $atts as $attr => $value ):
        
            if ( ! empty( $value ) ):
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            endif;
        endforeach;

        $title = apply_filters( 'the_title', html_entity_decode( $item->title ), $item->id );
        if ( $src )
            $output .= '<div class="intelliwidget-menu-thumbnail-container">
            <div class="bgcover intelliwidget-menu-thumbnail" style="background-image:url( ' . $src . ' )"></div>
            <a'. $attributes .'></a></div>';
        $output .= '<span class="intelliwidget-menu-title"><a'. $attributes .'>' . $title . '</a></span>';
        
        // get excerpt (if applicable)
        if ( strstr( $item->class, 'use_excerpt' ) ):
            if ( 'post' == $item->type )
                $excerpt = get_the_excerpt( $item->id );
            elseif ( 'term' == $item->type && ( $term = get_term_by( 'term_taxonomy_id', $item->id, 'blah' ) ) )
                $excerpt = $term->description;
            if ( $excerpt )
                $output .= '<span class="intelllwidget-excerpt">' . $excerpt . '</span>';
        endif;
    }

    public function end_el( &$output, $item, $depth = 0, $args = array() ) {
        $t = "    ";
        $n = "\n";
        $output .= "</li>{$n}";
    }

} // IntelliWidgetMainMenu

