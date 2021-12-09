<?php
use Applications\Member\Modules\Account\AccountController;
use Library\Config;
use Library\Image2D\Mlm\TreeFormatter;

$config = Config::getInstance();

$foots = $config->get("footsMember");
?>

<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-sitemap"></i> <?php echo ($_REQUEST[AccountController::ATT_VIEW_TITLE]); ?></h3>
    	<ol class="breadcrumb">
    		<li>
    			<i class="fa fa-sitemap"></i>
    			<?php if (isset($_GET['foot'])){ ?>
    			<a href="/member/tree/">Tree <span class="badge"></span></a>
    			<?php } else {?>
    			<span>Tree <span class="badge"></span></span>
    			<?php }?>
			</li>
			<?php if (isset($_GET['foot'])){ ?>
    		<li><i class="fa fa-tag"></i><?php echo (($_GET['foot'] == "all")? ("ALL"):(strtoupper($_GET['foot']))); ?></li>
    		<?php }?>
    	</ol>
    </div>
</div>

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
    	<div class="panel panel-default">
	        <ul class="panel-heading nav nav-tabs">
				<li role="presentation" class="<?php echo ((!isset($_GET['affichage']) || $_GET['affichage'] == 'stack')? "active" : ""); ?>">
					<a href="/member/tree/<?=$_GET['foot'] ?>-stack.html"><span class="glyphicon glyphicon-align-left"></span> Stacked list</a>
				</li>
				<li role="presentation" class="<?php echo ((isset($_GET['affichage']) && $_GET['affichage'] == 'tree')? "active" : ""); ?>">
					<a href="/member/tree/<?=$_GET['foot'] ?>-tree.html"><span class="glyphicon glyphicon-tree-conifer"></span> Tree</a>
				</li>
			</ul>
            <section class="panel-body">
            	<?php echo $_REQUEST[AccountController::ATT_TREE_FORMATTER]->format(TreeFormatter::FORMAT_HTML); ?>
            </section>
        </div>
    </div>
</div>
<?php } ?>