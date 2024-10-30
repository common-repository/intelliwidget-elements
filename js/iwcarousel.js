
( function ( $ ) {
    'use strict';
    
    function IwCarousel( element, options ) {
        // create local reference to object
        var _self = this;
        _self.element = $( element );
        _self.options = $.extend( {}, defaults, options );
                
        _self.getNext = function( el ) {
            
            if ( el.next().length ) {
                return el.next();
            } else {
                return _self.slides.first();
            }
        }
        
        _self.getPrev = function( el ) {
            
            if ( el.prev().length ) {
                return el.prev();
            } else {
                return _self.slides.last();
            }
        }
                
        _self.reorderSlides = function( new_slide, clone ){
            var i, j, ref = _self.count, clones;
            // reorder slides
            _self.ref = new_slide.css( 'order', 1 );
            if ( clone ){
                clones =  $().add( new_slide.clone()
                    .addClass( 'iwcarousel-clone' )
                    .css( 'order', 1 + ref )
                );
            }
            
            for ( i = j = 2; ( 2 <= ref ? j <= ref : j >= ref ); i = 2 <= ref ? ++j : --j ) {
                new_slide = _self.getNext( new_slide ).css( 'order', i );
                if ( clone ){
                    clones = clones.add( new_slide.clone()
                        .addClass( 'iwcarousel-clone' )
                        .css( 'order', i + ref )
                    );
                }
            }
            if ( clone ){
                // console.log( 'clones', clones );
                clones.appendTo( _self.iwcarousel );
            }
        }
        
        _self.shiftTo = function( pos ){
            
            if ( _self.animating ){
                return;
            }
                
            var currPos     = _self.ref.index(),
                ref         = _self.count,
                median      = Math.round( ref / 2 ),
                moves       = pos - currPos - 1,
                selected    = _self.slides.eq( pos ),
                new_slide   = _self.getPrev( selected ),
                dimension   = _self.vertical ? _self.ref.height() : _self.ref.width(),
                offset      = 0,
                clone       = 0;
            // abort if no moves required
            if ( !moves ){
                return;
            } 
            _self.animating = 1;
            if ( moves < -median ){
                moves += ref;
            } else if ( moves > median ){
                moves -= ref;
            }
            /**
             * calculate how to render move:
             * > 1 || < maxSlide : subtract ref
             */
            // console.log( 'calculating moves: ' + moves + ' maxSlide: ' + _self.maxSlide );
            if ( moves <= _self.maxSlide || moves > 1 ){
                clone = 1;
                if ( moves < _self.maxSlide || moves > 1 ){
                    moves -= ref;
                }
            }
            // console.log( 'new moves: ' + moves + ' clone: ' + ( clone ? 'yes' : 'no' ) );
            _self.reorderSlides( new_slide, clone ); 
            // calculate how far to transform
            offset = moves * dimension;
            // display reordered slides in transformed state
            _self.iwcarousel.removeClass( 'is-set' );
            if ( !_self.fade ){
                _self.iwcarousel.css( { transform: 'translate' + ( _self.vertical ? 'Y' : 'X' ) + '(' + offset + 'px)' } );
            }
            // wait a tiny bit so browser can redraw renumbered and transformed slides, then remove transformation
            return setTimeout( ( function() {
                _self.iwcarousel.addClass( 'is-set' ).removeAttr( 'style' );
                return _self.setActive();
            } ), 50 );
        }
                
        _self.shiftOne = function( dir ){
            // console.log( 'shifting ' + dir );
            if ( _self.playing ){
                _self.play();
            }
            if ( _self.animating || !_self.isVisible() ){
                return;
            }
            _self.animating = 1;
            var new_slide; 
            if ( 'prev' === dir ) {
                new_slide = _self.getPrev( _self.ref );
                _self.iwcarousel.addClass( 'is-reversing' );
            } else {
                new_slide = _self.getNext( _self.ref );
                _self.iwcarousel.removeClass( 'is-reversing' );
            }
            
            _self.reorderSlides( new_slide, 0 ); // pass zero - no need to clone for single shift

            _self.iwcarousel.removeClass( 'is-set' );
            return setTimeout( (function() {
                _self.iwcarousel.addClass( 'is-set' );
                return _self.setActive();
            }), 50 );
        }
                
        _self.play = function(){
            
            clearTimeout( _self.timeout );
            _self.playing = 1;
            _self.timeout = setTimeout( _self.shiftOne, _self.options.speed );
        }
        
        _self.stop = function (){
            clearTimeout( _self.timeout );
            _self.playing = 0;
        }
        
        _self.setupCtlNav = function(){
            
            var ctlNav = $( '<ol class="iwcarousel-ctl-nav">' ), 
                location    = _self.options.ctlNavContainer && $( _self.options.ctlNavContainer ).length ? $( _self.options.ctlNavContainer ) : _self.element;
            for ( var i = 0, ref = _self.count; i < ref; i++ ){
                ctlNav.append( '<li><a>' + i + '</a></li>' );
            }
            ctlNav.appendTo( location );
            // bind events to controls
            _self.ctlNav = ctlNav
                .on( 'click', 'li > a', function( e ){
                    e.preventDefault();
                    e.stopPropagation();
                    var target = $( e.target ).parent().index();
                    _self.shiftTo( target );
                    // stop slideshow
                    _self.stop();
                } );
        }
        
        _self.setupDirNav = function(){
            var dirNav      = $( '<ul class="iwcarousel-dir-nav"><li><a class="iwcarousel-prev" href="#">Previous</a></li><li><a class="iwcarousel-next" href="#">Next</a></li></ul>' ),
                location    = _self.options.dirNavContainer && $( _self.options.dirNavContainer ).length ? $( _self.options.dirNavContainer ) : _self.element;
            
            dirNav.appendTo( location );
            // bind to object
            _self.dirNav = dirNav
                .on( 'click', '.iwcarousel-prev,.iwcarousel-next', function( e ){
                    // console.log( 'iwcarousel dirNav click' );
                    e.preventDefault();
                    e.stopPropagation();
                    var target = $( e.target );
                    $( target ).hasClass( 'iwcarousel-next' ) && _self.shiftOne( 'next' ) || $( target ).hasClass( 'iwcarousel-prev' ) && _self.shiftOne( 'prev' );
                } );  
            
        }
        
        _self.isVisible = function(){
            return ( !document.hidden && _self.element.is( ':visible' ) && _self.element.css( 'visibility' ) !== 'hidden' );
        }
        
        _self.handleSwipe = function() {
            
            var absDeltax = Math.abs( _self.touchDeltaX ),
                direction;

            if ( absDeltax <= SWIPE_THRESHOLD ) {
                return;
            }

            direction = absDeltax / _self.touchDeltaX; 
            
            // swipe left
            if ( direction > 0 ) {
                _self.shiftOne( 'prev' );
            } 

            // swipe right
            if ( direction < 0 ) {
                _self.shiftOne( 'next' );
            }
        }
        
        _self.setupTouch = function() {

            if ( !this.hasTouch ) {
                return;
            }

            var start = function( event ) {
                    if ( _self.hasPointer && /touch|pen/.test( event.originalEvent.pointerType ) ) {
                        _self.touchStartX = event.originalEvent.clientX;
                    } else if ( !_self.hasPointer  ) {
                        _self.touchStartX = event.originalEvent.touches[ 0 ].clientX;
                    }
                },
                move = function( event ) {
                    // ensure swiping with one touch and not pinching
                    if  ( event.originalEvent.touches && event.originalEvent.touches.length > 1 ) {
                        _self.touchDeltaX = 0;
                    } else {
                        _self.touchDeltaX = event.originalEvent.touches[ 0 ].clientX - _self.touchStartX;
                    }
                },
                end = function( event ) {
                    if ( _self.hasPointer && /touch|pen/.test( event.originalEvent.pointerType ) ) {
                        _self.touchDeltaX = event.originalEvent.clientX - _self.touchStartX;
                    }

                    _self.handleSwipe();
                };
            
            // disable drag events on images
            _self.element.find( 'img' ).on( 'dragstart', function ( e ) {
                return e.preventDefault();
            } );

            if ( this._pointerEvent ) {
                
                _self.element.on( 'pointerdown', function ( event ) {
                    return start( event );
                } );
                _self.element.on( 'pointerup', function ( event ) {
                    return end( event );
                } );

                _self.element.addClass( 'pointer-event' );
                
            } else {
                
                _self.element.on( 'touchstart', function ( event ) {
                    return start( event );
                } );
                _self.element.on( 'touchmove', function ( event ) {
                    return move( event );
                } );
                _self.element.on( 'touchend', function ( event ) {
                    return end( event );
                } );
            }
        }
        
        _self.keydown = function( e ){
            
            // console.log( 'keydown event' );
            if ( /input|textarea/i.test( e.target.tagName ) ) {
                return;
            }
            
            switch ( e.which ) {
                /**
                 * FIXME - prevent tabbing to overflowed slides
                 *
                 case K_TAB:
                    // console.log( 'tab key', e );
                    if ( $.contains( _self.iwcarousel, e.target ) && !$( e.target ).is( ':visible' ) ){
                        return false;
                    }
                    break;
                */
                case K_ARROW_LEFT:
                    e.preventDefault();
                    _self.shiftOne( 'prev' );
                    break;

                case K_ARROW_RIGHT:
                    e.preventDefault();
                    _self.shiftOne( 'next' );
                    break;

                default:
            }
        }
        
        _self.setActive = function(){
            var activeClass = 'iwcarousel-active',
                currentIndex = _self.ref.index() + 1,
                activeIndex = currentIndex === _self.count ? 0 : currentIndex,
                controls;
            _self.slides.removeClass( activeClass ).eq( activeIndex ).addClass( activeClass );
            if ( _self.ctlNav ){
                controls = _self.ctlNav.find( 'a' ).removeClass( activeClass );
                controls.eq( activeIndex ).addClass( activeClass );
            }
        }
                        
        _self.init = function(){
            _self.element.css( { 'overflow':'hidden' } );
            _self.iwcarousel   = $( _self.options.container, _self.element ).first(); //.addClass( 'is-set' );
            _self.slides      = $( _self.options.selector, _self.iwcarousel );
            _self.count       = _self.slides.length;
            _self.ref         = _self.slides.last().css( 'order', 1 );
            _self.vertical    = _self.options.vertical || _self.element.hasClass( 'iwcarousel-vertical' );
            _self.fade        = _self.options.fade || _self.element.hasClass( 'iwcarousel-fade' );
            _self.animating   = 0;
            _self.touchStartX = 0;
            _self.touchDeltaX = 0;
            _self.hasTouch    = 'ontouchstart' in document.documentElement || navigator.maxTouchPoints > 0;
            _self.hasPointer  = Boolean( window.PointerEvent || window.MSPointerEvent );
            _self.setupTouch();
            _self.options.ctlNav && _self.setupCtlNav();
            _self.options.dirNav && _self.setupDirNav();
            _self.iwcarousel.on( 'webkitTransitionEnd transitionend', function(){
                // console.log( 'removing clones' );
                _self.animating = 0;
                $( '.iwcarousel-clone', _self.iwcarousel ).remove();
            } );
            $( document ).on( 'keydown', _self.keydown );
            var slideD = _self.vertical ? _self.ref.height() : _self.ref.width(),
                containerD = _self.vertical ? _self.iwcarousel.height() : _self.iwcarousel.width();
            _self.maxSlide = ( slideD ? Math.round( containerD / slideD ) : 1 ) - _self.count;
            _self.play();
            _self.setActive();
            //console.log( _self.ref.css( 'order' ) );
            //console.log( 'iwcarousel initialized. visible slides: ' + _self.visible + ' total slides: ' + _self.count );
        };
        
        _self.init();
    }
    
    var defaults = {
        'container':        "ul.slides",
        'selector':         "> li",
        'speed':            7000,
        'ctlNav':           true,
        'dirNav':           true,
        'ctlNavContainer':  '',
        'dirNavContainer':  '',
        'vertical':         false,
        'fade':             false
    },
    SWIPE_THRESHOLD = 40,
    K_ARROW_LEFT    = 37,   // KeyboardEvent.which value for left arrow key
    K_ARROW_RIGHT   = 39,   // KeyboardEvent.which value for right arrow key
    // future use
    M_BTN_RIGHT     = 3,    // MouseEvent.which value for the right button (assuming a right-handed mouse)
    K_ESCAPE        = 27,   // KeyboardEvent.which value for Escape (Esc) key
    K_SPACE         = 32,   // KeyboardEvent.which value for space key
    K_TAB           = 9,    // KeyboardEvent.which value for tab key
    K_ARROW_UP      = 38,   // KeyboardEvent.which value for up arrow key
    K_ARROW_DOWN    = 40;   // KeyboardEvent.which value for down arrow key
    
    $.fn.iwcarousel = function( options ) {
        
        if ( undefined === options ) { 
            options = {}; 
        }

        if ( "object" === typeof options ) {
            return this.each( function( ndx, el ) {
                if ( !$.data( el, 'iwcarousel' ) ) {
                    $.data( el, 'iwcarousel', 
                    new IwCarousel( this, options ) );
                }
            } );
        } else {
            // Helper strings to quickly perform functions on the slider
            var obj = $( this ).data( 'iwcarousel' );
            switch ( options ) {
                case "play": 
                    obj.play(); 
                    break;
                case "stop": 
                    obj.stop(); 
                    break;
                case "prev": 
                case "previous": 
                    obj.shiftOne( 'prev' ); 
                    break;
                case "next":
                    obj.shiftOne( 'next' ); 
                    break;
                default:
                    obj.shiftTo( parseInt( options ) );
            }
        }
 
    };
} )( jQuery );

