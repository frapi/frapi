<h1>FRAPI Encountered some errors</h1>
    <ul><?php foreach ($data['errors'] as $error): ?>
	<li>Name: <?php echo $error['name']; ?>, with message: <em><?php echo $error['message']; ?></em>, <?php 
	if (isset($error['at']) && !empty($error['at'])): ?>at: <?php echo $error['at']; endif; ?></li>
    </li><?php endforeach; ?>
    </ul>
