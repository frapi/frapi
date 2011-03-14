<h2>This is a form example</h2>

<?php if (isset($data['error'])) : ?>
<h3>Error: <?php echo $data['error']; ?>
<?php endif; ?>

<form action="/forms.html" method="post">
 <input value="name" name="example" /><br />
 <input type="submit" />
</form>

<h2>Extra data for example purposes</h2>
<?php print_r($data); ?>
