<?php

namespace App\Http\Controllers\Admin\Apps\JigsawDaily;

use App\Utils\Apps\JigsawDaily\Thumbnails;
use App\Utils\CloudStorageHelper;
use App\Utils\Prices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Jigdaily\Pack;
use App\Models\Jigdaily\PackPromo;
use Image;

class PackPromosController extends Controller
{
	public function index(Pack $pack)
	{
		$promos = PackPromo::where('pack_id', $pack->id)->orderBy('type', 'asc')->get();
		return view('admin.apps.jigsawdaily.packs.promos.index', compact('pack', 'promos'));
	}


	public function create(Pack $pack)
	{
		$promo = new PackPromo();
		$promo->name = $pack->name . ' Promo';
		$title = 'New Promo';
		$prices = json_encode(Prices::getPrices());
		$currencies = $this->getCurrencies();

		return view('admin.apps.jigsawdaily.packs.promos.create', compact('pack', 'promo', 'title', 'prices', 'currencies'));
	}


	public function store(Pack $pack, Request $request)
	{
		$rules = ['name' => 'required|max:255',
		          'file' => 'required'];

		$this->validate($request, $rules);

		$request->request->add(['pack_id' => $pack->id]);

		if($request->get('price') == 'custom')
		{
			$request->request->set('price', $request->get('price_custom'));
		}

		if($request->get('price') == '')
		{
			$request->request->set('price', null);
		}

		if($request->get('currency') == '')
		{
			$request->request->set('currency', null);
		}

		$promo = PackPromo::create($request->except('url', 'price_custom'));

		return redirect('admin/jigsaw-daily/packs/' . $pack->id . '/promos/');
	}


	public function edit(Pack $pack, PackPromo $promo)
	{
		$title = 'Edit Promo';
		$prices = json_encode(Prices::getPrices());
		$currencies = $this->getCurrencies();

		return view('admin.apps.jigsawdaily.packs.promos.create', compact('pack', 'promo', 'title', 'prices', 'currencies'));
	}


	public function update(Pack $pack, PackPromo $promo, Request $request)
	{
		if($request->get('price') == 'custom')
		{
			$request->request->set('price', $request->get('price_custom'));
		}

		if($request->get('price') == '')
		{
			$request->request->set('price', null);
		}

		if($request->get('currency') == '')
		{
			$request->request->set('currency', null);
		}

		$promo->update($request->except(['url', 'price_custom']));
		return redirect('admin/jigsaw-daily/packs/' . $pack->id . '/promos/');
	}


	public function upload(Request $request)
	{
		$image = $request->file('file');

		// Did file load correctly?
		if(!$request->hasFile('file') || !$image->isValid())
		{
			return response(['error' => 'File could not be uploaded'], 400);
		}

		// Is the file a valid image?
		if($image->getMimeType() != "image/jpeg" || $image->getClientOriginalExtension() != 'jpg')
		{
			return response(['error' => 'Please upload a valid JPEG image file'], 400);
		}

		// Correct image size?
		list($width, $height) = getimagesize($image);
		if($width != 1362 || $height != 828)
		{
			return response(['error' => 'Image must be 1362px x 828px'], 400);
		}

		// Create a unique filename
		$imageFileName = time() . '.' . $image->getClientOriginalExtension();

		// Create the thumbnails
		foreach(PackPromo::getWidths() as $fullWidth)
		{
			// Load the original image
			$img = Image::make($image);

			// Resize the image
			$img->resize($fullWidth, null, function($constraint)
			{
				$constraint->aspectRatio();
			});

			// Create the image data
			$imgData = (string) $img->encode('jpg');

			// Create the file path
			$filePath = Thumbnails::getPackPromoPath() . $fullWidth . '/' . $imageFileName;

			// Move the file to external storage
			if(CloudStorageHelper::getDisk()->put($filePath, $imgData, 'public') !== true)
			{
				return response(['error' => 'File could not be uploaded'], 400);
			}
		}

		return ['filename' => $imageFileName,
		        'url' => CloudStorageHelper::getUrl($filePath),
				'orgName' => $image->getClientOriginalName()];
	}


	public function getCurrencies()
	{
		$result = Prices::getCurrencies();
		$result = array_reverse($result, true);
		$result[''] = 'Any';

		return array_reverse($result, true);
	}


	public function bulk(Pack $pack)
	{
		return view('admin.apps.jigsawdaily.packs.promos.bulk', compact('pack'));
	}


	public function bulkCreate(Pack $pack, Request $request)
	{
		$promos = \json_decode($request->get('promos'));
		if($promos == null)
		{
			// TODO: Nicer error
			return 'Error!';
		}

		foreach($promos as $promoData)
		{
			// Which currency is this?
			$re = "/promo-(.+).jpg/";
			preg_match($re, $promoData->orgName, $matches);

			if($matches === false)
			{
				// TODO: Nicer error
				return 'Error!';
			}

			$currency = $matches[1];
			if($currency == 'OTHER')
			{
				$currency = '';
			}

			$promo = new PackPromo();
			$promo->pack_id = $pack->id;
			$promo->type = $request->get('type');
			$promo->file = $promoData->filename;
			$promo->status = 1;

			if($currency != '')
			{
				$promo->name = $request->get('name') . ' (' . $currency . ')';
				$promo->currency = $currency;
				$promo->price = Prices::getPricesByTier($request->get('tier'))[$currency];
			}
			else
			{
				$promo->name = $request->get('name');
				$promo->currency = null;
				$promo->price = null;
			}

			$promo->save();
		}

		return redirect('admin/jigsaw-daily/packs/' . $pack->id . '/promos/');
	}


}