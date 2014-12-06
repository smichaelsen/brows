<?php
namespace Smichaelsen\Brows\Filesystem;

use Smichaelsen\Brows\Domain\Model\LocalDirectoryItem;

class ImagePublisher {

	/**
	 * @var \Imagine\Gd\Imagine;
	 */
	protected $imageConverter;

	/**
	 * @var LocalDirectoryMount
	 */
	protected $publicDirectoryMount;

	public function __construct() {
		$this->imageConverter = new \Imagine\Gd\Imagine();
		$this->publicDirectoryMount = new LocalDirectoryMount();
		$this->publicDirectoryMount->setRootPath('assets/');
		$this->publicDirectoryMount->setPublicPath('assets/');
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
		if (!file_exists($this->publicDirectoryMount->getRootPath() . $targetFilename)) {
			$size    = new \Imagine\Image\Box($width, $height);
			$this->imageConverter->open($item->getAbsolutePath())->thumbnail($size)->save($this->publicDirectoryMount->getRootPath() . $targetFilename);
		}
		return $this->publicDirectoryMount->getAbsolutePublicPath() . $targetFilename;
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