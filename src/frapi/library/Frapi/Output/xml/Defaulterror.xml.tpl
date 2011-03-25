<response>
<?php /** First is for other types of errors. */ ?>
<?php if (!isset($data['errors']) || isset($data['error'])) : ?>
    <error><?php echo $data['error']; ?></error>
    <error_description><?php echo $data['error_description']; ?></error_description>
<?php else: ?>
    <errors><?php foreach ($data['errors'] as $error): ?>
	<error code="<?php echo $error['name']; ?>">
            <message><?php echo $error['message']; ?></message>
            <name><?php echo $error['name']; ?></name>
            <at><?php echo $error['at']; ?></at>
        </error><?php endforeach; ?>

    </errors>

<?php endif; ?>
</response>
