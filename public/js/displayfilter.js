/**
 * Filters a group of elements based on form filters.
 *
 * @param form          jQuery reference to the filter elements container
 * @param container     jQuery reference to the table
 *
 * @constructor
 */
function DisplayFilter(form, container)
{
	var FILTER_CONTROL_CLASS = '.filter-control';
	var ELEMENT_CLASS = '.filter-element';

	var init = function()
	{
		// Watch for filter controls changes
		form.find(FILTER_CONTROL_CLASS).each(function()
		{
			// Input type
			if($(this).is('input'))
			{
				$(this).on('keyup', filter);
			}
			// Select type
			else
			{
				$(this).on('change', filter);
			}

		});

		// Set up initial filtering
		filter();
	};


	var filter = function()
	{
		// Cycle through the elements
		container.find(ELEMENT_CLASS).each(function()
		{
			var element = $(this);
			var elementData = element.data('filter');
			var showElement = true;

			// Cycle through the filters
			form.find(FILTER_CONTROL_CLASS).each(function()
			{
				var filterName = $(this).attr('name');
				var filterValue = $(this).val();

				var isNumeric = $(this).hasClass('filter-numeric');
				var nullMatchesAll = $(this).hasClass('filter-null-matches-all');

				var elementValue = elementData[filterName];

				// Match anything
				if(filterValue == 'any')
				{
					// Do nothing...
				}
				// Null data value
				else if(elementValue == null && nullMatchesAll)
				{
					showElement = true;
				}
				// No data value
				else if(elementValue == undefined)
				{
					showElement = false;
				}
				// Numeric match
				else if(isNumeric)
				{
					var tmpArray = filterValue.split(':');
					var op = tmpArray[0];
					var val = Number(tmpArray[1]);

					if(op == 'lt')
					{
						showElement = elementValue < val;
					}
					else if(op == 'lte')
					{
						showElement = elementValue <= val;
					}
					else if(op == 'eq')
					{
						showElement = elementValue == val;
					}
					else if(op == 'gt')
					{
						showElement = elementValue > val;
					}
					else if(op == 'gte')
					{
						showElement = elementValue >= val;
					}
				}
				// Sub-match
				else if($(this).is('input'))
				{
					if(filterValue != '')
					{
						showElement = elementValue.toLowerCase().indexOf(filterValue.toLowerCase()) != -1;
					}
				}
				// Exact value match
				else
				{
					showElement = elementValue == filterValue;
				}

				// Break out of loop?
				if(!showElement)
				{
					return false;
				}

			});


			if(showElement)
			{
				$(this).show();
			}
			else
			{
				$(this).hide();
			}

		});
	};



	init();
}