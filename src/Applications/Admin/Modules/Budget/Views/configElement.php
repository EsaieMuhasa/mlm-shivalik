<div class="panel panel-default">
    <div class="panel-heading">
        <div>
            <strong class="panel-title">Budget rubrics</strong>
            <div class="btn-group pull-right" style="margin-top: 2px;">
                <a href="/admin/budget/new-rubric.html" class="btn btn-info">
                    <span class="fa fa-plus"></span> new rubric
                </a>
                <a href="/admin/budget/new-category.html" class="btn btn-primary">
                    <span class="fa fa-plus"></span> new category
                </a>
            </div>
        </div>
    </div>

    <ul class="panel-heading nav nav-tabs">
        <li role="presentation" class="<?php echo ((!isset($_GET['affichage']) || $_GET['affichage'] == 'budget')? "active" : ""); ?>">
            <a href="/admin/budget/element-config"><span class="fa fa-list"></span> Rubrics</a>
        </li>
        <li role="presentation" class="<?php echo ((isset($_GET['affichage']) && $_GET['affichage'] == 'categories')? "active" : ""); ?>">
            <a href="/admin/budget/element-config-categories"><span class="fa fa-table"></span> Categories</a>
        </li>
    </ul>

    <table class="table table-bordered panel-body">
        <?php if (isset($_GET['affichage']) && $_GET['affichage'] == 'categories') { ?>
        <thead>
            <tr>
                <th>Label</th>
                <th>Description</th>
                <th>recording date</th>
                <th>Option</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($_REQUEST['categories'] as $category) : ?>
            <tr>
                <td><?php echo htmlspecialchars($category->label); ?></td>
                <td><?php echo htmlspecialchars($category->description); ?></td>
                <td><?php echo htmlspecialchars($category->getFormatedDateAjout()); ?></td>
                <td>
                    <a class="btn btn-xs btn-danger" href="/admin/budget/remove-caregory-<?php echo $category->id; ?>">
                        <span class="glyphicon glyphicon-remove"></span> Delete
                    </a>
                    <a class="btn btn-xs btn-primary" href="/admin/budget/edit-caregory-<?php echo $category->id; ?>">
                        <span class="fa fa-edit"></span> Edit
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>

        <?php } else  { ?>
        <thead>
            <tr>
                <th>Label</th>
                <th>Owner</th>
                <th>recording date</th>
                <th>Option</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($_REQUEST['rubrics'] as $rubric) : ?>
            <tr>
                <td><?php echo htmlspecialchars($rubric->label); ?></td>
                <td><?php echo $rubric->owner? htmlspecialchars($rubric->owner->getFullName()) : '-' ; ?></td>
                <td><?php echo htmlspecialchars($rubric->getFormatedDateAjout('d/m/Y H\h:i')); ?></td>
                <td>
                    <a class="btn btn-xs btn-danger" href="/admin/budget/remove-rubric-<?php echo $rubric->id; ?>">
                        <span class="glyphicon glyphicon-remove"></span> Delete
                    </a>
                    <a class="btn btn-xs btn-primary" href="/admin/budget/edit-rubric-<?php echo $rubric->id; ?>">
                        <span class="fa fa-edit"></span> Edit
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <?php } ?>

        <?php ?>
    </table>
</div>