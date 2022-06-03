<?php

namespace BrunoAlod\BasicSocketServer;

use Socket;

class BasicSocketServer
{
    /**
     * IP to launch our server on.
     * 
     * @var string
     */
    public string $ip;

    /**
     * Port to launch our server on.
     * 
     * @var int
     */
    public int $port;

    /**
     * Master socket from which we read new connections.
     * 
     * @var Socket|resource
     */
    public $masterSocket;

    /**
     * Clients connected to the server.
     * 
     * @var List<SocketClient>
     */
    public SocketClientList $clients;

    private $onServerLaunch;
    private $onServerShutdown;
    private $onClientConnected;
    private $onClientDisconnect;
    private $onSend;
    private $onReceive;


    public function __construct(
        string $ip, 
        int $port
    )
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->clients = new SocketClientList();
    }

    /**
     * Launches the server.
     * 
     * @return void
     */
    public function launch() : void
    {
        $this->masterSocket = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_bind($this->masterSocket, $this->ip, $this->port);
        socket_listen($this->masterSocket);

        if(is_callable($this->onServerLaunch))
        {
            call_user_func($this->onServerLaunch);
        }

        while(true)
        {
            $sockets = $this->clients->toSocketArray();
            $read = [...$sockets, $this->masterSocket];
            $write = null;
            $except = null;

            if (socket_select($read, $write, $except, 0) < 1)
            {   
                usleep(100000);
                continue;
            }

            if (in_array($this->masterSocket, $read)) 
            {
                $newsock = socket_accept($this->masterSocket);
           
                $newSocketClient = new SocketClient($newsock);

                $this->clients->add($newSocketClient);

                $key = array_search($this->masterSocket, $read);

                unset($read[$key]);

                if(is_callable($this->onClientConnected))
                {
                    call_user_func($this->onClientConnected, $newSocketClient);
                }
            }
       
            foreach ($read as $read_sock) 
            {
                $clientSocket = $this->clients->getFromSocket($read_sock);

                $data = @socket_read($read_sock, 1024);

                if ($data === false || $data === "") 
                {
                    $this->disconnectClient($clientSocket);

                    continue;
                }

                $message = $data;

                if($message == null || $message == '') {
                    continue;
                }

                $this->receive($clientSocket, $message);
            }

            usleep(100000);
        }

        socket_close($this->masterSocket);
    }

    /**
     * Sends a packet to a client.
     * 
     * @return void
     */
    public function send(SocketClient $socketClient, string $message) : void
    {
        if(is_callable($this->onSend))
        {
            call_user_func($this->onSend, $socketClient, $message);
        }

        $socketClient->send($message);
    }

    /**
     * Receives a message from a client.
     */
    public function receive(SocketClient $socketClient, string $message)
    {
        if(is_callable($this->onReceive))
        {
            call_user_func($this->onReceive, $socketClient, $message);
        }
    }


    /**
     * Disconnects a client.
     * 
     * @return void
     */
    public function disconnectClient(SocketClient $socketClient) : void
    {
        for ($i=0; $i < count($this->clients->items); $i++) 
        {
            if($this->clients->items[$i]->socket == $socketClient->socket)
            {
                unset($this->clients->items[$i]);
                $this->clients->items = array_values($this->clients->items);

                if(is_callable($this->onClientDisconnect))
                {
                    call_user_func($this->onClientDisconnect, $socketClient);
                }

                break;
            }
        }

        socket_close($socketClient->socket);
    }
 
    /**
     * Shuts down the server.
     * 
     * @return void
     */
    public function shutdown() : void
    {
        if(is_callable($this->onServerShutdown))
        {
            call_user_func($this->onServerShutdown);
        }
    }

    /**
     * Callback setter for when the server launches.
     * 
     * @return self
     */
    public function onServerLaunch(callable $callable) : self
    {
        $this->onServerLaunch = $callable;

        return $this;
    }

    /**
     * Callback setter for when the server shutsdown.
     * 
     * @return self
     */
    public function onServerShutdown(callable $callable) : self
    {
        $this->onServerShutdown = $callable;

        return $this;
    }

    /**
     * Callback setter for when a client connects.
     * 
     * @return self
     */
    public function onClientConnected(callable $callable) : self
    {
        $this->onClientConnected = $callable;

        return $this;
    }

    /**
     * Callback setter for hen a client disconnects.
     * 
     * @return self
     */
    public function onClientDisconnect(callable $callable) : self
    {
        $this->onClientDisconnect = $callable;

        return $this;
    }

    /**
     * Callback setter for when a message is sent to a client.
     * 
     * @return self
     */
    public function onSend(callable $callable) : self
    {
        $this->onSend = $callable;

        return $this;
    }

    /**
     * Callback setter for when the server receives a message from a client.
     * 
     * @return self
     */
    public function onReceive(callable $callable) : self
    {
        $this->onReceive = $callable;

        return $this;
    }
}