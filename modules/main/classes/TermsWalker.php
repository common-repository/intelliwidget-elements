<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * class-intelliwidget-walker.php - IntelliWidgetMain Walker Class
 * based in part on code from Wordpress core post-template.php
 *
 * @package IntelliWidgetMain
 * @subpackage includes
 * @author Jason C Fleming
 * @copyright 2014-2015 Lilaea Media LLC
 * @access public
 */
class IntelliWidgetMainTermsWalker extends Walker {
	/**
	 * @see Walker::$tree_type
	 */
	var $tree_type = 'category';

	/**
	 * @see Walker::$db_fields
	 */
	var $db_fields = array ( 'parent' => 'parent', 'id' => 'term_taxonomy_id' );

	/**
	 * @see Walker::start_el()
	 */
	function start_el( &$output, $term, $depth = 0, $args = array(), $id = 0 ) {

        $matchval = ( int ) $term->term_taxonomy_id;
        $matcharr = iwinstance()->get( 'terms' );
            $in = in_array( $matchval, $matcharr );
            if ( $in || !iwinstance()->get( 'termssearch' ) || stristr( $term->name, iwinstance()->get( 'termssearch' ) ) ): 
                $pad = str_repeat( '-&nbsp;', $depth );
                $output .= "\t<option class=\"level-$depth\" value=\"$matchval\"";
                if ( $in )
                    $output .= ' selected';
                $output .= '>';
                $title = substr( $pad . $term->name, 0, 60 ) . ' (' . ucwords( str_replace( '_', ' ', $term->taxonomy ) ) . ')';
                $output .= esc_html( $title );
                $output .= "</option>\n";
            endif;
	}
}

