<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\request;
use Authorization\IdentityInterface;

/**
 * request policy
 */
class ProjectsUserPolicy
{
    /**
     * Check if $user can add projectsUser
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\projectsUser $projectsUser
     * @return bool
     */
    public function canAdd(IdentityInterface $user, projectsUser $projectsUser)
    {
        return true;
    }

    /**
     * Check if $user can edit projectsUser
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\projectsUser $projectsUser
     * @return bool
     */
    public function canEdit(IdentityInterface $user, projectsUser $projectsUser)
    {
    }

    /**
     * Check if $user can delete projectsUser
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\projectsUser $projectsUser
     * @return bool
     */
    public function canDelete(IdentityInterface $user, projectsUser $projectsUser)
    {
        $role = $user->role;
        if($role == 'Admin' || $role == 'Builder'){
            return true;
        }
        return false;
    }

    /**
     * Check if $user can view projectsUser
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\projectsUser $projectsUser
     * @return bool
     */
    public function canView(IdentityInterface $user, projectsUser $projectsUser)
    {
        return true;
    }

    public function canIndex(IdentityInterface $user, $projectsUser)
    {
        $role = $user->role;
        if($role == 'Admin' || $role == 'Builder'){
            return true;
        }
        return false;
    }

}
