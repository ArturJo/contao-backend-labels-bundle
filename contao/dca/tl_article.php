<?php

declare(strict_types=1);

use Contao\StringUtil;
use Solidwork\ContaoBackendLabelsBundle\Util\BackendLabelPermission;

// Show CSS ID and CSS class in article backend labels
$GLOBALS['TL_DCA']['tl_article']['list']['label']['label_callback'] = static function (array $row, string $label): string {
    // Call original core callback
    $label = (new tl_article())->addIcon($row, $label);

    if (!BackendLabelPermission::isGranted()) {
        return $label;
    }

    $parts = [];

    $cssId = StringUtil::deserialize($row['cssID'] ?? '');
    $cssHtmlId = trim($cssId[0] ?? '');
    $cssClass  = trim($cssId[1] ?? '');

    $renderWords = static function(string $value, string $class): string {
        $words = explode(' ', $value);
        sort($words);
        return '<code class="' . $class . '">' . implode(' ', array_map(
            fn($w) => '<span>' . htmlspecialchars($w) . '</span>',
            $words
        )) . '</code>';
    };

    if ($cssHtmlId !== '') {
        $parts[] = '<span>cssID:</span> ' . $renderWords($cssHtmlId, 'cssID');
    }
    if ($cssClass !== '') {
        $parts[] = '<span>cssClass:</span> ' . $renderWords($cssClass, 'cssClass');
    }

    if ($parts !== []) {
        $label .= ' <span class="cssIdClass" style="opacity:.6"><span class="divider"> | </span>' . implode('<span class="divider"> | </span>', $parts) . '</span>';
    }

    return $label;
};
