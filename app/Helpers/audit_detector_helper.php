<?php

if (!function_exists('detectModule')) {
    function detectModule(): string
    {
        $router = service('router');
        $controller = class_basename($router->controllerName());

        return strtoupper(preg_replace('/Controller$/', '', $controller));
    }
}

if (!function_exists('detectRefsFromPost')) {
    function detectRefsFromPost(array $post): array
    {
        $refs = [];

        foreach ($post as $key => $value) {
            if (!str_starts_with($key, 'id_')) {
                continue;
            }

            $table = substr($key, 3);

            if (is_array($value)) {
                array_walk_recursive($value, function ($v) use (&$refs, $table) {
                    if ($v !== null) {
                        $refs[] = ['table' => $table, 'id' => $v];
                    }
                });
            } else {
                $refs[] = ['table' => $table, 'id' => $value];
            }
        }

        return $refs;
    }
}

if (!function_exists('detectRefFromUri')) {
    function detectRefFromUri(): ?array
    {
        $segments = service('request')->uri->getSegments();
        $last = end($segments);

        if (is_numeric($last)) {
            return [
                'table' => detectTableFromController(),
                'id'    => $last
            ];
        }

        return null;
    }
}

if (!function_exists('detectTableFromController')) {
    function detectTableFromController(): string
    {
        $router = service('router');
        $controller = class_basename($router->controllerName());

        return strtolower(preg_replace('/Controller$/', '', $controller));
    }
}
