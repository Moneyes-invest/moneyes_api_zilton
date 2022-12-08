<?php
// Inspired by  https://github.com/jolicode/codingstyle
$header = <<<OEF
This file is part of the Stello API project.
(c) Stello <dev@stello.eu>
For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
OEF;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony'                              => true,
        'header_comment'                        => ['header' => $header],
        'array_syntax'                          => ['syntax' => 'short'],
        'ordered_class_elements'                => true,
        'ordered_imports'                       => true,
        'heredoc_to_nowdoc'                     => true,
        'php_unit_strict'                       => false,
        'php_unit_construct'                    => true,
        'phpdoc_add_missing_param_annotation'   => true,
        'non_printable_character'               => false,
        'phpdoc_no_package'                     => true,
        'comment_to_phpdoc'                     => true,
        'phpdoc_no_useless_inheritdoc'          => true,
        'phpdoc_order'                          => true,
        'phpdoc_separation'                     => true,
        'phpdoc_to_comment'                     => false,
        'phpdoc_indent'                         => true,
        'phpdoc_single_line_var_spacing'        => true,
        'phpdoc_line_span'                      => ['property' => 'single'],
        'phpdoc_inline_tag_normalizer'          => [
            'tags' => [
                'example',
                'id',
                'internal',
                'inheritdoc',
                'inheritdocs',
                'link',
                'source',
                'toc',
                'tutorial',
            ],
        ],
        'no_empty_phpdoc'                       => true,
        'strict_comparison'                     => true,
        'strict_param'                          => false,
        'single_quote'                          => true,
        'yoda_style'                            => true,
        'no_extra_blank_lines'                  => [
            'tokens' => [
                'break',
                'continue',
                'extra',
                'return',
                'throw',
                'use',
                'parenthesis_brace_block',
                'square_brace_block',
                'curly_brace_block',
            ],
        ],
        'echo_tag_syntax'                       => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else'                       => true,
        'no_useless_return'                     => true,
        'semicolon_after_instruction'           => true,
        'combine_consecutive_unsets'            => true,
        'ternary_to_null_coalescing'            => true,
        'declare_strict_types'                  => true,
        'full_opening_tag'                      => true,
        'no_unused_imports'                     => true,
        'ternary_operator_spaces'               => true,
        'doctrine_annotation_array_assignment'  => ['operator' => '='],
        'declare_equal_normalize'               => ['space' => 'single'],
        'no_superfluous_phpdoc_tags'            => true,
        'binary_operator_spaces'                => [
            'operators' => [
                '='  => 'align_single_space',
                '=>' => 'align_single_space',
            ],
        ],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in('src')
            ->in('public')
            ->in('config')
            ->in('migrations')
    );