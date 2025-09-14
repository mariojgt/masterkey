<?php

namespace Mariojgt\MasterKey\Support;

class StrUtil {
    public static function random(int $len = 32): string {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $out = '';
        for ($i=0; $i<$len; $i++) {
            $out .= $chars[random_int(0, strlen($chars)-1)];
        }
        return $out;
    }
}
