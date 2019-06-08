<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Utils;

trait ChoseArrayTrait
{
    /**
     * Chose a random index from the given array.
     *
     * @param  iterable $array
     *
     * @return int The chosen index.
     */
    protected function choseArrayIndex(iterable $array): int
    {
        $chosen = rand(1, count($array));
        foreach ($array as $index => $value) {
            if (--$chosen <= 0) {
                return $index;
            }
        }
    }

    /**
     * Chose a random value from the given array.
     *
     * @param  iterable $array
     *
     * @return mixed The chosen value.
     */
    protected function choseArrayValue(iterable $array)
    {
        $chosen = rand(1, count($array));
        foreach ($array as $index => $value) {
            if (--$chosen <= 0) {
                return $value;
            }
        }
    }
}
