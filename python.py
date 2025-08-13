import random
import json

# Структура склада: 3 секции × 4 ряда × 3 полки × 6–10 мест
warehouse_structure = []
product_catalog = [
    "Шкаф металлический", "Стул офисный", "Стол письменный", "Лестница складная", "Коробка с проводами",
    "Доска ламинированная", "Ящик с инструментами", "Банка с краской", "Трубы ПВХ", "Канистра с маслом"
]

product_counter = 1000
used_locations = []
locations = []

def generate_product():
    global product_counter
    product = {
        "code": f"P{product_counter}",
        "name": random.choice(product_catalog),
        "quantity": random.randint(1, 15),
        "size": {
            "length": random.randint(20, 100),
            "width": random.randint(20, 100),
            "height": random.choice([30, 40, 50])  # соответствие высоте полки
        }
    }
    product_counter += 1
    return product

for section in range(1, 4):
    for row in range(1, 5):
        for shelf in range(1, 4):
            shelf_height = random.choice([30, 40, 50])
            place_count = random.randint(6, 10)
            for place in range(1, place_count + 1):
                location_id = f"S{section}-R{row}-SH{shelf}-P{place}"
                status = "free"
                product = None

                # Заполняем примерно 60% мест товарами
                if random.random() < 0.6:
                    product = generate_product()
                    status = "occupied"
                    used_locations.append(location_id)

                place_data = {
                    "section": section,
                    "row": row,
                    "shelf": shelf,
                    "place": place,
                    "location_id": location_id,
                    "status": status,
                    "shelf_height": shelf_height,
                    "place_size": {
                        "length": random.randint(30, 120),
                        "width": random.randint(30, 120),
                        "height": shelf_height
                    },
                    "product": product
                }
                locations.append(place_data)

len(locations), len([l for l in locations if l["status"] == "occupied"])
