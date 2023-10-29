<?php
$text = $data['text'] ?? throw new \InvalidArgumentException('Missing required parameter: $text');
$href = $data['href'] ?? throw new \InvalidArgumentException('Missing required parameter: $href');
$class = \array_merge(['bg-blue-500 hover:bg-blue-700 text-white font-bold text-sm py-1.5 px-3 rounded transition border border-blue-900/30 hover:border-blue-900/50'], $data['class'] ?? []);
?>
<a href="<?php echo $href; ?>" class="<?php echo \implode(' ', $class); ?>"><?php echo $text; ?></a>
