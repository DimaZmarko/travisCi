<?php

namespace src\Commands;

interface CommandInterface
{
    /**
     * Performs calculations
     *
     * @param mixed ...$args
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function execute(...$args);
}
