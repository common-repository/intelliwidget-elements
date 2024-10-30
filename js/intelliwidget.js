/*!
 * intelliwidget.js - Javascript for the Admin.
 *
 * @package IntelliWidget
 * @subpackage js
 * @author Jason C Fleming
 * @copyright 2014-2016 Lilaea Media LLC
 * @access public
 *
 */
( function( $ ){
    "use strict";

    /**
     * Ajax Add new IntelliWidget Tab Section
     */
    function addProfile ( e ) { 
        // don't act like a link
        e.preventDefault();
        e.stopPropagation();
        var href        = $( e.target ).attr( 'href' ),
            postData    = urlToArray( href ); // build post data array from query string
        // add wp ajax action to array
        postData.action = 'iw_add';
        // send to wp
        postAjax( e.target, postData, callbackAdd );
        return false;  
    }
    
    function callbackAdd( element, response ) {
        //if ( window.IWAjax.debug ) { console.log( 'callbackAdd' ); }
        var $container  = $( element ).parent( '.inside' ).find( '.iw-profiles' ),
            $form       = $( response.form ).hide(),
            $tab        = $( response.tab ).hide();
        $container.append( $form );
        //if ( 'post' === window.IWAjax.objtype ) { bind_events( $form ); }
        $container.find( '.iw-tabs' ).append( $tab );
        $tab.show();
        $container.tabs( 'refresh' ).tabs( { active: $tab.index() } );
        initTabs();
    }
    
    function callbackDebug( el, resp ){
        if ( window.IWAjax.debug ) { 
            // console.log( 'in callbackDebug' ); 
        }
        $( el ).text( resp );
    }
    
    function callbackDelete( element ) { //, response ) {
        //if ( window.IWAjax.debug ) { console.log( 'callbackDelete' ); }
        var $profileform    = $( element ).parents( '.iw-profile' ).first(),
            $container      = $profileform.parent( '.iw-profiles' ),
            thisID          = $profileform.prop( 'id' ),
            // get box id 
            pre             = parseIds( thisID ),
            survivor = $profileform.index();
        $profileform.remove();
        //if ( window.IWAjax.debug ) { console.log( 'pre: ' + pre ); }
        $( '#iw_tab_' + pre ).remove();
        $container.tabs( 'refresh' );
        initTabs();
        $container.tabs( { active: survivor } );
    }
    
    function callbackMenu( element, response ) {
        //if ( window.IWAjax.debug ) { console.log( 'callbackMenus' ); }
        //if ( window.IWAjax.debug ) { console.log( element ); }
        $( element ).html( response ).prop( 'disabled', false ).multiSelect( 'refresh' );
    }
    
    function callbackMenus( element, response ) {
        if ( window.IWAjax.debug ) { console.log( 'in callbackMenus()' ); }
        if ( window.IWAjax.debug ) { console.log( element ); }
        $( element ).html( response ).find( '.intelliwidget-multiselect' ).multiSelect();
    }
    
    function callbackSave( element, response ) {
        //if ( window.IWAjax.debug ) { console.log( 'callbackSave' ); }
        // refresh profile form
        var $tab            = $( response.tab ),
            $curtab         = $( '.iw-tabs' ).find( '#' + $tab.prop( 'id' ) ),
            $profileform    = $( element ).parents( '.iw-profile' ).first(),
            $container      = $profileform.parent( '.iw-profiles' );
        $curtab.html( $tab.html() );
        $profileform.html( response.form ).find( '.intelliwidget-multiselect' ).multiSelect();
        //if ( 'post' === window.IWAjax.objtype ) { bind_events( $profileform ); }
        $container.tabs( 'refresh' ).tabs( { active: $curtab.index() } );
    }
    
    function checkKey( e ) {
        //if ( window.IWAjax.debug ) { console.log( 'checkKey' ); }
        if ( $( e.target ).hasClass( 'iw-menusearch' ) ) {
            // wait for typing to pause before submitting
            clearTimeout( $( e.target ).data( 'timer' ) );
            $( e.target ).data( 'timer', setTimeout( searchMenu, 400, e.target ) );
            if ( 13 === e.which ) { 
                e.stopPropagation();
                e.preventDefault();
                return false; 
            }
        } else if ( 13 === e.which ) { 
            e.stopPropagation();
            e.preventDefault();
            savePostData( e.target );
            return false;
        }
    }
    
    /**
     * Ajax Save Copy Page Input
     */
    function copyProfiles ( e ) { 
        // build post data array
        var postData = {};
        // find inputs for this profile
        postData.intelliwidget_widget_page_id = $( '#intelliwidget_widget_page_id' ).val();
        // add wp ajax action to array
        postData.action = 'iw_copy';
        //if ( window.IWAjax.debug ) { console.log( postData ); }
        // send to wp
        postAjax( e.target, postData, null );
        return false;  
    }
    
    /**
     * Ajax Delete IntelliWidget Tab Section
     */
    function deleteProfile ( e ) { 
        // don't act like a link
        e.preventDefault();
        e.stopPropagation();
        var href        = $( e.target ).attr( 'href' ), // get href from link
            postData    = urlToArray( href );     // build post data array from query string
        // add wp ajax action to array
        postData.action = 'iw_delete';
        // send to wp
        postAjax( e.target, postData, callbackDelete );
        return false;  
    }
    
    function endAjax( element, status ) {
        // reset status. use selector because element has been replaced
        var sel = '#' + $( element ).attr( 'id' );
        $( sel ).prop( 'disabled', false );
        setSpinner( sel, 'hidden' );
        $( sel ).parents( '.inside,.iw-profile' ).first().find( containerSel ).first().addClass( status );
    }
    
    function getDebug(){
        if ( window.IWAjax.debug ) { console.log( 'in getDebug' ); }
        postAjax( '#debug_output', { 'action': 'iw_debug' }, callbackDebug );
    }
    
    /**
     * Ajax Fetch multiselect menus
     */
    function getMenus( e )
    {
        if ( window.IWAjax.debug ) { console.log( 'in getMenus()' ); }
        /*jshint validthis: true */
        var parentSel       = is_widget_admin ?  '.widget' : '.iw-profile',
            $profileform    = $( e.target ).parents( parentSel ).first(),
            // parse id to get profile number
            thisID          = is_widget_admin ? $profileform.find( '.widget-id' ).val() : parseIds( $profileform.prop( 'id' ) ),
            // get profile selector
            menuSel         = is_widget_admin ? '#widget-' + thisID + '-menus' : '#intelliwidget_' + thisID + '_menus',
            // build post data array
            postData        = {};
        // only load once
        if ( $( menuSel ).has( 'select' ).length ) { 
            $( menuSel ).find( '.intelliwidget-multiselect' ).multiSelect( 'refresh' );
            return false; 
        }
        if ( is_widget_admin ) {
            postData[ 'widget-id' ] = $profileform.find( '.widget-id' ).val();
            // add wp ajax action to array
            postData.action = 'iw_widget_menus';
        } else {
            // find inputs for this profile
            $( 'input[type="hidden"]', $profileform ).each( function() {
                // add to post data
                postData[ $( this ).attr( 'id' ) ] = $( this ).val();
            } );
            // add wp ajax action to array
            postData.action = 'iw_menus';
        }
        //if ( window.IWAjax.debug ) { 
        //console.log( postData ); 
        //}
        // send to wp
        postAjax( menuSel, postData, callbackMenus );
    }
    
    function init() {
        if ( window.IWAjax.debug ) { console.log( 'in init()' ); }

        /**
         * EVENT LISTENERS ( delegate where possible )
         */
        // Add handler to check if panels were open before ajax save and reopen them
        $( document ).on( 'widget-updated', refreshOpenPanels );
        $( '.iw-profiles' ).tabs();
        $( 'body' ).on( 'click', '.iw-collapsible > .iw-toggle, .iw-collapsible > h4, .iw-collapsible > h3', function( e ) {
            //console.log( 'panel toggle');
            e.stopPropagation();
            var $p = $( this ).parent( '.iw-collapsible' ), 
                $sectionform = $( this ).parents( 'div.widget, div.iw-tabbed-section' ).first();
            if ( $p.hasClass( 'closed' ) ){
                //console.log( 'opening panel' );
                $p.removeClass( 'closed' );
                // get menus if this is post selection panel
                if ( $p.hasClass( 'panel-selection' ) ) {
                    //console.log( 'fetching selection panel' );
                    //$( '.panel-selection' ).not( $p ).each( function(){ $( this ).addClass( 'closed' ); } );
                    getMenus( e );
                }
            } else {
                //console.log( 'closing selection panel' );
                $p.addClass( 'closed' );
            }
            updateOpenPanels( $sectionform );
        } );
        $( 'body' ).on( 'change', '.iw-sortby-menu', function( e ){
            toggleSortOrderMenu( e.target );
        } );
        $( '.iw-sortby-menu' ).each( function( ndx, el ){
            toggleSortOrderMenu( el );
        } );
        // bind click events to edit page meta box buttons
        $( '#intelliwidget_main_meta_box,.main-meta-box' ).on( 'click', '.iw-save', function( e ){
            savePostData( e.target );
        } );
        // IW Pro gallery save event listener
        $( 'body' ).on( 'iwgallerysave', '.intelliwidget-media', function( e ){
            //if ( window.IWAjax.debug ) { console.log( 'gallery save event' ); }
            routeToForm( e.target );
        } );
        $( '#intelliwidget_post_meta_box' ).on( 'click', '.iw-cdfsave', saveCustomDataFields );    
        $( '#intelliwidget_main_meta_box,.main-meta-box' ).on( 'click', '.iw-copy', copyProfiles );    
        $( '#intelliwidget_main_meta_box,.main-meta-box' ).on( 'click', '.iw-add', addProfile );    
        $( '#intelliwidget_main_meta_box,.main-meta-box' ).on( 'click', '.iw-delete', deleteProfile );
        // update visibility of form inputs
        $( '#intelliwidget_main_meta_box,.main-meta-box' ).on( 'change', '.iw-control', function( e ){
            // console.log( e.target.id, $( e.target ).val() );
            savePostData( e.target ); // post metabox 
        } );
        $( 'body' ).on( 'change', '.intelliwidget-form-container .iw-widget-control', function( e ){
            // console.log( e.target.id, $( e.target ).val() );
            saveWidgetData( e.target ); // widget form 
        } );
        // bind keydown events
        $( '#intelliwidget_main_meta_box,.main-meta-box' ).on( 'keydown', 'input', checkKey );
        $( 'body' ).on( 'keydown', '.intelliwidget-form-container .iw-menusearch', function( e ) {
            if ( 13 === e.which ){
                //console.log( 'return key pressed' );
                e.stopPropagation();
                e.preventDefault();
                searchMenu( e.target );
                return false;
            }
            // wait for typing to pause before submitting
            clearTimeout( $( e.target ).data( 'timer' ) );
            $( e.target ).data( 'timer', setTimeout( searchMenu, 400, e.target ) );
            //return false;
        } );
        /**
         * Removing scripts from custom date fields --
        /**
         * manipulate IntelliWidget timestamp inputs
         * Adapted from wp-admin/js/post.js in Wordpress Core
         *
        if ( 'post' === window.IWAjax.objtype ) {
            // format visible timestamp values
            updateTimestampText( 'intelliwidget_event_date', false );
            updateTimestampText( 'intelliwidget_expire_date', false );
        }
        // bind edit links to reveal timestamp input form
        $( '#intelliwidget_post_meta_box' ).on( 'click', 'a.intelliwidget-edit-timestamp', function() {
            var field = $( this ).attr( 'id' ).split( '-', 1 );
            if ( $( '#' + field + '_div' ).is( ":hidden" ) ) {
                $( '#' + field + '_div' ).slideDown( 'fast' );
                $( '#' + field + '_mm' ).focus();
                $( this ).hide();
            }
            return false;
        } );
        // bind click to clear timestamp ( resets form to current date/time and clears date fields )
        $( '#intelliwidget_post_meta_box' ).on( 'click', '.intelliwidget-clear-timestamp', function() {
            var field = $( this ).attr( 'id' ).split( '-', 1 );
            $( '#' + field + '_div' ).slideUp( 'fast' );
            $( '#' + field + '_mm' ).val( $( '#' + field + '_cur_mm' ).val() );
            $( '#' + field + '_jj' ).val( $( '#' + field + '_cur_jj' ).val() );
            $( '#' + field + '_aa' ).val( $( '#' + field + '_cur_aa' ).val() );
            $( '#' + field + '_hh' ).val( $( '#' + field + '_cur_hh' ).val() );
            $( '#' + field + '_mn' ).val( $( '#' + field + '_cur_mn' ).val() );
            //$( '#' + field + '_og' ).prop( 'checked', false );
            $( '#' + field + '_timestamp' ).html( '' );
            $( '#' + field ).val( '' );
            $( 'a#' + field + '-edit' ).show();
            updateTimestampText( field, false );
            return false;
        } );
        // bind cancel button to reset values ( or empty string if orig field is empty ) 
        $( '#intelliwidget_post_meta_box' ).on( 'click', '.intelliwidget-cancel-timestamp', function() {
            var field = $( this ).attr( 'id' ).split( '-', 1 );
            $( '#' + field + '_div' ).slideUp( 'fast' );
            $( '#' + field + '_mm' ).val( $( '#' + field + '_hidden_mm' ).val() );
            $( '#' + field + '_jj' ).val( $( '#' + field + '_hidden_jj' ).val() );
            $( '#' + field + '_aa' ).val( $( '#' + field + '_hidden_aa' ).val() );
            $( '#' + field + '_hh' ).val( $( '#' + field + '_hidden_hh' ).val() );
            $( '#' + field + '_mn' ).val( $( '#' + field + '_hidden_mn' ).val() );
            //$( '#' + field + '_og' ).prop( 'checked', $( '#' + field + '_hidden_og' ).val() ? true : false );
            $( 'a#' + field + '-edit' ).show();
            updateTimestampText( field, false );
            return false;
        } );
        // bind 'Ok' button to update timestamp to inputs
        $( '#intelliwidget_post_meta_box' ).on( 'click', '.intelliwidget-save-timestamp', function () { 
            var field = $( this ).attr( 'id' ).split( '-', 1 );
            if ( updateTimestampText( field, true ) ) {
                $( '#' + field + '_div' ).slideUp( 'fast' );
                $( 'a#' + field + '-edit' ).show();
            }
            return false;
        } );
         * -- end date scripts
         */
        // bind right and left scroll arrows
        $( '.iw-profiles' ).on( 'click', '.iw-larr, .iw-rarr', function( e ) {
            e.preventDefault();
            e.stopPropagation();
            if ( $( this ).is( ':visible' ) ) {
                if ( $( this ).hasClass( 'iw-larr' ) ) { rightShiftTabs( $( this ) ); }
                else { leftShiftTabs( $( this ) ); }
            }
        } )
            .on( 'click', '.iw-tab-menu-toggle', toggleTabMenu )
            .on( 'click', '.iw-tab-menu-item', shiftToTab );
        
        $( '#the-list' ).on( 'wpListAddEnd', updateMetaMediaButtons );
        updateMetaMediaButtons();
        //$( '.intelliwidget-multiselect' ).multiSelect();
        // reflow tabs on resize
        $( window ).on( 'resize', resize );
        // END EVENT LISTENER BINDINGS
        
        // reveal intelliwidget profiles
        $( '.iw-profiles' ).slideDown();
        // set up tabs
        initTabs();
    }
    
    function resize(){
        // buffer resize events for single execution
        clearTimeout( resizeTimeout );
        resizeTimeout = setTimeout( function(){
            $( '.iw-profiles' ).each( function( pndx, p ) {
                var container = $( p );
                reflowTabs( container );
            });
        }, 100 );
    }
    
    function toggleTabMenu( e ){
        var menu = $( '.iw-tab-menu', e.target );
        if ( menu.hasClass( 'open' ) ){
            menu.stop().removeClass( 'open' ).fadeOut();
        } else {
            menu.stop().addClass( 'open' ).fadeIn();
        }
    }
    
    function updateMetaMediaButtons(){
        // remove all existing media buttons
        $( '#postcustomstuff' ).find( 'button.add_media' ).remove();
        // add media buttons for meta data inputs
        $( '#postcustomstuff' ).find( 'textarea' ).each( function( ndx, el ){
            addMediaButton( el );
        } );        
    }
    
    function addMediaButton( el )
    {
        var id = el.id,
            button = '<button type="button" id="insert_media_' + id + '" class="button insert-media add_media button-small" data-editor="' + id + '">Add Media</button>';
        $( button ).insertBefore( el );
    }
    
    /**
     * left pad with zeros 
     */
    function lpz( val ) {
        return ( '00' + val ).slice( -2 );
    }
    
    function parseIds( id ) {
            // parse id to get profile number
        var idparts         = id.split( '_' ),
            boxid           = idparts.pop(),
            objid           = idparts.pop();
        return objid + '_' + boxid;
    }
    
    /**
     * postAjax
     * Common function for all ajax calls
     */
    function postAjax( element, postData, callback ) {
        // console.log( 'in postAjax()' );
        //console.log( element );
        // if this is not widget page get post id
        startAjax( element );
        // handle nonce value
        if ( is_widget_admin ) {
            // customizer
            if ( 'undefined' !== typeof window.wp.customize && 'undefined' !== typeof window.wp.customize.Widgets ) {
                //if ( window.IWAjax.debug ) { console.log( 'customer widget nonce: ' + wp.customize.Widgets.data.nonce ); }
                postData.nonce = window.wp.customize.Widgets.data.nonce;
                postData.wp_customize = 'on';
            } else {
                // widget admin
                postData._wpnonce_widgets = $( '#_wpnonce_widgets' ).val();
            }
        } else {
            // post/term/options admin
            var id;
            if ( $( '#' + window.IWAjax.idfield ).length ) {
                id = $( '#' + window.IWAjax.idfield ).val();
            } else {
                id = parseInt( $( element ).attr( 'id' ).replace( /^intelliwidget_(\d+)_(\d+)/, "$1" ) );
            }
            postData[ window.IWAjax.idfield ] = id;
            postData[ 'iwpage_' + id ] = $( '#iwpage_' + id ).val();
        }
        // these two values instruct the server how to process the post
        postData.idfield = window.IWAjax.idfield;
        postData.objtype = window.IWAjax.objtype;
        //if ( window.IWAjax.debug ) { 
        // console.log( 'request', postData ); 
        //}
        //if ( window.IWAjax.debug ) { console.log( window.IWAjax ); }
        
        $.post(  
            // get ajax url from localized object
            window.IWAjax.ajaxurl,  
            //Data  
            postData,
            //on success function  
            function( response ) {
                //if ( window.IWAjax.debug ) { 
                // console.log( 'response', response ); 
            //}
                if ( 'string' === typeof response && response.match( /^fail/ ) ) {
                    // console.log( postData.action + ' failed' );
                    endAjax( element, 'failure' );
                } else {
                    // console.log( 'success' );
                    if ( callback ) {
                        callback( element, response );
                    }
                    endAjax( element, 'success' );
                    if ( !is_widget_admin ){
                        $( document ).trigger( 'widget-updated' );
                    }
                }
            }, 
            'json'
        ).fail( function( xhr, res, err ){ // xhr, res, err ) {
            // console.log( 'fail before post' );
            // console.log( err.message );
            // console.log( xhr );
            endAjax( element, 'failure' );
        } ).always( function(){
            if ( 'iw_debug' !== postData.action && window.IWAjax.debug ) {
                getDebug();
            }
        });  
        
        return false;  
    }
    
    function initTabs( index ) {
        $( '.iw-profiles' ).each( function( pndx, p ) {
            var container = $( p ),
                alltabs = $( '.iw-tab', container );
            //console.log( container );
            if ( undefined === index ){
                index = alltabs.length - 1;
            }
            //console.log( 'initTabs index: ', index );
            container.data( 'leftTabs', [] );
            container.data( 'rightTabs', [] );
            container.data( 'visTabs',   [] );
            alltabs.each( function( ndx, el ) {
                //console.log( 'initTabs sifting tabs:', el.id, container.data( 'rightTabs' ), container.data( 'visTabs' ) );
                if ( ndx > index ){
                    container.data( 'rightTabs' ).push( el.id );
                } else {
                    container.data( 'visTabs' ).push( el.id );
                }
            } );
            //console.log( 'initTabs rightTabs: ', container.data( 'rightTabs' ) );
            //console.log( 'initTabs visTabs: ', container.data( 'visTabs' ) );
            container.tabs( { active: index } );
            reflowTabs( container );
            initTabMenu( container );
        } );
    }
    
    function initTabMenu( container ){
            //console.log( container );
        var menu = $( '<ul>' );
        $( '.iw-tab', container ).each( function( ndx, el ){
            menu.append( '<li class="iw-tab-menu-item">' + $( el ).text() + '</li>' );
        })
        $( '.iw-tab-menu', container ).html( menu );
    }
    
    function leftShiftTabs( el ) {
        // right arrow clicked, shift all tabs to the left
        var container = el.parent( '.iw-profiles' ),
            leftMost;
        if ( ( leftMost = container.data( 'rightTabs' ).shift() ) ) {
            container.data( 'visTabs' ).push( leftMost );
        }
        reflowTabs( container );
    }
    
    function rightShiftTabs( el ) {
        // left arrow clicked, shift all tabs to the right
        var container = el.parent( '.iw-profiles' ),
            rightMost;
        if ( container.data( 'visTabs' ).length > 1 && ( rightMost = container.data( 'visTabs' ).pop() ) ) {
            container.data( 'rightTabs' ).unshift( rightMost );
        }
        reflowTabs( container );
    }
    
    function shiftToTab( e ){
        e.preventDefault();
        e.stopPropagation();
        initTabs( $( e.target ).index() );
        $( '.iw-tab-menu-toggle' ).trigger( 'click' );
    }
        
    function reflowTabs( container ) {
        //console.log( container );
        var count = 0,
            viewWidth = $( '.iw-tabs', container ).width() - 48,
            index = container.data( 'visTabs' ).length - 1,
            visWidth = 0,
            tabWidth,
            nextTab,
            leftTabs = container.data( 'visTabs' ).slice(); // remove reference
        //console.log( 'reflowTabs visTabs before reflow: ', container.data( 'visTabs' ) );
        $( '.iw-tab', container ).hide();
        if ( viewWidth ) {
            //console.log( 'reflowing left tabs...' );
            while( leftTabs.length ){
                nextTab = leftTabs.pop();
                //console.log( 'reflowTabs nextTab: ', nextTab );
                tabWidth = $( '#' + nextTab ).show().outerWidth();
                //console.log( 'reflowTabs viewWidth: ', viewWidth );
                //console.log( 'reflowTabs tabWidth: ', tabWidth );
                if ( visWidth + tabWidth > viewWidth ){
                    //console.log( 'reflowTabs too Big!: ', visWidth + tabWidth );
                    $( '#' + nextTab ).hide();
                    leftTabs.push( nextTab ); // put it back
                    break;
                } else {
                    //console.log( 'reflowTabs fits: ', visWidth + tabWidth );
                    visWidth += tabWidth;
                }
            }
            //console.log( 'reflowing right tabs...' );
            // now show rightTabs
            while( container.data( 'rightTabs' ).length ){
                nextTab = container.data( 'rightTabs' ).shift();
                //console.log( 'reflowTabs nextTab: ', nextTab );
                tabWidth = $( '#' + nextTab ).show().outerWidth();
                //console.log( 'reflowTabs viewWidth: ', viewWidth );
                //console.log( 'reflowTabs tabWidth: ', tabWidth );
                if ( visWidth + tabWidth > viewWidth ){
                    //console.log( 'reflowTabs too Big!: ', visWidth + tabWidth );
                    $( '#' + nextTab ).hide();
                    container.data( 'rightTabs' ).unshift( nextTab ); // put it back
                    break;
                } else {
                    container.data( 'visTabs' ).push( nextTab ); // put it back
                    //console.log( 'reflowTabs fits: ', visWidth + tabWidth );
                    visWidth += tabWidth;
                }
            }
        }
        container.data( 'leftTabs', leftTabs );
        //console.log( 'reflowTabs leftTabs after reflow: ', container.data( 'leftTabs' ) );
        //console.log( 'reflowTabs visTabs after reflow: ', container.data( 'visTabs' ) );
        //console.log( 'reflowTabs rightTabs after reflow: ', container.data( 'rightTabs' ) );
        setArrows( container );
    }
    
    function setArrows( container ) {
        
        //console.log( 'setting arrows...', container.data() );
        $( '.iw-larr, .iw-rarr', container ).css( 'visibility', 'hidden' );
        // if rightTabs, show >
        if ( container.data( 'rightTabs' ).length ) { 
            $( '.iw-rarr', container ).css( 'visibility', 'visible' ); 
        }
        // if leftTabs, show <
        if ( container.data( 'leftTabs' ).length ) { 
            $( '.iw-larr', container ).css( 'visibility', 'visible' ); 
        }
    }
    
    function refreshOpenPanels( e, widget ) { // a, b ) {

        for ( var key in openPanels ) {
            //if ( window.IWAjax.debug ) { if ( window.IWAjax.debug ) { console.log( 'refresh panels: ' + key ); } }
            if ( openPanels.hasOwnProperty( key ) && 1 === openPanels[ key ] ) {
                //if ( window.IWAjax.debug ) { if ( window.IWAjax.debug ) { console.log( 'refresh panels: ' + key ); } }
                $( '#' + key ).parent( '.iw-collapsible' ).removeClass( 'closed' );
                if ( widget ) {
                    $( '#' + key ).find( '.intelliwidget-multiselect' ).multiSelect();
                }
            }
        }
    }
    
    /*
     * hook for UW Pro gallery save
     * routes to save function depending on type of profile
     */ 
    function routeToForm( el ) {
        //if ( window.IWAjax.debug ) { console.log( 'in routeToForm()' ); }
        if ( $( el ).parents( 'div.widget' ).length ) { // this is a parent profile (widget) form
            saveWidgetData( el );
        } else { // this is a child profile (post metabox) form
            savePostData( el );
        }
    }
    
    function saveCustomDataFields( e ) {
        var postData = {};
        // find inputs for this profile
        $( '.intelliwidget-input' ).each( function() {
            postData[ $( this ).attr( 'id' ) ] = ( 'checkbox' === $( this ).attr( 'type' ) ? 
                ( $( this ).is( ':checked' ) ? 1 : 0 ) : $( this ).val() );
        } );
        // add wp ajax action to array
        postData.action = 'iw_cdfsave';
        //if ( window.IWAjax.debug ) { console.log( postData ); }
        // send to wp
        postAjax( e.target, postData, null );
    }
    
    /**
     * Ajax Save IntelliWidget Meta Box Data
     */
    function savePostData ( el ) { 
        /*jshint validthis: true */
        //if ( window.IWAjax.debug ) { console.log( 'in savePostData()' ); }
        var $profileform    = $( el ).parents( '.iw-profile' ).first(), // get profile selector
            $savebutton     = $profileform.find( '.iw-save' ), // get button selector
            thisID          = $profileform.prop( 'id' ),
            pre             = parseIds( thisID ),
            // build post data array
            postData        = {};
        //if ( window.IWAjax.debug ) { console.log( 'thisID: ' + thisID + ' pre: ' + pre ); }
        updateOpenPanels( $profileform );
        // special handling for post types ( array of checkboxes )
        postData[ 'intelliwidget_' + pre + '_post_types' ] = [];
        // find inputs for this profile
        $profileform.find( 'select,textarea,input[type=text],input[type=checkbox]:checked,input[type=hidden]' ).each( function() {
            // get field id
            var $el     = $( this ),
                field   = $el.prop( 'id' ),
                val     = $el.val();
            //if ( window.IWAjax.debug ) { console.log( 'fieldID: ' + fieldID ); }
            if ( field.indexOf( '_post_types' ) > 0 ) {
                // special handling for post types
                postData[ 'intelliwidget_' + pre + '_post_types' ].push( val );
            } else {
                // otherwise add to post data
                postData[ field ] = val;
            }
            if ( field.indexOf( '_menu_location' ) > 0 ) {
                // special case for menu_location
                if ( '' !== val ) { postData[ 'intelliwidget_' + pre + '_replace_widget' ] = 'nav_menu_location-' + val; }
            }
        } );
        // add wp ajax action to array
        postData.action = 'iw_save';
        //if ( window.IWAjax.debug ) { console.log( postData ); }
        // send to wp
        postAjax( $savebutton, postData, callbackSave );
        return false;  
    }
    
    function saveWidgetData ( el ) {
        /*jshint validthis: true */
        //if ( window.IWAjax.debug ) { console.log( 'in saveWidgetData()' ); }
        var $profileform = $( el ).parents( 'div.widget' ).first(),
            widgetid = $profileform.find( '.widget-id' ).val();
        //if ( window.IWAjax.debug ) { console.log( 'widget id: ' + widgetid ); }
        if ( 'undefined' !== typeof window.wp.customize && 'undefined' !== typeof window.wp.customize.Widgets ) {
            // customizer submits on change
            //return;
            var $control = window.wp.customize.Widgets.getWidgetFormControlForWidget( widgetid );
            $control.liveUpdateMode = false;
            $control.updateWidget();
            //if ( window.IWAjax.debug ) { console.log( $control ); }
        } else {
            updateOpenPanels( $profileform );
            window.wpWidgets.save( $profileform, 0, 0, 0 );
        }
    }
    
    function searchMenu( element ) {
        var searchVal   = $( element ).val(),
            parentSel   = is_widget_admin ?  '.widget' : '.iw-profile',
            $profile    = $( element ).parents( parentSel ).first(),
            searchID    = $( element ).attr( 'id' ),
            menuSel     = searchID.substring( 0, searchID.indexOf( 'search' ) ),
            parts       = menuSel.split( /[\-_]/ ),
            type        = parts.pop(),
            box_id      = parts.pop(),
            postTypes   = [],
            postData = {},
            site_id;
        //if ( window.IWAjax.debug ) { console.log( 'searchMenu: ' + searchID ); }
        postData[ searchID ]    = searchVal.replace( /[\n\r]/g, '' ); // strip newline/return;
        postData[ menuSel ]     = $( '#' + menuSel ).val();
        postData.menutype       = type;
        if ( is_widget_admin ) {
            postData[ 'widget-id' ] = 'intelliwidget-' + box_id;
            postData.action         = 'iw_widget_menu';
        } else {
            // if this is a child profile we must capture current post_types for search to work correctly
            $( 'input[name*="post_types"][type="hidden"]', $profile ).each( function() {
                // add to post data
                postTypes.push( $( this ).val() );
            } );
            if ( postTypes.length ){
                postData.post_types = postTypes;
            }
            // if multisite, pass profile site_id
            if ( ( site_id = $( 'input[name*="site_id"][type="hidden"]', $profile ))
               && site_id.length ){
                postData.site_id = site_id.val();
            }
            postData.intelliwidget_box_id = box_id;
            postData.action         = 'iw_menu';
        }
        postAjax( '#' + menuSel, postData, callbackMenu );
    }
    
    function setSpinner( element, status ) {
        var $spinner;
        if ( $( element ).hasClass( 'iw-save' ) ){
            $spinner = $( element ).parent( '.iw-save-container' ).siblings( '.spinner' ).first();
        } else if ( $( element ).hasClass( 'iw-delete' ) ) {
            $spinner = $( element ).parent( '.submitbox' ).siblings( '.spinner' ).first();
        } else if ( $( element ).hasClass( 'iw-toggle iw-collapsible' ) ) {
            $spinner = $( element ).parent( '.' ).siblings( '.spinner' ).first();
        } else {
            $spinner = $( element ).parents( '.inside' ).first().find( '.spinner' ).first();
        }
        $spinner.css( { 'visibility': status } ).show();
        if ( 'hidden' === status ) { $spinner.hide(); }
    }
        
    function startAjax( element ) {
        /* show/hide spinner */
        $( containerSel ).removeClass( 'success failure' );
        setSpinner( element, 'visible' );
        // disable the button until ajax returns
        $( element ).prop( 'disabled', true );    
    }
    
    function toggleSortOrderMenu( el ) {
        if ( -1 !== $.inArray( $( el ).val(), [ 'selection', 'rand' ] ) ){
            $( el ).siblings( '.iw-sortorder-menu' ).hide();
        } else {
            $( el ).siblings( '.iw-sortorder-menu' ).show();
        }
    }
    
    // store panel open state so it can persist across ajax refreshes
    function updateOpenPanels( container ) {
        container.find( '.inside' ).each( function() {
            var inside = $( this ).prop( 'id' );
            //if ( window.IWAjax.debug ) { console.log( 'update panels: ' + inside ); }
            openPanels[ inside ] = $( this ).parent( '.iw-collapsible' ).hasClass( 'closed' ) ? 0 : 1;
        } );
    }
    
    /** 
     * set visible timestamp and timestamp hidden inputs to form inputs 
     * only validates form if validate param is true
     * this allows values to be reset/cleared
     *//*
    function updateTimestampText( field, validate ) {
        // retrieve values from form
        var div             = '#' + field + '_div', 
            clearForm       = ( !validate && !$( '#' + field ).val() ),  
            aa              = $( '#' + field + '_aa' ).val(),
            mm              = lpz( $( '#' + field + '_mm' ).val() ), 
            jj              = lpz( $( '#' + field + '_jj' ).val() ), 
            hh              = lpz( $( '#' + field + '_hh' ).val() ), 
            mn              = lpz( $( '#' + field + '_mn' ).val() ),
            attemptedDate   = new Date( aa, mm - 1, jj, hh, mn ),
            _aa             = attemptedDate.getFullYear().toString(),
            _mm             = lpz( 1 + attemptedDate.getMonth() ), 
            _jj             = lpz( attemptedDate.getDate() ), 
            _hh             = lpz( attemptedDate.getHours() ), 
            _mn             = lpz( attemptedDate.getMinutes() ); //,
        //if ( window.IWAjax.debug ) { console.log( ' field: ' + div + ' aa: ' + aa + ' mm: ' + mm + ' jj: ' + jj + ' hh: ' + hh + ' mn: ' + mn ); }
        //if ( window.IWAjax.debug ) { console.log( ' attempted: ' + div + ' aa: ' + _aa + ' mm: ' + _mm + ' jj: ' + _jj + ' hh: ' + _hh + ' mn: ' + _mn ); }
        if ( ! $( div ).length ) { return true; }
        // construct date object
        // validate inputs by comparing to date object
        if ( _aa !== aa || _mm !== mm || _jj !== jj || _hh !== hh || _mn !== mn ) {
            // date object returned invalid
            // if validating, display error and return invalid
                if ( true === validate ) { //&& !og ) {
                    $( div ).addClass( 'form-invalid' );
                    $( '.iw-cdfsave' ).prop( 'disabled', true );
                    return false;
                }
                // otherwise clear form ( value is/was null )  
                clearForm = true;
        }
        // date validated or ignored, reset invalid class
        $( div ).removeClass( 'form-invalid' );
        
        $( '.iw-cdfsave' ).prop( 'disabled', false );
        if ( clearForm ) {
            // replace date fields with empty string
            $( '#' + field ).val( '' );
        } else {
            // format displayed date string from form values
            $( '#' + field + '_timestamp' ).html( 
                '<b>' +
                $( 'option[value="' + $( '#' + field + '_mm' ).val() + '"]', '#' + field + '_mm' ).text() + ' ' +
                jj + ', ' +
                aa + ' @ ' +
                hh + ':' +
                mn + '</b> '
            );
            // format date field from form values
            $( '#' + field ).val( 
                aa + '-' +
                $( '#' + field + '_mm' ).val() + '-' +
                jj + ' ' +
                hh + ':' +
                mn                    
            );
        }
        return true;
    }
    */
    /**
     * nice little url -> name:value pairs codec
     */
    function urlToArray( url ) {
        var pair, i, request = {},
            pairs = url.substring( url.indexOf( '?' ) + 1 ).split( '&' );
        for ( i = 0; i < pairs.length; i++ ) {
            pair = pairs[ i ].split( '=' );
            request[ decodeURIComponent( pair[ 0 ] ) ] = decodeURIComponent( pair[ 1 ] );
        }
        return request;
    }
    
    var openPanels      = {},
        containerSel    = '.iw-copy-container,.iw-save-container,.iw-cdf-container',
        is_widget_admin = ( 'widget' === window.IWAjax.objtype ),
        resizeTimeout    = 1;
    $( document ).ready( function() {
        if ( window.IWAjax.debug ) { console.log( 'document ready()' ); }
        init();
    } );
} )( jQuery );


/*
* MultiSelect v0.9.11
* Copyright (c) 2012 Louis Cuny
*
* This program is free software. It comes without any warranty, to
* the extent permitted by applicable law. You can redistribute it
* and/or modify it under the terms of the Do WTF You Want
* To Public License, Version 2, as published by Sam Hocevar. See
* http://sam.zoy.org/wtfpl/COPYING for more details.
*/

( function ( $ ) {

  "use strict";


 /* MULTISELECT CLASS DEFINITION
  * ====================== */
 /*jslint bitwise: true */
  var MultiSelect = function ( element, options ) {
    this.options = options;
    this.$element = $( element );
    this.$container = $( '<div/>', { 'class': "ms-container" } );
    this.$selectableContainer = $( '<div/>', { 'class': 'ms-selectable' } );
    this.$selectionContainer = $( '<div/>', { 'class': 'ms-selection' } );
    this.$selectableUl = $( '<ul/>', { 'class': "ms-list", 'tabindex' : '-1' } );
    this.$selectionUl = $( '<ul/>', { 'class': "ms-list", 'tabindex' : '-1' } );
    this.scrollTo = 0;
    this.elemsSelector = 'li:visible:not( .ms-optgroup-label,.ms-optgroup-container,.' + options.disabledClass + ' )';
  };

  MultiSelect.prototype = {
    constructor: MultiSelect,

    init: function(){
      var that = this,
          ms = this.$element;

      if ( ms.next( '.ms-container' ).length === 0 ){
        ms.css( { position: 'absolute', left: '-9999px' } );
        ms.attr( 'id', ms.attr( 'id' ) ? ms.attr( 'id' ) : Math.ceil( Math.random()*1000 ) + 'multiselect' );
        this.$container.attr( 'id', 'ms-' + ms.attr( 'id' ) );
        this.$container.addClass( that.options.cssClass );
        ms.find( 'option' ).each( function(){
          that.generateLisFromOption( this );
        } );

        this.$selectionUl.find( '.ms-optgroup-label' ).hide();

        if ( that.options.selectableHeader ){
          that.$selectableContainer.append( that.options.selectableHeader );
        }
        that.$selectableContainer.append( that.$selectableUl );
        if ( that.options.selectableFooter ){
          that.$selectableContainer.append( that.options.selectableFooter );
        }

        if ( that.options.selectionHeader ){
          that.$selectionContainer.append( that.options.selectionHeader );
        }
        that.$selectionContainer.append( that.$selectionUl );
        if ( that.options.selectionFooter ){
          that.$selectionContainer.append( that.options.selectionFooter );
        }

        that.$container.append( that.$selectableContainer );
        that.$container.append( that.$selectionContainer );
        ms.after( that.$container );

        that.activeMouse( that.$selectableUl );
        that.activeKeyboard( that.$selectableUl );

        var action = that.options.dblClick ? 'dblclick' : 'click';

        that.$selectableUl.on( action, '.ms-elem-selectable', function(){
          that.select( $( this ).data( 'ms-value' ) );
        } );
        that.$selectionUl.on( action, '.ms-elem-selection', function(){
          that.deselect( $( this ).data( 'ms-value' ) );
        } );

        that.activeMouse( that.$selectionUl );
        that.activeKeyboard( that.$selectionUl );

        ms.on( 'focus', function(){
          that.$selectableUl.focus();
        } );
      }

      var selectedValues = ms.find( 'option:selected' ).map( function(){ return $( this ).val(); } ).get();
      that.select( selectedValues, 'init' );

      if ( typeof that.options.afterInit === 'function' ) {
        that.options.afterInit.call( this, this.$container );
      }
    },

    generateLisFromOption : function( option, index ){ //, $container ){
      var that = this,
          ms = that.$element,
          attributes = "",
          $option = $( option );

      for ( var cpt = 0; cpt < option.attributes.length; cpt++ ){
        var attr = option.attributes[ cpt ];

        if( attr.name !== 'value' && attr.name !== 'disabled' ){
          attributes += attr.name + '="' + attr.value + '" ';
        }
      }
      var selectableLi = $( '<li ' + attributes + '><span>' + that.escapeHTML( $option.text() ) + '</span></li>' ),
          selectedLi = selectableLi.clone(),
          value = $option.val(),
          elementId = that.sanitize( value );

      selectableLi
        .data( 'ms-value', value )
        .addClass( 'ms-elem-selectable' )
        .attr( 'id', elementId + '-selectable' );

      selectedLi
        .data( 'ms-value', value )
        .addClass( 'ms-elem-selection' )
        .attr( 'id', elementId + '-selection' )
        .hide();

      if ( $option.prop( 'disabled' ) || ms.prop( 'disabled' ) ){
        selectedLi.addClass( that.options.disabledClass );
        selectableLi.addClass( that.options.disabledClass );
      }

      var $optgroup = $option.parent( 'optgroup' );

      if ( $optgroup.length > 0 ){
        var optgroupLabel = $optgroup.attr( 'label' ),
            optgroupId = that.sanitize( optgroupLabel ),
            $selectableOptgroup = that.$selectableUl.find( '#optgroup-selectable-' + optgroupId ),
            $selectionOptgroup = that.$selectionUl.find( '#optgroup-selection-' + optgroupId );

        if ( $selectableOptgroup.length === 0 ){
          var optgroupContainerTpl = '<li class="ms-optgroup-container"></li>',
              optgroupTpl = '<ul class="ms-optgroup"><li class="ms-optgroup-label"><span>' + optgroupLabel + '</span></li></ul>';

          $selectableOptgroup = $( optgroupContainerTpl );
          $selectionOptgroup = $( optgroupContainerTpl );
          $selectableOptgroup.attr( 'id', 'optgroup-selectable-' + optgroupId );
          $selectionOptgroup.attr( 'id', 'optgroup-selection-' + optgroupId );
          $selectableOptgroup.append( $( optgroupTpl ) );
          $selectionOptgroup.append( $( optgroupTpl ) );
          if ( that.options.selectableOptgroup ){
            $selectableOptgroup.find( '.ms-optgroup-label' ).on( 'click', function(){
              var values = $optgroup.children( ':not(:selected, :disabled )' ).map( function(){ return $( this ).val(); } ).get();
              that.select( values );
            } );
            $selectionOptgroup.find( '.ms-optgroup-label' ).on( 'click', function(){
              var values = $optgroup.children( ':selected:not(:disabled )' ).map( function(){ return $( this ).val(); } ).get();
              that.deselect( values );
            } );
          }
          that.$selectableUl.append( $selectableOptgroup );
          that.$selectionUl.append( $selectionOptgroup );
        }
        index = typeof index === 'undefined' ? $selectableOptgroup.find( 'ul' ).children().length : index + 1;
        selectableLi.insertAt( index, $selectableOptgroup.children() );
        selectedLi.insertAt( index, $selectionOptgroup.children() );
      } else {
        index = typeof index === 'undefined' ? that.$selectableUl.children().length : index;

        selectableLi.insertAt( index, that.$selectableUl );
        selectedLi.insertAt( index, that.$selectionUl );
      }
    },

    addOption: function( options ){
      var that = this;

      if ( options.value !== undefined && options.value !== null ){
        options = [ options ];
      } 
      $.each( options, function( ndx, option ){
        if ( option.value !== undefined && option.value !== null &&
            that.$element.find( "option[value='" + option.value + "']" ).length === 0 ){
          var $option = $( '<option value="' + option.value + '">' + option.text + '</option>' ),
              index = parseInt( ( typeof option.index === 'undefined' ? that.$element.children().length : option.index ) ),
              $container = typeof option.nested === 'undefined' ? that.$element : $( "optgroup[label='" + option.nested + "']" );

          $option.insertAt( index, $container );
          that.generateLisFromOption( $option.get( 0 ), index, option.nested );
        }
      } );
    },

    escapeHTML: function( text ){
      return $( "<div>" ).text( text ).html();
    },

    activeKeyboard: function( $list ){
      var that = this;

      $list.on( 'focus', function(){
        $( this ).addClass( 'ms-focus' );
      } )
      .on( 'blur', function(){
        $( this ).removeClass( 'ms-focus' );
      } )
      .on( 'keydown', function( e ){
        switch ( e.which ) {
          case 40:
          case 38:
            e.preventDefault();
            e.stopPropagation();
            that.moveHighlight( $( this ), ( e.which === 38 ) ? -1 : 1 );
            return;
          case 37:
          case 39:
            e.preventDefault();
            e.stopPropagation();
            that.switchList( $list );
            return;
          case 9:
            if( that.$element.is( '[tabindex]' ) ){
              e.preventDefault();
              var tabindex = parseInt( that.$element.attr( 'tabindex' ), 10 );
              tabindex = ( e.shiftKey ) ? tabindex-1 : tabindex + 1;
              $( '[tabindex="' + ( tabindex ) + '"]' ).focus();
              return;
            }else{
              if( e.shiftKey ){
                that.$element.trigger( 'focus' );
              }
            }
        }
        if( $.inArray( e.which, that.options.keySelect ) > -1 ){
          e.preventDefault();
          e.stopPropagation();
          that.selectHighlighted( $list );
          return;
        }
      } );
    },

    moveHighlight: function( $list, direction ){
      var $elems = $list.find( this.elemsSelector ),
          $currElem = $elems.filter( '.ms-hover' ),
          $nextElem = null,
          elemHeight = $elems.first().outerHeight(),
          containerHeight = $list.height(),
          //containerSelector = '#' + this.$container.prop( 'id' ),
          $optgroupUl,
          $optgroupLi,
          $nextOptgroupLi,
          $prevOptgroupLi;

      $elems.removeClass( 'ms-hover' );
      if ( direction === 1 ){ // DOWN

        $nextElem = $currElem.nextAll( this.elemsSelector ).first();
        if ( $nextElem.length === 0 ){
          $optgroupUl = $currElem.parent();

          if ( $optgroupUl.hasClass( 'ms-optgroup' ) ){
            $optgroupLi = $optgroupUl.parent();
            $nextOptgroupLi = $optgroupLi.next( ':visible' );

            if ( $nextOptgroupLi.length > 0 ){
              $nextElem = $nextOptgroupLi.find( this.elemsSelector ).first();
            } else {
              $nextElem = $elems.first();
            }
          } else {
            $nextElem = $elems.first();
          }
        }
      } else if ( direction === -1 ){ // UP

        $nextElem = $currElem.prevAll( this.elemsSelector ).first();
        if ( $nextElem.length === 0 ){
          $optgroupUl = $currElem.parent();

          if ( $optgroupUl.hasClass( 'ms-optgroup' ) ){
            $optgroupLi = $optgroupUl.parent();
            $prevOptgroupLi = $optgroupLi.prev( ':visible' );

            if ( $prevOptgroupLi.length > 0 ){
              $nextElem = $prevOptgroupLi.find( this.elemsSelector ).last();
            } else {
              $nextElem = $elems.last();
            }
          } else {
            $nextElem = $elems.last();
          }
        }
      }
      if ( $nextElem.length > 0 ){
        $nextElem.addClass( 'ms-hover' );
        var scrollTo = $list.scrollTop() + $nextElem.position().top - containerHeight / 2 + elemHeight / 2;
        $list.scrollTop( scrollTo );
      }
    },

    selectHighlighted: function( $list ){
      var $elems = $list.find( this.elemsSelector ),
          $highlightedElem = $elems.filter( '.ms-hover' ).first();

      if ( $highlightedElem.length > 0 ){
        if ( $list.parent().hasClass( 'ms-selectable' ) ){
          this.select( $highlightedElem.data( 'ms-value' ) );
        } else {
          this.deselect( $highlightedElem.data( 'ms-value' ) );
        }
        $elems.removeClass( 'ms-hover' );
      }
    },

    switchList: function( $list ){
      $list.blur();
      this.$container.find( this.elemsSelector ).removeClass( 'ms-hover' );
      if ( $list.parent().hasClass( 'ms-selectable' ) ){
        this.$selectionUl.focus();
      } else {
        this.$selectableUl.focus();
      }
    },

    activeMouse: function(){ // $list ){
      var that = this;

      $( 'body' ).on( 'mouseenter', that.elemsSelector, function(){
        $( this ).parents( '.ms-container' ).find( that.elemsSelector ).removeClass( 'ms-hover' );
        $( this ).addClass( 'ms-hover' );
      } );

      $( 'body' ).on( 'mouseleave', that.elemsSelector, function () {
          $( this ).parents( '.ms-container' ).find( that.elemsSelector ).removeClass( 'ms-hover' );
      } );
    },

    refresh: function() {
      this.destroy();
      this.$element.multiSelect( this.options );
    },

    destroy: function(){
      $( "#ms-" + this.$element.attr( "id" ) ).remove();
      this.$element.css( 'position', '' ).css( 'left', '' );
      this.$element.removeData( 'multiselect' );
    },

    select: function( value, method ){
      if ( typeof value === 'string' ){ value = [ value ]; }

      var that = this,
          ms = this.$element,
          msIds = $.map( value, function( val ){ return( that.sanitize( val ) ); } ),
          selectables = this.$selectableUl.find( '#' + msIds.join( '-selectable, #' ) + '-selectable' ).filter( ':not(.' + that.options.disabledClass + ')' ),
          selections = this.$selectionUl.find( '#' + msIds.join( '-selection, #' ) + '-selection' ).filter( ':not(.' + that.options.disabledClass + ')' ),
          options = ms.find( 'option:not(:disabled )' ).filter( function(){ return( $.inArray( this.value, value ) > -1 ); } );

      if ( method === 'init' ){
        selectables = this.$selectableUl.find( '#' + msIds.join( '-selectable, #' ) + '-selectable' );
        selections = this.$selectionUl.find( '#' + msIds.join( '-selection, #' ) + '-selection' );
      }

      if ( selectables.length > 0 ){
        selectables.addClass( 'ms-selected' ).hide();
        selections.addClass( 'ms-selected' ).show();

        options.prop( 'selected', true );

        that.$container.find( that.elemsSelector ).removeClass( 'ms-hover' );

        var selectableOptgroups = that.$selectableUl.children( '.ms-optgroup-container' );
        if ( selectableOptgroups.length > 0 ){
          selectableOptgroups.each( function(){
            var selectablesLi = $( this ).find( '.ms-elem-selectable' );
            if ( selectablesLi.length === selectablesLi.filter( '.ms-selected' ).length ){
              $( this ).find( '.ms-optgroup-label' ).hide();
            }
          } );

          var selectionOptgroups = that.$selectionUl.children( '.ms-optgroup-container' );
          selectionOptgroups.each( function(){
            var selectionsLi = $( this ).find( '.ms-elem-selection' );
            if ( selectionsLi.filter( '.ms-selected' ).length > 0 ){
              $( this ).find( '.ms-optgroup-label' ).show();
            }
          } );
        } else {
          if ( that.options.keepOrder && method !== 'init' ){
            var selectionLiLast = that.$selectionUl.find( '.ms-selected' );
            if( ( selectionLiLast.length > 1 ) && ( selectionLiLast.last().get( 0 ) !== selections.get( 0 ) ) ) {
              selections.insertAfter( selectionLiLast.last() );
            }
          }
        }
        if ( method !== 'init' ){
          ms.trigger( 'change' );
          if ( typeof that.options.afterSelect === 'function' ) {
            that.options.afterSelect.call( this, value );
          }
        }
      }
    },

    deselect: function( value ){
      if ( typeof value === 'string' ){ value = [ value ]; }

      var that = this,
          ms = this.$element,
          msIds = $.map( value, function( val ){ return( that.sanitize( val ) ); } ),
          selectables = this.$selectableUl.find( '#' + msIds.join( '-selectable, #' ) + '-selectable' ),
          selections = this.$selectionUl.find( '#' + msIds.join( '-selection, #' ) + '-selection' )
            .filter( '.ms-selected' )
            .filter( ':not( .' + that.options.disabledClass + ' )' ),
          options = ms.find( 'option' ).filter( function(){ return( $.inArray( this.value, value ) > -1 ); } );

      if ( selections.length > 0 ){
        selectables.removeClass( 'ms-selected' ).show();
        selections.removeClass( 'ms-selected' ).hide();
        options.prop( 'selected', false );

        that.$container.find( that.elemsSelector ).removeClass( 'ms-hover' );

        var selectableOptgroups = that.$selectableUl.children( '.ms-optgroup-container' );
        if ( selectableOptgroups.length > 0 ){
          selectableOptgroups.each( function(){
            var selectablesLi = $( this ).find( '.ms-elem-selectable' );
            if ( selectablesLi.filter( ':not( .ms-selected )' ).length > 0 ){
              $( this ).find( '.ms-optgroup-label' ).show();
            }
          } );

          var selectionOptgroups = that.$selectionUl.children( '.ms-optgroup-container' );
          selectionOptgroups.each( function(){
            var selectionsLi = $( this ).find( '.ms-elem-selection' );
            if ( selectionsLi.filter( '.ms-selected' ).length === 0 ){
              $( this ).find( '.ms-optgroup-label' ).hide();
            }
          } );
        }
        ms.trigger( 'change' );
        if ( typeof that.options.afterDeselect === 'function' ) {
          that.options.afterDeselect.call( this, value );
        }
      }
    },

    select_all: function(){
      var ms = this.$element,
          values = ms.val();

      ms.find( 'option:not( ":disabled" )' ).prop( 'selected', true );
      this.$selectableUl.find( '.ms-elem-selectable' ).filter( ':not( .' + this.options.disabledClass + ' )' ).addClass( 'ms-selected' ).hide();
      this.$selectionUl.find( '.ms-optgroup-label' ).show();
      this.$selectableUl.find( '.ms-optgroup-label' ).hide();
      this.$selectionUl.find( '.ms-elem-selection' ).filter( ':not( .' + this.options.disabledClass + ' )' ).addClass( 'ms-selected' ).show();
      this.$selectionUl.focus();
      ms.trigger( 'change' );
      if ( typeof this.options.afterSelect === 'function' ) {
        var selectedValues = $.grep( ms.val(), function( item ){
          return $.inArray( item, values ) < 0;
        } );
        this.options.afterSelect.call( this, selectedValues );
      }
    },

    deselect_all: function(){
      var ms = this.$element,
          values = ms.val();

      ms.find( 'option' ).prop( 'selected', false );
      this.$selectableUl.find( '.ms-elem-selectable' ).removeClass( 'ms-selected' ).show();
      this.$selectionUl.find( '.ms-optgroup-label' ).hide();
      this.$selectableUl.find( '.ms-optgroup-label' ).show();
      this.$selectionUl.find( '.ms-elem-selection' ).removeClass( 'ms-selected' ).hide();
      this.$selectableUl.focus();
      ms.trigger( 'change' );
      if ( typeof this.options.afterDeselect === 'function' ) {
        this.options.afterDeselect.call( this, values );
      }
    },

    sanitize: function( value ){
      var hash = 0, i, character;
      if ( value.length === 0 ) { return hash; }
      var ls = 0;
      for ( i = 0, ls = value.length; i < ls; i++ ) {
        character  = value.charCodeAt( i );
        hash  = ( ( hash << 5 ) - hash ) + character;
        hash |= 0; // Convert to 32bit integer
      }
      return hash;
    }
  };

  /* MULTISELECT PLUGIN DEFINITION
   * ======================= */

  $.fn.multiSelect = function () {
    var option = arguments[ 0 ],
        args = arguments;

    return this.each( function () {
      var $this = $( this ),
          data = $this.data( 'multiselect' ),
          options = $.extend( {}, $.fn.multiSelect.defaults, $this.data(), typeof option === 'object' && option );

      if ( !data ){ $this.data( 'multiselect', ( data = new MultiSelect( this, options ) ) ); }

      if ( typeof option === 'string' ){
        data[ option ]( args[ 1 ] );
      } else {
        data.init();
      }
    } );
  };

  $.fn.multiSelect.defaults = {
    keySelect: [ 32 ],
    selectableOptgroup: false,
    disabledClass : 'disabled',
    dblClick : false,
    keepOrder: false,
    cssClass: ''
  };

  $.fn.multiSelect.Constructor = MultiSelect;

  $.fn.insertAt = function( index, $parent ) {
    return this.each( function() {
      if ( index === 0 ) {
        $parent.prepend( this );
      } else {
        $parent.children().eq( index - 1 ).after( this );
      }
    } );
};

} )( window.jQuery );