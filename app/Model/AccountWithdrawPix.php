<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property string $account_withdraw_id
 * @property string $type
 * @property string $key
 */
class AccountWithdrawPix extends Model
{
    protected ?string $table = 'account_withdraw_pix';

    protected array $fillable = [
        'account_withdraw_id', 'type', 'key',
    ];

    protected array $casts = [
        'account_withdraw_id' => 'string',
    ];
}


