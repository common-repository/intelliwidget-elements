/**
 * adds gallery functionality to IntelliWidget Profiles
 */
( function( $, _ ){
    'use strict';
    
    /*
     * IntelliWidgetGalleryPost - subclass of wp.media.MediaFrame.Post
     */
    var IntelliWidgetGalleryPost = window.wp.media.view.MediaFrame.Post.extend( {
        
        initialize: function() {
            //console.log( 'activating IntelliWidgetGalleryPost' );
			window.wp.media.view.MediaFrame.Post.prototype.initialize.apply( this, arguments );
        },
        // only create the states we need for IntelliWidget
        createStates: function() {
            //console.log( 'creating states for IntelliWidgetGalleryPost' );
            var options = this.options;
    
            this.states.add([
                // Main states.
                new window.wp.media.controller.EditImage( { model: options.editImage } ),
    
                // Gallery states.
			    new IntelliWidgetGalleryEdit({
                    library: options.selection,
                    editing: options.editing,
                    menu:    'gallery'
                }),
    
                new IntelliWidgetGalleryAdd({
                    filterable: 'all',
                })
    
            ]);
            //console.log( 'done creating states for IntelliWidgetGalleryPost' );
        }
    }),
    
    // override GalleryEdit view
    IntelliWidgetGalleryEdit = window.wp.media.controller.GalleryEdit.extend( {
        initialize: function() {
            //console.log( 'initializing IntelliWidgetGalleryEdit' );
            
            // call original prototype
            window.wp.media.controller.GalleryEdit.prototype.initialize.apply( this, arguments );
        },
        activate: function() {
            //console.log( 'activating IntelliWidgetGalleryEdit' );
            //window.wp.media.controller.GalleryEdit.prototype.activate.apply( this, arguments );
            var library = this.get('library');
    
            // allow all mime types not just images.
            library.props.unset( 'type' );
            // Watch for uploaded attachments.
            library.observe( window.wp.Uploader.queue );
            // customize button
            /*
            this.frame.on( 'toolbar:render:gallery-edit', function(){
                this.toolbar.get( 'view' ).set( {
                    insert: {
                        style: 'primary',
                        text: 'Update Gallery',
                        click: function(){
                            iwgallery.to_form_from_selection();
                            this.frame.close();
                            // also save iW profile
                        }
                    }
                } );
            } );
            */
            // do not render gallery settings panel because they are handled by IntelliWidget Profile settings
            //this.frame.on( 'content:render:browse', this.gallerySettings, this );
            // call grandparent class activate method, bypassing parent class activate method
            window.wp.media.controller.Library.prototype.activate.apply( this, arguments );
        }
    } ),
    // override GalleryAdd view
    IntelliWidgetGalleryAdd = window.wp.media.controller.GalleryAdd.extend( {
        initialize: function() {
            //console.log( 'initializing IntelliWidgetGalleryAdd' );
            
            // call original prototype
            window.wp.media.controller.GalleryAdd.prototype.initialize.apply( this, arguments );
            // show all media items, not just images
            this.set( 'library', window.wp.media.query() );
        }
    } ),

    IntelliWidgetGallery = {
        init: function(){
            //console.log( 'initializing IntelliWidgetGallery' );
            var self = this;
            
            $( document ).on( 'click', '.intelliwidget-media', function( e ) {
                e.preventDefault();
                self._buttonid       = $( e.target ).attr( 'id' );
                self._collectionid   = self._buttonid.replace( /gallerybtn$/, '' ) + 'page';
                var collectionstr   = $( '#' + self._collectionid ).val().toString(),
                    collectiontype  = ( collectionstr.length ) ? 'library' : 'selection';
                    
                self._collection     = collectionstr.length ? collectionstr.split(',') : [];
                    
                //console.log( self._collection );

                self.frame().setState( 'library' === collectiontype ? 'gallery-edit' : 'iw-gallery-library' );
                //console.log( collectiontype );
                self.frame().open();
                if ( 'library' === collectiontype ) {
                    self.to_selection_from_collection();  // this must occur after frame is opened
                }
            } );
        },
        /**
         * 
         */
        to_selection_from_collection: function(){
            //console.log( 'attachment loading started...' );
            var self        = this,
                // get existing frame selection ( collectiontype )
                selection   = self.frame().state().get( 'library' );
            // reset selection
            selection.remove( selection.models );
            // iterate profile ids and add to selection
            $.each( self._collection, function( ndx, id ) {
                var attachment = window.wp.media.attachment( id );
                attachment.fetch();
                selection.add( attachment ? [ attachment ] : [] );
                //console.log( id + ' added' );
            } );
            //console.log( 'attachment loading done.' );
        }, // end load_attachments_from_form()
        
        to_form_from_selection: function() {
            var self        = this,
                selection   = IntelliWidgetGallery.frame().state().get( 'library' ).pluck( 'id' );
            //console.log( selection );
            // set profile ids from gallery selection
            $( '#' + self._collectionid ).val( selection.join( ',' ) );
        }, // end to_form_from_selection
        
        frame: function() {
            var self = this;
            if ( self._frame ){
                return self._frame;
            }
            
            self._frame = new IntelliWidgetGalleryPost( {
                state:  'gallery-library'
            } );
            
            self._frame.on( 'toolbar:render:gallery-edit', function(){
                // customize primary button
                IntelliWidgetGallery.frame().toolbar.get( 'view' ).set( {
                    insert: {
                        style: 'primary',
                        text: 'Update Gallery',
                        click: function(){
                            //console.log( 'insert button clicked' );
                            self.to_form_from_selection();
                            self.frame().close();
                            // also save iW profile
                            $( '#' + self._buttonid ).trigger( 'iwgallerysave' );
                        }
                    }
                } );
                // do not restrict to only images
                self.frame().state().get('library').props.unset( 'type' );
    
            } );
            /*
            this._frame.on( 'content:render:browse', function( browser ) {
                if ( !browser ) {
                    return;
                }
                // hide settings in sidebar -- these are set in the IW profile
                browser.sidebar.on( 'ready', function(){
                    browser.sidebar.unset( 'gallery' );
                } );
            } );
            */
            return self._frame;
        }, // end frame()
        // IntelliWidgetGallery properties
        _buttonid:      null,
        _collectionid:  null,
        _collection:    []

    }; // end IntelliWidgetGallery

    // wait until all scripts loaded to initialize
    $( document ).ready( function(){
        IntelliWidgetGallery.init();
    } );
} )( jQuery, _ );
