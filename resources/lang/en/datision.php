<?php

return [
    'menu' => [
        'title' => 'Datision Parameters',
    ],

    'titles' => [
        'index'  => 'Datision Parameters',
        'create' => 'Create Parameter',
        'edit'   => 'Edit Parameter',
        'show'   => 'Parameter Details',
    ],

    'fields' => [
        'machine_url'   => 'Machine URL',
        'threshold_sec' => 'Threshold (seconds)',
    ],

    'labels' => [
        'created_at' => 'Created At',
        'actions'    => 'Actions',
    ],

    'actions' => [
        'create' => 'New Parameter',
        'save'   => 'Save',
        'update' => 'Update',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit'   => 'Edit',
        'show'   => 'View',
        'back'   => 'Back',
    ],

    'messages' => [
        'empty' => 'No parameters found.',
    ],

    'confirm' => [
        'delete' => 'Are you sure you want to delete this item?',
    ],

    'flash' => [
        'created' => 'Parameter created successfully.',
        'updated' => 'Parameter updated successfully.',
        'deleted' => 'Parameter deleted successfully.',
    ],

    'validation' => [
        'machine_url_required' => 'Machine URL is required.',
        'machine_url_url'      => 'Machine URL must be a valid URL (http/https).',
        'threshold_required'   => 'Threshold (seconds) is required.',
        'threshold_integer'    => 'Threshold (seconds) must be an integer.',
        'threshold_min'        => 'Threshold (seconds) must be at least 0.',
        'threshold_max'        => 'Threshold (seconds) must be less than or equal to 86400.',
    ],
];