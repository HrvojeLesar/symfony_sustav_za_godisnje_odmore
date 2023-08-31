<?php

namespace App\Controller\Admin;

use App\Entity\ProjectTeam;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ProjectTeamCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProjectTeam::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('project'),
            AssociationField::new('team'),
        ];
    }
}
