<?php

namespace App\Models\Faq;

use App\Models\BaseModel;
use App\Parser\Faq\FaqParser;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends BaseModel
{
    use SoftDeletes;

    protected $table = 'faqs';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'order'    => 'integer',
        'isActive' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = FaqParser::class;

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where(function ($search) use ($request) {
                    $search->where('question', 'LIKE', "%$request->search%")
                        ->orWhere('answer', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('isActive') && $request->isActive !== null) {
                $query->where('isActive', $request->isActive);
            }

        })->orderBy('order', 'ASC')->orderBy('id', 'ASC');
    }
}