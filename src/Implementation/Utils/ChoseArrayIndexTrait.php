<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Utils;

trait ChoseArrayIndexTrait
{
    /**
     * Chose a random index from the given array.
     *
     * @param  array $array
     *
     * @return int The chosen index.
     */
    protected function choseArrayIndex(array $array): int
    {
        $count = count($array);
        $chosen = rand(1, $count);
        foreach ($array as $index => $value) {
            if (--$chosen <= 0) {
                return $index;
            }
        }
    }
}
