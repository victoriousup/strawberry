<?php

namespace App\Console\Commands;

use App\Models\StockPhotos\StockPhoto;
use App\Models\StockPhotos\Source;
use App\Models\StockPhotos\Category;
use App\Utils\CloudStorageHelper;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Storage;
use App;
use Image;
use DB;
use App\Utils\Apps\JigsawDaily\Thumbnails;

class ImportStockPhotos extends Command
{
	const URL = 'https://www.crazy4jigsaws.com/admin/export-stock-photos/';

	protected $signature = 'stock-photos:import
							{limit=20 : The maximum number of stock photos to be imported at a time}
							{--skip : Skips downloading and resizing images that already exist}
							{--prod : Signals that this is running on a production environment}
							{--force : Reimports new data even if it already exists }';

	protected $description = 'Imports stock photos from the Crazy4Jigsaws server';

	private $key = 'dbQjNbyFWEmhhamK75VvP74h';
	private $dev = false;
	private $limit = 5;
	private $skip = false;
	private $force = false;

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->limit = $this->argument('limit');
		$this->dev = !$this->option('prod');
		$this->skip = $this->option('skip');
		$this->force = $this->option('force');

		$client = new Client();
		$response = $client->get(self::URL, [
			'query' => [
				'limit' => $this->limit,
				'key' => $this->key,
				'dev' => $this->dev,
			]
		]);

		if ($response->getStatusCode() != 200)
		{
			$this->error('Could not access Crazy4Jigsaws server');
			return;
		}

		$photos = json_decode($response->getBody());
		if($photos == null)
		{
			$this->error('Invalid response provided by Crazy4Jigsaws server');
			return;
		}

		foreach ($photos as $photo)
		{
			DB::transaction(function() use ($photo)
			{
				$this->importPhoto($photo);
			});
		}
	}


	private function importPhoto($photo)
	{
		$this->info('Importing photo #' . $photo->id);

		if(StockPhoto::where('id', $photo->id)->first())
		{

			if($this->force)
			{
				StockPhoto::where('id', $photo->id)->delete();
			}
			else
			{
				$this->error('Photo already exists in database, skipping');
				$this->confirmImport($photo->id);

				return;
			}
		}

		// Download stock photo
		if(!$this->downloadPhoto($photo->id, $photo->photo_url, $photo->square_cropping))
		{
			$this->error('Unable to download/upload photo #' . $photo->id);
			return;
		}

		// Source (Colourbox, Fotolia, etc)
		$source = Source::firstOrCreate(['name' => $photo->source]);

		// Image
		$image = new StockPhoto();
		$image->id = $photo->id;
		$image->name = $photo->name;
		$image->description = $photo->description;
		$image->keywords = $photo->keywords;
		$image->square_cropping = $photo->square_cropping;
		$image->source()->associate($source);
		$image->stock_photo_id = $photo->source_id;
		$image->source_file = $photo->source_file;
		$image->photographer = $photo->photographer;
		$image->photographer_id = $photo->photographer_id;
		$image->org_title = $photo->org_title;
		$image->org_description = $photo->org_description;
		$image->org_keywords = implode(', ', $photo->org_keywords);
		$image->save();

		// Categories
		foreach ($photo->categories as $item)
		{
			$cat = Category::firstOrCreate(['slug' => $item->category_id, 'name' => $item->category_name, 'parent_category_id' => null]);
			$subcat = Category::firstOrCreate(['slug' => $item->subcategory_id, 'name' => $item->subcategory_name, 'parent_category_id' => $cat->id]);
			$image->addToSubcategory($subcat);
		}

		$this->confirmImport($photo->id);
	}


	private function downloadPhoto($id, $url, $crop)
	{
		// Get the cloud disk
		$disk = CloudStorageHelper::getDisk();

		// Skip existing photos
		if($this->skip)
		{
			if($disk->exists('secure/stock-photos/source/' . $id . '.jpg'))
			{
				return true;
			}
		}

		try
		{
			$content = file_get_contents($url);
		}
		catch(\Exception $e)
		{
			$this->error($e->getMessage());
			return false;
		}

		// -----------------------
		// Save the source image
		// -----------------------
		if($disk->put('secure/stock-photos/source/' . $id . '.jpg', $content, 'private') !== true)
		{
			return false;
		}

		// -----------------------
		// Jigsaw daily
		// -----------------------
		if(!Thumbnails::generate($id, $content, $crop))
		{
			return false;
		}

		return true;
	}


	private function confirmImport($photoId)
	{
		if ($this->dev)
		{
			return;
		}

		$client = new Client();
		$client->get(self::URL, [
			'query' => [
				'key' => $this->key,
				'id' => $photoId
			]
		]);
	}


}
