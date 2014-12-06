<?php
namespace Smichaelsen\Brows\Filesystem;

use AppZap\PHPFramework\Configuration\Configuration;
use Smichaelsen\Brows\Domain\Model\LocalDirectoryItem;

class ImagePublisher {

	/**
	 * @var \Imagine\Gd\Imagine;
	 */
	protected $imageConverter;

	public function __construct() {
		$this->imageConverter = new \Imagine\Gd\Imagine();
	}

	/**
	 * @param LocalDirectoryItem $item
	 * @param $width
	 * @param $height
	 * @return string
	 */
	public function publish(LocalDirectoryItem $item, $width, $height) {
		$hashIngredients = [
			$item->getItemPath(),
			$width,
			$height,
		];
		$hash = $this->hash(serialize($hashIngredients));
		$targetFilename = $hash . '.' . $item->getFileExtension();
		$size    = new \Imagine\Image\Box($width, $height);
		$this->imageConverter->open($item->getAbsolutePath())->thumbnail($size)->save(Configuration::get('phpframework', 'project_root') . 'assets/' . $targetFilename);
		return 'assets/' . $targetFilename;
	}

	/**
	 * @param string $input
	 * @param int $len
	 *
	 * @return string
	 */
	protected function hash($input, $len = 10) {
		return substr(md5($input),0,$len);
	}

}