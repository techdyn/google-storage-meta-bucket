<?php

namespace TechDyn\GoogleStorageMetaBucket\Tools;

use Closure;

class ReflectionTools
{
    private static $__reflectedProps = [];

    /**
     * @param $object
     * @param string|array $variable - returns array if array specified
     * @param string $className
     * @param bool|array $exec
     * @return mixed|null
     */
    public static function stealVariable(&$object, $variable, string $className, $exec = false)
    {
        $cacheKey = ((false !== $exec ? "e#" : "s#") . spl_object_hash($object) . "#{$variable}");
        if (!isset(self::$__reflectedProps[$cacheKey]))
        {
            self::$__reflectedProps[$cacheKey] = Closure::bind(function () use ($object, $variable, $exec)
            {
                $vars = is_array($variable) ? $variable : [ $variable ];
                $execParams = is_array($exec) ? $exec : [];

                $output = [];
                foreach($vars as $v)
                {
                    if (property_exists($object, $v) &&
                       (false === $exec || ($exec && is_callable($object->$v))))
                    {
                        $output[$v] =
                                $exec ?
                                    $object->$v(...$execParams[$v]) :
                                    $object->$v;
                    }
                }

                return
                    is_array($variable) ?
                    $output :
                    reset($output);

            }, null, $className);
        }

        if (!empty($__reflectedProps[$cacheKey]) &&
            is_callable(self::$__reflectedProps[$cacheKey]))
        {
            return self::$__reflectedProps[$cacheKey]();
        }

        return null;
    }
}