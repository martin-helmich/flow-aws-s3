<?php
namespace Helmich\S3\Resource\Publishing;

use Aws\S3\S3Client;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Resource\Publishing\FileSystemPublishingTarget;
use TYPO3\Flow\Resource\Resource;

class S3PublishingTarget extends FileSystemPublishingTarget {

	/**
	 * @var S3Client
	 */
	protected $client;

	/**
	 * @var string
	 * @Flow\Inject(setting="resource.publishing.profile")
	 */
	protected $profile;

	/**
	 * @var string
	 * @Flow\Inject(setting="resource.publishing.region")
	 */
	protected $region;

	/**
	 * @var string
	 * @Flow\Inject(setting="resource.publishing.bucket")
	 */
	protected $bucket;

	/**
	 * @var
	 * @Flow\Inject(setting="resource.publishing.expiration")
	 */
	protected $expiration;

	public function initializeObject() {
		parent::initializeObject();

		$this->client = S3Client::factory(
			[
				'profile' => $this->profile,
				'region'  => $this->region
			]
		);
	}

	public function publishPersistentResource(Resource $resource) {
		$key = $this->getObjectKeyForResource($resource);
		$exists = $this->client->doesObjectExist($this->bucket, $key);

		if (FALSE === $exists) {
			$this->client->putObject(
				[
					'Bucket'     => $this->bucket,
					'Key'        => $key,
					'SourceFile' => $this->getPersistentResourceSourcePathAndFilename($resource),
					'ACL'        => 'public-read',
					'Expires'    => new \DateTime('today + ' . $this->expiration)
				]
			);
			$this->client->waitUntilObjectExists(['Bucket' => $this->bucket, 'Key' => $key]);
		}

		return $this->client->getObjectUrl($this->bucket, $key);
	}

	public function unpublishPersistentResource(Resource $resource) {
		$key = $resource->getResourcePointer()->getHash() . '/' . $resource->getFilename();
		$this->client->deleteObject(
			[
				'Bucket' => $this->bucket,
				'Key'    => $key
			]
		);
	}

	/**
	 * @param Resource $resource
	 * @return string
	 */
	private function getObjectKeyForResource(Resource $resource) {
		$hash = $resource->getResourcePointer()->getHash();
		$key = substr($hash, 0, 2) . '/' . substr($hash, 2) . '/' . $resource->getFilename();
		return $key;
	}

}