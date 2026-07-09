<?php

namespace App\Models\Menu;

use App\Models\BaseModel;
use App\Parser\Menu\MenuParser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Menu extends BaseModel
{
    use SoftDeletes;

    protected $table = 'menus';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'groupId' => 'integer',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = MenuParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menuId')->orderBy('menuOrder');
    }

    public function rootItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menuId')
            ->where('menuParent', 0)
            ->orderBy('menuOrder');
    }

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |-------------------------------------------------------------------------
     */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where(function ($search) use ($request) {
                    $search->where('title', 'LIKE', "%$request->search%")
                        ->orWhere('handle', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('locale') && $request->locale) {
                $query->where('locale', $request->locale);
            }

            if ($request->has('groupId') && $request->groupId) {
                $query->where('groupId', $request->groupId);
            }

        })->orderBy('groupId', 'ASC')->orderBy('id', 'ASC');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public static function generateHandle(string $title, ?Menu $ignore = null): string
    {
        $handle = Str::slug($title);
        $exists = true;
        $counter = 1;
        $newHandle = $handle;

        while ($exists) {
            $query = self::where('handle', $newHandle);
            if ($ignore) {
                $query->where('id', '!=', $ignore->id);
            }

            if ($query->count() > 0) {
                $newHandle = "$handle-$counter";
                $counter++;
            } else {
                $exists = false;
            }
        }

        return $newHandle;
    }
}