<?php
$key = $data['key'] ?? throw new \InvalidArgumentException('Key is required');
$errors = \Framework\Support\Session::get('errors') ?? [];
$message = $errors[$key][0] ?? null;

?>

<?php if ($message) { ?>
    <div class="text-red-700 text-sm mt-1"><?php echo $message; ?></div>
<?php } ?>
