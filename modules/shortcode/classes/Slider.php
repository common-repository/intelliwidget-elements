<?php
if ( !defined( 'ABSPATH' ) ) exit;
/*
    Plugin Name: IntelliWidget Pro Flexslider
    Plugin URI: http://www.lilaeamedia.com/iwtemplates/
    Description: Core styles, scripts and shortcodes for IntelliWidget Framework
    Version: 1.0.0
    Author: Lilaea Media
    Author URI: http://www.lilaeamedia.com/
   
    This file and all accompanying files (C) 2014-2016 Lilaea Media LLC except where noted. See license for details.
*/


class IntelliWidgetShortCodeSlider {
    static function iwcarousel( $atts ) {
        $a = shortcode_atts( array( 
            'vid'       => 0,
            'custom'    => 0, 
            'auto'      => 1, 
            'dir'       => 0, 
            'ctl'       => 0, 
            'ctlc'      => '',
            'dirc'      => '',
            'sel'       => '.iwcarousel', 
            'speed'     => 7000, 
            'fade'      => false,
            'parallax'  => 0,
            'vert'      => false,
        ), $atts );
        if ( !wp_script_is( 'iwcarousel', 'enqueued' ) )
            wp_enqueue_script( 'iwcarousel', trailingslashit( IWELEMENTS_URL ) . 'js/iwcarousel.js', array( 'jquery' ), IWELEMENTS_VERSION, TRUE );
        
        if ( empty( $a[ 'custom' ] ) ):
        $script = "<script>
        jQuery(document).ready(function(\$){
            \$('" . $a[ 'sel' ] . "').iwcarousel({
                'speed':            7000,
                'ctlNav':           " . ( $a[ 'ctl' ] ? 'true' : 'false' ) . ",
                'dirNav':           " . ( $a[ 'dir' ] ? 'true' : 'false' ) . ",
                'ctlNavContainer':  '" . $a[ 'ctlc' ] . "',
                'dirNavContainer':  '" . $a[ 'dirc' ] . "',
                'vertical':         " . ( $a[ 'vert' ] ? 'true' : 'false' ) . ",
                'fade':             " . ( $a[ 'fade' ] ? 'true' : 'false' ) . ",
                'parallaxFade':     " . $a[ 'parallax' ];
                
                
        if ( $a[ 'vid' ] ):
            /**
             * FIXME: add start, before and after callbacks
             *
            $script .= ",
                start: function( slider ){
                    // start playing first video
                    var thisSlide = slider.slides[ 0 ];
                    $( thisSlide ).find( 'video' ).each(function() {
                        //$( this ).prop( 'src', $( this ).data( 'src' ) );
                        this.currentTime = 0;
                        this.play();
                    });
                    slider.trigger( 'flexstart' );
                },
                before: function( slider ){
                    // start playing video before transition
                    var thisSlide = slider.slides[ slider.animatingTo ];
                        
                    $( thisSlide ).find( 'video' ).each(function() {
                        //$( this ).prop( 'src', $( this ).data( 'src' ) );
                        // reset playhead to beginning
                        this.currentTime = 0;
                        this.play();
                    });
                    slider.trigger( 'flexbefore' );
                },
                after: function( slider ){
                    // delay video pause to happen after fade/slide css transition
                    setTimeout( function(){
                        var thisSlide = slider.slides[ slider.prevSlide ];
                        $( thisSlide ).find( 'video' ).each(function() {
                            this.pause();
                            // reset playhead to beginning
                            this.currentTime = 0;
                        });
                    
                    }, 600 ); // this should match transition
                    slider.trigger( 'flexafter' );
                }";
                */
        endif;
        $script .= "
            });
        });";
        $script .= "</script>";
        //wp_add_inline_script( 'jquery', $script, $position = 'after' );      
        return $script;
        endif;
    }
    
    static function flexslider( $atts ) {
        $a = shortcode_atts( array( 
            'vid'       => 0,
            'custom'    => 0, 
            'auto'      => 1, 
            'dir'       => 0, 
            'ctl'       => 0, 
            'sel'       => '.flexslider', 
            'speed'     => 7000, 
            'anim'      => 'fade',
            'trans'     => '600',
            'min'       => 0,
            'max'       => 0,
            'move'      => 0,
            'width'     => 0,
            'margin'    => 0,
            'parallax'  => 0,
            'orient'    => 'horizontal',
        ), $atts );
        if ( !wp_script_is( 'flexslider-js', 'enqueued' ) )
            wp_enqueue_script( 'flexslider-js', trailingslashit( IWELEMENTS_URL ) . 'js/iw.flexslider.js', array( 'jquery' ), IWELEMENTS_VERSION, TRUE );
        if ( !wp_style_is( 'flexslider-style', 'enqueued' ) )
            wp_enqueue_style( 
                'flexslider-style', 
                trailingslashit( IWELEMENTS_URL ) . 'css/flexslider.css', 
                FALSE, 
                IWELEMENTS_VERSION 
            );
        
        if ( empty( $a[ 'custom' ] ) ):
            $script = "<script>
        jQuery(document).ready(function(\$){
            \$('" . $a[ 'sel' ] . "').flexslider({
                'controlNav':" . ( $a[ 'ctl' ] ? 'true' : 'false' ) . ",
                'slideshow':" . ( $a[ 'auto' ] ? 'true' : 'false' ) . ",
                'directionNav':" . ( $a[ 'dir' ] ? 'true' : 'false' ) . ",
                'slideshowSpeed': " . $a[ 'speed' ] . ",
                'animation': '" . $a[ 'anim' ] . "',
                'animationSpeed': " . $a[ 'trans' ] . ",
                'direction': '" . $a[ 'orient' ] . "',
                'parallaxFade': " . $a[ 'parallax' ];
                
        if ( $a[ 'width' ] )
            $script .= ",
                'minItems': " . $a[ 'min' ] . ",
                'maxItems': " . $a[ 'max' ] . ", 
                'move': " . $a[ 'move' ] . ",
                'itemWidth': " . $a[ 'width' ] . ",
                'itemMargin': " . $a[ 'margin' ];
                
        if ( $a[ 'vid' ] ):
            $script .= ",
                start: function( slider ){
                    // start playing first video
                    /* 
                    var thisSlide = slider.slides[ 0 ];
                    $( thisSlide ).find( 'video' ).each(function() {
                        //$( this ).prop( 'src', $( this ).data( 'src' ) );
                        this.currentTime = 0;
                        this.play();
                    });
                    */
						console.log( 'flexstart' );
                    slider.trigger( 'flexstart' );
                },
                before: function( slider ){
                    // start playing video before transition
                    var thisSlide = slider.slides[ slider.animatingTo ];
                        
                    $( thisSlide ).find( 'video' ).each(function() {
                        //$( this ).prop( 'src', $( this ).data( 'src' ) );
                        // reset playhead to beginning
                        //this.currentTime = 0;
                        this.play();
                    });
						console.log( 'flexbefore' );
                    slider.trigger( 'flexbefore' );
                },
                after: function( slider ){
                    // delay video pause to happen after fade/slide css transition
                    
					setTimeout( function(){
                        var thisSlide = slider.slides[ slider.prevSlide ];
                        $( thisSlide ).find( 'video' ).each(function() {
                            this.pause();
                            // reset playhead to beginning
                            this.currentTime = 0;
                        });
							console.log( 'flexafter' );
                    
                    }, " . $a[ 'trans' ] . " ); // this should match transition
					
                    slider.trigger( 'flexafter' );
                }";
        endif;
        $script .= "
            });
        });";
        $script .= "</script>";
        //wp_add_inline_script( 'jquery', $script, $position = 'after' );      
        return $script;
        endif;
    }
}
    
