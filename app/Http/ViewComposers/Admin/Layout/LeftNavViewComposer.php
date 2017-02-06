<?php

namespace App\Http\ViewComposers\Admin\Layout;

use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class LeftNavViewComposer
{

	public function __construct()
	{

	}

	public function compose(View $view)
	{
		$uri = Route::current()->getUri();

		// Jigsaw Daily
		if(starts_with($uri, 'admin/jigsaw-daily'))
		{
			$view->with('left_nav_html', (new JigsawDailyLeftNav($uri))->html());
		}
		else
		{
			$view->with('left_nav_html', (new BaseLeftNav($uri))->html());
		}

	}

}

?>