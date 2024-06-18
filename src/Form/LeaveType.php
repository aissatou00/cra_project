<?php

namespace App\Form;

use App\Entity\Leave;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\LeaveType as LeaveTypeEntity; // Alias pour éviter le conflit
use Symfony\Component\Form\Extension\Core\Type\FileType;




class LeaveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('leavetype', TextType::class, ['label' => 'Type Congé',])
            ->add('leavetype', EntityType::class, [
                'class' => LeaveTypeEntity::class,
                'choice_label' => 'leavetype', // ou tout autre champ de l'entité LeaveType que vous voulez afficher
                'label' => 'Type Congé',
            ])
            ->add('Leave_from', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Début Congé',
                'required' => true,
                ])

            ->add('Leave_to', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fin Congé',
                'required' => true,])
           
           /* ->add('Leave_description', EntityType::class, [
                'class' => Leave::class,
                'choice_label' => 'Leave_description', // ou tout autre champ de l'entité LeaveType que vous voulez afficher
                'label' => 'Description',
            ])  */
            ->add('Leave_description', TextType::class, [
                'label' => 'Description',
                'required' => true,
                ])
        // champ certificat médical
            ->add('medicalCertificate', FileType::class, [
                'label' => ' ',
                'mapped' => false, // Si le fichier n'est pas directement stocké dans la base de données
                'required' => true,
                'attr' => ['style' => 'display:none;'], // Cachez le champ par défaut
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Leave::class,
        ]);
    }
}
