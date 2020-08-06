**Summary**: Manage client connections to OpenVPN processes

**Description**: Simple library written in PHP to manage client connections to 
OpenVPN processes through the OpenVPN management socket.

**License**:  AGPL-3.0-or-later

# Introduction

Simple library written in PHP to manage client connections to OpenVPN processes 
through the OpenVPN management socket.

# Features

This library supports:

* Traversing multiple OpenVPN management sockets;
* Show connected clients;
* Disconnect clients (by CN);

# API 

```php
    $connMan = new \LC\OpenVpn\ConnectionManager(
        [
            'tcp://localhost:11940',
            'tcp://localhost:11941',
        ]
    );

    $connMan->connections();
    $connMan->disconnect(['foo']);      // array with CNs to disconnect
```

Also see the example in `example/`.

# Contact

You can contact me with any questions or issues regarding this project. Drop
me a line at [fkooman@tuxed.net](mailto:fkooman@tuxed.net).

If you want to (responsibly) disclose a security issue you can also use the
PGP key with key ID `9C5EDD645A571EB2` and fingerprint
`6237 BAF1 418A 907D AA98  EAA7 9C5E DD64 5A57 1EB2`.

# License

[AGPLv3+](LICENSE).
