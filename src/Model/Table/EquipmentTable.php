<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Equipment Model
 *
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\BelongsTo $Projects
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Equipment newEmptyEntity()
 * @method \App\Model\Entity\Equipment newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Equipment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Equipment get($primaryKey, $options = [])
 * @method \App\Model\Entity\Equipment findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Equipment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Equipment[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Equipment|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Equipment saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Equipment[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Equipment[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Equipment[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Equipment[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class EquipmentTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('equipment');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Projects', [
            'foreignKey' => 'related_project_id',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'related_company_id',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'related_user_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('equipment_type')
            ->allowEmptyString('equipment_type');

        $validator
            ->boolean('is_licensed')
            ->notEmptyString('is_licensed');

        $validator
            ->date('hired_from_date')
            ->allowEmptyDate('hired_from_date');

        $validator
            ->date('hired_until_date')
            ->allowEmptyDate('hired_until_date');

        $validator
            ->allowEmptyString('worker_accessible');

        $validator
            ->nonNegativeInteger('related_project_id')
            ->allowEmptyString('related_project_id');

        $validator
            ->nonNegativeInteger('related_company_id')
            ->allowEmptyString('related_company_id');

        $validator
            ->nonNegativeInteger('related_user_id')
            ->allowEmptyString('related_user_id');

        $validator
            ->integer('auth_type')
            ->requirePresence('auth_type', 'create')
            ->notEmptyString('auth_type');

        $validator
            ->scalar('auth_value')
            ->maxLength('auth_value', 50)
            ->allowEmptyString('auth_value');


        $validator
            ->date('image_date')
            ->allowEmptyDate('image_date');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('related_project_id', 'Projects'), ['errorField' => 'related_project_id']);
        $rules->add($rules->existsIn('related_company_id', 'Companies'), ['errorField' => 'related_company_id']);
        $rules->add($rules->existsIn('related_user_id', 'Users'), ['errorField' => 'related_user_id']);

        return $rules;
    }
}
