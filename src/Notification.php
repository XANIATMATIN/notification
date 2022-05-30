<?php

namespace MatinUtils\Notifications;

class Notification
{
    public function setServerConfigs(string $name, string $medium, array $configs)
    {
        $data = [
            'name' => $name,
            'medium' => $medium,
            'configs' => $configs
        ];

        $url = env('NOTIFICATION_HOST', 'http://notification.api') . '/server';

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_TIMEOUT_MS => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => ['pid: ' . app('log-system')->getpid()],
            CURLOPT_POSTFIELDS => http_build_query($data)
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $options);

        try {
            $response = curl_exec($curl);
        } catch (\Throwable $th) {
            app('log')->error("cURL Error #:" . $th->getMessage());
        }
        if ($err = curl_error($curl)) {
            app('log')->error("cURL Error #:" . $err);
            return false;
        }
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 500) {
            app('log')->error("cURL Error ", [$response]);
            return false;
        }

        return json_decode($response, true);
    }

    public function getServerConfigs(string $name)
    {
        $data = [
            'name' => $name
        ];

        $url = env('NOTIFICATION_HOST', 'http://notification.api') . '/getServerConfigs';

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_TIMEOUT_MS => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => ['pid: ' . app('log-system')->getpid()],
            CURLOPT_POSTFIELDS => http_build_query($data)
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $options);

        try {
            $response = curl_exec($curl);
        } catch (\Throwable $th) {
            app('log')->error("cURL Error #:" . $th->getMessage());
        }
        if ($err = curl_error($curl)) {
            app('log')->error("cURL Error #:" . $err);
            return false;
        }
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 500) {
            app('log')->error("cURL Error ", [$response]);
            return false;
        }

        return json_decode($response, true);
    }

    public function bind(string $title, string $server, array $configs)
    {
        $data = [
            'title' => $title,
            'server' => $server,
            'configs' => $configs
        ];

        $url = config('notification.host', 'http://notification.api') . '/bind';

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_TIMEOUT_MS => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => ['pid: ' . app('log-system')->getpid()],
            CURLOPT_POSTFIELDS => http_build_query($data)
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $options);

        try {
            $response = curl_exec($curl);
        } catch (\Throwable $th) {
            app('log')->error("cURL Error #:" . $th->getMessage());
        }
        if ($err = curl_error($curl)) {
            app('log')->error("cURL Error #:" . $err);
            return false;
        }
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 500) {
            app('log')->error("cURL Error ", [$response]);
            return false;
        }

        return json_decode($response, true);
    }

    public function sendNotification(string $notification, array $audience, array $variables, string $additionToSubject = '', string $preferedSendType = 'socket')
    {
        $data = [
            'notification' => $notification,
            'audience' => $audience,
            'variables' => $variables,
            'additionToSubject' => $additionToSubject
        ];
        $sendType = $this->sendType($preferedSendType);

        return $this->{$sendType . 'SendNotification'}($data);
    }

    protected function sendType($preferedSendType)
    {
        $sendType = $preferedSendType == 'http' ? 'http' : config('notification.sendType', 'http');
        if ($sendType == 'socket') {
            if (!empty($this->socketClient)) {
                if (($this->socketClient->isConnected)) {
                    return 'socket';
                }
            } else {
                $host = config('notification.easySocket.host');
                if (!empty($host)) {
                    $this->socketClient = new SocketClient($host);
                    if ($this->socketClient->isConnected) {
                        return 'socket';
                    }
                }
            }
        }
        return 'http';
    }

    public function httpSendNotification(array $data)
    {
        $url = config('notification.host', 'http://notification.api') . "/send";
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_TIMEOUT_MS => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => ['pid: ' . app('log-system')->getpid()],
            CURLOPT_POSTFIELDS => http_build_query($data)
        ];
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        try {
            $response = curl_exec($curl);
        } catch (\Throwable $th) {
            app('log')->error("cURL Error #:" . $th->getMessage());
        }
        if ($err = curl_error($curl)) {
            app('log')->error("cURL Error #:" . $err);
            return false;
        }
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 500) {
            app('log')->error("cURL Error ", [$response]);
            return false;
        }

        return json_decode($response, true);
    }

    public function socketSendNotification(array $data)
    {
        $data['pid'] = app('log-system')->getPid();
        $socketData = json_encode($data);

        if (!$this->socketClient->send($socketData)) {
            return $this->httpSendNotification($data);
        }

        return ['status' => true];
    }

    public function sendCustomNotification(string $medium, array $audience, array $template)
    {
        $server = notificationName($medium);
        $url = env('NOTIFICATION_HOST', 'http://notification.api') . "/send/custom";

        $data = [
            'server' => $server,
            'audience' => $audience,
            'template' => $template
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_TIMEOUT_MS => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => ['pid: ' . app('log-system')->getpid()],
            CURLOPT_POSTFIELDS => http_build_query($data)
        ];
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        try {
            $response = curl_exec($curl);
        } catch (\Throwable $th) {
            app('log')->error("cURL Error #:" . $th->getMessage());
        }
        if ($err = curl_error($curl)) {
            app('log')->error("cURL Error #:" . $err);
            return false;
        }
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 500) {
            app('log')->error("cURL Error ", [$response]);
            return false;
        }

        return json_decode($response, true);
    }
}
