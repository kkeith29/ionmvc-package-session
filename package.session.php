<?php

namespace ionmvc\packages;

class session extends \ionmvc\classes\package {

	const version = '1.0.0';
	const class_type_driver = 'ionmvc.session_driver';

	public function setup() {
		$this->add_type('driver',[
			'type' => self::class_type_driver,
			'type_config' => [
				'file_prefix' => 'driver'
			],
			'path' => 'drivers'
		]);
		//create sessions directory
	}

	public static function package_info() {
		return [
			'author'      => 'Kyle Keith',
			'version'     => self::version,
			'description' => 'Session handler',
			'require' => [
				'cookie' => ['1.0.0','>=']
			]
		];
	}

}

?>