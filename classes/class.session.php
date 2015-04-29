<?php

namespace ionmvc\packages\session\classes;

use ionmvc\classes\autoloader;
use ionmvc\classes\config;
use ionmvc\classes\request;
use ionmvc\exceptions\app as app_exception;
use ionmvc\packages\session as session_pkg;

class session {

	private $driver = null;

	public function __construct() {
		if ( config::get('session.enabled') !== true ) {
			throw new app_exception('Sessions have been disabled');
		}
		$driver = config::get('session.driver');
		$this->driver = autoloader::class_by_type( $driver,session_pkg::class_type_driver,[
			'instance' => true
		] );
		if ( $this->driver === false ) {
			throw new app_exception( 'Unable to load session driver: %s',$driver );
		}
	}

	public function driver() {
		return $this->driver;
	}

	public static function __callStatic( $method,$args ) {
		$class = request::session()->driver();
		if ( !method_exists( $class,$method ) ) {
			throw new app_exception( "Method '%s' not found",$method );
		}
		return call_user_func_array( [ $class,$method ],$args );
	}

}

?>