<?php
use Framework\Support\Str;

$text = $data['text'] ?? throw new \InvalidArgumentException('Missing required parameter: $text');
$for = $data['for'] ?? Str::slug($text);
$class = \array_merge(['block mb-2 text-sm text-gray-700 font-bold cursor-pointer'], $data['class'] ?? []);
?>

<label class="<?php echo \implode(' ', $class); ?>" for="<?php echo $for; ?>"><?php echo $text; ?></label>
