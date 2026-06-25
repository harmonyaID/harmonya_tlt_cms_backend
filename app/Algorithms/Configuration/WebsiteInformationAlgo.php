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

    /**
     * @param WebsiteInformation|int|null $websiteInformation
     */
    public function __construct(protected WebsiteInformation|int|null $websiteInformation = null)
    {
        if (is_int($this->websiteInformation)) {
            $this->websiteInformation = WebsiteInformation::find($this->websiteInformation);
            if (!$this->websiteInformation) {
                errLanguageGet();
            }
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
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
