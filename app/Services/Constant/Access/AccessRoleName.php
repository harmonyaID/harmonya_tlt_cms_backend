<?php

namespace App\Services\Constant\Access;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AccessRoleName
{
    // Staff
    const STAFF_ADMIN = AccessGroup::STAFF . '.admin';
    const STAFF_ADMINISTRATOR = AccessGroup::STAFF . '.administrator';
    const STAFF_MANAGER = AccessGroup::STAFF . '.manager';
    const STAFF_SUPERVISOR = AccessGroup::STAFF . '.supervisor';
    const STAFF_EDITOR = AccessGroup::STAFF . '.editor';
    const STAFF_SUBS = AccessGroup::STAFF . '.subs';

    // Partner
    const PARTNER_ADMIN = AccessGroup::PARTNER . '.admin';
    const PARTNER_OPERATOR = AccessGroup::PARTNER . '.operator';
    const PARTNER_FINANCE = AccessGroup::PARTNER . '.finance';


    /** --- OPTION --- */

    const STAFF_OPTION = [
        self::STAFF_ADMIN,
        self::STAFF_ADMINISTRATOR,
        self::STAFF_MANAGER,
        self::STAFF_SUPERVISOR,
        self::STAFF_EDITOR,
        self::STAFF_SUBS,
    ];

    const PARTNER_OPTION = [
        self::PARTNER_ADMIN,
        self::PARTNER_OPERATOR,
        self::PARTNER_FINANCE,
    ];


    /** --- FUNCTIONS --- */

    public static function getStaff()
    {
        $options = self::STAFF_OPTION;

        $results = [];
        foreach ($options as $option) {
            $results[] = [
                'name' => $option,
                'display' => self::display($option)
            ];
        }

        return $results;
    }

    public static function getPartner()
    {
        $options = self::PARTNER_OPTION;

        $results = [];
        foreach ($options as $option) {
            $results[] = [
                'name' => $option,
                'display' => self::display($option)
            ];
        }

        return $results;
    }


    /** --- UNEXPORTED FUNCTIONS */

    protected static function display($permission)
    {
        $display = '';

        $names = explode('.', $permission);
        foreach ($names as $key => $name) {
            if ($key == 0) {
                continue;
            } elseif ($key > 1) {
                $display .= " ";
            }

            $display .= Str::title(str_replace('-', ' ', $name));
        }

        return $display;
    }

}