<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TaskCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Task::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Задачи')
            ->setEntityLabelInSingular(
                fn (?Task $task, ?string $pageName) => $task ? $task->toString() : 'Задача'
            )
            ->renderContentMaximized()
            ->renderSidebarMinimized();
    }

//    public function configureActions(Actions $actions): Actions
//    {
//        return parent::configureActions($actions)
//            ->disable(Action::DETAIL);
//    }

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
