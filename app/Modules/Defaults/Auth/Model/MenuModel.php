<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Auth\Model;

use Core\Facades\DB;
use Phalcon\Mvc\Model;
use Core\Models\Behavior\SoftDelete;
use PDO;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class MenuModel extends Model
{
    /**
     * Simple in-request cache of menu trees per role to avoid repeated DB hits
     */
    private static $menuCacheByRole = [];

    public function initialize()
    {
        $this->setSource('system_menu');
        

        $this->hasMany('id_menu', MenuModel::class, 'parent_menu', [
            'alias' => 'children',
            'reusable' => true
        ]);
    }

    // public static function getUserMenuList()
    // {
    //     $di      = \Phalcon\Di::getDefault();
    //     $session = $di->getShared('session');
    //     $db      = $di->getShared('db');

    //     $sql = "
    //         SELECT * 
    //         FROM akuntansi.system_menu 
    //         WHERE 1=1 
    //             AND is_aktif=1 
    //             AND is_tampil=1 
    //             AND id_menu in (SELECT id_menu FROM system_menu_otorisasi where id_role=?) ORDER by urutan;
    //     ";

    //     // $result = DB::query($sql)->fetchAll(PDO::FETCH_OBJ);
    //     $result = DB::query($sql, [$session->user['id_role']])->fetchAll(PDO::FETCH_OBJ);

    //     return self::toTree($result);
    // }


    public static function getUserMenuList()
    {
        $di      = \Phalcon\Di::getDefault();
        $session = $di->getShared('session');
        $db      = $di->getShared('db');

        $roleId = (int) ($session->user['id_role'] ?? 0);

        if (isset(self::$menuCacheByRole[$roleId])) {
            return self::$menuCacheByRole[$roleId];
        }

        $sql = "
            SELECT m.*
            FROM system_menu AS m
            WHERE m.is_aktif = 1
              AND m.is_tampil = 1
              AND EXISTS (
                  SELECT 1
                  FROM system_menu_otorisasi AS o
                  WHERE o.id_menu = m.id_menu
                    AND o.id_role = :role
              )
            ORDER BY m.urutan, m.id_menu;
        ";

        $result = $db->query($sql, ['role' => $roleId])->fetchAll(\PDO::FETCH_OBJ);

        $tree = self::toTreeOptimized($result);

        self::$menuCacheByRole[$roleId] = $tree;
        return $tree;
    }

    private static function toTreeOptimized(array $flatMenu, int $rootParent = 0): array
    {
        // Build adjacency list: parent_menu => [items]
        $childrenByParent = [];
        foreach ($flatMenu as $item) {
            $parentId = (int) ($item->parent_menu ?? 0);
            if (!isset($childrenByParent[$parentId])) {
                $childrenByParent[$parentId] = [];
            }
            $childrenByParent[$parentId][] = $item;
        }

        // Ensure each sibling group respects "urutan" if present
        foreach ($childrenByParent as &$siblings) {
            usort($siblings, function ($a, $b) {
                $ua = $a->urutan ?? 0;
                $ub = $b->urutan ?? 0;
                if ($ua === $ub) {
                    // Stable fallback by id_menu if available
                    return ($a->id_menu <=> $b->id_menu) ?: 0;
                }
                return $ua <=> $ub;
            });
        }
        unset($siblings);

        $build = function (int $parentId) use (&$childrenByParent, &$build): array {
            $nodes = [];
            $items = $childrenByParent[$parentId] ?? [];
            foreach ($items as $item) {
                $childNodes = $build((int) $item->id_menu);
                $item->has_children = !empty($childNodes);
                $item->children = $childNodes;
                $nodes[] = $item;
            }
            return $nodes;
        };

        return $build($rootParent);
    }

    /**
     * Debug method to check menu structure
     */
    public static function debugMenuStructure()
    {
        $menus = self::getUserMenuList();
        return self::printMenuTree($menus, 0);
    }

    private static function printMenuTree($menus, $level = 0)
    {
        $indent = str_repeat('  ', $level);
        $output = '';
        
        foreach ($menus as $menu) {
            $output .= $indent . "- {$menu->nama_menu} (ID: {$menu->id_menu}, Parent: {$menu->parent_menu})\n";
            if (!empty($menu->children)) {
                $output .= self::printMenuTree($menu->children, $level + 1);
            }
        }
        
        return $output;
    }

    public static function injectUserMenuList($viewVar)
    {
        $di   = \Phalcon\Di::getDefault();
        $view = $di->getShared('view');

        $menus = static::getUserMenuList();

        $view->setVar($viewVar, $menus);
    }

    /*
    public static function getCountSuratMasuk()
    {
        $di      = \Phalcon\Di::getDefault();
        $db      = $di->getShared('db');

        $sql = "
            SELECT count(id) as jumlah FROM vw_surat_masuk WHERE status_surat = 0 
        ";
        $result = $db->query($sql);
        $data = $result->fetchAll(\Pdo::FETCH_OBJ);
        $countSuratMasuk = $data[0]->jumlah;
        
        return $countSuratMasuk;
    }
    */
}
