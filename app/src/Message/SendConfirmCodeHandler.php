<?php

namespace App\Message;

use App\Repository\ChangeRequestRepository;
use App\Service\SendServiceInterface;

class SendConfirmCodeHandler
{
    private SendServiceInterface $sendService;
    private ChangeRequestRepository $repository;

    public function __construct(SendServiceInterface $sendService, ChangeRequestRepository $repository)
    {
        $this->sendService = $sendService;
        $this->repository = $repository;
    }

    public function __invoke(SendConfirmCodeMessage $message): void
    {
        $this->repository->removeOldRequests();
        $this->sendService->send($message->getCode(), $message->getContact());
    }
}