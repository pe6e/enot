<?php

namespace App\Service;

interface SendServiceInterface
{
    public function send(string $message, string $contact);
}