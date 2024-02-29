<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\WikiCategory;
use App\Entity\WikiPage;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class WikiCatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WikiCategory::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('cat_url'),
            TextareaField::new('catImage')
                ->setFormType(VichImageType::class)
                ->setLabel('Icone'),
            AssociationField::new('pages')
        ];
    }

}
