<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Request Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $project_id
 * @property int|null $company_id
 * @property string $request_type
 * @property string $request_text
 * @property \Cake\I18n\FrozenTime|null $created_at
 * @property \Cake\I18n\FrozenTime|null $approved_at
 * @property int $removal_status
 *
 * @property \App\Model\Entity\User $user
 */
class Request extends Entity
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
        'user_id' => true,
        'project_id' => true,
        'company_id' => true,
        'request_type' => true,
        'request_text' => true,
        'created_at' => true,
        'approved_at' => true,
        'removal_status' => true,
        'user' => true,
        'reason'=> true,
    ];
}
