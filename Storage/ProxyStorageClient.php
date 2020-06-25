<?php

namespace TechDyn\GoogleStorageMetaBucket\Storage;

use Google\Cloud\Storage\StorageClient;
use TechDyn\GoogleStorageMetaBucket\Traits\VarThiefTrait;

class ProxyStorageClient extends StorageClient
{
    use VarThiefTrait;

    /**
     * @param string $name
     * @param bool $userProject
     * @return MetaBucket
     */
    public function bucket($name, $userProject = false)
    {
        if (!$userProject) {
            $userProject = null;
        } elseif (!is_string($userProject)) {
            $userProject = $this->stealParentVar('projectId'); // private inherited variable
        }

        return new MetaBucket($this->connection, $name, [
            'requesterProjectId' => $userProject
        ]);
    }
}