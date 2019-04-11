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
     * @param int    $time
     * @param string $status
     */
    public function __construct(int $time, string $status = 'undefined')
    {
        $this->time = $time;
        $this->status = $status;
        $this->id = md5(rand(0,PHP_INT_MAX));
    }

    public function execute()
    {
        $this->status = 'process';
        echo 'I start sleep on: ' . $this->time . ' seconds'.PHP_EOL;
        $t = sleep($this->time);
        // If task will interrupted
        if(!empty($t)) {
            sleep($t);
        }
        echo 'I wake up!' . PHP_EOL;
        $this->status = 'done';
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

    public function setStatus(string  $status): void
    {
        $this->status = $status;
    }

}