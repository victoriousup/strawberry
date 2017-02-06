<?php

namespace App\Http\ViewComposers\Admin\Layout;

use Auth;

class LeftNav
{
	protected $items = [];
	protected $_uri;

	function __construct($uri)
	{
		$this->_uri = $uri;
		$this->init();
	}

	protected function init()
	{
		// Override this!
	}


	public function html()
	{
		$this->_getSelectedItems();
		return $this->_renderItems($this->items, true);
	}


	private function _renderItems($items, $topLevel = false)
	{
		if(count($items) == 0)
		{
			return '';
		}

		$itemHtml = '<li class="site-menu-item {has_sub} {active} {open}">' .
					'  <a class="{url_class}" href="{url}">' .
					'    {icon}' .
					'    <span class="site-menu-title">{name}</span>' .
					'    {badge}' .
					'  </a>' .
					'  {sub_items}' .
					'</li>';

		$html = $topLevel ? '<ul class="site-menu">' : '<ul class="site-menu-sub">';

		foreach($items as $item)
		{
			if($item->permission != null && !Auth::user()->can($item->permission))
			{
				continue;
			}

			$tmpHtml = str_replace('{has_sub}', count($item->items) > 0 ? 'has-sub' : '', $itemHtml);
			$tmpHtml = str_replace('{url}', count($item->items) > 0 ? 'javascript:void(0)' : $item->url, $tmpHtml);
			$tmpHtml = str_replace('{url_class}', $topLevel ? '' : 'animsition-link', $tmpHtml);
			$tmpHtml = str_replace('{name}', $item->name, $tmpHtml);
			$tmpHtml = str_replace('{icon}', $item->getIconHtml(), $tmpHtml);
			$tmpHtml = str_replace('{badge}', count($item->items) > 0 && $item->badge == '' ? '<span class="site-menu-arrow"></span>' : $item->badge, $tmpHtml);
			$tmpHtml = str_replace('{active}', $item->selected ? 'active' : '', $tmpHtml);
			$tmpHtml = str_replace('{open}', count($item->items) > 0 && $item->selected ? 'open' : '', $tmpHtml);
			$tmpHtml = str_replace('{sub_items}', $this->_renderItems($item->items), $tmpHtml);

			$html .= $tmpHtml;
		}

		$html .= '</ul>';

		return $html;
	}


	private function _getSelectedItems()
	{
		$exactMatch = null;
		$partialMatches = [];

		$this->_checkSelectedItems($this->items, $exactMatch, $partialMatches);

		// No exact match
		if($exactMatch == null)
		{
			$urlLength = -1;

			foreach($partialMatches as $match)
			{
				// Calculate the url length of the last element
				$newUrlLength = strlen($match[count($match) - 1]->url);

				if($newUrlLength > $urlLength)
				{
					$exactMatch = $match;
				}
			}
		}

		// We have selected elements
		if($exactMatch != null)
		{
			foreach($exactMatch as $item)
			{
				$item->selected = true;
			}
		}
	}


	private function _checkSelectedItems($items, &$exactMatch = null, &$partialMatches = [], $stack = [])
	{
		foreach($items as $item)
		{
			// Stop once we've found an exact match
			if($exactMatch != null)
			{
				return;
			}

			$tmpStack = $stack;
			$tmpStack[] = $item;

			$compare = $this->_urlContainsUrl($this->_uri, $item->url);

			// Exact match
			if($compare == 1)
			{
				$exactMatch = $tmpStack;
				return;
			}

			// Partial match
			if($compare == 0)
			{
				$partialMatches[] = $tmpStack;
			}

			// Check sub-items
			$this->_checkSelectedItems($item->items, $exactMatch, $partialMatches, $tmpStack);
		}
	}


	/**
	 * Compare two urls to see if the one contains the other. Returns an integer
	 * depending on how they compare:
	 *
	 * -1 = No match
	 * 0 = Sub-match
	 * 1 = Exact match
	 *
	 * For example given the mainUrl "admin/users/" for the following compare urls:
	 *
	 * "profile/" = -1 (no match)
	 * "admin/" = 0 (sub match)
	 * "admin/users/" = 1 (exact match)
	 *
	 *
	 * @param $mainUrl
	 * @param $compareUrl
	 *
	 * @return int
	 */
	private function _urlContainsUrl($mainUrl, $compareUrl)
	{
		$mainUrl = $this->_normalizeUrl($mainUrl);
		$compareUrl = $this->_normalizeUrl($compareUrl);

		// Exact match
		if($mainUrl == $compareUrl)
		{
			return 1;
		}

		// No match
		if($compareUrl == '' || stripos($mainUrl, $compareUrl) === FALSE)
		{
			return -1;
		}

		// Sub-match
		return 0;
	}


	/**
	 * Normalize two urls for comparison
	 *
	 * @param $url
	 * @return string
	 */
	private function _normalizeUrl($url)
	{
		if($url == '')
		{
			return;
		}

		$url = strtolower($url);
		if($url[strlen($url) - 1] != '/')
		{
			$url .= '/';
		}

		return $url;
	}
}