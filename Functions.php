<?php

function setEmailConfigs(string $serverName, array $data)
{
    $configs = [
        'driver' => $data['driver'],
        'host' => $data['host'],
        'port' => $data['port'],
        'username' => $data['username'],
        'password' => $data['password'],
        'encryption' => $data['encryption'] ?? '',
        'from' => [
            'name' => $data['name'],
            'address' => $data['address']
        ],
        'testEmail' => $data['testEmail']
    ];
    return app('notifications')->setServerConfigs($serverName, 'email', $configs);
}

function setSmsConfigs(string $serverName, array $data)
{
    return app('notifications')->setServerConfigs($serverName, 'sms', $data);
}

function setWhatsappConfigs(string $serverName, array $data)
{
    return app('notifications')->setServerConfigs($serverName, 'whatsapp', $data);
}

function getServerConfigs(string $name)
{
    return app('notifications')->getServerConfigs($name);
}

function bindNotification(string $title, string $server, string $subject, string $template, array $attachments = [])
{
    $configs = [
        'template' => $template,
        'subject' => $subject,
        'attachments' => $attachments
    ];
    return app('notifications')->bind($title, $server, $configs);
}
