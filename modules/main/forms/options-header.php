  <h2><?php echo __( 'IntelliWidget Elements Settings', 'intelliwidget' ); ?></h2>
  <h2 class="nav-tab-wrapper"><?php do_action( 'intelliwidget_options_tab', $active_tab ); ?></h2>
  <div id="intelliwidget_error_notice">
    <?php echo apply_filters( 'intelliwidget_options_errors', '' ); ?>
  </div>
<script>
    ( function ( $ ) {
        function init() {
            var activetab = 0;
            //console.log( 'in init tabs' );
            $( '.iw_extension_panels > ul > li' ).each( function( ndx, el ){
                //console.log( 'tab index: ' + ndx );
                if ( $( el ).hasClass( 'activetab' ) ){
                    //console.log( 'is active!' );
                    activetab = ndx;
                }
            });
            $( '.iw_extension_panels' )
                .tabs( { active: activetab } )
                .addClass( 'ui-helper-clearfix' )
                .fadeIn( function(){ 
                    // reflow IW Profile tabs
                    $( window ).trigger( 'resize' ); 
                } );
        }
        $( document ).ready( function () {
            init();
        } );
    } )( jQuery );
</script>