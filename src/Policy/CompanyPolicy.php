<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Company;
use Authorization\IdentityInterface;

/**
 * Company policy
 */
class CompanyPolicy
{
    /**
     * Check if $user can add Company
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Company $company
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Company $company)
    {
        $role = $user->role;
        if($role == 'Builder'|| $role == 'Admin'){
            return true;
        }
        return false;
    }

    /**
     * Check if $user can edit Company
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Company $company
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Company $company)
    {
        // not all users will be able to edit the company details
        // unless they are the company owners or creators
        // return true
        $role = $user->role;
        if($role == 'Admin'){
            return true;
        }
        return false;

        return true;
    }

    /**
     * Check if $user can delete Company
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Company $company
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Company $company)
    {
        $role = $user->role;
        if($role == 'Admin'){
            return true;
        }
        return false;
    }

    /**
     * Check if $user can view Company
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Company $company
     * @return bool
     */
    public function canView(IdentityInterface $user, Company $company)
    {
        return true;
    }

    public function canIndex(IdentityInterface $user, Company $company)
    {
        return true;
    }

    public function canMyindex(IdentityInterface $user, Company $company)
    {
        $role = $user->role;
        if($role != 'Admin'){
            return true;
        }
        return false;
    }


    public function canProjectPartner(IdentityInterface $user, Company $company){
        $role = $user->role;
        if($role == 'Builder'|| $role == 'Admin'){
            return true;
        }
        return false;
    }


    public function canStaff(IdentityInterface $user, Company $company)
    {
        return true;
    }

    public function canJoin(IdentityInterface $user, Company $company)
    {
        $role = $user->role;
        if($role != 'Admin'){
            return true;
        }
        return false;
    }

    public function canLeave(IdentityInterface $user, Company $company)
    {
        $role = $user->role;
        if($role != 'Admin'){
            return true;
        }
        return false;
    }
    public function canDeleteCompanyUser(IdentityInterface $user, Company $company)
    {
        $role = $user->role;
        if($role == 'Admin'){
            return true;
        }
        return false;
    }

    public function canremovePartner(IdentityInterface $user, Company $company)
    {

        return true;
    }
    public function canChange(IdentityInterface $user, Company $company)
    {
        if ($user->role == 'On-site Worker'){
            return true;
        } else {
            return false;
        }
    }

    public function canListCompaniesAjax(IdentityInterface $user, Company $company)
    {
        // Any logged-in user can access a list of companies
        // TODO: this may be required by non-logged-in user?
        return true;
    }

    public function canPending(IdentityInterface $user, Company $company)
    {
        return true;
    }
}
