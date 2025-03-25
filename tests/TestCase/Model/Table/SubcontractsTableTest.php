<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SubcontractsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SubcontractsTable Test Case
 */
class SubcontractsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SubcontractsTable
     */
    protected $Subcontracts;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Subcontracts',
        'app.Projects',
        'app.Companies',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Subcontracts') ? [] : ['className' => SubcontractsTable::class];
        $this->Subcontracts = $this->getTableLocator()->get('Subcontracts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Subcontracts);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SubcontractsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SubcontractsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
