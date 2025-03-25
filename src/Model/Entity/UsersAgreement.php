<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UsersAgreement Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $project_id
 * @property int $document_id
 * @property \Cake\I18n\FrozenTime|null $agreed_at
 * @property bool $agreement_status
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Project $project
 * @property \App\Model\Entity\Document $document
 */
class UsersAgreement extends Entity
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
        'document_id' => true,
        'agreed_at' => true,
        'agreement_status' => true,
        'user' => true,
        'project' => true,
        'document' => true,
    ];
}
