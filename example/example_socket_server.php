<?php

require __DIR__ . '/../vendor/autoload.php';

use BrunoAlod\BasicSocketServer\BasicSocketServer;
use BrunoAlod\BasicSocketServer\SocketClient;

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