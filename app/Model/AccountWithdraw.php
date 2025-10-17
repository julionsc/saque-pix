<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property string $id
 * @property string $account_id
 * @property string $method
 * @property string $amount
 * @property int $scheduled
 * @property string|null $scheduled_for
 * @property int $done
 * @property int $error
 * @property string|null $error_reason
 */
class AccountWithdraw extends Model
{
    protected ?string $table = 'account_withdraw';

    protected array $fillable = [
        'id', 'account_id', 'method', 'amount', 'scheduled', 'scheduled_for', 'done', 'error', 'error_reason',
    ];

    protected array $casts = [
        'id' => 'string',
        'scheduled' => 'integer',
        'done' => 'integer',
        'error' => 'integer',
        'amount' => 'decimal:2',
        'scheduled_for' => 'datetime',
    ];
}


