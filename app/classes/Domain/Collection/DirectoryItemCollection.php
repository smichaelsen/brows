<?php
namespace Smichaelsen\Brows\Domain\Collection;

use AppZap\PHPFramework\Domain\Collection\AbstractModelCollection;
use Smichaelsen\Brows\Domain\Model\DirectoryItem;

class DirectoryItemCollection extends AbstractModelCollection {

  /**
   * @param string $allowedFileExtensions comma separated list of file extensions
   *
   * @return DirectoryItemCollection
   */
  public function getFilesByExtensions($allowedFileExtensions) {
    $allowedFileExtensions = array_map(function ($string) {
      return trim(strtolower($string));
    }, explode(', ', $allowedFileExtensions));
    $matchingFiles = new DirectoryItemCollection();
    foreach ($this as $item) {
      /** @var $item DirectoryItem */
      if (in_array(strtolower($item->getFileExtension()), $allowedFileExtensions)) {
        $matchingFiles->add($item);
      }
    }
    return $matchingFiles;
  }

  /**
   * @return DirectoryItemCollection
   */
  public function getDirectories() {
    $directories = new DirectoryItemCollection();
    foreach ($this as $item) {
      /** @var $item DirectoryItem */
      if ($item->getLabel() === '@eaDir') {
        continue;
      }
      if ($item->isDirectory()) {
        $directories->add($item);
      }
    }
    return $directories;
  }

}