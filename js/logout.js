document.addEventListener("DOMContentLoaded", () => {

  const logoutBtn = document.getElementById("logoutBtn");

  if (!logoutBtn) return;

  logoutBtn.addEventListener("click", (e) => {

    e.preventDefault();

    /*
      BACKEND LATER:
      session_destroy();
    */

    localStorage.removeItem("kc_user");
    localStorage.removeItem("kc_cart");
    localStorage.removeItem("kc_wishlist");

    window.location.href = "login.php";

  });

});