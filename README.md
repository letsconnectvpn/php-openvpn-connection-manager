# Introduction

Simple libarary written in PHP to manage OpenVPN processes using the OpenVPN
management socket.

# Features

This library supports:

* Traversing multiple OpenVPN management sockets;
* Extracting currently connected clients;
* Disconnect a client (by CN);

# API 

    $connMan = new \SURFnet\LC\OpenVpn\ConnectionManager(
        [
            'tcp://localhost:11940',
            'tcp://localhost:11941',
        ]
    );

    $connMan->connections();
    $connMan->disconnect('foo');

Also see `example/`.

# Contact

# License
