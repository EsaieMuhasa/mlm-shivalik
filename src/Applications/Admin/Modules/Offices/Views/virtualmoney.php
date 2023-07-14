<?php 
use Applications\Admin\Modules\Offices\OfficesController;
use Core\Shivalik\Entities\Office;
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var Office $office
 */
$office = $_REQUEST[OfficesController::ATT_OFFICE];

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
$requests = $_REQUEST[OfficesController::ATT_VIRTUAL_MONEYS];
?>

<div class="row">
    <?php if (empty($requests)) { ?>
	<div class="col-xs-12">
		<a href="send.html" class="btn btn-primary">
			<span class="fa fa-plus"></span> Send virtual money
		</a>
	</div>
    <?php } else {?>
        <?php foreach ($requests as $request) : ?>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="thumbnail">
                <div class="alert alert-info">
                    <p>
                        <strong><span class="fa fa-comment"></span> <?php echo ($request->getWithdrawalsCount() != 0? ' Raport ':' Request '); ?></strong> 
                        of <i><?php echo $request->dateAjout->format('D, d M Y \a\t H:i'); ?></i>
                    </p>
                    <p class="h4">
                        <span class="label label-success">Afilliation: <?php echo "{$request->affiliation} {$config->get('devise')}"; ?></span>
						<span class="label label-success">Porduct: <?php echo "{$request->product} {$config->get('devise')}"; ?></span>
                    </p>
                </div>
                <div class="btn-group">
                    <a href="<?php echo "{$request->id}/accept.html"; ?>" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span>Accept</a>
                    <?php if ($request->getWithdrawalsCount() == 0) :?>
                    <a href="<?php echo "{$request->id}/dismiss.html"; ?>" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span>Dismiss</a>
                    <?php endif; ?>
                </div>
            </div>
            <!--/.info-box-->
        </div>
        <?php endforeach; ?>
    <?php } ?>
</div>
<hr/>
<div class="panel panel-default">
	<div class="panel-heading">
		<strong class="panel-title">Virtual moneys</strong>
	</div>
	<div class="panel-body">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box dark-bg">
                    <i class="glyphicon glyphicon-leaf"></i>
                    <div class="count"><?php echo ("{$office->getAvailableVirtualMoneyProduct()} {$config->get('devise')}"); ?></div>
                    <div class="title">Available product account</div>
                </div>
                <!--/.info-box-->
            </div>
        
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box dark-bg">
                    <i class="glyphicon glyphicon-user"></i>
                    <div class="count"><?php echo ("{$office->getAvailableVirualMoneyAfiliate()} {$config->get('devise')}"); ?></div>
                    <div class="title">Available membership account</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box red-bg">
                    <i class="glyphicon glyphicon-trash"></i>
                    <div class="count"><?php echo ("{$office->getTrashVirtualMoneyProduct()} {$config->get('devise')}"); ?></div>
                    <div class="title">Trash Product</div>
                </div>
                <!--/.info-box-->
            </div>
        
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box red-bg">
                    <i class="glyphicon glyphicon-trash"></i>
                    <div class="count"><?php echo ("{$office->getTrashVirtualMoneyAfiliate()} {$config->get('devise')}"); ?></div>
                    <div class="title">Trash Membership</div>
                </div>
                <!--/.info-box-->
            </div>
            
        </div>
	</div>
</div>

<?php if (!empty($_REQUEST[OfficesController::ATT_ALL_VIRTUAL_MONEYS])) : ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-bordered table-condenced panel panel-default">
    			<caption class="panel-title text-center">Offices virtual moneys rapports</caption>
				<thead class="panel-heading">
					<tr>
						<th>Date</th>
						<th>Afilliation</th>
						<th>Product</th>
					</tr>
				</thead>
				<tbody class="panel-body">
					<?php foreach ($_REQUEST[OfficesController::ATT_ALL_VIRTUAL_MONEYS] as $raport) : ?>
					<tr>
						<td><?php echo $raport->dateAjout->format('D, d M Y \a\t H:i'); ?></td>
						<td><?php echo "{$raport->afiliate} {$config->get('devise')}"; ?></td>
						<td><?php echo "{$raport->product} {$config->get('devise')}"; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php endif; ?>
