<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProjectPackageCommand
 *
 * @property integer $id
 * @property string $command
 * @property integer $project_package_command_type_id
 * @property integer $project_package_id
 * @property-read \imbalance\Models\ProjectPackage $projectPackage
 * @property-read \imbalance\Models\ProjectPackageCommandType $commandType
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackageCommand whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackageCommand whereCommand($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackageCommand whereProjectPackageCommandTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackageCommand whereProjectPackageId($value)
 * @mixin \Eloquent
 * @property integer $order
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackageCommand whereOrder($value)
 * @property string $run_on
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackageCommand whereRunOn($value)
 */
class ProjectPackageCommand extends Model
{
    protected $table = 'project_package_command';

    public $timestamps = false;

    protected $fillable = [
        'command',
        'order',
        'run_on',
        'project_package_command_type_id',
        'project_package_id'
    ];

    protected $guarded = [];

    public function projectPackage() {
        return $this->belongsTo('imbalance\Models\ProjectPackage', 'project_package_id');
    }

    public function commandType() {
        return $this->belongsTo('imbalance\Models\ProjectPackageCommandType', 'project_package_command_type_id');
    }

        
}