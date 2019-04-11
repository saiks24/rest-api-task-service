<?php
return [
  'redis' => [
    'host' => '0.0.0.0',
    'port' => 6379,
  ],
  'amqp' => [
    'host' => '0.0.0.0',
    'port' => 5672,
    'vhost' => '/',
    'login' => 'guest',
    'password' => 'guest'
  ],
  'rateLimit' => 5,
  'token' => 'kjnxy1fjj1o231t05tes',
  'taskStorage' => ['class' => \Saiks24\Storage\RedisTaskStorage::class, 'args' => new Redis()],
  'tasks' => [
      'test' => Saiks24\Command\TestCommand::class
  ]
];