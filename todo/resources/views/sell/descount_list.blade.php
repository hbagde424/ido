@extends('layouts.app')
@section('title', __('lang_v1.all_sales'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>Discount Payment Report </h1>
</section>

     @component('components.filters', ['title' => __('report.filters')])
        @include('sell.partials.sellfilternew')
        @if(!empty($sources))
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sell_list_filter_source',  __('lang_v1.sources') . ':') !!}

                    {!! Form::select('sell_list_filter_source', $sources, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
                </div>
            </div>
        @endif
    @endcomponent
    
<!-- Main content -->
<section class="content no-print">
  
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_sales')])
        @if(auth()->user()->can('direct_sell.view') ||  auth()->user()->can('view_own_sell_only') ||  auth()->user()->can('view_commission_agent_sell'))
            @php
                $custom_labels = json_decode(session('business.custom_labels'), true);
            @endphp
            <table class="table table-bordered table-striped" id="sell_table">
                <thead>
                    <tr>
                        <!--<th>@lang('messages.action')</th>-->
                        <th>@lang('messages.date')</th>
                        <th>@lang('sale.invoice_no')</th>
                        <th>Total Amount</th>
                        <th>Actual Descount</th>
                        <th>Amount Before Descount</th>
                        <!-- Add other headers here -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($tran as $sell)
                    <?php if (!in_array($sell->id, $returns)) { ?>
                        <tr>
                            <!--<td>{{ $sell->action }}</td>-->
                            <td>{{ $sell->transaction_date }}</td>
                            <td>{{ $sell->invoice_no }}</td>
                            <td>{{ $sell->final_total }}</td>
                            <td>{{ $sell->actual_discount }}</td>
                            <td><?php echo $sell->final_total + $sell->actual_descount; ?></td>
                            <!-- Add other columns here -->
                        </tr>
                        <?php } ?> 
                    @endforeach
                </tbody>
            </table>
        @endif
    @endcomponent
</section>
<!-- /.content -->

@stop

@section('javascript')
    <script>
        $(document).ready(function () {
            $('#sell_table').DataTable({
                // Add your DataTable options here
            });
        });
    </script>
@endsection
