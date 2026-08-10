<?php

namespace App\Models\Menu;

use App\Models\BaseModel;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MenuItem extends BaseModel
{
    use SoftDeletes;

    protected $table = 'menu_items';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'menuParent' => 'integer',
        'menuOrder' => 'integer',
        'typeId' => 'integer',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menuId');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menuParent', 'id')->orderBy('menuOrder');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'menuParent', 'id');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function featuredImageUrl()
    {
        if (!$this->featuredImage) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_MENU_ITEM . $this->featuredImage);
    }
}