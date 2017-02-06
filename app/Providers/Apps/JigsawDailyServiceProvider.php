<?php

namespace App\Providers\Apps;

use App\Models\Jigdaily\Coupon;
use App\Models\Jigdaily\Pack;
use App\Models\Jigdaily\PackPromo;
use App\Models\Jigdaily\Photo;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Cache;
use App\Jobs\ClearCDNCache;
use Carbon\Carbon;
use DB;



class JigsawDailyServiceProvider extends ServiceProvider
{
	use DispatchesJobs;

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{

		// ---------------------------------------
		// Pack promo has been saved
		// ---------------------------------------
		PackPromo::saved(function(PackPromo $promo)
		{
			$this->dispatch(new ClearCDNCache(['api/jigsaw-daily/v1/daily-promos/' . $promo->pack_id]));
		});


		// ---------------------------------------
		// Pack model has been saved
		// ---------------------------------------
		Pack::saved(function(Pack $pack)
		{
			// ----------------------
			// Create coupon code for free packs
			// ----------------------
			if($pack->coupon_id == null && $pack->price_tier == 0 && $pack->released)
			{
				$coupon = new Coupon();
				$coupon->pack_id = $pack->id;
				$coupon->code = Coupon::generateCode();
				$coupon->save();

				$pack->coupon()->associate($coupon);
				$pack->save();
			}
		});


		// ---------------------------------------
		// Pack model is being saved
		// ---------------------------------------
		Pack::saving(function(Pack $pack)
		{
			$orgPack = $pack->getOriginal();

			// ----------------------
			// Remove old coupon code
			// ----------------------
			if($pack->coupon_id != null && $pack->price_tier > 0)
			{
				$coupon = $pack->coupon;
				$coupon->expiration = Carbon::now()->addHours(24);
				$coupon->save();

				$pack->coupon()->dissociate();
			}


			// ----------------------
			// Update Google Play
			// ----------------------
			if(isset($pack->price_tier) && $pack->price_tier > 0)
			{
				// Did anything change since the last update?
				if(!$pack->exists || $orgPack['name'] != $pack->name || $orgPack['price_tier'] != $pack->price_tier)
				{
					$pack->saveToGooglePlay();
				}
			}


			// ----------------------
			// Update version
			// ----------------------
			if((isset($orgPack['released']) && $orgPack['released']) || (isset($pack->released) && $pack->released))
			{
				$this->updateVersion();
				$this->clearPackCache($pack->id);
			}

		});


		// ---------------------------------------
		// Photo model is being saved
		// ---------------------------------------
		Photo::saving(function(Photo $photo)
		{
			$orgPhoto = $photo->getOriginal();
			$updateVersion = false;

			// ----------------------
			// New pack
			// ----------------------
			if(isset($photo->pack_id))
			{
				$pack = Pack::find($photo->pack_id);
				if($pack != null && $pack->released)
				{
					$updateVersion = true;
					$this->clearPackCache($pack->id);
				}
			}


			// ----------------------
			// Old pack
			// ----------------------
			if(isset($orgPhoto['pack_id']) && $orgPhoto['pack_id'] != $photo->pack_id)
			{
				$pack = Pack::find($orgPhoto['pack_id']);
				if($pack != null && $pack->released)
				{
					$updateVersion = true;
					$this->clearPackCache($pack->id);
				}
			}

			// Update version?
			if($updateVersion)
			{
				$this->updateVersion();
			}

		});


	}


	private function updateVersion()
	{
		Cache::forever('jigdaily.version', time());

		// Clear CDN cache
		$this->dispatch(new ClearCDNCache(['api/jigsaw-daily/v1/version']));
	}


	private function clearPackCache($packId)
	{
		// Clear cached pack data
		Cache::forget('jigdaily.purchased.pack.' . $packId);

		// Clear CDN cache (store and pack data)
		$this->dispatch(new ClearCDNCache(['api/jigsaw-daily/v1/store', 'api/jigsaw-daily/v1/store/pack/' . $packId, 'api/jigsaw-daily/v1/recommended/' . $packId]));
	}



	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
