<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\WikiImage;
use App\Entity\WikiPage;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class WikiImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WikiImage::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextareaField::new('wikiImage')->setFormType(VichImageType::class),
        ];
    }

}
