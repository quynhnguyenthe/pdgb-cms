<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class BaseModel extends Model
{
    use HasFactory;
    protected $guarded = [];
    /**
     * @var bool
     */
    private $isRouterAdmin;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = Schema::getColumnListing(static::getTable());
    }

    protected static function boot()
    {
        parent::boot();
        $user = Auth::guard('api')->user();
        if ($user) {
            $userId = $user->id;
            static::creating(
                function ($model) use ($userId) {
                    if (Schema::hasColumn($model->table, 'created_by')) {
                        $model->created_by = $userId;
                    }
                    if (Schema::hasColumn($model->table, 'updated_by')) {
                        $model->updated_by = $userId;
                    }
                }
            );
            static::updating(
                function ($model) use ($userId) {
                    if (Schema::hasColumn($model->table, 'updated_by')) {
                        $model->updated_by = $userId;
                    }
                }
            );
        }
    }
}
