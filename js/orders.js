/* =========================
   ORDERS PAGE INTERACTIONS
========================= */

document.addEventListener("DOMContentLoaded", () => {

  /* =========================
     MODALS
  ========================= */

  const trackButtons = document.querySelectorAll(".open-track");
  const messageButtons = document.querySelectorAll(".open-message");
  const reviewButtons = document.querySelectorAll(".open-review");

  const trackModal = document.getElementById("trackModal");
  const messageModal = document.getElementById("messageModal");
  const reviewModal = document.getElementById("reviewModal");

  const closeButtons = document.querySelectorAll(".modal-close");

  // Open track modal
  trackButtons.forEach(button => {
    button.addEventListener("click", () => {
      trackModal.classList.add("show");
    });
  });

  // Open message modal
  messageButtons.forEach(button => {
    button.addEventListener("click", () => {
      messageModal.classList.add("show");
    });
  });

  // Open review modal
  reviewButtons.forEach(button => {
    button.addEventListener("click", () => {
      reviewModal.classList.add("show");
    });
  });

  /* =========================
     CLOSE MODALS
  ========================= */

  closeButtons.forEach(button => {
    button.addEventListener("click", () => {
      trackModal.classList.remove("show");
      messageModal.classList.remove("show");
      reviewModal.classList.remove("show");
    });
  });

  /* =========================
     CLOSE WHEN CLICKING OUTSIDE
  ========================= */

  window.addEventListener("click", (e) => {

    if (e.target === trackModal) {
      trackModal.classList.remove("show");
    }

    if (e.target === messageModal) {
      messageModal.classList.remove("show");
    }

    if (e.target === reviewModal) {
      reviewModal.classList.remove("show");
    }

  });

  /* =========================
     REVIEW SUBMIT
  ========================= */

  const reviewButton = document.querySelector("#reviewModal .btn-primary");

  if (reviewButton) {
    reviewButton.addEventListener("click", () => {

      alert("Review submitted successfully!");

      reviewModal.classList.remove("show");
    });
  }

  /* =========================
     MESSAGE SEND
  ========================= */

  const sendButton = document.querySelector(".chat-input .btn-primary");

  if (sendButton) {

    sendButton.addEventListener("click", () => {

      const input = document.querySelector(".chat-input input");

      if (input.value.trim() === "") return;

      const chatBox = document.querySelector(".chat-box");

      const newMessage = document.createElement("div");

      newMessage.classList.add("chat-msg", "customer");

      newMessage.textContent = input.value;

      chatBox.appendChild(newMessage);

      input.value = "";

      chatBox.scrollTop = chatBox.scrollHeight;
    });

  }

});