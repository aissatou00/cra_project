<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PersonneRepository;

class UserController extends AbstractController
{
   /* #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }*/

    /*
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, PersonneRepository $personneRepository)
    {
        // Supposons que vous récupériez l'ID de l'utilisateur et le nouveau mot de passe du formulaire
        $userId = $request->request->get('user_id');
        $newPlainPassword = $request->request->get('new_password');

        $user = $personneRepository->find($userId);
        if (!$user) {
            // Gérer l'erreur si l'utilisateur n'est pas trouvé
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $hashedPassword = $passwordHasher->hashPassword($user, $newPlainPassword);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        // Rediriger ou renvoyer une réponse après la mise à jour
    }*/
    #[Route('/user/change-password', name: 'user_change_password', methods: ['POST'])]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, PersonneRepository $personneRepository): Response
    {
        // Récupération de l'ID de l'utilisateur et du nouveau mot de passe depuis le formulaire
        $userId = $request->request->get('user_id');
        $newPlainPassword = $request->request->get('new_password');

        $user = $personneRepository->find($userId);
        if (!$user) {
            // Gérer l'erreur si l'utilisateur n'est pas trouvé
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('employee_index'); // Remplacer 'some_route' par votre route appropriée
        }

        // Hashage du nouveau mot de passe et mise à jour de l'utilisateur
        $hashedPassword = $passwordHasher->hashPassword($user, $newPlainPassword);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Mot de passe modifié avec succès.');
        return $this->redirectToRoute('employee_index'); // Remplacer 'some_route' par votre route appropriée ou renvoyer une réponse JSON pour les requêtes API
    }

    
}    