<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Leave; 

class FileDownloadController extends AbstractController
{
    private $security;
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    #[Route('/download/{filename}', name: 'download_file')]
    public function downloadFile(string $filename): Response
    {

        // Vérifiez si l'utilisateur est un administrateur
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas la permission d\'accéder à ce fichier.');
            return $this->redirectToRoute('leave_index'); // Redirigez vers une route appropriée
        }

        $filePath = $this->getParameter('medical_certificates_directory') . '/' . $filename;

        if (!file_exists($filePath)) {
            $this->addFlash('error', 'Le fichier n\'existe pas.');
            return $this->redirectToRoute('leave_index'); // Redirigez vers une route appropriée
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }
}