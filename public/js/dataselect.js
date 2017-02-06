/**
 *
 * @param selectBox     jQuery reference to the empty select box
 * @param hiddenInput   jQuery reference to the hidden form element
 * @param itemDisplay   jQuery reference to the element displaying selected items
 * @param data          Array of select data to be displayed
 *
 * @constructor
 */
function DataSelect(selectBox, hiddenInput, itemDisplay, data)
{
	// jQuery reference to the empty select box
	var selectBox = selectBox;

	// jQuery reference to the hidden form element that stores JSON ids
	var hiddenInput = hiddenInput;

	// jQuery reference to the element that will store selected items
	var itemDisplay = itemDisplay;

	// Array of select data to be displayed. Each element must have at least an id and name field.
	var data = data;

	// Template to render the selected elements
	var template =      '<div class="label label-primary label-outline">' +
						'   <a href="" data-id="{{id}}"><i class="fa-close" /></a>' +
						'   {{name}}' +
						'</div>';

	// Helper reference
	var _this = this;

	// Updated callback function
	this.updated = null;

	/**
	 * Initial setup method.
	 */
	this.init = function()
	{
		// Watch for changes in the select box
		selectBox.on('change', function()
		{
			// Add the newly selected item
			addItem(parseInt($(this).val()));
		});

		// Render the initial view
		update();
	};


	/**
	 * Adds an item id to the selected items.
	 *
	 * @param id
	 */
	var addItem = function(id)
	{
		var itemIds = getSelectedIds();
		itemIds.push(id);
		setSelectedIds(itemIds);

		update();
	};


	/**
	 * Removes an item id from the selected items.
	 *
	 * @param id
	 */
	this.removeItem = function(id)
	{
		var itemIds = getSelectedIds();
		var index = itemIds.indexOf(id);
		if(index != -1)
		{
			itemIds.splice(index, 1);
		}

		setSelectedIds(itemIds);
		update();
	};


	/**
	 * Renders an update
	 */
	var update = function()
	{
		var itemIds = getSelectedIds();

		// Clear all data from the select box
		selectBox.empty();

		// Add an empty placeholder on top
		selectBox.append('<option value="" disabled selected></option>');

		// Build the select box
		for(var i = 0; i < data.length; i++)
		{
			// Make sure this item is not already selected
			if(itemIds.indexOf(data[i].id) == -1)
			{
				selectBox.append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
			}
		}

		// Empty the item display
		itemDisplay.empty();

		// Build the new item display
		for(i = 0; i < itemIds.length; i++)
		{
			var itemData = getItemData(itemIds[i]);
			var html = template;
			html = html.replace('{{name}}', itemData.name);
			html = html.replace('{{id}}', itemData.id);

			// Handle item deletion
			var htmlItem = $(html);
			htmlItem.find('a').on('click', function()
			{
				var id = $(this).data('id');
				_this.removeItem(id);

				return false;
			});

			itemDisplay.append(htmlItem);
		}

		// Notify callback
		if(_this.updated)
		{
			_this.updated(getSelectedData());
		}
	};


	/**
	 * Returns an array of the currently selected item ids.
	 */
	var getSelectedIds = function()
	{
		var data = hiddenInput.val();

		if(data == '')
		{
			return [];
		}
		else
		{
			return JSON.parse(data);
		}
	};


	/**
	 * Sets the currently selected ids from an array of item ids
	 *
	 * @param ids
	 */
	var setSelectedIds = function(ids)
	{
		var data = JSON.stringify(ids);
		hiddenInput.val(data);
	};


	/**
	 * Returns an array of the currently selected data
	 */
	var getSelectedData = function()
	{
		var ids = getSelectedIds();
		var ret = [];

		for(var i = 0; i < ids.length; i++)
		{
			ret.push(getItemData(ids[i]));
		}

		return ret;
	};


	/**
	 * Returns an item based off the item's id
	 *
	 * @param id
	 */
	var getItemData = function(id)
	{
		for(var i = 0; i < data.length; i++)
		{
			if(data[i].id == id)
			{
				return data[i];
			}
		}

		return null;
	};
}