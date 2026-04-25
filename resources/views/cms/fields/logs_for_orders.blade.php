<div class="logs_table"></div>
<script>
    $.get( "/admin/load_order_logs/{{request()->get('id')}}", function( data ) {
        $( ".logs_table" ).html( data );
    });
</script>
<style>
    .logs_table thead {
        font-weight: bold;
    }
    .logs_table td, .logs_table head td{
        padding: 5px 3px;
    }
</style>