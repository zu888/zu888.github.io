<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RequestsFixture
 */
class RequestsFixture extends TestFixture
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
                'company_id' => 1,
                'request_type' => 'Lorem ipsum dolor sit amet',
                'request_text' => 'Lorem ipsum dolor sit amet',
                'created_at' => '2023-04-03 16:33:00',
                'approved_at' => '2023-04-03 16:33:00',
            ],
        ];
        parent::init();
    }
}
