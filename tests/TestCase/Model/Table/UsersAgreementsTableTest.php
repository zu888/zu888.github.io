<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersAgreementsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UsersAgreementsTable Test Case
 */
class UsersAgreementsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UsersAgreementsTable
     */
    protected $UsersAgreements;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.UsersAgreements',
        'app.Users',
        'app.Projects',
        'app.Documents',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('UsersAgreements') ? [] : ['className' => UsersAgreementsTable::class];
        $this->UsersAgreements = $this->getTableLocator()->get('UsersAgreements', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->UsersAgreements);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\UsersAgreementsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\UsersAgreementsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
