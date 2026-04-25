document.querySelectorAll('[data-ga4-click]').forEach(function (element) {
    element.addEventListener('click', function (e) {
        const product_id = this.getAttribute('data-id');
        const card = this.closest('.card');
        const listname = card.closest('.ga4_item_list_name').getAttribute('data-item_list_name');

        getCookie('analitic_list_name')
            .then(function (cArray) {
                let cObject = JSON.parse(cArray);
                cObject[product_id] = listname;
                return setCookie('analitic_list_name', JSON.stringify(cObject), 1);
            })
            .catch(function (error) {
                let cObject = {};
                cObject[product_id] = listname;
                return setCookie('analitic_list_name', JSON.stringify(cObject), 1);
            });

        if (typeof dataLayer !== 'undefined') {
            dataLayer.push({ ecommerce: null });
            dataLayer.push({
                'event': "select_item",
                'ecommerce': {
                    'items': [
                        {
                            'item_name': card.querySelector('.card-name').textContent,
                            'item_id': card.getAttribute('data-code'),
                            'price': card.getAttribute('data-sum'),
                            'item_category': card.getAttribute('data-category'),
                            'item_category2': card.getAttribute('data-category2'),
                            'item_list_name': listname,
                        }]
                }
            });
        }
    });
});

document.querySelectorAll('[data-ga4-one-click]').forEach(function (element) {
    element.addEventListener('click', function (e) {
        if (typeof dataLayer !== 'undefined') {
            dataLayer.push({ ecommerce: null });
            dataLayer.push({
                'event': "add_to_cart",
                'ecommerce': {
                    'items': [
                        {
                            'item_name': document.querySelector('meta[name="data-title"]').getAttribute('content'),
                            'item_id': document.querySelector('meta[name="data-code"]').getAttribute('content'),
                            'price': document.querySelector('meta[name="data-price"]').getAttribute('content'),
                            'item_brand': document.querySelector('meta[name="data-brand"]').getAttribute('content'),
                            'item_category': document.querySelector('meta[name="data-category"]').getAttribute('content'),
                            'item_category2': document.querySelector('meta[name="data-category2"]').getAttribute('content'),
                            'item_list_name': document.querySelector('meta[name="data-list-name"]').getAttribute('content'),
                            'quantity': 1
                        }]
                }
            });
        }
    });
});

function getCookie(name) {
    return new Promise(function (resolve, reject) {
        const value = "; " + document.cookie;
        const parts = value.split("; " + name + "=");
        if (parts.length === 2) {
            resolve(decodeURIComponent(parts.pop().split(";").shift()));
        } else {
            reject("Cookie not found");
        }
    });
}

function setCookie(name, value, days) {
    return new Promise(function (resolve, reject) {
        let expires = "";
        if (days) {
            let date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + encodeURIComponent(value || "") + expires + "; path=/; SameSite=Lax";
        resolve();
    });
}

function sendAnaliticCheckout(step = 0) {
    let ev, action, action_value;

    switch (step) {
        case 1:
            ev = 'add_contact_info';
            action = 'shipping_tier';
            action_value = '';
            break;
        case 2:
            ev = 'add_shipping_info';
            action = 'shipping_tier';
            action_value = document.querySelector('input[name="delivery_id"]:checked').getAttribute('data-name').replace(/<\/?[^>]+>/gi, '');
            break;
        case 3:
            ev = 'add_payment_info';
            action = 'payment_type';
            action_value = document.querySelector('input[name="pay_method_id"]:checked').getAttribute('data-name').replace(/<\/?[^>]+>/gi, '');
            break;
        default:
            return;
    }

    if (typeof dataLayer !== 'undefined') {
        fetch('/analitics/checkout/analitic-data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({}),
        })
            .then(response => response.json())
            .then(data => {
                if (data.googleGA4) {
                    // GA4
                    dataLayer.push({ 'ecommerce': null });
                    dataLayer.push({
                        'event': ev,
                        'ecommerce': {
                            [action]: action_value,
                            'currency': "UAH",
                            'value': data.googleGA4.total,
                            'items': data.googleGA4.products
                        }
                    });
                }
            })
            .catch((error) => {
                console.error('Fetch Error:', error);
            });
    }
}

function ga4Pusher(ecommerceData) {
    window.dataLayer = window.dataLayer || [];
    const data = Object.assign({}, ecommerceData);
    dataLayer.push(data);
}

function checkGA4Elements() {
    const scrollTop = window.scrollY;
    const windowHeight = window.innerHeight;
    const currentEls = document.querySelectorAll('.product-card[data-ga-push="0"]');
    let items = [];
    let ecommerceData = [];

    currentEls.forEach(function (el) {
        const offset = el.getBoundingClientRect();

        if (scrollTop <= offset.top && (el.offsetHeight + offset.top) < (scrollTop + windowHeight)) {
            el.setAttribute('data-ga-push', 1);
            items.push({
                'item_name': el.querySelector('.product-name').textContent,
                'item_id': el.getAttribute('data-code'),
                'price': el.getAttribute('data-sum'),
                /*'item_brand': '',
                'item_category': el.getAttribute('data-category'),
                'item_category2': el.getAttribute('data-category2'),
                'item_list_name': el.closest('.ga4_item_list_name').getAttribute('data-item_list_name'),*/
            });
        }
    });

    if (items.length > 0) {
        ecommerceData = {
            'event': 'view_item_list',
            'ecommerce': {
                'items': items,
            }
        };
    }

    return ecommerceData;
}

//document.addEventListener('DOMContentLoaded', function () {
document.addEventListener('livewire:navigated', function() {  //При использовании livewire
    // GA4 view products
    const ecommerceG4Data = checkGA4Elements();
    if (Object.keys(ecommerceG4Data).length > 0) {
        ga4Pusher(ecommerceG4Data);
    }
    // End GA4 view products
});

window.addEventListener('scroll', function () {
    // GA4 view products
    const ecommerceG4Data = checkGA4Elements();
    if (Object.keys(ecommerceG4Data).length > 0) {
        ga4Pusher(ecommerceG4Data);
    }
    // End GA4 view products
});


//Запускаем действия при сабмите любого Livewire-компонента формы
//Событие form-submitted (вызывается в компонентах форм)
Livewire.on('form-submitted', (event) => {
    if (typeof dataLayer !== 'undefined') {
        console.log('form-submitted', event);
        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            'event': 'sent_form'
        });
    }
});