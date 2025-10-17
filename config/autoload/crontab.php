<?php

declare(strict_types=1);

use Hyperf\Crontab\Crontab;

return [
    'enable' => true,
    'crontab' => [
        (new Crontab())
            ->setName('process_scheduled_withdraws')
            ->setRule('* * * * *')
            ->setCallback([App\Service\WithdrawService::class, 'processScheduled'])
            ->setMemo('Process scheduled withdraws every minute'),
    ], 
];
