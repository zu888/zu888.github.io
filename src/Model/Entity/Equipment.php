<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Equipment Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $equipment_type
 * @property bool $is_licensed
 * @property \Cake\I18n\FrozenDate|null $hired_from_date
 * @property \Cake\I18n\FrozenDate|null $hired_until_date
 * @property int|null $worker_accessible
 * @property int|null $related_project_id
 * @property int|null $related_company_id
 * @property int|null $related_user_id
 * @property int $auth_type
 * @property string|null $auth_value
 * @property string|null $image
 * @property \Cake\I18n\FrozenDate|null $image_date
 *
 * @property \App\Model\Entity\Project $project
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\User $user
 */
class Equipment extends Entity
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
        'name' => true,
        'description' => true,
        'equipment_type' => true,
        'is_licensed' => true,
        'hired_from_date' => true,
        'hired_until_date' => true,
        'worker_accessible' => true,
        'related_project_id' => true,
        'related_company_id' => true,
        'related_user_id' => true,
        'auth_type' => true,
        'auth_value' => true,
        'image' => true,
        'image_date' => true,
        'project' => true,
        'company' => true,
        'user' => true,
    ];
}
