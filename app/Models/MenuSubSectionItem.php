<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MenuSubSectionItem
 *
 * @property integer $id
 * @property string $name
 * @property string $link
 * @property integer $menu_sub_section_id
 * @property-read \imbalance\Models\MenuSubSection $menuSubSection
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\MenuSubSectionItem whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\MenuSubSectionItem whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\MenuSubSectionItem whereLink($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\MenuSubSectionItem whereMenuSubSectionId($value)
 * @mixin \Eloquent
 * @property string $component
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\MenuSubSectionItem whereComponent($value)
 */
class MenuSubSectionItem extends Model
{
    protected $table = 'menu_sub_section_item';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'link',
        'menu_sub_section_id'
    ];

    protected $guarded = [];

    public function menuSubSection() {
        return $this->belongsTo('imbalance\Models\MenuSubSection');
    }

        
}