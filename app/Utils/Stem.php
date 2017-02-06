<?php

namespace App\Utils;

use App\Utils\Porter;

class Stem
{

	/**
	 * Given a string, returns an array of stemmed keywords
	 *
	 * @param $string
	 * @return array
	 */
	public static function getStemmedString($string) : array
	{
		$string = preg_replace("/[^A-Za-z ]/", '', $string);
		$string = explode(' ', $string);

		return self::getStemmedKeywords($string);
	}


	/**
	 * Given an array of keywords, returns an array of stemmed keywords.
	 *
	 * @param array $keywords
	 */
	public static function getStemmedKeywords(array $keywords) : array
	{
		$ignoredWords = explode(' ', 'and the');
		$stemmed = [];

		foreach($keywords as $word)
		{
			$word = strtolower(trim($word));

			if(strlen($word) < 3 || array_search($word, $ignoredWords) !== false)
			{
				continue;
			}

			$word = Porter::Stem($word);

			if(array_search($word, $stemmed) === false)
			{
				$stemmed[] = $word;
			}
		}

		return $stemmed;
	}


}