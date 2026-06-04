document.addEventListener("DOMContentLoaded", () => {
  const profileToggle = document.querySelector(".profile-toggle");
  const profileDropdown = document.querySelector(".profile-dropdown");

  if (!profileToggle || !profileDropdown) return;

  profileToggle.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    profileDropdown.classList.toggle("show");
  });

  profileDropdown.addEventListener("click", (e) => {
    e.stopPropagation();
  });

  document.addEventListener("click", () => {
    profileDropdown.classList.remove("show");
  });
});