<?php
namespace Helmich\S3\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Helmich.S3".            *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cli\CommandController;
use TYPO3\Flow\Persistence\Doctrine\Query;
use TYPO3\Flow\Resource\Publishing\ResourcePublisher;
use TYPO3\Flow\Resource\Resource;

/**
 * @Flow\Scope("singleton")
 */
class BucketCommandController extends CommandController {

	/**
	 * @var ResourcePublisher
	 * @Flow\Inject
	 */
	protected $resourcePublisher;

	/**
	 * Import all resources into AWS S3.
	 *
	 * @return void
	 */
	public function syncCommand() {
		$query = new Query(Resource::class);
		$result = $query->execute();

		foreach ($result as $resource) {
			/** @var Resource $resource */
			$this->output('Importing resource <comment>' . $resource->getResourcePointer()->getHash() . '</comment>... ');
			$this->resourcePublisher->publishPersistentResource($resource);
			$this->output('<info>done</info>' . "\n");
		}
	}

}