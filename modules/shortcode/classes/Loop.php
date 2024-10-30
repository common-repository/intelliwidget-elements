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


class IntelliWidgetShortCodeLoop {
    static function loop( $atts, $content ) {
        // do not process iwloop if already in a loop. This protects against adding multiple iwloop shortcodes
        if ( in_iw_loop() ):
			//echo 'in iw loop' . "\n";
			return '';
		elseif ( empty( $content ) ):
			//echo 'empty content' . "\n";
			return '';
		elseif ( FALSE !== strpos( 'iwloop', $content ) ):
			//echo ' content contains iwloop';
            return '';
		endif;
		//echo __METHOD__ . "\n" . print_r( iwinstance(), TRUE )  . "\n";
        //if ( iwinstance()->get( 'layout' ) ):
            //echo '<pre><code><small>' . print_r( iwquery(), TRUE ) . print_r( iwinstance(), TRUE ) . '</small></code></pre>';
            //return; //die( $debug );
        //endif;
			//echo 'buffering...' . "\n";
            //ob_start();
		$return = '';
        if ( iwquery()->have_posts() ): 
            while ( iwquery()->have_posts() ): 
                iwquery()->the_post(); 
                iwquery()->convert_listdata();
                $return .= do_shortcode( $content );
            endwhile;
        else:
			//echo ' no loop items.' . "\n";
        endif;
            //$return =  ob_get_clean();
			//echo 'finished buffer.' . "\n";
			return $return;
    }
}
