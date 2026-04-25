/**
 * Калькулятор оплаты частями Приватбанк
 *
 * Загружает и инициализирует скрипт калькулятора оплаты частями Приватбанка
 * и обновляет элементы на странице с расчетными значениями.
 *
 * @param {string} serviceType - Тип сервиса оплаты (например, 'II' или 'IA' для определения типа 'ia', иначе 'pp').
 * @param {number} payPartsCount - Количество частей для оплаты.
 * @param {number} totalPrice - Общая стоимость, подлежащая оплате.
 */
function payPartsPrivatBankCalcInit(serviceType, payPartsCount, totalPrice) {
    serviceType = (serviceType === 'II' || serviceType === 'IA') ? 'ia' : 'pp';
    const script = document.createElement('script');
    script.type = "text/javascript";
    script.src = 'https://ppcalc.privatbank.ua/pp_calculator/resources/js/calculator.js';

    // Слушаем, когда загрузится внешний скрипт и запускаем функцию
    script.addEventListener('load', () => {
        scriptInit(serviceType);
    });
    document.body.append(script);

    /**
     * Выполняет расчет и обновление элементов на странице.
     *
     * @param {string} type - Тип сервиса ('ia' или 'pp').
     */
    function scriptInit(type) {
        const resCalc = PP_CALCULATOR.calculatePhys(payPartsCount, totalPrice);
        const allSumm = (type === 'pp') ? resCalc.ppAll : resCalc.ipAll;
        const perMonth = (type === 'pp') ? resCalc.ppValue : resCalc.ipValue;
        //console.log(resCalc);
        // Обновление всех найденных элементов с классом 'privatpayparts_permonth'
        document.querySelectorAll('.privatpayparts_permonth').forEach(element => {
            element.textContent = perMonth;
        });

        // Обновление всех найденных элементов с классом 'privatpayparts_allSum'
        document.querySelectorAll('.privatpayparts_allSum').forEach(element => {
            element.textContent = parseFloat(allSumm.replace(/[^\d.]/ig, ''));
        });
    }
}