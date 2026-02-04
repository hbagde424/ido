<div class="col-md-12">
    @component('components.widget', ['class' => 'box-solid', 'title' => __( 'essentials::lang.leaves_summary_for_user', ['user' => $user->user_full_name] )])
        <div class="table-responsive table-condensed">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th></th>
                        @foreach($statuses as $status)
                            <th>
                                {{$status['name']}}
                            </th>
                        @endforeach
                        <th>@lang('essentials::lang.max_allowed_leaves')</th>
                        <th style="background-color: #e3f2fd; color: #1976d2; font-weight: bold;">
                            <i class="fa fa-calendar-check"></i> @lang('essentials::lang.remaining')
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leave_types as $leave_type)
                        <tr>
                            <th>{{$leave_type->leave_type}}</strong></th>
                            @foreach($statuses as $k => $v)
                                <td>
                                    @if(!empty($leaves_summary[$leave_type->id][$k]))
                                        {{$leaves_summary[$leave_type->id][$k]}} @lang('lang_v1.days')
                                    @else
                                        0
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                @if(!empty($leave_type->max_leave_count))
                                    {{$leave_type->max_leave_count}} @lang('lang_v1.days')
                                    @if($leave_type->leave_count_interval == 'month')
                                        (@lang('essentials::lang.within_current_month'))
                                    @elseif($leave_type->leave_count_interval == 'year')
                                        (@lang('essentials::lang.within_current_fy'))
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td style="background-color: #f3e5f5; font-weight: bold;">
                                @if(isset($remaining_leaves[$leave_type->id]))
                                    @if($remaining_leaves[$leave_type->id] === 'Unlimited')
                                        <span style="color: #4caf50;">
                                            <i class="fa fa-infinity"></i> Unlimited
                                        </span>
                                    @else
                                        @if($remaining_leaves[$leave_type->id] > 5)
                                            <span style="color: #4caf50;">
                                                <i class="fa fa-check-circle"></i> {{$remaining_leaves[$leave_type->id]}} @lang('lang_v1.days')
                                            </span>
                                        @elseif($remaining_leaves[$leave_type->id] > 0)
                                            <span style="color: #ff9800;">
                                                <i class="fa fa-exclamation-triangle"></i> {{$remaining_leaves[$leave_type->id]}} @lang('lang_v1.days')
                                            </span>
                                        @else
                                            <span style="color: #f44336;">
                                                <i class="fa fa-times-circle"></i> 0 @lang('lang_v1.days')
                                            </span>
                                        @endif
                                    @endif
                                @else
                                    <span style="color: #9e9e9e;">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th>@lang('sale.total')</th>
                        @foreach($status_summary as $count)
                            <td>{{$count}} @lang('lang_v1.days')</td>
                        @endforeach
                        <td style="background-color: #f5f5f5; font-weight: bold;">
                            @php
                                $total_max = 0;
                                foreach($leave_types as $leave_type) {
                                    if (!empty($leave_type->max_leave_count)) {
                                        $total_max += $leave_type->max_leave_count;
                                    }
                                }
                            @endphp
                            @if($total_max > 0)
                                {{$total_max}} @lang('lang_v1.days')
                            @else
                                N/A
                            @endif
                        </td>
                        <td style="background-color: #e8f5e8; font-weight: bold; color: #2e7d32;">
                            @php
                                $total_remaining = 0;
                                $has_unlimited = false;
                                foreach($remaining_leaves as $remaining) {
                                    if ($remaining === 'Unlimited') {
                                        $has_unlimited = true;
                                        break;
                                    } else {
                                        $total_remaining += $remaining;
                                    }
                                }
                            @endphp
                            @if($has_unlimited)
                                <i class="fa fa-infinity"></i> Unlimited
                            @else
                                <i class="fa fa-calendar-check"></i> {{$total_remaining}} @lang('lang_v1.days')
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endcomponent
</div>