<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CompaniesUsersFixture
 */
class CompaniesUsersFixture extends TestFixture
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
                'company_id' => 1,
                'user_id' => 1,
                'is_company_admin' => 1,
                'confirmed' => 1,
                'inducted' => '2022-11-22 23:49:35',
            ],
        ];
        parent::init();
    }
}
