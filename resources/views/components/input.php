<?php
$class = \array_merge($data['class'] ?? [], ['rounded-md border border-blue-900 focus:border-blue-950 focus:ring focus:ring-blue-600/50']);
$type = $data['type'] ?? 'text';
$name = $data['name'] ?? '';
$id = $data['id'] ?? $name;
?>

<input class="<?php echo \implode(' ', $class); ?>" type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $id; ?>" />
