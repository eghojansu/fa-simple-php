<?php

// navigation

return [
    // main nav
    'main' => [
        [
            'label' => 'Beranda',
            'path' => '/',
        ],
        [
            'label' => 'Master',
            'path' => '#',
            'items' => [
                [
                    'label' => 'User',
                    'path' => 'master/user',
                ],
            ],
        ],
        [
            'label' => 'Laporan',
            'path' => '#',
            'items' => [
                [
                    'label' => 'User',
                    'path' => 'laporan/user',
                ],
            ],
        ],
    ],
    // account nav
    'account' => [
        [
            'label' => 'Account',
            'path' => '#',
            'items' => [
                [
                    'label' => 'Profile',
                    'path' => 'account/profil',
                ],
                [
                    'label' => 'Logout',
                    'path' => 'account/logout',
                ],
            ],
        ],
    ],
];
