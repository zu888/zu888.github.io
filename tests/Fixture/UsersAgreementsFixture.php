<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersAgreementsFixture
 */
class UsersAgreementsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'user_id' => 1,
                'project_id' => 1,
                'document_id' => 1,
                'agreed_at' => 1691939502,
                'agreement_status' => 1,
            ],
        ];
        parent::init();
    }
}
