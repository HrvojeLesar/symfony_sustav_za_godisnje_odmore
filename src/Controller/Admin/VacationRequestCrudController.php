<?php

namespace App\Controller\Admin;

use App\Entity\VacationRequest;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class VacationRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return VacationRequest::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
