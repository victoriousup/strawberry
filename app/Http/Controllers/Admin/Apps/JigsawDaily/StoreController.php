<?php

namespace App\Http\Controllers\Admin\Apps\JigsawDaily;

use App\Models\Jigdaily\Pack;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreController extends Controller
{
	public function index()
	{
		$packs = Pack::where(['released' => true, 'visible' => true])->orderBy('store_order', 'asc')->get();
		return view('admin.apps.jigsawdaily.store', compact('packs'));
	}


	public function save(Request $request)
	{
		$packs = $request->get('packOrder');

		$i = 0;
		foreach($packs as $packId)
		{
			Pack::find($packId)->update(['store_order' => $i]);
			$i++;
		}

		return ['success' => true];
	}
}
