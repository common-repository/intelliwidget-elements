<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) 
    exit;
/**
 * Export.php - Module Export class
 *
 * @package IntelliWidgetMainExport
 * @subpackage includes
 * @author Jason C Fleming
 * @copyright 2014-2015 Lilaea Media LLC
 * @access public
 *
 * The Export class controls processing and download of external module options data archives
 *
 */
class IntelliWidgetMainExport {
    
    static function export_options_file(){
        if ( iwctl()->validate_post( 'iwfexport', '_wpnonce' ) ) :
            $export = apply_filters( 'iwf_export_modules', array() );
            if ( empty ( $export ) )
                return;
            set_time_limit( 0 );
            // this process is intended make it really hard ( not impossible )
            // to manually edit export data and so we can validate on import
            require_once( ABSPATH . 'wp-includes/class-phpass.php' );
            $wp_hasher = new PasswordHash( 8, true );
            $data = serialize( $export );
            $md5 = md5( $data );
            $md5_hash = strrev( $wp_hasher->HashPassword( $md5 ) );
            $data = self::_enc( $data, $md5_hash );
            $hash = rtrim( strtr( base64_encode( $md5_hash ), '+/', '-_' ), '=' );
            $filename = 'iwfexport_' . get_stylesheet() . '_' . gmdate( 'Y-m-d-H-i', current_time( 'timestamp' ) ) . '.zip';
            // use php system upload dir to store temp files so that we can use pclzip
            $tmpdir = ini_get( 'upload_tmp_dir' ) ? ini_get( 'upload_tmp_dir' ) : sys_get_temp_dir();
            $datafile = trailingslashit( $tmpdir ) . $hash;
            $zipfile = trailingslashit( $tmpdir ) . $filename;

            if ( file_put_contents( $datafile, $data ) ):
                mbstring_binary_safe_encoding();
                require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );
                $archive = new PclZip( $zipfile );
                if ( $archive->create( $datafile, PCLZIP_OPT_REMOVE_PATH, dirname( $datafile ) ) ):
                    reset_mbstring_encoding();
                    header( 'Content-Description: File Transfer' );
                    header( 'Content-Type: application/octet-stream' );
                    header( 'Content-Length: ' . filesize( $zipfile ) );
                    header( 'Content-Disposition: attachment; filename=' . $filename );
                    header( 'Expires: 0' );
                    header( 'Cache-Control: must-revalidate' );
                    header( 'Pragma: public' );
                    readfile( $zipfile );
                    // remove the evidence
                    unlink( $zipfile );
                    unlink( $datafile );
                    die();
                endif;
            endif;
        else:
            wp_nonce_ays( '' );
        endif;        
    }

    /**
     * _enc
     * performs bitwise xor operation on string
     * against key and returns base64 encoded string.
     */
    static function _enc( $string, $key ) {
        if ( !strlen( $string ) || !strlen( $key ) ) return;
        while ( strlen( $key ) < strlen( $string ) ) $key .= $key;
        $enc = ''; 
        for( $ptr = 0, $len = strlen( $string ); $ptr < $len; $ptr++ ):
            $c = ord( substr( $string, $ptr, 1 ) );
            $k = ord( substr( $key, $ptr, 1 ) );
            $b = $c ^ $k;
            $enc .= chr( $b ); 
        endfor;
        return base64_encode( $enc ); 
    }
}