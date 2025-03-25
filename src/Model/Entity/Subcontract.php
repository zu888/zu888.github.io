<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Subcontract Entity
 *
 * @property int $id
 * @property int $project_id
 * @property int $parent_company_id
 * @property int $child_worker_id
 * @property string $description
 *
 * @property \App\Model\Entity\Project $project
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\User $user
 */
class Subcontract extends Entity
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
        'parent_company_id' => true,
        'child_worker_id' => true,
        'description' => true,
        'project' => true,
        'company' => true,
        'user' => true,
    ];
}
