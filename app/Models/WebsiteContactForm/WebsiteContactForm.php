<?php

namespace App\Models\WebsiteContactForm;

use App\Models\BaseModel;
use App\Models\Component\ComponentContactFormType;
use App\Parser\WebsiteContactForm\WebsiteContactFormParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteContactForm extends BaseModel
{
    use SoftDeletes;

    protected $table = 'website_contact_forms';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';


    protected $casts = [
        'isRead' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = WebsiteContactFormParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function getFormType(): BelongsTo
    {
        return $this->belongsTo(ComponentContactFormType::class, 'formTypeId');
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
                    $search->where('name', 'LIKE', "%$request->search%")
                        ->orWhere('email', 'LIKE', "%$request->search%")
                        ->orWhere('subject', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('formTypeId') && $request->formTypeId) {
                $query->where('formTypeId', $request->formTypeId);
            }

        })->orderBy('id', 'DESC');
    }
}