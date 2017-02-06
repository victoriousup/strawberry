<?php

namespace App\Utils;


class CDNHelper
{
	public static function getSecureUrl($file, $expireMinutes)
	{
		$url = CloudStorageHelper::getSecureUrl($file, $expireMinutes);

		if(self::useCDN())
		{
			return str_replace(CloudStorageHelper::getBaseUrl(), self::getBaseUrl(), $url);
		}

		return $url;
	}

	public static function getUrl($file)
	{
		return self::getBaseUrl() . '/' . $file;
	}


	public static function getBaseUrl()
	{
		if(self::useCDN())
		{
			return config('maxcdn.static.url');
		}
		else
		{
			return CloudStorageHelper::getBaseUrl();
		}
	}

	/**
	 * Returns true if a CDN should be used. Local storage or S3 can be
	 * used during development.
	 *
	 * @return bool
	 */
	public static function useCDN()
	{
		return config('maxcdn.key') != null;
	}

}