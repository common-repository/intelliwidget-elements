<?php
if ( !defined( 'ABSPATH' ) ) exit;
/*
    Plugin Name: IntelliWidget Pro Table Shortcode
    Plugin URI: http://www.lilaeamedia.com/intelliwidget/
    Description: Core styles, scripts and shortcodes for IntelliWidget Framework
    Version: 1.0.0
    Author: Lilaea Media
    Author URI: http://www.lilaeamedia.com/
   
    This file and all accompanying files (C) 2014-2019 Lilaea Media LLC except where noted. See license for details.
*/


class IntelliWidgetShortCodeTable {    
    static function shortcode_table( $attr, $content = NULL ){
        $a = shortcode_atts( array( 'class' => '', 'scope' => ''  ), $attr );
        $class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $a[ 'class' ] ) ) );
		// strip UTF-8 byte order markers
		$content = preg_replace('{^\xEF\xBB\xBF|\x1A}', '', $content);
		// normalize line endings:
		$content = preg_replace('{\r\n?|%%}', "\n", $content);
        // strip tabs
        $content = preg_replace( '{\t}', '    ', $content );
        // cleanup leading/trailing pipes - note using square brace character selector eliminates need to excape pipe.
        // also note stripping spaces eliminates need for trim function.
		$content  = preg_replace( '/(^ *[|]|[|] *$)/m', '', $content );

        // split into rows
        $rows     = explode( "\n", trim( $content, "\n" ) );
        $aligns = array();
        // begin output
        $output = "<table " . ( $class ? ' class="' . $class . '"' : '') . ">\n";
        $tb = 0;
        foreach ( $rows as $r => $row ):
            $test = trim( $row );
            if ( empty( $test ) )
                continue;
			$row_cells   = preg_split( '/ *[|] */', $row );
            $el         = '';
            $scope      = '';
            $th         = 0;
            $cellcount  = 0;
            foreach ( $row_cells as $c => $col ):
                
                $is_first   = $c == 0;
                $is_last    = $c == count( $row_cells ) - 1;
                
                $span = $tag = $align = '';
                // establish header or regular cell
                if ( preg_match( '{^ *([%\^>])?(\d*)([lrc])?;(.*)$}', $col, $matches ) ):
                    $tag    = $matches[ 1 ];
                    $span   = $matches[ 2 ];
                    $align  = $matches[ 3 ];
                    $col    = $matches[ 4 ];
                endif;
                if ( $a[ 'scope' ] ):
                    if ( $r == 0 ):
                        $el = 'th';
                        $scope = ' scope="col"';
                    elseif ( $is_first ):
                        $el = 'th';
                        $scope = ' scope="row"';
                    else:
                        $el = '';
                    endif;
                endif;
                if ( !empty( $tag ) ):
                    $el     = 'th';
                    if ( '^' == $tag ):
                        $scope = ' scope="col"';
                    elseif( '>' == $tag ):
                        $scope = ' scope="row"';
                    endif;
                elseif ( empty( $el ) || ( !$a[ 'scope' ] && $r ) ):
                    $el     = 'td';
                    $scope = '';
                endif;
                if ( 'th' == $el && $r == 0 && $is_first ):
                    $output .= "<thead>\n";
                    $th     = 1;
                endif;
                if ( !$th && !$tb ):
                    $tb     = 1;
                    $output .= "<tbody>\n";
                endif;
                if ( $is_first )
                    $output .= "<tr>\n";
                // look for align token
                if ( !empty( $align ) ):
                    $align = 'c' == $align ? 
                        ' class="text-center"' //align="center"'
                        : ( 'r' == $align ?
                        ' class="text-right"'  //align="right"'
                        : ' class="text-left"' //align="left"'
                          );
                else:
                    // use attribute from first row
                    $align = isset( $aligns[ $cellcount ] ) ? $aligns[ $cellcount ] : ( isset( $aligns[ $cellcount - 1 ] ) ? $aligns[ $cellcount - 1 ] : '' );
                endif;
                if ( empty( $aligns[ $cellcount ] ) )
                    $aligns[ $cellcount ] = $align;

                //$output .= '<!-- r:' . $r . ' c:' . $c . ' cellcount: ' . $cellcount . ' align: ' . ( isset( $aligns[ $cellcount ] ) ? $aligns[ $cellcount ] : '' ) . ' is_first: ' . $is_first . ' is_last: ' . $is_last . " -->\n";
    
                // look for column span
                $cellcount += empty( $span ) ? 1 : $span;
                $span = empty( $span ) ? '' : ' colspan="' . $span . '"';
        
        
                $output .= '<' . $el . $scope . $align . $span . '>' . $col . '</' . $el . ">\n";
                if ( $is_last )
                    $output .= "</tr>\n";
                if ( $r == 0 && $th && $is_last ):
                    $output .= '</thead>';
                    $th = 0;
                endif;
                
            endforeach;
        endforeach;
        $output .= "</tbody></table>";
        return do_shortcode( $output );
    }
}
