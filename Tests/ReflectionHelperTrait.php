<?php

declare(strict_types=1);

/*
 * This file is part of the ekino/data-protection-bundle project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\DataProtectionBundle\Tests;

trait ReflectionHelperTrait
{
    /**
     * @param mixed $object
     *
     * @return bool|mixed
     */
    public function invokeMethod($object, string $methodName, array $parameters = [])
    {
        try {
            $method = new \ReflectionMethod($object, $methodName);
            $method->setAccessible(true);

            return $method->invokeArgs($object, $parameters);
        } catch (\ReflectionException $e) {
        }

        return false;
    }
}
