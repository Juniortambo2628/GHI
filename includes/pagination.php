<?php
/**
 * Reusable Pagination Component
 *
 * @param int $currentPage - Current page number
 * @param int $totalPages - Total number of pages
 * @param string $baseUrl - Base URL for pagination links
 * @param array $queryParams - Additional query parameters to preserve
 */

$currentPage ??= 1;
$totalPages ??= 1;
$baseUrl ??= $_SERVER['PHP_SELF'];
$queryParams ??= [];

// Preserve current query parameters
$currentParams = $_GET;
unset($currentParams['page']);

// Merge with provided params
$allParams = array_merge($currentParams, $queryParams);

// Build query string
$queryString = '';
if ($allParams !== []) {
    $queryString = '?' . http_build_query($allParams);
}

if ($totalPages <= 1) {
    return; // Don't show pagination if only one page
}
?>

<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
        <!-- Previous -->
        <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $currentPage > 1 ? $baseUrl . $queryString . ($queryString !== '' && $queryString !== '0' ? '&' : '?') . 'page=' . ($currentPage - 1) : '#'; ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        
        <!-- Page Numbers -->
        <?php
        $startPage = max(1, $currentPage - 2);
$endPage = min($totalPages, $currentPage + 2);

if ($startPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo $baseUrl . $queryString . ($queryString !== '' && $queryString !== '0' ? '&' : '?') . 'page=1'; ?>">1</a>
            </li>
            <?php if ($startPage > 2): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php endif;
 ?>
<?php endif;
 ?>
        
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo $baseUrl . $queryString . ($queryString !== '' && $queryString !== '0' ? '&' : '?') . 'page=' . $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor;
 ?>
        
        <?php if ($endPage < $totalPages): ?>
            <?php if ($endPage < $totalPages - 1): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php endif;
 ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo $baseUrl . $queryString . ($queryString !== '' && $queryString !== '0' ? '&' : '?') . 'page=' . $totalPages; ?>"><?php echo $totalPages; ?></a>
            </li>
<?php endif;
 ?>
        
        <!-- Next -->
        <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $currentPage < $totalPages ? $baseUrl . $queryString . ($queryString !== '' && $queryString !== '0' ? '&' : '?') . 'page=' . ($currentPage + 1) : '#'; ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>

