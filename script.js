const storage = {};

function addItem() {
  const code = document.getElementById("addCode").value;
  const location = document.getElementById("addLocation").value;
  if (code && location) {
    storage[code] = location;
    document.getElementById("addResult").innerText = `Товар ${code} добавлен в ${location}`;
  } else {
    document.getElementById("addResult").innerText = "Введите код и местоположение товара.";
  }
}

function moveItem() {
  const code = document.getElementById("moveCode").value;
  const newLoc = document.getElementById("moveLocation").value;
  if (storage[code]) {
    storage[code] = newLoc;
    document.getElementById("moveResult").innerText = `Товар ${code} перемещён в ${newLoc}`;
  } else {
    document.getElementById("moveResult").innerText = "Товар не найден.";
  }
}

function searchItem() {
  const code = document.getElementById("searchCode").value;
  if (storage[code]) {
    document.getElementById("searchResult").innerText = `Товар находится в: ${storage[code]}`;
  } else {
    document.getElementById("searchResult").innerText = "Товар не найден.";
  }
}

// скрипт для страницы добавления товара 
async function findAvailableLocations() {
  const name = document.getElementById("productName").value;
  const qty = document.getElementById("productQty").value;
  const length = parseInt(document.getElementById("length").value);
  const width = parseInt(document.getElementById("width").value);
  const height = parseInt(document.getElementById("height").value);

  const response = await fetch("find_locations.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ name, qty, length, width, height })
  });

  const data = await response.json();
  const dropdown = document.getElementById("locationDropdown");
  dropdown.innerHTML = "";

  data.forEach(loc => {
    const option = document.createElement("option");
    option.value = loc.location_id;
    option.textContent = `${loc.location_id} (${loc.length}×${loc.width}×${loc.height})`;
    dropdown.appendChild(option);
  });

  if (data.length > 0) {
    document.getElementById("locationSelection").style.display = "block";
  } else {
    alert("Подходящих мест не найдено.");
  }
}

async function submitProduct() {
  const name = document.getElementById("productName").value;
  const qty = document.getElementById("productQty").value;
  const length = parseInt(document.getElementById("length").value);
  const width = parseInt(document.getElementById("width").value);
  const height = parseInt(document.getElementById("height").value);
  const location_id = document.getElementById("locationDropdown").value;

  const response = await fetch("add_product.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ name, qty, length, width, height, location_id })
  });

  const result = await response.json();
  if (result.success) {
    document.getElementById("successMessage").textContent = "Товар добавлен";
  } else {
    document.getElementById("successMessage").textContent = "Ошибка при добавлении товара";
  }
}
// скрипт для страницы добавления товара КОНЕЦ
// СРИПТ ДЛЯ ПОИСКА ТОВАРА
// function searchByName() {
//   const name = document.getElementById("searchName").value;

//   fetch("search.php", {
//     method: "POST",
//     headers: {
//       "Content-Type": "application/x-www-form-urlencoded",
//     },
//     body: `action=name&name=${encodeURIComponent(name)}`
//   })
//   .then(response => response.json())
//   .then(data => {
//     const resultDiv = document.getElementById("searchResult");
//     resultDiv.innerHTML = ""; // очищаем старый результат

//     if (data.length > 0) {
//       data.forEach(product => {
//         resultDiv.innerHTML += `
//           <div class="card">
//             <p><strong>Название:</strong> ${product.name}</p>
//             <p><strong>Код товара:</strong> ${product.code}</p>
//             <p><strong>Количество:</strong> ${product.quantity}</p>
//             <p><strong>Размер:</strong> ${product.size}</p>
//             <p><strong>Секция:</strong> ${product.section}</p>
//             <p><strong>Ряд:</strong> ${product.row}</p>
//             <p><strong>Полка:</strong> ${product.shelf}</p>
//             <p><strong>Место:</strong> ${product.place}</p>
//           </div>
//         `;
//       });
//     } else {
//       resultDiv.innerHTML = "<p>Товар не найден.</p>";
//     }
//   })
//   .catch(error => {
//     console.error("Ошибка:", error);
//     document.getElementById("searchResult").innerHTML = "<p>Ошибка при поиске.</p>";
//   });
// }
//                             ПОИСК ПО НАЗВАНИЮ
 async function searchByName() {
  const name = document.getElementById("searchName").value;

  const response = await fetch("search.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `name=${encodeURIComponent(name)}`
  });

  const data = await response.json();

  if (data.error) {
    document.getElementById("results").innerHTML = `<p style="color:red;">${data.error}</p>`;
  } else if (data.length === 0) {
    document.getElementById("results").innerHTML = `<p>Ничего не найдено</p>`;
  } else {
    showProductDetails(data[0]); // отображаем первый найденный товар
  }
}



//                                     ПОИСК ПО МЕСТОПОЛОЖЕНИЮ
async function searchByLocation() {
  const section = document.getElementById("section").value;
  const row = document.getElementById("row").value;
  const shelf = document.getElementById("shelf").value;
  const place = document.getElementById("place").value;

  const response = await fetch("search_location.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `section=${section}&row=${row}&shelf=${shelf}&place=${place}`
  });

  const data = await response.json();

  if (data.error) {
    document.getElementById("results").innerHTML = `<p style="color:red;">${data.error}</p>`;
  } else if (data.length === 0) {
    document.getElementById("results").innerHTML = `<p>Ничего не найдено</p>`;
  } else {
    showProductDetails(data[0]); // отображаем первый найденный товар
  }
}

function showMoveOptions(code, length, width, height, buttonElement) {
  const container = buttonElement.nextElementSibling;
  const dropdown = container.querySelector(".locationDropdown");
  container.style.display = "block";

  fetch("find_locations.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ length, width, height })
  })
    .then(res => res.json())
    .then(data => {
      dropdown.innerHTML = "";
      if (data.length === 0) {
        const opt = document.createElement("option");
        opt.text = "Нет доступных мест";
        opt.disabled = true;
        dropdown.add(opt);
        return;
      }

      data.forEach(loc => {
        const option = document.createElement("option");
        option.value = loc.location_id;
        option.textContent = `${loc.location_id} (${loc.length}×${loc.width}×${loc.height})`;
        dropdown.appendChild(option);
      });
    });
}

function confirmMove(button, code) {
  const newLocationId = button.previousElementSibling.value;

  fetch("move_product.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ code, new_location: newLocationId })
  })
    .then(res => res.json())
    .then(result => {
      if (result.success) {
        alert("✅ Товар перемещён");
        location.reload();
      } else {
        alert("❌ Ошибка при перемещении");
      }
    });
}

function showProductDetails(product) {
  currentProduct = product;

  document.getElementById("results").innerHTML = `
    <p><strong>Название:</strong> ${product.name}</p>
    <p><strong>Код:</strong> ${product.code}</p>
    <p><strong>Количество:</strong> ${product.quantity}</p>
    <p><strong>Размер:</strong> ${product.length}×${product.width}×${product.height}</p>
    <p><strong>Местоположение:</strong> Секция ${product.section}, Ряд ${product.row}, Полка ${product.shelf}, Место ${product.place}</p>
    <button onclick="issueProduct()">Выдать товар</button>
    <button onclick="showMoveOptions('${product.code}', ${product.length}, ${product.width}, ${product.height}, this)">
                      Переместить товар
                    </button>

                    <div class="move-section" style="display:none; margin-top: 10px;">
                      <p><strong>Выберите новое местоположение товара:</strong></p>
                      <select class="locationDropdown"></select>
                      <button onclick="confirmMove(this, '${product.code}')">Сохранить</button>
                    </div>
                    <hr>

  `;
}
