<?php

namespace App\Message;

use App\Repository\ChangeRequestRepository;
use App\Service\TgSendService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendTgConfirmCodeHandler extends SendConfirmCodeHandler
{
    public function __construct(TgSendService $sendService, ChangeRequestRepository $repository)
    {
        parent::__construct($sendService, $repository);
    }
}