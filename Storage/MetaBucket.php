<?php

namespace TechDyn\GoogleStorageMetaBucket\Storage;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\Connection\ConnectionInterface;
use Google\Cloud\Storage\StorageObject;
use Psr\Http\Message\StreamInterface;

class MetaBucket extends Bucket
{
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
        $metadata = $this->metadata ? [ 'metadata' => $this->metadata ] : [];

        return parent::upload($data, array_replace_recursive($metadata, $options));
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
}
