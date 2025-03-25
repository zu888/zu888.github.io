<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Project;
use Authorization\IdentityInterface;
use Cake\Datasource\FactoryLocator;

/**
 * Project policy
 */
class ProjectPolicy
{
    /**
     * Check if $user can add Project
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Project $project
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Project $project)
    {
        $role = $user->role;
        if($role == 'Builder' || $role == 'Admin'){
            return true;
        }
        return false;
    }

    /**
     * Check if $user can edit Project
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Project $project
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Project $project)
    {
        $role = $user->role;
        if($role == 'Builder' || $role == 'Admin'){
            return true;
        }
        return false;    }

    /**
     * Check if $user can delete Project
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Project $project
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Project $project)
    {
        $role = $user->role;
        if($role == 'Admin'){
            return true;
        }
        return false;
    }

    /**
     * Check if $user can view Project
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Project $project
     * @return bool
     */
    public function canView(IdentityInterface $user, Project $project)
    {
        return true;
    }

    public function canIndex(IdentityInterface $user, Project $project)
    {
        if($user->role == "Visitor" || $user->role == "Client" || $user->role == "Consultant"){
            return false;
        }
        return true;
    }
    public function canAllprojects(IdentityInterface $user, Project $project)
    {
        $role = $user->role;
        if($role != 'Builder'){
            return true;
        }
        return false;
    }
    public function canpdf(IdentityInterface $user, Project $project)
    {
        if ($user->role == 'Admin') {
            return true;
        } elseif ($user->id == $project->builder_id) {
            return true;
        } else {
            return false;
        }
    }
    public function cangenerateqr(IdentityInterface $user, Project $project)
    {
        if ($user->role == 'Admin') {
            return true;
        } elseif ($user->id == $project->builder_id) {
            return true;
        } else {
            return false;
        }
    }
    public function canstaff(IdentityInterface $user, Project $project)
    {
        $company = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->select('company_id')->where([
            'user_id' => $user->id,
            'is_company_admin' => 1
        ])->first();
        $company_id = $company->company_id;
        $assignedProjectsIds = FactoryLocator::get('Table')->get('CompaniesProjects')->find()->select()->where([
            'company_id' => $company_id,
            'project_id' => $project->id
        ])->first();
        if ($user->id == $project->builder_id || $assignedProjectsIds) {
            return true;
        } else {
            return false;
        }
    }

    public function canRemoveContractor(IdentityInterface $user, Project $project)
    {
        if ($user->id == $project->builder_id) {
            return true;
        } else {
            return false;
        }
    }

    public function canCancelProject(IdentityInterface $user, Project $project)
    {
        $role = $user->role;
        if($role == 'Builder' || $role == 'Admin'){
            return true;
        }
        return false;
    }

    public function canCompleteProject(IdentityInterface $user, Project $project)
    {
        $role = $user->role;
        if($role == 'Builder'){
            return true;
        }
        return false;
    }

}
