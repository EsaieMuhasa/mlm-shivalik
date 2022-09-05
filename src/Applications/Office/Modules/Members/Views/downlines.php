<?php
use Applications\Member\Modules\Account\AccountController;
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

$foots = $config->get("footsMember");
?>

<?php if (!isset($_GET['foot'])) {?>
<div class="row">

	<?php
	$somme = 0;
	foreach ($foots->getItems() as $item) { ?>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="info-box blue-bg" href="<?php echo strtolower($item->getName()); ?>.html" style="display: block;">
            <i class="fa fa-tag"></i>
            <span class="count" style="display: block;"><?php echo ($_REQUEST[$item->getName()]);  $somme += $_REQUEST[$item->getName()]; ?></span>
            <span class="title" style="display: block;"><?php echo $item->getName(); ?></span>
        </a>
        <!--/.info-box-->
    </div>
    <?php } ?>
    
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="info-box brown-bg" href="all.html" style="display: block;">
            <i class="fa fa-tags"></i>
            <span class="count" style="display: block;"><?php echo ($somme); ?></span>
            <span class="title" style="display: block;">TOTAL</span>
        </a>
        <!--/.info-box-->
    </div>
</div>
<?php } else {?>

<div class="row">
    <div class="col-md-12">
        <section class="table-responsive">
        	<table class="table table-bordered">
        		<caption><?php echo count($_REQUEST[AccountController::ATT_MEMBERS]); ?>  member<?php echo (count($_REQUEST[AccountController::ATT_MEMBERS])>1? 's':''); ?></caption>
        		<thead>
        			<tr>
        				<th>N°</th>
        				<th>Photo</th>
        				<th>Names</th>
        				<th>Username</th>
        				<th>ID</th>
        				<th>State</th>
        			</tr>
        		</thead>
        		<tbody>
        			<?php $num = 0; ?>
					<?php foreach ($_REQUEST[AccountController::ATT_MEMBERS] as $member) : ?>
    					<tr>
    						<td><?php echo (++$num);?></td>
    						<td style="width: 30px;">
    							<img style="width: 30px;" src="/<?php echo ($member->photo);?>">
    						</td>
    						<td><?php echo htmlspecialchars($member->names);?></td>
    						<td><?php echo ($member->pseudo);?></td>
    						<td><?php echo ($member->matricule);?></td>
    						<td><?php echo ($member->enable? 'Enable':'Disable');?></td>
    					</tr>
					<?php endforeach; ?>
        		</tbody>
        	</table>
        </section>
    </div>
</div>
<?php } ?>