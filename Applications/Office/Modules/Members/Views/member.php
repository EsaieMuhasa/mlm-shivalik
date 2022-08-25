<?php
use Applications\Admin\Modules\Members\MembersController;
use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\Member;

/**
 * @var Member $member
 */
$member = $_REQUEST[MembersController::ATT_MEMBER];

/**
 * @var Account $compte
 */
$compte = $_REQUEST[MembersController::ATT_COMPTE];


/**
 * @var GradeMember $gradeMember
 * @var GradeMember $requestedGradeMember
 */
if (isset($_REQUEST[MembersController::ATT_GRADE_MEMBER])) {
	$gradeMember = isset($_REQUEST[MembersController::ATT_GRADE_MEMBER])? $_REQUEST[MembersController::ATT_GRADE_MEMBER] : null;
}else {
	$gradeMember = null;
}
$requestedGradeMember = isset($_REQUEST[MembersController::ATT_REQUESTED_GRADE_MEMBER])? $_REQUEST[MembersController::ATT_REQUESTED_GRADE_MEMBER]:null;

?>

<div class="row">
    <div class="col-md-2 col-sm-3 col-xs-3">
        <div class="thumbnail text-left text-center">
            <img style="" alt="" src="/<?php echo ("{$gradeMember->getGrade()->getIcon()}") ?>">
        </div>
    </div>
    <div class="col-md-2 col-sm-3 col-xs-3">
        <div class="thumbnail text-left text-center">
            <img style="" alt="" src="/<?php echo ("{$member->getPhoto()}") ?>">
        </div>
    </div>
    <div class="col-md-8 col-sm-6 col-xs-6">
        <p class="h4 text-primary"><?php echo ("{$member->matricule}") ?></p>
        <p class="h5"><?php echo ("{$member->names}") ?></p>
        <p class="h5"><?php echo ("Username: {$member->pseudo}") ?></p>
        <p class="h5"><?php echo ("Telephone: {$member->telephone}") ?></p>
    </div>
</div>
<hr/>
<h2 class="text-center">Uplines</h2>
<div class="row">
    <div class="col-md-6 col-sm-6">
        <div class="thumbnail">
            <h3 class="text-info">Sponsor</h3><hr/>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <div class="thumbnail text-left text-center">
                                <img style="" alt="" src="/<?php echo ("{$gradeMember->getGrade()->getIcon()}") ?>">
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="thumbnail text-left text-center">
                                <img style="" alt="" src="/<?php echo ("{$member->getPhoto()}") ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-9">
                    <p class="h4 text-primary"><?php echo ("{$member->matricule}") ?></p>
                    <p class="h5"><?php echo ("{$member->names}") ?></p>
                    <p class="h5"><?php echo ("Username: {$member->pseudo}") ?></p>
                    <p class="h5"><?php echo ("Telephone: {$member->telephone}") ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6">
        <div class="thumbnail">
            <h3 class="text-info">Parent</h3><hr/>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <div class="thumbnail text-left text-center">
                                <img style="" alt="" src="/<?php echo ("{$gradeMember->getGrade()->getIcon()}") ?>">
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="thumbnail text-left text-center">
                                <img style="" alt="" src="/<?php echo ("{$member->getPhoto()}") ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-9">
                    <p class="h4 text-primary"><?php echo ("{$member->matricule}") ?></p>
                    <p class="h5"><?php echo ("{$member->names}") ?></p>
                    <p class="h5"><?php echo ("Username: {$member->pseudo}") ?></p>
                    <p class="h5"><?php echo ("Telephone: {$member->telephone}") ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
