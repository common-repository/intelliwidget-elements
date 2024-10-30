<?php
if ( !defined( 'ABSPATH' ) ) exit;
/*
    Plugin Name: IntelliWidget Pro Gallery Shortcode
    Plugin URI: http://www.lilaeamedia.com/iwtemplates/
    Description: Core styles, scripts and shortcodes for IntelliWidget Framework
    Version: 1.0.0
    Author: Lilaea Media
    Author URI: http://www.lilaeamedia.com/
   
    This file and all accompanying files (C) 2014-2016 Lilaea Media LLC except where noted. See license for details.
*/


class IntelliWidgetShortCodeGallery {   
    /**
     * Override core gallery shortcode to use unsorted lists,
     * add responsive behavior, pdf lists and other features.
     * Function requires a value in ul_class attribute
     * otherwise it falls back to core version.
     */
    static function gallery( $attr ) {
        static $instance = 0;
        $instance++;
        if ( ! empty( $attr['ids'] ) ) {
            // 'ids' is explicitly ordered, unless you specify otherwise.
            if ( empty( $attr['orderby'] ) )
                $attr['orderby'] = 'post__in';
        }
        // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
        if ( isset( $attr[ 'orderby' ] ) ):
            $attr[ 'orderby' ] = sanitize_sql_orderby( $attr[ 'orderby' ] );
            if ( !$attr[ 'orderby' ] )
                unset( $attr[ 'orderby' ] );
        endif;
        if ( isset( $attr[ 'ul_class' ] ) ):
            $attr[ 'ul_class' ] = sanitize_text_field( $attr[ 'ul_class' ] );
            if ( !$attr[ 'ul_class' ] )
                unset( $attr[ 'ul_class' ] );
        endif;
        if ( isset( $attr[ 'li_class' ] ) ):
            $attr[ 'li_class' ] = sanitize_text_field( $attr[ 'li_class' ] );
            if ( !$attr[ 'li_class' ] )
                unset( $attr[ 'li_class' ] );
        endif;
        
        // bypass Enhanced Media Library (EML) filter
        // TODO: add other plugin overrides 
        $eml = 0;
        if ( has_filter( 'shortcode_atts_gallery', 'wpuxss_eml_shortcode_atts' ) ):
            $eml = 1;
            remove_filter( 'shortcode_atts_gallery', 'wpuxss_eml_shortcode_atts' );
        endif;
        extract( shortcode_atts( array(
            'order'     => 'ASC',
            'orderby'   => 'menu_order ID',
            'size'      => 'large',
            'ids'       => '',
            'ul_class'  => 'slides',
            'li_class'  => '',
            'cols'      => 1,
            'paginate'  => 0,
            'ppp'       => -1,
            'tag'       => 'ul',
            'caption'   => 0,
        ), $attr, 'gallery'));
    
        // restore EML filter if bypassed
        if ( $eml )
            add_filter( 'shortcode_atts_gallery', 'wpuxss_eml_shortcode_atts', 10, 3 );
		// build query
        $query = array(
            'post_status'       => 'inherit', 
            'post_type'         => 'attachment', 
            'posts_per_page'    => $ppp,
            'order'             => $order, 
            'orderby'           => $orderby,
        );
        $queryok = 0;

        if ( !empty( $ids ) ):
            //$query[ 'include' ] = $ids;
            $query[ 'post__in' ] = explode( ',', $ids );
            $queryok = 1;
        else:
            if ( isset( $attr[ 'type' ] ) ):
                $query[ 'post_mime_type' ] = 'image' == $attr[ 'type' ] ? 'image' : 'application';
                $queryok = 1;
            endif;
            
            if ( isset( $attr[ 'monthnum' ] ) && isset( $attr[ 'year' ] ) ):
                $query[ 'monthnum' ] = $attr[ 'monthnum' ];
                $query[ 'year' ]    = $attr[ 'year' ];
                $queryok = 1;
            endif;
            
            $tax_query = array();
            foreach ( get_taxonomies_for_attachments( 'names' ) as $taxonomy ):
                if ( isset( $attr[ $taxonomy ] ) ):
                    $terms = explode( ',', $attr[ $taxonomy ] );
                    $tax_query[] = array(
                        'taxonomy' => $taxonomy,
                        'field' => 'term_id',
                        'terms' => $terms,
                        'operator' => 'AND',
                    );
                endif;
            endforeach;
            
            if ( !empty( $tax_query ) ):
                $tax_query[ 'relation' ] = 'AND';
                $query[ 'tax_query'] = $tax_query;
                $queryok = 1;
            endif;
        // default ot attached media if no ids are present
            if ( empty( $attr[ 'ids' ] ) && empty( $attr[ 'id' ] ) ):
                global $post;
                $attr[ 'id' ] = $post->ID;
            endif;
            if ( !empty( $attr[ 'id' ] ) ):
                $query[ 'post_parent' ] = intval( $attr[ 'id' ] );
                $queryok = 1;
            endif;
        endif;
        $attachments = array();
        if ( $queryok ):
            if ( 'post__in' === $orderby )
                $query[ 'orderby' ] = 'menu_order ID';
                
            if ( 'RAND' == $order || 'rand' == $orderby )
                $query[ 'orderby' ] = 'none';
                
            if ( $paged = get_query_var( 'gpg' ) )
                $query[ 'paged' ] = intval( $paged );
            else
                $paged = 0;
            if ( $keywd = get_query_var( 'gkw' ) )
                $query[ 's' ] = sanitize_text_field( $keywd );
            else
                $keywd = '';
        	//echo '<pre><code><small>' . print_r( $query, TRUE ) . '</small></code></pre>';

            $qobj = new WP_Query( $query );
            //echo '<pre><code><small>' . print_r( $qobj, TRUE ) . '</small></code></pre>';
            foreach ( $qobj->posts as $key => $post )
                $attachments[ $post->ID ] = $post;
            wp_reset_postdata();

        endif;
        if ( is_feed() ) {
            $output = "\n";
            foreach ( $attachments as $att_id => $attachment )
                $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
            return $output;
        }
        $listtag = 'ul' == $tag ? 'ul' : 'div';
        $itemtag = 'ul' == $tag ? 'li' : 'div';
        $selector = "gallery-{$instance}";
    
        $size_class = sanitize_html_class( $size );
        $output = '<' . $listtag . ' id="' . $selector . '" class="' . $ul_class . '">';
    
        $i = 0;
        foreach ( $attachments as $id => $attachment ):
            // set Templates responsive behavior if multi column
            $cell_class = self::item_classes( ++$i, $cols, $li_class );
            $image_caption  = trim( $attachment->post_excerpt ); //get_post( $id )->post_excerpt;
            if ( strstr( $attachment->post_mime_type, 'application' ) ):
                $parts = pathinfo( $attachment->guid );
                $linkclass = substr( $parts[ 'extension' ], 0, 3 ) . 'link';
                $output .= '<' . $itemtag . ' class="' . $cell_class . '">' . str_replace( '<a', '<a class="' . $linkclass . '"', wp_get_attachment_link( $id, $size, FALSE, FALSE ) ) . '</' . $itemtag . '>';
            elseif ( strstr( $cell_class, 'bgcover' ) ):
                $image = wp_get_attachment_image_src( $id, 'full' );
                $output .= '<' . $itemtag . ' class="' . $cell_class . '" style="background-image:url( ' . $image[ 0 ] . ' )"></' . $itemtag . '>' . PHP_EOL;
            else:
                if ( ! empty( $attr[ 'link' ] ) && 'file' === $attr[ 'link' ] ):
                    $image_output = wp_get_attachment_link( $id, $size, FALSE, FALSE );
                elseif ( ! empty( $attr[ 'link' ] ) ):
                    $image_title    = esc_attr( get_the_title( $id ) );
                    $image_link     = wp_get_attachment_url( $id );
                    $image          = wp_get_attachment_image( $id, $size, FALSE ); 
                    $image_class    = 'pp' === $attr[ 'link' ] ? 'woocommerce-main-image zoom' : 'thickbox';
                    $image_output   = sprintf( '<a href="%s" itemprop="image" class="%s" title="%s" data-rel="prettyPhoto[' . $selector . ']">%s</a>', $image_link, $image_class, $image_caption, $image );
                elseif ( !empty( $attr[ 'link' ] ) && 'page' === $attr[ 'link' ] ):
                    $image_output = wp_get_attachment_link( $id, $size, TRUE, FALSE );
                else:
                    $image_output = wp_get_attachment_image( $id, $size, FALSE );
                endif;
                $output .= '<' . $itemtag . ' class="' . $cell_class . '">';
                $output .= $image_output;
                if ( $caption && $image_caption )
                    $output .= '<p class="wp-caption-text gallery-caption" id="' . $selector . '-' . $id . '">' . wptexturize( $image_caption ) . '</p>';
                $output .= '</' . $itemtag . '>';
            endif;
        endforeach;
    
        $output .= '</' . $listtag . '>';
        
        // add paging if necessary
        if ( $paginate && $qobj->max_num_pages > 1 ): // generate pagination output
            $args = array(
                'base'      => '?%_%',
                'format'    => 'gpg=%#%',
                'total'     => $qobj->max_num_pages,
                'current'   => max( 1, $paged ),
                'prev_text' => __( '&laquo;' ),
                'next_text' => __( '&raquo;' ),
                'add_args'  => empty( $keywd ) ? FALSE : array( 'gkw' => $keywd ),
            );
            $output .= '<div class="paginate">' . paginate_links( $args ) . '</div>';
        endif;
        return $output;
    }
 
    /**
     * slightly different variation of the Templates and IntelliWidget post classes function
     * in that it accepts and returns a scalar classes var
     */
    static function item_classes( $seq, $cols = 1, $classes = '' ) {
        $classes    = (array) $classes;
        $classes[]  = 'post-seq-' . $seq;
        $classes[]  = ( $seq % 2 === 0 ) ? 'even' : 'odd';
        $row_len    = intval( $cols );
        if ( $row_len > 1 ):
            //$classes[] = 'cell'; // float inline with right margin
            $classes[] = 'width-1-' 
                . ( in_array( $row_len, array( 7,9,11 ) ) ? --$row_len : $row_len ); // width percentage based on colums
            $classes[] = 'clearfix'; // always clearfix floated divs so they behave correctly
            // no margin at end of row
            if ( $seq % $row_len === 0 ):
                $classes[] = 'end';
            // clear float at start of row
            elseif ( $seq % $row_len === 1 ):
                $classes[] = 'clear';
            endif;
        endif;
        return implode( ' ', $classes );
    }

    static function gallery_pagination_vars( $vars ) {
        $vars[] = 'gkw';
        $vars[] = 'gpg';
        return $vars;
    }
    
}
    
