<?php

namespace VoyagerInc\PermissionRole\Services\Contracts;

interface ConfigDataServiceInterface
{
    public function get($key, $default = null);
}