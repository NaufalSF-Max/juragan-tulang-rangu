<?php

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'employee'])) {
    header("Location: ../../login.php");
    exit();
}
