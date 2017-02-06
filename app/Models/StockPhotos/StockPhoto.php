<?php

namespace App\Models\StockPhotos;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Utils\Stem;
use Exception;
use App\Models\Jigdaily\Photo;

class StockPhoto extends Model
{
	protected $table = 'stock_photos';

	public $timestamps = false;

	protected $guarded = ['id'];


	public function source()
	{
		return $this->belongsTo('App\Models\StockPhotos\Source', 'stock_photo_source_id');
	}



	public function getSquareCroppingAttribute()
	{
		return json_decode($this->attributes['square_cropping']);
	}

	public function setSquareCroppingAttribute($value)
	{
		$this->attributes['square_cropping'] = json_encode($value);
	}



	public function setKeywordsAttribute(Array $keywords)
	{
		$this->attributes['keywords'] = implode(', ', $keywords);
	}

	public function getKeywordsAttribute()
	{
		return explode(', ', $this->attributes['keywords']);
	}


	/**
	 * Adds the stock photo to an existing subcategory. If this is the first subcategory to be
	 * added, it will become the default subcategory for the image.
	 *
	 * @param Category $subcategory
	 * @throws Exception
	 */
	public function addToSubcategory(Category $subcategory)
	{
		// Valid subcategory?
		if(!$subcategory->isSubcategory())
		{
			throw new Exception('Not a valid subcategory object');
		}

		// Set this subcategory as default
		if(!isset($this->attributes['default_subcategory_id']) || $this->attributes['default_subcategory_id'] == null)
		{
			$this->setDefaultSubcategory($subcategory);
		}

		DB::table('stock_photos_categories')->insert([
			'stock_photo_id' => $this->attributes['id'],
			'category_id' => $subcategory->parent_category_id,
			'subcategory_id' => $subcategory->id,
		]);
	}


	/**
	 * Sets a subcategory as the default.
	 *
	 * @param Category $subcategory
	 * @throws Exception
	 */
	public function setDefaultSubcategory(Category $subcategory)
	{
		// Valid subcategory?
		if(!$subcategory->isSubcategory())
		{
			throw new Exception('Not a valid subcategory object');
		}

		$this->attributes['default_subcategory_id'] = $subcategory->id;
		$this->save();
	}


	/**
	 * Returns a SQL query builder for the subcategories.
	 */
	public function subcategories()
	{
		$ids = DB::table('stock_photos_categories')->where('stock_photo_id', $this->attributes['id'])->lists('subcategory_id');
		return Category::whereIn('id', $ids);
	}


	public function save(array $options = []):bool
	{
		$this->stem();
		$tmpExists = $this->exists;
		$response = parent::save();

		// Update Jigbug photo
		$photo = Photo::firstOrNew(['id' => $this->attributes['id']]);
		$photo->stemmed_keywords = $this->stemmed_keywords;
		$photo->name = $this->attributes['name'];
		$photo->save();

		return $response;
	}


	private function stem()
	{
		$stemStr = implode(' ', [$this->attributes['name'], $this->attributes['keywords']]);
		$this->attributes['stemmed_keywords'] = implode(' ', Stem::getStemmedString($stemStr));
	}
}
