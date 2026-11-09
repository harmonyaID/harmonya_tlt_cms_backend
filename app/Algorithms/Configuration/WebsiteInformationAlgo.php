<?php

namespace App\Algorithms\Configuration;

use App\Models\Configuration\WebsiteInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebsiteInformationAlgo
{
    /**
     * @var string
     */
    protected string $newPassword = "";

    public function __construct(protected WebsiteInformation|int|null $websiteInformation = null)
    {
        if (is_int($this->websiteInformation)) {
            $this->websiteInformation = WebsiteInformation::find($this->websiteInformation);
            if (!$this->websiteInformation) {
                errLanguageGet();
            }
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->websiteInformation->update($request->all());
            });

            return success($this->websiteInformation);
        } catch (\Error $error) {
            exception($error);
        }
    }
}
