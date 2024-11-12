<?php

namespace App\Action;

use App\Dto\ActionDto;

interface ActionInterface
{
    public function do(ActionDto $dto);
}