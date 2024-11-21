<?php // Constants: Folder directories/uri's
	// site configuration plugin
	define('SITE_CONFIGURATION', 'AES');

    // AWS & DREAM HOST
	define('AES_FEE_INSCRIPTION', '63'); // Product registration fee
	define('AES_LATAM_SCHOLARSHIP', '62');
	define('AES_DUAL_9NO', '51'); // Product Initial 9 Grade (lower)
	define('AES_DUAL_9NO_VARIABLE', '54'); // Product variable (lower)
	define('AES_DUAL_10MO', '52');// Product Initial 10mo Grade (middle)
	define('AES_DUAL_10MO_VARIABLE', '57'); // Product variable  (middle)
	define('AES_DUAL_DEFAULT', '53');    // Product initial 11 and bacheloder (upper)
	define('AES_DUAL_DEFAULT_VARIABLE','60'); // Product variable (upper)
	define('ROLES_OF_STAFF', array('owner', 'administrador', 'admision', 'admission', 'alliance', 'administration', 'allied', 'webinar-aliance'));

	// Moodle
	define('LOWER_COURSES_MOODLE', array(2, 3));
	define('MIDDLE_COURSES_MOODLE', array(2, 3));
	define('UPPER_COURSES_MOODLE', array(2, 3));

	// FOR ALL USERS, DONT COMMENT
	define('AES_PROGRAM_ID', 'HSD-01'); // AES program for endpoint for laravel create user and move documents
	define('AES_PERIOD', '20242025'); // AES period for endpoint for laravel create user and move documents
	define('AES_TYPE_PROGRAM', '0'); // AES type of program for endpoint for laravel create user and move documents
	define('URL_LARAVEL_PPADMIN', 'https://admin.american-elite.us/'); 
	define('ROLE_ID_STUDENT_MOODLE', 5); 