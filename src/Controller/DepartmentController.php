<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Department;
use App\Form\DepartmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class DepartmentController extends AbstractController
{
    #[Route('/department', name: 'department_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $departments = $entityManager->getRepository(Department::class)->findAll();
        return $this->render('department/index.html.twig', [
        //    'controller_name' => 'DepartmentController',
              'departments' => $departments,
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/department/new', name: 'department_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $department = new Department();
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($department);
            $entityManager->flush();

            return $this->redirectToRoute('department_index');
        }

        return $this->render('department/new.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/department/{id}/edit', name: 'department_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $department = $entityManager->getRepository(Department::class)->find($id);

    if (!$department) {
        throw $this->createNotFoundException('Aucun département trouvé pour l\'id '.$id);
    }
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('department_index');
        }

        return $this->render('department/edit.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/department/{id}/delete', name: 'department_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
    $department = $entityManager->getRepository(Department::class)->find($id);

    if (!$department) {
        throw $this->createNotFoundException('Aucun département trouvé pour l\'id '.$id);
    }
        if ($this->isCsrfTokenValid('delete'.$department->getId(), $request->request->get('_token'))) {
            $entityManager->remove($department);
            $entityManager->flush();
        }

        return $this->redirectToRoute('department_index');
    } 

}

