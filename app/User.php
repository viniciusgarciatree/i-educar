<?php

namespace App;

use App\Models\LegacyEmployee;
use App\Models\LegacyPerson;
use App\Models\LegacyUserType;
use App\Models\School;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int            $id
 * @property string         $name
 * @property string         $email
 * @property string         $role
 * @property string         $login
 * @property string         $password
 * @property string         $created_at
 * @property LegacyUserType $type
 * @property LegacyEmployee $employee
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * @var string
     */
    protected $table = 'pmieducar.usuario';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_usuario';

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $timestamps = false;

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_usuario;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->person->name;
    }

    /**
     * @return string
     */
    public function getEmailAttribute()
    {
        return $this->employee->email;
    }

    /**
     * @return string
     */
    public function getLoginAttribute()
    {
        return $this->employee->login;
    }

    /**
     * @return string
     */
    public function getPasswordAttribute()
    {
        return $this->employee->password;
    }

    /**
     * @param string $password
     *
     * @return void
     */
    public function setPasswordAttribute($password)
    {
        $this->employee->password = $password;
        $this->employee->save();
    }

    /**
     * @return string
     */
    public function getRememberTokenAttribute()
    {
        return $this->employee->remember_token;
    }

    /**
     * @param string $token
     *
     * @return void
     */
    public function setRememberTokenAttribute($token)
    {
        $this->employee->remember_token = $token;
        $this->employee->save();
    }

    /**
     * @return string
     */
    public function getRoleAttribute()
    {
        return $this->type->name;
    }

    /**
     * @return string
     */
    public function getCreatedAtAttribute()
    {
        return $this->data_cadastro;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->type->level;
    }

    /**
     * @return BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(LegacyUserType::class, 'ref_cod_tipo_usuario', 'cod_tipo_usuario');
    }

    /**
     * @return BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(LegacyPerson::class, 'cod_usuario');
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->type->level === LegacyUserType::LEVEL_ADMIN;
    }

    /**
     * @return bool
     */
    public function isSchooling()
    {
        return $this->type->level === LegacyUserType::LEVEL_SCHOOLING;
    }

    /**
     * @return bool
     */
    public function isInstitutional()
    {
        return $this->type->level === LegacyUserType::LEVEL_INSTITUTIONAL;
    }

    /**
     * @return bool
     */
    public function isLibrary()
    {
        return $this->type->level === LegacyUserType::LEVEL_LIBRARY;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return boolval($this->employee->ativo && $this->ativo);
    }

    /**
     * @return bool
     */
    public function isInactive()
    {
        return !$this->isActive();
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        if (empty($date = $this->employee->data_expiracao)) {
            return false;
        }

        return now()->isAfter($date);
    }

    /**
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(LegacyEmployee::class, 'cod_usuario', 'ref_cod_pessoa_fj');
    }

    /**
     * @return BelongsToMany
     */
    public function processes()
    {
        return $this->belongsToMany(
            Menu::class,
            'pmieducar.menu_tipo_usuario',
            'ref_cod_tipo_usuario',
            'menu_id',
            'ref_cod_tipo_usuario',
            'id'
        )->withPivot(['visualiza', 'cadastra', 'exclui']);
    }

    /**
     * @return BelongsToMany
     */
    public function menu()
    {
        if(self::isAdmin()){
            return $this->processes()
                ->where('active', '=', 'true');
        }else{
            return $this->processes()
                ->where('active', '=', 'true')
                ->wherePivot('visualiza', 1)
                ->withPivot(['visualiza', 'cadastra', 'exclui']);
        }

    }

    /**
     * @return BelongsToMany
     */
    public function schools()
    {
        return $this->belongsToMany(
            School::class,
            'pmieducar.escola_usuario',
            'ref_cod_usuario',
            'ref_cod_escola',
            'cod_usuario',
            'cod_escola'
        );
    }
}
