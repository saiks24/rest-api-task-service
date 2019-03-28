<?php

namespace Saiks24\Command;


class TestCommand implements CommandInterface
{
    private $time;
    private $status;
    private $id;

    /**
     * TestCommand constructor.
     *
     * @param $time
     * @param $status
     * @param $id
     */
    public function __construct($time, $status, $id)
    {
        $this->time = $time;
        $this->status = $status;
        $this->id = $id;
    }


    public function execute()
    {

    }

    /**
     * @return mixed
     */
    public function getStatus() : string
    {
        return $this->status;
    }

    public function getId(): string
    {
        return $this->id;
    }


}