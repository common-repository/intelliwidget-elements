<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) 
    exit;

/**
 * 
 * The Controller Class manages all the actions and filters related to a front-end IntelliWidget instance
 * 
 */

class IntelliWidgetMainController {
    
    var $recursions = 0;
    var $menu_map;
    static $shortcode_id    = 0;
    var $term_children   = array();
    var $objtype;
    var $idfield;
    var $moduledir = 'main';
    var $cached_intelliwidgets;
    
    /**
     * from Admin.php
     */
    
    var $menus;
    var $terms;
    var $templates;
    var $post_types;
    var $intelliwidgets;
    var $tax_menu;
    var $copy_id;
    var $objid;
    var $import_modules;
    static $active_tab;
    
    

    
    function __construct(){
      
        add_action( 'init', array( $this, 'init' ) );
        // only use primary site intelliwidgets on front end
        if ( is_multisite() && !is_main_site() )
            add_filter( 'sidebars_widgets', array( $this, 'sidebars_widgets' ) );
    }

    function get_sidebars_widgets(){
        if ( !isset( $this->cached_intelliwidgets ) ):
            $this->cached_intelliwidgets = array();
            // only use primary site intelliwidgets as replace options
            if ( is_multisite() )
                switch_to_blog( get_main_site_id() );
        
            $_sidebars_widgets = get_option( 'sidebars_widgets', array() );
        
            if ( is_multisite() && ms_is_switched() )
                restore_current_blog();
        
            foreach( $_sidebars_widgets as $sidebar_id => $_widgets ):
                if ( FALSE === strpos( $sidebar_id, 'wp_inactive' ) 
                    && FALSE === strpos( $sidebar_id, 'orphaned' )
                    && FALSE === strpos( $sidebar_id, 'array_version' )
                    && is_array( $_widgets ) ):
                    foreach( $_widgets as $widget_id ):
                        if ( FALSE !== strpos( $widget_id, 'intelliwidget' ) ):
                            $this->cached_intelliwidgets[ $sidebar_id ][] = $widget_id;
                        endif;
                    endforeach;
                endif;
            endforeach;
        endif;
        return $this->cached_intelliwidgets;
    }
    
    static function sidebars_widgets( $sidebars_widgets ){
        foreach( $this->get_sidebars_widgets() as $sidebar_id => $_widgets )
                foreach ( $_widgets as $widget_id )
                    $sidebars_widgets[ $sidebar_id ][] = $widget_id;
        return $sidebars_widgets;
    }
    
    static function debug_widgets( $data ){
        // merge main site intelliwidgets
        
        global $wp_registered_widgets, $wp_registered_sidebars, $_wp_sidebars_widgets;
        echo "<!-- \n" 
            . esc_html( print_r( $data, TRUE ) ) . "\n" 
            . esc_html( print_r( $wp_registered_widgets, TRUE ) ) . "\n" 
            . esc_html( print_r( $wp_registered_sidebars, TRUE ) ) . "\n" 
            . esc_html( print_r( $_wp_sidebars_widgets, TRUE ) ) . "\n" 
            . "-->\n";
    }
    
    function action_addltext_above( $args ) {
      
        if ( ( 'above' == iwinstance()->get( 'text_position' ) || 'only' == iwinstance()->get( 'text_position' ) ) ):
            echo apply_filters( 'intelliwidget_custom_text', iwinstance()->get( 'custom_text' ), $args );
        endif;
    }

    function action_addltext_below( $args ) {
      
        if ( 'below' == iwinstance()->get( 'text_position' ) ):
            echo apply_filters( 'intelliwidget_custom_text', 
                iwinstance()->get( 'custom_text' ), $args );
        endif;
        iwquery()->paginate_links();
    }
    
    function action_nav_menu( $args = array(), $post_id = NULL ) {
      
        // skip to after widget content if this is custom text only
        if ( 'only' == iwinstance()->get( 'text_position' ) ) return;
        if ( iwinstance()->get( 'nav_menu' ) ):
            $classes = !iwinstance()->get( 'nav_menu_classes' ) ? array() : preg_split( '{ +}', iwinstance()->get( 'nav_menu_classes' ) );
            $classes[] = 'iw-menu';
            $classstr = implode( ' ', apply_filters( 'intelliwidget_nav_menu_classes', $classes ) );
            if ( '-1' == iwinstance()->get( 'nav_menu' ) ):
                wp_page_menu( array( 
                    'show_home' => TRUE, 
                    'menu_class'    => $classstr,
                    )
                );
            else:
                wp_nav_menu( array( 
                    'fallback_cb'   => '', 
                    'menu'          => iwinstance()->get( 'nav_menu' ), 
                    'menu_class'    => $classstr,
                    )
                );
            endif;
        endif;
    }

    function action_post_list( $args = array(), $post_id = NULL ) {
        // skip to after widget content if this is custom text only
        if ( 'only' == iwinstance()->get( 'text_position' ) ) return;
        /**
         * hook for replacing template for special conditions (do not link, etc)
         */
        if ( $template = iwinstance()->get( 'template' ) ):
            iwquery()->get_posts();
            // use layout from shortcode content
            if ( $layout = iwinstance()->get( 'layout' ) ):
                echo do_shortcode( $layout );
            // use template
            else:
                // action hook for IntelliWidget Pro or third party templates
                if ( has_action( 'intelliwidget_action_' . $template ) ):
                    do_action( 'intelliwidget_action_' . $template );
                // default and custom templates
                elseif ( $templatepath = $this->get_template( $template ) ):
                    ob_start();
					//$content = file_get_contents( $templatepath ); //
                    include ( $templatepath );
                    $content = ob_get_clean();
                    echo do_shortcode( $content );
                endif;
            endif;
        endif;
    }

    function action_taxonomy_menu( $args = array(), $post_id = NULL ) {
      
        // skip to after widget content if this is custom text only
        if ( 'only' == iwinstance()->get( 'text_position' ) ) return;
        if ( iwinstance()->get( 'taxonomy' ) && taxonomy_exists( iwinstance()->get( 'taxonomy' ) ) ):
            $current_term_id = NULL;
            $current_ancestors = array();
            $queried_object = get_queried_object();
            if ( is_object( $queried_object ) 
                && isset( $queried_object->term_taxonomy_id ) ):
                $current_term_id      = $queried_object->term_id;
                $current_ancestors    = get_ancestors( $queried_object->term_id, $queried_object->taxonomy );
            endif;

            if ( is_singular() ):
                global $post;
                $terms = wp_get_object_terms( $post->ID, iwinstance()->get( 'taxonomy' ), array( 'orderby' => 'parent' ) );
                if ( $terms ):
                    $current_term   = end( $terms );
                    $current_term_id = $current_term->term_id;
                    $current_ancestors = get_ancestors( $current_term_id, $current_term->taxonomy );
                endif;
            endif;

            echo '<ul class="intelliwidget-taxonomy-menu">';

            wp_list_categories( apply_filters( 'intelliwidget_tax_menu_args', array( 
                'walker'            => new IntelliWidgetMainTaxonomyWalker,
                'title_li'          => '',
                'pad_counts'        => 1,
                'show_option_none'  => __( 'None', 'intelliwidget' ),
                'current_term_id'   => $current_term_id,
                'current_ancestors' => $current_ancestors,
                'taxonomy'          => iwinstance()->get( 'taxonomy' ),
                'hide_empty'        => iwinstance()->get( 'hide_empty' ),
                'current_only'      => iwinstance()->get( 'current_only' ),
                'show_count'        => iwinstance()->get( 'show_count' ), 
                'hierarchical'      => iwinstance()->get( 'hierarchical' ), 
                'show_descr'        => iwinstance()->get( 'show_descr' ),
                'menu_order'        => 'menu_order' == iwinstance()->get( 'sortby' ) ? 'asc' : FALSE,
                'orderby'           => 'title' == iwinstance()->get( 'sortby' ) ? 'title'   : NULL,
            ) ) );

            echo '</ul>';
         endif;
    }

    /**
     * Output the widget using selected template
     */
    function build_list( $args, $post_id = NULL ) {
        extract( $args, EXTR_SKIP );
		//echo 'buffering...';
        ob_start();
        // render before widget argument
        echo apply_filters( 'intelliwidget_before_widget', $before_widget, $args );
        // handle title
        if ( iwinstance()->get( 'title' ) && !iwinstance()->get( 'hide_title' ) ):
            echo apply_filters( 'intelliwidget_before_title', $before_title, $args );
            echo apply_filters( 'intelliwidget_title', iwinstance()->get( 'title' ), $args );
            echo apply_filters( 'intelliwidget_after_title', $after_title, $args );
        endif;
        // handle custom text above content
        do_action( 'intelliwidget_above_content', $args );
        // use action hook to render content
        if ( has_action( 'intelliwidget_action_' . iwinstance()->get( 'content' ) ) ):
            do_action( 'intelliwidget_action_' . iwinstance()->get( 'content' ), $args, $post_id );
        endif;
        // handle custom text below content
        do_action( 'intelliwidget_below_content', $args );
        // render after widget argument
        echo apply_filters( 'intelliwidget_after_widget', $after_widget, $args );
        // close output buffer and flush
        $content = ob_get_clean();
		//echo 'debuffered.';
        // skip if no posts retrieved option is set
        if ( !iwquery()->post_count && ( 'gallery' == iwinstance()->get( 'content' ) || ( $noposts = iwinstance()->get( 'hide_no_posts' ) ) ) )
            return apply_filters( 'intelliwidget_built_content', $noposts, $args, $post_id );
        return apply_filters( 'intelliwidget_built_content', $content, $args, $post_id );
    }
    
    function d( $log = '', $fn = '', $backtrace = TRUE ) {
        
    }
    
    /**
     * Front-end css
     */
    function enqueue_styles() {
      
        wp_enqueue_style( 'iwtemplates', trailingslashit( IWELEMENTS_URL ) . 'css/iwtemplates.css', FALSE, IWELEMENTS_VERSION );
        if ( 'lite' == $this->get_option( 'use_bootstrap' ) )
            wp_enqueue_style( 'bootstrap-lite', trailingslashit( IWELEMENTS_URL ) . 'css/bootstrap.lite.css', FALSE, IWELEMENTS_VERSION );
        elseif ( 'full' == $this->get_option( 'use_bootstrap' ) )
            wp_enqueue_style( 'bootstrap-full', trailingslashit( IWELEMENTS_URL ) . 'css/bootstrap.min.css', FALSE, IWELEMENTS_VERSION );
        if ( $this->get_option( 'use_iwicons' ) )
            wp_enqueue_style( 
                'iwmenuicons', 
                trailingslashit( IWELEMENTS_URL ) . 'css/iwmenuicons.css', 
                FALSE, IWELEMENTS_VERSION );
            
    }

    function filter_before_widget( $before_widget, $args = array() ) {
      
        if ( iwinstance()->get( 'container_id' ) ):
            $before_widget = preg_replace( '/id=".+?"/', 'id="' . iwinstance()->get( 'container_id' ) . '"', $before_widget );
        endif;
        $before_widget = preg_replace( '/class="/', 'class="' 
            . apply_filters( 'intelliwidget_classes', iwinstance()->get( 'classes' ) ) . ' ', $before_widget );
        return $before_widget;
    }
    
    function filter_classes( $classes ) {
      
        return trim( preg_replace( "/[, ;]+/", ' ', $classes ) );
    }
        
    function filter_content( $content ) {
      
        if ( strpos( $content, '<!--nextpage-->' ) ) {
            $content = preg_replace( "#\s*<!\-\-nextpage\-\->.*#s", '', $content );
        }
        // remove intelliwidget shortcode to stop endless recursion
        if ( $this->recursions > 2 ) return $content;
        $this->recursions++;
        // otherwise, parse shortcodes
        $content = do_shortcode( $content ); // preg_replace( "#\[intelliwidget.*?\]#s", '', $content ) );
        $this->recursions--;
        return $content;
    }
            
    function filter_custom_text( $custom_text, $args = array() ) {
      
        $custom_text = apply_filters( 'widget_text', $custom_text );
        if ( iwinstance()->get( 'filter' ) )
            $custom_text = wpautop( $custom_text );
        return '<div class="textwidget">' . $custom_text . '</div>';
    }
    
    function filter_title( $title, $args = array() ) {
      
        if ( !empty( $title ) ) {
            return apply_filters( 'widget_title', $title );
        }
        return $title;
    }
    
    /**
     * Trim the content to a set number of words.
     */
    function filter_trim_excerpt( $text, $length = 55, $tags = '' ) {
        if ( is_array( $length ) ) $length = isset( $length[ 'length' ] ) ? $length[ 'length' ] : 15; // backward compat
        $moretag = '<!--more-->';
        $length = empty( $length ) ? 1024 : intval( $length );
        $allowed_tags = '';
        if ( !empty( $tags ) ):
            $tags = explode( ',', $tags );
            foreach ( $tags as $tag ):
                $allowed_tags .= '<' . trim( $tag ) . '>';
            endforeach;          
        endif;
        $text       = preg_replace( "/\[intelliwidget[^\]]+?\](.+?\[\/intelliwidget\])?/s", "", $text );
        $text       = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $text );
        $textarr    = explode( $moretag, $text, 2 );
        $more       = ( count( $textarr ) > 1 );
        $text       = $textarr[ 0 ];
        
        //$text     = str_replace( ']]>', ']]&gt;', $text );
        $text       = strip_tags( $text, $allowed_tags );
        if ( empty( $allowed_tags ) && !$more ):
            $words  = preg_split( '/[\r\n\t ]+/', $text, $length + 1 );
            if ( count( $words ) > $length ):
                array_pop( $words );
                array_push( $words, '...' );
                $text = implode( ' ', $words );
            endif;
        elseif ( $allowed_tags ):
            $text = $this->get_words_html( $text, $length, $more );
        endif; 
        return $text;
    }

    function formpath( $basename ){
      
        return implode( DIRECTORY_SEPARATOR, array( 
            IWELEMENTS_DIR,
            'modules',
            $this->moduledir,
            'forms',
            preg_replace( "/[^\w\-]/", '', $basename ) . '.php'
        ) );
    }
    
    function get_box_map( $id, $objtype = NULL ) {
      
        //$objtype = isset( $this->objtype ) ? $this->objtype : $objtype;
        if ( !empty( $id ) && !empty( $objtype ) && ( $data = $this->get_meta( $id, '_intelliwidget_', $objtype, 'map' ) ) ):
          
            return $data;
        endif;
        return array();
    }
        
    function get_meta( $id, $optionname, $objtype, $index = NULL ) {
      
        // are there settings for this widget?
        if ( !empty( $id ) && !empty( $objtype ) ):
            //echo '<!-- objtype: ' . $objtype . ' -->' . PHP_EOL;
            switch( $objtype ):
                case 'post':               
                    if ( isset( $index ) ) $optionname .= $index;
                    $instance = maybe_unserialize( 
                        get_post_meta( $id, $optionname, TRUE ) );
                    break;
                default:
                    $optionname = 'intelliwidget_data_' . $objtype . '_' . $id;
                    //echo '<!-- optionname: ' . $optionname . ' -->' . PHP_EOL;
                    if ( is_multisite() )
                        switch_to_blog( get_main_site_id() );	   
                    if ( $data = get_option( $optionname ) ):
                        if ( isset( $index ) && isset( $data[ $index ] ) ):
                            $instance = $data[ $index ];
                        endif;
                    endif;
                    if ( is_multisite() && ms_is_switched() )
                        restore_current_blog();  
            endswitch;
            if ( isset( $instance ) ):
                if ( is_array( $instance ) ):
                    if ( isset( $instance[ 'custom_text' ] ) )
                        // base64 encoding saves us from markup serialization heartburn
                        $instance[ 'custom_text' ] = stripslashes( base64_decode( $instance[ 'custom_text' ] ) );
                    if ( isset( $instance[ 'hide_no_posts' ] ) )
                        // base64 encoding saves us from markup serialization heartburn
                        $instance[ 'hide_no_posts' ] = stripslashes( base64_decode( $instance[ 'hide_no_posts' ] ) );
                endif;
                return $instance;
            endif;
        endif;
        return FALSE;
    }

    function get_option( $name, $key = NULL ) {
        return IntelliWidgetMainCore::get_option( $name, $key );
    }
    
    function get_post_settings_data( $instance, $args ) {
		//echo __METHOD__ . "\n";
        // if this is single post/page, check for child profiles
        if ( is_singular() // this is an individual post, custom post type or page
            && isset( $args[ 'widget_id' ] ) ):
            global $post;
            // if this page is using another page's settings and they exist for this widget, use them
            $other_post_id = $this->get_meta( $post->ID, '_intelliwidget_', 'post', 'widget_page_id' );
		//echo $other_post_id . "\n";
            if ( $post_data = $this->get_settings_data( $post->ID, $args[ 'widget_id' ], 'post' ) ):
                $instance = $post_data;
			endif;
            	// check for no-copy override
            	if ( !empty( $other_post_id )
                	&& empty( $post_data[ 'nocopy' ] ) 
                	&& ( $other_data = $this->get_settings_data( $other_post_id, $args[ 'widget_id' ], 'post' ) ) ):
                	$instance = $other_data;
				endif;
        endif;
		
		/*
		echo '<textarea>' 
			. "other post id: " . $other_post_id 
			. "\ninstance: " 
			. print_r( $instance, TRUE ) 
			. "\npost profile: " 
			. print_r( $post_data, TRUE ) 
			. "\nother profile: " 
			. print_r( $other_data, TRUE ) 
			. '</textarea>';
			*/
        return $instance; //iwinstance()->merge( $instance, $post_data );
    }
    
    /**
     * For customized pages, retrieve the page-specific instance settings for the particular widget
     * being replaced
     */
    function get_settings_data( $id, $widget_id, $objtype ) {
      
        // if this is a child profile with no associated widget, no need to get map
        if ( is_numeric( $widget_id ) ):
            $box_id = $widget_id;
        else:
            // the box map stores meta box => widget id relations in page meta data
            $box_map = $this->get_box_map( $id, $objtype );
            //echo "BOX MAP: " . print_r( $box_map, TRUE ) . "\n";
            if ( is_array( $box_map ) )
                if ( !( $box_id = array_search( $widget_id, $box_map ) ) ):
                    //echo "NO BOX ID MATCHING " . $widget_id . "\n";
                    if ( !( $box_id = array_search( $this->normalize_nickname( $widget_id ), $box_map ) ) ):
                        //echo "NO BOX ID MATCHING NORMALIZED " . $this->normalize_nickname( $widget_id ) . "\n";
                        return FALSE;
                    endif;
                endif;
        endif;
        //echo 'box_id: ' . $box_id . "\n";
            //$widget_map = array_flip( $box_map );
            // if two boxes point to the same widget, the second gets clobbered here
                //$box_id = array_key_exists( $widget_id, $widget_map ) ? $widget_map[ $widget_id ] : $widget_id;
                // are there settings for this widget?
        if ( $instance = $this->get_meta( $id, '_intelliwidget_data_', $objtype, $box_id ) )
            return $instance;
        // all failures fall through gracefully
        return FALSE;
    }
    
    /**
     * Retrieve a template file from either the theme or the plugin directory.
     * First, check if an action hook exists for this template value and execute
     * Second check if file exists. If no file exists, return FALSE
     */
    function get_template( $template = NULL ) {
      
        if ( NULL == $template ) return FALSE;
        $themeFile  = get_stylesheet_directory() . '/intelliwidget/' . $template . '.php';
        $parentFile = get_template_directory() . '/intelliwidget/' . $template . '.php';
        $pluginFile = IWELEMENTS_DIR . '/templates/' . $template . '.php';
        if ( file_exists( $themeFile ) ) return $themeFile;
        if ( file_exists( $parentFile ) ) return $parentFile;
        if ( file_exists( $pluginFile ) ) return $pluginFile;
        return FALSE;
    }
  
    /**
     * recurse terms array to get children of given term
     */
    function get_term_children( $ttid, &$terms ) {
      
        $ttid = intval( $ttid );
        if ( isset( $this->term_children[ $ttid ] ) ) return $this->term_children[ $ttid ];
        if ( !isset( $terms[ $ttid ] ) )
            return array();
        $children = $terms[ $ttid ];
        foreach ( (array) $terms[ $ttid ] as $child ):
            if ( $ttid == $child )
                continue;
            if ( isset( $terms[ $child ] ) )
                $children = array_merge( $children, $this->get_term_children( $child, $terms ) );
        endforeach;
        $this->term_children[ $ttid ] = $children;
        return $children;
    }
    
    /**
     * variation of the core taxonomy function that caches and returns term_taxonomy_ids instead of term_ids
     */
    function get_term_hierarchy( $taxonomy ) {
	  
	    if ( !is_taxonomy_hierarchical( $taxonomy ) )
		    return array();
	    if ( !( $children = get_site_option( "{$taxonomy}_iw_children" ) ) ):
            $children   = array();
            // using array_reduce this way performs like array_map but returns assoc array
            $terms      = array_reduce( 
                get_terms( 
                    $taxonomy, 
                    array( 
                        'get'       => 'all', 
                        'orderby'   => 'id', 
                        'fields'    => 'all' 
                    ) 
                ), 
                array( $this, 'map_terms' ), 
                array() 
            );
            foreach ( $terms as $termid => $term ):
                $children[ $term[ 'ttid' ] ] = array();
                if ( $term[ 'parent' ] > 0 && isset( $terms[ $term[ 'parent' ] ] ) ):
                    $children[ $terms[ $term[ 'parent' ] ][ 'ttid' ] ][] = $term[ 'ttid' ];
                endif;
            endforeach;
            update_site_option( 
                "{$taxonomy}_iw_children",
                $children,
                FALSE
            );
        endif;       
	    return $children;
    }
    
    function get_words_html( $text, $length, $more = FALSE ) {
      
        $opentags   = array();
        $excerpt    = '';
        $text       = preg_replace( '/<(br|hr)[ \/]*>/', "<$1/>", $text );
        preg_match_all( '/(<[^>]+?>)?([^<]*)/', $text, $elements );
        if ( !empty( $elements[ 2 ] ) ):
            $count = 0;
            foreach( $elements[ 2 ] as $string ):
                $html = array_shift( $elements[ 1 ] );
                if ( preg_match( '/<(\w+)[^\/]*>/', $html, $matches ) ):
                    $opentags[] = $matches[ 1 ];
                elseif ( preg_match( '/<\/(\w+)/', $html, $matches ) ):
                    $close = array_pop( $opentags );
                endif;
                $excerpt .= $html;
                $words = preg_split( '/[\r\n\t ]+/', $string );
                foreach ( $words as $word ):
                    if ( empty( $word ) ) continue;
                    $count++;
                    if ( $count <= $length || $more ):
                        $excerpt .= $word . ' ';
                    else:
                        $excerpt .= ' ...';
                        break;
                    endif;
                endforeach;
                if ( $count > $length && !$more ) break;
            endforeach;
            while ( count( $opentags ) ):
                $close = array_pop( $opentags );
                $excerpt .= '</' . $close . '>';
            endwhile;
        endif;
        return $excerpt;
    }
    
    function init(){
        
      
        //WordPress front-end actions
        add_action( 'wp_enqueue_scripts',               array( $this, 'enqueue_styles' ),               5 );

        //WordPress front-end filters
        add_filter( 'theme_mod_nav_menu_locations',     array( $this, 'theme_mod_nav_menu_locations' ), 10 );
        add_filter( 'post_mime_types',                  array( $this, 'post_mime_types' )               );
        add_filter( 'post_link',                        array( $this, 'post_link' ),                    10, 3 );
        add_filter( 'page_link',                        array( $this, 'post_link' ),                    10, 3 );
        add_filter( 'post_type_link',                   array( $this, 'post_link' ),                    10, 3 );
        add_filter( 'the_title',                        array( $this, 'post_title' ),                   100, 2 );

        // IntelliWidget front-end actions
        add_action( 'intelliwidget_action_post_list',   array( $this, 'action_post_list' ),             10, 3 );
        add_action( 'intelliwidget_action_nav_menu',    array( $this, 'action_nav_menu' ),              10, 3 );
        add_action( 'intelliwidget_action_tax_menu',    array( $this, 'action_taxonomy_menu' ),         10, 3 );
        add_action( 'intelliwidget_action_gallery',     'IntelliWidgetMainGallery::render',             10, 3 );
        add_action( 'intelliwidget_above_content',      array( $this, 'action_addltext_above' ),        10, 3 );
        add_action( 'intelliwidget_below_content',      array( $this, 'action_addltext_below' ),        10, 3 );
        
        // IntelliWidget front-end filters
        add_filter( 'intelliwidget_before_widget',      array( $this, 'filter_before_widget' ),         10, 3 );
        add_filter( 'intelliwidget_title',              array( $this, 'filter_title' ),                 10, 3 );
        add_filter( 'intelliwidget_custom_text',        array( $this, 'filter_custom_text' ),           10, 3 );
        add_filter( 'intelliwidget_classes',            array( $this, 'filter_classes' ),               10, 3 );
        add_filter( 'intelliwidget_content',            array( $this, 'filter_content' ),               10, 3 );
        add_filter( 'intelliwidget_trim_excerpt',       array( $this, 'filter_trim_excerpt' ),          10, 3 );
        // page-specific data always takes priority over condset and term data
        add_filter( 'intelliwidget_extension_settings', array( $this, 'get_post_settings_data' ),       20, 3 );

        /**
         * from Admin.php
         */
        
      
        // WordPress admin actions
        add_post_type_support( 'page', 'excerpt' );
        add_post_type_support( 'post', 'page-attributes' );
        add_action( 'load-post.php',                        array( $this, 'post_form_actions' )                 );
        add_action( 'load-post-new.php',                    array( $this, 'post_form_actions' )                 );
        add_action( 'load-customize.php',                   array( $this, 'widget_form_actions' )               );
        add_action( 'load-widgets.php',                     array( $this, 'widget_form_actions' )               );
        add_action( 'manage_post_posts_custom_column',      array( $this, 'render_menu_order' )                 );
        add_action( 'save_post',                            array( $this, 'post_save_data' ),                   1, 2 );
        add_action( 'admin_menu',                           array( $this, 'options_page' )                      );
        add_action( 'clean_term_cache',                     array( $this, 'clean_term_cache' ),                 10, 2 );
        add_action( 'admin_enqueue_scripts',                array( $this, 'admin_scripts' ),                    5 );
        
        // WordPress admin filters
        add_filter( 'manage_edit-post_columns',             array( $this, 'menu_order_column')                  );
        add_filter( 'manage_edit-post_sortable_columns',    array( $this, 'order_column_register_sortable' )    );
        add_filter( 'media_view_settings',                  array( $this, 'media_view_settings'),               10, 2 );
        
        // options page actions
        add_action( 'intelliwidget_options_tab',            array( $this, 'options_tab' ),                      10, 2 ); 
        add_action( 'intelliwidget_options_header',         array( $this, 'options_header' ),                   10, 2 ); 

        // profile form actions/filters
        add_action( 'intelliwidget_form_all_before',        array( $this, 'render_general_settings' ),          10, 5 );
        add_action( 'intelliwidget_form_post_list',         array( $this, 'render_post_selection_settings' ),   5, 5 );
        
        if ( IntelliWidgetMainCore::get_option( 'use_legacy' ) )
            add_action( 'intelliwidget_form_post_list',         array( $this, 'render_appearance_settings' ),       10, 5 );
        add_action( 'intelliwidget_form_nav_menu',          array( $this, 'render_nav_menu_settings' ),         10, 5 );
        add_action( 'intelliwidget_form_tax_menu',          array( $this, 'render_tax_menu_settings' ),         10, 5 );
        add_action( 'intelliwidget_form_gallery',           array( $this, 'render_gallery_settings' ),          10, 5 );
        add_action( 'intelliwidget_form_all_after',         array( $this, 'render_addl_text_settings' ),        10, 5 );
        add_action( 'intelliwidget_post_selection_menus',   array( $this, 'render_post_selection_menus' ),      10, 4 );
        add_filter( 'intelliwidget_update_profile_data',    array( $this, 'filter_update_data' ),               10, 2 );
        
        
        // ajax actions
        add_action( 'wp_ajax_iw_cdfsave',                   array( $this, 'ajax_save_cdf_data' )           );
        add_action( 'wp_ajax_iw_save',                      array( $this, 'ajax_save_data' )               );
        add_action( 'wp_ajax_iw_copy',                      array( $this, 'ajax_copy_data' )               );
        add_action( 'wp_ajax_iw_delete',                    array( $this, 'ajax_delete_profile' )   );
        add_action( 'wp_ajax_iw_add',                       array( $this, 'ajax_add_profile' )      );
        add_action( 'wp_ajax_iw_menus',                     array( $this, 'ajax_post_get_select_menu_form' )    );
        add_action( 'wp_ajax_iw_menu',                      array( $this, 'ajax_post_get_select_menu' )         );
        add_action( 'wp_ajax_iw_widget_menus',              array( $this, 'ajax_widget_get_select_menu_form' )  );
        add_action( 'wp_ajax_iw_widget_menu',               array( $this, 'ajax_widget_get_select_menu' )       );
        add_action( 'wp_ajax_iw_debug',                     array( $this, 'ajax_debug' ),                       10, 2 );
        add_action( 'wp_ajax_iwtemplates_debug',              array( $this, 'ajax_debug' ),                       10, 2 );
    }
    

    /**
     * return lookup array 
     */
    function map_terms( $res, $el ) {
      
        $res[ $el->term_id ] = array( 
            'ttid'      => $el->term_taxonomy_id, 
            'parent'    => $el->parent 
        );
        return $res;
    }

    function normalize_nickname( $string ){
        return ':n:' . preg_replace( "/[^A-Za-z0-9\-]+/", '-', strtolower( $string ) );
    }
    
    function post_classes( $seq, $classes = array() ) {
        
        $classes[] = 'post-seq-' . $seq;
        $classes[] = ( $seq % 2 === 0 ) ? 'even' : 'odd';
        // add menu classes
        global $post;
        $id = ( int ) iwquery()->post->ID;
        if ( is_object( $post ) ):
            if ( ( int ) $post->ID == $id ):
                $classes[] = 'intelliwidget-current-menu-item';
            endif;
            if ( is_post_type_hierarchical( $post->post_type ) ):
                $ancestors = get_post_ancestors( $post->ID );
                if ( in_array( $id, $ancestors ) ):
                    $classes[] = 'intelliwidget-current-menu-ancestor';
                endif;
                if ( $id == current( $ancestors ) ):
                    $classes[] = 'intelliwidget-current-menu-parent';
                endif;
            endif;
        endif;
        
        // IWP 2.0: add public taxonomies to IW Post Classes
        $taxonomies = get_taxonomies( array( 'public' => true ) );
        foreach ( (array) $taxonomies as $taxonomy ) {
            if ( is_object_in_taxonomy( iwquery()->post->post_type, $taxonomy ) ) {
                foreach ( (array) get_the_terms( iwquery()->post->ID, $taxonomy ) as $term ) {
                    if ( empty( $term->slug ) ) {
                        continue;
                    }

                    $term_class = sanitize_html_class( $term->slug, $term->term_id );
                    if ( is_numeric( $term_class ) || ! trim( $term_class, '-' ) ) {
                        $term_class = $term->term_id;
                    }

                    // 'post_tag' uses the 'tag' prefix for backward compatibility.
                    if ( 'post_tag' == $taxonomy ) {
                        $classes[] = 'tag-' . $term_class;
                    } else {
                        $classes[] = sanitize_html_class( $taxonomy . '-' . $term_class, $taxonomy . '-' . $term->term_id );
                    }
                }
            }
        }
        
        return implode( ' ', array_unique( $classes ) );
    }
    
    function post_link( $permalink, $post, $leavetitle ) {
        if ( is_object( $post ) )
            $id = $post->ID;
        else
            $id = $post;
        if ( ( $val = get_post_meta( $id, 'intelliwidget_external_url', TRUE ) )
            && ( ( $use = get_post_meta( $id, 'intelliwidget_all_links', TRUE ) )
                || in_iw_loop() ) )
            return $val;
        return $permalink;
    }
    
    function post_mime_types( $args ) {
      
        $post_mime_types[ 'application' ] = array(
            __( 'Application', 'intelliwidget' ),
            __( 'Manage Application', 'intelliwidget' ),
            _n_noop( 
                __( 'Application', 'intelliwidget' ) . ' <span class="count">(%s)</span>', 
                __( 'Applications', 'intelliwidget' ) . ' <span class="count">(%s)</span>'
            )
        );
        return $post_mime_types;
    }

    function post_title( $title, $id = NULL ) {
      
        // do not use filter on intelliwidgets
        
        if ( is_admin() || empty( $id ) || ( is_object( iwquery()->post ) && $id == iwquery()->post->ID ) ) return $title;
        if ( ( $use = get_post_meta( $id, 'intelliwidget_all_titles', TRUE ) ) 
            && ( $val = get_post_meta( $id, 'intelliwidget_alt_title', TRUE ) ) )
            $title = $val;
        return $title;
    }    

    function theme_mod_nav_menu_locations( $mods ) {
      
        if ( !is_admin() && is_array( $mods ) ):
            if ( !isset( $this->menu_map ) ):
                $this->menu_map = array();
                foreach ( $mods as $location => $menu ):
                    $args = array( 'widget_id' => 'nav_menu_location-' . $location );
                    $instance = apply_filters( 'intelliwidget_extension_settings', array(), $args );
                    if ( !empty( $instance ) 
                        && !empty( $instance[ 'menu_location' ] )
                            && $instance[ 'menu_location' ] == $location 
                                && $instance[ 'nav_menu' ] ):
                        iwinstance()->defaults( $instance );
                        $this->menu_map[ $location ] = iwinstance()->get( 'nav_menu' );
                    endif;
                endforeach;
            endif;
            $mods = array_merge( $mods, $this->menu_map );
        endif;
        return $mods;
    }
    
    /**
     * From previous Admin.php
     * ALL CONTROLLER METHODS ARE NOW IN THE SAME FILE
     */
    
    function add_profile() {
        
        $box_map = $this->get_box_map( $this->objid, $this->objtype );
        $newkey = 1;
        while ( array_key_exists( $newkey, $box_map ) ):
            $newkey++;
        endwhile;
        $box_map[ $newkey ] = '';
        $this->update_meta( $this->objid, '_intelliwidget_', $box_map, 'map' );
        return $newkey;
    }
    
    /**
     * Output scripts to the admin. 
     */
    function admin_scripts() {
      
        if ( !wp_script_is( 'intelliwidget-js', 'enqueued' ) ): // prevent multiple initialization by other plugins
            wp_enqueue_style( 'wp-jquery-ui-dialog' );
            wp_enqueue_style( 'intelliwidget-js', trailingslashit( IWELEMENTS_URL ) . 'css/intelliwidget-admin.css', array(), IWELEMENTS_VERSION );
            wp_enqueue_style( 'iwsort-js', trailingslashit( IWELEMENTS_URL ) . 'css/iwsort.css' );
            wp_enqueue_script( 'intelliwidget-js', trailingslashit( IWELEMENTS_URL ) . 'js/intelliwidget.js', array( 'jquery-ui-tabs' ), IWELEMENTS_VERSION, FALSE );
            wp_enqueue_script( 'nestedsortable', trailingslashit( IWELEMENTS_URL ) . 'js/nestedsortable.js', array( 'jquery-ui-sortable' ), IWELEMENTS_VERSION, FALSE );
            wp_enqueue_script( 'iwsort-js', trailingslashit( IWELEMENTS_URL ) . 'js/iwsort.js', array( 'nestedsortable' ), IWELEMENTS_VERSION, FALSE );
            
            wp_localize_script( 'intelliwidget-js', 'IWAjax', array(
                'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                'objtype'   => $this->objtype,
                'idfield'   => $this->idfield,
                'debug'     => $this->get_option( 'use_debug' ),
                'relmedia'  => apply_filters( 'iwtemplates_default_part_options_thumbnail', array() ), // get related media options
            ) );
            $current_screen = get_current_screen();
            //echo '<!-- SCREEN BASE: ' . $current_screen->base . ' -->' . PHP_EOL;
        
            if ( in_array( $current_screen->base, array( 'intelliwidget_page_intelliwidget-condsets', 'intelliwidget_page_intelliwidget-templates', 'widgets', 'term' ) ) ):
                //echo '<!-- CALLING wp_enqueue_media() -->' . PHP_EOL;
                wp_enqueue_media();
            endif;
            wp_enqueue_script( 'intelliwidget-pro-js', trailingslashit( IWELEMENTS_URL ) . 'js/iw-gallery.js', array( 'media-editor','media-views','intelliwidget-js' ), IWELEMENTS_VERSION, FALSE );
        endif;
    }
        
    // ajax add for posts only - duplicate this for other types
    function ajax_add_profile() {
      
        if ( !$this->validate_post( 'iwadd', '_wpnonce', TRUE ) )
            $this->ajax_resp( 'fail:add' );
        // note that the query string version uses "post" instead of "post_ID"
        if ( !( $box_id = $this->add_profile() ) ) 
            $this->ajax_resp( 'fail:add-profile' );
      
        iwinstance()->defaults();
        $profile = new IntelliWidgetMainProfile( $this->objid, $box_id );
        if ( !( $name = $this->get_intelliwidget_name( iwinstance()->get( 'replace_widget' ) ) ) )
            $name = $this->get_intelliwidget_name( 'none' );
        $profile->set_title( $name );
        $response = array(
            'tab'   => $profile->get_tab(),
            'form'  => $profile->begin_profile( $this->objid, $box_id ) . $this->get_profile_form( $profile ) . $profile->end_profile(),
        );
        $this->ajax_resp( $response );
    }

    // ajax copy for posts only - duplicate this for other types
    function ajax_copy_data() {
      
        if ( !$this->validate_post( 'iwpage_%s', 'iwpage_%s', TRUE ) ) 
            $this->ajax_resp( 'fail:copy-invalid' );
        if ( FALSE === $this->save_copy_id() ) 
            $this->ajax_resp( 'fail:copy-save' );
        $this->ajax_resp( 'success' );
    }
    
    function ajax_debug(){
      
        ob_start();
        echo  '=== AJAX DEBUG OUTPUT ===' . PHP_EOL;
        IntelliWidgetMainCore::print_debug();
        $this->ajax_resp( ob_get_clean() );
    }
    
    // widgets only
    function ajax_get_widget_instance( &$widget ){
        
        if ( empty( $_POST[ 'widget-id' ] ) )
            return FALSE;
        $widget_id = sanitize_text_field( $_POST[ 'widget-id' ] );
        if ( isset( $_POST[ 'wp_customize' ] ) && 'on' == $_POST[ 'wp_customize' ] ):
            //$action         = 'update-widget';
            $action         = 'preview-customize_' . get_stylesheet();
            //$nonce          = 'nonce';        
            $nonce          = 'customize_preview_nonce';
            $is_customizer  = TRUE;
        else:
            $action         = 'save-sidebar-widgets';
            $nonce          = '_wpnonce_widgets';
            $is_customizer  = FALSE;
        endif;
        if ( !$this->validate_post( $action, $nonce, TRUE ) ) 
            return FALSE;
        if ( $is_customizer ):
            // this must be a new customizer widget so create a temporary instance
            $widget         = new IntelliWidgetMainWidget();
            iwinstance()->defaults();
            $number         = str_replace( $widget->id_base . '-', '', $widget_id );
            $widget->_set( $number );
            return $number;
        endif;
        return str_replace( 'intelliwidget-', '', $widget_id );
    }
    
    // ajax delete for posts only - duplicate this for other types
    function ajax_delete_profile() {
      
        if ( !$this->validate_post( 'iwdelete', '_wpnonce', TRUE ) ) 
            $this->ajax_resp( 'fail:delete' );
        $box_id = isset( $_POST[ 'iwdelete' ] ) ? intval( $_POST[ 'iwdelete' ] ) : NULL;
        if ( FALSE === $this->delete_profile( $box_id ) ) 
            $this->ajax_resp( 'fail:delete-save' );
        $this->ajax_resp( 'success' );
    }

    /**
     * Get multi select menu options for specific instance. To cut down on response size,
     * The result set is limited to 200 items. Items that are truncated from the default
     * set can be retreived in subsquent ajax requests by passing a filter value. This solution
     * was chosen in favor of more complicated paging and caching to keep the interface
     * as simple as possible.
     */
    
    function ajax_post_get_select_menu() {
      
        // validate for all object types
        // FIXME - filter for terms/condsets ???
        $box_id_key = current( preg_grep( "/_box_id$/", array_keys( $_POST ) ) );
        $box_id     = isset( $_POST[ $box_id_key ] ) ? intval( $_POST[ $box_id_key ] ) : NULL;

        if ( empty( $box_id ) || !$this->validate_post( 'iwpage_%s', 'iwpage_%s', TRUE ) )
            $this->ajax_resp( 'fail:select-menu' );
        
        // FIXME: not pulling master profile settings if child profile has not been saved yet
        iwinstance()->defaults( $this->get_meta( $this->objid, '_intelliwidget_data_', $this->objtype, $box_id ) );
        @file_put_contents( dirname( __FILE__ ) . '/debug.menu.txt', print_r( $_POST, TRUE ) . "\n", FILE_APPEND );
        if ( isset( $_POST[ 'post_types' ] ) )
            iwinstance()->set( 'post_types', (array) $_POST[ 'post_types' ] );
        if ( isset( $_POST[ 'site_id' ] ) )
            iwinstance()->set( 'site_id', intval( $_POST[ 'site_id' ] ) );
        
        $type = isset( $_POST[ 'menutype' ] ) && 'terms' == $_POST[ 'menutype' ] ? 'terms' : 'page';
        /**
         * To allow for filtering of the results, any selected items must be passed in the request and then passed back
         * in the result set. This is done by temporarily modifying the instance with the selections from the request. 
         * In addition, the filter search string is added to the instance and passed to the function. The result is a
         * combination of the search results and the currently selected items.
         */
        // FIXME -- use get_field_id function here?
        $selectedkey = 'intelliwidget_' . $this->objid . '_' . $box_id . '_' . $type;
        iwinstance()->set( $type, $this->filter_sanitize_input( $_POST[ $selectedkey ] ) );
        iwinstance()->set( $type . 'search', $this->filter_sanitize_input( $_POST[ $selectedkey . 'search' ] ) );
        $function = 'terms' == $type ? 'get_terms_list' : 'get_posts_list' ;
        ob_start();
        echo call_user_func( array( $this, $function ) );
        $this->ajax_resp( ob_get_clean() );
    }
        
    function ajax_post_get_select_menu_form() {
      
        $box_id_key             = current( preg_grep( "/_box_id$/", array_keys( $_POST ) ) );
        $box_id                 = isset( $_POST[ $box_id_key ] ) ? intval( $_POST[ $box_id_key ] ) : NULL;
        if ( empty( $box_id ) || !$this->validate_post( 'iwpage_%s', 'iwpage_%s', TRUE ) ) 
            $this->ajax_resp( 'fail:select-menu-form' );


        iwinstance()->defaults( $this->get_meta( $this->objid, '_intelliwidget_data_', $this->objtype, $box_id ) );
        $profile = new IntelliWidgetMainProfile( $this->objid, $box_id );
        ob_start();
        $this->render_post_selection_menus( $profile );
        $this->ajax_resp( ob_get_clean() );
    }
    
    function ajax_resp( $msg ) {
      
        die( json_encode( $msg ) );
    }

    // posts only
    function ajax_save_cdf_data() {
      
        if ( !$this->validate_post( 'iwpage_%s', 'iwpage_%s', TRUE ) ) 
            $this->ajax_resp( 'fail:cdf-invalid' );
        if ( FALSE === $this->save_cdf_data() ) 
            $this->ajax_resp( 'fail:cdf-save' );
        $this->ajax_resp( 'success' );
    }
    
    function ajax_save_data() {
        $box_id_key = current( preg_grep( "/_box_id$/", array_keys( $_POST ) ) );
        $box_id = isset( $_POST[ $box_id_key ] ) ? intval( $_POST[ $box_id_key ] ) : NULL;
        if ( empty( $box_id ) || 
            !$this->validate_post( 'iwpage_%s', 'iwpage_%s', TRUE ) ) 
                $this->ajax_resp( 'fail:post-invalid' );

        if ( FALSE === $this->save_data() ) 
            $this->ajax_resp( 'fail:save-data' ); 
        
        /** ???? here ???? ***/
        
        $this->get_copy_id( $this->objid );
        iwinstance()->defaults( $this->get_meta( $this->objid, '_intelliwidget_data_', $this->objtype, $box_id ) );
        $profile = new IntelliWidgetMainProfile( $this->objid, $box_id );
        if ( !( $name = $this->get_intelliwidget_name( iwinstance()->get( 'replace_widget' ) ) ) )
            $name = $this->get_intelliwidget_name( 'none' );
        $profile->set_title( $name );

        $this->ajax_resp( array(
            'tab'   => $profile->get_tab( iwinstance()->get( 'nickname' ) ),
            'form'  => $this->get_profile_form( $profile ),
        ) );
    }

    function ajax_widget_get_select_menu() {

        $widget     = NULL;
        iwinstance()->defaults();
        if ( ( $number = $this->ajax_get_widget_instance( $widget ) ) ):
            $type = 'terms' == $_POST[ 'menutype' ] ? 'terms' : 'page';
            // FIXME -- use get_field_id function here?
            $selectedkey = 'widget-intelliwidget-' . $number . '-' . $type;
            iwinstance()->set( $type, $this->filter_sanitize_input( $_POST[ $selectedkey ] ) );
            iwinstance()->set( $type . 'search', $this->filter_sanitize_input( $_POST[ $selectedkey . 'search' ] ) );
            $function = 'terms' == $type ? 'get_terms_list' : 'get_posts_list' ;
            ob_start();
            echo call_user_func( array( $this, $function ) );
            $this->ajax_resp( ob_get_clean() );
        endif;
        $this->ajax_resp( 'fail:widget-select-menu' );
    }
    
    function ajax_widget_get_select_menu_form() {
        
        $widget     = NULL;
        if ( $this->ajax_get_widget_instance( $widget ) ):
            ob_start();
            $this->render_post_selection_menus( $widget );
            $this->ajax_resp( ob_get_clean() );
        endif;
        $this->ajax_resp( 'fail:widget-select-menu-form' );
    }

    /**
     * fires whenever terms are updated to update term_taxonomy children cache
     */
    function clean_term_cache( $ids, $taxonomy ) {
      
        delete_site_option( "{$taxonomy}_iw_children" );
        $this->get_term_hierarchy( $taxonomy );
    }
    
    function copy_form( $obj ) {
      
        include( $this->formpath( 'copy' ) );
    }
    
    function debug_admin( $fn, $msg ){
        file_put_contents( IWELEMENTS_DIR . '/debug.txt', $fn . ' ' . $msg, FILE_APPEND );
    }
    
    function delete_meta( $id, $optionname, $box_id = NULL ) {
      
        if ( empty( $id ) || empty( $optionname ) )
            return;
        switch( $this->objtype ):
            case 'post':
                if ( isset( $box_id ) ) $optionname .= $box_id;
                return delete_post_meta( $id, $optionname );
            default:
                $optionname = 'intelliwidget_data_' . $this->objtype . '_' . $id;
                if ( is_multisite() )
                    switch_to_blog( get_main_site_id() );	
                if ( isset( $box_id ) && ( $data = get_option( $optionname ) ) ):
                    unset( $data[ $box_id ] );
                    update_option( 
                        $optionname, 
                        $data,
                        FALSE
                    );
                endif;
                if ( is_multisite() && ms_is_switched() )
                    restore_current_blog();    
        endswitch;
    }
    
    function delete_profile( $box_id ) {
      
        $box_map = $this->get_box_map( $this->objid, $this->objtype );
        $this->delete_meta( $this->objid, '_intelliwidget_data_', $box_id );
        unset( $box_map[ $box_id ] );
        $this->update_meta( $this->objid, '_intelliwidget_', $box_map, 'map' );
    }

    /**
     * 
     */
    function display_quickedit_menu_order( $column_name, $post_type ) {
      
        if ( $column_name != 'menu_order' ) return;
        global $post;
        ?>
        <fieldset class="inline-edit-col-right">
          <div class="inline-edit-col column-<?php echo $column_name; ?>">
            <label class="inline-edit-group alignleft">
            <span class="title">Order</span><input type="text" size="4" name="menu_order" value="<? echo intval( $post->menu_order ); ?>" />
            </label>
          </div>
        </fieldset>
        <?php
    }
    
    function enqueue_admin(){
        // layout interface styles
        wp_enqueue_style(
            'intelliwidget-template-options', 
            trailingslashit( IWELEMENTS_URL ) . 'css/layout.css', 
            FALSE, 
            IWELEMENTS_VERSION
        );
    }
    /**
     * Stub for data validation
     */
    function filter_sanitize_input( $unclean = NULL ) {
        
      
        //file_put_contents( 'debug_main.txt', 'filtering: ' . print_r( $unclean, TRUE ) . "\n", FILE_APPEND );
        if ( is_array( $unclean ) ):
            return array_map( array( $this, __FUNCTION__ ), $unclean );
        else:
            return sanitize_text_field( $unclean );
        endif;
        
    }
    
    // filter: intelliwidget_update_profile_data
    function filter_update_data( $old_instance, $new_instance ){
      
        /**
         * v7.1 no longer silently converts strings to arrays so it must be done explicitly.
         */
        if ( !is_array( $old_instance ) )
            $old_instance = array();
        if ( !is_array( $new_instance ) )
            $new_instance = array();
        //file_put_contents( 'debug_main.txt', 'BEFORE old: ' . print_r( $old_instance, TRUE ) . ' new: ' . print_r( $new_instance, TRUE ) . "\n", FILE_APPEND );
        $text_fields = $this->get_fields( 'text' );
        foreach ( $new_instance as $name => $value ):
            // special handling for text inputs
            if ( in_array( $name, $text_fields ) ):
                if ( current_user_can( 'unfiltered_html' ) ):
                    $old_instance[ $name ] =  $value;
                else:
                    // raw html parser/cleaner-upper: see WP docs re: KSES
                    $old_instance[ $name ] = stripslashes( 
                    wp_filter_post_kses( addslashes( $value ) ) ); 
                endif;
        //file_put_contents( 'debug_main.txt', 'textfield name: ' . $name . ' value: ' . print_r( $value, TRUE ) . ' old: ' . print_r( $old_instance[ $name ], TRUE ) . ' new: ' . print_r( $new_instance[ $name ], TRUE ) . "\n", FILE_APPEND );
        // unset search fields
            elseif ( 0 === strpos( $name, 'iw' ) || in_array( $name, array( 'pagesearch', 'termsearch', 'profiles_only' ) ) ):
                if ( isset( $old_instance[ $name ] ) ):
                    unset( $old_instance[ $name ] );
                endif;
        //file_put_contents( 'debug_main.txt', 'unset name: ' . $name . "\n", FILE_APPEND );
            // strict sanitize
            else:
                $old_instance[ $name ] = $this->filter_sanitize_input( $value );
        //file_put_contents( 'debug_main.txt', 'name: ' . $name . ' value: ' . print_r( $value, TRUE ) . ' old: ' . print_r( $old_instance[ $name ], TRUE ) . ' new: ' . print_r( $new_instance[ $name ], TRUE ) . "\n", FILE_APPEND );
            endif;
            // handle multi selects that may not be passed or may just be empty
            if ( 'page_multi' == $name && empty( $new_instance[ 'page' ] ) )
                $old_instance[ 'page' ] = array();
            if ( 'terms_multi' == $name && empty( $new_instance[ 'terms' ] ) )
                $old_instance[ 'terms' ] = array();
        endforeach;
        // set/unset checkboxes
        foreach ( $this->get_fields( 'checkbox' ) as $name )
            $old_instance[ $name ] = ( isset( $new_instance[ $name ] ) && $new_instance[ $name ] ); // might be hidden field w/empty string
        //$iwq = new IntelliWidgetMainQuery(); // do not use for now ( 2.3.4 )
        //$old_instance[ 'querystr' ] = $iwq->iw_query( $old_instance );
        // handle post type
        if ( 'gallery' == $new_instance[ 'content' ] ):
            $old_instance[ 'post_types' ] = array( 'attachment' );
            $old_instance[ 'items' ] = 0;
        elseif ( empty( $new_instance[ 'post_types' ] ) || in_array( 'attachment', $new_instance[ 'post_types' ] ) ):
            $old_instance[ 'post_types' ] = array( 'post' );
        endif;
        //file_put_contents( 'debug_main.txt', 'AFTER old: ' . print_r( $old_instance, TRUE ) . ' new: ' . print_r( $new_instance, TRUE ) . "\n", FILE_APPEND );
        return $old_instance;
    }
 
    function get_copy_id( $id ){
      
        $this->copy_id = $this->get_meta( $id, '_intelliwidget_', $this->objtype, 'widget_page_id' );
    }
    
    function get_current_panel_id(){
      
        return ( 
            isset( $_GET[ 'template_id' ] ) ? 
                sanitize_text_field( $_GET[ 'template_id' ] ) :
                0 
        );
    }

    /**
     * helper function - retrieves from IntelliWidgetMainStrings class.
     */
    function get_fields( $key ){
      
        return IntelliWidgetMainStrings::get_fields( $key );
    }
    
    function get_intelliwidget_name( $key ){
      
        return array_search( $key, $this->get_intelliwidgets() );
    }
    
    /**
     * IMPORTANT NOTE: get_intelliwidgets() and get_widget_instance() will cause the widgets admin 
     * to fail because of the way we dereference the widget object (bypassing the customizer manager framework)
     * Make sure it is only called on post, term or condset objtype, never when is_widget == true!
     */
    function get_intelliwidgets() {
      
        // cache intelliwidgets assoc array 
        if ( !isset( $this->intelliwidgets ) ):
            global $wp_registered_sidebars;
            $this->intelliwidgets = array();
        
            if ( is_multisite() )
                switch_to_blog( get_main_site_id() );
        
            $_instances = get_option( 'widget_intelliwidget' );
        
            if ( is_multisite() && ms_is_switched() )
                restore_current_blog();

        
            foreach ( $this->get_menu( 'replaces' ) as $value => $label )
                $this->intelliwidgets[ $value ] = $label;
        
            foreach ( $this->get_sidebars_widgets() as $sidebar_id => $_widgets ):
                if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) ):
                    $count = 0;
                    foreach( $_widgets as $widget_id ):
                        if ( preg_match ( "{^intelliwidget\-(\d+)$}", $widget_id, $matches ) ):
                            $number = $matches[ 1 ];
                            if ( isset( $_instances[ $number ] ) ):
                                // get nickname from widget globals
                                $instance = $_instances[ $number ];
                                $count++;
                                if ( empty( $instance[ 'nickname' ] ) )
                                    $instance[ 'nickname' ] = $count;
                                $this->intelliwidgets[ $widget_id ] = $wp_registered_sidebars[ $sidebar_id ][ 'name' ] 
                                    . ' [' . $instance[ 'nickname' ] . ']';
                            endif;
                        endif;
                    endforeach;
                endif;
            endforeach;
        endif;
        return $this->intelliwidgets;
    }
    
    /**
     * helper function - retrieves from IntelliWidgetMainStrings class.
     */
    function get_label( $key ){
      
        return IntelliWidgetMainStrings::get_label( $key );
    }
    
    /**
     * helper function - retrieves from IntelliWidgetMainStrings class.
     */
    function get_menu( $key ){
      
        return IntelliWidgetMainStrings::get_menu( $key );
    }
    
    function get_nav_menus() {
      
        if ( !isset( $this->menus ) ):
            $nav_menus = $this->get_menu( 'default_nav' );
            $menus = get_terms( 'nav_menu', array( 'hide_empty' => FALSE ) );
            foreach ( $menus as $menu )
                $nav_menus[ $menu->term_id ] = $menu->name;
            $this->menus = $nav_menus;
        endif;
        return $this->menus;
    }

    function get_nonce_url( $id, $action, $box_id = NULL ) {
      
        global $pagenow; 
        $val = 'delete' == $action ? $box_id : 1;
        return wp_nonce_url( admin_url( $pagenow . '?iw' . $action . '=' . $val . '&objid=' . $id ), 'iw' . $action );
    }
        
    /**
     * Get list of posts as select options. Selects all posts of the type( s ) specified in the instance data
     * and returns them as a multi-select menu. To enable filtering, any selected posts must be passed back
     * with the return set.
     */
    function get_posts_list( $profiles = FALSE ) {
      
        if ( is_multisite() ):
            $this_site_id = get_current_blog_id();
            if ( iwinstance()->get( 'site_id' ) != $this_site_id )
                switch_to_blog( intval( iwinstance()->get( 'site_id' ) ) );	
        endif;
        iwinstance()->set( 'page', $this->val2array( iwinstance()->get( 'page' ) ) );
        iwinstance()->set( 'profiles_only', $profiles );
        $posts = iwquery()->post_list_query();
    	$output                         = '';
	    if ( ! empty( $posts ) ) {
            $args = array( $posts, 0 );
            $walker = new IntelliWidgetMainWalker(); 
	        $output .= call_user_func_array( array( $walker, 'walk' ), $args );
	    }

        if ( is_multisite() && ms_is_switched() )
            restore_current_blog();    

	    return $output;
    }

    function get_profile_data( $id, $objtype ){
        $data = array(
            'profiles'  => array(),
            'box_map'   => $this->get_box_map( $id, $objtype )
        );
        ksort( $data[ 'box_map' ] );
        foreach( $data[ 'box_map' ] as $box_id => $sidebar_widget_id )
            $data[ 'profiles' ][ $box_id ] = iwinstance()->defaults( $this->get_meta(
                $id, 
                '_intelliwidget_data_', 
                $objtype, 
                $box_id
            ) );
        return $data;
    }
        
    function get_profile_form( $profile ) {
      
        ob_start();
        $this->render_profile_form( $profile );
        return ob_get_clean();
    }
    
    function get_tax_menu() {
      
        if ( !isset( $this->tax_menu ) ):
            $this->load_terms();
            $menu = array( '' => __( 'None', 'intelliwidget' ) );
            foreach ( array_keys( $this->terms ) as $name ):
                $taxonomy = get_taxonomy( $name );
                $menu[ $name ] = $taxonomy->label;
            endforeach;
            $this->tax_menu = $menu;
        endif;
        return $this->tax_menu;
    }

    /**
     * All terms are retrieved and cached and then each instance is filtered by the Walker class.
     */
    function get_terms_list() {

    	
    	$output = '';
        $post_types = $this->val2array( iwinstance()->get( 'post_types' ) );
        iwinstance()->set( 'terms', $this->val2array( iwinstance()->get( 'terms' ) ) );
        $this->load_terms();
        $terms = array();
        foreach ( $this->val2array( preg_grep( '/post_format/', get_object_taxonomies( $post_types ), PREG_GREP_INVERT ) ) as $tax ):
			if ( isset( $this->terms[ $tax ] ) )
                $terms = array_merge( $terms, $this->terms[ $tax ] );
        endforeach;
	    if ( empty( $terms ) ):
            return FALSE;
        else:
            $args = array( $terms, 0 );
            $walker = new IntelliWidgetMainTermsWalker(); 
	        $output .= call_user_func_array( array( $walker, 'walk' ), $args );
	    endif;
        return $output;
    }
    
    /**
     * helper function - retreives from IntelliWidgetMainStrings class
     */
    function get_tip( $key ){
      
        return IntelliWidgetMainStrings::get_tip( $key );
    }
    
    /**
     * Return a list of template files in the theme folder( s ) and plugin folder.
     * Templates actually render the output to the widget based on instance settings
     *
     * @return <array>
     */
    function get_widget_templates() {

        if ( !isset( $this->templates ) ):
            $templates  = array();
            $paths      = array();
            $parentPath = get_template_directory() . '/intelliwidget';
            $themePath  = get_stylesheet_directory() . '/intelliwidget';
            $paths[]    = IWELEMENTS_DIR . '/legacy/';
            $paths[]    = IWELEMENTS_DIR . '/templates/';
            $paths[]    = $parentPath;
            if ( $parentPath != $themePath ) $paths[] = $themePath;
            foreach ( $paths as $path ):
                if ( file_exists( $path ) && ( $handle = opendir( $path ) ) ):
                    while ( FALSE !== ( $file = readdir( $handle ) ) ):
                        if ( ! preg_match( "/^\./", $file ) && preg_match( '/\.php$/', $file ) ):
                            $file = str_replace( '.php', '', $file );
                            $name = str_replace( '-', ' ', $file );
                            $templates[ $file ] = ucfirst( $name ) . __( ' (Static)', 'intelliwidget' );
                        endif;
                    endwhile;
                    closedir( $handle );
                endif;
            endforeach;
            // hook custom actions into templates menu
            $templates = apply_filters( 'intelliwidget_templates', $templates );
            asort( $templates, phpversion() < 5.4 ? NULL : SORT_NATURAL | SORT_FLAG_CASE );
            $this->templates = $templates;
        endif;
        return $this->templates;
    }
        
    function is_widget( $obj ){
      
        return 'IntelliWidgetMainWidget' == get_class( $obj );
    }
    
    /**
     * Hide Custom Post Fields Meta Box by default
     */
    function hide_post_meta_box( $hidden ) {
      
        $hidden[] = 'intelliwidget_post_meta_box';
        return $hidden;
    }
    
    function load_terms() {
      
        // cache all available terms ( Lightweight IW objects )
        if ( isset( $this->terms ) ) return;
        $indexarray = array();
        $types = array_merge( IntelliWidgetMainCore::get_post_types(), array( 'attachment' ) );
        if ( ( $terms = get_terms( array_intersect(
            preg_grep( '/post_format/', get_object_taxonomies( $types ), PREG_GREP_INVERT ), 
                get_taxonomies( array( 'public' => TRUE, 'query_var' => TRUE ) )
            ), array( 'hide_empty' => FALSE ) ) ) ):
            foreach ( $terms as $object ):
                $indexarray[ $object->taxonomy ][] = $object;
            endforeach;
        endif;
        $this->terms = $indexarray;
    }
    
    function lookup_term( $id, $tax ) {
      
        foreach( $this->terms[ $tax ] as $term ):
            if ( $term->term_id == $id ) return $term->term_taxonomy_id;
        endforeach;
        return -1;
    }
    
    // Backwards compatability: replaces original category value with new term taxonomy id value
    function map_category_to_tax( $category ) {
      
        $catarr = $this->val2array( $category );
        $tax = array( 'category' );
        if ( !isset( $this->terms ) ) $this->load_terms();
        return array_map( array( $this, 'lookup_term' ), $catarr, $tax );
    }
    
    function media_view_settings( $settings, $post ){
      
        if ( is_object( $post ) ):
            $shortcode = '[gallery ids="-1"]';
     
            $settings[ 'iwgallery' ] = array( 'shortcode' => $shortcode ); 
        endif;
        return $settings;
    }
    
    function menu_order_column( $columns ) {
      
        $columns[ 'menu_order' ] = "Order";
        return $columns;
    }
    
    function options_header( $active_tab ){
        include( $this->formpath( 'options-header' ) );
    }
    
    // operations specific to modules tab
    function options_init() {
        
        /**
         * import/export functions
         */
        if ( ( $import_file = $this->check_import_file() ) ):
            IntelliWidgetMainImport::check_import_file( $import_file ); // confirm or run import
        else:
            if ( isset( $_POST[ 'iwf_export' ] ) ) // run export
                IntelliWidgetMainExport::export_options_file();
            if ( isset( $_POST[ 'iwf_import' ] ) ) // run upload
                IntelliWidgetMainImport::upload_options_file();
        endif;
        /**
         * end import/export functions
         */
        
        if ( isset( $_POST[ 'iwelements_save_plugin_options' ] ) )
            // fire action after setup theme to test if pagecells theme is being used
            $this->save_plugin_options();
        add_action( 'admin_print_styles',  array( $this, 'enqueue_admin' ) );
    }
    
    function check_import_file(){
        $uploads = wp_upload_dir();
        $path = $uploads[ 'basedir' ] . "/" . IWELEMENTS_IMPORT_FILE;
        if ( file_exists( $path ) )
            return $path;
        return FALSE;
    }
    
    function _error( $msg ){
        wp_die( '<strong>' . $msg . '</strong>' );
    }
    
    function options_page() {
      
        //if ( empty( $this->admin_hook ) ):
            $hook = add_menu_page(
                __( 'IntelliWidget', 'intelliwidget' ), 
                __( 'IntelliWidget', 'intelliwidget' ), 
                'edit_theme_options', 
                'iwelements', 
                array( $this, 'options_panel' ),
                'dashicons-admin-generic',
                61
            );
            // only load plugin-specific data 
            // when options page is loaded
            add_action( 'load-' . $hook, array( $this, 'options_init' ) );
            $hook = add_submenu_page(
                'iwelements', 
                __( 'Modules', 'intelliwidget' ), 
                __( 'Modules', 'intelliwidget' ), 
                'edit_theme_options', 
                'iwelements', 
                array( $this, 'options_panel' )
            );
            add_action( 'load-' . $hook, array( $this, 'options_init' ) );        

       // endif;
    }

    function options_panel() {
      
        global $pagenow;
        
        include( $this->formpath( 'modules' ) );
    }
    
    function options_tab( $active_tab ){
      
        global $pagenow;
        include( $this->formpath( 'options-tab-modules' ) );
    }

    function order_column_register_sortable( $columns ){
    
      $columns['menu_order'] = 'menu_order';
      return $columns;
    }
    
    /**
     * Generate input form that applies to posts
     * @return  void
     */
    function post_cdf_meta_box() {
      
        foreach ( IntelliWidgetMainCore::get_post_types() as $type ):
            add_meta_box( 
                'intelliwidget_post_meta_box',
                $this->get_label( 'cdf_title' ),
                array( $this, 'post_cdf_meta_box_form' ),
                $type,
                'side',
                'low'
            );
        endforeach;
        add_filter( 'default_hidden_meta_boxes', array( $this, 'hide_post_meta_box' ) );
    }
    
    /**
     * Output the form in the post meta box. Params are passed by add_meta_box() function
     */
    function post_cdf_meta_box_form( $post, $metabox ) {
      
        if ( !isset( $this->objid ) )
            $this->objid = $post->ID;
        include( $this->formpath( 'custom-fields' ) );
    }
    
    function post_form_actions() {
      
        $this->objtype  = 'post';
        $this->idfield  = 'post_ID';
        add_action( 'add_meta_boxes', array( $this, 'post_main_meta_box' ) );
        add_action( 'add_meta_boxes', array( $this, 'post_cdf_meta_box' ) );
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
    }
    
    /**
     * Generate input form that applies to entire page ( add new, copy settings )
     * @return  void
     */
    function post_main_meta_box() {
      
        // set up meta boxes
        foreach ( IntelliWidgetMainCore::get_post_types() as $type ):
            add_meta_box( 
                'intelliwidget_main_meta_box',
                $this->get_label( 'metabox_title' ),
                array( $this, 'post_meta_box_form' ),
                $type,
                'side',
                'low'
            );
        endforeach;
    }
    
    /**
     * Output the form in the page-wide meta box. Params are passed by add_meta_box() function
     */
    function post_meta_box_form( $post, $metabox ) {
      
        if ( !isset( $this->objid ) )
            $this->objid = $post->ID;
        $this->render_profiles( $post->ID, $post->post_type );
    }
    
    /**
     * This hook fires on save_post action - entire post admin page update, not ajax save
     */
    function post_save_data( $post_id ) {
      
        /**
         * Skip auto-save and revisions. wordpress saves each post twice, once for the revision and once to update
         * the actual post record. The parameters passed by the 'save_post' action are for the revision, so 
         * we must use the post_ID passed in the form data, and skip the revision. 
         */
        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) )
            //|| ( !empty( $post ) && !in_array( $post->post_type, array( 'post', 'page' ) ) ) )
            return FALSE;
        if ( wp_doing_ajax() && !$this->validate_post( 'iwpage_%s', 'iwpage_%s', FALSE ) )
            return FALSE;
        elseif ( isset( $_POST[ $this->idfield ] ) )
            $this->objid = sanitize_text_field( $_POST[ $this->idfield ] );
        else
            return FALSE;
        
        //$this->save_data(); -- do not save IW Profiles when saving entire post admin page
        // save custom post data if it exists
        $this->save_cdf_data();
        // save copy page id ( i.e., "use settings from ..." ) if it exists
        $this->save_copy_id();
    }

    function render_gallery_settings( $widgetobj ) { 
      
        include( $this->formpath( 'gallery' ) );
    }
    
    function render_nav_menu_settings( $widgetobj ) { 
      
        include( $this->formpath( 'nav-menu' ) );
    }

    function render_post_selection_menus( $widgetobj ) {       
      
        include( $this->formpath( 'post-menu' ) );
    }
    
    function render_post_selection_settings( $widgetobj ) { 
      
        include( $this->formpath( 'post-settings' ) );
    }

    function render_addl_text_settings( $widgetobj ) { 
      
        include( $this->formpath( 'addl-text' ) );
    }

    function render_appearance_settings( $widgetobj ) { 
      
        include( $this->formpath( 'appearance-settings' ) );
    }

    function render_general_settings( $widgetobj ) { 
      
        include( $this->formpath( 'general-settings' ) );
    }
    
    function render_menu_order( $column ) {
      
        global $post;
        if ( 'menu_order' == $column )
            echo $post->menu_order;
    }

    function render_profile_footer() {
      
        echo "</div></div>\n";
    }
      
    /**
     * this is the main IntelliWidget "profile" control panel
     */
    function render_profile_form( $widgetobj ) {
      
        include( $this->formpath( 'main' ) );
    }
  
    function render_profile_header( $widgetobj, $profilekey ) {
      
        printf(
            '<div class="postbox iw-collapsible closed panel-%4$s" id="%1$s-panel" '
            . 'title="' . __( 'Click to toggle', 'intelliwidget' ) . '">'
            . '<div class="iw-toggle" title="' . __( 'Click to toggle', 'intelliwidget' ) . '"></div>'
            . '<h4 title="%2$s">%3$s</h4><div id="%1$s-panel-inside" class="inside">', 
            $widgetobj->get_field_id( $profilekey ),
            $this->get_tip( $profilekey ),
            $this->get_label( $profilekey ),
            $profilekey
        );
    }

    function render_profiles( $id, $type = NULL ) {
        $profile_data = $this->get_profile_data( $id, $this->objtype );
        $merged_data = apply_filters( 'intelliwidget_merge_profiles', $profile_data, $this->objtype, $type );
        if ( $profile_data != $merged_data )
            // save profiles if overridden by conditional profiles
            $this->save_data( $profile_data );
        $profile_data = $merged_data;
        // add copy profiles from... form
        if ( 'condset' != $this->objtype ):
            $this->get_copy_id( $id );
            include( $this->formpath( 'copy' ) );
        endif;
        // add new profile link
        include( $this->formpath( 'add' ) );
        if ( $profile_data ):
            $tabs = $form = '';
            foreach( $profile_data[ 'profiles' ] as $box_id => $instance ):
                // filter to restrict settings to conditional set
                iwinstance()->defaults( $instance );
                $profile = new IntelliWidgetMainProfile( $id, $box_id );
                if ( !( $name = $this->get_intelliwidget_name( iwinstance()->get( 'replace_widget' ) ) ) )
                    $name = $this->get_intelliwidget_name( 'none' );
                $profile->set_title( $name );

                $tabs .= $profile->get_tab( iwinstance()->get( 'nickname' ) ) . "\n";
                $form .= $profile->begin_profile() 
                    . $this->get_profile_form( $profile )
                    . $profile->end_profile() . "\n";
            endforeach;
            include( $this->formpath( 'profiles' ) );
        endif;
    }
    
    function save_cdf_data() {
      
        // reset the data array
        $prefix    = 'intelliwidget_';
        if ( empty( $this->objid ) )
            return;
        foreach ( $this->get_fields( 'custom' ) as $cfield ):
            $cdfield = $prefix . $cfield;
            if ( array_key_exists( $cdfield, $_POST ) ):
                if ( empty( $_POST[ $cdfield ] ) || '' == $_POST[ $cdfield ] ):
                    $this->delete_meta( $this->objid, $cdfield );
                else:
                    $newdata = $_POST[ $cdfield ];
                    if ( !current_user_can( 'unfiltered_html' ) ):
                        $newdata = stripslashes( 
                        wp_filter_post_kses( addslashes( $newdata ) ) ); 
                    endif;
                    $this->update_meta( $this->objid, $cdfield, $newdata );
                endif;
            endif;
        endforeach;
        if ( isset( $_POST[ 'intelliwidget_menu_order' ] ) ):
            $mo = abs( intval( trim( $_POST[ 'intelliwidget_menu_order' ] ) ) );
            // bypass update post headaches and just update the menu order column of the post record directly
            global $wpdb;
            $sql = 'UPDATE ' . $wpdb->prefix . 'posts SET menu_order = %d WHERE ID = %d';
            $sql = $wpdb->prepare( $sql, $mo, $this->objid );
            $wpdb->query( $sql );
            //wp_update_post( array( 'ID' => $post_id, 'menu_order' => $mo ) );
        endif;
    }
    
    function save_copy_id() {
      
        $copy_id = isset( $_POST[ 'intelliwidget_widget_page_id' ] ) ? intval( $_POST[ 'intelliwidget_widget_page_id' ] ) : NULL;
        if ( isset( $copy_id ) )
            $this->update_meta( $this->objid, '_intelliwidget_', $copy_id, 'widget_page_id' );
    }
    
    function save_data( $profile_data = array() ) {
        if ( empty( $profile_data ) ):
            // reset the data array
            $post_data = array();
            $prefix    = 'intelliwidget_';
            // since we can now save a single meta box via ajax post, 
            // we need to manipulate the existing boxmap
            $box_map = $this->get_box_map( $this->objid, $this->objtype );
            // allow customization of input fields
            /***
             * Here is some perlesque string handling. Using grep gives us a subset of relevant data fields
             * quickly. We then iterate through the fields, parsing out the actual data field name and the 
             * box_id from the input key.
             */
            foreach( preg_grep( '#^' . $prefix . '#', array_keys( $_POST ) ) as $field ):
                // find the box id and field name in the post key with a perl regex
                preg_match( '#^' . $prefix . '(\d+)_(\d+)_([\w\-]+)$#', $field, $matches );
                if ( count( $matches ) ):
                    if ( !( $this->objid == $matches[ 1 ] ) || !( $box_id = $matches[ 2 ] ) ) continue;
                    $name      = $matches[ 3 ];
                    // sanitization comes later
                    $post_data[ $box_id ][ $name ] = $_POST[ $field ];
                else: 
                    continue;
                endif;
            endforeach;
        else:
            $post_data = $profile_data[ 'profiles' ];
            $box_map = $profile_data[ 'box_map' ];
        endif;
        // track meta boxes updated
        $boxcounter = 0;
        // additional processing for each box data segment
        foreach ( $post_data as $box_id => $new_instance ):
            // get current values
            $old_instance = $this->get_meta( $this->objid, '_intelliwidget_data_', $this->objtype, $box_id );
      
      
            $old_instance = apply_filters( 'intelliwidget_update_profile_data', $old_instance, $new_instance );
            // base64 encoding saves us from markup serialization heartburn
            if ( isset( $old_instance[ 'custom_text' ] ) ) 
                $old_instance[ 'custom_text' ] = base64_encode( $old_instance[ 'custom_text' ] );
            if ( isset( $old_instance[ 'custom_text' ] ) ) 
                $old_instance[ 'hide_no_posts' ] = base64_encode( $old_instance[ 'hide_no_posts' ] );
      
            // save new data
            $this->update_meta( $this->objid, '_intelliwidget_data_', $old_instance, $box_id );
        
            // update box map
            $box_map[ $box_id ] = $this->set_box_map( $old_instance[ 'replace_widget' ], $old_instance[ 'nickname' ] );
            // increment box counter
            $boxcounter++;
        endforeach;
        if ( $boxcounter )
            // if we have updates, save new map
            $this->update_meta( $this->objid, '_intelliwidget_', $box_map, 'map' );
    }
        
    /**
     * save as serialized objects.
     * DO NOT AUTOLOAD 
     * or bad things may happen...
     */
    function save_options() {
      
        $res = update_site_option( 
            IWELEMENTS_OPTIONS,
            IntelliWidgetMainCore::$options
        );
        return $res;
    }
    
    function save_plugin_options() {
      
        /** FIXME: this should not be hardcoded **/
        if ( $this->validate_post( 'iwelementsupd', '_wpnonce' ) ) :
            $noticestr = '&updated=1';
            $sanitized_update_key = preg_replace( "/\W/", '', sanitize_text_field( $_POST[ 'iwelements_update_key' ] ) );
            $this->set_option( 'update_key', $sanitized_update_key );
            $this->set_option( 'disable_emojis', isset( $_POST[ 'disable_emojis' ] ) );
            $this->set_option( 'use_shortcodes', isset( $_POST[ 'use_shortcodes' ] ) );
            $this->set_option( 'use_condset', isset( $_POST[ 'use_condset' ] ) );
            $this->set_option( 'use_legacy', isset( $_POST[ 'use_legacy' ] ) );
            $this->set_option( 'use_iwicons', isset( $_POST[ 'use_iwicons' ] ) );
            $this->set_option( 'use_switcher', isset( $_POST[ 'use_switcher' ] ) );
            if ( isset( $_POST[ 'use_bootstrap' ] ) )
                $this->set_option( 'use_bootstrap', sanitize_text_field( $_POST[ 'use_bootstrap' ] ) );
            do_action( 'iwelements_save_plugin_options' );
            $this->save_options();
        else:
            $noticestr = '&error=invalid';
        endif;
        $this->redirect( $noticestr );
    }
    
    function redirect( $noticestr = '' ){
        global $pagenow;
        wp_safe_redirect( admin_url( $pagenow . '?page=iwelements' . $noticestr ) );
        die();
    }
    
    function set_box_map( $widget_id, $nickname ){
      
        if ( empty( $widget_id ) || 'none' == $widget_id ):
            if ( empty( $nickname ) ):
                return '';
            else:
                return $this->normalize_nickname( $nickname );
            endif;
        endif;
        return $widget_id;
    }
    
    function set_option( $name, $value, $key = NULL ) {
      
        if ( !empty( $key ) && is_array( IntelliWidgetMainCore::$options[ $name ] ) ):
            if ( NULL === $value ) unset( IntelliWidgetMainCore::$options[ $name ][ $key ] );
            else IntelliWidgetMainCore::$options[ $name ][ $key ] = $value;
            return;
        endif;
        if ( NULL === $value ) unset( IntelliWidgetMainCore::$options[ $name ] );
        else IntelliWidgetMainCore::$options[ $name ] = $value;
    }
    
    function render_tax_menu_settings( $widgetobj ) { 
      
        include( $this->formpath( 'tax-menu' ) );
    }
    
    function template_panel_tab( $id, $name, $active ) {
      
        include( $this->formpath( 'templatetab' ) );
    }
    
    function render_timestamp_form( $field, $post_date ){
      
        include( $this->formpath( 'timestamp' ) );
    }
    
    function update_meta( $id, $optionname, $data, $index = NULL ) {
      
        
        if ( empty( $id ) || empty( $optionname ) )
            return FALSE;
        switch( $this->objtype ):
            case 'post':
                if ( isset( $index ) ) $optionname .= $index;
                $serialized = maybe_serialize( $data );
                
                $res = update_post_meta( $id, $optionname, $serialized );
              
                break;
            default:
                $optionname = 'intelliwidget_data_' . $this->objtype . '_' . $id;
                if ( isset( $index ) ):
                    if ( is_multisite() )
                        switch_to_blog( get_main_site_id() );	   
                    if ( !( $option = get_option( $optionname ) ) )
                        $option = array();
                    $option[ $index ] = $data;
                    $res = update_option(
                        $optionname, 
                        $option,
                        FALSE
                    );
                    if ( is_multisite() && ms_is_switched() )
                        restore_current_blog();  
                  
                endif;
        endswitch;
    }
           
    function val2array( $value ) {
      
        $value = empty( $value ) ? 
            array() : ( is_array( $value ) ?
                $value : explode( ',', $value ) );
        sort( $value );
        return $value;
    }
        
    function validate_post( $action, $noncefield, $is_ajax = FALSE ) {
      
        if ( 'iwelementsupd' == $action ):
            $capability = 'activate_plugins';
        elseif( 'iwfexport' == $action || 'iwfimport' == $action ):
            $capability = 'manage_options';
        else:
            if ( isset( $_POST[ 'idfield' ] ) )
                $this->idfield  = sanitize_text_field( $_POST[ 'idfield' ] );
            if ( isset( $_POST[ 'objtype' ] ) )
                $this->objtype  = sanitize_text_field( $_POST[ 'objtype' ] );
            if ( isset( $_POST[ 'objid' ] ) )
                $this->objid = sanitize_text_field( $_POST[ 'objid' ] );
            elseif ( isset( $_POST[ $this->idfield ] ) )
                $this->objid = sanitize_text_field( $_POST[ $this->idfield ] );
            else
                $this->objid = NULL;
            if ( empty( $this->objtype ) ):
              
                return FALSE;
            endif;
            if ( empty( $this->idfield ) ):
              
                return FALSE;
            endif;
            $capability = 'post' == $this->objtype ?
                'edit_post' :
                'manage_options';
            if ( $this->objid ):
                $action = sprintf( $action, $this->objid );
                $noncefield = sprintf( $noncefield, $this->objid );
            endif;
          
        endif;
        /*
        if ( 'POST' != $_SERVER[ 'REQUEST_METHOD' ] ):
          
            return FALSE;
        endif;
        */
        if ( $is_ajax && !check_ajax_referer( $action, $noncefield, FALSE ) ):
          
            return FALSE;
        endif;
        if ( !$is_ajax && !check_admin_referer( $action, $noncefield, FALSE ) ):
          
            return FALSE;
        endif;
        if ( !current_user_can( $capability, $this->objid ) ):
          
            return FALSE;
        endif;
        return TRUE;
    }
    
    function widget_form_actions(){
      
        $this->objtype  = 'widget';
        $this->idfield  = 'widget-id';
    }

    
    
    
    
    
    
    
}