<?php
namespace Smichaelsen\Brows\Domain\Model;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Domain\Model\AbstractModel;
use Smichaelsen\Brows\Filesystem\LocalDirectoryMount;
use Smichaelsen\Brows\Utility\FileExtensionUtility;

class DirectoryItem extends AbstractModel {

  /**
   * @var array
   */
  protected $exifData;

  /**
   * @var string
   */
  protected $fileExtension;

  /**
   * @var int
   */
  protected $includedImages;

  /**
   * @var int
   */
  protected $includedVideos;

  /**
   * @var string
   */
  protected $itemPath;

  /**
   * @var LocalDirectoryMount
   */
  protected $mount;

  /**
   * @var string
   */
  protected $label;

  /**
   * @var DirectoryItem
   */
  protected $titleImage;

  /**
   * @return array
   */
  public function getExifData() {
    if (!isset($this->exifData)) {
      $this->exifData = @exif_read_data($this->getAbsolutePath());
      if ($this->exifData === FALSE) {
        $size = getimagesize($this->getAbsolutePath());
        $this->exifData['ExifImageWidth'] = $size[0];
        $this->exifData['ExifImageLength'] = $size[1];
      }
      if (isset($this->exifData['GPSLatitude']) && is_array($this->exifData['GPSLatitude'])) {
        list($decimalNumerator, $decimalDenominator) = explode('/', $this->exifData['GPSLatitude'][0]);
        list($minutesNumerator, $minutesDenominator) = explode('/', $this->exifData['GPSLatitude'][1]);
        list($secondsNumerator, $secondsDenominator) = explode('/', $this->exifData['GPSLatitude'][2]);
        $this->exifData['GPSLatitudeDecimal'] = $this->coordinatesDsmToDecimal(
          $decimalNumerator / $decimalDenominator,
          $minutesNumerator / $minutesDenominator,
          $secondsNumerator / $secondsDenominator
        );
      }
      if (isset($this->exifData['GPSLongitude']) && is_array($this->exifData['GPSLongitude'])) {
        list($decimalNumerator, $decimalDenominator) = explode('/', $this->exifData['GPSLongitude'][0]);
        list($minutesNumerator, $minutesDenominator) = explode('/', $this->exifData['GPSLongitude'][1]);
        list($secondsNumerator, $secondsDenominator) = explode('/', $this->exifData['GPSLongitude'][2]);
        $this->exifData['GPSLongitudeDecimal'] = $this->coordinatesDsmToDecimal(
          $decimalNumerator / $decimalDenominator,
          $minutesNumerator / $minutesDenominator,
          $secondsNumerator / $secondsDenominator
        );
      }
    }
    return $this->exifData;
  }

  /**
   * @return string
   */
  public function getItemPath() {
    return $this->itemPath;
  }

  /**
   * @param string $itemPath
   */
  public function setItemPath($itemPath) {
    $this->itemPath = ltrim($itemPath, './');
  }

  /**
   * @return LocalDirectoryMount
   */
  public function getMount() {
    return $this->mount;
  }

  /**
   * @param LocalDirectoryMount $mount
   */
  public function setMount($mount) {
    $this->mount = $mount;
  }

  /**
   * @return string
   */
  public function getLabel() {
    $pathParts = explode('/', rtrim($this->itemPath, '/'));
    return $this->label ?: array_pop($pathParts);
  }

  /**
   * @param string $label
   */
  public function setLabel($label) {
    $this->label = $label;
  }

  /**
   * @return string
   */
  public function getFileExtension() {
    if (!$this->fileExtension) {
      $pathInfo = pathinfo($this->itemPath);
      $this->fileExtension = array_key_exists('extension', $pathInfo) ? strtolower($pathInfo['extension']) : '';
    }
    return $this->fileExtension;
  }

  /**
   * @return string
   */
  public function getAbsolutePath() {
    return $this->mount->getRootPath() . $this->itemPath;
  }

  /**
   * @return bool
   */
  public function isDirectory() {
    return is_dir($this->getAbsolutePath());
  }

  /**
   *
   */
  public function getTitleImage() {
    if (!$this->titleImage) {
      $items = $this->mount->getItems($this->getItemPath());
      $files = $items->getFilesByExtensions(FileExtensionUtility::getAllowedFileExtensions(FileExtensionUtility::ALLOWED_IMAGES));
      $files->rewind();
      $titleImage = $files->current();
      if (! $titleImage instanceof DirectoryItem) {
        $directories = $items->getDirectories();
        $directories->rewind();
        /** @var DirectoryItem $firstSubDirectory */
        $firstSubDirectory = $directories->current();
        $titleImage = $firstSubDirectory->getTitleImage();
      }
      $this->titleImage = $titleImage;
    }
    return $this->titleImage;
  }

  /**
   * @return int
   */
  public function getIncludedImages() {
    if (!$this->includedImages) {
      $this->includedImages = $this->getIncludedItems(FileExtensionUtility::ALLOWED_IMAGES);
    }
    return $this->includedImages;
  }

  /**
   * @return int
   */
  public function getIncludedVideos() {
    if (!$this->includedVideos) {
      $this->includedVideos = $this->getIncludedItems(FileExtensionUtility::ALLOWED_VIDEOS);
    }
    return $this->includedVideos;
  }

  /**
   * @param string $scope
   * @return int
   */
  public function getIncludedItems($scope = FileExtensionUtility::ALLOWED_ALL) {
    if ($scope === FileExtensionUtility::ALLOWED_ALL) {
      return $this->getIncludedImages() + $this->getIncludedVideos();
    }
    $items = $this->mount->getItems($this->getItemPath());
    $files = $items->getFilesByExtensions(FileExtensionUtility::getAllowedFileExtensions($scope));
    $count = $files->count();
    foreach ($items->getDirectories() as $directory) {
      /** @var DirectoryItem $directory */
      $count += $directory->getIncludedItems($scope);
    }
    return $count;
  }

  /**
   * @param float $decimal
   * @param float $minutes
   * @param float $seconds
   * @return float
   */
  protected function coordinatesDsmToDecimal($decimal, $minutes, $seconds) {
    return $decimal + ($minutes * 60 + $seconds) / 3600;
  }

}