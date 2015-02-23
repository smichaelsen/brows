<?php
namespace Smichaelsen\Brows\Domain\Model;

use AppZap\PHPFramework\Domain\Model\AbstractModel;
use Smichaelsen\Brows\Filesystem\LocalDirectoryMount;

class DirectoryItem extends AbstractModel{

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
		$pathInfo = pathinfo($this->itemPath);
		return array_key_exists('extension', $pathInfo) ? $pathInfo['extension'] : '';
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

}