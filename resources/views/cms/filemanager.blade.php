<section id="widget-grid" class="">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-right: 0px; padding-left: 0px;">
            <div id="table-preloader" class="smoke_lol"><i class="fa fa-gear fa-4x fa-spin"></i></div>
            <div class="jarviswidget jarviswidget-color-blue" id="wid-id-1" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">

                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>{{__cms('Медиахранилище')}}</h2>
                </header>
                <div>
                    <div class="jarviswidget-editbox"></div>
                    <div class="widget-body no-padding">
                        {{--<iframe width="100%" onresize="onIframeLoad()"
                                src="/packages/tinymce4/plugins/responsivefilemanager/filemanager/dialog.php?type=4&descending=false&lang=ru&akey=key"
                                frameborder="0" scrolling="no" id="print_frame"></iframe>  --}}

                        <div id="mceu_209" class="mce-container mce-panel mce-floatpanel mce-window mce-in" hidefocus="1" role="dialog" aria-labelledby="mceu_209" aria-describedby="mceu_209-none" aria-label="RESPONSIVE FileManager" style="border-width: 1px;z-index: 65536;left: 60.5px;top: 0.5px;/* width: 1546px; *//* height: 564px; *//* transform: scale(1); */"><div class="mce-reset" role="application"><div id="mceu_209-body" class="mce-container-body mce-window-body mce-abs-layout" style="width: 100%;height: calc(100vh - 125px);"><iframe src="/packages/tinymce4/plugins/responsivefilemanager/filemanager/dialog.php?type=4&amp;descending=false&amp;lang=ru&amp;akey=key" tabindex="-1" style="width: 100%;border: none;height: 100%;"></iframe></div></div></div>



                    </div>
                </div>
            </div>
        </article>
    </div>
</section>


<style>
    #content_admin.row {
        margin: 0;
    }

    #content {
        height: 100%;
        padding-bottom: 0; /**/
    }
    #main {
        padding-bottom: 0;/**/
    }

    #container {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

</style>
<script type="text/javascript">
    /*function onIframeLoad() {
        ifrhgh();
    }

    function ifrhgh() {
        const iframehght = $("iframe").contents().find('.container-fluid').height();
        $("iframe").height(iframehght + 50);
    }

    $(document).resize(function () {
        ifrhgh();
    });

    $(document).ready(function () {
        // Выбираем iframe по его id
        const iframe = $('#print_frame');

        // Устанавливаем обработчик события загрузки содержимого iframe
        iframe.on('load', function () {
            // Вызов функции после загрузки содержимого iframe
            onIframeLoad();
        });

        // Проверяем готовность содержимого iframe
        const checkIframeReadyState = function () {
            if (iframe[0].contentWindow.document.readyState === 'complete') {
                onIframeLoad();
            } else {
                setTimeout(checkIframeReadyState, 100);
            }
        };

        checkIframeReadyState();
    });*/
</script>
