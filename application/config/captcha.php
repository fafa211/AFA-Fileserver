<?php
return array(
    'default' => array(
        'style' => 'basic',
        'width' => 85,
        'height' => 35,
        'complexity' => 4,
        'background' => '',
        'fontpath' => DRIVERPATH . 'captcha/fonts/',
        'fonts' => array(
            'DejaVuSerif.ttf'
        ),
        'promote' => FALSE
    ),
    'words'=>array('apple','oran','dogo','suff','love','myby'),
    'riddles'=>array(
        array('china','beijing'),
        array('shanmai','do'),
    )
);