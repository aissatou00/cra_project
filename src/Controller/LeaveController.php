<?php

namespace App\Controller;

use App\Entity\Leave;
use App\Form\LeaveType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\File\Exception\FileException;


class LeaveController extends AbstractController
{

    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }


    #[Route('/leave', name: 'leave_index')]
    public function index(): Response
    {
        $user = $this->security->getUser();
    
        // Vérifier si l'utilisateur actuel a un rôle d'admin
        if ($this->isGranted('ROLE_ADMIN')) {
            // Récupérer tous les congés pour les administrateurs
            $leaves = $this->entityManager->getRepository(Leave::class)->findAll();
        } else {
            // Si l'utilisateur est une instance de Employee, alors nous pouvons le récupérer directement
            if ($user instanceof \App\Entity\Employee) {
                $employee = $user;
            } else {
                throw $this->createNotFoundException('Employee entity not found.');
            }
    
            // Récupérer les congés uniquement pour l'employé actuel
            $leaves = $this->entityManager->getRepository(Leave::class)->findBy(['employee' => $employee]);
        }
    
        return $this->render('leave/index.html.twig', [
            'leaves' => $leaves,
        ]);
    }

    



    #[Route('/new', name: 'leave_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $leave = new Leave();
        $form = $this->createForm(LeaveType::class, $leave);
        $form->handleRequest($request);

        // Vérifier si les dates de congé sont vides
        if ($form->isSubmitted()) {
            $leaveFrom = $form->get('Leave_from')->getData();
            $leaveTo = $form->get('Leave_to')->getData();
        
            if (!$leaveFrom && !$leaveTo) {
                $this->addFlash('error', 'Veuillez sélectionner une date de début et de fin de congé.');
            } elseif (!$leaveFrom) {
                $this->addFlash('error', 'Veuillez sélectionner une date de début de congé.');
            } elseif (!$leaveTo) {
                $this->addFlash('error', 'Veuillez sélectionner une date de fin de congé.');
            } elseif ($form->isValid()) {
                //vérifier que la description n'est pas vide
                if (empty($leave->getLeaveDescription())) {
                    $this->addFlash('error', 'La description de la demande de congé est requise.');
                } else {
                     // Vérifier si le certificat médical est requis mais manquant
                    $leavetype = $form->get('leavetype')->getData(); // Assurez-vous que le champ leavetype est bien configuré dans votre formulaire
                    // Processus de téléversement du certificat médical
                    $medicalCertificateFile = $form->get('medicalCertificate')->getData();

                    // Si le type de congé nécessite un certificat médical mais aucun n'est téléversé
                    if (($leavetype->getId() == 5 || $leavetype->getId() == 6) && !$medicalCertificateFile) {
                        $this->addFlash('error', 'Veuillez téléverser votre certificat médical.');
                    } else {
        
                    if ($medicalCertificateFile) {
                        $newFilename = uniqid().'.'.$medicalCertificateFile->guessExtension();
                        try {
                            $medicalCertificateFile->move(
                                $this->getParameter('medical_certificates_directory'),
                                $newFilename
                            );
                            $leave->setMedicalCertificatePath($newFilename);
                        } catch (FileException $e) {
                            $this->logger->error('Erreur de téléchargement du fichier : '.$e->getMessage());
                            $this->addFlash('error', 'Une erreur s\'est produite lors du téléchargement du certificat médical. Veuillez réessayer.');
                        }
                        
                    }
        
                    // Définir une valeur par défaut pour leave_status si nécessaire
                    $leave->setLeaveStatus(1);
        
                    // Obtenez l'utilisateur connecté
                    $user = $this->security->getUser();
                    if (!$user instanceof \App\Entity\Employee) {
                        throw $this->createNotFoundException('Connected user is not an employee.');
                    }
        
                    // Associez directement l'utilisateur connecté à l'objet Leave
                    $leave->setEmployee($user);
        
                    // Sauvegarde dans la base de données
                    $this->entityManager->persist($leave);
                                $this->entityManager->flush();
                    // Ajouter le message de succès uniquement si aucune erreur n'a été ajoutée avant
                    $session = $request->getSession();
                    if (!$session->getFlashBag()->has('error')) {
                        $this->addFlash('success', 'La demande de congé a été enregistrée avec succès.');
                    }

                    return $this->redirectToRoute('leave_new');
                }
            }        
        }
    }    
        return $this->render('leave/new.html.twig', [
            'form' => $form->createView(),
        ]);
        
    }

    //mise à jour du statut de demande de congé (en cours, approuvé, rejeté)
    #[Route('/leave/update-status/{id}', name: 'leave_update_status', methods: ['POST'])]
    public function updateStatus(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        // Récupérer l'entité Leave en utilisant l'ID
        $leave = $entityManager->getRepository(Leave::class)->find($id);

        // Vérifier si l'entité Leave a été trouvée
        if (!$leave) {
            // Vous pouvez retourner une réponse d'erreur ou lancer une exception
            throw $this->createNotFoundException('No leave found for id '.$id);
        }

        // Récupérer le nouveau statut depuis la requête
        $newStatus = $request->request->get('status');

        // Mettre à jour le statut et sauvegarder les modifications
        if (in_array($newStatus, [1, 2, 3])) {
            $leave->setLeaveStatus($newStatus);
            $entityManager->flush();

            return $this->redirectToRoute('leave_index');
        } else {
            return $this->json(['error' => 'Invalid status'], 400);
        }
    }

//afficher un champs de commentaire après avoir sélectionné rejeté
    #[Route('/leave/update-comment/{id}', name: 'leave_update_comment', methods: ['POST'])]
    public function updateComment(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $leave = $entityManager->getRepository(Leave::class)->find($id);
        if (!$leave) {
            throw $this->createNotFoundException('No leave found for id '.$id);
        }

        $comment = $request->request->get('rejectionComment');
        if ($comment) {
            $leave->setRejectionComment($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire mis à jour avec succès.');
        } else {
            $this->addFlash('error', 'Le commentaire ne peut pas être vide.');
        }

        return $this->redirectToRoute('leave_index');
    }



    #[Route('/leave/delete/{id}', name: 'leave_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        // Récupérer l'entité Leave en utilisant l'ID
        $leave = $entityManager->getRepository(Leave::class)->find($id);

        // Vérifier si l'entité Leave a été trouvée
        if (!$leave) {
            throw $this->createNotFoundException('No leave found for id '.$id);
        }

        // Vérifier le token CSRF pour sécuriser la suppression
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete'.$id, $submittedToken)) {
            // Supprimer l'entité
            $entityManager->remove($leave);
            $entityManager->flush();

            // Ajouter un message flash pour informer de la suppression
            //$this->addFlash('success', 'Le congé a été supprimé avec succès.');
        } else {
            // Gérer l'échec de la vérification CSRF
            $this->addFlash('error', 'Token de sécurité invalide, impossible de supprimer le congé.');
        }

        return $this->redirectToRoute('leave_index');
    }





}
