<?php

namespace App\Http\Controllers\Admin\Apps\JigsawDaily;

use App\Models\Jigdaily\Sticker;
use App\Models\Jigdaily\TestDevice;
use App\Utils\Apps\JigsawDaily\Thumbnails;
use App\Utils\CloudStorageHelper;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class TestDevicesController extends Controller
{
	public function index()
	{
		$devices = TestDevice::orderBy('id', 'asc')->get();

		return view('admin.apps.jigsawdaily.test-devices.index', compact('devices'));
	}


	public function create()
	{
		$title = 'Create Test Device';
		$device = new TestDevice();

		return view('admin.apps.jigsawdaily.test-devices.create', compact('title', 'device'));
	}


	public function store(Request $request)
	{
		$this->validate($request, [ 'device_id' => 'required|max:255|unique:jigdaily_test_devices',
									'description' => 'required|max:255']);

		$device = TestDevice::create($request->all());

		return redirect('admin/jigsaw-daily/test-devices/');
	}


	public function edit(TestDevice $device)
	{
		$title = 'Edit Test Device';

		return view('admin.apps.jigsawdaily.test-devices.create', compact('title', 'device'));
	}


	public function update(Request $request, TestDevice $device)
	{
		$device->update($request->all());

		return redirect('admin/jigsaw-daily/test-devices/');
	}


	public function delete(Request $request, TestDevice $device)
	{
		$device->delete();

		return redirect('admin/jigsaw-daily/test-devices/');
	}

}
