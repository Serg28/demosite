<div class="account-page__content">
    <h2 class="fsz-28 fw-600 mb-24 content-heading">{{__t('Особисті данні')}}</h2>
    <div class="account-main">
        <div class="account-main__wrap p-24 br--br-4 bg--white flex fd--column">
            <div class="top-row flex fd--column">
                <p class="fsz-18 fw-600">{{__t('Ваші данні')}}</p>
                <div class="acc-info flex fd--column">
                    <div class="row flex v--center">
                        <span class="color--gray">{{__t('Ім\'я')}}</span>
                        <p>{{$user?->first_name ?? ''}}</p>
                    </div>
                    <div class="row flex v--center">
                        <span class="color--gray">{{__t('Прізвище')}}</span>
                        <p>{{$user?->last_name ?? ''}}</p>
                    </div>
                    <div class="row flex v--center">
                        <span class="color--gray">{{__t('По-батькові')}}</span>
                        <p>{{$user?->patronymic ?? ''}}</p>
                    </div>
                    <div class="row flex v--center">
                        <span class="color--gray">Email</span>
                        <p>{{$user?->email ?? ''}}</p>
                    </div>
                    <div class="row flex v--center">
                        <span class="color--gray">{{__t('Телефон')}}</span>
                        <p>{{$user?->phone ?? ''}}</p>
                    </div>
                    {{--<div class="row flex v--center">
                        <span class="color--gray">Пошта</span>
                        <p class="color--blue">Не заповнено</p>
                    </div> --}}
                </div>
                <div class="flex" style="gap: 30px">
                <a href="#" class="get-edit_ color--blue js-lw-modal" data-component="profile.user.edit-data">{{__t('Редагувати профіль')}}</a>
                <a href="#" class="get-edit_ color--blue js-lw-modal" data-component="profile.user.edit-password">{{__t('Змінити пароль')}}</a>
                </div>
            </div>
            {{--<div class="bottom-row flex fd--column">
                <p class="fsz-18 fw-600">Збережені адреси</p>
                <div class="radio-wrap flex fd--column">
                    <div class="radio-row flex v--start br--br-4 p-16 active">
                        <label for="input-radio-1" class="radio">
                            <input type="radio" id="input-radio-1" name="radio_adress" checked>
                        </label>
                        <div class="right flex fd--column">
                            <p class="fw-600">У відділення Нової Пошти</p>
                            <span>Відділення №5: вул. Федорова, 32 (м. Олімпійська), Київ</span>
                            <div class="btn-row flex v--center">
                                <div class="btn get-edit-adress color--blue">Редагувати</div>
                                <div class="btn get-delete adress color--blue">Видалити</div>
                            </div>
                        </div>
                    </div>
                    <div class="radio-row flex v--start br--br-4 p-16 ">
                        <label for="input-radio-1" class="radio">
                            <input type="radio" id="input-radio-1" name="radio_adress" >
                        </label>
                        <div class="right flex fd--column">
                            <p class="fw-600">Самовивіз з магазину SmartMag</p>
                            <span>бул. Лесі Українки, 10А, Київ</span>
                            <div class="btn-row flex v--center">
                                <div class="btn get-edit-adress color--blue">Редагувати</div>
                                <div class="btn get-delete adress color--blue">Видалити</div>
                            </div>
                        </div>
                    </div>
                    <div class="radio-row flex v--start br--br-4 p-16 ">
                        <label for="input-radio-1" class="radio">
                            <input type="radio" id="input-radio-1" name="radio_adress" >
                        </label>
                        <div class="right flex fd--column">
                            <p class="fw-600">Поштомат Нової Пошти</p>
                            <span>Поштомат №23231: вул. Причальна, 5а, Київ</span>
                            <div class="btn-row flex v--center">
                                <div class="btn get-edit-adress color--blue">Редагувати</div>
                                <div class="btn get-delete adress color--blue">Видалити</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="add-adress color--blue flex v--center"><strong>+</strong>Додати адресу</div>
            </div> --}}
        </div>
    </div>
</div>