<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatController extends AbstractController
{
    #[Route('recruteur/startChat/{id}', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $em, UserRepository $userRepository, $id)
    {
        $csrfToken = $request->request->get('csrf_token');

        // Vérifiez si le jeton CSRF est valide
        if (!$this->isCsrfTokenValid('send_message', $csrfToken)) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('L\'utilisateur n\'a pas été trouvé.');
        }

        $message = new Message();
        $content = $request->request->get('content');
        $message->setContent(htmlspecialchars($content, ENT_QUOTES, 'UTF-8'));
        $message->setSender($this->getUser());
        $message->setReceiver($user);
        $em->persist($message);
        $em->flush();

        return $this->redirectToRoute('app_show_candidat', ['id' => $id]);
    }

    #[Route('/discussions', name: 'app_discussions')]
    public function discussions()
    {
        // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Récupérer tous les messages envoyés et reçus par l'utilisateur
        $sentMessages = $user->getSentMessages();
        $receivedMessages = $user->getReceivedMessages();

        // Fusionner les messages envoyés et reçus en une seule liste de "discussions"
        $discussions = array_merge($sentMessages->toArray(), $receivedMessages->toArray());

        // Créer une liste unique des utilisateurs avec lesquels l'utilisateur actuel a eu une discussion
        $discussedUsers = [];
        $lastMessages = [];
        foreach ($discussions as $message) {
            $otherUser = $message->getSender() == $user ? $message->getReceiver() : $message->getSender();

            // Si l'utilisateur n'est pas encore dans la liste ou si le message est plus récent que le dernier enregistré
            if (!array_key_exists($otherUser->getId(), $lastMessages) || $message->getCreatedAt() > $lastMessages[$otherUser->getId()]['date']) {
                $lastMessages[$otherUser->getId()] = [
                    'message' => $message->getContent(),
                    'date' => $message->getCreatedAt(),
                    'sender' => $message->getSender(),
                ];
            }

            if (!in_array($otherUser, $discussedUsers)) {
                $discussedUsers[] = $otherUser;
            }
        }

        // Renvoyer vers la vue avec toutes les données nécessaires
        return $this->render('chat/discussions.html.twig', [
            'discussedUsers' => $discussedUsers,
            'lastMessages' => $lastMessages,
        ]);
    }

    // Fonction pour afficher les messages d'une discussion
    #[Route('/discussions/{receiverId}', name: 'discussions')]
    public function chat(Request $request, UserRepository $userRepository, $receiverId = null, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Récupérer tous les messages envoyés et reçus par l'utilisateur
        $sentMessages = $user->getSentMessages();
        $receivedMessages = $user->getReceivedMessages();

        // Fusionner les messages envoyés et reçus en une seule liste de "discussions"
        $discussions = array_merge($sentMessages->toArray(), $receivedMessages->toArray());

        // Créer une liste unique des utilisateurs avec lesquels l'utilisateur actuel a eu une discussion
        $discussedUsers = [];
        $lastMessages = [];
        foreach ($discussions as $message) {
            $otherUser = $message->getSender() == $user ? $message->getReceiver() : $message->getSender();

            // Si l'utilisateur n'est pas encore dans la liste ou si le message est plus récent que le dernier enregistré
            if (!array_key_exists($otherUser->getId(), $lastMessages) || $message->getCreatedAt() > $lastMessages[$otherUser->getId()]['date']) {
                $lastMessages[$otherUser->getId()] = [
                    'message' => $message->getContent(),
                    'date' => $message->getCreatedAt(),
                    'sender' => $message->getSender(),
                ];
            }

            if (!in_array($otherUser, $discussedUsers)) {
                $discussedUsers[] = $otherUser;
            }
        }

        // Récupérer les messages de la discussion sélectionnée, si une discussion est sélectionnée
        $selectedMessages = null;
        if ($receiverId !== null) {
            $selectedMessages = array_filter($discussions, function($message) use ($receiverId) {
                return $message->getReceiver()->getId() == $receiverId || $message->getSender()->getId() == $receiverId;
            });

            // Convertir le tableau de messages en tableau pour pouvoir le trier
            $selectedMessages = array_values($selectedMessages);

            // Trier les messages par date de création
            usort($selectedMessages, function($a, $b) {
                return $a->getCreatedAt() <=> $b->getCreatedAt();
            });

            // Marquer tous les messages non lus comme lus
            foreach ($selectedMessages as $message) {
                if ($message->getReceiver() === $user && !$message->isIsRead()) {
                    $message->setIsRead(true);
                }
            }

            $entityManager->flush();
        }

        // Créer le formulaire pour un nouveau message
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Ajouter l'utilisateur actuel comme expéditeur du message
            $message->setSender($user);

            // Ajouter le destinataire du message
            $receiver = $userRepository->find($receiverId);
            $message->setReceiver($receiver);

            // Enregistrer le message dans la base de données
            $entityManager->persist($message);
            $entityManager->flush();

            // Rediriger vers la même page pour éviter de soumettre le formulaire deux fois
            return $this->redirectToRoute('discussions', ['receiverId' => $receiverId]);
        }

        // Renvoyer vers la vue avec toutes les données nécessaires
        return $this->render('chat/discussions.html.twig', [
            'discussedUsers' => $discussedUsers,
            'selectedMessages' => $selectedMessages,
            'lastMessages' => $lastMessages,
            'form' => $form->createView(),
        ]);
    }
}
