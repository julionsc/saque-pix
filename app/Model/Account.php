<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property string $id
 * @property string $name
 * @property string|null $email
 * @property string $balance
 */
class Account extends Model
{
    protected ?string $table = 'account';

    protected array $fillable = [
        'id', 'name', 'email', 'balance',
    ];

    protected array $casts = [
        'id' => 'string',
    ];
}


