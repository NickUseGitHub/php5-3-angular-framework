<?php

namespace Application\Library\Proxy;

use Application\Library\Dependency\IDependency;
use Application\Util\Session;
use Application\Util\StringLib;

/**
* 
*/
class Kart
{
    const SESS_KEY = "drm_kart";

    public static function saveToSession($items)
    {
        Session::set(self::SESS_KEY, self::serializeData($items));
    }

    public static function getItems()
    {
        $items = Session::get(self::SESS_KEY);
        $items = self::unSerializeData($items);
        return $items;
    }

    public static function setItem($keyItem, $item)
    {
        if (empty($keyItem)) {
            return false;
        }

        $items = self::getItems();
        if (empty($items[$keyItem])) {
            return false;
        }

        $items[$keyItem] = $item;
        self::saveToSession($items);
        return true;
    }

    public static function addNew($item)
    {
        if (!is_array($item)) {
            return false;
        }

        $length = 5;
        $keyItem = StringLib::generateRandomString($length);

        $items = self::getItems();
        $items = $items? : array();
        $items[$keyItem] = $item;
        self::saveToSession($items);
        return true;
    }


    public static function remove($keyItem)
    {
        if (empty($keyItem)) {
            return false;
        }
        $items = self::getItems();
        unset($items[$keyItem]);
        self::saveToSession($items);
        
        return true;
    }

    public static function emptyKart()
    {
        Session::delete(self::SESS_KEY);
        return true;
    }

    public static function checkout(IDependency $model, $bill_order_id)
    {
        if (empty($bill_order_id)) {
            return false;
        }

        $items = self::getItems();

        if (empty($items)) {
            return false;
        }

        $current_time = date('Y-m-d H:i:s');
        $itemForAdd = array();
        foreach ($items as $key => $item) {
            $items[$key]['bill_order_id'] = $bill_order_id;
            $items[$key]['update_date'] = $current_time;
            $items[$key]['update_by'] = 1;
            $items[$key]['create_date'] = $current_time;
            $items[$key]['create_by'] = 1;

            $itemForAdd[] = $items[$key];
        }

        $result = $model->insertMany($itemForAdd);

        if ($result) {
            self::emptyKart();
            return true;
        }
        return false;
    }

    private static function serializeData($data)
    {
        return serialize($data);
    }

    private static function unSerializeData($data)
    {
        return unserialize($data);
    }

}