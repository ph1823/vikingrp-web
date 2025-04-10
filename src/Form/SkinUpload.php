<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class SkinUpload extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'skinImage',
                VichImageType::class,
                [
                    'image_uri' => false,
                    'allow_delete' => false,
                    'download_label' => 'Télécharger le skin',
                    'constraints' => [
                        new Image([
                            'maxSize' => '3M',
                            'maxSizeMessage' => 'Le fichier est trop lourd ({{ size }} {{ suffix }}). Taille maximum : {{ limit }} {{ suffix }}.',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Le fichier doit être une image valide (jpeg, png).',
                        ]),
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
