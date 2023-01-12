<?php
require_once('./services/secure.php');
if (isset($loggedIn)) :
    require_once('./services/mysql.php');
    $link = connect();
    $idMovie = $_GET['reviewMovieId'];
    $reviews = getReviews($link, $id);
?>
    <style>
        .review {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 0 1rem 0 1rem;
        }

        .reviews {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .reviewRow {
            display: flex;
            flex-direction: column;
            padding: 0 1rem 0 1rem;
        }

        .review-title {
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        p {
            margin-left: auto;
        }

        h5,
        h6 {
            margin: 0;
            padding: 0;
        }
    </style>
    <div class="reviews">
        <?php foreach ($reviews as $review) : ?>
            <div class="reviewRow">
                <div class="review-title">
                    <a href="profile.php?id=<?= $review['userId'] ?>">
                        <h5><?= getName($link, $review['userId']) ?></h5>
                    </a> &nbsp; - &nbsp;
                    <h6><?= $review['date'] ?></h6>
                    <p class="range-field disabled">
                        <input type="range" id="test5" min="1" max="5" value="<?= $review['stars'] ?>" />
                    </p>
                </div>
                <div class="review-description">
                    <?= $review['description'] ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>