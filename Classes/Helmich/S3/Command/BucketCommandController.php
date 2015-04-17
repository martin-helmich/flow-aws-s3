<?php
namespace Helmich\S3\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Helmich.S3".            *
 *                                                                        *
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
	 * An example command
	 *
	 * The comment of this command method is also used for TYPO3 Flow's help screens. The first line should give a very short
	 * summary about what the command does. Then, after an empty line, you should explain in more detail what the command
	 * does. You might also give some usage example.
	 *
	 * It is important to document the parameters with param tags, because that information will also appear in the help
	 * screen.
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