<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MenuSubSection
 *
 * @property integer $id
 * @property string $name
 * @property integer $menu_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\MenuSubSectionItem[] $items
 * @property-read \imbalance\Models\Menu $menu
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\MenuSubSection whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\MenuSubSection whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\MenuSubSection whereMenuId($value)
 * @mixin \Eloquent
 * @property string $link
 * @property string $component
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\MenuSubSection whereLink($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\MenuSubSection whereComponent($value)
 * @property integer $component_id
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\MenuSubSection whereComponentId($value)
 */
class MenuSubSection extends Model
{
    protected $table = 'menu_sub_section';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'menu_id'
    ];

    protected $guarded = [];

    public function items() {
        return $this->hasMany('imbalance\Models\MenuSubSectionItem');
    }

    public function menu() {
        return $this->belongsTo('imbalance\Models\Menu');
    }

    public function component() {
        return $this->belongsTo('imbalance\Models\Component');
    }

        
}