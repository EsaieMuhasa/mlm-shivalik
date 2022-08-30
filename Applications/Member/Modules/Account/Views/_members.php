<?php
use Applications\Member\Modules\Account\AccountController;

?>
<div class="row">
    <div class="col-md-12">
        <section class="table-responsive">
            <table class="table table-bordered">
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