<?php

namespace App\Http\Controllers\Admin\Apps\JigsawDaily;

use App\Models\Jigdaily\Device;
use App\Models\Jigdaily\DeviceTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlayersController extends Controller
{
	public function index(Request $request)
	{
		$error = $request->get('error', false);

		return view('admin.apps.jigsawdaily.players.index', compact('error'));
	}


	public function device(Request $request)
	{
		$device = Device::where('device_id', $request->get('id'))->first();
		if($device == null)
		{
			return redirect('admin/jigsaw-daily/players/?error=true');
		}
		else
		{
			$transactions = DeviceTransaction::where('device_id', $device->id)->orderBy('date', 'ASC')->with('transaction.pack')->get();
			$lifetimeValue = $transactions->pluck('transaction')->pluck('price_usd')->sum();

			return view('admin.apps.jigsawdaily.players.device', compact('device', 'transactions', 'lifetimeValue'));
		}
	}



}