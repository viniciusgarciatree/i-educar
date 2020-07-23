<?php

namespace Tests\Unit\Educacenso\Validator;

use Faker\Factory;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\SchoolManagerAccessCriteria;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;
use iEducar\Modules\Educacenso\Validator\SchoolManagers;
use iEducar\Modules\ValueObjects\SchoolManagerValueObject;
use Tests\TestCase;

class SchoolManagerTest extends TestCase
{
    public function getInstance($arrayValueObject, $administrativeDependency = 1, $operatingSituation = 1)
    {
        return new SchoolManagers($arrayValueObject, $administrativeDependency, $operatingSituation);
    }

    public function testEmptyManagerShouldBeInvalid()
    {
        $this->markTestSkipped();

        $valueObject = $this->getFakeValueObject();
        $valueObject->individualId = null;
        $validator = $this->getInstance([$valueObject]);
        $this->assertFalse($validator->isValid());
    }

    public function testWithoutOrEmptyRoleShouldBeInvalid()
    {
        $valueObject = $this->getFakeValueObject();
        $valueObject->roleId = null;
        $validator = $this->getInstance([$valueObject]);
        $this->assertFalse($validator->isValid());
    }

    public function testRoleIsDirectorAndAccesCriteriaIsEmptyShouldBeInvalid()
    {
        $this->markTestSkipped();

        $valueObject = $this->getFakeValueObject();
        $valueObject->roleId = SchoolManagerRole::DIRETOR;
        $valueObject->accessCriteriaId = null;
        $validator = $this->getInstance([$valueObject]);
        $this->assertFalse($validator->isValid());

        $valueObject->accessCriteriaId = 1;
        $validator = $this->getInstance([$valueObject]);
        $this->assertTrue($validator->isValid());
    }

    public function testAccessCriteriaIsOtherAndDescriptionIsEmptyShouldBeInvalid()
    {
        $this->markTestSkipped();

        $valueObject = $this->getFakeValueObject();
        $valueObject->accessCriteriaId = SchoolManagerAccessCriteria::OUTRO;
        $valueObject->accessCriteriaDescription = null;
        $validator = $this->getInstance([$valueObject]);
        $this->assertFalse($validator->isValid());

        $valueObject->accessCriteriaDescription = '1';
        $validator = $this->getInstance([$valueObject]);
        $this->assertTrue($validator->isValid());
    }

    public function testRoleIsDirectorAndAccessTypeIsEmptyShouldBeInvalid()
    {
        $this->markTestSkipped();

        $valueObject = $this->getFakeValueObject();
        $valueObject->roleId = SchoolManagerRole::DIRETOR;
        $valueObject->linkTypeId = null;
        $validator = $this->getInstance([$valueObject], DependenciaAdministrativaEscola::FEDERAL);
        $this->assertFalse($validator->isValid());

        $valueObject->linkTypeId = 1;
        $validator = $this->getInstance([$valueObject], DependenciaAdministrativaEscola::FEDERAL);
        $this->assertTrue($validator->isValid());

        $valueObject->linkTypeId = null;
        $validator = $this->getInstance([$valueObject], DependenciaAdministrativaEscola::PRIVADA);
        $this->assertTrue($validator->isValid());
    }

    private function getFakeValueObject()
    {
        $faker = Factory::create();
        $valueObject = new SchoolManagerValueObject();
        $valueObject->individualId = $faker->randomNumber(1);
        $valueObject->roleId = $faker->randomNumber(1);
        $valueObject->accessCriteriaId = $faker->randomNumber(1);
        $valueObject->accessCriteriaDescription = $faker->word;
        $valueObject->linkTypeId = $faker->randomNumber(1);
        $valueObject->isChief = $faker->boolean;
        return $valueObject;
    }
}
