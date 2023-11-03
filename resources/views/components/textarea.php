<?php
$class = \array_merge($data['class'] ?? [], ['rounded-md border border-blue-900 focus:border-blue-950 focus:ring focus:ring-blue-600/50']);
$name = $data['name'] ?? '';
$id = $data['id'] ?? $name;
$cols = $data['cols'] ?? '';
$rows = $data['rows'] ?? '';
$value = $this->old($name, $data['value'] ?? '');

?>

<textarea class="<?php echo \implode(' ', $class); ?>" name="<?php echo $name; ?>" id="<?php echo $id; ?>" cols="<?php echo $cols; ?>" rows="<?php echo $rows; ?>" ><?php echo $value; ?></textarea>
