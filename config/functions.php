<?php
function isCurrentPage($fileName)
{
    $currentPage = basename($_SERVER['PHP_SELF']);
    return ($currentPage === $fileName);
}
?>