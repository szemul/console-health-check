<?php

/**
 * CONFIG
 */
$dirs = [
    'src/',
    'tests/',
];

$excludePaths = [];

$excludeDirs = [];

$rules = [
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
    'binary_operator_spaces' => ['operators' => ['=>' => 'align', '=' => 'align']],
    'blank_line_after_opening_tag' => false,
    'blank_line_before_statement' => ['statements' => ['declare', 'return', 'throw', 'try']],
    'class_attributes_separation' => ['elements' => ['method' => 'one']],
    'concat_space' => ['spacing' => 'one'],
    'function_typehint_space' => true,
    'heredoc_to_nowdoc' => true,
    'include' => true,
    'linebreak_after_opening_tag' => true,
    'magic_constant_casing' => true,
    'native_function_casing' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_blank_lines_before_namespace' => false,
    'no_empty_comment' => true,
    'no_empty_phpdoc' => true,
    'no_empty_statement' => true,
    'no_extra_blank_lines' => ['tokens' => ['extra']],
    'no_leading_namespace_whitespace' => true,
    'no_mixed_echo_print' => ['use' => 'echo'],
    'no_multiline_whitespace_around_double_arrow' => true,
    'multiline_whitespace_before_semicolons' => true,
    'no_short_bool_cast' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'no_spaces_around_offset' => ['positions' => ['inside']],
    'no_superfluous_phpdoc_tags' => true,
    'no_trailing_comma_in_list_call' => true,
    'no_trailing_comma_in_singleline_array' => true,
    'no_unneeded_control_parentheses' => ['statements' => ['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case', 'yield']],
    'no_unused_imports' => true,
    'no_whitespace_before_comma_in_array' => true,
    'normalize_index_brace' => true,
    'object_operator_without_whitespace' => true,
    'phpdoc_indent' => true,
    'phpdoc_inline_tag_normalizer' => true,
    'phpdoc_no_access' => true,
    'phpdoc_no_package' => true,
    'phpdoc_no_useless_inheritdoc' => true,
    'phpdoc_scalar' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_trim' => true,
    'phpdoc_types' => true,
    'phpdoc_var_without_name' => true,
    'self_accessor' => true,
    'single_line_comment_style' => ['comment_types' => ['hash']],
    'single_quote' => true,
    'space_after_semicolon' => true,
    'standardize_not_equals' => true,
    'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
    'trim_array_spaces' => true,
    'unary_operator_spaces' => true,
    'whitespace_after_comma_in_array' => true,
];

/**
 * CREATE FINDER
 */
$finder = PhpCsFixer\Finder::create()->in($dirs);

foreach ($excludeDirs as $dir) {
    $finder->exclude($dir);
}
foreach ($excludePaths as $path) {
    $finder->notPath($path);
}

return (new PhpCsFixer\Config())
    ->setRules($rules)
    ->setFinder($finder)
    ->setRiskyAllowed(true);
