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
     * @param string $id
     *
     * @return \Saiks24\Command\CommandInterface
     */
    public function get(string $id) : CommandInterface;

    /** Delete command from storage
     * @param string $id
     *
     * @return bool
     */
    public function delete(string $id) : bool;
}