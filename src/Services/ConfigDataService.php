<?php

namespace VoyagerInc\PermissionRole\Services;

class ConfigDataService implements Contracts\ConfigDataServiceInterface
{
    public function get($key, $default = true)
    {
        $configName = 'permission-role';

        $key = $configName . '.' . $key;

        return config($key, $default);
    }
}
