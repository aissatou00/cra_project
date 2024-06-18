<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }


    #[Route('/admin/dashboard', name:'admin_dashboard')]
     
    public function adminDashboard()
    {
        // Logique pour le tableau de bord de l'administrateur
        return $this->render('dashboard/admin_dashboard.html.twig');
    }

    
     #[Route('/employee/dashboard', name:'employee_dashboard')]
     
    public function employeeDashboard()
    {
        // Logique pour le tableau de bord de l'employé
        return $this->render('dashboard/employee_dashboard.html.twig');
    }
}
