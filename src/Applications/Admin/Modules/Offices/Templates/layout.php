<?php
use Applications\Admin\Modules\Offices\OfficesController;
use Core\Shivalik\Entities\Office;
use PHPBackend\Page;

/**
 * @var Office $office
 */
$office = isset($_REQUEST[OfficesController::ATT_OFFICE])? $_REQUEST[OfficesController::ATT_OFFICE] : null;
?>


<?php if ($office == null) { ?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-briefcase"></i> <?php echo ($_REQUEST[OfficesController::ATT_VIEW_TITLE]); ?></h3>
    	
    	<?php if (!isset($_GET['option']) || $_GET['option'] != 'add') {?>
    	<a class="btn btn-primary" href="add.html">
    		<span class="fa fa-plus"></span> New office
    	</a>    	
    	<?php } else { ?>
    	<ol class="breadcrumb">
    		<li>
    			<i class="fa fa-briefcase"></i>
    			<a href="/admin/offices/">Offices</a>
			</li>
			<li>
    			<i class="fa fa-plus"></i>new office
			</li>
    	</ol>
    	<?php } ?>
    </div>
</div>
<?php } else { ?>
<?php require_once 'nav.php'; ?>
<?php }?>
<hr/>

<?php echo $_REQUEST[Page::ATT_VIEW]; ?>
