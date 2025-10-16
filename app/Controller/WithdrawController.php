<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\WithdrawService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class WithdrawController extends AbstractController
{
    #[Inject]
    protected WithdrawService $withdrawService;

    public function withdraw(string $accountId): ResponseInterface
    {
        $payload = [];

        try {
            $raw = (string) $this->request->getBody()->getContents();
            $payload = $raw !== '' ? json_decode($raw, true, 512, JSON_THROW_ON_ERROR) : (array) $this->request->post();
        } catch (\Throwable $e) {
            return $this->response->json([
                'success' => false,
                'error' => 'Invalid JSON payload',
            ])->withStatus(400);
        }
        
        try {
            $result = $this->withdrawService->createWithdraw($accountId, $payload);
            return $this->response->json([
                'success' => true,
                'data' => $result,
            ])->withStatus(201);
        } catch (\InvalidArgumentException $e) {
            return $this->response->json([
                'success' => false,
                'error' => $e->getMessage(),
            ])->withStatus(422);
        } catch (\Throwable $e) {
            \Hyperf\Context\Context::set('last_exception', $e->getMessage());
            return $this->response->json([
                'success' => false,
                'error' => $e->getMessage(),
            ])->withStatus(500);
        }
    }
}


