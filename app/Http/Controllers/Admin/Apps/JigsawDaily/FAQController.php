<?php

namespace App\Http\Controllers\Admin\Apps\JigsawDaily;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FAQController extends Controller
{

	public function faq($version, $platform, $device)
	{
		$faqs = [];

		//$faqs[] = ['alert' => 'This is an important message everyone needs to read'];

		// ---------------------
		// General Questions
		// ---------------------
		$faqs[] = ['h' => 'General Questions'];

		$faqs[] = [
			'q' => 'Is Jigsaw Daily free?',
			'a' => 'We offer a free daily puzzle and several free packs pre-installed with the app. Additional packs can be purchased in the "Store" section.'
		];

		$faqs[] = [
			'q' => 'How do I play the free daily puzzle?',
			'a' => 'The free daily puzzle is displayed on the "Puzzles" page when you first open up the app. {{img1}}',
			'images' => [
				'img1' => $this->imageUrl('daily-puzzle.jpg')
			]
		];

		$faqs[] = [
			'q' => 'How do I upload a custom photo?',
			'a' => 'We do not offer the ability to upload custom photos at this time.'
		];

		$faqs[] = [
			'q' => 'How do I remove the completed puzzle checkmarks?',
			'a' => 'You can delete the pack and redownload it to your device to clear all the completed checkmarks.'
		];

		$faqs[] = [
			'q' => 'What if I still have more questions?',
			'a' => 'You can contact us using the <a href="as:url@contact">contact form</a> or email us at support@jigsawdaily.com.'
		];


		// ---------------------
		// Adding and Removing Packs
		// ---------------------
		$faqs[] = ['h' => 'Adding and Removing Packs'];


		$faqs[] = [
			'q' => 'How to I transfer my purchases to a different device?',
			'a' => 'You can restore your purchases made on different device in the game "Options" menu. Scroll down to the "Restore Previous Purchases" section and tap it.'
		];

		$faqs[] = [
			'q' => 'How do I get more jigsaw puzzles?',
			'a' => 'We offer a free daily puzzle and several free packs pre-installed with the app. Additional packs can be purchased in the "Store" section.'
		];

		$faqs[] = [
			'q' => 'How do I delete packs?',
			'a' => 'Tap on the pack and scroll all the way to the bottom of the page. Tap the "Remove Pack" button to delete the pack from your device. {{img1}}',
			'images' => [
				'img1' => $this->imageUrl('remove-pack.jpg')
			]
		];

		$faqs[] = [
			'q' => 'How do I redownload deleted packs?',
			'a' => 'Deleted packs appear at the bottom of the "Packs" section and are grayed out. Tap on a deleted pack to restore it to your device. {{img1}}',
			'images' => [
				'img1' => $this->imageUrl('restore.jpg')
			]
		];



		// ---------------------
		// Game Questions
		// ---------------------
		$faqs[] = ['h' => 'Game Questions'];

		$faqs[] = [
			'q' => 'How do I choose the number of pieces?',
			'a' => 'After you select a puzzle image to play, use the slider to determine how many pieces will be in the puzzle. {{img1}}',
			'images' =>
			[
				'img1' => $device == 'phone' ? $this->imageUrl('piece-slider-phone.jpg') : $this->imageUrl('piece-slider-tablet.jpg')
			]
		];

		$faqs[] = [
			'q' => 'How do I use the tray feature?',
			'a' => 'Scroll through the tray to find the puzzle piece that you are looking for. Then tap the piece and drag it out of the tray. {{img1}}',
			'images' => [
				'img1' => $this->imageUrl('tray.jpg')
			]
		];

		$faqs[] = [
			'q' => 'How do I turn off the tray?',
			'a' => 'You can turn off the tray in the options menu, located on the left side of the game. Locate the "tray" option and toggle the tray setting to "off".'
		];

		$faqs[] = [
			'q' => 'How do I rotate pieces?',
			'a' => 'The puzzle pieces are already in the correct orientation. You do not need to rotate them.'
		];

		$faqs[] = [
			'q' => 'What does the shuffle button do?',
			'a' => 'The shuffle button randomly scatters all of the unconnected pieces around the edge of the game. Note that this will not disconnect any currently connected pieces. {{img1}}',
			'images' => [
				'img1' => $this->imageUrl('shuffle.jpg')
			]
		];

		$faqs[] = [
			'q' => 'What does the edges button do?',
			'a' => 'The edges button hides all non-edge pieces. This can make it easier to get started when playing larger cuts. {{img1}}',
			'images' => [
				'img1' => $this->imageUrl('edges.jpg')
			]
		];

		$faqs[] = [
			'q' => 'How do I find a missing piece?',
			'a' => 'Sometimes a piece can get hidden underneath other pieces or blend into an area of the puzzle that is already completed. Try tapping on the "shuffle" button on the left side of the game to move the pieces around. {{img1}}',
			'images' => [
				'img1' => $this->imageUrl('shuffle.jpg')
			]
		];

		$faqs[] = [
			'q' => 'How can I preview the puzzle image while playing?',
			'a' => 'Tap on the "preview" button located on the left side of the game. This will bring up a thumbnail image of the current puzzle you are working on. {{img1}}',
			'images' => [
				'img1' => $this->imageUrl('preview.jpg')
			]
		];



		// ---------------------
		// Game Options
		// ---------------------
		$faqs[] = ['h' => 'Game Options'];

		$faqs[] = [
			'q' => 'How do I customize the game?',
			'a' => 'You can customize the background color, piece size and other settings in the options menu. The options menu is located on the left side of the game. {{img1}}',
			'images' => [
				'img1' => $this->imageUrl('options.jpg')
			]
		];

		$faqs[] = [
			'q' => 'How do I change the background color?',
			'a' => 'You can change the background color in the game options menu, located on the left side of the game. Scroll down and find the "Background" section.'
		];

		$faqs[] = [
			'q' => 'How do I turn off the sounds?',
			'a' => 'You can disable all sounds in the game options menu, located on the left side of the game. Toggle off the "sounds" setting.'
		];

		$faqs[] = [
			'q' => 'What is the ghost image option?',
			'a' => 'This option places a semi-transparent preview image underneath the puzzle. Pieces placed in the correct position will lock into place. {{img1}}',
			'images' => [
				'img1' => $this->imageUrl('ghost.jpg')
			]
		];

		$faqs[] = [
			'q' => 'What is the visible border option?',
			'a' => 'This option places a borderline underneath the puzzle. Pieces placed into the correct position will lock into place. {{img1}}',
			'images' => [
				'img1' => $this->imageUrl('border.jpg')
			]
		];

		$faqs[] = [
			'q' => 'How can I make the puzzle pieces smaller?',
			'a' => 'In the game options menu, toggle the "larger pieces" setting to "off".'
		];


		// ---------------------
		// In-Progress Puzzles
		// ---------------------
		$faqs[] = ['h' => 'In-Progress Puzzles'];

		$faqs[] = [
			'q' => 'How do I save a puzzle to complete later?',
			'a' => 'Puzzles are automatically saved as soon as you start to play. If you leave the game in the middle of playing, you can return to your in-progress game at any time.'
		];

		$faqs[] = [
			'q' => 'How do I resume an in-progress puzzle?',
			'a' => 'Tap on the "In-Progress" button on the main section of the app, then tap on the puzzle that you wish to resume. {{img1}}',
			'images' => [
				'img1' => $this->imageUrl('in-progress.jpg')
			]
		];

		$faqs[] = [
			'q' => 'How do I delete an in-progress puzzle I no longer want to play?',
			'a' => 'Open up the in-progress puzzles section and tap on the "X" icon located in the top right corner of the puzzle.'
		];

		$faqs[] = [
			'q' => 'Why did my in-progress puzzle disapper?',
			'a' => 'Puzzles are removed from the in-progress section once they are completed.'
		];

		return ['faqs' => $faqs];
	}


	private function imageUrl($path)
	{
		return config('maxcdn.cdn.url') . '/apps/jigsaw-daily/images/faq/' . $path;
	}

}
