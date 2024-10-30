<?php
if ( !defined( 'ABSPATH' ) ) exit;
/*
    Plugin Name: IntelliWidget Pro Datafield
    Plugin URI: http://www.lilaeamedia.com/iwtemplates/
    Description: Core styles, scripts and shortcodes for IntelliWidget Framework
    Version: 1.0.0
    Author: Lilaea Media
    Author URI: http://www.lilaeamedia.com/
   
    This file and all accompanying files (C) 2014-2016 Lilaea Media LLC except where noted. See license for details.
*/


class IntelliWidgetShortCodeDatafield {    
    static function datafield( $atts ) {
        $a = shortcode_atts( array( 
            'name'      => '', 
            'filter'    => 1, 
            'format'    => '', 
            'tag'       => '', 
            'tagc'      => '',
            'link'      => '',
            'linkc'     => '',
        ), $atts );
        if ( empty( $a[ 'name' ] ) )
            return '';
        $post = get_iw_post();
        if ( is_object( $post ) ):
            switch( $a[ 'name' ] ):
                case 'pubdate':
                    $content = $post->post_date;
        break;
                case 'slug':
                    $content = $post->post_name;
        break;
                case 'excerpt':
                    $content = empty( $post->post_excerpt ) ? $post->post_content : $post->post_excerpt;
        break;
                case 'title':
                    $content = in_iw_loop() ? ( empty( $post->alt_title ) ? $post->post_title : ( iwinstance()->get( 'keep_title' ) ? $post->post_title : $post->alt_title ) ) : $post->post_title;
        break;
                case 'content':
                    $content =  $post->post_content;
        break;
                default:
                    $content = get_post_meta( $post->ID, $a[ 'name' ], TRUE );
            endswitch;
        endif;
        if ( !empty( $a[ 'format' ] ) )
            $content = date( $a[ 'format' ], strtotime( $content ) );
        if ( 'none' != $a[ 'filter' ] ):
            $filter = 'title' == $a[ 'name' ] ? 'the_title'
                : ( 'excerpt' == $a[ 'name' ] ? 'intelliwidget_trim_excerpt'
                : ( $a[ 'filter' ] ? 'the_content' 
                   : 'widget_text' ) );
             $content = apply_filters( $filter, apply_filters( 'iwtemplates_datafield_' . $a[ 'name' ], $content ) );
        endif;
        return IntelliWidgetShortCodeCore::wrap_content( $content, $a, $post );
    }
    
    static function term( $atts ){
        $a = shortcode_atts( array( 'tax' => 'category', 'link' => 0, 'all' => 0, 'orderby' => '' ), $atts );
        $post = get_iw_post();
        $args = array( 'fields' => 'names' );
        if ( $a[ 'orderby' ] ):
            $args[ 'orderby' ] = 'meta_value';
            $args[ 'meta_key' ] = $a[ 'orderby' ];
        endif;
        if ( is_object( $post ) ):
            if ( $names = wp_get_object_terms( $post->ID, $a[ 'tax' ], $args ) ):
        
                // use primary term if available. FIXME - make this pluggable hook
                if ( !$a[ 'all' ] && ( $primary = get_post_meta( $post->ID, '_yoast_wpseo_primary_' . $a[ 'tax' ], TRUE ) )
                    && ( $term = get_term( $primary, $a[ 'tax' ] ) ) )
                    $names = array( $term->name );
        
                $type = $post->post_type;
                $linknames = array();
                foreach( $names as $name ):
                    if ( $a[ 'link' ] ):
                        $linkname = '<a href="' . get_term_link( $name ) . '">' . $name . '</a>';
                    else:
                        $linkname = $name;
                    endif;
                    $linknames[] = sprintf( '<span class="%s-%s">%s</span>', $type, $a[ 'tax' ], $linkname );
                    if ( !$a[ 'all' ] )
                        break;
                endforeach;
                
                $termlist = implode( '<span class="term-delimiter"></span>', $linknames );
                return apply_filters( 'shortcode_term', $termlist, $linknames, $type, $a[ 'tax' ] );
            endif;
        endif;
    }
    
    static function author( $atts ){
        $a = shortcode_atts( array( 'link' => 0 ), $atts );
        $post = get_iw_post();
        if ( is_object( $post ) ):
            $content = get_the_author_meta( 'display_name', $post->post_author );
            if( $a[ 'link' ] ):
                $url = get_author_posts_url( get_the_author_meta( 'ID', $post->post_author ) );
                $content = '<a href="' . $url . '" class="author-link">' . $content . '</a>';
            endif;
            return $content;
        endif;
        
    }
}
    
