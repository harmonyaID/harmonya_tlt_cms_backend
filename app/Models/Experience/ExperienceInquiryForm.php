<?php

namespace App\Models\Experience;

use App\Models\BaseModel;
use App\Parser\Experience\ExperienceInquiryFormParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperienceInquiryForm extends BaseModel
{
    protected $table = 'experience_inquiry_forms';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $casts = [
        'totalGuests'          => 'integer',
        'accommodationNights'  => 'integer',
        'maxNightlyBudget'     => 'integer',
        'eventDate'            => 'date',
        self::CREATED_AT       => 'datetime',
        self::UPDATED_AT       => 'datetime',
    ];

    public $parserClass = ExperienceInquiryFormParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class, 'experienceId');
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
                $query->where(function ($q) use ($request) {
                    $q->where('fullName', 'LIKE', "%$request->search%")
                        ->orWhere('email', 'LIKE', "%$request->search%")
                        ->orWhere('phone', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('experienceId') && $request->experienceId) {
                $query->where('experienceId', $request->experienceId);
            }

        })->orderBy('id', 'DESC');
    }
}