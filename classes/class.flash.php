<?php

namespace ionmvc\packages\session\classes;

use ionmvc\classes\app;
use ionmvc\classes\request;
use ionmvc\exceptions\app as app_exception;

class flash {

	const error   = 1;
	const warning = 2;
	const success = 3;
	const info    = 4;

	private $config = [
		'css' => false
	];

	public static function __callStatic( $method,$args ) {
		$class = request::flash();
		$method = "_{$method}";
		if ( !method_exists( $class,$method ) ) {
			throw new app_exception( "Method '%s' not found",$method );
		}
		return call_user_func_array( [ $class,$method ],$args );
	}

	public function __construct() {
		request::hook()->attach('destruct',function( $session ) {
			$session->remove('flash.message.current');
		},8);
		$session = request::session()->driver();
		if ( $session->is_set('flash.message.new') ) {
			$session->set('flash.message.current',$session->get('flash.message.new'));
			$session->remove('flash.message.new');
		}
		if ( $session->is_set('flash.data.new') ) {
			$session->set('flash.data.current',$session->get('flash.data.new'));
			$session->remove('flash.data.new');
		}
	}

	public function _config( $data ) {
		$this->config = array_merge( $this->config,$data );
	}

	public function _message( $data,$type=self::info,$config=array() ) {
		$array = ( isset( $config['now'] ) && $config['now'] ? 'current' : 'new' );
		return session::set("flash.message.{$array}[]",compact('data','type','config'));
	}

	public function _get_messages() {
		$retval = [];
		if ( session::is_set('flash.message.current') ) {
			$retval = session::get('flash.message.current');
		}
		if ( count( $retval ) > 0 && $this->config['css'] !== false ) {
			if ( !package::loaded('asset') ) {
				throw new app_exception('Asset package does not exist');
			}
			\ionmvc\packages\asset\classes\asset::add( $this->config['css'] );
		}
		return $retval;
	}

	public function _data( $key,$value=null ) {
		if ( is_null( $value ) ) {
			return session::get("flash.data.current.{$key}");
		}
		return session::set("flash.data.new.{$key}",$value);
	}

	public function _get_data() {
		$retval = [];
		if ( session::is_set('flash.data.current') ) {
			$retval = session::get('flash.data.current');
		}
		return $retval;
	}

}

?>