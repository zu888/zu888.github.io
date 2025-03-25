<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\request;
use Authorization\IdentityInterface;
use Cake\Datasource\FactoryLocator;

/**
 * request policy
 */
class requestPolicy
{
    /**
     * Check if $user can add request
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\request $request
     * @return bool
     */
    public function canAdd(IdentityInterface $user, request $request)
    {
        $role = $user->role;
        if($role == 'Admin' || $role == 'Builder' ){
            return true;
        }
        return false;
    }

    /**
     * Check if $user can edit request
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\request $request
     * @return bool
     */
    public function canEdit(IdentityInterface $user, request $request)
    {
        $role = $user->role;
        if($role == 'Admin' || $role == 'Builder' ){
            return true;
        }
        return false;
    }

    public function canRemoval(IdentityInterface $user, request $request)
    {
        $role = $user->role;
        if($role == 'Admin' || $role == 'Builder' ){
            return true;
        }
        return false;
    }

    /**
     * Check if $user can delete request
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\request $request
     * @return bool
     */
    public function canDelete(IdentityInterface $user, request $request)
    {
        $role = $user->role;
        if($role == 'Admin' || $role == 'Builder' ){
            return true;
        }
        return false;
    }

    /**
     * Check if $user can view request
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\request $request
     * @return bool
     */
    public function canView(IdentityInterface $user, request $request)
    {
        $role = $user->role;
        if($role == 'Admin' || $role == 'Builder' ){
            return true;
        }
        return false;
    }

    public function canAddBuilderRequest(IdentityInterface $user, request $request)
    {
        $role = $user->role;
        if($role != 'Admin' && $role != 'Builder' ){
            return true;
        }
        return false;
    }

    public function canAddProjectRequest(IdentityInterface $user, request $request)
    {
        $role = $user->role;
        if($role == 'Admin' || $role == 'Builder' ){
            return true;
        }
        return false;
    }

    public function canAddCompanyRequest(IdentityInterface $user, request $request)
    {
        $role = $user->role;
        if($role == 'Admin' || $role == 'Builder' ){
            return true;
        }
        return false;
    }

    public function canIndex(IdentityInterface $user, request $request)
    {
        $role = $user->role;
        if($role == 'Admin' || $role == 'Builder'){
            return true;
        }
        return false;
    }

    public function canApproveRequest(IdentityInterface $user, request $request)
    {
        $role = $user->role;
        if($role == 'Admin' || $role == 'Builder' ){
            return true;
        }
        return false;
    }
}
