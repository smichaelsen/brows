<?php
namespace Smichaelsen\Brows\Controller;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Mvc\AbstractController;
use AppZap\PHPFramework\Mvc\View\TwigView;
use Smichaelsen\Brows\Domain\Collection\DirectoryItemCollection;
use Smichaelsen\Brows\Domain\Model\DirectoryItem;
use Smichaelsen\Brows\Filesystem\ImagePublisher;
use Smichaelsen\Brows\Filesystem\LocalDirectoryMount;

class GalleryController extends AbstractController {

	/**
	 * @var string
	 */
	protected $allowedFileExtensions = 'jpg, jpeg, gif, png';

	/**
	 * @var LocalDirectoryMount
	 */
	protected $mount;

	/**
	 * @var TwigView
	 */
	protected $response;

	/**
	 * @throws \Exception
	 */
	public function initialize() {
		$this->mount = new LocalDirectoryMount();
		$this->mount->setRootPath(Configuration::get('application', 'media_root_folder'));
		$prefix = Configuration::get('phpframework', 'uri_path_prefix', FALSE);
		if ($prefix) {
			$baseUrl = '/' . trim($prefix, '/') . '/';
		} else {
			$baseUrl = '/';
		}
		$this->response->set('base_url', $baseUrl);
		$this->registerTwigFunctions();
	}

	/**
	 * Handle GET requests
	 *
	 * @param array $params
	 */
	public function get($params) {
		if ($params[0] === '') {
			$currentPath = '.';
		} else {
			$currentPath = $params[0];
			$rootline = $this->getRootlineFromPath($currentPath);
			$this->response->set('rootline', $rootline);
		}
		$currentDirectory = $this->mount->getItems($currentPath);
		$directories = $currentDirectory->getDirectories();
		$items = $currentDirectory->getFilesByExtensions($this->allowedFileExtensions);
		$this->response->set('currentPath', $currentPath === '.' ? 'Home' : $currentPath);
		$this->response->set('directories', $directories);
		$this->response->set('items', $items);
	}

	/**
	 * @param string $path
	 * @return DirectoryItemCollection
	 */
	protected function getRootlineFromPath($path) {
		$rootline = new DirectoryItemCollection();
		$rootDirectory = new DirectoryItem();
		$rootDirectory->setItemPath('/');
		$rootDirectory->setLabel('Home');
		$rootline->add($rootDirectory);
		$currentPathSegments = explode('/', trim($path, '/'));
		$lastPath = '';
		foreach ($currentPathSegments as $currentPathSegment) {
			$lastPath .= ($lastPath ? '/' : '') . $currentPathSegment;
			$segment = new DirectoryItem();
			$segment->setItemPath($lastPath);
			$rootline->add($segment);
		}
		return $rootline;
	}


	/**
	 *
	 */
	protected function registerTwigFunctions() {
		$imagePublisher = new ImagePublisher();
		$this->response->addOutputFunction('publicUrl', function($image, $width = NULL, $height = NULL) use ($imagePublisher){
			return $imagePublisher->publish($image, $width, $height);
		});
		$this->response->addOutputFunction('asset', function($path) {
			$path = 'assets/' . trim($path, '/') . '/';
			$prefix = Configuration::get('phpframework', 'uri_path_prefix', FALSE);
			if ($prefix) {
				return '/' . trim($prefix, '/') . '/' . trim($path, '/');
			} else {
				return '/' . trim($path, '/');
			}
		});
	}

}
