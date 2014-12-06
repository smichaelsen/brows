<?php
namespace Smichaelsen\Brows\Filesystem;

use AppZap\PHPFramework\Configuration\Configuration;
use Smichaelsen\Brows\Domain\Collection\FileCollection;
use Smichaelsen\Brows\Domain\Model\LocalDirectoryItem;

class LocalDirectoryMount {

	/**
	 * @var string
	 */
	protected $publicPath;

	/**
	 * @var string
	 */
	protected $rootPath;

	/**
	 * @return string
	 */
	public function getAbsolutePublicPath() {
		if (isset($this->publicPath)) {
			return $this->prefixPublicPath($this->publicPath);
		}
		return NULL;
	}

	/**
	 * @param $path
	 *
	 * @return string
	 */
	protected function prefixPublicPath($path) {
		$prefix = Configuration::get('phpframework', 'uri_path_prefix', FALSE);
		if ($prefix) {
			return '/' . trim($prefix, '/') . '/' . trim($path, '/') . '/';
		} else {
			return '/' . trim($path, '/') . '/';
		}
	}

	/**
	 * @return string
	 */
	public function getPublicPath() {
		return $this->publicPath;
	}

	/**
	 * @param string $publicPath
	 */
	public function setPublicPath($publicPath) {
		$this->publicPath = $publicPath;
	}

	/**
	 * @return string
	 */
	public function getRootPath() {
		return $this->rootPath;
	}

	/**
	 * @param string $rootPath
	 * @throws \Exception
	 */
	public function setRootPath($rootPath) {
		$rootPath = Configuration::get('phpframework', 'project_root') . $rootPath;
		$rootPath = rtrim(realpath($rootPath), '/') . '/';
		if ($rootPath === FALSE) {
			throw new \Exception('root path ' . $rootPath . ' does not exist.', 1417768634);
		}
		$this->rootPath = $rootPath;
	}

	/**
	 * @param string $path
	 *
	 * @return FileCollection
	 * @throws \Exception
	 */
	public function getItems($path) {
		$path = rtrim($path, '/') . '/';
		$this->validatePath($path);
		$absolutePath = $this->getAbsolutePath($path);
		$filenames = scandir($absolutePath);
		$collection = new FileCollection();
		foreach ($filenames as $key => $filename) {
			if ($filename === '.' || $filename === '..') {
				unset($filenames[$key]);
			} else {
				$item = new LocalDirectoryItem();
				$item->setMount($this);
				$item->setItemPath($path . $filename);
				$collection->set_item($item);
			}
		}
		return $collection;
	}

	/**
	 * @param $path
	 *
	 * @throws \Exception
	 */
	protected function validatePath($path) {
		$absolutePath = $this->getAbsolutePath($path);
		if ($absolutePath === FALSE && strpos($absolutePath, $this->rootPath) !== 0) {
			throw new \Exception('path ' . $path . ' does not exist or is outside the root path', 1417768785);
		}
	}

	/**
	 * @param string $path
	 *
	 * @return string
	 */
	protected function getAbsolutePath($path) {
		$absolutePath = $this->rootPath . $path;
		return rtrim(realpath($absolutePath), '/') . '/';
	}

}