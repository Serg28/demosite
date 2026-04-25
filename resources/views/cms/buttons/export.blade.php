<div class="widget-toolbar">
    <div class="btn-group">
        <button class="btn btn-default" data-toggle="modal"
                data-target="#exportfeedbacksModal">{{ __t('Экспорт') }}</button>
    </div>
</div>
<div class="modal fade" id="exportfeedbacksModal" tabindex="-1" role="dialog"
     aria-labelledby="exportfeedbacksModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-center">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Закрыть</span>
                </button>
                <h4 class="modal-title" id="exportfeedbacksModalLabel">
                    {{__t('Экспорт')}}
                </h4>
            </div>

            <div class="modal-body">

                <form class="form-horizontal" id="feedbacks-export-form" role="form" method="get"
                      action="{{ $route ?? '/' }}">
                    <div class="form-check">
                        <label class="form-check-label" for="feedbacks_id">Дата от</label>
                        <input type="date" name="date_from" value="date_from"
                               class="form-check-input"
                               id="feedbacks_id">
                    </div>

                    <div class="form-check">
                        <label class="form-check-label" for="feedbacks_id">Дата до</label>
                        <input type="date" name="date_to" value="date_to"
                               class="form-check-input" id="feedbacks_id">
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">
                    Закрыть
                </button>
                <button type="button" id="feedbacks-submit" class="btn btn-primary">
                    {{ __t('Экспорт') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-body .form-horizontal .col-sm-2,
    .modal-body .form-horizontal .col-sm-10 {
        width: 100%
    }

    .modal-body .form-horizontal .control-label {
        text-align: left;
    }

    .modal-body .form-horizontal .col-sm-offset-2 {
        margin-left: 15px;
    }

    .modal-center {
        background-color: white;
        color: black;
    }
</style>

<script>
    $("#feedbacks-submit").on('click', function (event) {
        event.preventDefault();
        $("#feedbacks-export-form").submit();
    })
</script>
