<?php

namespace App\Utils;

use App;
use Storage;


class CloudStorageHelper
{
	/**
	 * Returns a reference to the cloud disk, or a local disk if
	 * not using cloud storage.
	 *
	 * @return mixed
	 */
	public static function getDisk()
	{
		if(self::useCloudStorage())
		{
			return Storage::disk('s3');
		}
		else
		{
			return Storage::disk('local');
		}
	}


	public static function getSecureUrl($file, $expireMinutes)
	{
		if(!self::useCloudStorage())
		{
			return self::getUrl($file);
		}

		$client = self::getDisk()->getDriver()->getAdapter()->getClient();

		$command = $client->getCommand('GetObject', [
				'Bucket'                     => config('filesystems.disks.s3.bucket'),
				'Key'                        => $file
		]);

		$request = $client->createPresignedRequest($command, '+' . $expireMinutes . ' minutes');
		return (string) $request->getUri();
	}


	/**
	 * Returns an absolute url to a file hosted in cloud storage.
	 *
	 * @param $file
	 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
	 */
	public static function getUrl($file)
	{
		return self::getBaseUrl() . '/' . $file;
	}



	public static function getBaseUrl()
	{
		if(self::useCloudStorage())
		{
			return 'https://s3.amazonaws.com/' . config('filesystems.disks.s3.bucket');
		}
		else
		{
			return url();
		}
	}


	/**
	 * Returns true if cloud storage should be used. Local storage can
	 * be used during development.
	 *
	 * @return bool
	 */
	public static function useCloudStorage()
	{
		return config('filesystems.disks.s3.bucket') != null;
	}
}