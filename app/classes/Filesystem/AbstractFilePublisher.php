<?php
namespace Smichaelsen\Brows\Filesystem;

abstract class AbstractFilePublisher {

  /**
   * @var LocalDirectoryMount
   */
  protected $publicDirectoryMount;

  public function __construct() {
    $this->publicDirectoryMount = new LocalDirectoryMount();
    $this->publicDirectoryMount->setRootPath('assets/images/');
    $this->publicDirectoryMount->setPublicPath('assets/images/');
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

}