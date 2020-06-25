<?php

namespace TechDyn\GoogleStorageMetaBucket\Storage;

use Google\Cloud\Core\Upload\ResumableUploader;
use Google\Cloud\Core\Upload\StreamableUploader;
use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\Connection\ConnectionInterface;
use Google\Cloud\Storage\StorageObject;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\StreamInterface;

class MetaBucket extends Bucket
{
    /**
     * @var array
     */
    protected $metadata;

    /**
     * MetaBucket constructor.
     * @param ConnectionInterface $connection
     * @param $name
     * @param array $info
     */
    public function __construct(ConnectionInterface $connection, $name, array $info = [])
    {
        parent::__construct($connection, $name, $info);
        $this->metadata = [];
    }

    /**
     * @param StreamInterface|resource|string|null $data
     * @param array $options
     * @return StorageObject
     */
    public function upload($data, array $options = [])
    {
        return parent::upload($data, $this->populateOptions($options));
    }

    /**
     * @param StreamInterface|resource|string|null $data
     * @param array $options
     * @return PromiseInterface
     */
    public function uploadAsync($data, array $options = [])
    {
        return parent::uploadAsync($data, $this->populateOptions($options));
    }

    /**
     * @param array $sourceObjects
     * @param string $name
     * @param array $options
     * @return StorageObject
     */
    public function compose(array $sourceObjects, $name, array $options = [])
    {
        return parent::compose($sourceObjects, $name, $this->populateOptions($options));
    }

    /**
     * @param StreamInterface|resource|string $data
     * @param array $options
     * @return StreamableUploader
     */
    public function getStreamableUploader($data, array $options = [])
    {
        return parent::getStreamableUploader($data, $this->populateOptions($options));
    }

    /**
     * @param StreamInterface|resource|string|null $data
     * @param array $options
     * @return ResumableUploader
     */
    public function getResumableUploader($data, array $options = [])
    {
        return parent::getResumableUploader($data, $this->populateOptions($options));
    }

    /**
     * @param string $name
     * @param $value
     * @return $this
     */
    public function setOption(string $name, $value)
    {
        $this->metadata[$name] = $value;

        return $this;
    }

    /**
     * @param $options
     * @return array
     */
    private function populateOptions($options)
    {
        return array_replace_recursive(
            $this->metadata ?
                [ 'metadata' => $this->metadata ] : []
            , $options);
    }
}
