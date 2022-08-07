<?php

namespace App\Controller;

use App\Entity\ChangeRequest;
use App\Entity\User;
use App\Form\UserConfirmType;
use App\Form\UserType;
use App\Message\SendEmailConfirmCodeMessage;
use App\Message\SendPhoneConfirmCodeMessage;
use App\Message\SendTgConfirmCodeMessage;
use App\Repository\ChangeRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile", name="app_personal")
 */
class PersonalPageController extends AbstractController
{
    /**
     * @Route("/", name="_page")
     */
    public function index(): Response
    {
        return $this->render('personal_page/index.html.twig', [
            'controller_name' => 'PersonalPageController',
        ]);
    }

    /**
     * @Route("/edit", name="_edit")
     */
    public function edit(Request $request, EntityManagerInterface $em, ChangeRequestRepository $changeRequestRepository, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /* @var $user User */
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var $data User */
            $data = $form->getData();
            $code = random_int(1000000, 9999999);

            $changeRequestRepository->removeOldRequests();
            $changeRequest = $changeRequestRepository->findOneBy(['user' => $user]) ?? new ChangeRequest();
            $changeRequest->setUser($user);
            $changeRequest->setName($data->getName());
            $changeRequest->setEmail($data->getEmail());
            $changeRequest->setConfirmCode($code);
            $changeRequest->setDateCreate(new \DateTime());
            $em->persist($changeRequest);
            $em->flush($changeRequest);

            switch ($this->getParameter('send_type')) {
                case "tg":
                    $message = new SendTgConfirmCodeMessage($user->getEmail(), $code);
                    break;
                case "phone":
                    $message = new SendPhoneConfirmCodeMessage($user->getEmail(), $code);
                    break;
                default:
                    $message = new SendEmailConfirmCodeMessage($user->getEmail(), $code);
                    break;
            }
            $bus->dispatch($message);

            return $this->redirectToRoute('app_personal_confirm_page');
        }
        return $this->renderForm('personal_page/edit.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/edit/confirm_page", name="_confirm_page")
     */
    public function confirmChanges(EntityManagerInterface $em, Request $request, ChangeRequestRepository $changeRequestRepository): \Symfony\Component\HttpFoundation\RedirectResponse|Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $changeRequestRepository->removeOldRequests();
        /* @var $user User */
        $user = $this->getUser();

        $form = $this->createForm(UserConfirmType::class);
        $form->handleRequest($request);

        $changeRequest = $changeRequestRepository->findOneBy(['user' => $user]);
        if (!$changeRequest) {
            $form->addError(new FormError('Not found change request'));
        } else {
            //чтобы не ползать в логи за кодом
            dump($changeRequest->getConfirmCode());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data['code'] == $changeRequest->getConfirmCode()) {
                $user->setName($changeRequest->getName());
                $user->setEmail($changeRequest->getEmail());
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('app_personal_page');
            } else {
                $form->addError(new FormError('Invalid code!'));
            }
        }
        return $this->renderForm('personal_page/confirm.html.twig', [
            'form' => $form,
        ]);
    }
}
