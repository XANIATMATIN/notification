<?php

function setEmailConfigs(string $serverName, string $address, string $username, string $password, string $host, string $port, string $driver, string $name, string $encryption)
{
    $configs = [
        'driver' => $driver,
        'host' => $host,
        'port' => $port,
        'username' => $username,
        'password' => $password,
        'encryption' => $encryption,
        'from' => [
            'name' => $name,
            'address' => $address
        ]
    ];
    return app('notifications')->setServerConfigs($serverName, 'email', $configs);
}

function getEmailConfigs(string $name)
{
    return app('notifications')->getServerConfigs($name);
}

function bindNotification(string $title, string $server, string $subject, string $template)
{
    $configs = [
        'template' => $template,
        'subject' => $subject
    ];
    return app('notifications')->bind($title, $server, $configs);
}
