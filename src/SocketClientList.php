<?php

namespace BrunoAlod\BasicSocketServer;

use Socket;

class SocketClientList
{
    public $items = [];

    public function add($item) : self
    {
        $this->items[] = $item;

        return $this;
    }

    public function getFromSocket($socket)
    {
        foreach ($this->items as $item)
        {
            if($item->socket == $socket)
            {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return array<Socket|resource>
     */
    public function toSocketArray() : array
    {
        $result = [];

        foreach ($this->items as $socketClient)
        {
            $result[] = $socketClient->socket;
        }

        return $result;
    }
}