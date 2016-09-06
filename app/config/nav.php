<?php

// navigation

return [
    // main nav
    'main' => [
        [
            'label' => 'Beranda',
            'path' => 'admin',
        ],
        [
            'label' => 'Master',
            'path' => '#',
            'items' => [
                [
                    'label' => 'User',
                    'path' => 'admin/master/user',
                ],
            ],
        ],
        [
            'label' => 'Laporan',
            'path' => '#',
            'items' => [
                [
                    'label' => 'User',
                    'path' => 'admin/laporan/user',
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
                    'path' => 'admin/account',
                ],
                [
                    'label' => 'Logout',
                    'path' => 'admin/logout',
                ],
            ],
        ],
    ],
];
