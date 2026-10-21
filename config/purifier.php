<?php

return [
    'encoding'         => 'UTF-8',
    'finalize'         => true,
    'ignoreNonStrings' => false,
    'cachePath'        => storage_path('app/purifier'),
    'cacheFileMode'    => 0755,

    'settings' => [

        'default' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'div,b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]',
            'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty'   => true,
        ],

        
        'blog' => [
            'HTML.Doctype' => 'HTML 4.01 Transitional',

            'HTML.AllowedElements' => [
                'p', 'br', 'hr', 'span', 'div',
                'strong', 'b', 'em', 'i', 'u', 's', 'strike', 'sub', 'sup',
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'ul', 'ol', 'li', 'blockquote', 'pre', 'code',
                'a', 'img', 'figure', 'figcaption',
                'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'caption',
            ],

            'HTML.AllowedAttributes' => [
                '*.class', '*.style',
                'a.href', 'a.title', 'a.target', 'a.rel',
                'img.src', 'img.alt', 'img.width', 'img.height',
                'td.colspan', 'td.rowspan', 'th.colspan', 'th.rowspan',
                'ol.start',
            ],

            'CSS.AllowedProperties' => 'text-align,width,height,float,margin,margin-left,margin-right,color,background-color,font-size,font-weight,font-style,text-decoration,border,border-collapse',

            'Attr.AllowedFrameTargets' => ['_blank'],
            'Attr.AllowedRel'          => ['nofollow', 'noopener', 'noreferrer'],
            'URI.AllowedSchemes'       => ['http' => true, 'https' => true, 'mailto' => true],

            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty'   => false,
        ],

        
        'custom_definition' => [
            'id'    => 'blog-html5-definitions',
            'rev'   => 1,
            'debug' => false,
            'elements' => [
                ['figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common'],
                ['figcaption', 'Inline', 'Flow', 'Common'],
            ],
        ],

        'custom_attributes' => [],
        'custom_elements'   => [],
    ],
];