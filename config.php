<?php
$availableLanguages = [
    '0' => 'Auto detect',
    'en' => 'English',
    'bg' => 'Bulgarian',
];

$positions = [
    'top-left' => 'Top left',
    'top-middle' => 'Top middle',
    'top-right' => 'Top right',

    'right-top' => 'Right top',
    'right-middle' => 'Right middle',
    'right-bottom' => 'Right bottom',

    'bottom-left' => 'Bottom left',
    'bottom-middle' => 'Bottom middle',
    'bottom-right' => 'Bottom right',

    'left-top' => 'Left top',
    'left-middle' => 'Left middle',
    'left-bottom' => 'Left bottom',
];

$defaultValues = [
    'page_type' => 'location_services',
    'sticky_button_position' => 'right-middle',
    'sticky_button_text' => 'Make an appointment',
    'sticky_button_text_color' => '#ffffff',
    'sticky_button_background_color' => '#343232',
    'sticky_tooltip_text' => 'You can make an online appointment here',
    'sticky_tooltip_show_delay' => 10,
    'sticky_tooltip_expire_time' => 86400,
    'buttons_selector' => '',
    'modal_max_width' => '',
    'modal_max_height' => '',
];

$rules = [
    'enabled' => 'boolean',
    'page_type' => 'required_with:enabled|in:location_services,location_classes,business_profile,location_profile',
    'slug' => 'required',
    'language' => 'required_with:enabled|in:' . implode(',', array_keys($availableLanguages)),
    'sticky_button_enabled' => 'boolean',
    'sticky_button_text' => 'required_with:sticky_button_enabled',
    'sticky_button_text_color' => 'required_with:sticky_button_enabled',
    'sticky_button_background_color' => 'required_with:sticky_button_enabled',
    'sticky_tooltip_text' => 'required_with:sticky_button_enabled',
    'sticky_tooltip_show_delay' => 'required_with:sticky_button_enabled',
    'sticky_tooltip_expire_time' => 'required_with:sticky_button_enabled',
    'sticky_button_position' => 'required_with:sticky_button_enabled|in:' . implode(',', array_keys($positions)),
    'buttons_selector' => '',
    'modal_max_width' => [
        'regex:/^(\d+)(px|%)$/',
    ],
    'modal_max_height' => [
        'regex:/^(\d+)(px|%)$/',
    ]
];


