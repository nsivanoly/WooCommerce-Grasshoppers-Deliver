<section class="status">
    <h2>Order Status</h2>
    <?php if (count($statuses)): ?>
        <table class="woocommerce-table woocommerce-table-status">
            <thead>
            <tr>
                <th>REMARK</th>
                <th>TIMESTAMP</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($statuses as $status): ?>
                <tr>
                    <td><?php echo $status['REMARK']; ?></td>
                    <td><?php echo $status['TIMESTAMP']; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No Details found!</p>
    <?php endif; ?>
</section>