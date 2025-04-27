<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\WikiPage;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class WikiPageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WikiPage::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('url'),
            TextareaField::new('iconImage')->setFormType(VichImageType::class),
            CollectionField::new('images')->useEntryCrudForm(WikiImageCrudController::class),
            AssociationField::new('wikiCategory')
                //->setFormType(VichImageType::class)
        ];
    }

}
