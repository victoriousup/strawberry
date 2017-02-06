{!! BForm::open()->addClass('form-inline')->id('filter') !!}

{!! BForm::select('Not Used In', 'daysAgoUsed')
			->options([
				'any' => '(Show All)',
				'gt:7' => '7 Days',
				'gt:30' => '30 Days',
				'gt:60' => '60 Days',
				'gt:90' => '90 Days',
				])
			->addClass('filter-control filter-numeric filter-null-matches-all') !!}

{!! BForm::text('Pack Name', 'name')->addClass('filter-control') !!}

{!! BForm::close() !!}