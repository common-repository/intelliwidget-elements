<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) 
    exit;
/**
 * Import.php - Module Import class
 *
 * @package IntelliWidgetMainImport
 * @subpackage includes
 * @author Jason C Fleming
 * @copyright 2014-2015 Lilaea Media LLC
 * @access public
 *
 * The Import class controls upload, confirmation and processing of external module options data archives
 *
 */
class IntelliWidgetMainImport {
    
    static function ctl(){
        return iwctl();
    }
    
    static function check_import_file( $path ){
        if ( isset( $_POST[ 'iwf_cancel_import' ] ) ):
            @unlink( $path );
        else:
            try {
                require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );
                $archive = new PclZip( $path );
                if ( ( $opt = $archive->extract( PCLZIP_OPT_EXTRACT_AS_STRING ) ) && isset( $opt[ 0 ] ) && is_array( $opt[ 0 ] ) ):
                    $md5_hash = base64_decode( str_pad( strtr( $opt[ 0 ][ 'filename' ], '-_', '+/' ), 
                        strlen( $opt[ 0 ][ 'filename' ] ) % 4, '=', STR_PAD_RIGHT ) );
                    $stream = self::_dec( $opt[ 0 ][ 'content' ], $md5_hash );
                    require_once( ABSPATH . 'wp-includes/class-phpass.php' );
                    $wp_hasher = new PasswordHash( 8, true );
                    if ( $wp_hasher->CheckPassword( md5( $stream ), strrev( $md5_hash ) ) ):
                        if ( ( $new_options = unserialize( $stream ) )
                            && is_array( $new_options ) ):
                            if ( isset( $_POST[ 'iwf_import' ] ) ):
                                if ( self::ctl()->validate_post( 'iwfimport', '_wpnonce' ) ):
                                    // import data
                                    do_action( 'iwf_import_modules', $new_options );
                                else:
                                    throw new Exception( 'Unauthorized' );
                                endif;
                                @unlink( $path );
                            else:
                                self::ctl()->import_modules = array_keys( $new_options );
                                // display confirm inputs
                            endif;
                            return;
                        else:
                            throw new Exception( 'Not Valid Archive' );
                        endif;
                    endif;
                endif;
            } catch( Exception $e ){
                // PclZip failure
                @unlink( $path );
                self::ctl()->_error( 'fail:' . $e->getMessage() );
            }
        endif;
    }
    
    static function upload_options_file(){
        if ( current_user_can( 'upload_files' ) && current_user_can( 'edit_theme_options' ) ):
            $uploads = wp_upload_dir();
            $path = $uploads[ 'basedir' ] . "/" . IWELEMENTS_IMPORT_FILE;
            if ( isset( $_FILES[ 'iwf_options_file' ][ 'tmp_name' ] ) ):
                if ( !move_uploaded_file( $_FILES['iwf_options_file']['tmp_name'], $path ) ):
                    self::ctl()->_error( 'fail:' . "Could not upload the file. Check your site's directory permissions." );
                else:
                    self::ctl()->redirect();
                endif;
            endif;
        endif;
    }
    
    /**
     * _dec
     * performs bitwise xor operation on base64/xor encoded string
     * against key and returns decoded string.
     */
    static function _dec( $string, $key ) {
        if ( !strlen( $string ) || !strlen( $key ) ) return;
        $dec = '';
        $enc = base64_decode( $string );
        while ( strlen( $key ) < strlen( $enc ) ) $key .= $key;
        for( $ptr = 0, $len = strlen( $enc ); $ptr < $len; $ptr++ ):
            $b = ord( substr( $enc, $ptr, 1 ) );
            $k = ord( substr( $key, $ptr, 1 ) );
            $c = $b ^ $k;
            $dec .= chr( $c );
        endfor;
        return $dec;
    }

}