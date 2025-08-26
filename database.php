<?php

// This is the database connection configuration.
return array(
	//'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
	// uncomment the following lines to use a MySQL database

	'connectionString' => 'mysql:host=testing_db;dbname=gis_dsdw',
	'emulatePrepare' => true,
	'username' => 'dsdw',
	'password' => 'dsdw_gis',
	'charset' => 'utf8',

);
