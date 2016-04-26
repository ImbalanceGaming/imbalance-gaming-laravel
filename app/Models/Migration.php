<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Migration
 *
 * @property string $migration
 * @property integer $batch
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Migration whereMigration($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Migration whereBatch($value)
 * @mixin \Eloquent
 */
class Migration extends Model
{
    protected $table = 'migrations';

    public $timestamps = false;

    protected $fillable = [
        'migration',
        'batch'
    ];

    protected $guarded = [];

        
}