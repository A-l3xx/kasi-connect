const imageInput = document.querySelector('input[name="product_image"]');

if (imageInput) {

  const previewContainer = document.createElement("div");
  previewContainer.className = "preview-grid";

  imageInput.parentNode.appendChild(previewContainer);

  imageInput.addEventListener("change", function () {

    previewContainer.innerHTML = "";

    const file = this.files[0];

    if (!file) return;

    const reader = new FileReader();

    reader.onload = function (e) {

      const previewCard = document.createElement("div");
      previewCard.className = "preview-card";

      previewCard.innerHTML = `
        <img src="${e.target.result}" alt="Preview">
      `;

      previewContainer.appendChild(previewCard);
    };

    reader.readAsDataURL(file);

  });

}