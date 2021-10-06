<?php

namespace Pipen\ApiTesting\Traits;

use Exception;

trait JsonAssert
{
    /**
     * Check if key exists
     *
     * @param array  $collection
     * @param string $keys
     * @param int    $size
     *
     * @return bool
     * @throws \Exception
     */
    public function assertJsonKey(array $collection, string $keys, int $size = -1): bool
    {
        $associativeKeys = explode('.', $keys);
        $currentIndex    = null;

        # Find keys
        foreach ($associativeKeys as $key) {
            if ($currentIndex === null && isset($collection[$key])) {
                $currentIndex = $collection[$key];
            } else {
                isset($currentIndex[$key]) ?
                $currentIndex = $currentIndex[$key] :
                throw new Exception('Key: "' . $key . '" not found');
            }
        }

        # Check if size excepted is correct
        if ($size >= 0) {
            if (count($currentIndex) != $size) {
                throw new Exception('Results size different, current size: ' . count($currentIndex) . ', size excepted: ' . $size);
            }
        }

        return true;
    }
}
