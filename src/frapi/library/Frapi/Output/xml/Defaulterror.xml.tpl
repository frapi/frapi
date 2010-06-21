<response>
    <errors><?php foreach ($data['errors'] as $error): ?>
	<error code="<?php echo $error['name']; ?>">
            <message><?php echo $error['message']; ?></message>
            <name><?php echo $error['name']; ?></name>
            <at><?php echo $error['at']; ?></at>
        </error><?php endforeach; ?>

    </errors>
</response>
