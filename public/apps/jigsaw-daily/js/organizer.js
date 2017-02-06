/**
 * The Organizer class contains all of the logic to display the photo filtering and communication with the server.
 *
 * @constructor
 */
function Organizer()
{
	var _this = this;

	// Reference to the category filter
	var categoryFilter = $('select[name=category]');

	// Reference to the subcategory filter
	var subcategoryFilter = $('select[name=subcategory]');

	// Reference to the search filter
	var searchFilter = $('input[name=search]');

	// Container to hold loaded photos
	var photosContainer = $('#photos');

	var loadingText = $('#loading');

	// Current offset for lazy loading
	var offset = 0;

	// Is there more data to lazy load?
	var hasMoreData = false;

	// Infinite page scrolling
	var iScroll;

	var baseUrl = 'admin/jigsaw-daily/organize/';

	// Current pack id we are working on
	var packId = -1;

	// Pack preview object
	var packPreview = null;

	// External pop-up window
	var popup = null;

	var photoHtml =     '<div id="photo_{id}" class="photo">' +
						'    <div class="inner">' +
						'        <a href="" onclick="organizer.addToPack({id}); return false;" class="btn btn-primary"><i class="fa-plus"></i> Add to Pack</a><br>' +
						'    </div>' +
						'    <img src="{img}">' +
						'    {name}' +
						'</div>';

	var init = function()
	{
		// Category filter
		categoryFilter.on('change', function()
		{
			searchFilter.val('');
			loadCategory($(this).val());
		});

		// Subcategory filter
		subcategoryFilter.on('change', function()
		{
			searchFilter.val('');
			loadPhotos();
		});
		subcategoryFilter.hide();

		// Search bar
		searchFilter.keydown(function(e)
		{
			if(e.which == 13)
			{
				loadPhotos();
				return false;
			}
		});

		// Search button
		$('#searchBtn').on('click', function()
		{
			loadPhotos();
			return false;
		});

		// Lazy load data
		iScroll = new InfiniteScroll(loadingText);
		iScroll.onLoadMore = function()
		{
			if(hasMoreData)
			{
				loadPhotos(true);
			}
		};

		// Load initial jigsaws
		loadPhotos();

		// Setup internal pack preview
		packPreview = new PackPreview(_this);

		// Close the pop-up when page location changes
		window.onunload = function()
		{
			closePopup();
		};
	};


	/**
	 * ------------------------------------------------------------------------
	 * Loads a specific jigsaw category
	 *
	 * @param categoryId
	 * ------------------------------------------------------------------------
	 */
	var loadCategory = function(categoryId)
	{
		subcategoryFilter.hide();

		// --------------------------
		// Load the sub-category select box
		// --------------------------
		if(categoryId != -1)
		{
			var url = baseUrl + 'category/' + categoryId + '/subcategories/';
			$.get(url, function(data)
			{
				subcategoryFilter.empty();
				subcategoryFilter.append('<option value="-1">All</option>');

				for(var i = 0; i < data.length; i++)
				{
					var subcat = data[i];
					subcategoryFilter.append('<option value="' + subcat.id + '">' + subcat.name + '</option>');
				}

				subcategoryFilter.show();
			});
		}

		// Load the available jigsaws
		loadPhotos();

	};


	/**
	 * ------------------------------------------------------------------------
	 * Loads the current photos according to the set filters.
	 *
	 * @param lazy  Defaults to false, true if the data is being lazy loaded into the container.
	 * ------------------------------------------------------------------------
	 */
	var loadPhotos = function(lazy)
	{
		// Is the data being lazy loaded? Defaults to "false"
		lazy = lazy == undefined ? false : lazy;

		// How many data elements to load at a single time
		var limit = 50;

		// Filter parameters
		var categoryId = categoryFilter.val() == -1 ? -1 : categoryFilter.val();
		var subcategoryId = subcategoryFilter.is(':visible') && subcategoryFilter.val() != -1 ? subcategoryFilter.val() : -1;
		var search = searchFilter.val();

		// Lazy load the data
		if(lazy)
		{
			offset += limit;
		}
		// Load the initial set of data
		else
		{
			offset = 0;
		}

		var url = baseUrl + 'photos/';
		var params = {categoryId: categoryId, subcategoryId: subcategoryId, search: search, offset: offset, limit: limit};

		// Clear out the existing jigsaws
		if(!lazy)
		{
			photosContainer.empty();
		}

		// Display the loading text while waiting for the data
		loadingText.show();

		// We'll assume that we don't have any more data left to show
		hasMoreData = false;
		iScroll.disable();

		$.post(url, params, function(data)
		{
			loadingText.hide();

			for(var i = 0; i < data.length; i++)
			{
				var photo = data[i];
				var html = '';

				// We actually do still have data left to show
				if(i == limit)
				{
					hasMoreData = true;
					loadingText.show();
					iScroll.enable();
				}
				// Photo
				else
				{
					html = photoHtml;
					html = html.replace(/{id}/g, photo.id);
					html = html.replace(/{img}/g, photo.img);
					html = html.replace(/{name}/g, photo.name);
				}

				photosContainer.append(html);
			}

		});
	};


	/**
	 * ------------------------------------------------------------------------
	 * Adds a photo to the current pack.
	 *
	 * @param photoId   Photo id to add
	 * ------------------------------------------------------------------------
	 */
	this.addToPack = function(photoId)
	{
		var elem = $('#photo_' + photoId);
		if(elem)
		{
			// No pack selected
			if(packId == -1)
			{
				alertify.alert('Please select a pack before adding new photos.');
				return;
			}

			// Hide the image
			elem.hide();

			// Send the data
			var url = baseUrl + 'add';
			var params = {packId: packId, photoId: photoId};

			$.post(url, params, function(data)
			{
				_this.setPack(packId);

			}).fail(function()
			{
				alertify.alert('Unable to add image to pack, please try again later.');
				elem.show();
			});
		}
	};


	/**
	 * ------------------------------------------------------------------------
	 * Removes a photo from the current pack.
	 *
	 * @param photoId   Photo id to remove
	 * ------------------------------------------------------------------------
	 */
	this.removeFromPack = function(photoId)
	{
		var url = baseUrl + 'remove';
		var params = {packId: packId, photoId: photoId};

		$.post(url, params, function(data)
		{
			_this.setPack(packId);
			var photoElement = $('#photo_' + photoId);

			// If the photo exists on the page, just show it again
			if(photoElement)
			{
				photoElement.show();
			}
			// Otherwise reload the list of jigsaws in case it may show up there
			else
			{
				loadPhotos();
			}

		}).fail(function()
		{
			alertify.alert('Unable to remove image from pack, please try again later.');
		});
	};


	/**
	 * ------------------------------------------------------------------------
	 * Sets the pack cover image for the current pack.
	 *
	 * @param photoId   Photo id of the cover image
	 * ------------------------------------------------------------------------
	 */
	this.setPackCover = function(photoId)
	{
		var url = baseUrl + 'set-cover';
		var params = {packId: packId, photoId: photoId};

		$.post(url, params, function(data)
		{
			_this.callPackPreview('renderPackCover', [photoId]);

		}).fail(function()
		{
			alertify.alert('Unable to set pack cover, please try again later.');
		});

	};


	/**
	 * ------------------------------------------------------------------------
	 * Sets an active pack id
	 *
	 * @param id    Pack id
	 * ------------------------------------------------------------------------
	 */
	this.setPack = function(id)
	{
		packId = id;

		// Load the pack data
		var url = baseUrl + 'pack/' + id;
		$.get(url, function(data)
		{
			_this.callPackPreview('renderPackData', [data]);

		}).fail(function()
		{
			alertify.alert('Unable to load pack, please try again later.');
		});

	};


	/**
	 * ------------------------------------------------------------------------
	 * Redirects the page to edit the current pack.
	 * ------------------------------------------------------------------------
	 */
	this.editPack = function()
	{
		if(packId == -1)
		{
			return;
		}

		closePopup();
		document.location.href = 'admin/jigsaw-daily/packs/' + packId + '/edit';
	};


	/**
	 * ------------------------------------------------------------------------
	 * Redirects the page to create a new pack.
	 * ------------------------------------------------------------------------
	 */
	this.newPack = function()
	{
		closePopup();
		document.location.href = 'admin/jigsaw-daily/packs/create';
	};


	/**
	 * ------------------------------------------------------------------------
	 * Returns the current pack id, or -1.
	 *
	 * @returns {number}
	 * ------------------------------------------------------------------------
	 */
	this.getPackId = function()
	{
		return packId;
	};


	/**
	 * ------------------------------------------------------------------------
	 * Opens a new window with the pack preview elements.
	 * ------------------------------------------------------------------------
	 */
	this.breakOut = function()
	{
		packPreview.renderBreakOut(true);

		var url = baseUrl + 'popup/';
		if(packId != -1)
		{
			url += packId;
		}

		popup = window.open(url);
	};


	/**
	 * ------------------------------------------------------------------------
	 * Called when the pack preview window is closed.
	 * ------------------------------------------------------------------------
	 */
	this.breakIn = function()
	{
		popup = null;
		packPreview.renderBreakOut(false);
	};


	/**
	 * ------------------------------------------------------------------------
	 * Forces the pack preview window to be closed.
	 * ------------------------------------------------------------------------
	 */
	var closePopup = function()
	{
		if(popup)
		{
			popup.close();
		}
	};


	/**
	 * ------------------------------------------------------------------------
	 * Calls a function on both the internal and external pack preview objects.
	 *
	 * @param fn        Function name
	 * @param params    Function parameters
	 * ------------------------------------------------------------------------
	 */
	this.callPackPreview = function(fn, params)
	{
		// Internal pack preview
		packPreview[fn].apply(packPreview, params);

		// External pack preview
		if(popup != null && popup.packPreview)
		{
			popup.packPreview[fn].apply(popup.packPreview, params);
		}
	};


	// Initial setup
	init();
}


/**
 * The PackPreview class controls the rendering of items in a pack. This class always has an instance within the main
 * organizer page, and may also have a second instance if the preview panel is broken out into a new window.
 *
 * @param organizer     Reference to the main organizer object
 * @param external      Defaults to false, true if this class instance is in an external window
 *
 * @constructor
 */
function PackPreview(organizer, external)
{
	// Is the pack preview being viewed in an external frame?
	external = external == undefined ? false : external;

	var _this = this;

	// Reference to the pack preview element
	var $packPreview = $('#packPreview');

	// Reference to pack selection
	var $packSelect = $packPreview.find('select[name=pack]').first();

	// Reference to the pack preview section
	var $photos = $packPreview.find('.photos').first();

	// Edit button
	var $editBtn = $packPreview.find('.btn-edit').first();

	// New button
	var $newBtn = $packPreview.find('.btn-new').first();

	// Preview size (0 = collapsed)
	var previewSize = 0;

	// Size control buttons
	var $increaseSizeBtn = $packPreview.find('.increase-size').first();
	var $decreaseSizeBtn = $packPreview.find('.decrease-size').first();
	var $breakOutBtn = $packPreview.find('.break-out').first();

	var photoHtml =     '<div id="pack_photo_{id}" class="photo">' +
						'    <div class="ribbon ribbon-primary ribbon-badge"><span class="ribbon-inner">Cover</span></div>' +
						'    <div class="inner">' +
						'        <a href="" class="btn btn-danger btn-remove"><i class="fa-times"></i> Remove</a><br>' +
						'        <a href="" class="btn btn-primary btn-cover"><i class="fa-check"></i> Set as Cover</a><br>' +
						'    </div>' +
						'    <img src="{img}">' +
						'    {name}' +
						'</div>';


	/**
	 * ------------------------------------------------------------------------
	 * Initial pack preview setup.
	 * ------------------------------------------------------------------------
	 */
	var init = function()
	{
		// Disable first option
		$packSelect.find('option[value=-1]').first().attr('disabled', 'disabled');

		// Disable edit button
		$editBtn.hide();

		// Edit button clicked
		$editBtn.on('click', function()
		{
			organizer.editPack();
			return false;
		});

		// New button clicked
		$newBtn.on('click', function()
		{
			organizer.newPack();
			return false;
		});

		// Load pack preview
		$packSelect.on('change', function()
		{
			if($(this).val() != -1)
			{
				organizer.setPack($(this).val());
			}
		});

		// Load pack if one is pre-selected
		if($packSelect.val() != null)
		{
			organizer.setPack($packSelect.val());
		}

		// ---------------------
		// External window
		// ---------------------
		if(external)
		{
			$packPreview.addClass('external');

			if(organizer.getPackId() != -1)
			{
				organizer.setPack(organizer.getPackId());
			}
		}
		// ---------------------
		// Internal window
		// ---------------------
		else
		{
			// Increase size
			$increaseSizeBtn.on('click', increasePreviewSize);

			// Decrease size
			$decreaseSizeBtn.on('click', decreasePreviewSize);

			// Break out
			$breakOutBtn.on('click', organizer.breakOut);

			// Update initial size
			updatePreviewSize();

			// Update panel size
			updatePanelSize();

			$(window).resize(function()
			{
				updatePanelSize();
			});
		}
	};


	/**
	 * ------------------------------------------------------------------------
	 * Increases the preview height.
	 * ------------------------------------------------------------------------
	 */
	var increasePreviewSize = function()
	{
		previewSize++;
		updatePreviewSize();
	};


	/**
	 * ------------------------------------------------------------------------
	 * Decreases the preview height.
	 * ------------------------------------------------------------------------
	 */
	var decreasePreviewSize = function()
	{
		previewSize--;
		updatePreviewSize();
	};


	/**
	 * ------------------------------------------------------------------------
	 * Updates the preview height and button displays.
	 * ------------------------------------------------------------------------
	 */
	var updatePreviewSize = function()
	{
		// Max/min sizes
		previewSize = Math.max(0, previewSize);
		previewSize = Math.min(2, previewSize);

		if(previewSize == 0)
		{
			$photos.hide();
		}
		else
		{
			$photos.show();
			$photos.css('height', 230 * previewSize);
		}

		// Make sure we can always scroll
		$('#mainPanel').css({'margin-bottom': (previewSize * 230) + 20});

		$increaseSizeBtn.show();
		$decreaseSizeBtn.show();

		// No minimizing
		if(previewSize == 0)
		{
			$decreaseSizeBtn.hide();
		}
		// No maximizing
		else if(previewSize == 2)
		{
			$increaseSizeBtn.hide();
		}

	};


	/**
	 * ------------------------------------------------------------------------
	 * Renders the pack data returned from the server.
	 *
	 * @param data
	 * ------------------------------------------------------------------------
	 */
	this.renderPackData = function(data)
	{
		// Select the correct drop down
		$packSelect.val(data.id);

		// Set the correct number of photos in the pack
		var selectedOption = $packSelect.find(':selected').text(data.name + ' (' + data.photos.length + ')');

		// Display the edit button
		$editBtn.show();

		// Clear existing pack preview
		$photos.empty();

		// Add pack previews
		for(var i = 0; i < data.photos.length; i++)
		{
			_this.renderPhoto(data.photos[i]);
		}
	};


	/**
	 * ------------------------------------------------------------------------
	 * Renders a specific photo in the pack preview.
	 *
	 * @param photo
	 * ------------------------------------------------------------------------
	 */
	this.renderPhoto = function(photo)
	{
		html = photoHtml;
		html = html.replace(/{id}/g, photo.id);
		html = html.replace(/{name}/g, photo.name);
		html = html.replace(/{img}/g, photo.img);

		var $htmlElem = $(html);

		// Setup remove button
		$htmlElem.find('.btn-remove').first().on('click', function()
		{
			organizer.removeFromPack(photo.id);
			return false;
		});

		// Setup pack cover button
		$htmlElem.find('.btn-cover').first().on('click', function()
		{
			organizer.setPackCover(photo.id);
			return false;
		});

		$photos.append($htmlElem);

		// Is this the pack cover?
		if(photo.cover)
		{
			$htmlElem.find('.ribbon').show();
			$htmlElem.find('.btn-cover').hide();
		}
	};


	/**
	 * ------------------------------------------------------------------------
	 * Renders the pack cover ribbon on a selected photo.
	 *
	 * @param photoId   Photo id of the pack cover image
	 * ------------------------------------------------------------------------
	 */
	this.renderPackCover = function(photoId)
	{
		$photos.find('.ribbon').hide();
		$photos.find('.btn-cover').show();

		$('#pack_photo_' + photoId + ' .ribbon').show();
		$('#pack_photo_' + photoId + ' .btn-cover').hide();
	};


	/**
	 * ------------------------------------------------------------------------
	 * Called when the parent frame width is adjusted.
	 * ------------------------------------------------------------------------
	 */
	var updatePanelSize = function()
	{
		$('#packPreview').width($('#mainPanel').width());
	};


	/**
	 * ------------------------------------------------------------------------
	 * Renders the pack preview to handle break out / break in conditions.
	 *
	 * @param breakOut  True if the pack preview has been broken out into a new window.
	 * ------------------------------------------------------------------------
	 */
	this.renderBreakOut = function(breakOut)
	{
		if(breakOut)
		{
			$packPreview.hide();
		}
		else
		{
			$packPreview.show();
		}
	};


	// Initial setup
	init();
}