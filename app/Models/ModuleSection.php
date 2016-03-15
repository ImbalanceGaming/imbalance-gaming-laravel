<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModuleSection
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $module_id
 * @property-read \imbalance\Models\Module $module
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Menu[] $menus
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Permission[] $permissions
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ModuleSection whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ModuleSection whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ModuleSection whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ModuleSection whereModuleId($value)
 * @mixin \Eloquent
 */
class ModuleSection extends Model
{
    protected $table = 'module_section';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'module_id'
    ];

    protected $guarded = [];

    public function module() {
        return $this->belongsTo('imbalance\Models\Module');
    }

    public function menus() {
        return $this->hasMany('imbalance\Models\Menu');
    }

    public function permissions() {
        return $this->belongsToMany('imbalance\Models\Permission', 'module_section_access');
    }
        
}