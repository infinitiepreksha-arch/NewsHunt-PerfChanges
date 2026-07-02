<?php

namespace App\Services;

use Illuminate\Support\Str;
use JsonException;

class HelperService {
    public static function changeEnv($updateData = array()) {
        if (count($updateData) > 0) {
            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');
            // Split string on every " " and write into array
            $env = explode(PHP_EOL, $env);

            $env_array = [];
            foreach ($env as $env_value) {
                if (empty($env_value)) {
                    //Add and Empty Line
                    $env_array[] = "";
                    continue;
                }

                $entry = explode("=", $env_value, 2);
                $env_array[$entry[0]] = $entry[0] . "=\"" . str_replace("\"", "", $entry[1]) . "\"";
            }

            // Turn the array back to a String
            $env = implode("\n", $env_array);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);
            return true;
        }
        return false;
    }

    /**
     * @param $categories
     * @param int $level
     * @param string $parentCategoryID
     * @description - This function will return the nested category Option tags using in memory optimization
     * @return mixed
     */
    public static function childCategoryRendering(&$categories, $level = 0, $parentCategoryID = '') {
        // Foreach loop only on the parent category objects
        foreach (collect($categories)->where('parent_category_id', $parentCategoryID) as $key => $category) {
            echo "<option value='$category->id'>" . str_repeat('&nbsp;', $level * 4) . '|-- ' . $category->name . "</option>";
            //Once the parent category object is rendered we can remove the category from the main object so that redundant data can be removed
            $categories->forget($key);

            //Now fetch the subcategories of the main category
            $subcategories = $categories->where('parent_category_id', $category->id);
            if (!empty($subcategories)) {
                //Finally if subcategories are available then call the recursive function & see the magic
                return self::childCategoryRendering($categories, $level + 1, $category->id);
            }
        }

        return false;
    }

    public static function buildNestedChildSubcategoryObject($categories) {
        // Used json_decode & encode simultaneously because i wanted to convert whole nested array into object
        try {
            return json_decode(json_encode(self::buildNestedChildSubcategoryArray($categories), JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return (object)[];
        }
    }

    private static function buildNestedChildSubcategoryArray($categories) {
        $children = [];
        //First Add Parent Categories to root level in an array
        foreach ($categories->toArray() as $value) {
            if ($value["parent_category_id"] == "") {
                $children[] = $value;
            }
        }

        //Then loop on the Parent Category to find the children categories
        foreach ($children as $key => $value) {
            $children[$key]["subcategories"] = self::findChildCategories($categories->toArray(), $value['id']);
        }
        return $children;
    }


    public static function findChildCategories($arr, $parent) {
        $children = [];
        foreach ($arr as $key => $value) {
            if ($value['parent_category_id'] == $parent) {
                $children[] = $value;
            }
        }
        foreach ($children as $key => $value) {
            $children[$key]['subcategories'] = self::findChildCategories($arr, $value['id']);
        }

        return $children;
    }

    /**
     * Generate Slug for any model
     * @param $model - Instance of Model
     * @param $slug
     * @param null $excludeID
     * @param int $count
     * @return mixed|string
     */
}
