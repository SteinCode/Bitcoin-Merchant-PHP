function toggleVerifiedPayerFields() {
  var checkbox = document.getElementById("verifiedPayersOnly");
  var fields = document.getElementById("verifiedPayerFields");
  fields.style.display = checkbox.checked ? "block" : "none";
}
