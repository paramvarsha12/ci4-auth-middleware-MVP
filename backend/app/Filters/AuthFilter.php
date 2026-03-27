<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');
        
        if (!$header) {
            return service('response')
                ->setJSON(['error' => 'Authorization header missing'])
                ->setStatusCode(401);
        }

        $token = str_replace('Bearer ', '', $header);
        
        if (empty($token)) {
            return service('response')
                ->setJSON(['error' => 'Token missing'])
                ->setStatusCode(401);
        }
        
        try {
            $key = getenv('JWT_SECRET') ?: 'your-secret-key';
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            
            // Check if token is expired
            if (isset($decoded->exp) && $decoded->exp < time()) {
                return service('response')
                    ->setJSON(['error' => 'Token expired'])
                    ->setStatusCode(401);
            }
            
            $request->user = $decoded;
        } catch (\Firebase\JWT\ExpiredException $e) {
            return service('response')
                ->setJSON(['error' => 'Token expired'])
                ->setStatusCode(401);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return service('response')
                ->setJSON(['error' => 'Invalid token signature'])
                ->setStatusCode(401);
        } catch (\Exception $e) {
            return service('response')
                ->setJSON(['error' => 'Invalid token'])
                ->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
