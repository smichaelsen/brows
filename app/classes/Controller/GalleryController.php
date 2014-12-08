<?php
namespace Smichaelsen\Brows\Controller;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Domain\Collection\GenericModelCollection;
use AppZap\PHPFramework\Mvc\AbstractController;
use Smichaelsen\Brows\Domain\Model\LocalDirectoryItem;
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
	 * @throws \Exception
	 */
	public function initialize() {
		$this->mount = new LocalDirectoryMount();
		$this->mount->setRootPath(Configuration::get('application', 'media_root_folder'));
		$this->registerTwigFunctions();
	}

	/**
	 * Handle GET requests
	 */
	public function get() {
		$items = $this->mount->getItems('.')->filterForFileExtension($this->allowedFileExtensions);
		$this->response->set('items', $items);
	}


	/**
	 *
	 */
	protected function registerTwigFunctions() {
		$imagePublisher = new ImagePublisher();
		$this->response->add_output_function('publicUrl', function($image, $width = NULL, $height = NULL) use ($imagePublisher){
			return $imagePublisher->publish($image, $width, $height);
		});
		$this->response->add_output_function('asset', function($path) {
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
