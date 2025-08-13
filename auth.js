document.getElementById("openModalBtn").addEventListener("click", () => {
  document.getElementById("authModal").style.display = "block";
  document.getElementById("modalBackdrop").style.display = "block";
});

document.getElementById("loginBtn").addEventListener("click", async () => {
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  const response = await fetch('login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ username, password })
  });

  const result = await response.json();

  if (result.success) {
    window.location.href = "/index.html";
  } else {
    alert("Неверное имя пользователя или пароль.");
  }
});
