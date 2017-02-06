<?php

namespace App\Http\Controllers\Admin\Apps\JigsawDaily;

use App\Models\Jigdaily\Pack;
use App\Models\Jigdaily\PackPromo;
use App\Models\Jigdaily\Photo;
use App\Models\Jigdaily\Sticker;
use App\Utils\Apps\JigsawDaily\Thumbnails;
use App\Utils\CloudStorageHelper;
use App\Utils\Prices;
use Illuminate\Http\Request;
use Response;
use Image;
use Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PacksController extends Controller
{
	public function index()
	{
		$packs = Pack::orderBy('released', 'asc')->orderBy('name', 'asc')->get();

		return view('admin.apps.jigsawdaily.packs.index', compact('packs'));
	}


	public function create()
	{
		$title = 'Create Pack';
		$pack = new Pack(['visible' => true, 'price_tier' => 1]);
		$tiers = $this->getTiers();
		$stickerData = $this->getStickerData();
		$packData = $this->getRecommendedData();

		return view('admin.apps.jigsawdaily.packs.create',
			compact('title', 'pack', 'tiers', 'stickerData', 'packData'));
	}


	public function store(Request $request)
	{
		$this->validate($request, ['name' => 'required|max:255']);

		$params = $request->except(['_stickers', '_recommended']);

		$pack = Pack::create($params);

		return redirect('admin/jigsaw-daily/packs/');
	}


	public function edit(Pack $pack)
	{
		$title = 'Edit Pack';
		$tiers = $this->getTiers();
		$stickerData = $this->getStickerData();
		$packData = $this->getRecommendedData($pack->id);

		return view('admin.apps.jigsawdaily.packs.create',
			compact('title', 'pack', 'tiers', 'stickerData', 'packData'));
	}


	public function update(Request $request, Pack $pack)
	{
		$params = $request->except(['_stickers', '_recommended']);
		$pack->update($params);

		return redirect('admin/jigsaw-daily/packs/');
	}


	public function preview(Pack $pack)
	{
		$photos = $pack->photos()->orderBy('name', 'asc')->get();

		return view('admin.apps.jigsawdaily.packs.preview', compact('pack', 'photos'));
	}


	public function storePreviewImage(Pack $pack)
	{
		$disk = CloudStorageHelper::getDisk();
		$photos = $pack->photos()->orderBy('name', 'asc')->limit(6)->get();
		$img = Image::make('../public/apps/jigsaw-daily/images/template.png');

		$positions = [[122, 81], [463, 81], [803, 81], [122, 427], [463, 427], [803, 427]];
		$i = 0;

		foreach($photos as $photo)
		{
			$img->insert($disk->get($photo->getLocalFileUrl(300)), 'top-left', $positions[$i][0], $positions[$i][1]);
			$i++;
		}

		return $img->response('jpg');
	}


	public function promoBuilder(Pack $pack)
	{
		$photos = $pack->photos()->orderBy('name', 'asc')->get();

		return view('admin.apps.jigsawdaily.packs.promo-builder', compact('pack', 'photos'));
	}


	public function promoBuilderGenerate(Request $request)
	{
		$ids = \json_decode($request->get('ids'));

		if($ids == null || count($ids) != 7)
		{
			return 'Error';
		}

		$disk = CloudStorageHelper::getDisk();
		$img = Image::canvas(1362, 828, '#ffffff');

		$positions = [[0, 0], [0, 280], [0, 560], [1094, 0], [1094, 280], [1094, 560], [280, 0]];

		for($i = 0; $i < count($ids); $i++)
		{
			$photo = Photo::find($ids[$i]);
			if($photo != null)
			{
				if($i < 6)
				{
					$squareImage = Image::make($disk->get($photo->getLocalFileUrl(500)));
					$squareImage->resize(268, 268);
				}
				else
				{
					$squareImage = Image::make($photo->getFullFileUrl(5));
					$squareImage->resize(802, 828);
				}

				$img->insert($squareImage, 'top-left', $positions[$i][0], $positions[$i][1]);
			}
		}

		return $img->response('png');
	}


	public function download(Pack $pack)
	{
		$cloudDisk = CloudStorageHelper::getDisk();
		$localDisk = Storage::disk('local');

		// Create a temporary file
		$tmpFileName = 'pack-' . time() . '.zip';
		$tmpFile = tempnam(sys_get_temp_dir(), $tmpFileName);
		$tmpFileName = sys_get_temp_dir() . '/' . $tmpFileName;

		// Create the zip file
		$zip = new Filesystem(new ZipArchiveAdapter($tmpFileName));

		// --------------------------
		// Insert photos
		// --------------------------
		$photos = $pack->photos()->orderBy('name', 'asc')->get();
		foreach($photos as $photo)
		{
			$file = $cloudDisk->get(Thumbnails::getFullPhotoPath() . $photo->id . '.jpg');
			$zip->put($photo->id . '.jpg', $file);
		}

		// --------------------------
		// Add in the pack JSON document
		// --------------------------
		$packData = [
			'id' => $pack->id,
			'name' => $pack->name,
			'cover_id' => $pack->cover_id,
			'jigsaws' => []
		];

		foreach($pack->photos()->orderBy('name', 'asc')->get() as $photo)
		{
			$packData['jigsaws'][] = ['id' => $photo->id];
		}

		$zip->put('pack-' . $pack->id . '.json', json_encode($packData));


		// --------------------------
		// Generate the zip
		// --------------------------
		$zip->getAdapter()->getArchive()->close();

		// Force downloading the temporary file
		return Response::download($tmpFileName, 'pack-' . $pack->id . '.zip');
	}


	private function getTiers()
	{
		$ret = [];
		$ret[0] = 'Free';

		$prices = Prices::getPrices()['USD'];

		foreach($prices as $key => $value)
		{
			$ret[$key + 1] = 'Tier ' . ($key + 1) . ' ($' . number_format($value, 2) . ' USD)';
		}

		return $ret;
	}


	private function getStickerData()
	{
		$stickers = Sticker::orderBy('name', 'asc')->get();

		foreach($stickers as $sticker)
		{
			$sticker->img = $sticker->getCdnUrl(200);
		}

		return $stickers;
	}


	private function getRecommendedData($currentPackId = -1)
	{
		$packs = Pack::where('id', '!=', $currentPackId)
					->orderBy('name', 'asc')
					->get();

		return $packs;
	}


}
