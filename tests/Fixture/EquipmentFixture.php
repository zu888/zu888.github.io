<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * EquipmentFixture
 */
class EquipmentFixture extends TestFixture
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
                'name' => 'Lorem ipsum dolor sit amet',
                'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'equipment_type' => 'Lorem ipsum dolor sit amet',
                'is_licensed' => 1,
                'hired_from_date' => '2023-09-08',
                'hired_until_date' => '2023-09-08',
                'worker_accessible' => 1,
                'related_project_id' => 1,
                'related_company_id' => 1,
                'related_user_id' => 1,
                'auth_type' => 1,
                'auth_value' => 'Lorem ipsum dolor sit amet',
                'image' => 'Lorem ipsum dolor sit amet',
                'image_date' => '2023-09-08',
            ],
        ];
        parent::init();
    }
}
