<?php

return [
    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin Devgenfour'),
        'email' => env('ADMIN_EMAIL', 'admin@devgenfour.com'),
        'password' => env('ADMIN_PASSWORD'),
    ],
    // logo_scale enlarges logos whose image has wide white margins so every partner looks equally sized.
    'partners' => [
        ['name' => 'MyACQ', 'logo' => 'OurPartner/ACQ.png', 'logo_scale' => 'scale-240'],
        ['name' => 'A.I.O All In One Store', 'logo' => 'OurPartner/AIO.png', 'logo_scale' => 'scale-250'],
        ['name' => 'Desa Merdeka', 'logo' => 'OurPartner/DesaMerdeka.jpg', 'logo_scale' => 'scale-135'],
        ['name' => 'Kementerian Perindustrian Republik Indonesia', 'logo' => 'OurPartner/Kemenprin.png', 'logo_scale' => 'scale-145'],
        ['name' => 'Politeknik Negeri Indramayu', 'logo' => 'OurPartner/polindra.jpg', 'logo_scale' => 'scale-100'],
    ],
];
