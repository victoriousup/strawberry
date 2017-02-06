@extends('layouts.admin.page', [

	'title' => 'Device Detail',

	'breadcrumbs' => [  'Jigsaw Daily', 'admin/jigsaw-daily/',
						'Players', 'admin/jigsaw-daily/players/']

])
@section('content')


	<div class="col-md-6">
		<div class="widget">
			<div class="widget-content padding-30">
				<div class="counter counter-lg">
					<div class="counter-label text-uppercase">Lifetime Value (USD)</div>
					<div class="counter-number-group">
						<span class="counter-icon margin-right-10 green-600">
							<i class="fa fa-bar-chart"></i>
						</span>
						<span class="counter-number">

							${{ number_format($lifetimeValue * 0.70, 2) }}

						</span>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="col-md-6">
		<div class="widget">
			<div class="widget-content padding-30">
				<div class="counter counter-lg">
					<div class="counter-label text-uppercase">Total Purchases</div>
					<div class="counter-number-group">
						<span class="counter-icon margin-right-10 blue-600">
							<i class="fa fa-shopping-cart"></i>
						</span>
						<span class="counter-number">{{ count($transactions) }}</span>
					</div>
				</div>
			</div>
		</div>
	</div>


	<table class="table table-bordered">
		<tr>
			<th>Pack</th>
			<th>Date</th>
			<th>Earnings (USD)</th>
			<th>Native Price</th>
			<th>Downloads</th>
			<th>Unique Devices</th>
			<th>Receipt Id</th>
		</tr>

		@foreach($transactions as $transaction)

			<tr>
				<td>

					{{ $transaction->transaction->pack->name }}

					@if($transaction->transaction->sandbox)

						<div class="label label-warning">Sandbox</div>

					@endif

					@if(!$transaction->original_purchaser)

						<div class="label label-default">Restored</div>

					@endif

				</td>
				<td>{{ $transaction->date }}</td>
				<td>${{ number_format($transaction->transaction->price_usd * 0.70, 2) }}</td>
				<td>{{ number_format($transaction->transaction->price, 2) }} {{ $transaction->transaction->currency }}</td>
				<td>{{ $transaction->transaction->downloads }}</td>
				<td>{{ $transaction->transaction->unique_devices }}</td>
				<td>{{ $transaction->transaction->receipt_id }}</td>
			</tr>

		@endforeach


	</table>





@endsection
