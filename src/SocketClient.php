<?php

namespace MatinUtils\Notifications;

use MatinUtils\EasySocket\Client as EasySocketClient;

class SocketClient extends EasySocketClient
{
    public function send($data = '')
    {
        if ($this->isConnected) {
            $data .= "\0";
            return $this->writeOnSocket($data);
        } else {
            return false;
        }
    }
}
