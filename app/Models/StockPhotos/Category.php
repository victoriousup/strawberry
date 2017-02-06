<?php

namespace App\Models\StockPhotos;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\StockPhotos\StockPhoto;

class Category extends Model
{
    protected $table = 'stock_photo_categories';

	protected $fillable = ['slug', 'name', 'parent_category_id'];

	public $timestamps = false;

	/**
	 * Returns a SQL query builder to get the subcategories associated with a category.
	 * @return mixed
	 */
	public function subcategories()
	{
		return Category::where('parent_category_id', $this->id);
	}


	/**
	 * Returns the parent category.
	 * @return Category
	 */
	public function parentCategory()
	{
		return Category::find($this->attributes['parent_category_id']);
	}


	/**
	 * Returns a SQL query builder to get the stock photos associated with the category or
	 * subcategories.
	 */
	public function stockPhotos()
	{
		$colName = $this->isSubcategory() ? 'subcategory_id' : 'category_id';
		$photos = DB::table('stock_photos_categories')->where($colName, $this->attributes['id'])->lists('stock_photo_id');
		return StockPhoto::whereIn('id', $photos);
	}


	/**
	 * Returns true if this is a subcategory
	 * @return bool
	 */
	public function isSubcategory()
	{
		return !is_null($this->attributes['parent_category_id']);
	}
}
