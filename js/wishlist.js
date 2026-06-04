document.addEventListener("DOMContentLoaded", () => {
  const wishlistGrid = document.getElementById("wishlistGrid");
  const emptyWishlist = document.getElementById("emptyWishlist");
  const searchInput = document.getElementById("wishlistSearch");
  const sortSelect = document.getElementById("wishlistSort");

  function getCards() {
    return Array.from(document.querySelectorAll(".wishlist-card"));
  }

  function checkEmptyWishlist() {
    const visibleCards = getCards().filter((card) => card.style.display !== "none");

    if (getCards().length === 0 || visibleCards.length === 0) {
      wishlistGrid.style.display = "none";
      emptyWishlist.style.display = "block";
    } else {
      wishlistGrid.style.display = "grid";
      emptyWishlist.style.display = "none";
    }
  }

  function filterWishlist() {
    const query = searchInput.value.toLowerCase().trim();
    const filter = sortSelect.value;

    getCards().forEach((card) => {
      const name = card.dataset.name.toLowerCase();
      const type = card.dataset.type;

      const matchesSearch = name.includes(query);
      const matchesType =
        filter === "all" ||
        filter === "low" ||
        filter === "high" ||
        (filter === "products" && type === "product") ||
        (filter === "services" && type === "service");

      card.style.display = matchesSearch && matchesType ? "block" : "none";
    });

    checkEmptyWishlist();
  }

  function sortWishlist() {
    const cards = getCards();
    const sortValue = sortSelect.value;

    if (sortValue === "low") {
      cards.sort((a, b) => Number(a.dataset.price) - Number(b.dataset.price));
    }

    if (sortValue === "high") {
      cards.sort((a, b) => Number(b.dataset.price) - Number(a.dataset.price));
    }

    cards.forEach((card) => wishlistGrid.appendChild(card));
    filterWishlist();
  }

  document.querySelectorAll(".remove-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      this.closest(".wishlist-card").remove();
      checkEmptyWishlist();
    });
  });

  document.querySelectorAll(".add-cart-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const name = this.dataset.name || "Item";

      alert(name + " added to cart");
      window.location.href = "cart.php";
    });
  });

  searchInput.addEventListener("input", filterWishlist);
  sortSelect.addEventListener("change", sortWishlist);

  checkEmptyWishlist();
});