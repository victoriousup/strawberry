<?php

namespace App\Http\Controllers;

use App\Models\Utils\HelpScoutConversation;
use App\Utils\GeoIP;
use HelpScout\ApiException;
use HelpScout\model\Conversation;
use HelpScout\model\Customer;
use HelpScout\model\ref\CustomerRef;
use HelpScout\model\ref\MailboxRef;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HelpScout\ApiClient;
use Log;

class ContactController extends Controller
{
	public function contactJigDaily(Request $request)
	{
		return $this->submit('jigdaily', 'Jigsaw Daily Support Request', $request);
	}


	private function submit(String $app, String $subject, Request $request)
	{
		$rules = [  'name' => 'required',
					'email' => 'required|email',
					'message' => 'required'];

		$validator = Validator::make($request->all(), $rules);

		// Invalid input
		if($validator->fails())
		{
			return response(['success' => false, 'message' => 'Please correct the errors below', 'errors' => $validator->errors()], 400);
		}

		// Submit to HelpScout
		$mailboxId = -1;
		if($app == 'jigdaily')
		{
			$mailboxId = config('helpscout.mailboxes.jigsawdaily');
		}

		$convId = $this->submitToHelpscout(
			$mailboxId,
			$this->getFirstName($request->get('name')),
			$this->getLastName($request->get('name')),
			$request->get('email'),
			$subject,
			$request->get('message'));

		// Could not submit message
		if($convId == null)
		{
			return response(['success' => false, 'message' => 'Sorry, we were unable to submit your message at this time. Please try again later.', 'errors' => []], 400);
		}

		// Record message in database
		$this->addToDatabase($convId, $app, $request);

		return ['success' => true, 'message' => 'Your message has been submitted. We\'ll get back to you shortly!'];
	}


	private function addToDatabase($helpscoutId, $app, Request $request)
	{
		$conv = new HelpScoutConversation($request->only(['platform', 'device_type', 'version', 'device', 'device_id', 'analytics_id', 'currency']));
		$conv->helpscout_id = $helpscoutId;
		$conv->app = $app;
		$conv->ip = $request->ip();

		// Additional attributes
		$attributes = [];
		foreach($request->all() as $key => $value)
		{
			$pos = strpos($key, 'attribute_');
			if($pos !== false && $pos == 0)
			{
				$attributes[substr($key, strlen('attribute_'))] = $value;
			}
		}

		if(count($attributes) > 0)
		{
			$conv->attributes = json_encode($attributes);
		}

		// Look up country and city info
		$geoIP = new GeoIP();

		try
		{
			if(!$geoIP->isLocalIP($conv->ip))
			{
				$location = $geoIP->locate($conv->ip);

				$conv->country = $location->country->name;
				$conv->country_code = $location->country->isoCode;
				$conv->state = $location->mostSpecificSubdivision->name;
				$conv->city = $location->city->name;
			}
		}
		catch(\Exception $e)
		{

		}

		$conv->save();
	}


	/**
	 * Submits a message to HelpScout. Returns the conversation id or null if the
	 * message could not be submitted.
	 *
	 * @param $firstName
	 * @param $lastName
	 * @param $email
	 * @param $message
	 *
	 * @return integer
	 */
	private function submitToHelpscout($mailboxId, $firstName, $lastName, $email, $subject, $message)
	{
		$helpscout = ApiClient::getInstance();
		$helpscout->setKey(config('helpscout.key'));

		$mailbox = new MailboxRef();
		$mailbox->setId($mailboxId);

		$customer = new CustomerRef();
		$customer->setEmail($email);
		$customer->setFirstName($firstName);
		$customer->setLastName($lastName);

		$conv = new Conversation();
		$conv->setSubject($subject);
		$conv->setMailbox($mailbox);
		$conv->setCustomer($customer);
		$conv->setType('email');

		$thread = new \HelpScout\model\thread\Customer();
		$thread->setType('customer');
		$thread->setBody($message);
		$thread->setStatus('active');

		$thread->setCreatedBy($customer);

		$conv->setThreads([$thread]);
		$conv->setCreatedBy($customer);

		try
		{
			$helpscout->createConversation($conv);
		}
		catch(ApiException $e)
		{
			Log::info($e->getMessage());
			return null;
		}

		return $conv->getId();
	}


	private function getFirstName($name)
	{
		return $this->getNameParts($name)['first'];
	}


	private function getLastName($name)
	{
		return $this->getNameParts($name)['last'];
	}


	private function getNameParts($name)
	{
		$nameParts = explode(' ', $name);
		$firstName = $nameParts[0];
		array_shift($nameParts);
		$lastName = implode(' ', $nameParts);

		return ['first' => $firstName, 'last' => $lastName];
	}

}
