<?php if (isset($_SESSION['notifications']) && is_array($_SESSION['notifications'])): ?>
    <?php foreach ($_SESSION['notifications'] as &$notification): ?>
        <?php if (isset($notification['status']) && $notification['status'] != 0): ?>
            <div class="notification <?php echo htmlspecialchars($notification['type']); ?>">
                <?php echo htmlspecialchars($notification['message']); ?>
            </div>
            <?php $notification['status'] = 0;?>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php unset($notification); // Boa prática: remove a referência após o loop ?>
<?php endif;?>