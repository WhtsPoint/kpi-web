<?php

namespace App\Entity;

interface SendMessageInterface
{
    public function sendMessage(Chat $chat, Message $message): void;

    public function sendConnectedMessage(Chat $chat, ConnectedUser $connectedUser): void;

    public function sendDisconnectedMessage(Chat $chat, ConnectedUser $disconnectedUser): void;
}