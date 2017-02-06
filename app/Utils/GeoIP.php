<?php

namespace App\Utils;

use GeoIp2\WebService\Client;

class GeoIP
{
	private $_client;

	function __construct()
	{
		$this->_client = new Client(106342, 'y9w0qdDwkeTZ');
	}

	public function locate($ip)
	{
		return $this->_client->city($ip);
	}

	public function isLocalIP($ip)
	{
		return substr($ip, 0, 4) == '192.';
	}
}