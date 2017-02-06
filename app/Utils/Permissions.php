<?php

namespace App\Utils;

class Permissions
{
	public static function getPermissions()
	{
		$ret = [];

		// Jigsaw Daily
		$per = new Permission('jigdaily', 'Jigsaw Daily');
		$per->addSub(new Permission('jigdaily:manage', 'Manage (Packs, Stickers, etc)'));
		$per->addSub(new Permission('jigdaily:reports', 'Sales Reports'));
		$per->addSub(new Permission('jigdaily:players', 'Player Details'));
		$ret[] = $per;

		return $ret;
	}
}

class Permission
{
	public $id;
	public $name;
	public $sub = [];

	public function __construct($id, $name)
	{
		$this->id = $id;
		$this->name = $name;
	}

	public function addSub($permission)
	{
		$this->sub[] = $permission;
	}
}