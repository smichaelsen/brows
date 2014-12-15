<?php
namespace Smichaelsen\Brows\Filesystem;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Smichaelsen\Brows\Domain\Model\DirectoryItem;

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
	 * @param DirectoryItem $item
	 * @param int $width
	 * @param int $height
	 *
	 * @return string
	 */
	public function publish(DirectoryItem $item, $width = NULL, $height = NULL) {
		$targetFilename = $this->getTargetFilename($item, $width, $height);
		if (!$this->isPublished($item, $width, $height)) {
			$image = $this->imageConverter->open($item->getAbsolutePath());
			$this->processImage($image, $width, $height);
			$this->ensureFolderByPath($this->publicDirectoryMount->getRootPath() . $targetFilename);
			$image->save($this->publicDirectoryMount->getRootPath() . $targetFilename);
		}
		return $this->publicDirectoryMount->getAbsolutePublicPath() . $targetFilename;
	}

	/**
	 * @param DirectoryItem $item
	 * @param int $width
	 * @param int $height
	 * @return string
	 */
	public function publicUrl(DirectoryItem $item, $width = NULL, $height = NULL) {
		$targetFilename = $this->getTargetFilename($item, $width, $height);
		return $this->publicDirectoryMount->getAbsolutePublicPath() . $targetFilename;
	}

	/**
	 * @param DirectoryItem $item
	 * @param int $width
	 * @param int $height
	 * @return bool
	 */
	public function isPublished(DirectoryItem $item, $width = NULL, $height = NULL) {
		$targetFilename = $this->getTargetFilename($item, $width, $height);
		return file_exists($this->publicDirectoryMount->getRootPath() . $targetFilename);
	}

	/**
	 * @param string $input
	 * @param int $len
	 *
	 * @return string
	 */
	protected function hash($input, $len = 12) {
		$hash = substr(md5($input), 0, $len);
		$hash{2} = '/';
		return $hash;
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

	/**
	 * @param string $targetFilepath
	 */
	protected function ensureFolderByPath($targetFilepath) {
		$pathParts = explode('/', $targetFilepath);
		array_pop($pathParts);
		$targetDirectory = join('/', $pathParts);
		if (!is_dir($targetDirectory)) {
			mkdir($targetDirectory, 0777, TRUE);
		}
	}

	/**
	 * @param \Smichaelsen\Brows\Domain\Model\DirectoryItem $item
	 * @param $width
	 * @param $height
	 *
	 * @return string
	 */
	protected function getTargetFilename(DirectoryItem $item, $width, $height) {
		$hashIngredients = [
			$item->getItemPath(),
			$width,
			$height,
		];
		$hash = $this->hash(serialize($hashIngredients));
		$targetFilename = $hash . '.' . strtolower($item->getFileExtension());

		return $targetFilename;
	}

}
