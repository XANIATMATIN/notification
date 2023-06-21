<?php

namespace MatinUtils\Notifications;

use MatinUtils\EasySocket\Client as EasySocketClient;

class SocketClient extends EasySocketClient
{
    public function send($data = '')
    {
        return $this->writeOnSocket(app('easy-socket')->prepareMessage($data));
    }
}
