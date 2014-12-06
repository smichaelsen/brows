<?php
namespace Smichaelsen\Brows\Filesystem;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
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
		$this->publicDirectoryMount->setRootPath('assets/images/');
		$this->publicDirectoryMount->setPublicPath('assets/images/');
	}

	/**
	 * @param LocalDirectoryItem $item
	 * @param $width
	 * @param $height
	 * @return string
	 */
	public function publish(LocalDirectoryItem $item, $width = NULL, $height = NULL) {
		$hashIngredients = [
			$item->getItemPath(),
			$width,
			$height,
		];
		$hash = $this->hash(serialize($hashIngredients));
		$targetFilename = $hash . '.' . $item->getFileExtension();
		if (!file_exists($this->publicDirectoryMount->getRootPath() . $targetFilename)) {
			$image = $this->imageConverter->open($item->getAbsolutePath());
			$this->processImage($image, $width, $height);
			$image->save($this->publicDirectoryMount->getRootPath() . $targetFilename);
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

	/**
	 * @param \Imagine\Image\ImageInterface $image
	 * @param int $width
	 * @param int $height
	 */
	protected function processImage(ImageInterface $image, $width = NULL, $height = NULL) {
		if ($width === NULL && $height === NULL) {
			// preserve original size
			return;
		}
		$originalSize = $image->getSize();
		$originalHeight = $originalSize->getHeight();
		$originalWidth = $originalSize->getWidth();
		if ($width !== NULL && $height !== NULL) {
			// crop scale
			$scale = max($width/$originalWidth, $height/$originalHeight);
			$scaledWidth = $originalWidth * $scale;
			$scaledHeight = $originalHeight * $scale;
			if ($scale !== 1) {
				$image->resize(new Box($scaledWidth, $scaledHeight));
			}
			$cropHeight = $cropWidth = 0;
			if ($scaledHeight > $height) {
				$cropHeight = $scaledHeight - $height;
			}
			if ($scaledWidth > $width) {
				$cropWidth = $scaledWidth - $width;
			}
			$image->crop(new Point($cropWidth / 2, $cropHeight /2), new Box($width, $height));
		} else {
			// proportional scale
			// TODO
		}
	}

}