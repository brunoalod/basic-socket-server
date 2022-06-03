<?php

namespace BrunoAlod\BasicSocketServer;

class SocketClient
{
    public string $id;
    public $socket;

    public function __construct(
        $socket
    )
    {
        $this->id = random_int(PHP_INT_MIN, PHP_INT_MAX);
        $this->socket = $socket;
    }

    public function send(string $data) : void
    {
        socket_write($this->socket, $data);
    }
}