<?php
namespace App\Http\ViewComposers\Admin\Layout;

class LeftNavItem
{
	public $name;
	public $url;
	public $permission;
	public $icon;
	public $badge;
	public $items = [];
	public $selected = false;

	public function __construct($name, $url, $permission = null, $icon = null, $badge = null)
	{
		$this->name = $name;
		$this->url = $url;
		$this->permission = $permission;
		$this->icon = $icon;
		$this->badge = $badge;
	}

	public function getIconHtml()
	{
		if($this->icon == null || $this->icon == '')
		{
			return '';
		}

		return '<i class="site-menu-icon ' . $this->icon . '" aria-hidden="true"></i>';
	}
}