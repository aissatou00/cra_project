<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\LeaveType;
use App\Form\LeaveTypeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class LeaveTypeController extends AbstractController
{
    #[Route('/leave/type', name: 'leave_type_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $leaveTypes = $entityManager->getRepository(LeaveType::class)->findAll();

        return $this->render('leave_type/index.html.twig', [
            //'controller_name' => 'LeaveTypeController',
            'leaveTypes' => $leaveTypes,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/Leave/Type/new', name: 'leave_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $leaveType = new LeaveType();
        $form = $this->createForm(LeaveTypeFormType::class, $leaveType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($leaveType);
            $entityManager->flush();

            return $this->redirectToRoute('leave_type_index');
        }

        return $this->render('leave_type/new.html.twig', [
            'leaveType' => $leaveType,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/Leave/Type/{id}/edit', name: 'leave_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id , EntityManagerInterface $entityManager): Response
    {
        $leaveType = $entityManager->getRepository(LeaveType::class)->find($id);
    if (!$leaveType) {
        throw $this->createNotFoundException('Aucun type de congé trouvé pour l\'id '.$id);
    }
        $form = $this->createForm(LeaveTypeFormType::class, $leaveType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('leave_type_index');
        }

        return $this->render('leave_type/edit.html.twig', [
            'leaveType' => $leaveType,
            'form' => $form->createView(),
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/Leave/Type/{id}/delete', name: 'leave_type_delete', methods: ['GET','POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
    $leaveType = $entityManager->getRepository(LeaveType::class)->find($id);

    if (!$leaveType) {
        throw $this->createNotFoundException('Aucun type de congé trouvé pour l\'id '.$id);
    }

        // Supprimer les enregistrements Leave liés au LeaveType avant de supprimer le LeaveType lui-même
        $leaves = $entityManager->getRepository(\App\Entity\Leave::class)->findBy(['leavetype' => $leaveType]);
        foreach ($leaves as $leave) {
            $entityManager->remove($leave);
        }
        if ($this->isCsrfTokenValid('delete'.$leaveType->getId(), $request->request->get('_token'))) {
            $entityManager->remove($leaveType);
            $entityManager->flush();
        }
        

        return $this->redirectToRoute('leave_type_index');
    }
}
