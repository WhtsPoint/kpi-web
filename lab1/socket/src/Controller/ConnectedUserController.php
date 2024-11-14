<?php

namespace App\Controller;

use App\Entity\ConnectedUser;
use App\Entity\ConnectedUserPool;
use Exception;
use Swoole\Http\Request;
use Swoole\Server;

class ConnectedUserController
{
    public function __construct(
        private readonly Server $server,
        private readonly ConnectedUserPool $pool
    ) {
    }

    /**
     * @throws Exception
     */
    public function connectById(Request $request): void
    {
        $user = new ConnectedUser($request->fd);

        $this->pool->add($user);
    }
}