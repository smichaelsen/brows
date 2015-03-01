<?php
namespace Smichaelsen\Brows\Filesystem;

use Smichaelsen\Brows\Domain\Model\DirectoryItem;

class VideoPublisher extends AbstractFilePublisher {

  /**
   * @param DirectoryItem $item
   *
   * @return string
   */
  public function publish(DirectoryItem $item) {
    $hashIngredients = [
      $item->getItemPath(),
    ];
    $hash = $this->hash(serialize($hashIngredients));
    $targetFilename = $hash . '.' . strtolower($item->getFileExtension());
    if (!file_exists($this->publicDirectoryMount->getRootPath() . $targetFilename)) {
      $this->ensureFolderByPath($this->publicDirectoryMount->getRootPath() . $targetFilename);
      copy($item->getAbsolutePath(), $this->publicDirectoryMount->getRootPath() . $targetFilename);
    }
    return $this->publicDirectoryMount->getAbsolutePublicPath() . $targetFilename;
  }

}