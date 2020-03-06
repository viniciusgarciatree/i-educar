<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * LegacySchool
 *
 * @property LegacyInstitution $institution
 */
class LegacySchool extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.escola';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_escola';

    /**
     * @var array
     */
    protected $fillable = [
        'cod_escola',
        'ref_usuario_cad',
        'ref_usuario_exc',
        'ref_cod_instituicao',
        'ref_cod_escola_rede_ensino',
        'sigla',
        'data_cadastro',
        'data_exclusao',
        'ref_idpes',
        'ativo',
        'orgao_vinculado_escola',
        'situacao_funcionamento',
        'zona_localizacao',
        'localizacao_diferenciada',
        'dependencia_administrativa',
        'mantenedora_escola_privada',
        'categoria_escola_privada',
        'conveniada_com_poder_publico',
        'cnpj_mantenedora_principal',
        'regulamentacao',
        'esfera_administrativa',
        'unidade_vinculada_outra_instituicao',
        'inep_escola_sede',
        'codigo_ies',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_escola;
    }

    /**
     * Relacionamento com a instituição.
     *
     * @return BelongsTo
     */
    public function institution()
    {
        return $this->belongsTo(LegacyInstitution::class, 'ref_cod_instituicao');
    }

    /**
     * @return BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(LegacyPerson::class, 'ref_idpes');
    }

    /**
     * @return BelongsToMany
     */
    public function courses()
    {
        return $this->belongsToMany(
            LegacyCourse::class,
            'pmieducar.escola_curso',
            'ref_cod_escola',
            'ref_cod_curso'
        )->withPivot('ativo', 'anos_letivos');
    }

    /**
     * @return BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(LegacyOrganization::class, 'ref_idpes');
    }

    public function getNameAttribute()
    {
        return DB::selectOne('SELECT relatorio.get_nome_escola(:escola) AS nome', ['escola' => $this->id])->nome;
    }

    /**
     * @return HasOne
     */
    public function inep()
    {
        return $this->hasOne(SchoolInep::class, 'cod_escola', 'cod_escola');
    }

    /**
     * @return BelongsToMany
     */
    public function grades()
    {
        return $this->belongsToMany(
            LegacyLevel::class,
            'pmieducar.escola_serie',
            'ref_cod_escola',
            'ref_cod_serie'
        )->withPivot('ativo', 'anos_letivos');
    }

    /**
     * @return HasMany
     */
    public function schoolClasses()
    {
        return $this->hasMany(LegacySchoolClass::class, 'ref_ref_cod_escola');
    }

    /**
     * @return HasMany
     */
    public function schoolManagers()
    {
        return $this->hasMany('App\\Models\\SchoolManager', 'school_id');
    }
}
