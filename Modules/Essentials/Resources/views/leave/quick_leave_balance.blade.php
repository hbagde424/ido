@if(!empty($balance) && count($balance) > 0)
<div class="col-md-12">
    @component('components.widget', ['class' => 'box-info', 'title' => '<i class="fa fa-calendar-check"></i> ' . __('essentials::lang.leave_remaining') . ' - <span id="balance_user_name">' . $user_name . '</span>'])
        <div class="row">
            @foreach($balance as $balance_item)
            <div class="col-md-3 col-sm-6">
                <div class="info-box" style="margin-bottom: 10px;">
                    <span class="info-box-icon" 
                          style="background-color: 
                          @if($balance_item['remaining'] === 'Unlimited') 
                              #4caf50
                          @elseif(is_numeric($balance_item['remaining']) && $balance_item['remaining'] > 5) 
                              #4caf50
                          @elseif(is_numeric($balance_item['remaining']) && $balance_item['remaining'] > 0) 
                              #ff9800
                          @else 
                              #f44336
                          @endif
                          ;">
                        <i class="fa fa-calendar-minus"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{$balance_item['leave_type']}}</span>
                        <span class="info-box-number" style="font-size: 16px;">
                            @if($balance_item['remaining'] === 'Unlimited')
                                <span style="color: #4caf50;">
                                    <i class="fa fa-infinity"></i> Unlimited
                                </span>
                            @else
                                <span style="color: 
                                @if($balance_item['remaining'] > 5) 
                                    #4caf50
                                @elseif($balance_item['remaining'] > 0) 
                                    #ff9800
                                @else 
                                    #f44336
                                @endif
                                ; font-weight: bold;">
                                    {{$balance_item['remaining']}} @lang('lang_v1.days')
                                </span>
                            @endif
                        </span>
                        <div class="progress" style="height: 4px; margin-top: 5px;">
                            @if($balance_item['max_allowed'] !== 'Unlimited' && is_numeric($balance_item['max_allowed']))
                                @php
                                    $percentage = $balance_item['max_allowed'] > 0 ? (($balance_item['remaining'] / $balance_item['max_allowed']) * 100) : 0;
                                @endphp
                                <div class="progress-bar" 
                                     style="width: {{$percentage}}%; 
                                     background-color: 
                                     @if($percentage > 50) 
                                         #4caf50
                                     @elseif($percentage > 20) 
                                         #ff9800
                                     @else 
                                         #f44336
                                     @endif
                                     ;"></div>
                            @else
                                <div class="progress-bar" style="width: 100%; background-color: #4caf50;"></div>
                            @endif
                        </div>
                        <span class="progress-description" style="font-size: 11px; color: #666;">
                            Used: {{$balance_item['used']}} / 
                            @if($balance_item['max_allowed'] === 'Unlimited')
                                ∞
                            @else
                                {{$balance_item['max_allowed']}}
                            @endif
                            @lang('lang_v1.days')
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endcomponent
</div>
@else
<div class="col-md-12">
    @component('components.widget', ['class' => 'box-warning', 'title' => '<i class="fa fa-info-circle"></i> ' . __('essentials::lang.leave_remaining')])
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> 
            @if(empty($user_name))
                Please select an employee to view leave balance.
            @else
                No leave types configured or no leave data available for <strong>{{ $user_name }}</strong>.
            @endif
        </div>
    @endcomponent
</div>
@endif