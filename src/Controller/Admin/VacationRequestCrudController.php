<?php

namespace App\Controller\Admin;

use App\Entity\VacationRequest;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class VacationRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return VacationRequest::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user'),
            DateField::new('fromDate'),
            DateField::new('toDate'),
            AssociationField::new('annualVacation'),
            AssociationField::new('approvedByTeamLead'),
            AssociationField::new('approvedByProjectLead'),
            BooleanField::new('isApprovedByTeamLead')->hideOnForm(),
            BooleanField::new('isApprovedByProjectLead')->hideOnForm(),
            DateTimeField::new('approvalStatusTeamUpdatedAt')->hideOnForm(),
            DateTimeField::new('approvalStatusProjectUpdatedAt')->hideOnForm(),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
        ];
    }
}
