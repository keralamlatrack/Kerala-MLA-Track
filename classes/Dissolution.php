<?php

namespace MySociety\TheyWorkForYou;

class Dissolution {
    public static function dates() {
        $out = array();
        $dates = array_filter(explode(',', DISSOLUTION_DATE));
        foreach ($dates as $houseanddate) {
            list ($house, $date) = explode(':', $houseanddate);
            $out[$house] = $date;
        }
        return $out;
    }

    public static function db() {
        if (!($dates = self::dates())) {
            return null;
        }

        $params = array();
        $query = array();
        foreach ($dates as $house => $date) {
            $params[":dissdate$house"] = $date;
            $query[] = "left_house = :dissdate$house";
        }

        return array(
            'query' => '(' . join(' OR ', $query) . ')',
            'params' => $params,
        );
    }
}
