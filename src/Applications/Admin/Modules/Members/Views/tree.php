<?php
use Applications\Member\Modules\Account\AccountController;
use PHPBackend\AppConfig;
use PHPBackend\Request;
use PHPBackend\Image2D\Mlm\TreeFormatter;
use Applications\Admin\Modules\Members\MembersController;
use Core\Shivalik\Entities\Member;

/**
 * @var AppConfig $config
 * @var Member $member
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

$foots = $config->get("footsMember");
$member = $_REQUEST[MembersController::ATT_MEMBER];
?>

<div class="row">

	<?php
	$somme = 0;
	foreach ($foots->getItems() as $item) { ?>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="info-box <?php echo (isset($_GET['foot']) && $_GET['foot'] == strtolower($item->getName()) ? 'dark':'blue'); ?>-bg" href="<?php echo strtolower($item->getName()); ?>.html" style="display: block;">
            <i class="fa fa-tag"></i>
            <span class="count" style="display: block;"><?php echo ($_REQUEST[$item->getName()]);  $somme += $_REQUEST[$item->getName()]; ?></span>
            <span class="title" style="display: block;"><?php echo $item->getName(); ?></span>
        </a>
        <!--/.info-box-->
    </div>
    <?php } ?>
    
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="info-box <?php echo (isset($_GET['foot']) && $_GET['foot'] == 'all' ? 'dark':'brown'); ?>-bg" href="all.html" style="display: block;">
            <i class="fa fa-tags"></i>
            <span class="count" style="display: block;"><?php echo ($somme); ?></span>
            <span class="title" style="display: block;">TOTAL</span>
        </a>
        <!--/.info-box-->
    </div>
</div>

<?php if (isset($_GET['foot'])) {?>

<div class="row">

    <div class="col-md-12">
    	<div class="panel panel-default">
	        <ul class="panel-heading nav nav-tabs">
				<li role="presentation" class="<?php echo ((isset($_GET['affichage']) && $_GET['affichage'] == 'stack')? "active" : ""); ?>">
					<a href="/admin/members/<?php echo $member->getId(); ?>/tree/<?=$_GET['foot'] ?>-stack.html"><span class="glyphicon glyphicon-align-left"></span> Stacked list</a>
				</li>
				<li role="presentation" class="<?php echo ((!isset($_GET['affichage']) || $_GET['affichage'] == 'tree')? "active" : ""); ?>">
					<a href="/admin/members/<?php echo $member->getId(); ?>/tree/<?=$_GET['foot'] ?>-tree.html"><span class="glyphicon glyphicon-tree-conifer"></span> Tree</a>
				</li>
			</ul>
            <section class="panel-body">
            	<?php if (!isset($_GET['affichage']) || $_GET['affichage'] == 'tree') { ?>
            	<div class="tree-render" data-treeRender ="/admin/members/<?php echo $member->getId(); ?>/tree/<?=$_GET['foot'] ?>.json"></div>
            	<?php } else {
            	    echo "\"tree\":".$_REQUEST[AccountController::ATT_TREE_FORMATTER]->format(TreeFormatter::FORMAT_HTML); 
            	}?>
            </section>
        </div>
    </div>
</div>
<?php } ?>