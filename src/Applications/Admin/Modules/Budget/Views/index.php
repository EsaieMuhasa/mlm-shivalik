<?php 
    use Applications\Admin\Modules\Budget\BudgetController;
?>
<h3 class="page-header">
	<i class="fa fa-laptop"></i> <?php echo ($_REQUEST[BudgetController::ATT_VIEW_TITLE]); ?>
</h3>
<hr/>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong class="panel-title">Budget rubrics</strong>
        <a href="/admin/budget/new-rubric.html" style="margin-top: 2px;" class="btn btn-primary pull-right">
            <span class="fa fa-plus"></span> new nubric
        </a>
    </div>
    <div class="panel-body"></div>
</div>