<?php
$class = \array_merge($data['class'] ?? [], ['rounded-md border border-blue-900 focus:border-blue-950 focus:ring focus:ring-blue-600/50']);
$type = $data['type'] ?? 'text';
$name = $data['name'] ?? throw new \InvalidArgumentException('Missing required parameter: $name');
$id = $data['id'] ?? $name;
$value = $this->old($name, $data['value'] ?? '');
?>

<input class="<?php echo \implode(' ', $class); ?>" type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>">
