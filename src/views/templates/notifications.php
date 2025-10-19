<?php if (isset($notifications) && is_array($notifications)): ?>
    <?php foreach ($notifications as $notification): ?>
        <div class="notification <?php echo htmlspecialchars($notification['type']); ?>">
            <?php echo htmlspecialchars($notification['message']); ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>