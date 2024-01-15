document.addEventListener("DOMContentLoaded", function () {
  fetchFormData();
  attachLinkEventHandlers();
  setupEscapeKeyListener();
});

function fetchFormData() {
  fetch("index.php?action=getFormData")
    .then((response) => response.json())
    .then((data) => {
      populateForm(data);
    })
    .catch((error) => console.error("Error fetching form data:", error));
}

function populateForm(data) {
  if (!data) return;
  document.getElementById("project_id").value = data.project_id || "";
  document.getElementById("payCurrency").value = data.payCurrency || "";
  document.getElementById("payAmount").value = data.payAmount || "";
  document.getElementById("receiveCurrency").value = data.receiveCurrency || "";
  document.getElementById("receiveAmount").value = data.receiveAmount || "";
  document.getElementById("description").value = data.description || "";
  document.getElementById("lang").value = data.lang || "";
  document.getElementById("payNetworkName").value = data.payNetworkName || "";
  document.getElementById("payerName").value = data.payerName || "";
  document.getElementById("payerSurname").value = data.payerSurname || "";
  document.getElementById("payerEmail").value = data.payerEmail || "";
  document.getElementById("payerDateOfBirth").value =
    data.payerDateOfBirth || "";
}

function toggleVerifiedPayerFields() {
  var checkbox = document.getElementById("verifiedPayersOnly");
  var fields = document.getElementById("verifiedPayerFields");
  fields.style.display = checkbox.checked ? "block" : "none";
}

function attachLinkEventHandlers() {
  attachLinkEventHandler("debug.php", "debugLogWindow");
  attachLinkEventHandler("orders.php", "ordersWindow");
}

function attachLinkEventHandler(href, windowId) {
  var link = document.querySelector(`.header-link[href="${href}"]`);
  if (link) {
    link.addEventListener("click", function (e) {
      e.preventDefault(); // Prevent default link behavior
      toggleWindow(windowId);
    });
  }
}

function toggleWindow(windowId) {
  var windowElement = document.getElementById(windowId);
  if (windowElement) {
    windowElement.classList.toggle("show-window");
  }
}

function setupEscapeKeyListener() {
  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      closeAllWindows();
    }
  });
}

function closeAllWindows() {
  var windows = document.querySelectorAll(".side-window");
  windows.forEach(function (window) {
    if (window.classList.contains("show-window")) {
      window.classList.remove("show-window");
    }
  });
}
