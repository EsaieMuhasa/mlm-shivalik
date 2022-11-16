<div class="panel panel-default">
    <div class="panel-heading">
        <strong class="panel-title">Accouts states</strong>
    </div>

    <table class="table table-bordered panel-body">
        <thead>
            <tr>
                <th>Accont name</th>
                <th>Owner</th>
                <th>Available amount</th>
                <th>Global part</th>
                <th>Specific part</th>
                <th>Cash out</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_REQUEST['accounts'] as $item) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($item->label); ?></td>
                    <td><?php echo htmlspecialchars($item->owner ? $item->owner->matricule : ''); ?></td>
                    <td><?php echo htmlspecialchars($item->available); ?> $ </td>
                    <td><?php echo htmlspecialchars($item->globalPart); ?> $ </td>
                    <td><?php echo htmlspecialchars($item->specificPart); ?> $ </td>
                    <td><?php echo htmlspecialchars($item->sumOutlays); ?> $ </td>
                    <td>
                        <a class="btn btn-primary" href="/admin/budget/cash-out/<?php echo $item->id; ?>/new">
                            <span class="fa fa-minus"></span> Withdrawal
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>