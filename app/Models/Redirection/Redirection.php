<?php

namespace App\Models\Redirection;

use App\Models\BaseModel;
use App\Parser\Redirection\RedirectionParser;
use Illuminate\Database\Eloquent\SoftDeletes;

class Redirection extends BaseModel
{
    use SoftDeletes;

    protected $table = 'redirections';

    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'statusCode' => 'integer',
        'isActive' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = RedirectionParser::class;


    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if (
                $request->has('search') &&
                strlen($request->search) > 1
            ) {
                $query->where(function ($search) use ($request) {
                    $search->where(
                        'name',
                        'LIKE',
                        "%{$request->search}%"
                    )
                        ->orWhere(
                            'sourceUrl',
                            'LIKE',
                            "%{$request->search}%"
                        )
                        ->orWhere(
                            'targetUrl',
                            'LIKE',
                            "%{$request->search}%"
                        );
                });
            }

            if (
                $request->has('statusCode') &&
                $request->statusCode
            ) {
                $query->where(
                    'statusCode',
                    $request->statusCode
                );
            }

            if (
                $request->has('isActive') &&
                $request->isActive !== null
            ) {
                $query->where(
                    'isActive',
                    $request->isActive
                );
            }
        })->orderBy('id', 'DESC');
    }
}
