<?php
namespace Smichaelsen\Brows\Filesystem;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Smichaelsen\Brows\Domain\Model\DirectoryItem;

class ImagePublisher extends AbstractFilePublisher {

  /**
   * @var \Imagine\Image\AbstractImagine;
   */
  protected $imageConverter;

  public function __construct() {
    parent::__construct();
    if (class_exists('Imagick')) {
      $this->imageConverter = new \Imagine\Imagick\Imagine();
    } elseif (class_exists('Gmagick')) {
      $this->imageConverter = new \Imagine\Gmagick\Imagine();
    } else {
      $this->imageConverter = new \Imagine\Gd\Imagine();
    }
  }

  /**
   * @param DirectoryItem $item
   * @param $width
   * @param $height
   *
   * @return string
   */
  public function publish(DirectoryItem $item, $width = NULL, $height = NULL) {
    $hashIngredients = [
      $item->getItemPath(),
      $width,
      $height,
    ];
    $hash = $this->hash(serialize($hashIngredients));
    $targetFilename = $hash . '.' . strtolower($item->getFileExtension());
    if (!file_exists($this->publicDirectoryMount->getRootPath() . $targetFilename)) {
      $image = $this->imageConverter->open($item->getAbsolutePath());
      $this->processImage($image, $width, $height);
      $this->ensureFolderByPath($this->publicDirectoryMount->getRootPath() . $targetFilename);
      $image->save($this->publicDirectoryMount->getRootPath() . $targetFilename);
    }
    return $this->publicDirectoryMount->getAbsolutePublicPath() . $targetFilename;
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
      $scale = max($width / $originalWidth, $height / $originalHeight);
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
      $image->crop(new Point($cropWidth / 2, $cropHeight / 2), new Box($width, $height));
    } else {
      // proportional scale
      // TODO
    }
  }

}
