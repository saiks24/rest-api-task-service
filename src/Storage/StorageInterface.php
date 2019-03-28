<?php
namespace Saiks24\Storage;

use Saiks24\Command\CommandInterface;

interface StorageInterface
{

    /** Add command to storage
     * @param \Saiks24\Command\CommandInterface $command
     *
     * @return mixed
     */
    public function add(CommandInterface $command);

    /** Get command from storage
     * @return \Saiks24\Command\CommandInterface
     */
    public function get() : CommandInterface;

    /** Delete command from storage
     * @param string $id
     *
     * @return bool
     */
    public function delete(string $id) : bool;
}