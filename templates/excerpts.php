<?php
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * excerpts.php - Template for basic featured post list
 *
 * This can be copied to a folder named 'intelliwidget' in your theme
 * to customize the output.
 *
 * @package IntelliWidget
 * @subpackage templates
 * @author Jason C Fleming
 * @copyright 2014-2015 Lilaea Media LLC
 * @access public
 */
?>
[iwloop][cell intelliwidget-excerpt-container row]
[cell2 col-md-3 intelliwidget-image-container][thumbnail bg class=ars link=1 class=ars][/cell2]
[cell2 col-md-9 ][datafield name=title tag=h3 tagc=intelliwidget-title link=1]
[datafield name=excerpt tag=span tagc=intelliwidget-excerpt] [cell3 tag=span link=1 more-link]Read More[/cell3][/cell2][/cell][/iwloop]
