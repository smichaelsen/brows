<?php
namespace Smichaelsen\Brows\Domain\Collection;

use AppZap\PHPFramework\Domain\Collection\AbstractModelCollection;
use Smichaelsen\Brows\Domain\Model\LocalDirectoryItem;

class FileCollection extends AbstractModelCollection {

	/**
	 * @param $allowedFileExtensions
	 * @return $this
	 */
	public function filterForFileExtension($allowedFileExtensions) {
		$allowedFileExtensions = array_map('trim', explode(', ', $allowedFileExtensions));
		$itemsToRemove = new FileCollection();
		foreach ($this as $item) {
			/** @var $item LocalDirectoryItem */
			$itemFileExtension = $item->getFileExtension();
			if (!in_array($itemFileExtension, $allowedFileExtensions)) {
				$itemsToRemove->set_item($item);
			}
		}
		$this->removeItems($itemsToRemove);
		return $this;
	}

}