<?php

namespace App\Message;

use App\Repository\ChangeRequestRepository;
use App\Service\PhoneSendService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendPhoneConfirmCodeHandler extends SendConfirmCodeHandler
{
    public function __construct(PhoneSendService $sendService, ChangeRequestRepository $repository)
    {
        parent::__construct($sendService, $repository);
    }
}