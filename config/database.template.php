<?php

return array(
    'connection.options' => array(
        /**
         * pdo_mysql | pdo_sqlite
         */
        'driver' => 'pdo_sqlite',
        //  'host'     => 'localhost',
        //  'dbname'   => 'opentribes',
        //   'user'     => 'username',
        //  'password' => 'password',
        //  'charset'  => 'utf8',
        'path' => realpath(__DIR__ . '/../storage/ot.connection')
    )
);
