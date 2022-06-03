# basic-socket-server

This package provides a basic TCP socket server written in PHP.

### How to install
-----------
PHP >=7.4 is required. 

You may install this package by running
```
composer require bruno-alod/basic-socket-server
```

### Usage
-----------
``` php

$server = new BasicSocketServer('127.0.0.1', 3000);

$server->onServerLaunch(function() {
    echo 'onServerLaunch' . PHP_EOL;
});

$server->onServerShutdown(function() {
    echo 'onServerShutdown' . PHP_EOL;
});

$server->onClientConnected(function(SocketClient $socketClient) {
    echo 'onClientConnected' . PHP_EOL;
});

$server->onClientDisconnect(function(SocketClient $socketClient) {
    echo 'onClientDisconnect' . PHP_EOL;
});

$server->onSend(function(SocketClient $socketClient, string $message) {
    echo 'onSend: ' . $message . PHP_EOL;
});

$server->onReceive(function(SocketClient $socketClient, string $message) {
    echo 'onReceive: ' . $message . PHP_EOL;
});

$server->launch();

```