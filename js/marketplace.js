document.querySelectorAll(".wishlist-btn").forEach((btn) => {
  btn.addEventListener("click", function () {
    const name = this.dataset.name || "Product";
    const icon = this.querySelector("i");

    this.classList.toggle("saved");

    if (this.classList.contains("saved")) {
      icon.className = "fa-solid fa-heart";
      this.innerHTML = '<i class="fa-solid fa-heart"></i> Saved';
      alert(name + " added to wishlist");
    } else {
      icon.className = "fa-regular fa-heart";
      this.innerHTML = '<i class="fa-regular fa-heart"></i> Wishlist';
      alert(name + " removed from wishlist");
    }
  });
});

document.querySelectorAll(".cart-btn").forEach((btn) => {
  btn.addEventListener("click", function () {
    const name = this.dataset.name || "Product";
    alert(name + " added to cart");
    window.location.href = "cart.php";
  });
});