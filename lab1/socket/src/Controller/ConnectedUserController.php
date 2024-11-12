<?php

namespace App\Controller;

use App\Entity\ConnectedUser;
use App\Entity\ConnectedUserPool;
use Exception;
use OpenSwoole\WebSocket\Server;
use Swoole\Http\Request;

class ConnectedUserController
{
    public function __construct(
      private readonly ConnectedUserPool $pool
    ) {
    }

    /**
     * @throws Exception
     */
    public function connectById(Server $server, Request $request): void
    {
        $user = new ConnectedUser($request->fd);

        $this->pool->add($user);

        $server->push($request->fd, json_encode(['success' => true]));
    }
}