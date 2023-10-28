<?php
$message = $data['message'] ?? '';
?>

<?php if ($message) { ?>
    <div class="text-red-700 text-sm mt-1"><?php echo $message; ?></div>
<?php } ?>
