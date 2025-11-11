<?php
// config/ai.php

return [
    // Lista blanca de hosts permitidos para target_url (proxy)
    // Puedes poblarla desde .env con AI_ALLOWED_HOSTS
    'allowed_hosts' => array_filter(array_map('trim', explode(',', env('AI_ALLOWED_HOSTS', '13.48.27.24')))),
];