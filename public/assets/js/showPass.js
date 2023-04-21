function showPass() {
  var x = document.getElementById("connexion_password");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
