<?php

namespace App\Http\Controllers\Admin\Apps\JigsawDaily;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
	public function daily(Request $request)
	{
		$date = new Carbon($request->get('date', Carbon::now()));
		$platform = (int) $request->get('platform', -1);

		$query = DB::table('jigdaily_transactions')
			->select(DB::raw('pack_id, (SELECT name FROM jigdaily_packs WHERE id = pack_id) as pack_name, COUNT(*) as transactions, (SUM(price_usd) * 0.70) as revenue'))
			->whereDate('date', '=', $date->toDateString())
			->where('sandbox', false)
			->groupBy('pack_id')
			->orderBy('transactions', 'desc');

		if($platform >= 0)
		{
			$query->where('platform', $platform);
		}

		$packs = collect($query->get());

		$revenue = $packs->pluck('revenue')->sum();
		$transactions = $packs->pluck('transactions')->sum();

		return view('admin.apps.jigsawdaily.reports.daily', compact('date', 'platform', 'packs', 'revenue', 'transactions'));
	}

}