<?php

namespace App\Http\ViewComposers\Admin\Layout;

use App\Http\ViewComposers\Admin\Layout\LeftNavItem;

class JigsawDailyLeftNav extends LeftNav
{
	protected function init()
	{
		$this->items[] = new LeftNavItem('Home', 'admin', null, 'fa-home');

		$this->items[] = new LeftNavItem('Dashboard', 'admin/jigsaw-daily/', null, 'fa-dashboard');

		$item = new LeftNavItem('Manage', '', 'jigdaily:manage', 'fa-cube');
		$item->items[] = new LeftNavItem('Store', 'admin/jigsaw-daily/store/');
		$item->items[] = new LeftNavItem('Packs', 'admin/jigsaw-daily/packs/');
		$item->items[] = new LeftNavItem('Stickers', 'admin/jigsaw-daily/stickers/');
		$item->items[] = new LeftNavItem('Organize', 'admin/jigsaw-daily/organize/');
		$item->items[] = new LeftNavItem('Daily Jigsaws', 'admin/jigsaw-daily/daily/');
		$this->items[] = $item;

		$item = new LeftNavItem("Reports", '', 'jigdaily:reports', 'fa-signal');
		$item->items[] = new LeftNavItem('Daily Revenue', 'admin/jigsaw-daily/reports/daily');
		$this->items[] = $item;

		$this->items[] = new LeftNavItem('Players', 'admin/jigsaw-daily/players/', 'jigdaily:players', 'fa-user');

		$item = new LeftNavItem('Settings', '', 'jigdaily:settings', 'fa-cog');
		$item->items[] = new LeftNavItem('Test Devices', 'admin/jigsaw-daily/test-devices/');
		$this->items[] = $item;
	}
}

