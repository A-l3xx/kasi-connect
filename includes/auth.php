<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/* REQUIRE LOGIN */

function requireLogin() {

  if (!isset($_SESSION["user_id"])) {

    header("Location: ../pages/login.php");
    exit;
  }
}

/* REQUIRE ADMIN */

function requireAdmin() {

  requireLogin();

  if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
  ) {

    header("Location: ../pages/home.php");
    exit;
  }
}

/* REQUIRE SELLER */

function requireSeller() {

  requireLogin();

  if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "seller"
  ) {

    header("Location: ../pages/home.php");
    exit;
  }
}

/* REQUIRE CUSTOMER */

function requireCustomer() {

  requireLogin();

  if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "customer"
  ) {

    header("Location: ../pages/home.php");
    exit;
  }
}