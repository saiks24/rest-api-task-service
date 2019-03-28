<?php
namespace Saiks24\Command;

interface CommandInterface
{

    /** Run command execute
     * @return mixed
     */
    public function execute();

    /** Get identity of command
     * @return string
     */
    public function getId(): string;

    /** Get status of command execution
     * @return string
     */
    public function getStatus() : string;

}