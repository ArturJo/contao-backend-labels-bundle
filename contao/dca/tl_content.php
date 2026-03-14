<?php

declare(strict_types=1);

use Contao\ContentModel;
use Contao\Image;
use Contao\StringUtil;
use Solidwork\ContaoBackendLabelsBundle\Util\BackendLabelPermission;

// Show CSS ID, CSS class and additional info in backend labels for all content elements
$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'] = static function (array $row): string {
    $labelResult = (new tl_content())->addCteType($row);

    if (is_array($labelResult)) {
        [$type, $preview, $key] = $labelResult;
        $dragHandle = '<button type="button" class="drag-handle" data-action="keydown->contao--sortable#move">' . Image::getHtml('drag.svg') . '</button>';
        $label = '<div class="cte_type draggable ' . $key . '">' . $dragHandle . '<div>' . $type . '</div></div>';
        if ($preview !== '') {
            $label .= '<div class="cte_content" data-contao--limit-height-target="node"><div class="cte_preview" style="contain:paint">' . $preview . '</div></div>';
        }
    } else {
        $label = $labelResult;
    }

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

    // Count child elements (element_group only)
    if (($row['type'] ?? '') === 'element_group') {
        $childCount = (int) ContentModel::countBy(
            ['pid=? AND ptable=? AND tstamp!=0'],
            [$row['id'], 'tl_content']
        );
        if ($childCount > 0) {
            $label_singular = $GLOBALS['TL_LANG']['tl_content']['element_group_child'] ?? 'child element';
            $label_plural   = $GLOBALS['TL_LANG']['tl_content']['element_group_children'] ?? 'child elements';
            $parts[] = $childCount . ' ' . ($childCount === 1 ? $label_singular : $label_plural);
        }
    }

    if ($parts !== []) {
        $label = preg_replace(
            '/(<div class="cte_type [^"]*">)(.*?)(<\/div>)/s',
            '$1$2$3<div class="cssIdClassWrapper"><span class="cssIdClass" style="opacity:.6">' . implode('<span class="divider"> | </span>', $parts) . '</span></div>',
            $label,
            1
        ) ?? $label;
    }

    return $label;
};
