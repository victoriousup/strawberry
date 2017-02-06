<?php

return [

	'client_email' => env('GOOGLE_CLIENT_EMAIL'),

	'private_key' => str_replace('\n', "\n", env('GOOGLE_KEY')),



	'jigsaw_daily' => [

		'id' => env('GOOGLE.JIGSAW_DAILY.ID', 'com.digitalstrawberry.jigsawdaily'),

		'key' => env('GOOGLE.JIGSAW_DAILY.KEY', 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhlgNKnxfVO+yjc9BVVAgSosMFYo/Mt9ICR/1yDfoL2XV4wT8Mg89t2CQSXWiOBkGT25iJPZQ4Sju+rj4FkJ0F2KpgGQaIwDjIJS53miVQvaSSIUCy/4nltNCImVXpVzGP5PkXwUZ5hJnDqc3H63tCF82fU9EkGkvQL9JWXuU24pqYISIoJW8VMLh63uRt+na588f6hfs09UZAfWMyFVxjEFZRAW1/8wcRzWVvE3ulRh/fEtUsE2ngv23BwBMR5qFkqMVOCbKOiTS/Z9OXbfFiwGFrx54nNzWYNFH5mHpMcBY+tJ56KQIFtzJkC6esMDu5v98zkRw57Pv18rYAfvpRwIDAQAB'),
	],


];