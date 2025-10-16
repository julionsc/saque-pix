<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Di\Annotation\Inject;
use App\Model\Account;
use App\Model\AccountWithdraw;
use App\Model\AccountWithdrawPix;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Ramsey\Uuid\Uuid;
use Brick\Money\Money;


class WithdrawService
{
    #[Inject]
    private \Hyperf\Contract\ConfigInterface $config;
    
    public function createWithdraw(string $accountId, array $payload): array
    {
        $currency_code = $this->config->get('currency_code', 'BRL');
        $amount = Money::of($payload['amount'], $currency_code);
        $schedule = $payload['schedule'] ?? null;
        $isScheduled = $schedule ==! null;

        $this->validateRequest($accountId, $payload, $schedule, $amount, $currency_code);

        return Db::transaction(function () use ($accountId, $payload, $isScheduled, $schedule, $amount, $currency_code) {
            $account = Account::query()->where('id', $accountId)->lockForUpdate()->first();

            if (! $account) {
                throw new \InvalidArgumentException('Account not found');
            }

            if (! $isScheduled) {
                $balance = Money::of($account->balance, $currency_code);

                if ($balance->isLessThan($amount)) {
                    throw new \InvalidArgumentException('Insufficient balance');
                }

                $account->balance = (string) $balance->minus($amount)->getAmount();
                $account->save();
            }

            $withdrawId = Uuid::uuid4()->toString();

            $withdraw = new AccountWithdraw([
                'id' => $withdrawId,
                'account_id' => $account->id,
                'method' => 'PIX',
                'amount' => (string) $amount->getAmount(),
                'scheduled' => $isScheduled ? 1 : 0,
                'scheduled_for' => $isScheduled ? Carbon::parse($schedule)->toDateTimeString() : null,
                'done' => $isScheduled ? 0 : 1,
                'error' => 0,
                'error_reason' => null,
            ]);

            $withdraw->save();

            $pix = $payload['pix'];

            $withdrawPix = new AccountWithdrawPix([
                'account_withdraw_id' => $withdrawId,
                'type' => 'email',
                'key' => $pix['key'],
            ]);

            $withdrawPix->save();

            return [
                'withdraw_id' => $withdrawId,
                'scheduled' => $isScheduled,
                'scheduled_for' => $isScheduled ? $withdraw->scheduled_for : null,
                'balance' => $account->balance,
            ];
        });
    }

    private function validateRequest($accountId, $payload, $schedule, $amount, $currency_code) {

        if (! Uuid::isValid($accountId)) {
            throw new \InvalidArgumentException('accountId must be a valid UUID');
        }

        $this->validatePayload($payload);

        if ($schedule ==! null) {
            $this->validateSchedule($schedule);
        }
        
        if ($amount->isLessThanOrEqualTo(Money::zero($currency_code))) {
            throw new \InvalidArgumentException('Amount must be greater than zero');
        }
    }

    private function validatePayload(array $payload): void
    {
        if (($payload['method'] ?? '') !== 'PIX') {
            throw new \InvalidArgumentException('method must be PIX');
        }

        if (! isset($payload['pix']['type'], $payload['pix']['key'])) {
            throw new \InvalidArgumentException('pix.type and pix.key are required');
        }

        if ($payload['pix']['type'] !== 'email') {
            throw new \InvalidArgumentException('Only PIX type email is supported');
        }

        if (! filter_var($payload['pix']['key'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('pix.key must be a valid email');
        }

        if (! isset($payload['amount'])) {
            throw new \InvalidArgumentException('amount is required');
        }
    }

    private function validateSchedule(string $schedule): void
    {
        $dt = Carbon::parse($schedule);
        $now = Carbon::now();

        if ($dt->lessThanOrEqualTo($now)) {
            throw new \InvalidArgumentException('schedule must be in the future');
        }

        if ($dt->greaterThan($now->copy()->addDays(7))) {
            throw new \InvalidArgumentException('schedule cannot be more than 7 days in the future');
        }
    }
}


