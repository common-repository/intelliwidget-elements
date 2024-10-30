<?php
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * menu.php - Template for Custom Page Menus
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
[cell tag=ul menuclass]
[iwloop][cell2 t=li itemclass][datafield name=title link=1][/cell2][/iwloop]
[/cell]

