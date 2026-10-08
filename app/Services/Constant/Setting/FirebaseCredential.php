<?php

namespace App\Services\Constant\Setting;

class FirebaseCredential
{
    const PROJECT_ID = 'projectId';
    const CLIENT_EMAIL = 'clientEmail';
    const PRIVATE_KEY = 'privateKey';

    const OPTION = [
        self::PROJECT_ID => 'Project ID',
        self::CLIENT_EMAIL => 'Client Email',
        self::PRIVATE_KEY => 'Private Key',
    ];
}