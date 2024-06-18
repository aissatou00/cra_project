<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\PointageRepository;
use Symfony\Component\Security\Core\Security;
use App\Form\PointageType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Pointage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry; // For fetching data from database

use App\Entity\Employee;
use App\Entity\LeaveType;
use App\Repository\LeaveTypeRepository;



class PointageController extends AbstractController
{/*
    #[Route('/pointage', name: 'app_pointage')]
    public function index(): Response
    {
        return $this->render('pointage/index.html.twig', [
            'controller_name' => 'PointageController',
        ]);
    }*/



    #[Route('/pointage', name: 'pointage_index')]
    public function index(PointageRepository $pointageRepository, Security $security, ManagerRegistry $doctrine, LeaveTypeRepository $leaveTypeRepository, Request $request): Response
    {
        $user = $security->getUser();
        ///$year = date('Y');
        ///$month = date('m');
        // Récupérez le mois et l'année de la requête, utilisez les valeurs courantes par défaut
        $year = $request->query->get('year', date('Y'));
        $month = $request->query->get('month', date('m'));
        $filterName = $request->query->get('filterName', ''); // Récupération du filtre de nom



        /// Générez une liste de dates pour le mois courant ou le mois souhaité
        ///$dates = [];
        ///$today = new \DateTime(); // Ajustez selon les besoins
        ///for ($i = 1; $i <= $today->format('t'); $i++) {
            ///$dates[] = new \DateTime($today->format('Y-m-') . $i);
        ///}
        // Générez une liste de dates pour le mois choisi
        $dates = [];
        $dateObj = \DateTime::createFromFormat('Y-m', $year . '-' . $month);
        for ($i = 1; $i <= $dateObj->format('t'); $i++) {
            $dates[] = new \DateTime($year . '-' . $month . '-' . sprintf('%02d', $i));
        }

        // Déterminer si l'utilisateur est un admin
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());

        /// Récupérez les employés depuis la base de données
        $employees = $doctrine->getRepository(Employee::class)->findAll();

        /// Fetch leave types
        $leaveTypes = $leaveTypeRepository->findAll();



        if ($isAdmin) {
            // filtrer le nom d'un employé 
            if (!empty($filterName)) {
                $employees = $doctrine->getRepository(Employee::class)->findByName($filterName);
            }
            // Récupérer le pointage de tous les employés pour le mois et l'année en cours
            // Cette méthode hypothétique findByMonthAndYear doit être implémentée dans votre PointageRepository
            $pointages = $pointageRepository->findByMonthAndYear($year, $month);
        } else {
            // Récupérer le pointage de l'employé connecté
            // La méthode findByEmployeeAndMonth doit être adaptée ou créée dans votre PointageRepository
            $pointages = $pointageRepository->findByEmployeeAndMonth($user->getId(), $year, $month);
        }

        // Afficher les jours de la semaine

        $dateObj = \DateTime::createFromFormat('Y-m', $year . '-' . $month);
        $daysInMonth = (int)$dateObj->format('t');

        $listeDates = []; // Utilisez une nouvelle variable pour stocker les dates
        $joursDeLaSemaine = [ // Déplacez les traductions dans une nouvelle variable
            'Monday' => 'Lundi',
            'Tuesday' => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi',
            'Friday' => 'Vendredi',
            'Saturday' => 'Samedi',
            'Sunday' => 'Dimanche',
        ];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDate = $year . '-' . $month . '-' . sprintf('%02d', $i);
            $dayOfWeek = (new \DateTime($currentDate))->format('l'); // 'l' retourne le jour complet de la semaine(en anglais)
            $dayOfWeekFrench = $joursDeLaSemaine[$dayOfWeek]; // Traduit le jour complet de la semaine en français
            // Associer la date à son jour de la semaine en français
            $listeDates[$currentDate] = $dayOfWeekFrench;     
           
        }

        ///filtrer les employés par nom sur la page admin
       ///enregistrements de présence ou de travail pour les employés(page admin)  
        $pointages = $pointageRepository->findByMonthAndYear($year, $month);
        $pointagesFiltered = [];
        foreach ($pointages as $pointage) {
            $pointagesFiltered[$pointage->getEmployee()->getId()][$pointage->getDate()->format('Y-m-d')] = $pointage;
        }

        /// afficher les pointages déjà soumis(les pointages précédents) sur la page employé    
        /// Supposons que $user représente l'employé connecté
        $employeePointages = $pointageRepository->findBy(['employee' => $user]);

        // Préparer les données pour le template
        $pointages = $pointageRepository->findByEmployeeAndMonth($user->getId(), $year, $month);
        $pointagesByDate = [];
        foreach ($pointages as $pointage) {
            $dateKey = $pointage->getDate()->format('Y-m-d');
            $pointagesByDate[$dateKey] = [
                'isPresent' => $pointage->isIspresent(),
                'comment' => $pointage->getComments(),
                'leaveType' => $pointage->getCategorieAbsence() ? $pointage->getCategorieAbsence()->getId() : null,
            ];
        }





        return $this->render('pointage/index.html.twig', [
            
            'pointages' => $pointages,
            'isAdmin' => $isAdmin,
            'year' => $year,
            'month' => $month,
            'dates' => $listeDates, // passer la liste des dates traduites
            'employees' => $employees, /// Ajout de la variable employees
            'leaveTypes' => $leaveTypes, ///
            'filterName' => $filterName, /// Passez le filtre de nom à la vue
            'pointagesFiltered' => $pointagesFiltered, ///pointage filtre 
            'pointagesByDate' => $pointagesByDate, ///afficher les dates des pointages précédents
            
        ]);   
            
    }


   
    
    
    
    // Autres actions comme la création, la modification, la suppression...

/*
    #[Route('/pointage/store', name: 'pointage_store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $em): Response
    {
        // Utilisation de ->all() pour obtenir toutes les données POST(pour le bouton)
        $postData = $request->request->all();
        
        // Exemple de validation très basique : vérifie si au moins une case de présence a été cochée.
        // Adaptez cette logique en fonction de vos besoins spécifiques.
        if (empty($postData['attendance']) && empty($postData['leave'])) {
            // Si aucune donnée de présence ou d'absence, redirigez avec un message d'erreur.
            $this->addFlash('error', 'Aucune donnée de pointage soumise.');
            return $this->redirectToRoute('pointage_index');
        }
        
        // Initialisation sécurisée des variables comme tableaux (pour le bouton)
        $attendanceData = is_array($postData['attendance'] ?? null) ? $postData['attendance'] : [];
        $leaveData = is_array($postData['leave'] ?? null) ? $postData['leave'] : [];
        $commentsData = is_array($postData['comments'] ?? null) ? $postData['comments'] : [];
        $categorieAbsenceData = is_array($postData['categorieAbsence'] ?? null) ? $postData['categorieAbsence'] : [];

        foreach ($attendanceData as $employeeId => $dates) {
            if (!is_array($dates)) { // S'assure que $dates est bien un tableau (pour le bouton)
                continue;
            }
            foreach ($dates as $date => $present) {
                $employee = $em->getRepository(Employee::class)->find($employeeId);
                if (!$employee) {
                    // Gérer l'erreur si l'employé n'est pas trouvé
                    continue;
                }

                $dateTime = new \DateTime($date);
                $leaveType = null;
                if (isset($categorieAbsenceData[$employeeId][$date])) {
                    $leaveTypeId = $categorieAbsenceData[$employeeId][$date];
                    $leaveType = $em->getRepository(LeaveType::class)->find($leaveTypeId);
                    // Notez que si $leaveTypeId est vide ou null, $leaveType restera null.
                }

                // Vérifiez si un enregistrement Pointage existe déjà pour cette combinaison d'employé et de date
                 $pointage = $em->getRepository(Pointage::class)->findOneBy(['employee' => $employee, 'date' => $dateTime]);

                if (!$pointage) {
                    $pointage = new Pointage(); // Créez une nouvelle instance si aucun enregistrement existant n'est trouvé
                    $pointage->setEmployee($employee);
                    $pointage->setDate($dateTime);
                }

                // Définissez les propriétés de Pointage en fonction des données soumises
                $pointage->setIspresent($present === '1'); // Convertissez la valeur soumise en booléen
                $pointage->setCategorieAbsence($leaveType); // Peut être null si aucune catégorie d'absence n'est sélectionnée

                // Gérez les commentaires (assurez-vous que le champ est présent pour cette date et cet employé)
                if (isset($commentsData[$employeeId][$date])) {
                    $pointage->setComments($commentsData[$employeeId][$date]);
                }

                $em->persist($pointage);
            }
        }

        $em->flush(); // Appliquez les modifications en base de données

        // Ajoutez un message flash de succès
        $this->addFlash('success', 'Le pointage a été enregistré avec succès.');


        // Redirection vers la page de confirmation ou d'affichage des pointages
        return $this->redirectToRoute('pointage_index');
       

        
    }
     
*/

  

    //controller où la catégorie d'absence est null dans la bd et ne contient ici aucun id par défaut


  #[Route('/pointage/store', name: 'pointage_store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $postData = $request->request->all();
        $user = $security->getUser();
        $isAdmin = $security->isGranted('ROLE_ADMIN');

        // Assurez-vous que le mois et l'année sont bien passés et récupérés
        $month = $request->request->get('month', date('m'));
        $year = $request->request->get('year', date('Y'));

        if (empty($postData['attendance']) && empty($postData['leave'])) {
            $this->addFlash('error', 'Aucune donnée de pointage soumise.');
            return $this->redirectToRoute('pointage_index', ['month' => $month, 'year' => $year]);
        }

        if ($isAdmin) {
            foreach ($postData['attendance'] as $employeeId => $dates) {
                foreach ($dates as $date => $present) {
                    $employee = $em->getRepository(Employee::class)->find($employeeId);
                    if (!$employee) {
                        continue;
                    }

                    $dateTime = new \DateTime($date);
                    $pointage = $em->getRepository(Pointage::class)->findOneBy(['employee' => $employee, 'date' => $dateTime]) ?: new Pointage();
                    $pointage->setEmployee($employee);
                    $pointage->setDate($dateTime);
                    $pointage->setIspresent($present === '1');

                    $leaveType = isset($postData['leave'][$employeeId][$date]) && !empty($postData['categorieAbsence'][$employeeId][$date]) 
                                 ? $em->getRepository(LeaveType::class)->find($postData['categorieAbsence'][$employeeId][$date]) 
                                 : null;
                    $pointage->setCategorieAbsence($leaveType);

                    $comments = $postData['comments'][$employeeId][$date] ?? '';
                    $pointage->setComments($comments);

                    $em->persist($pointage);
                }
            }
        } else {
            // Vérifie si 'attendance' est défini pour éviter l'erreur de clé non définie
        if (isset($postData['attendance'])) {
            foreach ($postData['attendance'] as $date => $presentValue) {
                $dateTime = new \DateTime($date);
                $pointage = $em->getRepository(Pointage::class)->findOneBy(['employee' => $user, 'date' => $dateTime]) ?: new Pointage();
                $pointage->setEmployee($user);
                $pointage->setDate($dateTime);
                $pointage->setIspresent($presentValue === '1');

                // Gestion des absences avec vérification pour éviter l'erreur de clé non définie
                $leaveType = null;
                if (isset($postData['leave'][$date]) && $postData['leave'][$date] === '1' && isset($postData['categorieAbsence'][$date])) {
                    //  débogage de LeaveType
                    //dump($postData['categorieAbsence'][$date]);
                    $leaveType = $em->getRepository(LeaveType::class)->find($postData['categorieAbsence'][$date]);
                }
                $pointage->setCategorieAbsence($leaveType);

                $comments = $postData['comments'][$date] ?? '';
                $pointage->setComments($comments);

                $em->persist($pointage);
            }
        }

        // Vérifie si 'leave' est défini pour les cas où 'attendance' n'est pas défini mais 'leave' l'est
        if (isset($postData['leave'])) {
            foreach ($postData['leave'] as $date => $leaveValue) {
                //imposer la sélection d'une catégorie_absence
                if ($leaveValue === '1' && empty($postData['categorieAbsence'][$date])) {
                    $this->addFlash('error', "Vous devez sélectionner une catégorie d'absence pour la date : $date.");
                    return $this->redirectToRoute('pointage_index');
                }
                
                $dateTime = new \DateTime($date);
                // Vérifiez si un pointage existe déjà pour cette date
                $pointage = $em->getRepository(Pointage::class)->findOneBy(['employee' => $user, 'date' => $dateTime]) ?: new Pointage();
                $pointage->setEmployee($user);
                $pointage->setDate($dateTime);
        
                // Puisque 'leave' est défini, cela signifie l'absence de l'employé. 
                // Vous pouvez définir 'ispresent' à false ou le laisser non défini si votre logique d'application le permet
                $pointage->setIspresent($leaveValue === '1' ? false : true);
        
                // Gestion de la catégorie d'absence
                if (isset($postData['categorieAbsence'][$date])) {
                    $leaveTypeId = $postData['categorieAbsence'][$date];
                    $leaveType = $em->getRepository(LeaveType::class)->find($leaveTypeId);
                    $pointage->setCategorieAbsence($leaveType);
                }
        
                // Gestion des commentaires (si applicable)
                $comments = $postData['comments'][$date] ?? '';
                $pointage->setComments($comments);
        
                $em->persist($pointage);
            }
        }
    }

    $em->flush();
    $this->addFlash('success', 'Le pointage a été enregistré avec succès.');

    return $this->redirectToRoute('pointage_index', ['month' => $month, 'year' => $year]);
}
    

  
 /*   
    
//controlleur où categorie d'absence n'est pas null dans la bd et a l'id 6 dans ce controller comme id par défaut qui correspond à autre(dans type de congé)
#[Route('/pointage/store', name: 'pointage_store', methods: ['POST'])]
public function store(Request $request, EntityManagerInterface $em): Response
{
    $postData = $request->request->all();
    $user = $this->getUser(); // Récupère l'utilisateur connecté
    $isAdmin = $this->isGranted('ROLE_ADMIN'); // Vérifie si l'utilisateur est un administrateur
    

    // Assurez-vous d'avoir une catégorie d'absence par défaut dans votre base de données
    $defaultLeaveTypeId = 6; // Remplacez par l'ID réel
    $defaultLeaveType = $em->getRepository(LeaveType::class)->find($defaultLeaveTypeId);
    if (!$defaultLeaveType) {
        throw new \Exception("La catégorie d'absence par défaut est introuvable.");
    }
    
    if (!$user) {
        $this->addFlash('error', 'Utilisateur non identifié.');
        return $this->redirectToRoute('pointage_index');
    }
    
    if (empty($postData['attendance']) && empty($postData['leave'])) {
        $this->addFlash('error', 'Aucune donnée de pointage soumise.');
        return $this->redirectToRoute('pointage_index');
    }
    
    // Traitement pour les administrateurs
    if ($isAdmin) {
        
        
        foreach ($postData['attendance'] as $employeeId => $dates) {
            foreach ($dates as $date => $present) {
                $employee = $em->getRepository(Employee::class)->find($employeeId);
                if (!$employee) {
                    continue; // Ou gérez l'erreur de manière appropriée
                }

                $dateTime = new \DateTime($date);
                $pointage = $em->getRepository(Pointage::class)->findOneBy(['employee' => $employee, 'date' => $dateTime]);

                if (!$pointage) {
                    $pointage = new Pointage();
                    $pointage->setEmployee($employee);
                    $pointage->setDate($dateTime);
                }

                $pointage->setIspresent($present === '1');

                $leaveType = null;
                if (isset($postData['leave'][$employeeId][$date])) {
                    $leaveTypeId = $postData['leave'][$employeeId][$date];
                    $leaveType = $em->getRepository(LeaveType::class)->find($leaveTypeId);
                }
                $pointage->setCategorieAbsence($leaveType);

                $comments = $postData['comments'][$date] ?? '';
                $pointage->setComments($comments);

                $em->persist($pointage);
            }
        }
    } else {
        // Traitement pour les utilisateurs non administrateurs (employés)
        if (!$isAdmin) {
            if (isset($postData['attendance'])) { // Vérifiez si la clé 'attendance' existe
                foreach ($postData['attendance'] as $date => $presentValue) {
                    $dateTime = new \DateTime($date);
                    $pointage = $em->getRepository(Pointage::class)->findOneBy(['employee' => $user, 'date' => $dateTime]) ?: new Pointage();
                    $pointage->setEmployee($user);
                    $pointage->setDate($dateTime);
                    $pointage->setIspresent($presentValue === '1');

                    //Utilisez la catégorie d'absence par défaut si aucune catégorie spécifique n'est choisie
                    $leaveTypeId = $postData['categorieAbsence'][$date] ?? $defaultLeaveTypeId;
                    $leaveType = $em->getRepository(LeaveType::class)->find($leaveTypeId) ?: $defaultLeaveType;
                    $pointage->setCategorieAbsence($leaveType);

                    ///$leaveType = null;
                    ///if (isset($postData['categorieAbsence'][$date])) {
                    ///   $leaveTypeId = $postData['categorieAbsence'][$date];
                    ///   $leaveType = $em->getRepository(LeaveType::class)->find($leaveTypeId);
                /// }
                    $pointage->setCategorieAbsence($leaveType); // Cela peut être `null` si aucune catégorie n'est sélectionnée

                    // Assurez-vous que les commentaires ne soient jamais null
                    $comments = $postData['comments'][$date] ?? '';
                    $pointage->setComments($comments);

                    $em->persist($pointage);
                }
            }
        }       
    }
            
    $em->flush();
    $this->addFlash('success', 'Le pointage a été enregistré avec succès.');
    
    return $this->redirectToRoute('pointage_index');
    }
  */
}




