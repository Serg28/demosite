@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo')
@stop

@section('main')
    <div class="comparison">

        <div class="container">
            <h2 class="fsz-34 fw-600 heading">Порівняння товарів</h2>
            <div class="scrl mt-16">
                <div class="comparison__tabs flex v--center">
                    <div class="tab flex v--start color--gray pb-4 current relative" data-comparison-screen="1">Телефони<span class="fsz-12 ml-2">(3)</span></div>
                    <div class="tab flex v--start color--gray pb-4 relative" data-comparison-screen="2">Ноутбуки<span class="fsz-12 ml-2">(2)</span></div>
                    <div class="tab flex v--start color--gray pb-4 relative" data-comparison-screen="3">Холодильники<span class="fsz-12 ml-2">(3)</span></div>
                </div>
            </div>
        </div>
        <div class="screen screen-1 active">
            <div class="fixed-comparison-row">
                <div class="container">
                    <div class="custom-swiper-wrapper">
                        <div class="comparison-fixed-swiper swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="col flex v--center h--between">
                                        <a href="" class="img flex v--center h--center">
                                            <img src="assets/images/p-i1.png" alt="">
                                        </a>
                                        <div class="right flex fd--column">
                                            <a href="" class="name color--black fsz-14">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="bottom flex h--between v--end">
                                                <div class="price-wrap">
                                                    <s class="fsz-13 color--gray">33 999 ₴</s>
                                                    <p class="fw-600 fsz-14 color--red">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="col flex v--center h--between">
                                        <a href="" class="img flex v--center h--center">
                                            <img src="assets/images/p-i1.png" alt="">
                                        </a>
                                        <div class="right flex fd--column">
                                            <a href="" class="name color--black fsz-14">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="bottom flex h--between v--end">
                                                <div class="price-wrap">
                                                    <s class="fsz-13 color--gray">33 999 ₴</s>
                                                    <p class="fw-600 fsz-14 color--red">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="col flex v--center h--between">
                                        <a href="" class="img flex v--center h--center">
                                            <img src="assets/images/p-i1.png" alt="">
                                        </a>
                                        <div class="right flex fd--column">
                                            <a href="" class="name color--black fsz-14">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="bottom flex h--between v--end">
                                                <div class="price-wrap">
                                                    <s class="fsz-13 color--gray">33 999 ₴</s>
                                                    <p class="fw-600 fsz-14 color--red">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swiper-slide">
                                    <div class="col flex v--center h--center"><a href="" class="main-btn green"><strong>+</strong>Додати ще товар</a></div>
                                </div>
                            </div>
                            <div class="swiper-pagination custom-pagiation"></div>
                        </div>
                        <div class="custom-swiper-btn custom-swiper-btn-prev comparison-fixed-swiper-btn-prev"><img src="assets/images/arrow-blue-left.svg" alt=""></div>
                        <div class="custom-swiper-btn custom-swiper-btn-next comparison-fixed-swiper-btn-next"><img src="assets/images/arrow-blue-right-1.svg" alt=""></div>
                    </div>
                </div>
            </div>
            <div class="comparison__wrap mt-24">
                <div class="container">
                    <div class="top-row flex v--center h--between pt-16 pb-16">
                        <div class="toggler-wrapper flex v--center">
                            <p>Тільки відмінності</p>
                            <input type="checkbox" id="toggler-input-1" class="toggler-input">
                            <div class="toggler-wrap d-flex">
                                <label for="toggler-input-1" class="toggler"></label>
                            </div>
                        </div>
                        <div class="clear fsz-15 fw-600 color--blue">Очистити список</div>
                    </div>
                    <div class="comparison__swiper-wrapper-row custom-swiper-wrapper">
                        <div class="comparison-prod-swiper swiper custom-swiper" data-slider="1">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="before"></div>
                                        <div class="labels">
                                            <div class="label super-price">Супер ціна</div>
                                            <div class="label discount">-15%</div>
                                        </div>
                                        <div class="buttons">
                                            <button class="trash cart-btn">
                                                <img src="assets/images/trash.svg" alt="" class="visible">
                                            </button>
                                        </div>
                                        <div class="top">
                                            <div class="image image-1">
                                                <div class="images">
                                                    <img src="assets/images/p-i2.png" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bottom">
                                            <a href="" class="product-name fsz-16 fw-400 color--black">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="row flex v--center h--between">
                                                <div class="left">
                                                    <span class="fsz-12 color--gray">Код товару: 912455</span>
                                                </div>
                                                <div class="right">
                                                    <a href="" class="raiting flex v--center">
												<span class="stars flex v--center">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-empty.svg" alt="">
												</span>
                                                        <span class="num color--black fsz-13">129</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="bottom-row flex h--between v--end">
                                                <div class="price-wrap ">
                                                    <!-- <s class="fsz-14 color--gray">25 713 ₴</s> -->
                                                    <p class="price fsz-18 fw-600">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="before"></div>
                                        <div class="labels">
                                            <div class="label super-price">Супер ціна</div>
                                            <div class="label discount">-15%</div>
                                        </div>
                                        <div class="buttons">
                                            <button class="trash cart-btn">
                                                <img src="assets/images/trash.svg" alt="" class="visible">
                                            </button>
                                        </div>
                                        <div class="top">
                                            <div class="image image-1">
                                                <div class="images">
                                                    <img src="assets/images/p-i3.png" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bottom">
                                            <a href="" class="product-name fsz-16 fw-400 color--black">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="row flex v--center h--between">
                                                <div class="left">
                                                    <span class="fsz-12 color--gray">Код товару: 912455</span>
                                                </div>
                                                <div class="right">
                                                    <a href="" class="raiting flex v--center">
												<span class="stars flex v--center">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-empty.svg" alt="">
												</span>
                                                        <span class="num color--black fsz-13">129</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="bottom-row flex h--between v--end">
                                                <div class="price-wrap ">
                                                    <!-- <s class="fsz-14 color--gray">25 713 ₴</s> -->
                                                    <p class="price fsz-18 fw-600">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="before"></div>
                                        <div class="labels">
                                            <div class="label super-price">Супер ціна</div>
                                            <div class="label discount">-15%</div>
                                        </div>
                                        <div class="buttons">
                                            <button class="trash cart-btn">
                                                <img src="assets/images/trash.svg" alt="" class="visible">
                                            </button>
                                        </div>
                                        <div class="top">
                                            <div class="image image-1">
                                                <div class="images">
                                                    <img src="assets/images/p-i1.png" alt="">
                                                </div>
                                            </div>
                                            <!-- <div class="colors flex v--center">
                                                <span class="color product-card-color current" style="background: #A8B9C9;" data-color="1"></span>
                                                <span class="color product-card-color" style="background: #1D252B;" data-color="2"></span>
                                                <span class="color product-card-color" style="background: #E6E0EB;" data-color="3"></span>
                                                <span class="color product-card-color" style="background: #FC102F;" data-color="4"></span>
                                                <a href="" class="all-colors">+1</a>
                                            </div> -->
                                        </div>
                                        <div class="bottom">
                                            <a href="" class="product-name fsz-16 fw-400 color--black">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="row flex v--center h--between">
                                                <div class="left">
                                                    <span class="fsz-12 color--gray">Код товару: 912455</span>
                                                </div>
                                                <div class="right">
                                                    <a href="" class="raiting flex v--center">
												<span class="stars flex v--center">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-empty.svg" alt="">
												</span>
                                                        <span class="num color--black fsz-13">129</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="bottom-row flex h--between v--end">
                                                <div class="price-wrap ">
                                                    <!-- <s class="fsz-14 color--gray">25 713 ₴</s> -->
                                                    <p class="price fsz-18 fw-600">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <a href="" class="main-btn green"><strong>+</strong>Додати ще товар</a>
                                </div>
                                <!-- <div class="swiper-slide">
                                    <a href="" class="main-btn green"><strong>+</strong>Додати ще товар</a>
                                </div> -->
                            </div>
                            <div class="swiper-pagination custom-pagiation"></div>
                        </div>
                        <div class="custom-swiper-btn custom-swiper-btn-prev comparison-swiper-btn-prev"><img src="assets/images/arrow-blue-left.svg" alt=""></div>
                        <div class="custom-swiper-btn custom-swiper-btn-next comparison-swiper-btn-next"><img src="assets/images/arrow-blue-right-1.svg" alt=""></div>
                    </div>
                </div>
            </div>
            <div class="table-section mt-24">
                <div class="container">
                    <h3 class="fsz-24 fw-600">Основне</h3>
                </div>
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            <div class="table-row">
                                <span class="color--gray">Серія</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">iPhone 14</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">iPhone 14</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">iPhone 14</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                    </div>
                                    <div class="swiper-button-prev table-prev"></div>
                                    <div class="swiper-button-next table-next"></div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Рік випуску</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">2022</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2021</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2023</div>
                                        </div>
                                        <div class="swiper-slide"></div>
                                    </div>
                                    <div class="swiper-button-prev table-prev"></div>
                                    <div class="swiper-button-next table-next"></div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Операційна система</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">iOS 16</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">iOS 15</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Android 13</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-section mt-24">
                <div class="container">
                    <h3 class="fsz-24 fw-600">Зв'язок</h3>
                </div>
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            <div class="table-row">
                                <span class="color--gray">Кількість SIM-карт</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">2 SIM</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">1 SIM</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2 SIM</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Тип SIM-карти</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Nano-SIM, e-SIM</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Nano-SIM</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Nano-SIM</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Тип слоту</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">-</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">-</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">SIM + SIM</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Стандарти з’язку</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">2G, 3G, 4G (LTE), 5G</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2G, 3G, 4G (LTE), 5G</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2G, 3G, 4G (LTE), 5G</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-section mt-24">
                <div class="container">
                    <h3 class="fsz-24 fw-600">Екран</h3>
                </div>
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            <div class="table-row">
                                <span class="color--gray">Тип екрану</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">OLED</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">OLED</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Dynamic AMOLED 2X</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Діагональ екрану</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">6,1"</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">6,1"</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">6,1"</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Роздільна здатність екрану</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Super Retina XDR</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Super Retina XDR</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2340 х 1080</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Частота оновлення екрану</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">-</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">-</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">48-120 Гц</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Щільність пікселів, PPI</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">460</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">460</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">422</div>
                                        </div>
                                        <div class="swiper-slide"></div>
                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Захист скла</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Так</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Так</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Corning Gorilla Glass Victus 2</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Кількість кольорів</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">16 млн</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">16 млн</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">16 млн</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Співвідношення сторін</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">19,5:9</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">19,5:9</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">19,5:9</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Співвідношення екран/корпус </span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">87,1%</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">86%</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">88,1%</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Співвідношення екран/корпус </span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Підтримка HDR
                                                Широке колірне охоплення (P3)
                                                Олеофобне покриття, стійке до відбитків пальців</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Олеофобне покриття, стійке до появи слідів від пальців
                                                Підтримка HDR
                                                Технологія True Tone
                                                Широке колірне охоплення (P3)
                                                Тактильний відгук при натисканні
                                                Контрастність 2 000 000:1 (стандартна)
                                                Яскравість до 800 кд/м² (стандартна)
                                                Яскравість до 1200 кд/м² під час перегляду контенту у форматі HDR</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info"> HDR10+</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-section mt-24">
                <div class="container">
                    <h3 class="fsz-24 fw-600">Процесор</h3>
                </div>
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            <div class="table-row">
                                <span class="color--gray">Процесор</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Apple A15 Bionic</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Apple A15 Bionic</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Qualcomm Snapdragon 8 Gen 2</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Кількість ядер</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">6</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">6</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">8</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Частота процесора</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">3,23 ГГц</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">iOS 15</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">3,36 ГГц</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="screen screen-2">
            <div class="fixed-comparison-row">
                <div class="container">
                    <div class="custom-swiper-wrapper">
                        <div class="comparison-fixed-swiper swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="col flex v--center h--between">
                                        <a href="" class="img flex v--center h--center">
                                            <img src="assets/images/p-i1.png" alt="">
                                        </a>
                                        <div class="right flex fd--column">
                                            <a href="" class="name color--black fsz-14">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="bottom flex h--between v--end">
                                                <div class="price-wrap">
                                                    <s class="fsz-13 color--gray">33 999 ₴</s>
                                                    <p class="fw-600 fsz-14 color--red">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="col flex v--center h--between">
                                        <a href="" class="img flex v--center h--center">
                                            <img src="assets/images/p-i1.png" alt="">
                                        </a>
                                        <div class="right flex fd--column">
                                            <a href="" class="name color--black fsz-14">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="bottom flex h--between v--end">
                                                <div class="price-wrap">
                                                    <s class="fsz-13 color--gray">33 999 ₴</s>
                                                    <p class="fw-600 fsz-14 color--red">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="col flex v--center h--between">
                                        <a href="" class="img flex v--center h--center">
                                            <img src="assets/images/p-i1.png" alt="">
                                        </a>
                                        <div class="right flex fd--column">
                                            <a href="" class="name color--black fsz-14">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="bottom flex h--between v--end">
                                                <div class="price-wrap">
                                                    <s class="fsz-13 color--gray">33 999 ₴</s>
                                                    <p class="fw-600 fsz-14 color--red">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swiper-slide">
                                    <div class="col flex v--center h--center"><a href="" class="main-btn green"><strong>+</strong>Додати ще товар</a></div>
                                </div>
                            </div>
                            <div class="swiper-pagination custom-pagiation"></div>
                        </div>
                        <div class="custom-swiper-btn custom-swiper-btn-prev comparison-fixed-swiper-btn-prev"><img src="assets/images/arrow-blue-left.svg" alt=""></div>
                        <div class="custom-swiper-btn custom-swiper-btn-next comparison-fixed-swiper-btn-next"><img src="assets/images/arrow-blue-right-1.svg" alt=""></div>
                    </div>
                </div>
            </div>
            <div class="comparison__wrap mt-24">
                <div class="container">
                    <div class="top-row flex v--center h--between pt-16 pb-16">
                        <div class="toggler-wrapper flex v--center">
                            <p>Тільки відмінності</p>
                            <input type="checkbox" id="toggler-input-2" class="toggler-input">
                            <div class="toggler-wrap d-flex">
                                <label for="toggler-input-2" class="toggler"></label>
                            </div>
                        </div>
                        <div class="clear fsz-15 fw-600 color--blue">Очистити список</div>
                    </div>
                    <div class="comparison__swiper-wrapper-row custom-swiper-wrapper">
                        <div class="comparison-prod-swiper swiper custom-swiper" data-slider="2">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="before"></div>
                                        <div class="labels">
                                            <div class="label super-price">Супер ціна</div>
                                            <div class="label discount">-15%</div>
                                        </div>
                                        <div class="buttons">
                                            <button class="trash cart-btn">
                                                <img src="assets/images/trash.svg" alt="" class="visible">
                                            </button>
                                        </div>
                                        <div class="top">
                                            <div class="image image-1">
                                                <div class="images">
                                                    <img src="assets/images/p-i2.png" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bottom">
                                            <a href="" class="product-name fsz-16 fw-400 color--black">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="row flex v--center h--between">
                                                <div class="left">
                                                    <span class="fsz-12 color--gray">Код товару: 912455</span>
                                                </div>
                                                <div class="right">
                                                    <a href="" class="raiting flex v--center">
												<span class="stars flex v--center">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-empty.svg" alt="">
												</span>
                                                        <span class="num color--black fsz-13">129</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="bottom-row flex h--between v--end">
                                                <div class="price-wrap ">
                                                    <!-- <s class="fsz-14 color--gray">25 713 ₴</s> -->
                                                    <p class="price fsz-18 fw-600">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="before"></div>
                                        <div class="labels">
                                            <div class="label super-price">Супер ціна</div>
                                            <div class="label discount">-15%</div>
                                        </div>
                                        <div class="buttons">
                                            <button class="trash cart-btn">
                                                <img src="assets/images/trash.svg" alt="" class="visible">
                                            </button>
                                        </div>
                                        <div class="top">
                                            <div class="image image-1">
                                                <div class="images">
                                                    <img src="assets/images/p-i3.png" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bottom">
                                            <a href="" class="product-name fsz-16 fw-400 color--black">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="row flex v--center h--between">
                                                <div class="left">
                                                    <span class="fsz-12 color--gray">Код товару: 912455</span>
                                                </div>
                                                <div class="right">
                                                    <a href="" class="raiting flex v--center">
												<span class="stars flex v--center">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-empty.svg" alt="">
												</span>
                                                        <span class="num color--black fsz-13">129</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="bottom-row flex h--between v--end">
                                                <div class="price-wrap ">
                                                    <!-- <s class="fsz-14 color--gray">25 713 ₴</s> -->
                                                    <p class="price fsz-18 fw-600">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="before"></div>
                                        <div class="labels">
                                            <div class="label super-price">Супер ціна</div>
                                            <div class="label discount">-15%</div>
                                        </div>
                                        <div class="buttons">
                                            <button class="trash cart-btn">
                                                <img src="assets/images/trash.svg" alt="" class="visible">
                                            </button>
                                        </div>
                                        <div class="top">
                                            <div class="image image-1">
                                                <div class="images">
                                                    <img src="assets/images/p-i1.png" alt="">
                                                </div>
                                            </div>
                                            <!-- <div class="colors flex v--center">
                                                <span class="color product-card-color current" style="background: #A8B9C9;" data-color="1"></span>
                                                <span class="color product-card-color" style="background: #1D252B;" data-color="2"></span>
                                                <span class="color product-card-color" style="background: #E6E0EB;" data-color="3"></span>
                                                <span class="color product-card-color" style="background: #FC102F;" data-color="4"></span>
                                                <a href="" class="all-colors">+1</a>
                                            </div> -->
                                        </div>
                                        <div class="bottom">
                                            <a href="" class="product-name fsz-16 fw-400 color--black">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="row flex v--center h--between">
                                                <div class="left">
                                                    <span class="fsz-12 color--gray">Код товару: 912455</span>
                                                </div>
                                                <div class="right">
                                                    <a href="" class="raiting flex v--center">
												<span class="stars flex v--center">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-empty.svg" alt="">
												</span>
                                                        <span class="num color--black fsz-13">129</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="bottom-row flex h--between v--end">
                                                <div class="price-wrap ">
                                                    <!-- <s class="fsz-14 color--gray">25 713 ₴</s> -->
                                                    <p class="price fsz-18 fw-600">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <a href="" class="main-btn green"><strong>+</strong>Додати ще товар</a>
                                </div>
                                <!-- <div class="swiper-slide">
                                    <a href="" class="main-btn green"><strong>+</strong>Додати ще товар</a>
                                </div> -->
                            </div>
                            <div class="swiper-pagination custom-pagiation"></div>
                        </div>
                        <div class="custom-swiper-btn custom-swiper-btn-prev comparison-swiper-btn-prev"><img src="assets/images/arrow-blue-left.svg" alt=""></div>
                        <div class="custom-swiper-btn custom-swiper-btn-next comparison-swiper-btn-next"><img src="assets/images/arrow-blue-right-1.svg" alt=""></div>
                    </div>
                </div>
            </div>
            <div class="table-section mt-24">
                <div class="container">
                    <h3 class="fsz-24 fw-600">Основне</h3>
                </div>
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            <div class="table-row">
                                <span class="color--gray">Серія</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">iPhone 14</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">iPhone 14</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">iPhone 14</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                    </div>
                                    <div class="swiper-button-prev table-prev"></div>
                                    <div class="swiper-button-next table-next"></div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Рік випуску</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">2022</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2021</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2023</div>
                                        </div>
                                        <div class="swiper-slide"></div>
                                    </div>
                                    <div class="swiper-button-prev table-prev"></div>
                                    <div class="swiper-button-next table-next"></div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Операційна система</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">iOS 16</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">iOS 15</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Android 13</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-section mt-24">
                <div class="container">
                    <h3 class="fsz-24 fw-600">Зв'язок</h3>
                </div>
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            <div class="table-row">
                                <span class="color--gray">Кількість SIM-карт</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">2 SIM</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">1 SIM</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2 SIM</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Тип SIM-карти</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Nano-SIM, e-SIM</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Nano-SIM</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Nano-SIM</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Тип слоту</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">-</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">-</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">SIM + SIM</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Стандарти з’язку</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">2G, 3G, 4G (LTE), 5G</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2G, 3G, 4G (LTE), 5G</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2G, 3G, 4G (LTE), 5G</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-section mt-24">
                <div class="container">
                    <h3 class="fsz-24 fw-600">Екран</h3>
                </div>
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            <div class="table-row">
                                <span class="color--gray">Тип екрану</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">OLED</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">OLED</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Dynamic AMOLED 2X</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Діагональ екрану</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">6,1"</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">6,1"</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">6,1"</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Роздільна здатність екрану</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Super Retina XDR</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Super Retina XDR</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2340 х 1080</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Частота оновлення екрану</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">-</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">-</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">48-120 Гц</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Щільність пікселів, PPI</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">460</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">460</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">422</div>
                                        </div>
                                        <div class="swiper-slide"></div>
                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Захист скла</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Так</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Так</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Corning Gorilla Glass Victus 2</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Кількість кольорів</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">16 млн</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">16 млн</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">16 млн</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Співвідношення сторін</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">19,5:9</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">19,5:9</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">19,5:9</div>
                                        </div>
                                        <div class="swiper-slide"></div>
                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Співвідношення екран/корпус </span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">87,1%</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">86%</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">88,1%</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Співвідношення екран/корпус </span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Підтримка HDR
                                                Широке колірне охоплення (P3)
                                                Олеофобне покриття, стійке до відбитків пальців</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Олеофобне покриття, стійке до появи слідів від пальців
                                                Підтримка HDR
                                                Технологія True Tone
                                                Широке колірне охоплення (P3)
                                                Тактильний відгук при натисканні
                                                Контрастність 2 000 000:1 (стандартна)
                                                Яскравість до 800 кд/м² (стандартна)
                                                Яскравість до 1200 кд/м² під час перегляду контенту у форматі HDR</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info"> HDR10+</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-section mt-24">
                <div class="container">
                    <h3 class="fsz-24 fw-600">Процесор</h3>
                </div>
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            <div class="table-row">
                                <span class="color--gray">Процесор</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Apple A15 Bionic</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Apple A15 Bionic</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Qualcomm Snapdragon 8 Gen 2</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Кількість ядер</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">6</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">6</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">8</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Частота процесора</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">3,23 ГГц</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">iOS 15</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">3,36 ГГц</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="screen screen-3">
            <div class="fixed-comparison-row">
                <div class="container">
                    <div class="custom-swiper-wrapper">
                        <div class="comparison-fixed-swiper swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="col flex v--center h--between">
                                        <a href="" class="img flex v--center h--center">
                                            <img src="assets/images/p-i1.png" alt="">
                                        </a>
                                        <div class="right flex fd--column">
                                            <a href="" class="name color--black fsz-14">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="bottom flex h--between v--end">
                                                <div class="price-wrap">
                                                    <s class="fsz-13 color--gray">33 999 ₴</s>
                                                    <p class="fw-600 fsz-14 color--red">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="col flex v--center h--between">
                                        <a href="" class="img flex v--center h--center">
                                            <img src="assets/images/p-i1.png" alt="">
                                        </a>
                                        <div class="right flex fd--column">
                                            <a href="" class="name color--black fsz-14">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="bottom flex h--between v--end">
                                                <div class="price-wrap">
                                                    <s class="fsz-13 color--gray">33 999 ₴</s>
                                                    <p class="fw-600 fsz-14 color--red">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="col flex v--center h--between">
                                        <a href="" class="img flex v--center h--center">
                                            <img src="assets/images/p-i1.png" alt="">
                                        </a>
                                        <div class="right flex fd--column">
                                            <a href="" class="name color--black fsz-14">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="bottom flex h--between v--end">
                                                <div class="price-wrap">
                                                    <s class="fsz-13 color--gray">33 999 ₴</s>
                                                    <p class="fw-600 fsz-14 color--red">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swiper-slide">
                                    <div class="col flex v--center h--center"><a href="" class="main-btn green"><strong>+</strong>Додати ще товар</a></div>
                                </div>
                            </div>
                            <div class="swiper-pagination custom-pagiation"></div>
                        </div>
                        <div class="custom-swiper-btn custom-swiper-btn-prev comparison-fixed-swiper-btn-prev"><img src="assets/images/arrow-blue-left.svg" alt=""></div>
                        <div class="custom-swiper-btn custom-swiper-btn-next comparison-fixed-swiper-btn-next"><img src="assets/images/arrow-blue-right-1.svg" alt=""></div>
                    </div>
                </div>
            </div>
            <div class="comparison__wrap mt-24">
                <div class="container">
                    <div class="top-row flex v--center h--between pt-16 pb-16">
                        <div class="toggler-wrapper flex v--center">
                            <p>Тільки відмінності</p>
                            <input type="checkbox" id="toggler-input-3" class="toggler-input">
                            <div class="toggler-wrap d-flex">
                                <label for="toggler-input-3" class="toggler"></label>
                            </div>
                        </div>
                        <div class="clear fsz-15 fw-600 color--blue">Очистити список</div>
                    </div>
                    <div class="comparison__swiper-wrapper-row custom-swiper-wrapper">
                        <div class="comparison-prod-swiper swiper custom-swiper" data-slider="3">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="before"></div>
                                        <div class="labels">
                                            <div class="label super-price">Супер ціна</div>
                                            <div class="label discount">-15%</div>
                                        </div>
                                        <div class="buttons">
                                            <button class="trash cart-btn">
                                                <img src="assets/images/trash.svg" alt="" class="visible">
                                            </button>
                                        </div>
                                        <div class="top">
                                            <div class="image image-1">
                                                <div class="images">
                                                    <img src="assets/images/p-i2.png" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bottom">
                                            <a href="" class="product-name fsz-16 fw-400 color--black">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="row flex v--center h--between">
                                                <div class="left">
                                                    <span class="fsz-12 color--gray">Код товару: 912455</span>
                                                </div>
                                                <div class="right">
                                                    <a href="" class="raiting flex v--center">
												<span class="stars flex v--center">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-empty.svg" alt="">
												</span>
                                                        <span class="num color--black fsz-13">129</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="bottom-row flex h--between v--end">
                                                <div class="price-wrap ">
                                                    <!-- <s class="fsz-14 color--gray">25 713 ₴</s> -->
                                                    <p class="price fsz-18 fw-600">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="before"></div>
                                        <div class="labels">
                                            <div class="label super-price">Супер ціна</div>
                                            <div class="label discount">-15%</div>
                                        </div>
                                        <div class="buttons">
                                            <button class="trash cart-btn">
                                                <img src="assets/images/trash.svg" alt="" class="visible">
                                            </button>
                                        </div>
                                        <div class="top">
                                            <div class="image image-1">
                                                <div class="images">
                                                    <img src="assets/images/p-i3.png" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bottom">
                                            <a href="" class="product-name fsz-16 fw-400 color--black">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="row flex v--center h--between">
                                                <div class="left">
                                                    <span class="fsz-12 color--gray">Код товару: 912455</span>
                                                </div>
                                                <div class="right">
                                                    <a href="" class="raiting flex v--center">
												<span class="stars flex v--center">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-empty.svg" alt="">
												</span>
                                                        <span class="num color--black fsz-13">129</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="bottom-row flex h--between v--end">
                                                <div class="price-wrap ">
                                                    <!-- <s class="fsz-14 color--gray">25 713 ₴</s> -->
                                                    <p class="price fsz-18 fw-600">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="before"></div>
                                        <div class="labels">
                                            <div class="label super-price">Супер ціна</div>
                                            <div class="label discount">-15%</div>
                                        </div>
                                        <div class="buttons">
                                            <button class="trash cart-btn">
                                                <img src="assets/images/trash.svg" alt="" class="visible">
                                            </button>
                                        </div>
                                        <div class="top">
                                            <div class="image image-1">
                                                <div class="images">
                                                    <img src="assets/images/p-i1.png" alt="">
                                                </div>
                                            </div>
                                            <!-- <div class="colors flex v--center">
                                                <span class="color product-card-color current" style="background: #A8B9C9;" data-color="1"></span>
                                                <span class="color product-card-color" style="background: #1D252B;" data-color="2"></span>
                                                <span class="color product-card-color" style="background: #E6E0EB;" data-color="3"></span>
                                                <span class="color product-card-color" style="background: #FC102F;" data-color="4"></span>
                                                <a href="" class="all-colors">+1</a>
                                            </div> -->
                                        </div>
                                        <div class="bottom">
                                            <a href="" class="product-name fsz-16 fw-400 color--black">Смартфон Apple iPhone 14 512GB Blue (MPXN3)</a>
                                            <div class="row flex v--center h--between">
                                                <div class="left">
                                                    <span class="fsz-12 color--gray">Код товару: 912455</span>
                                                </div>
                                                <div class="right">
                                                    <a href="" class="raiting flex v--center">
												<span class="stars flex v--center">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-full.svg" alt="">
													<img src="assets/images/star-empty.svg" alt="">
												</span>
                                                        <span class="num color--black fsz-13">129</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="bottom-row flex h--between v--end">
                                                <div class="price-wrap ">
                                                    <!-- <s class="fsz-14 color--gray">25 713 ₴</s> -->
                                                    <p class="price fsz-18 fw-600">42 899 ₴</p>
                                                </div>
                                                <button class="main-btn blue-small icon-left"><span class="icon"><img src="assets/images/cart-white.svg" alt=""></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <a href="" class="main-btn green"><strong>+</strong>Додати ще товар</a>
                                </div>
                                <!-- <div class="swiper-slide">
                                    <a href="" class="main-btn green"><strong>+</strong>Додати ще товар</a>
                                </div> -->
                            </div>
                            <div class="swiper-pagination custom-pagiation"></div>
                        </div>
                        <div class="custom-swiper-btn custom-swiper-btn-prev comparison-swiper-btn-prev"><img src="assets/images/arrow-blue-left.svg" alt=""></div>
                        <div class="custom-swiper-btn custom-swiper-btn-next comparison-swiper-btn-next"><img src="assets/images/arrow-blue-right-1.svg" alt=""></div>
                    </div>
                </div>
            </div>
            <div class="table-section mt-24">
                <div class="container">
                    <h3 class="fsz-24 fw-600">Основне</h3>
                </div>
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            <div class="table-row">
                                <span class="color--gray">Серія</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">iPhone 14</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">iPhone 14</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">iPhone 14</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                    </div>
                                    <div class="swiper-button-prev table-prev"></div>
                                    <div class="swiper-button-next table-next"></div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Рік випуску</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">2022</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2021</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2023</div>
                                        </div>
                                        <div class="swiper-slide"></div>
                                    </div>
                                    <div class="swiper-button-prev table-prev"></div>
                                    <div class="swiper-button-next table-next"></div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Операційна система</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">iOS 16</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">iOS 15</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Android 13</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-section mt-24">
                <div class="container">
                    <h3 class="fsz-24 fw-600">Зв'язок</h3>
                </div>
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            <div class="table-row">
                                <span class="color--gray">Кількість SIM-карт</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">2 SIM</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">1 SIM</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2 SIM</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Тип SIM-карти</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Nano-SIM, e-SIM</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Nano-SIM</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Nano-SIM</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Тип слоту</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">-</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">-</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">SIM + SIM</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Стандарти з’язку</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">2G, 3G, 4G (LTE), 5G</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2G, 3G, 4G (LTE), 5G</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2G, 3G, 4G (LTE), 5G</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-section mt-24">
                <div class="container">
                    <h3 class="fsz-24 fw-600">Екран</h3>
                </div>
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            <div class="table-row">
                                <span class="color--gray">Тип екрану</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">OLED</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">OLED</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Dynamic AMOLED 2X</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Діагональ екрану</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">6,1"</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">6,1"</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">6,1"</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Роздільна здатність екрану</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Super Retina XDR</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Super Retina XDR</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">2340 х 1080</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Частота оновлення екрану</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">-</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">-</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">48-120 Гц</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Щільність пікселів, PPI</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">460</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">460</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">422</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Захист скла</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Так</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Так</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Corning Gorilla Glass Victus 2</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Кількість кольорів</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">16 млн</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">16 млн</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">16 млн</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Співвідношення сторін</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">19,5:9</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">19,5:9</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">19,5:9</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Співвідношення екран/корпус </span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">87,1%</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">86%</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">88,1%</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Співвідношення екран/корпус </span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Підтримка HDR
                                                Широке колірне охоплення (P3)
                                                Олеофобне покриття, стійке до відбитків пальців</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Олеофобне покриття, стійке до появи слідів від пальців
                                                Підтримка HDR
                                                Технологія True Tone
                                                Широке колірне охоплення (P3)
                                                Тактильний відгук при натисканні
                                                Контрастність 2 000 000:1 (стандартна)
                                                Яскравість до 800 кд/м² (стандартна)
                                                Яскравість до 1200 кд/м² під час перегляду контенту у форматі HDR</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info"> HDR10+</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-section mt-24">
                <div class="container">
                    <h3 class="fsz-24 fw-600">Процесор</h3>
                </div>
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            <div class="table-row">
                                <span class="color--gray">Процесор</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">Apple A15 Bionic</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Apple A15 Bionic</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">Qualcomm Snapdragon 8 Gen 2</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Кількість ядер</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">6</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">6</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">8</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-row">
                                <span class="color--gray">Частота процесора</span>
                                <div class="table-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="cell-info">3,23 ГГц</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">iOS 15</div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="cell-info">3,36 ГГц</div>
                                        </div>
                                        <div class="swiper-slide"></div>

                                        <div class="swiper-button-prev table-prev"></div>
                                        <div class="swiper-button-next table-next"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <livewire:form.subscribe />
@stop
@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/comparison.min.css')}}">
@endpush
@push('footer-styles')
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
@endpush
@push('footer-scripts')
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="/assets/js/swiper.js"></script>
@endpush

