<?php

namespace App\Models\Setting;

use App\Models\BaseModel;
use App\Parser\Setting\ApiConfigurationParser;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiConfiguration extends BaseModel
{
    use SoftDeletes;

    protected $table = 'api_configurations';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'credentials' => 'encrypted:array',
        'isActive' => 'boolean',
        'lastTestedAt' => 'datetime',
        'lastTestSuccessful' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = ApiConfigurationParser::class;

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |-------------------------------------------------------------------------
     */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where('name', 'LIKE', "%$request->search%");
            }

            if ($request->has('module') && $request->module) {
                $query->where('module', $request->module);
            }

            if ($request->has('isActive') && $request->isActive !== null) {
                $query->where('isActive', $request->isActive);
            }

        })->orderBy('id', 'ASC');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    /**
     * Look up a single credential value by key, e.g. credential('client_id').
     *
     * @param string $field
     *
     * @return mixed|null
     */
    public function credential(string $field)
    {
        return $this->credentials[$field] ?? null;
    }
}
