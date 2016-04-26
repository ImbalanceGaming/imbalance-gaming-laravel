<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProjectPackageCommandType
 *
 * @property integer $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\ProjectPackageCommand[] $projectPackageCommands
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackageCommandType whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackageCommandType whereName($value)
 * @mixin \Eloquent
 */
class ProjectPackageCommandType extends Model
{
    protected $table = 'project_package_command_type';

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    protected $guarded = [];

    public function projectPackageCommands() {
        return $this->hasMany('imbalance\Models\ProjectPackageCommand');
    }

        
}