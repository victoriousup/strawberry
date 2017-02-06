<?php

namespace App\Models;


trait HasPermissions
{
	protected $loadedCachedPermissions = false;
	protected $cachedPermissions = [];


	public function permissions()
	{
		return $this->hasMany('App\Models\UserPermission', 'user_id', 'id');
	}


	public function addPermission($permission)
	{
		$this->loadCachedPermissions();

		if(!$this->hasPermission($permission))
		{
			$this->cachedPermissions[] = $permission;
			(new UserPermission(['user_id' => $this->id, 'permission' => $permission]))->save();
		}
	}


	public function deletePermission($permission)
	{
		$this->loadCachedPermissions();

		if($this->hasPermission($permission))
		{
			$this->permissions()->where('permission', $permission)->delete();

			$this->cachedPermissions = array_diff($this->cachedPermissions, [$permission]);
		}
	}


	public function hasPermission($permission)
	{
		$this->loadCachedPermissions();
		return array_search($permission, $this->cachedPermissions) !== false;
	}


	/**
	 * Syncs up an array of permissions with the database. Any
	 * permissions not included in this array will be removed for this
	 * user, and any new ones will be added.
	 *
	 * @param array $newPermissions
	 */
	public function syncPermissions(array $newPermissions)
	{
		$this->loadCachedPermissions();

		// Add new permissions
		foreach($newPermissions as $permission)
		{
			if(!$this->hasPermission($permission))
			{
				$this->addPermission($permission);
			}
		}

		// Remove missing permissions
		foreach($this->cachedPermissions as $permission)
		{
			if(array_search($permission, $newPermissions) === false)
			{
				$this->deletePermission($permission);
			}
		}

	}


	protected function loadCachedPermissions()
	{
		if($this->loadedCachedPermissions)
		{
			return;
		}

		$this->loadedCachedPermissions = true;
		$this->cachedPermissions = $this->permissions()->pluck('permission')->toArray();
	}
}