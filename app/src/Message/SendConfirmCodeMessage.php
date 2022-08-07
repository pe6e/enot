<?php

namespace App\Message;

class SendConfirmCodeMessage
{
    private string $contact;
    private int $code;

    public function __construct(string $contact, int $code)
    {
        $this->contact = $contact;
        $this->code = $code;
    }

    public function getContact(): string
    {
        return $this->contact;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}