<?php


namespace App\Http\Controllers;

use App\Models\Utils\HelpScoutConversation;
use HelpScoutApp\DynamicApp;
use Illuminate\Routing\Controller;

class HelpScoutAppController extends Controller
{
	public function index()
	{
		$app = new DynamicApp('Ov3VJ6r0ZTyfn4jHhEiw');
		if($app->isSignatureValid())
		{
			$html = [];
			$convId = $app->getConversation()->getId();

			$conv = HelpScoutConversation::where('helpscout_id', $app->getConversation()->getId())->first();
			if(!is_null($conv))
			{
				// ----------------
				// Device
				// ----------------
				$html[] = '<h1><b>Device</h1></b>';
				$html[] = '<ul>';

				// Platform
				if(!is_null($conv->platform) && !is_null($conv->device_type))
				{
					$html[] = '<li><b>Platform: </b>';
					$html[] = $conv->platform == 'iOS' ? 'iOS' : 'Android';
					$html[] = $conv->device_type == 'phone' ? ' Phone' : ' Tablet';
					$html[] = '</li>';
				}

				// Version
				if(!is_null($conv->version))
				{
					$html[] = '<li><b>App Version: </b>' . $conv->version . '</li>';
				}

				// Device
				if(!is_null($conv->device))
				{
					$html[] = '<li><b>Type: </b>' . $conv->device . '</li>';
				}

				// UUID
				if(!is_null($conv->device_id))
				{
					$html[] = '<li><b>UUID: </b>' . $conv->device_id . '</li>';
				}

				$html[] = '</ul><br>';

				// ----------------
				// User
				// ----------------
				$html[] = '<h1><b>User</b></h1>';
				$html[] = '<ul>';

				// Currency
				if(!is_null($conv->currency) && $conv->currency != 'null')
				{
					$html[] = '<li><b>Currency: </b>' . $conv->currency . '</li>';
				}

				// Location
				if(!is_null($conv->country) && !is_null($conv->state))
				{
					$html[] = '<li><b>Location: </b>' . $conv->state . ', ' . $conv->country . '</li>';
				}

				$html[] = '</ul><br>';

				// ----------------
				// Actions
				// ----------------
				$html[] = '<h1><b>Actions</b></h1>';
				$html[] = '<ul>';

				// View purchases
				if(!is_null($conv->device_id))
				{
					$html[] = '<li><a href="' . url('admin/jigsaw-daily/players/device?id=' . $conv->device_id) . '">View Purchases</a></li>';
				}

				// View events
				if(!is_null($conv->analytics_id))
				{
					$html[] = '<li><a href="">View Events</a></li>';
				}

				$html[] = '</ul>';
			}

			return $app->getResponse(implode($html));

		}
		else
		{
			return response('Invalid Request', 400);
		}
	}
}