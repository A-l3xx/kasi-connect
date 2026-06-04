<?php
require_once "../../includes/auth.php";
requireSeller();
require_once "../../includes/db.php";

$sellerId = (int)$_SESSION["user_id"];

$title = trim($_POST["title"] ?? "");
$category = trim($_POST["category"] ?? "");
$price = trim($_POST["price"] ?? "");
$locationArea = trim($_POST["location_area"] ?? "");
$description = trim($_POST["description"] ?? "");

if (
  $title === "" ||
  $category === "" ||
  $price === "" ||
  $locationArea === "" ||
  $description === "" ||
  !isset($_FILES["product_image"])
) {
  header("Location: ../../pages/add-listing.php?error=missing_fields");
  exit;
}

$imagePath = null;

if ($_FILES["product_image"]["error"] === UPLOAD_ERR_OK) {
  $uploadDir = "../../uploads/products/";

  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  $fileTmp = $_FILES["product_image"]["tmp_name"];
  $fileName = $_FILES["product_image"]["name"];
  $fileSize = $_FILES["product_image"]["size"];
  $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

  $allowedExt = ["jpg", "jpeg", "png", "webp"];

  if (!in_array($fileExt, $allowedExt)) {
    header("Location: ../../pages/add-listing.php?error=invalid_image");
    exit;
  }

  if ($fileSize > 5 * 1024 * 1024) {
    header("Location: ../../pages/add-listing.php?error=image_too_large");
    exit;
  }

  $newFileName = "product_" . $sellerId . "_" . time() . "." . $fileExt;
  $destination = $uploadDir . $newFileName;

  if (!move_uploaded_file($fileTmp, $destination)) {
    header("Location: ../../pages/add-listing.php?error=image_upload_failed");
    exit;
  }

  $imagePath = "uploads/products/" . $newFileName;
} else {
  header("Location: ../../pages/add-listing.php?error=image_required");
  exit;
}

try {
  $priceValue = (float)$price;
  $priceLabel = "R" . number_format($priceValue, 2);

  $stmt = $pdo->prepare("
    INSERT INTO products (
      seller_id,
      title,
      description,
      category,
      location_area,
      price,
      price_label,
      image_path,
      image_class,
      status,
      created_at
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'peach', 'active', NOW())
  ");

  $stmt->execute([
    $sellerId,
    $title,
    $description,
    $category,
    $locationArea,
    $priceValue,
    $priceLabel,
    $imagePath
  ]);

  header("Location: ../../pages/manage-listings.php?success=created");
  exit;

} catch (PDOException $e) {
  header("Location: ../../pages/add-listing.php?error=server_error");
  exit;
}