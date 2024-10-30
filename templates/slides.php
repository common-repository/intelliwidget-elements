<?php
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * slides.php - Template to generate ul li output. Useful for jQuery sliders.
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
[cell tag=ul slides menuclass][iwloop]<li class="slide">[datafield name=content]</li>[/iwloop][/cell]
