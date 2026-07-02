<?php
namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SystemUpgradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // * means create,list,update,delete
        $permissionsList = [
            'role'         => '*',
            'adminuser'    => '*',
            'notification' => '*',
            'customer'     => [
                'only' => ['list', 'update'],
            ],
            'settings'     => [
                'only' => ['update'],
            ],
            'country'      => '*',
        ];

        $permissionsList = self::generatePermissionList($permissionsList);

        $permissions = array_map(static function ($data) {
            return [
                'name'       => $data,
                'guard_name' => 'web',
            ];
        }, $permissionsList);
        Permission::upsert($permissions, ['name'], ['name']);

        /*Create Settings which are new & ignore the old values*/
        Setting::insertOrIgnore(config('constants.DEFAULT_SETTINGS'));
    }

    /**
     * @param array {
     * <pre>
     *  permission_name :array<string> array {
     *      * : string // List , Create , Edit , Delete
     *      only : string // List , Create , Edit , Delete
     *      custom: array { // custom permissions will be prefixed with permission_name eg. permission_name-permission1
     *          permission1: string,
     *          permission2: string,
     *      }
     *  }
     * } $permission
     * @return array
     */
    public static function generatePermissionList($permissions)
    {
        $permissionList = [];

        foreach ($permissions as $name => $permission) {
            $defaultPermission = self::getDefaultPermissions($name);

            if (is_array($permission)) {
                $permissionList = self::handleArrayPermission($name, $permission, $defaultPermission, $permissionList);
            } else {
                $permissionList = array_merge($permissionList, $defaultPermission);
            }
        }

        return $permissionList;
    }

    private static function getDefaultPermissions($name)
    {
        return [
            $name . "-list",
            $name . "-create",
            $name . "-update",
            $name . "-delete",
        ];
    }

    private static function handleArrayPermission($name, $permission, $defaultPermission, $permissionList)
    {
        if (in_array("*", $permission, true)) {
            return array_merge($permissionList, $defaultPermission);
        }

        if (array_key_exists("only", $permission)) {
            foreach ($permission["only"] as $row) {
                $permissionList[] = $name . "-" . strtolower($row);
            }
        }

        if (array_key_exists("custom", $permission)) {
            foreach ($permission["custom"] as $customPermission) {
                $permissionList[] = $name . "-" . $customPermission;
            }
        }

        return $permissionList;
    }

}
