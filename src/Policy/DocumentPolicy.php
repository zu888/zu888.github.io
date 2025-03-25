<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Document;
use Authorization\IdentityInterface;
use Cake\Datasource\FactoryLocator;

/**
 * Document policy
 */
class DocumentPolicy
{
    /**
     * Check if $user can add Document
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Document $document
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Document $document)
    {

        return true;
    }

    /**
     * Check if $user can edit Document
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Document $document
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Document $document)
    {
        if ($document->related_project_id) {
            $project = FactoryLocator::get('Table')->get('Projects')->get($document->related_project_id);
            if ($project->builder_id == $user->id) {
                return true;
            } else {
                return false;
            }
        }

        if ($document->related_company_id) {
            $documentCompany = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->select('company_id')->where([
                'company_id' => $document->related_company_id,
                'user_id' => $user->id,
                'is_company_admin' => 1
            ])->first();
            if ($documentCompany) {
                return true;
            } else {
                return false;
            }
        }

        if ($document->related_user_id) {
            if ($document->related_user_id == $user->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if $user can delete Document
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Document $document
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Document $document)
    {
        if ($document->related_project_id) {
            $project = FactoryLocator::get('Table')->get('Projects')->get($document->related_project_id);
            if ($project->builder_id == $user->id || $user->role == 'Contractor') {
                return true;
            } else {
                return false;
            }
        }

        if ($document->related_company_id) {
            $documentCompany = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->select('company_id')->where([
                'company_id' => $document->related_company_id,
                'user_id' => $user->id,
                'is_company_admin' => 1
            ])->first();
            if ($documentCompany) {
                return true;
            } else {
                return false;
            }
        }

        if ($document->related_user_id) {
            if ($document->related_user_id == $user->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if $user can view Document
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Document $document
     * @return bool
     */
    public function canView(IdentityInterface $user, Document $document)
    {
        $role = $user->role;
        $uploaderId = $document->uploaded_user_id;
        if ($role == 'Builder'|| $user->id == $uploaderId|| $role == 'Contractor' || $role == 'On-site Worker') {
            return true;
        }
        return false;

        if ($document->related_project_id) {
            $documentProject = FactoryLocator::get('Table')->get('Inductions')->find()->select('user_id')->where([
                'user_id' => $user->id,
                'project_id' => $document->related_project_id
            ])->first();

            $project = FactoryLocator::get('Table')->get('Projects')->get($document->related_project_id);

            $contractor = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->where([
                'user_id' => $user->id,
                'is_company_admin' => 1
            ])->first();
            $contracted = FALSE;
            if ($contractor) {
                $contracted = FactoryLocator::get('Table')->get('CompaniesProjects')->find()->where([
                    'company_id' => $contractor->company_id,
                    'project_id' => $document->related_project_id
                ])->first();
            }
            $authView = false;


            $auth_type =  $document->auth_type;
            if ($auth_type == 1) {
                if ($user->role == 'Admin') {
                    $authView = true;
                };
            } else if ($auth_type == 2) {
                $auth_value = explode(';', $document->auth_value);
                if (in_array($user->email, $auth_value)) {
                    $authView = true;
                }
            } else if ($auth_type == 3) {
                $auth_value = explode(',', $document->auth_value);
                if (in_array($user->role, $auth_value)) {
                    $authView = true;
                }
            }



            if ($documentProject || $project->builder_id == $user->id || $contracted || $authView) {
                return true;
            } else {
                return false;
            }
        }

        if ($document->related_company_id) {
            if ($document->worker_accessible == 0) {
                if ($user->role == 'On-site Worker') {
                    return false;
                }
            }

            $documentCompany = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->select('company_id')->where([
                'company_id' => $document->related_company_id,
                'user_id' => $user->id
            ])->first();
            if ($documentCompany) {
                return true;
            } else {
                return false;
            }
        }

        if ($document->related_user_id) {
            $employeeCompany = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->select('company_id')->where([
                'user_id' => $document->related_user_id
            ])->first();
            $currentUserCompany = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->select('company_id')->where([
                'user_id' => $user->id,
                'is_company_admin' => 1
            ])->first();

            if ($currentUserCompany == $employeeCompany || $document->related_user_id == $user->id) {
                return true;
            }
        }

        return false;
    }

    public function canIndex(IdentityInterface $user, Document $document)
    {
        return false;
    }

    public function canreview(IdentityInterface $user, Document $document)
    {
        return true;
    }

    public function canUnarchived(IdentityInterface $user, Document $document)
    {
        return true;
    }
}
