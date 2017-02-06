<?php

namespace App\Http\Controllers\Admin\Apps\JigsawDaily;

use App\Http\Controllers\Controller;
use App\Models\Jigdaily\Pack;
use App\Models\Jigdaily\Photo;
use App\Models\Jigdaily\Category;
use App\Utils\Porter;
use Illuminate\Http\Request;

class OrganizeController extends Controller
{

	/**
	 * Main index page.
	 *
	 * @param Request $request
	 * @param $packId
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index(Request $request, $packId = -1)
	{
		$categories = Category::whereNull('parent_category_id')->orderBy('name', 'asc')->lists('name', 'id')->prepend('All', -1)->toArray();

		$packs = [];
		$packs['-1'] = 'Select a pack';

		$tmpPacks = Pack::where('released', 0)->orWhere('id', $packId)->orderBy('name', 'asc')->get();
		foreach($tmpPacks as $pack)
		{
			$totalPhotos = $pack->photos()->count();
			$packs[$pack->id] = $pack->name . ' (' . $totalPhotos . ')';
		}

		return view('admin.apps.jigsawdaily.organize.index', compact('categories', 'packs', 'packId'));
	}


	public function popup(Request $request, $packId = -1)
	{
		$packs = [];
		$packs['-1'] = 'Select a pack';

		$tmpPacks = Pack::where('released', 0)->orWhere('id', $packId)->orderBy('name', 'asc')->get();
		foreach($tmpPacks as $pack)
		{
			$totalPhotos = $pack->photos()->count();
			$packs[$pack->id] = $pack->name . ' (' . $totalPhotos . ')';
		}

		return view('admin.apps.jigsawdaily.organize.popup', compact('categories', 'packs', 'packId'));
	}


	/**
	 * Returns a list of sub-categories given a specific category.
	 * @param Category $category
	 *
	 * @return mixed
	 */
	public function subcategories(Category $category)
	{
		return $category->subcategories()->orderBy('name', 'asc')->get(['id', 'name']);
	}


	/**
	 * Adds a photo to a pack.
	 *
	 * @param Request $request
	 *
	 * @return array
	 */
	public function add(Request $request)
	{
		$pack = Pack::findOrFail($request->get('packId'));
		$photo = Photo::findOrFail($request->get('photoId'));

		$pack->addPhoto($photo->id);

		return [];
	}


	/**
	 * Removes a photo from a pack.
	 *
	 * @param Request $request
	 *
	 * @return array
	 */
	public function remove(Request $request)
	{
		$pack = Pack::findOrFail($request->get('packId'));
		$pack->removePhoto($request->get('photoId'));

		return [];
	}


	/**
	 * Returns pack data.
	 *
	 * @param Pack $pack
	 *
	 * @return array
	 */
	public function pack(Pack $pack)
	{
		$ret = [];
		$ret['id'] = $pack->id;
		$ret['name'] = $pack->name;
		$ret['photos'] = [];

		$photos = $pack->photos()->orderBy('name', 'asc')->get(['id', 'name']);
		foreach($photos as $photo)
		{
			$photo->cover = $photo->id == $pack->cover_id;
			$photo->img = $photo->getFileUrl(300);
			$ret['photos'][] = $photo;
		}

		return $ret;
	}


	/**
	 * Sets the pack cover.
	 *
	 * @param Request $request
	 *
	 * @return array
	 */
	public function setCover(Request $request)
	{
		$pack = Pack::findOrFail($request->get('packId'));
		$photo = Photo::findOrFail($request->get('photoId'));

		$pack->cover_id = $photo->id;
		$pack->save();

		return [];
	}


	/**
	 * Returns the filtered photos.
	 *
	 * @param Request $request
	 *
	 * @return array
	 */
	public function photos(Request $request)
	{
		$categoryId = $request->get('categoryId', -1);
		$subcategoryId = $request->get('subcategoryId', -1);
		$search = $request->get('search', '');
		$offset = $request->get('offset', 0);
		$limit = $request->get('limit', 100);
		$query = null;

		// Category
		if($categoryId != -1 || $subcategoryId != -1)
		{
			$category = Category::find($subcategoryId != -1 ? $subcategoryId : $categoryId);
			if($category)
			{
				$query = $category->stockPhotos()->where('stemmed_keywords', 'ILIKE', '%' . Porter::Stem($search) . '%');
			}
		}
		// Search everything
		else if($search != '')
		{
			$query = Photo::where('stemmed_keywords', 'ILIKE', '%' . Porter::Stem($search) . '%');
		}

		// Run the query
		if($query != null)
		{
			$jigsaws = $query->whereNull('pack_id')
					->orderBy('name', 'asc')
					->offset($offset)
					->take($limit + 1)
					->get(['id', 'name']);

			// Add additional data
			foreach($jigsaws as $jigsaw)
			{
				$jigsaw->img = $jigsaw->getFileUrl(300);
			}

			return $jigsaws;
		}

		return [];
	}


}