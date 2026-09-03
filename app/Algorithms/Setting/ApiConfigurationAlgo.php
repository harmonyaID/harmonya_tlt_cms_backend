<?php

namespace App\Algorithms\Setting;

use App\Models\Setting\ApiConfiguration;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiConfigurationAlgo
{
    public function __construct(protected ApiConfiguration|int|null $apiConfiguration = null)
    {
        if (is_int($this->apiConfiguration)) {
            $this->apiConfiguration = ApiConfiguration::find($this->apiConfiguration);
            if (!$this->apiConfiguration) {
                errApiConfigurationGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->apiConfiguration = ApiConfiguration::create([
                    'name' => $request->name,
                    'key' => \Illuminate\Support\Str::slug($request->name, '_'),
                    'module' => $request->module,
                    'credentials' => $request->credentials ?? [],
                    'isActive' => $request->isActive,
                ] + created_by());

                if (!$this->apiConfiguration) {
                    errApiConfigurationSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->apiConfiguration)
                    ->setType(ActivityType::API_CONFIGURATION)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new API configuration: " . $this->apiConfiguration->name);
            });

            return success($this->apiConfiguration);
        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * Update the configuration. New credential values are merged into the existing
     * set (not replaced wholesale) so the admin doesn't have to re-enter every
     * field just to rotate a single secret.
     */
    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $data = $request->only('name', 'module', 'isActive');

                if ($request->has('credentials') && is_array($request->credentials)) {
                    $existing = $this->apiConfiguration->credentials ?? [];
                    $incoming = array_filter($request->credentials, fn ($v) => $v !== null && $v !== '');
                    $data['credentials'] = array_merge($existing, $incoming);
                }

                $this->apiConfiguration->update($data);

                activity()->setCausedBy()
                    ->setReference($this->apiConfiguration)
                    ->setType(ActivityType::API_CONFIGURATION)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update API configuration: " . $this->apiConfiguration->name);
            });

            return success($this->apiConfiguration);
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                if (!$this->apiConfiguration->delete()) {
                    errApiConfigurationDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->apiConfiguration)
                    ->setType(ActivityType::API_CONFIGURATION)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete API configuration: " . $this->apiConfiguration->name);
            });

            return success();
        } catch (\Error $error) {
            exception($error);
        }
    }
}
