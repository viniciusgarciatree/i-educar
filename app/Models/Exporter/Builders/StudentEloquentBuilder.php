<?php

namespace App\Models\Exporter\Builders;

use App\Support\Database\JoinableBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\JoinClause;

class StudentEloquentBuilder extends Builder
{
    use JoinableBuilder;

    /**
     * @param array $columns
     *
     * @return StudentEloquentBuilder
     */
    public function mother($columns)
    {
        $this->addSelect(
            $this->joinColumns('mother', $columns)
        );

        return $this->leftJoin('exporter_person as mother', function (JoinClause $join) {
            $join->on('exporter_student.mother_id', '=', 'mother.id');
        });
    }

    /**
     * @param array $columns
     *
     * @return StudentEloquentBuilder
     */
    public function father($columns)
    {
        $this->addSelect(
            $this->joinColumns('father', $columns)
        );

        return $this->leftJoin('exporter_person as father', function (JoinClause $join) {
            $join->on('exporter_student.father_id', '=', 'father.id');
        });
    }

    /**
     * @param array $columns
     *
     * @return StudentEloquentBuilder
     */
    public function guardian($columns)
    {
        $this->addSelect(
            $this->joinColumns('guardian', $columns)
        );

        return $this->leftJoin('exporter_person as guardian', function (JoinClause $join) {
            $join->on('exporter_student.guardian_id', '=', 'guardian.id');
        });
    }

    /**
     * @return StudentEloquentBuilder
     */
    public function benefits()
    {
        $this->addSelect(
            $this->joinColumns('benefits', ['benefits'])
        );

        return $this->leftJoin('exporter_benefits as benefits', function (JoinClause $join) {
            $join->on('exporter_student.student_id', '=', 'benefits.student_id');
        });
    }

    /**
     * @return StudentEloquentBuilder
     */
    public function disabilities()
    {
        $this->addSelect(
            $this->joinColumns('disabilities', ['disabilities'])
        );

        return $this->leftJoin('exporter_disabilities as disabilities', function (JoinClause $join) {
            $join->on('exporter_student.id', '=', 'disabilities.person_id');
        });
    }

    /**
     * @return StudentEloquentBuilder
     */
    public function phones()
    {
        $this->addSelect(
            $this->joinColumns('phones', ['phones'])
        );

        return $this->leftJoin('exporter_phones as phones', function (JoinClause $join) {
            $join->on('exporter_student.id', '=', 'phones.person_id');
        });
    }

    /**
     * @param array $columns
     *
     * @return StudentEloquentBuilder
     */
    public function place($columns)
    {
        $this->addSelect(
            $this->joinColumns('place', $columns)
        );

        return $this->leftJoin('person_has_place', function (JoinClause $join) {
            $join->on('exporter_student.id', '=', 'person_has_place.person_id');
        })->leftJoin('addresses as place', function (JoinClause $join) {
            $join->on('person_has_place.place_id', '=', 'place.id');
        });
    }
}
