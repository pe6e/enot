<?php

namespace App\Service;

abstract class SendService implements SendServiceInterface
{
    public abstract function send(string $message, string $contact);
}