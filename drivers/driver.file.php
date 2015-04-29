<?php

namespace ionmvc\packages\session\drivers;

use ionmvc\classes\config;
use ionmvc\classes\func;
use ionmvc\classes\igsr;
use ionmvc\classes\input;
use ionmvc\classes\path;
use ionmvc\exceptions\app as app_exception;
use ionmvc\packages\cookie\classes\cookie;

class file extends base {

	protected $save_path;
	protected $session_id;

	public function __construct() {
		parent::__construct();
		if ( !isset( $this->config['file']['save_path'] ) ) {
			throw new app_exception('Session save path is not set in config');
		}
		$this->save_path = rtrim( path::get( $this->config['file']['save_path'] ),'/' ) . '/';
		if ( !path::test( path::writable,$this->save_path,false ) ) {
			throw new app_exception('Session save path is not writable');
		}
		$session_id = cookie::get( $this->config['name'] );
		if ( $session_id === false && $this->config['allow_query_string'] ) {
			$session_id = input::get( $this->config['name'],false );
		}
		$data = [];
		if ( $session_id === false || strlen( $session_id ) !== $this->config['id_length'] || preg_match( '/^[a-zA-Z0-9]+$/',$session_id ) !== 1 ) {
			$session_id = func::rand_string( $this->config['id_length'],'alpha,numeric' );
		}
		else {
			$session_file = $this->save_path . $session_id;
			if ( file_exists( $session_file ) && ( $contents = file_get_contents( $session_file ) ) !== false && ( $contents = base64_decode( $contents,true ) ) !== false ) {
				$contents = @unserialize( $contents );
				if ( is_array( $contents ) ) {
					$data = $contents;
				}
				unset( $contents );
			}
		}
		$this->session_id = $session_id;
		$this->data = new igsr;
		$this->data->set_data( $data );
	}

	public function destruct() {
		if ( !cookie::is_set( $this->config['name'] ) ) {
			cookie::set( $this->config['name'],$this->session_id,cookie::session_only );
		}
		$data = base64_encode( serialize( $this->data->get_data() ) );
		if ( file_put_contents( $this->save_path . $this->session_id,$data ) === false ) {
			throw new app_exception('Unable to write session data to file');
		}
	}

	//garbage collection

}

?>