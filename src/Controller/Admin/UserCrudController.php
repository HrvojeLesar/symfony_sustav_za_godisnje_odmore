<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('firstName')->setRequired(true),
            TextField::new('lastName')->setRequired(true),
            EmailField::new('email')->setRequired(true),
            ChoiceField::new('roles')->setChoices(Role::roleChoices())->allowMultipleChoices(),
            AssociationField::new('workplace')->setRequired(true),
        ];
    }
}
