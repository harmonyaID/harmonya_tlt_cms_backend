<?php

namespace App\Services\Constant\Menu;

use App\Services\Constant\BaseIDName;

class MenuItemType extends BaseIDName
{
    const MENU = 'Menu';
    const MENU_ID = 1;
    const MEGA_MENU = 'Mega Menu';
    const MEGA_MENU_ID = 2;

    const OPTION = [
        self::MENU_ID => self::MENU,
        self::MEGA_MENU_ID => self::MEGA_MENU,
    ];
}
