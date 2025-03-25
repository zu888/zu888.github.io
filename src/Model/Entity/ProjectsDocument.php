<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProjectsDocument Entity
 *
 * @property int $id
 * @property int $project_id
 * @property int $document_id
 * @property int|null $company_id
 * @property int|null $user_id
 * @property string $status
 * @property int|null $auth_type
 * @property string|null $auth_value
 *
 * @property \App\Model\Entity\Project $project
 * @property \App\Model\Entity\Document $document
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\User $user
 */
class ProjectsDocument extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'project_id' => true,
        'document_id' => true,
        'company_id' => true,
        'user_id' => true,
        'status' => true,
        'auth_type' => true,
        'auth_value' => true,
        'project' => true,
        'document' => true,
        'company' => true,
        'user' => true,
    ];
}
