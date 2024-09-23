<?php

require __DIR__ . '/inc/functions.inc.php';
require __DIR__ . '/inc/db-connect.inc.php';

date_default_timezone_set('Europe/Warsaw');

$perPage =2;
$page = (int) ($_GET['page'] ?? 1);
if ($page < 1) $page = 1;

// $page =1 , $offset => 0
// $page =2 , $offset => $perPage
// $page =3 , $offset => $perPage *2
$offset = ($page - 1) * $perPage;

$stmtCount = $pdo->prepare('SELECT COUNT(*) AS `count` FROM `entries`');
$stmtCount->execute();
$count  = $stmtCount->fetch(PDO::FETCH_ASSOC)['count'];

$numPages = ceil($count / $perPage);



$stmt  = $pdo->prepare('SELECT * FROM `entries` ORDER BY `DATE` DESC, `ID` DESC LIMIT :perPage OFFSET :offset');
$stmt->bindValue('perPage', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>
<?php require __DIR__ . '/views/header.view.php'; ?>
<h1 class="main-heading">Entries</h1>
<?php foreach ($results AS $result): ?>
<div class="card">
    <?php if (!empty($result['image'])): ?>
    <div class="card_image-container">
        <img class="card_image" src="uploads/<?php echo e($result['image']); ?>" />
    </div>
    <?php endif; ?>
    <div class="card_desc-container">
<?php 
    $dateExploded = explode('-', $result['date']);
    $timestamp = mktime(12, 0 , 0 , $dateExploded[1], $dateExploded[2], $dateExploded[0]);

?>
      <div class="card_desc-time"><?php echo e(date('d.m.Y', $timestamp)); ?></div>
        <h2 class="card_heading"><?php echo e($result['title']); ?></h2>
        <p class="card_paragraph">
        <?php echo nl2br(e($result['message'])); ?>
        </p>
    </div>
</div>
<?php endforeach; ?>


<?php if ($numPages > 1): ?>
    <ul class="pagination">
        <?php if ($page > 1): ?>
            <li class="pagination_li">
                <a 
                    class="pagination_link" 
                    href="index.php?<?php echo http_build_query(['page' => $page - 1]); ?>">⏴</a>
            </li>
        <?php endif; ?>
        <?php for($x = 1; $x <= $numPages; $x++): ?>
            <li class="pagination_li">
                <a 
                    class="pagination_link <?php if ($page === $x): ?>pagination_link-active<?php endif; ?>" 
                    href="index.php?<?php echo http_build_query(['page' => $x]); ?>">
                    <?php echo e($x); ?>
                </a>
            </li>
        <?php endfor; ?>
        <?php if ($page < $numPages): ?>
            <li class="pagination_li">
                <a 
                    class="pagination_link" 
                    href="index.php?<?php echo http_build_query(['page' => $page + 1]); ?>">⏵</a>
            </li>
        <?php endif; ?>
    </ul>
<?php endif; ?>
<?php require __DIR__ . '\views\footer.view.php'; ?>
