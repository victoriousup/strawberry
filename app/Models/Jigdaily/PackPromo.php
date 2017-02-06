<?php

namespace App\Models\Jigdaily;

use App\Utils\Apps\JigsawDaily\Thumbnails;
use App\Utils\CDNHelper;
use Illuminate\Database\Eloquent\Model;

class PackPromo extends Model
{
	public $active = true;

	protected $table = 'jigdaily_pack_promos';
	public $timestamps = false;
	protected $guarded = ['id'];


	public function getCdnUrl($width)
	{
		return CDNHelper::getUrl(Thumbnails::getPackPromoPath() . $width . '/' . $this->file);
	}


	public static function getWidths()
	{
		return [1362, 904, 681, 452];
	}

}
