<?php
namespace Smichaelsen\Brows\Utility;

use AppZap\PHPFramework\Configuration\Configuration;

class FileExtensionUtility {

  const ALLOWED_ALL = 'all';
  const ALLOWED_IMAGES = 'images';
  const ALLOWED_VIDEOS = 'videos';

  /**
   * @param string $scope
   * @return string
   */
  public static function getAllowedFileExtensions($scope = self::ALLOWED_ALL) {
    if ($scope === self::ALLOWED_IMAGES) {
      return Configuration::get('application', 'allowed_image_file_extensions');
    }
    if ($scope === self::ALLOWED_VIDEOS) {
      return Configuration::get('application', 'allowed_video_file_extensions');
    }
    return trim(Configuration::get('application', 'allowed_image_file_extensions') . ', ' . Configuration::get('application', 'allowed_video_file_extensions'), ', ');
  }

}