( function ( $ ) {
    
    function addElement(){
        var id = getCustomId(),
            el = {"id":id,"parent_id":null,"depth":0,"type":"custom","title":"Untitled"};
        listdata.push( el );
        $( '<li id="item_' + el.id + '">' + elementContents( el ) + '</li>' ).appendTo( 'ul.iw-sort-list' ).find( '.iw-sort-toggle' ).trigger( 'click' );
    }
    
    function getProfileID( el ){
        var parts;
        if ( ! ( parts = el.id.match( /^(.+[\-_])([a-z]+)$/) ) ){
            parts = $( el ).parent().first().attr( 'id' ).match( /^(.+[\-_])([a-z]+)$/);
        }
        profileid = parts ? parts[ 1 ] : '';
    }
    
    function clearData( e ){
        getProfileID( e.target );
        if ( confirm( 'Remove manual data?' ) ){
            $( '#' + profileid + 'listdata' ).val( '' )
            .trigger( 'change' );        
        }
    }
    
    function deleteElement( e ){
        var div = $( e.target ).closest( 'div' ),
            el = div.closest( 'li' ),
            id = el.attr( 'id' ).replace( /^item_/, '' ),
            child = el.children( 'ul' );
        if ( !isCustom( id ) ){
            deselected.push( id );
        }
        div.fadeOut( 'slow', function(){ 
            el.replaceWith( child.contents().detach().unwrap() );
        } );
    }

    function elementContents( el ){
        var original = escAttr( getLabel( el.id ) );
        return '<div><span class="iw-sort-controls"><button class="iw-sort-toggle"></button>&nbsp;<button class="iw-sort-delete"></button></span>' +
        '<span class="iw-sort-title">' + el.title + '</span>' +
        '<div class="iw-sort-inputs" style="display:none">' +
        '<input type="hidden" name="' + el.id + '_obj_id" id="' + el.id + '_obj_id" value="' + 
            escAttr( getProperty( 'obj_id', el ) ) + '" /> ' +
        '<input type="hidden" name="' + el.id + '_type" id="' + el.id + '_type" value="' + 
            escAttr( getProperty( 'type', el ) ) + '" /> ' +
        '<div><label>Label</label> <input class="iw-sort-input-title" type="text" name="' + el.id + '_title" id="' + el.id + '_title" value="' + 
            escAttr( getProperty( 'title', el ) ) + '" /></div>' +
        '<div><label>URL</label> <input class="iw-sort-input-url" type="text" name="' + el.id + '_url" id="' + el.id + '_url" value="' + 
            escAttr( getProperty( 'url', el ) ) + '" /></div>' +
        '<div><label>Class</label> <input class="iw-sort-input-class" type="text" name="' + el.id + '_class" id="' +  el.id + '_class" value="' + 
            escAttr( getProperty( 'class', el ) ) + '" /></div>' +
        '<div><label><input class="iw-sort-input-target" type="checkbox" name="' + el.id + '_target" id="' + el.id + '_target" value="_blank" ' + 
            ( getProperty( 'target', el ) ? ' checked' : '' ) + ' />Open in New Tab</label>' +
        '<label>Use Image ' + getImageMenu( el ) + '</label></div>' +
        ( original ? '<span class="iw-sort-original">(Original Title: ' + original + ')</span>' : '' ) + '</div></div>';
    }
    
    function escAttr( s ){
        return ( '' + s )
        .replace( /&/g, '&amp;' )
        .replace( /"/g, '&quot;' )
        .replace( /'/g, '&apos;' )
        .replace( />/g, '&gt;' )
        .replace( /</g, '&lt;' );
    }
    
    function getCustomId(){
        var counter = 0,
            id = '001';
        while ( getElement( id ) ){
            counter++;
            id = '00' + counter;
        }
        return id;
    }
    
    function getElement( id, type ){
        return listdata.find( function( x ){
            // return item by customId
            if ( undefined === type ){
                return x.id === id
            }
            // return item by type and obj_id
            return x.obj_id === id && x.type === type
        } );
    }
    
    function getImageMenu( el ){
        var menu = '<select  class="iw-sort-input-image" name="' + el.id + '_image" id="' + el.id + '_image" >' +
            '<option value="">None</option><option value="featured" ' + ( 'featured' === getProperty( 'image', el ) ? ' selected' : '' ) + '>Featured Image</option>',
            options;
        if ( undefined !== window.IWAjax.relmedia.mod
           && ( options = window.IWAjax.relmedia.mod.source ) ){
            options.value.forEach( function( val, ndx ){
                menu += '<option value="' + val.toString() + '"' + ( val === getProperty( 'image', el ) ? ' selected' : '' ) + '>' + options.optlabel[ ndx ].toString() + '</option>';
            } );
        }
        menu += '</select>';
        return menu;
        
    }
    
    function getLabel( id ){
        return cleanLabel( $( selected[ id ] ).text() );
    }
    
    function cleanLabel( text ){
        return text.replace( /^[\s\-]*|\s*\(.+?\)\s*$/g, '' );
    }
    
    function getListData( el ){
        getProfileID( el );
        var listdataraw = $( '#' + profileid + 'listdata' ).val(),
            json = unescAttr( listdataraw );
        listdata = 
            undefined !== json && json ? JSON.parse( json ) : 
        [];
        selected = {};
        deselected = [];
        // console.log( 'before', listdata );
        // merge selected posts
        $( '#ms-' + profileid + 'page .ms-selection .ms-selected' ).each( function( ndx, el ){
            var elid = $( el ).data( 'ms-value' ).toString(),
                label = cleanLabel( $( el ).text() ),
                obj;
            if ( ( obj = getElement( elid ) ) ){
                // if id exists, convert old format to new format
                obj.type = 'post';
                obj.obj_id = elid;
                obj.id = getCustomId();
            } else if ( !( obj = getElement( elid, 'post' ) ) ){
                // otherwise add to menu
                obj = { id: getCustomId(), obj_id: elid, type: 'post', title: label, depth: 0, parent_id: null };
                listdata.push( obj );
            }
            selected[ obj.id ] = el;
        });  
        // merge selected posts
        $( '#ms-' + profileid + 'terms .ms-selection .ms-selected' ).each( function( ndx, el ){
            var elid = $( el ).data( 'ms-value' ).toString(),
                label = cleanLabel( $( el ).text() ),
                obj;

            // add to menu
            if ( !( obj = getElement( elid, 'term' ) ) ){
                obj = { id: getCustomId(), obj_id: elid, type: 'term', title: label, depth: 0, parent_id: null };
                listdata.push( obj );
            }
            selected[ obj.id ] = el;
        });
        // console.log( 'after', listdata );
    }
          
    function getProperty( prop, obj ){
        return undefined === obj[ prop ] ? '' : obj[ prop ];
    }
    
    function init() {
        renderModal();
        $( 'body' ).on( 'click', '.intelliwidget-listdata', showModal )
            .on( 'click', '.intelliwidget-clear-listdata', clearData );
        $( '.iw-sort-wrap' ).on( 'click', '.iw-sort-delete', deleteElement )
            .on( 'click', '.iw-sort-toggle', toggleElement )
            .on( 'click', '.iw-sort-add', addElement )
            .on( 'change blur', '.iw-sort-input-title', updateTitle )
            .on( 'click', '.iw-sort-save', setListData )
            .on( 'click', '.iw-sort-cancel', hideModal );
    }
    
    function isCustom( id ){
        var item;
        return ( ( item = getElement( id ) )
            && 'custom' === item.type );
    }
       
    function hideModal(){
        $( '.iw-sort-modal' ).fadeOut();
    }
    
    function renderModal(){
        var html = '<div class="iw-sort-modal"><div class="iw-sort-wrap">' +
            '<h3>Sort Menu Items <span class="iw-sort-controls">' +
            '<button class="iw-sort-add">+Item</button>' +
            '<button class="iw-sort-save">Apply</button>' +
            '<button class="iw-sort-cancel">Cancel</button>' +
            '</span></h3>' +
            '<div class="iw-sort-container"></div>' +
            '<span class="iw-sort-controls">' +
            '<button class="iw-sort-add">+Item</button>' +
            '<button class="iw-sort-save">Apply</button>' +
            '<button class="iw-sort-cancel">Cancel</button>' +
            '</span></div></div>';
        $( html ).hide().appendTo( 'body' );
    }
    
    function sanitizeInput( val ){
        return val.replace( /<.*?>/g, ' ' ).replace( /[^\w\-]/g, ' ' ).trim();
    }
    
    function setListData(){

        var a = list.nestedSortable( 'refresh' ).nestedSortable( 'toArray' ),
            newlistdata = [];
        /*
        q = new URLSearchParams( s );
        for (var p of q.entries() ){
            // console.log( p );
        }
        */       
        a.forEach( function( el ){
            var obj;
            if ( el.id ){
                if ( ( obj = getElement( el.id ) ) ) {
                    obj.parent_id   = el.parent_id;
                    obj.depth       = el.depth;
                    obj.title       = escAttr( $( '#' + el.id + '_title' ).val() );
                    obj.url         = escAttr( $( '#' + el.id + '_url' ).val() );
                    obj.class       = sanitizeInput( $( '#' + el.id + '_class' ).val() );
                    obj.target      = $( '#' + el.id + '_target' ).is(':checked') ? '_blank' : '';
                    obj.image       = sanitizeInput( $( '#' + el.id + '_image' ).val() );
                    obj.type        = $( '#' + el.id + '_type' ).val();
                    obj.obj_id      = $( '#' + el.id + '_obj_id' ).val();
                    newlistdata.push( obj );
                }
            }
        });
        listdata = newlistdata;
        
        deselected.forEach( function( el ){
            $( selected[ el ] ).trigger( 'click' );
        });
        $( '#' + profileid + 'listdata' ).val( 
            escAttr( 
                JSON.stringify( listdata )
            ) 
        )
        .trigger( 'change' );
        
        hideModal();
    } 
     
    function showModal( e ){
        e.preventDefault();
        
        //listdata.sort( ( a, b ) => ( a.order > b.order ) ? 1 : -1 );
        var depth = 0,
            html = '<ul class="iw-sort-list">',
            first = 1;
        getListData( e.target );
        listdata.forEach( function( el ){
            //console.log( el.id );
            // convert any deselected items
            if ( !isCustom( el.id ) && !selected[ el.id ] ){
                el = convertToCustom( el );
            }
            if ( depth < el.depth ){ 
                html += "<ul>\n";
                depth++;
            }
            else if ( depth > el.depth ){
                while ( depth > el.depth ){
                    html += "</li></ul>\n";
                    depth--;
                }
                html += "</li>\n";
            }
            else if ( !first ){
                html += "</li>\n";
            }
            html += '<li id="item_' + el.id + '">' + elementContents( el );
            first = 0;
        } );
        html += ( first ? '' : '</li>' ) + '</ul>';
        //console.log( id, listdata, html );
        $( '.iw-sort-container' ).html( html );
        list = $( '.iw-sort-list' ).nestedSortable({
            forcePlaceholderSize:   true,
            items:                  'li',
            handle:                 '> div',
            placeholder:            'menu-highlight',
            listType:               'ul',
            maxLevels:              5,
            opacity:                .6,
        });
        $( '.iw-sort-modal' ).fadeIn();
    }
    
    function convertToCustom( el ){
        //el.id = getCustomId();
        el.type = 'custom';
        //flag if no url is set
        if ( !el.url ){
            el.url = '#';
            el.title += ' [moved]';
        }
        return el;
    }
    
    function toggleElement( e ){
        var toggle = $( e.target ),
            inputs = toggle.closest( 'div' ).children( '.iw-sort-inputs' );
        if ( toggle.hasClass( 'open' ) ){
            toggle.removeClass( 'open' );
            inputs.stop().slideUp().removeClass( 'open' );
        } else {
            toggle.addClass( 'open' );
            inputs.stop().slideDown().addClass( 'open' );
        }
    }
    
    function unescAttr( s ){
        return ( '' + s )
        .replace( /\&amp;quot;/g, '\\"')
        .replace( /\&amp;amp;/g, '&')
        .replace( /\&amp;/g, '&' )
        .replace( /\&quot;/g, '"' )
        .replace( /\&apos;/g, "'" )
        .replace( /\&gt;/g, '>' )
        .replace( /\&lt;/g, '<' );
    }

    function updateTitle( e ){
        var val = e.target.value,
            el = $( e.target ).closest( 'li > div' ),
            title = el.children( '.iw-sort-title' );
        title.text( val );
    }
    
    var profileid,
        listdata,
        list,
        selected,
        deselected;
    $( document ).ready( init );

})( jQuery );
