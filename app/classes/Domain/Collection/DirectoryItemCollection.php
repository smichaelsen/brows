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
		$allowedFileExtensions = array_map('trim', explode(', ', $allowedFileExtensions));
		$matchingFiles = new DirectoryItemCollection();
		foreach ($this as $item) {
			/** @var $item DirectoryItem */
			if (in_array($item->getFileExtension(), $allowedFileExtensions)) {
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
			if ($item->isDirectory()) {
				$directories->add($item);
			}
		}
		return $directories;
	}

}