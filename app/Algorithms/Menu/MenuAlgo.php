<?php

namespace App\Algorithms\Menu;

use App\Models\Menu\Menu;
use App\Models\Menu\MenuItem;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuAlgo
{
    public function __construct(protected Menu|int|null $menu = null)
    {
        if (is_int($this->menu)) {
            $this->menu = Menu::find($this->menu);
            if (!$this->menu) errMenuGet();
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->menu = Menu::create([
                    'title' => $request->title,
                    'handle' => Menu::generateHandle($request->handle ?: $request->title),
                    'groupId' => $request->groupId ?? 0,
                    'locale' => $request->locale,
                ] + created_by());

                if (!$this->menu) errMenuSave();

                if ($request->has('items') && is_array($request->items)) {
                    $this->syncItems($request->items);
                }

                activity()->setCausedBy()->setReference($this->menu)
                    ->setType(ActivityType::MENU)->setAction(ActivityAction::CREATE)
                    ->log("Enter new menu: " . $this->menu->title);

            });

            return success($this->menu->load('rootItems.children'));
        } catch (\Error $error) { exception($error); }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->menu->update([
                    'title' => $request->title,
                    'handle' => Menu::generateHandle($request->handle ?: $request->title, $this->menu),
                    'groupId' => $request->groupId ?? $this->menu->groupId,
                    'locale' => $request->locale,
                ]);

                if ($request->has('items') && is_array($request->items)) {
                    // hapus semua item lama lalu sync ulang
                    MenuItem::where('menuId', $this->menu->id)->delete();
                    $this->syncItems($request->items);
                }

                activity()->setCausedBy()->setReference($this->menu)
                    ->setType(ActivityType::MENU)->setAction(ActivityAction::UPDATE)
                    ->log("Update menu: " . $this->menu->title);

            });

            return success($this->menu->load('rootItems.children'));
        } catch (\Error $error) { exception($error); }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                MenuItem::where('menuId', $this->menu->id)->delete();

                if (!$this->menu->delete()) errMenuDelete();

                activity()->setCausedBy()->setReference($this->menu)
                    ->setType(ActivityType::MENU)->setAction(ActivityAction::DELETE)
                    ->log("Delete menu: " . $this->menu->title);

            });

            return success();
        } catch (\Error $error) { exception($error); }
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    private function syncItems(array $items, int $parentId = 0)
    {
        foreach ($items as $order => $item) {
            $menuItem = MenuItem::create([
                'menuId' => $this->menu->id,
                'menuParent' => $parentId,
                'menuLabel' => $item['menuLabel'],
                'menuUrl' => $item['menuUrl'],
                'menuOrder' => $item['menuOrder'] ?? $order + 1,
            ]);

            // rekursif untuk children
            if (!empty($item['children']) && is_array($item['children'])) {
                $this->syncItems($item['children'], $menuItem->id);
            }
        }
    }
}