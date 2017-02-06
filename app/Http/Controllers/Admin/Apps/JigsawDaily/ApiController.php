<?php

namespace App\Http\Controllers\Admin\Apps\JigsawDaily;

use App\Http\Controllers\Controller;
use App\Models\Jigdaily\Coupon;
use App\Models\Jigdaily\DailyPhoto;
use App\Models\Jigdaily\Device;
use App\Models\Jigdaily\DeviceTransaction;
use App\Models\Jigdaily\Pack;
use App\Models\Jigdaily\PackPromo;
use App\Models\Jigdaily\TestDevice;
use App\Models\Jigdaily\Transaction;
use App\Utils\Apps\JigsawDaily\CouponReceipt;
use App\Utils\Apps\JigsawDaily\FreeReceipt;
use App\Utils\Apps\JigsawDaily\KeyReceipt;
use App\Utils\Apps\JigsawDaily\Thumbnails;
use App\Utils\ExchangeRates;
use App\Utils\RandomString;
use App\Utils\Receipts\AppleReceipt;
use App\Utils\Receipts\GoogleReceipt;
use App\Utils\Receipts\Receipt;
use Carbon\Carbon;
use Cache;
use Illuminate\Http\Request;
use Log;

class ApiController extends Controller
{
	public function version()
	{
		$version = Cache::get('jigdaily.version', 0);

		// How many packs are released?
		$releasedPacks = Pack::where(['released' => true, 'visible' => true])->count();

		return ['version' => $version, 'packs' => $releasedPacks];
	}


	public function featured()
	{
		$packs = Pack::where(['released' => 1, 'visible' => 1, 'featured' => 1])->get(['id', 'name', 'cover_id']);

		foreach($packs as $pack)
		{
			$pack->img = $pack->getCover()->getFileUrl('{width}');
			unset($pack->cover_id);
		}

		return ['packs' => $packs, 'thumbnails' => Thumbnails::getWidths()];
	}


	public function store()
	{
		$packs = Pack::where(['released' => 1, 'visible' => 1])->orderBy('store_order', 'asc')->get(['id', 'name', 'cover_id', 'sticker_ids', 'visible']);

		foreach($packs as $pack)
		{
			$pack->img = $pack->getCover()->getFileUrl('{width}');
			$pack->product_id = $this->getProductId($pack->id);
			unset($pack->cover_id);

			$pack->stickers = $pack->getStickers();
			unset($pack->sticker_ids);

			foreach($pack->stickers as $sticker)
			{
				$sticker->img = $sticker->getCdnUrl('{width}');
				unset($sticker->name, $sticker->file, $sticker->id);
			}
		}

		return ['packs' => $packs, 'thumbnails' => Thumbnails::getWidths()];
	}


	public function storePack(Pack $pack)
	{
		if(!$pack->released)
		{
			abort(404);
		}

		$photos = $pack->photos()->orderBy('name', 'asc')->get(['id']);

		$ret = [];
		$ret['id'] = $pack->id;
		$ret['name'] = $pack->name;
		$ret['product_id'] = $this->getProductId($pack->id);
		$ret['thumbnails'] = Thumbnails::getWidths();
		$ret['version'] = Cache::get('jigdaily.version', 0);
		$ret['jigsaws'] = [];

		foreach($photos as $photo)
		{
			$photo->img = $photo->getFileUrl('{width}');

			$ret['jigsaws'][] = $photo;
		}

		return $ret;
	}


	public function packInfo(Request $request)
	{
		$packIds = $request->get('pack_ids', []);
		$packs = Pack::whereIn('id', $packIds)->where('released', true)->get();

		$ret = [];
		$ret['thumbnails'] = Thumbnails::getWidths();
		$ret['packs'] = [];

		foreach($packs as $pack)
		{
			$cover = $pack->getCover();
			if($cover == null)
			{
				continue;
			}

			$tmpPack = [];
			$tmpPack['id'] = $pack->id;
			$tmpPack['name'] = $pack->name;
			$tmpPack['cover_id'] = $pack->cover_id;
			$tmpPack['img'] = $cover->getFileUrl('{width}');

			$ret['packs'][] = $tmpPack;
		}

		return $ret;
	}


	public function recommended(Pack $pack)
	{
		if(!$pack->released)
		{
			abort(404);
		}

		$packs = $pack->getRecommendedPacks()->where('released', true)->get(['id', 'name', 'cover_id']);

		if(sizeof($packs) < 5)
		{
			$ids = $packs->pluck('id');
			$ids->push($pack->id);

			$randPacks = Pack::whereNotIn('id', $ids)->where('released', true)->where('price_tier', '>=', 1)->orderByRaw('RANDOM()')->limit(5)->get(['id', 'name', 'cover_id']);
			$packs = $packs->merge($randPacks);
		}

		foreach($packs as $tmpPack)
		{
			$tmpPack->img = $tmpPack->getCover()->getFileUrl('{width}');
			unset($tmpPack->cover_id);
		}

		return ['packs' => $packs, 'thumbnails' => Thumbnails::getWidths()];
	}


	public function testDevice(Request $request)
	{
		Log::info('Checking testing device ' . $request->get('device_id'));

		$device = TestDevice::where('device_id', $request->get('device_id'))->first();

		if(is_null($device) || !$device->active)
		{
			return response(['error' => 'Invalid', 'message' => 'This device is not a testing device'], 400);
		}
		else
		{
			return ['success' => true];
		}
	}


	public function daily($date)
	{
		$today = new Carbon();
		$date = new Carbon($date);

		if(abs($date->diffInDays($today)) > 3)
		{
			return response(['error' => 'IncorrectDate', 'serverDate' => $today->format('Y-m-d')], 400);
		}

		$daily = DailyPhoto::where('date', $date->format('Y-m-d'))->with('photo')->first();
		if($daily == null)
		{
			$daily = $this->chooseRandomDaily($date);
			if($daily == null)
			{
				return response(['error' => 'NoJigsaw'], 400);
			}
		}

		$photo = $daily->photo()->first();
		$pack = Pack::where(['id' => $photo->pack_id, 'released' => true])->first(['id', 'name']);

		$ret = [];
		$ret['date'] = $date->format('Y-m-d');
		$ret['id'] = $photo->id;
		$ret['img'] = $photo->getFileUrl('{width}');
		$ret['full_img'] = $photo->getFullFileUrl(60 * 24 * 3);
		$ret['thumbnails'] = Thumbnails::getWidths();
		$ret['pack_id'] = $pack->id;
		$ret['pack_name'] = $pack->name;

		return $ret;
	}


	public function dailyPromos(Pack $pack, Request $request)
	{
		$ret = [];
		$ret['daily_promos'] = [];
		$ret['recommended_promos'] = [];
		$ret['thumbnails'] = PackPromo::getWidths();

		$promos = PackPromo::where('pack_id', $pack->id)->where('status', 1)->where('type', '<=', 1)->get(['id', 'pack_id', 'name', 'currency', 'price', 'file']);
		foreach($promos as $promo)
		{
			$promo->img = $promo->getCdnUrl('{width}');
			unset($promo->file);
			$ret['daily_promos'][] = $promo;
		}

		$promos = PackPromo::where('pack_id', $pack->id)->where('status', 1)->whereIn('type', [0, 2])->get(['id', 'pack_id', 'name', 'currency', 'price', 'file']);
		foreach($promos as $promo)
		{
			$promo->img = $promo->getCdnUrl('{width}');
			unset($promo->file);
			$ret['recommended_promos'][] = $promo;
		}

		return $ret;
	}


	public function purchase(Request $request)
	{
		// Request vars
		$platform = $request->get('platform', 0);
		$price = $request->get('price', 0);
		$currency = $request->get('currency', 'USD');
		$packId = $request->get('pack_id', 0);
		$couponCode = $request->get('coupon', null);
		$deviceId = $request->get('device_id', '');
		$testingDevice = (bool) $request->get('testing_device', false);
		$key = $request->get('key', null);

		$receipt = null;
		$firstTimePurchase = false;
		$priceUSD = 0;
		$productId = $this->getProductId($packId);

		try
		{
			// Existing purchase key
			if($key != null)
			{
				$receipt = new KeyReceipt($platform, $packId, $key);
			}
			// Testing device (free IAP)
			else if($testingDevice)
			{
				$receipt = new FreeReceipt($platform, $deviceId, $productId);
			}
			// Coupon code
			else if($couponCode != null)
			{
				$receipt = new CouponReceipt($platform, $packId, $deviceId, $couponCode);
			}
			// Apple (iOS)
			else if($platform == Receipt::PLATFORM_APPLE)
			{
				$receipt = new AppleReceipt($request->get('receipt_data'), $productId);
			}
			// Google (Android)
			else
			{
				$receipt = new GoogleReceipt($request->get('signature'), $request->get('response_data'), config('google.jigsaw_daily.key'), $productId);
			}
		}
		// An exception occured, try again later
		catch(\Exception $e)
		{
			return response(['error' => 'UnableToValidate', 'message' => 'Try again later'], 400);
		}

		// Fail
		if(!$receipt->valid)
		{
			return response(['error' => 'InvalidReceipt', 'message' => $receipt->error], 400);
		}

		// Sandbox?
		$sandbox = $request->get('sandbox', $receipt->sandbox);

		// Look up data about the pack
		$packData = $this->getPurchasedPackData($packId);
		if($packData == null)
		{
			return response(['error' => 'InvalidReceipt'], 400);
		}


		// ------------------------
		// Device
		// ------------------------
		$device = Device::where(['platform' => $platform, 'device_id' => $deviceId])->first();
		if($device == null)
		{
			$device = new Device([

				'platform' => $platform,
				'device_id' => $deviceId

			]);
			$device->save();
		}


		// ------------------------
		// Key receipt
		// ------------------------
		if(is_a($receipt, KeyReceipt::class))
		{
			$transaction = $receipt->transaction;
			$transaction->downloads++;
			$transaction->save();
		}
		else
		{
			// ------------------------
			// Existing transaction?
			// ------------------------
			$transaction = Transaction::where(['platform' => $platform, 'receipt_id' => $receipt->originalTransactionId])->first();
			if($transaction != null)
			{
				// Has this receipt been disabled?
				if(!$transaction->active)
				{
					return response(['error' => 'ReceiptDisabled'], 400);
				}

				$transaction->downloads++;
				$transaction->save();
			}
			// ------------------------
			// New transaction
			// ------------------------
			else
			{
				$firstTimePurchase = true;

				// Convert currency
				$priceUSD = ExchangeRates::convertToUSD($price, $currency);
				if($priceUSD == null)
				{
					$priceUSD = 0;
				}

				$transaction = new Transaction([

					'platform'   => $platform,
					'receipt_id' => $receipt->originalTransactionId,
					'pack_id'    => $packId,
					'price'      => $price,
					'currency'   => $currency,
					'price_usd'  => $priceUSD,
					'sandbox'    => $sandbox,
					'downloads'  => 1,
					'coupon_id'  => is_a($receipt, CouponReceipt::class) ? $receipt->coupon->id : null,

				]);
				$transaction->save();

				// Coupon code
				if(is_a($receipt, CouponReceipt::class))
				{
					$receipt->coupon->redemptions++;
					$receipt->coupon->save();
				}
			}
		}


		// ------------------------
		// Device transaction
		// ------------------------
		$deviceTransaction = DeviceTransaction::where(['device_id' => $device->id, 'transaction_id' => $transaction->id])->first();
		if($deviceTransaction == null)
		{
			$deviceTransaction = new DeviceTransaction([

				'device_id' => $device->id,
				'transaction_id' => $transaction->id,
				'ip' => $request->ip(),
				'original_purchaser' => $firstTimePurchase,
				'downloads' => 1,

			]);
			$deviceTransaction->save();

			$transaction->unique_devices++;
			$transaction->save();
		}
		else
		{
			$deviceTransaction->downloads++;
			$deviceTransaction->save();
		}


		// Fill in additional data
		return array_merge($packData, [

			'price_usd' => $priceUSD * 0.70,
			'first_time_purchase' => $firstTimePurchase,
			'sandbox' => $sandbox,
			'key' => KeyReceipt::getKeyFromTransactionId($transaction->id)

		]);
	}


	/**
	 * -------------------------------------------------------------------------------
	 * Returns cached pack data needed to download a pack.
	 *
	 * @param $packId
	 *
	 * @return mixed
	 * -------------------------------------------------------------------------------
	 */
	private function getPurchasedPackData($packId)
	{
		$cacheKey = 'jigdaily.purchased.pack.' . $packId;

		$data = Cache::get($cacheKey, function() use ($packId, $cacheKey)
		{
			// Find pack data
			$pack = Pack::find($packId);
			if($pack == null || !$pack->released)
			{
				return null;
			}

			// Find photos
			$photos = $pack->photos()->orderBy('name', 'asc')->get();

			// Generate pack data
			$data = [];
			$data['name'] = $pack->name;
			$data['cover_id'] = $pack->cover_id;
			$data['cover_url'] = $pack->getCover()->getFileUrl('{width}');
			$data['jigsaws'] = [];

			foreach($photos as $photo)
			{
				$data['jigsaws'][] = [
						'id' => $photo->id,
						'img' => $photo->getFullFileUrl(60 * 48)
				];
			}

			// Store the pack data
			Cache::put($cacheKey, $data, Carbon::now()->addHours(12));

			return $data;
		});

		return $data;
	}


	private function getProductId($packId)
	{
		return 'com.digitalstrawberry.jigsawdaily.pack.' . $packId;
	}


	private function chooseRandomDaily($date)
	{
		// Randomly choose a released pack
		$pack = Pack::where(['released' => 1, 'visible' => 1])->orderByRaw("RANDOM()")->limit(1)->first();
		if($pack != null)
		{
			// Randomly choose a puzzle from the pack
			$photo = $pack->photos()->orderByRaw("RANDOM()")->limit(1)->first();
			if($photo != null)
			{
				// Save to the database
				$daily = DailyPhoto::firstOrCreate(['date' => $date, 'photo_id' => $photo->id]);

				return $daily;
			}
		}

		return null;
	}


}
