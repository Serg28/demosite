//document.addEventListener("DOMContentLoaded", function() {
document.addEventListener('livewire:navigated', function() {
    const labelContainers = document.querySelectorAll(".labels");

    labelContainers.forEach(function(container) {
        const labels = Array.from(container.querySelectorAll(".label"));

        // Устанавливаем прозрачность перед сортировкой
        container.style.opacity = "0.1";

        // Универсальная функция для преобразования значения с единицами измерения или обычного текста
        function parseUnitValue(text) {
            const trimmedText = text.trim();

            // Настройки для преобразования единиц
            const unitsMap = {
                "ТБ": 1000, // 1 ТБ = 1000 ГБ
                "ГБ": 1,    // 1 ГБ = 1 ГБ
                "кг": 1000, // 1 кг = 1000 г
                "г": 1,     // 1 г = 1 г
                "м": 100,   // 1 м = 100 см
                "см": 1     // 1 см = 1 см
            };

            // Регулярное выражение для чисел с единицами или без единиц
            const match = trimmedText.match(/^(\d+(?:\.\d+)?)\s*(ТБ|ГБ|кг|г|м|см)?$/i);

            if (match) {
                let [ , value, unit ] = match;
                value = parseFloat(value);

                // Преобразование к базовой единице
                if (unit && unitsMap[unit]) {
                    return value * unitsMap[unit];
                }

                return value; // Если нет единицы, возвращаем числовое значение как есть
            }

            // Если это обычная строка без чисел, возвращаем её как строку для алфавитной сортировки
            return trimmedText;
        }

        labels.sort(function(a, b) {
            const valueA = parseUnitValue(a.textContent);
            const valueB = parseUnitValue(b.textContent);

            // Проверка типов, чтобы сортировать числа перед строками
            if (typeof valueA === "number" && typeof valueB === "number") {
                return valueA - valueB; // Сравниваем как числа
            } else if (typeof valueA === "number") {
                return -1; // Числа идут перед строками
            } else if (typeof valueB === "number") {
                return 1;  // Числа идут перед строками
            } else {
                return valueA.localeCompare(valueB); // Сравниваем строки алфавитно
            }
        });

        labels.forEach(function(label) {
            container.appendChild(label);
        });

        // Возвращаем полную непрозрачность после сортировки
        container.style.opacity = "1";
    });
});