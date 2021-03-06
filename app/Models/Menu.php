<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Menu
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $placement
 * @property integer $module_section_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\MenuSubSection[] $subSections
 * @property-read \imbalance\Models\ModuleSection $moduleSection
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Menu whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Menu whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Menu whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Menu wherePlacement($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Menu whereModuleSectionId($value)
 * @mixin \Eloquent
 * @property string $link
 * @property string $component
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Menu whereLink($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Menu whereComponent($value)
 * @property integer $component_id
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Menu whereComponentId($value)
 */
class Menu extends Model
{
    protected $table = 'menu';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'placement',
        'module_section_id'
    ];

    protected $guarded = [];

    public function subSections() {
        return $this->hasMany('imbalance\Models\MenuSubSection');
    }

    public function moduleSection() {
        return $this->belongsTo('imbalance\Models\ModuleSection');
    }

    public function component() {
        return $this->belongsTo('imbalance\Models\Component');
    }
    
}