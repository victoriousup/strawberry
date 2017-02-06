<?php
namespace App\Utils\Colourbox;

class ColourboxAPI
{
	const API_ENDPOINT       = "https://api.colourbox.com/";
	const API_AUTHENTICATE   = "authenticate/userpasshmac";
	const API_PURCHASE       = "media/purchase";
	const API_DOWNLOAD       = "media/:id/download_location";
	const API_DATA           = "media/:id/";

	private $_apiKey, $_apiSecret, $_colourboxCompanyId;
	private $_token = null;
	private $_tokenExpiration = -1;
	private $_pageContent = null;


	function __construct()
	{
		$this->_apiKey = config('colourbox.key');
		$this->_apiSecret = config('colourbox.secret');
		$this->_colourboxCompanyId = intval(config('colourbox.id'));
	}


	/**
	 * Logs the user into Colourbox
	 * @return True if login is successful
	 */
	public function login()
	{
		$timestamp = time();
		$hmac = hash_hmac('sha1', $this->_apiKey . ':' . $timestamp, $this->_apiSecret);

		$params = array();
		$params['username'] = config('colourbox.username');
		$params['password'] = config('colourbox.password');
		$params['key'] = $this->_apiKey;
		$params['ts'] = $timestamp;
		$params['hmac'] = $hmac;

		$result = $this->apiRequest(self::API_AUTHENTICATE, 'post', $params);

		if($result->statusCode == 200 && $result->responseJson->token)
		{
			$this->_token = $result->responseJson->token;
			$this->_tokenExpiration = $result->responseJson->validUntil;
			return true;
		}

		throw new \Error('Invalid login: ' . $result->response);
	}


	/**
	 * Logs a user in with an existing access token.
	 * @param $token
	 */
	public function loginWithToken($token)
	{
		$this->_token = $token;
	}


	/**
	 * Returns the current access token if the user is logged in
	 * @return null
	 */
	public function getToken()
	{
		return $this->_token;
	}


	public function search(ColourboxSearch $search)
	{
		//$url = 'search/colourbox/media?q=kitten&media_count=10&return_values=thumbnail_url+unique_media_id+title';
		$url = 'search/colourbox/media?' . $search->getQuery();
		$result = $this->apiRequest($url);

		return $result->response;
	}


	/**
	 * Purchase a single stock photo or multiple photos
	 * @param $mediaIds Single mediaId or array of multiple mediaIds
	 * @return bool True if purchase was successful
	 */
	public function purchase($mediaIds)
	{
		if(!is_array($mediaIds))
		{
			$mediaIds = array($mediaIds);
		}

		$params = array();
		$params['unique_media_ids'] = $mediaIds;
		$params['colourbox_id'] = $this->_colourboxCompanyId;

		$result = $this->apiRequest(self::API_PURCHASE, 'post', $params);

		if($result->statusCode != 200)
		{
			error_log($result->response);
		}

		return $result->statusCode == 200;
	}


	/**
	 * Downloads a previously purchased photo
	 * @param $mediaId
	 * @param $filePath
	 * @return bool
	 */
	public function download($mediaId, $filePath)
	{
		$url = str_replace(':id', $mediaId, self::API_DOWNLOAD);
		$result = $this->apiRequest($url, 'get');

		if($result->statusCode == 200 && $result->responseJson->url)
		{
			$downloadUrl = $result->responseJson->url;

			if(file_put_contents($filePath, file_get_contents($downloadUrl)) === false)
			{

			}
			else
			{
				return true;
			}
		}

		throw new \Exception('Unable to download file: ' . $result->response);
	}


	public function getMediaId($colourboxId)
	{
		$content = $this->getPageContent($colourboxId);

		if($content != null)
		{
			$match = preg_match("/<meta property=\"cbx:unique_media_id\" content=\"(.*)\" \/>/", $content, $output_array);
			if(sizeof($output_array) > 0)
			{
				return intval($output_array[1]);
			}
		}

		return -1;
	}


	public function getContributorId($colourboxId)
	{
		$content = $this->getPageContent($colourboxId);

		if($content != null)
		{
			$match = preg_match("/\"supplier_id\":([0-9]+),/", $content, $output_array);
			if(sizeof($output_array) > 0)
			{
				return intval($output_array[1]);
			}
		}

		return -1;
	}


	public function getContributorName($colourboxId)
	{
		$content = $this->getPageContent($colourboxId);

		if($content != null)
		{
			//preg_match("/<a class=\"text-xs-n4\" itemprop=\"author\" href=\"(.*)\">(.*)<\\/a>/", $content, $output_array);
			preg_match("/<a class=\"text-xs-n4\" itemprop=\"author\" href=\"(.*)\">(.*)<\\/a>/", $content, $output_array);

			if(sizeof($output_array) > 0)
			{
				return $output_array[2];
			}
		}

		return '';
	}


	public function getMediaData($mediaId)
	{
		$url = 'search/colourbox/media?media_count=1&unique_media_id=' . $mediaId . '&return_values=title+keywords+uploaded_by+media_type+supplier_id';
		$result = $this->apiRequest($url, 'get');

		if($result->statusCode == 200 && count($result->responseJson->response->media) > 0)
		{
			return $result->responseJson->response->media[0];
		}

		return null;
	}


	public function getPurchasedMediaData($mediaId)
	{
		$url = str_replace(':id', $mediaId, self::API_DATA);
		$result = $this->apiRequest($url, 'get');

		if($result->statusCode == 200)
		{
			return $result->responseJson;
		}

		error_log($result->response);

		return null;
	}


	/**
	 * Returns true if the user is currently logged in.
	 * @return bool
	 */
	public function isLoggedIn()
	{
		return !is_null($this->_token);
	}


	/**
	 * Submits an API request to Colourbox.
	 * @param $method
	 * @param string $type
	 * @param array $requestParams
	 * @return stdClass
	 */
	private function apiRequest($method, $type = 'get', $requestParams = array())
	{
		$ch = curl_init(self::API_ENDPOINT . $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if($type == 'post')
		{
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestParams));
		}

		if($this->isLoggedIn())
		{
			$headers = array();
			$headers[] = 'Authorization: CBX-SIMPLE-TOKEN Token=' . $this->_token;
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		$result = new \stdClass();
		$result->response = curl_exec($ch);
		$result->responseJson = json_decode($result->response);
		$result->statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$result->error = curl_error($ch);
		curl_close($ch);

		return $result;
	}


	private function getPageContent($colourboxId)
	{
		if($this->_pageContent == null)
		{
			$ch = curl_init('https://www.colourbox.com/image/' . $colourboxId);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

			$response = curl_exec($ch);
			$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$error = curl_error($ch);

			curl_close($ch);

			if($statusCode == 200 && $error == 0)
			{
				$this->_pageContent = $response;
			}
		}

		return $this->_pageContent;
	}
}

?>