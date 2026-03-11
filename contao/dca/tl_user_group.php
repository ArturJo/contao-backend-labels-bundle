<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

// Add showBackendLabels field to user group
$GLOBALS['TL_DCA']['tl_user_group']['fields']['sldwrk_showBackendLabels'] = [
    'exclude'   => false,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50'],
    'sql'       => ['type' => 'boolean', 'default' => false],
];

PaletteManipulator::create()
    ->addLegend('backend_labels_legend', 'admin_legend', PaletteManipulator::POSITION_AFTER, true)
    ->addField('sldwrk_showBackendLabels', 'backend_labels_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_user_group');