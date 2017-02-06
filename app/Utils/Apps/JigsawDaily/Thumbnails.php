<?php

namespace App\Utils\Apps\JigsawDaily;

use App\Utils\CloudStorageHelper;
use Image;
use Storage;
use Kraken;
use Log;

class Thumbnails
{
	public static function getWidths()
	{
		return [500, 400, 300, 200];
	}

	public static function getStickerPath()
	{
		return 'public/apps/jigsaw-daily/stickers/';
	}

	public static function getPackPromoPath()
	{
		return 'public/apps/jigsaw-daily/pack-promos/';
	}

	public static function getPhotoPath()
	{
		return 'public/apps/jigsaw-daily/photos/';
	}

	public static function getFullPhotoPath()
	{
		return 'secure/apps/jigsaw-daily/photos/full/';
	}

	public static function getFullWidth()
	{
		return 700;
	}

	public static function getHash($id)
	{
		return substr(md5($id . 'b2AQzEUj'), 0, 10);
	}

	public static function generate($id, $content, $crop)
	{
		$s3 = Storage::disk('s3');
		$localDisk = Storage::disk('local');
		$kraken = new Kraken(config('kraken.key'), config('kraken.secret'));

		// -----------------------
		// Crop the image
		// -----------------------
		$img = Image::make($content);
		$img->crop($crop->width, $crop->height, $crop->x, $crop->y);
		$img->resize(Thumbnails::getFullWidth(), null, function($constraint)
		{
			$constraint->aspectRatio();
		});
		$imgData = (string) $img->encode('jpg', 90);


		// -----------------------
		// Save the image to S3
		// -----------------------
		$filename = self::getFullPhotoPath() . $id . '.jpg';
		if($s3->put($filename, $imgData, 'private') !== true)
		{
			return false;
		}


		// -----------------------
		// Thumbnails
		// -----------------------
		$params = [
			'url' => CloudStorageHelper::getSecureUrl($filename, 5),
			'wait' => true,
			'lossy' => true,
			's3_store' => [
				'key' => config('filesystems.disks.s3.key'),
				'secret' => config('filesystems.disks.s3.secret'),
				'bucket' => config('filesystems.disks.s3.bucket'),
				'region' => config('filesystems.disks.s3.region'),
				'acl' => 'public_read'
			],
			'resize' => []
		];

		foreach(Thumbnails::getWidths() as $width)
		{
			$imageName = $id . '-' . Thumbnails::getHash($id) . '.jpg';
			$imagePath = Thumbnails::getPhotoPath() . $width . '/' . $imageName;

			$resize = [
				'id' => $width,
				'strategy' => 'landscape',
				'width' => $width,
				'storage_path' => $imagePath
			];

			$params['resize'][] = $resize;
		}

		$data = $kraken->url($params);
		if(!$data["success"])
		{
			return false;
		}

		return true;
	}
}