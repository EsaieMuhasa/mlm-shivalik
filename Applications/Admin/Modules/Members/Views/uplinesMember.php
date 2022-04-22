<?php
use Applications\Admin\Modules\Members\MembersController;
use Core\Shivalik\Entities\Member;
?>

<?php 
/**
 * @var Member $member
 */
$member = $_REQUEST[MembersController::ATT_MEMBER];
?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<strong class="panel-title">Sponsor</strong>
			</div>
			<div class="panel-body">
				<div class="alert alert-info">
    				<h4><?php echo htmlspecialchars($member->getSponsor()->getNames()); ?></h4>
    				ID: <?php echo htmlspecialchars($member->getSponsor()->getMatricule()); ?><br/>
    				Username: <?php echo htmlspecialchars($member->getSponsor()->getPseudo()); ?><br/>
				</div>
				<div class="row">
					<div class="col-md-6 col-xs-6">
						<img class="thumbnail" alt="" src="/<?php echo $member->getSponsor()->getIcon()->getDefault(); ?>"/>
					</div>
					<div class="col-xs-6">
        				<img class="thumbnail" alt="" src="/<?php echo $member->getSponsor()->getPacket()->getGrade()->getIcon(); ?>"/>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<a class="btn btn-primary btn-block" href="/admin/members/<?php echo $member->getSponsor()->getId(); ?>/">
					<span class="fa fa-open"></span> Show account
				</a>
			</div>
		</div>
	</div>
	
	<div class="col-lg-6 col-md-6 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<strong class="panel-title">Parent</strong>
			</div>
			<div class="panel-body">
				<div class="alert alert-info">
    				<h4><?php echo htmlspecialchars($member->getParent()->getNames()); ?></h4>
    				ID: <?php echo htmlspecialchars($member->getParent()->getMatricule()); ?><br/>
    				Username: <?php echo htmlspecialchars($member->getParent()->getPseudo()); ?><br/>
				</div>
				<div class="row">
					<div class="col-md-6 col-xs-6">
						<img class="thumbnail" alt="" src="/<?php echo $member->getParent()->getIcon()->getDefault(); ?>"/>
					</div>
					<div class="col-xs-6">
        				<img class="thumbnail" alt="" src="/<?php echo $member->getParent()->getPacket()->getGrade()->getIcon(); ?>"/>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<a class="btn btn-primary btn-block" href="/admin/members/<?php echo $member->getParent()->getId(); ?>/">
					<span class="fa fa-open"></span> Show account
				</a>
			</div>
		</div>
	</div>
</div>