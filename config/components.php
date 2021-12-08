<?php
return [
    // all possible components for a game
    'all' => [
        'disc',
        'cartridge',
        'box',
        'case',
        'manual',
        'case_art',
        'cartridge_holder',
        'clamshell',
        'box_or_case',
        'art_or_holder',
        'case_sticker',
        'styrofoam',
        'insert',
    ],
    // components considered for listing to be complete
    'complete' => [
        'disc',
        'cartridge',
        'box',
        'case',
        'manual',
        'case_art',
        'cartridge_holder',
        'clamshell',
        'box_or_case',
        'art_or_holder',
        'case_sticker',
    ],
    // extra components not needed to be complete
    'extra' => [
        'styrofoam',
        'insert',
    ],
];
