<?php

namespace Saiks24\Command;


class TestCommand implements CommandInterface
{
    private $time;

    /**
     * TestCommand constructor.
     *
     * @param $time
     */
    public function __construct($time)
    {
        $this->time = $time;
    }

    public function execute()
    {

    }

}