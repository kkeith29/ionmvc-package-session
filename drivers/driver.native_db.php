<?php

namespace ionmvc\packages\session\drivers;

use ionmvc\classes\config;
use ionmvc\classes\db;
use ionmvc\classes\igsr;

class native_db extends base {

	protected $table;
	protected $create = false;

	public function __construct() {
		$this->table = db::table( config::get('session.native_db.table') );
		session_set_save_handler(
			array( $this,'open' ),
			array( $this,'close' ),
			array( $this,'read' ),
			array( $this,'write' ),
			array( $this,'destroy' ),
			array( $this,'gc' )
		);
		session_start();
		$this->data = new igsr;
		$this->data->set_data( $_SESSION );
		parent::__construct();
	}

	public function destruct() {
		session_write_close();
	}

	public function open( $save_path,$session_name ) {
		return true;
	}

	public function close() {
		return true;
	}

	public function read( $session_id ) {
		$query = $this->table->query('select')->fields('id','data')->where('id','=',$session_id)->limit(1)->execute();
		if ( $query->num_rows() === 0 ) {
			$this->create = true;
			return '';
		}
		return base64_decode( $query->result()->data );
	}

	public function write( $session_id,$data ) {
		if ( $this->create === true ) {
			$this->table->query('insert')->fields(array(
				'id'   => $session_id,
				'data' => base64_encode( $data ),
				'time' => time()
			))->execute();
			return true;
		}
		$this->table->query('update')->fields(array(
			'data' => base64_encode( $data ),
			'time' => time()
		))->where('id','=',$session_id)->execute();
		return true;
	}

	public function destroy( $session_id ) {
		$this->table->query('delete')->where('id','=',$session_id)->execute();
		return true;
	}

	public function gc( $lifetime ) {
		$this->table->query('delete')->where('time','<',( time() - $lifetime ))->execute();
		return true;
	}

}

?>