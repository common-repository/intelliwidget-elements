<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) 
    exit;

class IntelliWidgetMainLinkQueue extends IntelliWidgetMainBackgroundProcess {

	/**
	 * @var string
	 */
    protected $action       = 'link_queue';
    protected $wait_delay   = 1000000; // microseconds
	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		// Actions to perform
        // failsafe bad inputs
        if ( empty( $item[ 'id' ] ) || !intval( $item[ 'id' ] ) || empty( $item[ 'url' ] ) || empty( $item[ 'method' ] ) ):
            //file_put_contents( IWELEMENTS_DIR . '/linkqueue.log.txt', __METHOD__ . ":BAD INPUTS\n", FILE_APPEND );
            return FALSE;
        endif;
        
        global $wpdb;
        //file_put_contents( IWELEMENTS_DIR . '/linkqueue.log.txt', __METHOD__ . ":ITEM\n" . print_r( $item, TRUE ) . "\n", FILE_APPEND );
        /**
         * we need to match with and without the domain so break the url into components
         */
        $parts = parse_url( $item[ 'url' ] );
        
        // find posts using this link
        if ( 'find' == $item[ 'method' ]  ):
            usleep( $this->wait_delay ); // wait for database to finish updating
            $newurl = get_permalink( $item[ 'id' ] );
            // sanity check new link
            if ( empty( $newurl ) ):
                //file_put_contents( IWELEMENTS_DIR . '/linkqueue.log.txt', __METHOD__ . ":EMPTY LINK \n", FILE_APPEND );
                return FALSE;
            endif;
            // check if link changed
            if ( $newurl == $item[ 'url' ] ):
                //file_put_contents( IWELEMENTS_DIR . '/linkqueue.log.txt', __METHOD__ . ":NO CHANGE \n", FILE_APPEND );
                return FALSE;
            endif;
            $like       = '%' . $wpdb->esc_like( $parts[ 'path' ] ) . '%';
            $sql        = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_content LIKE %s", $like );

            $res = $wpdb->get_col( $sql );
            if ( $success = count( $res ) ):
                $sl = new IntelliWidgetMainLinkQueue();
                // add task to replace link for each matching post 
                foreach ( $res as $id ):
                    $item = array(
                        'id'        => $id,
                        'url'       => $item[ 'url' ],
                        'newurl'    => $newurl,
                        'method'    => 'replace',
                    );
                    $sl->push_to_queue( $item ); 
                endforeach;
                $sl->save()->dispatch();
            endif;
        // swap old link with new link 
        elseif ( 'replace' == $item[ 'method' ] ):
            // sanity check new link
            if ( empty( $item[ 'newurl' ] ) ):
                //file_put_contents( IWELEMENTS_DIR . '/linkqueue.log.txt', __METHOD__ . ":EMPTY LINK \n", FILE_APPEND );
                return FALSE;
            endif;
            $sql        = $wpdb->prepare( "SELECT post_content FROM $wpdb->posts WHERE ID = %s", $item[ 'id' ] );
            $res        = $wpdb->get_col( $sql );
            // sanity check result
            if ( empty( $res ) ):
                //file_put_contents( IWELEMENTS_DIR . '/linkqueue.log.txt', __METHOD__ . ":EMPTY RESULT \n", FILE_APPEND );
                return FALSE;
            endif;

            // generate regular expression for swapping link
            $docroot    = $parts[ 'scheme' ] . '://' . $parts[ 'host' ];
            $regex      = '{"(' . preg_quote( $docroot ) . ')?' . preg_quote( $parts[ 'path' ] ) . '}';
            $replace    = '"' . $item[ 'newurl' ];
            $newcontent = preg_replace( $regex, $replace, current( $res ) );
            // sanity check result
            if ( empty( $newcontent ) ):
                //file_put_contents( IWELEMENTS_DIR . '/linkqueue.log.txt', __METHOD__ . ":EMPTY RESULT \n", FILE_APPEND );
                return FALSE;
            endif;
            // update post 
        
            $success = $wpdb->update(
                $wpdb->posts,
                array(
                    'post_content' => $newcontent,
                ),
                array(
                    'ID' => $item[ 'id' ],
                )
            );
        endif;
        //file_put_contents( IWELEMENTS_DIR . '/linkqueue.log.txt', __METHOD__ . ":TASK COMPLETE " . $success . " \n", FILE_APPEND );
        return FALSE;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
    
	protected function complete() {
		parent::complete();
		// Show notice to user or perform some other arbitrary task...
	}

}