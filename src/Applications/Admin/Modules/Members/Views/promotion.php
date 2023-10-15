<?php

use Applications\Admin\Modules\Members\MembersController;
use Applications\Member\Modules\Account\AccountController;
use PHPBackend\Request;
use PHPBackend\Config\AppMetadata;

/**
 * 
 * @var AppMetadata $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
$member = $_REQUEST[MembersController::ATT_MEMBER] ?? null;
?>

<h1>Promotion </h1>

<div class="panel">
	<div class="panel-heading">
		<strong class="panel-title">Choose date intervale</strong>
	</div>
	<form class="panel-body" method="post">
		<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="field-promotion-min-date">Min date</label>
					<input class="form-control" id="field-promotion-min-date" name="min" 
						value="<?= $_REQUEST['min']->format('Y-m-d') ?>" type="date" autocomplete="off"/>
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="field-promotion-max-date">Max date</label>
					<input class="form-control" id="field-promotion-max-date" name="max" 
						value="<?= $_REQUEST['max']->format('Y-m-d') ?>" type="date" autocomplete="off"/>
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="field-member-id">Member ID</label>
					<input class="form-control" id="field-member-id" name="id" 
						value="<?php echo ($member != null ? $member->matricule : '') ; ?>" type="" autocomplete="off"/>
				</div>
			</div>
		</div>

		<button type="submit" class="btn btn-primary">
			<span class="fa fa-refresh"></span> Check
		</button>
	</form>
</div>


<?php if ( count($_REQUEST[AccountController::ATT_MEMBERS])) : ?>

<div class="row">
    <div class="col-md-12">
        <section class="table-responsive">
        	<table class="table table-bordered panel panel-default">
        		<caption><?php echo count($_REQUEST[AccountController::ATT_MEMBERS]); ?>  member<?php echo (count($_REQUEST[AccountController::ATT_MEMBERS])>1? 's':''); ?></caption>
        		<thead class="panel-heading">
        			<tr>
        				<th>N°</th>
        				<th>Date and time</th>
        				<th>Names</th>
        				<th>ID</th>
        				<th>Packet</th>
        			</tr>
        		</thead>
        		<tbody class="panel-body">
        			<?php $num = 0; ?>
					<?php foreach ($_REQUEST[AccountController::ATT_MEMBERS] as $member) : ?>
    					<tr>
    						<td><?php echo (++$num);?></td>
							<td><?php echo ($member->dateAjout->format('d-m-Y \a\t H:i:s'));?></td>
    						<td><?php echo htmlspecialchars($member->names);?></td>
    						<td><?php echo ($member->matricule);?></td>
    						<td><?php echo ($member->packet->grade->name);?></td>
    					</tr>
					<?php endforeach; ?>
        		</tbody>
        	</table>
        </section>
    </div>
</div>


<?php elseif ($member != null) : ?>
	<div class="alert alert-danger">
		<strong>
			Promotion ,<?php echo $_REQUEST['min']->format('d F Y') ?> to <?php echo $_REQUEST['max']->format('d F Y') ?> :
		</strong>

		No sponsoring at choosed date intervale, in <em><?= htmlspecialchars($member->fullName) ?> (<?= htmlspecialchars($member->matricule) ?>) </em> account.
	</div>
<?php endif; ?>