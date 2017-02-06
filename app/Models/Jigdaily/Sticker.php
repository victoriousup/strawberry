<?php

namespace App\Models\Jigdaily;

use Illuminate\Database\Eloquent\Model;
use App\Utils\CDNHelper;
use App\Utils\Apps\JigsawDaily\Thumbnails;

class Sticker extends Model
{
	protected $table = 'jigdaily_stickers';
	public $timestamps = false;
	protected $guarded = ['id'];

	public function packs()
	{
		return $this->belongsToMany('App\Models\Jigdaily\Pack', 'jigdaily_pack_sticker', 'pack_id', 'sticker_id');
	}

	/**
	 * Returns the CDN url where the original sticker image can be viewed
	 */
	public function getCdnUrl($width = 500)
	{
		return CDNHelper::getUrl(Thumbnails::getStickerPath() . $width . '/' . $this->file);
	}
}
