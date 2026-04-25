<div>
    <div id="product-list">
        @foreach($items as $item)
            <div class="product-item">{{ $item->t('title') }}</div>
        @endforeach
    </div>

    <button wire:click="loadMore" id="load-more-btn">
        Показать еще
    </button>
</div>

@script
<script>


            console.log("Livewire загружен и инициализирован!");

            let allItems = []; // Массив для хранения всех уникальных элементов

            // Хук Livewire, который срабатывает перед обновлением компонента
            Livewire.hook('commit.prepare', ({ component }) => {
                if(component.name=='test-products') {
                    console.log("Подготовка коммита для: ", component.name);

                    // Получаем текущие элементы из контейнера перед обновлением
                    let productList = document.getElementById('product-list');
                    let currentItems = productList.querySelectorAll('.product-item');


                    // Заполняем allItems существующими элементами
                    allItems = []; // Очищаем массив перед обновлением
                    currentItems.forEach(item => {
                        allItems.push(item.outerHTML);
                    });

                    console.log("Текущие элементы перед добавлением: ", allItems.length);
                }
            });

            // Хук Livewire, который срабатывает после обновления DOM
            Livewire.hook('morph.updated', ({ component }) => {
                if(component.name=='test-products') {
                    console.log("DOM обновлен для компонента: ", component.name);

                    // Получаем контейнер для списка продуктов
                    let productList = document.getElementById('product-list');

                    // Получаем новые элементы, добавленные в ответе
                    let newItems = component.serverMemo.data.newItems || []; // Убедитесь, что путь правильный

                    // Добавляем новые элементы в массив allItems и в DOM
                    newItems.forEach(itemHTML => {
                        if (!allItems.includes(itemHTML)) {
                            allItems.push(itemHTML); // Сохраняем элемент в массив
                            productList.insertAdjacentHTML('beforeend', itemHTML); // Добавляем новый элемент в DOM
                            console.log("Добавлен новый элемент: ", itemHTML); // Отладка
                        }
                    });

                    console.log("Общее количество уникальных элементов: ", allItems.length); // Выводим общее количество уникальных элементов
                }
            });




</script>
@endscript