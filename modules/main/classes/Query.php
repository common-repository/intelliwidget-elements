<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * Query.php - IntelliWidgetMain Query Class
 * based in part on code from Wordpress core post.php and query.php
 *
 * @package IntelliWidgetMain
 * @subpackage includes
 * @author Jason C Fleming
 * @copyright 2014-2015 Lilaea Media LLC
 * @access public
 */
class IntelliWidgetMainQuery {
    static $instance;
    var $post;
    var $posts;
    var $post_count   = 0;
    var $found_posts  = 0;
    var $in_the_loop  = FALSE;
    var $current_post = -1;
    var $postmeta;
    var $ids;
    
    /**
     * Make me a global singleton
     */
    function __construct(){
        self::$instance = $this;
    }
    
    function reset_posts(){
        $this->post = NULL;
        $this->posts = array();
        $this->post_count   = 0;
        $this->found_posts  = 0;
        $this->in_the_loop  = FALSE;
        $this->current_post = -1;
        $this->postmeta = array();
        $this->ids = array();
        
    }
	/**
	 * Set up the next post and iterate current post index.
	 *
	 * @return next post.
	 */
	function next_post() {

		$this->current_post++;

		$this->post = $this->posts[ $this->current_post ];
        $this->post->intelliwidget_count    = $this->current_post + 1;
        $this->post->intelliwidget_is_first = 0 == $this->current_post;
        $this->post->intelliwidget_is_last  = $this->current_post + 1 == $this->post_count;
		return $this->post;
	}
	/**
	 * Whether there are more posts available in the loop.
	 *
	 *
	 * @return bool True if posts are available, FALSE if end of loop.
	 */
	function have_posts() {
		if ( $this->current_post + 1 < $this->post_count ) {
			return TRUE;
		} elseif ( $this->current_post + 1 == $this->post_count && $this->post_count > 0 ) {
			// Do some cleaning up after the loop
			$this->rewind_posts();
		}

        if ( is_multisite() && ms_is_switched() ):
            restore_current_blog();
            //echo '<!-- End Loop Restored Site ' . get_current_blog_id() . ' -->' . PHP_EOL;
        endif;
		$this->in_the_loop = FALSE;
		return FALSE;
	}

	/**
	 * Rewind the posts and reset post index.
	 *
	 */
	function rewind_posts() {
		$this->current_post = -1;
		if ( $this->post_count > 0 ) {
			$this->post = $this->posts[ 0 ];
		}
	}

	/**
	 * Sets up the current post.
	 *
	 * Retrieves the next post, sets up the post, sets the 'in the loop'
	 * property to TRUE.
	 *
	 * @uses iwquery()->post
	 */
	function the_post() {
		$this->in_the_loop = TRUE;

		if ( -1 == $this->current_post ){ // loop has just started
            // stub for future functionality
        }
        if ( is_multisite() && ms_is_switched() ):
            restore_current_blog();
            //echo '<!-- Restored Site ' . get_current_blog_id() . ' -->' . PHP_EOL;
        endif;
		$this->next_post();
        if ( is_multisite() && isset( $this->post->site_id ) ): //&& get_current_blog_id() != iwquery()->post->site_id ):
            switch_to_blog( $this->post->site_id );
            //echo '<!-- Switched to Site ' . get_current_blog_id() . ' -->' . PHP_EOL;
        endif;
	}
    
    /**
     * Intelliwidget has a lot of internal logic that can't be done efficiently using the standard
     * WP_Query parameters. This function dyanamically builds a custom query so that the majority of the 
     * post and postmeta data can be retrieved in two optimized db queries.
     */
    function iw_query() {
        global $wpdb;
        $select = "
SELECT DISTINCT
    p1.ID
FROM {$wpdb->posts} p1
        ";
        
        $joins = array();

        if( iwinstance()->get( 'include_private' ) && current_user_can( 'read_private_posts' ) ):
            $clauses = array(
                "(p1.post_status IN ('publish','private','inherit'))",
                //"(p1.post_password = '' OR p1.post_password IS NULL)",
            );
        else:
            $clauses = array(
                "(p1.post_status IN ('publish','inherit'))",
                //"(p1.post_password = '' OR p1.post_password IS NULL)",
            );
        endif;
        // placeholders
        $prepargs   = array();
        
        // meta query joins
        foreach( array( '1', '2', '3' ) as $metasuffix ):
            $metak = 'metak' . $metasuffix;
            $pm     = 'pm' . $metasuffix;
            if ( iwinstance()->get( $metak ) )

                $joins[] = "
    LEFT JOIN {$wpdb->postmeta} $pm ON $pm.post_id = p1.ID
        AND $pm.meta_key = " . $this->prep_array( iwinstance()->get( $metak ), $prepargs, 's' );
        
        endforeach;

        // taxonomies
        $term_clauses = array();
        $taxonomies = preg_grep( '/post_format/', get_object_taxonomies( 
            ( array ) iwinstance()->get( 'post_types' ) ), 
                PREG_GREP_INVERT );
        // use subqueries for taxonomy AND search to accommodate child terms
        if ( iwinstance()->get( 'terms' ) && iwinstance()->get( 'allterms' ) ):
            $ttids = $this->get_term_taxonomy_ids( iwinstance()->get( 'terms' ), $taxonomies, TRUE ); // true returns subset for each term
            foreach ( $ttids as $ttid_array ):
                $term_clauses[] = " ( p1.ID " . ( '2' == iwinstance()->get( 'allterms' ) ? 'NOT ' : '' ) . "IN ( SELECT object_id FROM {$wpdb->term_relationships}
                WHERE term_taxonomy_id IN ( " . $this->prep_array( $ttid_array, $prepargs, 'd' ) . " ) ) ) ";
            endforeach;
        // constrain results to current queried taxonomy
        else:
            if ( iwinstance()->get( 'same_term' ) ):
                // get terms from wp_query object and add to terms array
                $t = get_queried_object();
                if ( isset( $t->term_taxonomy_id ) ):
                    $ttids = $this->get_term_taxonomy_ids( 
                        $t->term_taxonomy_id, 
                        $t->taxonomy 
                    );
                else:
                    $ttids = wp_get_post_terms( 
                        $t->ID, 
                        $taxonomies, 
                        array( 'fields' => 'tt_ids' ) 
                    );
                endif;
            // otherwise match all child terms
            elseif ( iwinstance()->get( 'terms' ) ):
                $ttids = $this->get_term_taxonomy_ids( iwinstance()->get( 'terms' ), $taxonomies );
            endif;
            if ( !empty( $ttids ) ):
                $term_clauses[] = '( tx1.term_taxonomy_id IN ( ' . $this->prep_array( $ttids, $prepargs, 'd' ) . ' ) )';
                $joins[] = "LEFT JOIN {$wpdb->term_relationships} tx1 ON p1.ID = tx1.object_id ";
            endif;
        endif;

        // include specific posts along with any term matches
        if ( !( $pages = iwinstance()->get( 'page' ) ) || empty( $pages[ 0 ] ) || in_array( -1, $pages ) ):
            iwinstance()->set( 'page', array() );
            if ( count( $term_clauses ) ):
                $clauses[] = implode( ' AND ', $term_clauses );
            endif;
        else:
            //echo 'PAGES: ' . print_r( $pages, TRUE );
            $page_clause = '(p1.ID IN ('. $this->prep_array( $pages, $prepargs, 'd' ) . ') )';
            if ( count( $term_clauses ) ):
                $clauses[] = '( ( ' . implode( ' AND ', $term_clauses ) . ' ) OR ' . $page_clause . ' )';
            else:
                $clauses[] = $page_clause;
            endif;
        endif;
        // select next tier of child pages
        if ( iwinstance()->get( 'child_pages' ) ):
            global $post;
            if ( is_object( $post ) )
                $clauses[] = '(p1.post_parent = ' . $post->ID . ')';
        endif;
        // post types
        if ( !iwinstance()->get( 'post_types' ) ):
            iwinstance()->set( 'post_types', 'page' );
        endif;
        $clauses[] = '(p1.post_type IN ('. $this->prep_array( iwinstance()->get( 'post_types' ), $prepargs ) . ') )';

        
        /**
         * instead, add three user-defined meta conditions:
         */
        
        foreach( array( '1', '2', '3' ) as $metasuffix ):
            // meta query
            $metak  = 'metak' . $metasuffix;
            $metac  = 'metac' . $metasuffix;
            $metav  = 'metav' . $metasuffix;
            $pm     = 'pm' . $metasuffix;
            if ( iwinstance()->get( $metak ) && iwinstance()->get( $metac ) ):
                if ( ! ( $value = iwinstance()->get( $metav ) ) )
                    $value = '';
                if ( '{now}' == $value )
                    $value = date( 'Y-m-d h:i:s' , current_time( 'timestamp' ) );
                switch ( iwinstance()->get( $metac ) ):

                    case 'eq':
                        $stm = "$pm.meta_value IS NOT NULL AND $pm.meta_value = " . $this->prep_array( array( $value ), $prepargs, 's' );
                        break;
                    case 'ne':
                        $stm = "$pm.meta_value IS NOT NULL AND $pm.meta_value != " . $this->prep_array( array( $value ), $prepargs, 's' );
                        break;
                    case 'gt':
                        $stm = "$pm.meta_value IS NOT NULL AND $pm.meta_value > " . $this->prep_array( array( $value ), $prepargs, 's' );
                        break;
                    case 'lt':
                        $stm = "$pm.meta_value IS NOT NULL AND $pm.meta_value < " . $this->prep_array( array( $value ), $prepargs, 's' );
                        break;
                    case 'gn':
                        $stm = "$pm.meta_value IS NOT NULL AND CAST($pm.meta_value AS DECIMAL) > CAST(" . $this->prep_array( array( $value ), $prepargs, 's' ) . " AS DECIMAL)";
                        break;
                    case 'ln':
                        $stm = "$pm.meta_value IS NOT NULL AND CAST($pm.meta_value AS DECIMAL) < CAST(" . $this->prep_array( array( $value ), $prepargs, 's' ) . " AS DECIMAL)";
                        break;
                    case 'in':
                        $stm = "$pm.meta_value IS NOT NULL AND $pm.meta_value IN (" . $this->prep_array( $value, $prepargs, 's' ) . ")";
                        break;
                    case 'ni':
                        $stm = "$pm.meta_value IS NOT NULL AND $pm.meta_value NOT IN (" . $this->prep_array( $value, $prepargs, 's' ) . ")";
                        break;
                    case 'nl':
                        $stm = "$pm.meta_value IS NULL";
                        break;
                    case 'nn':
                    default:
                        $stm = "$pm.meta_value IS NOT NULL";
                        break;        
                endswitch;
                $clauses[] = '( ' . $stm . ' )';

            endif;
        // end meta query
        endforeach;
        
        $query = $select . implode( ' ', $joins ) . ' WHERE ' . implode( "\n AND ", $clauses ) . $this->orderby();
        //echo "<!--\n" . $query . "\n-->\n";
        return $wpdb->prepare( $query, $prepargs );
        
    }
    
    function orderby() {
        $order = iwinstance()->get( 'sortorder' ) == 'ASC' ? 'ASC' : 'DESC';
        if ( !iwinstance()->get( 'daily' ) ):
            switch ( iwinstance()->get( 'sortby' ) ):
                /*case 'event_date':
                    $orderby = 'pm2.meta_value ' . $order;
                    break;
                    */
                case 'rand':
                    $orderby = 'RAND()';
                    break;
                case 'menu_order':
                    $orderby = 'p1.menu_order ' . $order;
                    break;
                case 'date':
                    $orderby = 'p1.post_date ' . $order;
                    break;
                case 'selection': // like post__in
                    if ( is_array( iwinstance()->get( 'page' ) ) && count( iwinstance()->get( 'page' ) ) )
                        $orderby = 'FIELD( p1.ID,' . implode( ',', iwinstance()->get( 'page' ) ) . ' )';
                    else
                        return '';
                    break;
                case 'title':
                default:
                    $orderby = 'p1.post_title ' . $order;
                    break;
            endswitch;
        else:
            $orderby = 'p1.ID ';
        endif;
        return ' ORDER BY ' . $orderby;
    }
    
    function get_posts() {
        
        $this->reset_posts();
        
        //echo __METHOD__ . "\n" . print_r( iwinstance(), TRUE );
        if ( !iwinstance()->get( 'page' )
            && !iwinstance()->get( 'terms' ) 
            && !iwinstance()->get( 'related' ) 
            && !iwinstance()->get( 'listdata' )
            && !iwinstance()->get( 'metak' )
            && !iwinstance()->get( 'metak1' )
            && !iwinstance()->get( 'child_pages' ) ):
            //die( 'not enough parameters' );
            return FALSE;
        endif;
        // skip empty galleries
        if ( 'gallery' == iwinstance()->get( 'content' ) 
            && in_array( -1, iwinstance()->get( 'page' ) )
            && !iwinstance()->get( 'terms' ) )
            return FALSE;

        // custom sorted profile - use listdata for posts and override any template actions with internal function
        if ( 'post_list' == iwinstance()->get( 'content' ) && ( $listdata = $this->get_listdata() ) ):
            //echo '<!-- ' . esc_html( print_r( $listdata, TRUE ) ) . ' -->';
            $this->posts = apply_filters( 'intelliwidget_get_listdata', $listdata );
            $this->post_count = count( $this->posts );
            $template = !iwinstance()->get( 'template' ) ? 'menu' : iwinstance()->get( 'template' );
            // replace default intelliwidget template action with hierarchical menu
            remove_all_actions( 'intelliwidget_action_' . $template );
            add_action( 'intelliwidget_action_' . $template, array( $this, 'hierarchical_menu' ) );
            return;
        endif;
        
        // get relation if applicible
        $qo = get_queried_object();
        if ( is_singular() 
            && iwinstance()->get( 'related' )
            && ( $pt = current( ( array ) iwinstance()->get( 'post_types' ) ) )
            && ( $related_id = get_post_meta( $qo->ID, '_intelliwidget_' . $pt . '_id', TRUE ) ) ):
        
            if ( $related_post = get_post( $related_id ) ):
                $this->posts = array( $related_post );
                $this->post_count = 1;
                return;
            endif;
        endif;

        
        /**
         * tabled until more testing
        // used cached post data if possible
        $cache_key = md5( serialize( $instance ) );
        
        $cache_key .= '_' . $current_site_id;
        global $post;
        if ( is_object( $post ) )
            $cache_key .= '_' . $post->ID;
        
        if ( $posts = get_site_transient( 'intelliwidget_cache_' . $cache_key ) ):
            $this->posts = $posts;
            $this->post_count = count( $posts );
            $this->found_posts = count( $posts );
        else:
         */
            $current_site_id = get_current_blog_id();
            if ( !iwinstance()->get( 'site_id' ) || 'current' == iwinstance()->get( 'site_id' ) ):
                $sites = array( $current_site_id );
            elseif ( 'all' == iwinstance()->get( 'site_id' ) ):
                $sites = array_keys( IntelliWidgetMainCore::get_sites() );
            else:
                $sites = array( iwinstance()->get( 'site_id' ) );
            endif;
        
            $main_site_id = get_main_site_id();

            foreach ( $sites as $site_id ):
                if ( is_multisite() ):
                    if ( 'all' == iwinstance()->get( 'site_id' ) && $site_id == $current_site_id && $main_site_id != $current_site_id )
                        continue;
                    if ( $site_id != $current_site_id )
                        switch_to_blog( $site_id );
                endif;
                global $wpdb;
                $query = $this->iw_query();
                //echo $query;
                $this->ids = $wpdb->get_col( $query, 0 );
                //print_r( $ids );
                $count = count( $this->ids );
                if ( $count ):
                    if (iwinstance()->get( 'daily' ) ):
                        $doy = gmdate( 'z', current_time( 'timestamp' ) );
                        $index = intval( $doy ) % $count;
                        $res = array_slice( $res, $index, 1 );
                    endif;
                    $clauses    = array();
                    $prepargs   = array();
                    $orderby    = $this->orderby();
                    // now flesh out objects
                    $select = "
        SELECT DISTINCT
            p1.ID,
            p1.post_content, 
            p1.post_excerpt,
            p1.post_type,
            p1.post_mime_type,
            p1.guid,
            p1.post_name,
            COALESCE(NULLIF(TRIM(p1.post_title), ''), " 
            . $this->prep_array( __( 'Untitled', 'intelliwidget' ), $prepargs ) . ") AS post_title,
            p1.post_date AS post_date,
            p1.post_author,
            'raw' AS filter,
            pm3.meta_value AS link_classes,
            pm4.meta_value AS alt_title,
            pm5.meta_value AS link_target,
            pm6.meta_value AS external_url,
            pm7.meta_value AS thumbnail_id
        FROM {$wpdb->posts} p1
        ";

        $joins = array( "
                
        LEFT JOIN {$wpdb->postmeta} pm3 ON pm3.post_id = p1.ID
            AND pm3.meta_key = 'intelliwidget_link_classes'
                    ", "
        LEFT JOIN {$wpdb->postmeta} pm4 ON pm4.post_id = p1.ID
            AND pm4.meta_key = 'intelliwidget_alt_title'
                    ", "
        LEFT JOIN {$wpdb->postmeta} pm5 ON pm5.post_id = p1.ID
            AND pm5.meta_key = 'intelliwidget_link_target'
                    ", "
        LEFT JOIN {$wpdb->postmeta} pm6 ON pm6.post_id = p1.ID
            AND pm6.meta_key = 'intelliwidget_external_url'
                    ", "
        LEFT JOIN {$wpdb->postmeta} pm7 ON pm7.post_id = p1.ID
            AND pm7.meta_key = '_thumbnail_id'
                    " );
                    $clauses[] = '(p1.ID IN ('. implode(',', $this->ids ) . ') )';

                    /**
                     * special cases:
                     * - skip post - exclude current post from results
                     * - related - display only posts with post_type relation to current post
                     * - sort by meta value - instance[ metas ]
                     */
                    if ( ( 'event_date' == iwinstance()->get( 'sortby' ) || iwinstance()->get( 'metas' ) || iwinstance()->get( 'skip_post' ) || iwinstance()->get( 'related' ) ) ):
                        // 2.3.7.4: using get_queried_object instead of global $post in case secondary query was not reset
                        if ( iwinstance()->get( 'skip_post' ) )
                            $clauses[] = "(p1.ID != {$qo->ID})";
                        if ( iwinstance()->get( 'related' ) )
                            $joins[] = "
        JOIN {$wpdb->postmeta} pm8 ON pm8.post_id = p1.ID
            AND pm8.meta_key = '_intelliwidget_" . $qo->post_type . "_id'
            AND pm8.meta_value = " . $qo->ID . "
                            ";
                        // special case for sorting by custom meta value
                        if ( 'event_date' == iwinstance()->get( 'sortby' ) || iwinstance()->get( 'metas' ) ):
                                if ( 'event_date' == iwinstance()->get( 'sortby' ) ):
                                    $metas = '_event_date_start_date';
                                else:
                                    $metas = iwinstance()->get( 'metas' );
                                endif;
                                $joins[] = "
        LEFT JOIN {$wpdb->postmeta} pm9 ON pm9.post_id = p1.ID
            AND pm9.meta_key = " . $this->prep_array( $metas, $prepargs, 's' );
                                $order = iwinstance()->get( 'sortorder' ) == 'ASC' ? 'ASC' : 'DESC';

                                $orderby = ' ORDER BY pm9.meta_value ' . $order;
                        endif;
                    endif;


        $items = intval( iwinstance()->get( 'items' ) );
        $limit = '';
        if ( !iwinstance()->get( 'daily' ) && !empty( $items )
            && ( !is_multisite() || !iwinstance()->get( 'paged' ) ) ):
            $limit = ' LIMIT %d';
            $prepargs[] = $items;
        endif;
        
        
        $query = $select . implode( ' ', $joins ) . ' WHERE ' . implode( "\n AND ", $clauses ) . $orderby . $limit;
                    $res      = $wpdb->get_results( $wpdb->prepare( $query, $prepargs ), OBJECT );
                else:
                    $res = array();
                endif;
                $this->merge_posts( $res, $site_id );
                $this->post_count += count( $res );
                $this->found_posts += count( $res );
                if ( is_multisite() && ms_is_switched() )
                    restore_current_blog();
            endforeach;
        /**
         * tabled until more testing
            set_site_transient( 'intelliwidget_cache_' . $cache_key, $this->posts, 60 * 60 * 24 ); // cache posts for 24 hours
        endif;
         */
        if ( is_multisite() 
            && ( 'all' == iwinstance()->get( 'site_id' ) || iwinstance()->get( 'paged' ) ) ):
            global $wp_query;
            $items  = intval( iwinstance()->get( 'items' ) );
            $offset = 0;
            $paged  = 0;
            if ( iwinstance()->get( 'paged' ) && isset( $wp_query->query_vars[ 'paged' ] ) && $wp_query->query_vars[ 'paged' ] ):
                $paged = $wp_query->query_vars[ 'paged' ];
                $offset = ( $paged - 1 ) * $items;

            endif;
            uasort( $this->posts, array( $this, 'sort_posts' ) );
            $this->posts = array_slice( 
                $this->posts, 
                $offset, 
                $items 
            );
            $this->post_count = count( $this->posts );
        endif;
    }

    function sort_posts( $a, $b ){
        return $a->post_date < $b->post_date ? 1 : -1;
    }
    
    function merge_posts( $posts, $site_id ){
        if ( is_multisite() ):
            $this->posts = (array)$this->posts;
            foreach ( $posts as $post ):
                $post->site_id = $site_id;
                $this->posts[] = $post;
            endforeach;
        else:
            $this->posts = $posts;
        endif;
    }
    /**
     * append values to args array and append corresponding placeholders to placeholders array
     * $args is array ref
     */
    function prep_array( $value, &$args, $type = 's' ) {
        $values = is_array( $value ) ? $value : explode( ',', $value );
        $placeholders = array();
        foreach( $values as $val ):
            $placeholders[] = ( 'd' == $type ? '%d' : '%s' );
            $args[] = 'w' == $type ? '%' . trim( $val ) . '%' : trim( $val );
        endforeach;
        return implode( ',', $placeholders );
    }
    
    /**
     * post_list_query
     * 
     * lightweight post query for use in menus.
     * Merges current selection ( page array ) with first 200 
     * results from text search ( pagesearch ).
     */
    function post_list_query() {
        
        if ( !iwinstance()->get( 'site_id' ) )
            $site_id = get_current_blog_id();

        if ( is_multisite() )
            switch_to_blog( $site_id );

        global $wpdb;
        $limit  = IWELEMENTS_MAX_MENU_POSTS;
        $args   = array();
        $clause = array();
		
        $selected = count( iwinstance()->get( 'page' ) ) ? 
            " ( SELECT ID IN ( " . $this->prep_array( iwinstance()->get( 'page' ), $args, 'd' ) . " ) ) AS selected " :
            " 1 AS selected ";
        
        $query = "
        SELECT
        ";
        if ( iwinstance()->get( 'profiles_only' ) ):
            $query .= " pm.meta_id as has_profile,
        ";
        endif; 
        $query .= "
            ID,
            post_title,
            post_type,
            post_parent,
            " . $selected . "
        FROM {$wpdb->posts}
        ";
        if ( iwinstance()->get( 'profiles_only' ) ):
            $query .= " JOIN {$wpdb->postmeta} pm ON pm.meta_key = '_intelliwidget_map' and pm.post_id = ID 
            LEFT JOIN {$wpdb->postmeta} pm2 ON pm2.post_id = ID AND pm2.meta_key = '_intelliwidget_widget_page_id'
        ";
            $limit = 0;
        endif;
        $query .= " WHERE post_type IN (" . $this->prep_array( iwinstance()->get( 'post_types' ), $args ) . ")
            AND (post_status = 'publish' " . ( current_user_can( 'read_private_posts' ) ? " OR post_status = 'private'" : '' ) . ")
			";
            //AND (post_password = '' OR post_password IS NULL)
        //";
        if ( iwinstance()->get( 'profiles_only' ) ):
            $query .= "
            AND ( pm2.meta_value IS NULL OR pm2.meta_value = '' OR pm2.meta_value = '0' )
            ";
        endif;
        // return currently selected posts in addition to text matches
        if ( iwinstance()->get( 'pagesearch' ) ):
            $clause[] = " ( post_title LIKE " . $this->prep_array( iwinstance()->get( 'pagesearch' ), $args, 'w' ) . " )
        ";
        else:
            $clause[] = " ( 1=1 ) ";
        endif;
        if ( count( iwinstance()->get( 'page' ) ) )
            $clause[] = " ( ID IN ( " . $this->prep_array( iwinstance()->get( 'page' ), $args, 'd' ) . " ) )
        ";
        if ( count( $clause ) )
            $query .= ' AND ( ' . implode( " OR ", $clause ) . " )
        "; 
        $query .= " ORDER BY selected DESC, post_type, post_title
        ";
        if ( $limit ): 
            $query .= " LIMIT " . $limit;
        endif;
        
        

        $res = $wpdb->get_results( $wpdb->prepare( $query, $args ), OBJECT );

        if ( is_multisite() && ms_is_switched() )
            restore_current_blog();
        return $res;
    }
    
    /**
     * terms_query
     *
     * Returns the most frequently used ( relevant ) term object from a list of term ids
     */
    function terms_query( $ttids = array() ) {
        global $wpdb;
        $args = array();
        $query = "
        SELECT t.*, tt.* 
        FROM $wpdb->terms AS t 
            INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id 
        WHERE term_taxonomy_id IN (" . $this->prep_array( $ttids, $args, 'd' ) . ")
        ORDER BY tt.count DESC
        LIMIT 1
        ";
        return $wpdb->get_row( $wpdb->prepare( $query, $args ), OBJECT );
    }
    
    function get_term_taxonomy_ids( $ttids, $taxonomies, $and = FALSE ) {
        $actual_ttids = array();
        $terms = array();
        foreach ( ( array ) $taxonomies as $taxonomy )
            $terms = iwctl()->get_term_hierarchy( $taxonomy ) + $terms;
        foreach ( ( array ) $ttids as $ttid ):
            if ( '' === $ttid ) continue;
            $child_ttids = iwctl()->get_term_children( $ttid, $terms );
            if ( $and ):
                // return array of arrays for AND
                $child_ttids[] = $ttid;
                $actual_ttids[] = $child_ttids;
            else:
                // return single array for OR
                $actual_ttids = array_merge( $actual_ttids, $child_ttids );
                $actual_ttids[] = $ttid;
            endif;
        endforeach;
        return $actual_ttids;
    }
    
    function get_ids(){
        return $this->ids;
    }

    /**
     * This is a modified version of WP paginate_links function 
     */
    function paginate_links( $args = array() ) {
        if ( iwinstance()->get( 'paged' ) ):
            $args = array(
                'total' => intval( $this->found_posts / ( iwinstance()->get( 'items' ) ?: 1 ) ) + 1,
                'prev_text' => __( '&laquo;' ),
                'next_text' => __( '&raquo;' ),
            );
            echo '<div class="navigation"><span class="pagination">' . paginate_links( $args ) . '</span></div>';
        endif;
    }
    
    function get_listdata(){
        if ( !iwinstance()->get( 'listdata' ) )
            return FALSE;
        if ( ( $data = json_decode( html_entity_decode( iwinstance()->get( 'listdata' ) ) ) )
            && is_array( $data )
            && count( $data ) ):
            foreach ( $data as $item )
                $item->site_id = iwinstance()->get( 'site_id' );
            return $data;
        endif;
        return FALSE;
    }
    

    /**
     * this function replaces intelliwidget template action when child profile's hierarchical list data is being used
     */
    function hierarchical_menu(){
        // use layout object from IntelliWidget Layouts if present
		
        if ( class_exists( 'IntelliWidgetTemplatesCore' )
            && !empty( IntelliWidgetTemplatesCore::$controller ) ):
            IntelliWidgetTemplatesCore::$controller->get_tree()->load_objects( iwinstance()->get( 'template' ) );
            if ( $template = IntelliWidgetTemplatesCore::$controller->get( iwinstance()->get( 'template' ) ) )
                $menu = new IntelliWidgetMainLayoutList( $template );
        endif;
        // otherwise use IntelliWidget's Main Menu walker
        if ( empty( $menu ) )
            $menu = new IntelliWidgetMainMenu();
        $menu->render( $this->posts );
    }
    
    function convert_listdata( $item = NULL ){
        if ( empty( $item ) )
            $item = $this->post;
        if ( !isset( $item->id ) )
            return;
        // handle legacy data
        // convert legacy data
        if ( !empty( $item->obj_id ) )
            $item->id = intval( $item->obj_id );
        if ( !isset( $item->type ) ):
            if ( preg_match( "/^00/", $item->id ) ): 
                $item->type = 'custom';
            else:
                $item->type = 'post';
            endif;
        endif;

        if ( 'post' != $item->type ):
            $this->post = $item;
            $this->post->ID = 0;
        elseif ( !( $this->post = get_post( intval( $item->id ) ) )
            || is_wp_error( $this->post ) ):
            return;
        endif;
        // get link
        if ( $item->url ):
            $this->post->external_url = html_entity_decode( $item->url );
        elseif ( 'term' == $item->type ):
            // IntelliWidget uses term_taxonomy_id for terms so we need to lookup the term_id
            // note that the second argument (taxonomy) is arbitrary because the true taxonomy is determined
            // by the term_taxonomy_id
            if ( $term = get_term_by( 'term_taxonomy_id', $item->id, 'blah' ) ):
                if ( is_wp_error( $term ) )
                    return;
            
                $this->post->external_url = get_term_link( $term->term_id );
                $this->post->post_content = $term->description;
            endif;
        endif;
        
        // failsafe for empty object
        if ( is_wp_error( $this->post->external_url ) )
            $this->post->external_url = '#';        
        $title = apply_filters( 'the_title', html_entity_decode( $item->title ), $this->post->ID );
        $this->post->post_title = $title;
        $this->post->alt_title = $title;
        $this->post->link_target = empty( $item->target ) ? '' : $item->target;

        // get thumbnail
        if ( isset( $item->image ) && $item->image ):
            if ( 'featured' == $item->image )
                $key = '_thumbnail_id';
            else
                $key = '_intelliwidget_' . $item->image . '_id';
            // for posts
            if (
                ( 'post' == $item->type 
                && ( $thumbnail_id = get_post_meta( $item->id, $key, TRUE ) ) )
                ||
                ( 'term' == $item->type && isset( $term )
                && ( $thumbnail_id = get_term_meta( $term->term_id, $key, TRUE ) ) ) ):
                if ( !is_wp_error( $thumbnail_id ) )
                    $this->post->thumbnail_id = $thumbnail_id;
            endif;
        endif;
        // get classes
        $classes = empty( $item->class ) ? array() : (array) $item->class;
        //if ( 'post' == $item->type && $this->classmeta && ( $metaclasses = get_post_meta( $item->id, $this->classmeta, TRUE ) ) )
        //    $classes[] = $metaclasses;

        $this->post->link_classes = implode( ' ', $classes );
    }
}
