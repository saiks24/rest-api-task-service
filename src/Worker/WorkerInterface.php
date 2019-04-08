<?php
namespace Saiks24\Worker;

interface WorkerInterface
{

    /** Run queue worker
     * @return mixed
     */
    public function run();

    /** Stop queue worker
     * @return mixed
     */
    public function stop();
}