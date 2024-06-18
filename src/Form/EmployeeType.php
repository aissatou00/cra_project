<?php

namespace App\Form;

use App\Entity\Employee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\Personne;
use App\Entity\Department;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mobile', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le numéro de mobile ne peut pas être vide.']),
                    new Assert\Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Le numéro de mobile ne doit contenir que des chiffres.'
                    ])
                ],
                'label' => 'Numéro de mobile',
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                new Assert\NotBlank(['message' => 'L\'adresse ne peut pas être vide.']),
                new Assert\Length(['max' => 255]),
            ],
            'label' => 'Adresse',
        ])
            //->add('birthday', DateType::class, ['label' => 'Anniversaire'])parceque avec cette méthode, la date d'anniversaire est limitée à 2019
           /// ->add('birthday', DateType::class, [
            ///    'widget' => 'single_text', // Utiliser le widget HTML5
                // Ajouter des attributs HTML5 pour contrôler les années, par exemple
           ///     'attr' => ['min' => '1900-01-01', 'max' => date('Y-m-d')],
           ///     'label' => 'Anniversaire',
            ///    ])

            //->add('department', TextType::class, ['label' => 'Département'])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'department', // ou tout autre propriété de l'entité Department que vous voulez afficher
                'label' => 'Département',
            ])
           // ->add('personne', PersonneType::class)
           // Ajouter les champs de l'entité Personne directement
            ->add('name', TextType::class, [
                'mapped' => false, // Ne pas mapper automatiquement
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom ne peut pas être vide.']),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z]+$/',
                        'message' => 'Le prénom ne doit contenir que des lettres.',
                    ])
                ],
                'label' => 'Prénom',
            ])
            ->add('email', EmailType::class, [
                'mapped' => false, // Ne pas mapper automatiquement
                'attr' => ['autocomplete' => 'off'], // Désactive l'autocomplétion
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email ne peut pas être vide.']),
                    new Assert\Email(['message' => 'Veuillez entrer une adresse email valide.']),
                ],
                'label' => 'Email',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false, // Ne pas mapper automatiquement
                'required' => true,
                'attr' => ['autocomplete' => 'new-password'], // Désactive l'autocomplétion et suggère un nouveau mot de passe
                'first_options' => [
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Le mot de passe ne peut pas être vide.']),
                        new Assert\Regex([
                            'pattern' => '/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}$/',
                            'message' => 'Le mot de passe doit contenir au moins 12 caractères, un nombre, une majuscule, et un caractère spécial.',
                        ]),
                    ],
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
            ]);


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $employee = $event->getData();
            $form = $event->getForm();
            
            // Pour un nouvel employé, ne pré-remplissez pas les champs email et password
            if (!$employee || null === $employee->getId()) {
            // Initialisez les champs 'name', 'email', et 'password' à des valeurs vides
                $form->get('name')->setData('');
                $form->get('email')->setData('');
                $form->get('password')->setData('');
            }
                    
            // Logique pour pré-remplir les champs lors de la modification d'un employé existant
            if ($employee && $employee->getId() !== null) {
                $form->get('name')->setData($employee->getName());
                $form->get('email')->setData($employee->getUsername());
                // Ne pas pré-remplir le champ mot de passe
                $form->get('password')->setData('');
            }
        });
            

        // Écouter l'événement de soumission pour mapper manuellement les données
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $employee = $event->getData();

            // Supposons que votre entité Employee a des propriétés héritées de Personne
            // Mettre à jour ces propriétés avec les valeurs du formulaire
            $employee->setName($form->get('name')->getData());
            $employee->setUsername($form->get('email')->getData());
            $employee->setPassword($form->get('password')->getData()); // Assurez-vous de hasher le mot de passe selon vos besoins

            // Mettre à jour l'objet Employee avec les nouvelles données
            $event->setData($employee);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
