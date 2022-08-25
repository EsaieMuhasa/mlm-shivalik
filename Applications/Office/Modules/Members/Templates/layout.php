<?php
use Applications\Office\Modules\Members\MembersController;
use PHPBackend\Page;

?>

<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header">
    		<a href="/office/members" class=""><i style="color: #00109A;" class="fa fa-users"></i></a> <?php echo ($_REQUEST[MembersController::ATT_VIEW_TITLE]); ?>
    	</h3>
    </div>
</div>

<?php 
if(isset($_GET['id'])) {
	require_once 'nav-member.php';
}
?>

<?php echo $_REQUEST[Page::ATT_VIEW]; ?>