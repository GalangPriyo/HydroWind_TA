<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MQTT Broker Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting to your MQTT broker. For HiveMQ Cloud,
    | TLS must be enabled and port should be 8883.
    |
    */

    'host' => env('MQTT_BROKER_HOST', 'localhost'),
    'port' => env('MQTT_BROKER_PORT', 1883),
    'username' => env('MQTT_USERNAME'),
    'password' => env('MQTT_PASSWORD'),
    'client_id' => env('MQTT_CLIENT_ID', 'laravel-client-') . uniqid(),
    'tls' => env('MQTT_TLS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | TLS/SSL Specific Configuration
    |--------------------------------------------------------------------------
    |
    | Additional TLS settings for secure connections.
    |
    */
    'tls_settings' => [
        'verify_peer' => env('MQTT_TLS_VERIFY_PEER', false),
        'verify_peer_name' => env('MQTT_TLS_VERIFY_PEER_NAME', false),
        'allow_self_signed' => env('MQTT_TLS_ALLOW_SELF_SIGNED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Connection Settings
    |--------------------------------------------------------------------------
    */
    'keepalive' => env('MQTT_KEEPALIVE', 30), // Seconds
    'clean_session' => env('MQTT_CLEAN_SESSION', false),
    'reconnect_delay' => env('MQTT_RECONNECT_DELAY', 5), // Seconds

    /*
    |--------------------------------------------------------------------------
    | Default Topics
    |--------------------------------------------------------------------------
    */
    'topics' => [
        'sensor',
        'baterai',
        'gps'
    ],

    /*
    |--------------------------------------------------------------------------
    | QoS Level
    |--------------------------------------------------------------------------
    |
    | Quality of Service level (0, 1, or 2)
    |
    */
    'qos' => env('MQTT_QOS', 0),
];
