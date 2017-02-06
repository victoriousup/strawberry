<?php

namespace App\Http\Controllers\Admin\Apps\JigsawDaily;

use App\Models\Jigdaily\Sticker;
use App\Utils\Apps\JigsawDaily\Thumbnails;
use App\Utils\CloudStorageHelper;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Image;

class StickersController extends Controller
{

	/**
	 * List all the stickers.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$stickers = Sticker::orderBy('name', 'asc')->get();

		return view('admin.apps.jigsawdaily.stickers.index', compact('stickers'));
	}


	/**
	 * Create a new sticker.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create()
	{
		$title = 'New Sticker';
		$sticker = new Sticker(['xScale' => 0, 'yScale' => 0]);

		return view('admin.apps.jigsawdaily.stickers.create', compact('title', 'sticker'));
	}


	/**
	 * Adds the new sticker to the database.
	 */
	public function store(Request $request)
	{
		$rules = ['name' => 'required|max:255',
				  'file' => 'required'];

		$this->validate($request, $rules);

		$sticker = Sticker::create($request->except('url'));

		return redirect('admin/jigsaw-daily/stickers/');
	}


	/**
	 * Edit an existing sticker
	 *
	 * @param $sticker
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit(Sticker $sticker)
	{
		$title = 'Edit Sticker';

		return view('admin.apps.jigsawdaily.stickers.create', compact('title', 'sticker'));
	}


	/**
	 * Updates an existing sticker in the database.
	 *
	 * @param $sticker
	 * @return mixed
	 */
	public function update(Request $request, Sticker $sticker)
	{
		$sticker->update($request->except(['url']));
		return redirect('admin/jigsaw-daily/stickers/');
	}


	/**
	 * Uploads a sticker image.
	 *
	 * @param Request $request
	 * @return array
	 */
	public function uploadImage(Request $request)
	{
		// Width of the template file for the sticker
		$templateWidth = 500;

		$image = $request->file('file');

		// Did file load correctly?
		if(!$request->hasFile('file') || !$image->isValid())
		{
			return response(['error' => 'File could not be uploaded'], 400);
		}

		// Is the file a valid image?
		if(array_search($image->getMimeType(), ["image/jpeg", "image/png"]) === false)
		{
			return response(['error' => 'Please upload a valid JPEG or PNG image file'], 400);
		}

		// Create a unique filename
		$imageFileName = time() . '.' . $image->getClientOriginalExtension();

		// Get the image size
		list($width, $height) = getimagesize($image);
		$widthScale = $width / $templateWidth;
		$heightScale = $height / $templateWidth;

		// Create the thumbnails
		foreach(Thumbnails::getWidths() as $fullWidth)
		{
			// Load the original image
			$img = Image::make($image);

			// Determine what the final width and height should be
			$finalWidth = $fullWidth * $widthScale;

			// Resize the image
			$img->resize($finalWidth, null, function($constraint)
			{
				$constraint->aspectRatio();
			});

			// Get the image data for upload
			if($image->getClientOriginalExtension() == 'png')
			{
				$imgData = (string) $img->encode('png');
			}
			else
			{
				$imgData = (string) $img->encode('jpg');
			}

			// Create the file path
			$filePath = Thumbnails::getStickerPath() . $fullWidth . '/' . $imageFileName;

			// Move the file to external storage
			if(CloudStorageHelper::getDisk()->put($filePath, $imgData, 'public') !== true)
			{
				return response(['error' => 'File could not be uploaded'], 400);
			}
		}

		return ['filename' => $imageFileName,
				'url' => CloudStorageHelper::getUrl($filePath),
				'widthScale' => $widthScale,
				'heightScale' => $heightScale];
	}
}
