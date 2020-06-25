<?php

namespace TechDyn\GoogleStorageMetaBucket\Traits;

use TechDyn\GoogleStorageMetaBucket\Tools\ReflectionTools;

trait VarThiefTrait
{
    private static $__reflectionParentClass = null;

    /**
     * @param string $variableName
     * @param bool|array $exec
     * @return mixed|null
     */
    private function stealParentVar(string $variableName, $exec = false)
    {
        return ReflectionTools::stealVariable(
            $this,
            $variableName,
            self::$__reflectionParentClass ?: get_parent_class($this),
            $exec
        );
    }
}