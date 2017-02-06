/**
 * The InfiniteScroll class makes it easy to add infinite scrolling functionality to a page.
 *
 * Set up the class to watch for a specific element on the page to come into view. Typically this will be "loading"
 * text that is displayed on the very bottom of the page. When this element comes into view minus a preload distance,
 * the "onLoadMore" callback will be fired.
 *
 * Make sure to enable the class view the enable() method. You'll also need to call the enable() method each time
 * the "onLoadMore" callback is fired, indicating that you are ready to load more content again.
 *
 * @param element           Element to watch for loading to begin
 * @param preloadDistance   Optional, default is 500
 *
 * @constructor
 */

function InfiniteScroll(element, preloadDistance)
{
	// Preload distance
	preloadDistance = preloadDistance == undefined ? 500 : preloadDistance;

	// Callback
	this.onLoadMore = null;

	var $window = $(window);
	var _this = this;
	var active = false;

	var init = function()
	{
		$window.on('scroll', function(e)
		{
			_this.update();
		});

	};


	this.update = function()
	{
		if(!active)
		{
			return;
		}

		var windowBottom = $window.scrollTop() + $window.height();
		var elementTop = element.offset().top;
		var distance = elementTop - windowBottom - preloadDistance;

		if(distance <= 0 && this.onLoadMore != null)
		{
			active = false;
			this.onLoadMore();
		}
	};


	/**
	 * Call this method to enable watching the element. You will need to call this method each time the
	 * "onLoadMore" callback is fired, indicating that you are ready to load more content.
	 */
	this.enable = function()
	{
		active = true;
		this.update();
	};


	/**
	 * Call this method to disable watching the element, indicating that you do not wish to load any more
	 * content at this time.
	 */
	this.disable = function()
	{
		active = false;
	};


	init();
}