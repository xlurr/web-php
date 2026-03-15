<?php include 'views/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><?php echo htmlspecialchars($product['name']); ?></h5>
            </div>
            <div class="card-body">
                <p><strong>Цена:</strong> <?php echo number_format($product['price'], 2); ?> руб.</p>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Отзывы покупателей</h5>
            </div>
            <div class="card-body">
                <?php if ($reviews && $reviews->num_rows > 0): ?>
                    <ul class="list-group mb-3">
                        <?php while ($review = $reviews->fetch_assoc()): ?>
                            <li class="list-group-item">
                                <span>Оценка: <?php echo $review['rating']; ?></span>
                                <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                                <small class="text-muted"><?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?></small>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>Пока нет отзывов. Будьте первым, кто оставит отзыв.</p>
                <?php endif; ?>

                <button id="toggleReviewFormBtn" class="btn btn-primary mb-3">Оставить отзыв</button>

                <div id="reviewForm" style="display:none;">
                    <form method="POST" action="?page=products&action=add_review">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="mb-3">
                            <label for="review_text" class="form-label">Отзыв</label>
                            <textarea name="review_text" id="review_text" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="rating" class="form-label">Оценка</label>
                            <select name="rating" id="rating" class="form-control" required>
                                <option value="" selected disabled>Выберите оценку</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Добавить отзыв</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('toggleReviewFormBtn').addEventListener('click', function() {
    var form = document.getElementById('reviewForm');
    if (form.style.display === 'none') {
        form.style.display = 'block';
        this.textContent = 'Скрыть форму отзыва';
    } else {
        form.style.display = 'none';
        this.textContent = 'Оставить отзыв';
    }
});
</script>

<?php include 'views/footer.php'; ?>
