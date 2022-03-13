<?php

namespace Core\Concerns;

interface Arrayable
{
    /**
     * @return array
     */
    public function toArray(): array;
}