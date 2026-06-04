document.addEventListener("DOMContentLoaded", () => {
  const deliveryFee = 40;
  const serviceFee = 10;

  const itemCountEl = document.querySelector("[data-summary='items']");
  const subtotalEl = document.querySelector("[data-summary='subtotal']");
  const deliveryEl = document.querySelector("[data-summary='delivery']");
  const serviceEl = document.querySelector("[data-summary='service']");
  const totalEl = document.querySelector("[data-summary='total']");
  const cartLayout = document.querySelector(".cart-layout");
  const emptyCart = document.querySelector(".empty-cart");

  function money(amount) {
    return "R" + amount.toFixed(0);
  }

  function updateCart() {
    let subtotal = 0;
    let itemCount = 0;

    document.querySelectorAll(".cart-item").forEach((item) => {
      const price = Number(item.dataset.price);
      const qty = Number(item.querySelector(".qty-number").textContent);

      subtotal += price * qty;
      itemCount += qty;

      item.querySelector(".item-total").textContent = money(price * qty);
    });

    itemCountEl.textContent = itemCount;
    subtotalEl.textContent = money(subtotal);
    deliveryEl.textContent = money(itemCount > 0 ? deliveryFee : 0);
    serviceEl.textContent = money(itemCount > 0 ? serviceFee : 0);
    totalEl.textContent = money(itemCount > 0 ? subtotal + deliveryFee + serviceFee : 0);
  }

  function checkEmptyCart() {
    const remaining = document.querySelectorAll(".cart-item").length;

    if (remaining === 0) {
      cartLayout.style.display = "none";
      emptyCart.style.display = "block";
    }
  }

  function attachCartEvents() {
    document.querySelectorAll(".cart-item").forEach((item) => {
      const minusBtn = item.querySelector(".qty-minus");
      const plusBtn = item.querySelector(".qty-plus");
      const qtyEl = item.querySelector(".qty-number");
      const removeBtn = item.querySelector(".remove-btn");
      const wishlistBtn = item.querySelector(".wishlist-btn");

      minusBtn.addEventListener("click", () => {
        let qty = Number(qtyEl.textContent);

        if (qty > 1) {
          qtyEl.textContent = qty - 1;
          updateCart();
        }
      });

      plusBtn.addEventListener("click", () => {
        let qty = Number(qtyEl.textContent);
        qtyEl.textContent = qty + 1;
        updateCart();
      });

      removeBtn.addEventListener("click", () => {
        item.remove();
        updateCart();
        checkEmptyCart();
      });

      wishlistBtn.addEventListener("click", () => {
        alert("Item moved to wishlist");
        item.remove();
        updateCart();
        checkEmptyCart();
      });
    });
  }

  attachCartEvents();
  updateCart();
  checkEmptyCart();
});