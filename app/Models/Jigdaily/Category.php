<?php

namespace App\Models\Jigdaily;

use DB;

class Category extends \App\Models\StockPhotos\Category
{
	/**
	 * Returns a SQL query builder to get the stock photos associated with the category or
	 * subcategories.
	 */
	public function stockPhotos()
	{
		$colName = $this->isSubcategory() ? 'subcategory_id' : 'category_id';
		$photos = DB::table('stock_photos_categories')->where($colName, $this->attributes['id'])->lists('stock_photo_id');
		return Photo::whereIn('id', $photos);
	}

}