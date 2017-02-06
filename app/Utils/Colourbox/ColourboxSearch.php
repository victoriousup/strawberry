<?php

namespace App\Utils\Colourbox;

class ColourboxSearch
{
	const MEDIA_TYPE_IMAGE = 'image';
	const MEDIA_TYPE_VECTOR = 'vector';
	const MEDIA_TYPE_VIDEO = 'video';

	const ORIENTATION_HORIZONTAL = 'horizontal';
	const ORIENTATION_VERTICAL = 'vertical';

	const ORDER_RELEVANCE = 'relevance';
	const ORDER_CREATED = 'created';

	const DIRECTION_ASC = 'asc';
	const DIRECTION_DESC = 'desc';

	public $term = '';

	public $exclude = '';

	public $results = 20;

	public $offset = null;

	public $mediaTypes = [];

	public $orientation = null;

	// Values are 110, 200, 320, 480, 800, 1200, 1600
	public $thumbnailSize = 320;

	public $order = null;

	public $direction = null;

	public function getQuery()
	{
		$query = [];

		// Search
		$query['q'] = str_replace(' ', '+', $this->term);
		if($this->exclude != '')
		{
			$query['q'] .= '-' . str_replace(' ', '-', $this->exclude);
		}

		// Results
		$query['media_count'] = $this->results;

		// Offset
		if($this->offset != null)
		{
			$query['media_offset'] = $this->offset;
		}

		// Media type
		if(count($this->mediaTypes) > 0)
		{
			$query['media_type'] = implode('+', $this->mediaTypes);
		}

		// Orientation
		if($this->orientation != null)
		{
			$query['orientation'] = $this->orientation;
		}

		// Thumbnail size
		$query['thumbnail_size'] = $this->thumbnailSize . 'px';

		// Search order
		if($this->order != null)
		{
			$query['order'] = $this->order;
		}

		// Search direction
		if($this->direction)
		{
			$query['direction'] = $this->direction;
		}

		return http_build_query($query);
	}
}