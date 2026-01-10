<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\HotReloader\HotReloader;
use CodeIgniter\Config\Services;
/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying or extending core files. This file provides a central
 * location to define your events, though they can always be added
 * at run-time, also, if needed.
 *
 * You create code that can execute by subscribing to events with
 * the 'on()' method. This accepts any form of callable, including
 * Closures, that will be executed when the event is triggered.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 */

Events::on('pre_system', static function () {
    if (ENVIRONMENT !== 'testing') {
        if (ini_get('zlib.output_compression')) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static fn($buffer) => $buffer);
    }

    /*
     * --------------------------------------------------------------------
     * Debug Toolbar Listeners.
     * --------------------------------------------------------------------
     * If you delete, they will no longer be collected.
     */
    if (CI_DEBUG && ! is_cli()) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        Services::toolbar()->respond();
        // Hot Reload route - for framework use on the hot reloader.
        if (ENVIRONMENT === 'development') {
            Services::routes()->get('__hot-reload', static function () {
                (new HotReloader())->run();
            });
        }
    }
    Events::on('post_controller_constructor', function () {
        $renderer = Services::renderer();

        $idUser = session()->get('id_user');

        if (!$idUser) {
            $renderer->setVar('countNotif', 0);
            return;
        }

        try {
            $client = Services::curlrequest([
                'timeout' => 2,
            ]);

            $response = $client->get(
                'http://172.23.44.16/ComplaintSystem/public/api/chat/unread/' . $idUser
            );

            $data = json_decode($response->getBody(), true);

            $renderer->setVar(
                'countNotif',
                $data['data']['unread_messages'] ?? 0
            );
        } catch (\Throwable $e) {
            log_message('error', 'Notif API error: ' . $e->getMessage());
            $renderer->setVar('countNotif', 0);
        }
    });
});
