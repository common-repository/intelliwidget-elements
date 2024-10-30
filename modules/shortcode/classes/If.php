<?php
if ( !defined( 'ABSPATH' ) ) exit;
/*
    Plugin Name: IntelliWidget Pro If Shortcode
    Plugin URI: http://www.lilaeamedia.com/iwtemplates/
    Description: Core styles, scripts and shortcodes for IntelliWidget Framework
    Version: 1.0.0
    Author: Lilaea Media
    Author URI: http://www.lilaeamedia.com/
   
    This file and all accompanying files (C) 2014-2016 Lilaea Media LLC except where noted. See license for details.
*/


class IntelliWidgetShortCodeIf {    
    static function shortcode_if( $atts, $content = NULL ){
        //echo '<!-- ' . print_r( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ), TRUE ) . ' -->' . "\n";
        $a = shortcode_atts( array( 'query' => NULL, 'field' => NULL, 'cond' => '', 'value' => '' ), $atts );
        if ( $parts = explode( '[else]', $content ) ):
            $if = array_shift( $parts );
            $else = array_shift( $parts );
        endif;
        $res = isset( $else ) ? $else : '';
        if ( empty( $a[ 'query' ] ) ):
            // must have a field to test
            if ( empty( $a[ 'field' ] ) )
                return;
            // shorthand test if field has any value
            if ( empty( $a[ 'value' ] ) && empty( $a[ 'cond' ] ) )
                $a[ 'cond' ] = 'notempty';
            // use iw post if intelliwidget
            if ( in_iw_loop() ):
                
                $post = iwquery()->post;
                //echo '<!-- in_iw_loop ! -->' . "\n";
            // otherwise use WP post from queried object
            else:
                //echo '<!-- NOT in_iw_loop ! -->' . "\n";
                global $post;    
            endif;
            if ( is_object( $post ) ):
                $test = get_post_meta( $post->ID, $a[ 'field' ], TRUE );
                //echo '<!-- test field: ' . $a[ 'field' ] . ' -->' . "\n";
                // test against another meta field
                if ( !empty( $a[ 'value' ] ) ):
                    // special cases
                    if ( preg_match( "{^datefield:(.+)$}", $a[ 'value' ], $matches ) ):
                        $valuefield = $matches[ 1 ];
                        $a[ 'value' ] = date( 'Y-m-d', strtotime( get_post_meta( $post->ID, $valuefield, TRUE ) ) );
                        $test = date( 'Y-m-d', strtotime( $test ) );
                    elseif ( preg_match( "{^field:(.+)$}", $a[ 'value' ], $matches ) ):
                        $valuefield = $matches[ 1 ];
                        $a[ 'value' ] = get_post_meta( $post->ID, $valuefield, TRUE );
                    endif;
                endif;
                //echo '<!-- testing if ' . $test . ' is ' . $a[ 'cond' ] . ' than ' . $a[ 'value' ] . ' -->' . PHP_EOL;

                switch ( $a[ 'cond' ] ):
                    case 'empty':
                        if ( empty( $test ) )
                            $res = $if;
                        break;
                    case 'notempty':
                        if ( !empty( $test ) )
                            $res = $if;
                        break;
                    case 'ne':
                        if ( $test != $a[ 'value' ] )
                            $res = $if;
                        break;
                    case 'gt':
                        if ( $test > $a[ 'value' ] )
                            $res = $if;
                        break;
                    case 'lt':
                        if ( $test < $a[ 'value' ] )
                            $res = $if;
                        break;
                    case 'ge':
                        if ( $test >= $a[ 'value' ] )
                            $res = $if;
                        break;
                    case 'le':
                        if ( $test <= $a[ 'value' ] )
                            $res = $if;
                        break;
                    case 'in':
                        if ( in_array( $test, explode( ',', $a[ 'value' ] ) ) )
                            $res = $if;
                        break;
                    case 'notin':
                        if ( !in_array( $test, explode( ',', $a[ 'value' ] ) ) )
                            $res = $if;
                    case 'eq':
                    default:
                        if ( $test == $a[ 'value' ] )
                            $res = $if;
                endswitch;
            endif;
        else:
            //echo '<!-- testing for ' . $a[ 'query' ] . ' -->' . PHP_EOL;
            $conditions = explode( ',', $a[ 'query' ] );
            foreach( $conditions as $cond ):
                if ( IntelliWidgetCondSetCore::test_condition( $cond ) ):
                    $res = $if;
                    break;
                endif;
            endforeach;
        endif;
        return do_shortcode( $res );
    }


}
    
