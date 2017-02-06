<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Utils\Prices;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Google_Client;
use Google_Service_AndroidPublisher;
use Google_Service_AndroidPublisher_InAppProduct;
use Google_Service_AndroidPublisher_Price;
use Google_Auth_AssertionCredentials;
use Google_Service_AndroidPublisher_InAppProductListing;

class SaveAndroidIAP extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	protected $iap;

	/**
	 * Create a new job instance.
	 */
	public function __construct(Google_Service_AndroidPublisher_InAppProduct $iap)
	{
		$this->iap = $iap;
	}


	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$cred = new Google_Auth_AssertionCredentials(
			config('google.client_email'),
			['https://www.googleapis.com/auth/androidpublisher'],
			config('google.private_key'));

		$client = new Google_Client();
		$client->setAssertionCredentials($cred);
		if($client->getAuth()->isAccessTokenExpired())
		{
			$client->getAuth()->refreshTokenWithAssertion();
		}

		$service = new Google_Service_AndroidPublisher($client);
		$tmpIAP = null;

		try
		{
			$tmpIAP = $service->inappproducts->get($this->iap->getPackageName(), $this->iap->getSku());
		}
		catch(\Exception $e)
		{
			// Product doesn't exist
		}

		$result = null;
		$opt = ['autoConvertMissingPrices' => true];

		// New product
		if($tmpIAP == null)
		{
			$result = $service->inappproducts->insert($this->iap->getPackageName(), $this->iap, $opt);
		}
		// Update product
		else
		{
			$result = $service->inappproducts->update($this->iap->getPackageName(), $this->iap->getSku(), $this->iap, $opt);
		}
	}


	public static function createSimpleIAP($packageName, $sku, $title, $description, $priceUSD, $prices = []) : Google_Service_AndroidPublisher_InAppProduct
	{
		$pricesObj = [];

		foreach($prices as $key => $value)
		{
			$tmpPrice = new Google_Service_AndroidPublisher_Price();
			$tmpPrice->setPriceMicros($value * 1000000);
			$tmpPrice->setCurrency($key);

			foreach(Prices::getCountryCodesForCurrency($key) as $country)
			{
				$pricesObj[$country] = $tmpPrice;
			}
		}

		$defaultPrice = new Google_Service_AndroidPublisher_Price();
		$defaultPrice->setPriceMicros($priceUSD * 1000000);
		$defaultPrice->setCurrency('USD');

		$iap = new Google_Service_AndroidPublisher_InAppProduct();
		$iap->setPackageName($packageName);
		$iap->setSku($sku);
		$iap->setDefaultLanguage('en-US');
		$iap->setDefaultPrice($defaultPrice);
		$iap->setPrices($pricesObj);
		$iap->setListings(['en-US' => ['title' => $title, 'description' => $description]]);
		$iap->setPurchaseType('managedUser');
		$iap->setStatus('active');

		return $iap;
	}

}
