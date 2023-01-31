<?php
use Applications\Admin\Modules\Members\MembersController;
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

?>
<section class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th>N°</th>
				<th>Photo</th>
				<th>Names</th>
				<th>ID</th>
				<th>Username</th>
				<th>Required amount</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($_REQUEST[MembersController::ATT_MEMBERS] as $key => $user): ?>
				<tr>
					<td>
						<?php  echo ($key + 1); ?>
					</td>

					<td style="width: 30px;">
						<img style="width: 30px;" src="/<?php echo ($user->photo);?>">
					</td>

					<td><?php echo htmlspecialchars($user->names);?></td>
					<td><?php echo ($user->matricule);?></td>
					<td><?php echo ($user->pseudo);?></td>
					<td>
						<?php echo ($user->getSumInputs() - $user->getSumOutputs())." USD"; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</section>


