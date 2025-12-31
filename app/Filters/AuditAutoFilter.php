<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\AuditModelResolver;

class AuditAutoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        log_message('error', '🔥 AUDIT FILTER BEFORE TRIGGERED');

        helper('audit');

        $method = strtolower($request->getMethod());

        if (!in_array($method, ['post', 'put', 'patch', 'delete'])) {
            return;
        }

        // Ambil payload
        $payload = $request->getPost() ?: [];
        if (empty($payload)) {
            $raw = $request->getBody();
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $payload = $decoded;
            }
        }

        log_message('error', 'AUDIT PAYLOAD: ' . json_encode($payload));
        
        $router = service('router');

        $controller = class_basename($router->controllerName());
        $methodName = $router->methodName();

        // $action = strtolower($methodName);
        // atau kalau mau lengkap:
        $action = strtolower($controller . '::' . $methodName);

        $newPayload = [
            'body'   => $payload,
            'params' => service('request')->uri->getSegments(),
            'query'  => $request->getGet(),
            'method' => $method,
            'uri'    => current_url(),
            'ip'     => $request->getIPAddress(),
        ];
        if (empty($refs)) {
            $refs[] = [
                'table' => strtolower($methodName),
                'id'    => 0,
            ];
        }

        unset($payload['csrf_test_name']);

        if (empty($refs)) {
            log_message('error', '❌ AUDIT SKIPPED (NO REFS)');
            return;
        }

        // $action = match ($method) {
        //     'post' => 'insert',
        //     'put', 'patch' => 'update',
        //     'delete' => 'delete',
        // };

        foreach ($refs as $ref) {
            log_audit([
                'module'   => detectModule(),
                'action'   => $action,
                'ref_type' => $ref['table'] ?? $request->getGet(),
                'ref_id'   => $ref['id'] ?? 0,
                'message'  => strtoupper($action) . ' via filter',
                'old'      => null,
                'new'      => $newPayload,
            ]);
        }

        log_message('error', '✅ AUDIT FILTER EXECUTED');
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        log_message('error', '🔥 AUDIT FILTER AFTER TRIGGERED');
    }
}
