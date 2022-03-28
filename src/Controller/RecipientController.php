<?php

namespace App\Controller;

use App\Entity\Recipient;
use App\Form\RecipientType;
use App\Repository\RecipientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recipient')]
class RecipientController extends AbstractController
{
    #[Route('/{page<\d+>}', name: 'app_recipient_index', methods: ['GET'])]
    public function index(RecipientRepository $recipientRepository, int $page = 1): Response
    {
        return $this->render('recipient/index.html.twig', [
            'recipients' => $recipientRepository->findby([], null, 50),
        ]);
    }

    #[Route('/new', name: 'app_recipient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RecipientRepository $recipientRepository): Response
    {
        $recipient = new Recipient();
        $form = $this->createForm(RecipientType::class, $recipient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipientRepository->add($recipient);
            return $this->redirectToRoute('app_recipient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recipient/new.html.twig', [
            'recipient' => $recipient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recipient_show', methods: ['GET'])]
    public function show(Recipient $recipient): Response
    {
        return $this->render('recipient/show.html.twig', [
            'recipient' => $recipient,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recipient_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipient $recipient, RecipientRepository $recipientRepository): Response
    {
        $form = $this->createForm(RecipientType::class, $recipient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipientRepository->add($recipient);
            return $this->redirectToRoute('app_recipient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recipient/edit.html.twig', [
            'recipient' => $recipient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recipient_delete', methods: ['POST'])]
    public function delete(Request $request, Recipient $recipient, RecipientRepository $recipientRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recipient->getId(), $request->request->get('_token'))) {
            $recipientRepository->remove($recipient);
        }

        return $this->redirectToRoute('app_recipient_index', [], Response::HTTP_SEE_OTHER);
    }
}
