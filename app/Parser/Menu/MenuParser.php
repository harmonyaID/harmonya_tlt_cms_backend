<?php

namespace App\Parser\Menu;

use App\Services\Constant\Menu\MenuItemType;
use Logia\Core\Parser\BaseParser;

class MenuParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) return null;

        $items = [];
        foreach ($data->rootItems as $item) {
            $items[] = self::parseItem($item);
        }

        return [
            'id' => $data->id,
            'title' => $data->title,
            'handle' => $data->handle,
            'groupId' => $data->groupId,
            'locale' => $data->locale,
            'items' => $items,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) return null;

        return [
            'id' => $data->id,
            'title' => $data->title,
            'handle' => $data->handle,
            'groupId' => $data->groupId,
            'locale' => $data->locale,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function parseItem($item): array
    {
        $children = [];
        foreach ($item->children as $child) {
            $children[] = self::parseItem($child);
        }

        return [
            'id' => $item->id,
            'menuParent' => $item->menuParent,
            'menuLabel' => $item->menuLabel,
            'menuUrl' => $item->menuUrl,
            'menuOrder' => $item->menuOrder,
            'type' => MenuItemType::idName($item->typeId),
            'description' => $item->description,
            'featuredImage' => $item->featuredImageUrl(),
            'children' => $children,
        ];
    }
}