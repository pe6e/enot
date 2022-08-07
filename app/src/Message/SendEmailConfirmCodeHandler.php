<?php

namespace App\Message;

use App\Repository\ChangeRequestRepository;
use App\Service\EmailSendService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendEmailConfirmCodeHandler extends SendConfirmCodeHandler
{
    public function __construct(EmailSendService $sendService, ChangeRequestRepository $repository)
    {
        parent::__construct($sendService, $repository);
    }
}