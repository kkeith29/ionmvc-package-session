<?php

namespace ionmvc\packages\session\drivers;

use ionmvc\classes\app;
use ionmvc\classes\config;
use ionmvc\classes\request;
use ionmvc\classes\uri;

class base {

	protected $data;
	protected $config = [
		'name'               => 'session_id',
		'id_length'          => 40,
		'allow_query_string' => false
	];

	public function __construct() {
		request::hook()->attach('destruct',function() {
			$this->set('last_page',uri::current());
			$this->set('time',time());
			$this->destruct();
		},5);
		$this->config = array_merge( $this->config,config::get( 'session',[] ) );
	}

	public function destruct() {}

	public function is_set( $key ) {
		return $this->data->is_set( $key );
	}

	public function get( $key ) {
		return $this->data->get( $key );
	}

	public function set( $key,$value ) {
		return $this->data->set( $key,$value );
	}

	public function remove( $key ) {
		return $this->data->remove( $key );
	}

	public function expired( $mins ) {
		$mins = ( 60 * $mins );
		if ( ( $this->data->get('time') + $mins ) < time() ) {
			return true;
		}
		return false;
	}

	public function debug() {
		$this->data->debug();
	}

}

?>