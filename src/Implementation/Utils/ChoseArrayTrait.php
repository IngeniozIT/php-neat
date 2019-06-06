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
        $count = count($array);
        $chosen = rand(1, $count);
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
        $count = count($array);
        $chosen = rand(1, $count);
        foreach ($array as $index => $value) {
            if (--$chosen <= 0) {
                return $value;
            }
        }
    }
}
