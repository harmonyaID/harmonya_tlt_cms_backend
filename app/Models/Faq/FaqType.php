<?php

namespace App\Models\Faq;

use App\Models\BaseModel;
use App\Parser\Faq\FaqTypeParser;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaqType extends BaseModel
{
    use SoftDeletes;

    protected $table = 'faq_types';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'order' => 'integer',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = FaqTypeParser::class;

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

        })->orderBy('order', 'ASC')->orderBy('id', 'ASC');
    }
}
