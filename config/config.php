<?php

$config = [
	'session' => [
		'enabled'   => true,
		'driver'    => 'file',
		'name'      => 'session_id',
		'id_length' => 40,
		'allow_query_string' => false,
		'file' => [
			'save_path' => 'storage:sessions'
		],
		'db' => [
			'table' => 'sessions'
		]
	]
];

?>