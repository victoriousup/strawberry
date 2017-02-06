<?php

namespace App\Http\ViewComposers\Admin\Layout;

use App\Http\ViewComposers\Admin\Layout\LeftNavItem;

class BaseLeftNav extends LeftNav
{
	protected function init()
	{
		// ---------------
		// Home
		// ---------------
		$this->items[] = new LeftNavItem('Home', 'admin/', null, 'fa-home');


		// ---------------
		// Apps
		// ---------------
		$item = new LeftNavItem('Apps', '', null, 'fa-rocket');
		$item->items = [
			new LeftNavItem('Jigsaw Daily', 'admin/jigsaw-daily/', 'jigdaily')
		];
		$this->items[] = $item;


		// ---------------
		// Administration
		// ---------------
		$item = new LeftNavItem('Administration', '', 'admin', 'fa-cog');
		$this->items[] = $item;
		$item->items = [
			new LeftNavItem('Users', 'admin/users'),
			new LeftNavItem('Access Logs', 'admin/users/logs')
		];
	}
}

