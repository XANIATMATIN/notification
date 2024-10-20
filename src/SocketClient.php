<?php

namespace MatinUtils\Notifications;

use MatinUtils\EasySocket\Client as EasySocketClient;

class SocketClient extends EasySocketClient
{
    public function send($data = '')
    {
        $req = $this->writeOnSocket(app('easy-socket')->prepareMessage(json_encode($data)));
        if (!($data['withResponse'] ?? false)) {
            return $req;
        }
        return $this->readSocket();
    }
}
