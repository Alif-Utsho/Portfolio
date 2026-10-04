<form class="analytics-filters" method="GET">
    @if (isset($path))<input type="hidden" name="path" value="{{ $path }}">@endif
    <label>Range<select data-date-preset><option value="custom">Custom dates</option><option value="today">Today</option><option value="yesterday">Yesterday</option><option value="7">Last 7 days</option><option value="30" selected>Last 30 days</option><option value="90">Last 90 days</option><option value="365">Last 365 days</option></select></label>
    <label>From<input type="date" name="from" value="{{ $filters['from']->toDateString() }}"></label><label>To<input type="date" name="to" value="{{ $filters['to']->toDateString() }}"></label>
    <label>Country<input name="country_code" maxlength="2" placeholder="US" value="{{ request('country_code') }}"></label>
    <label>Device<select name="device"><option value="">All devices</option>@foreach (['desktop', 'mobile', 'tablet', 'unknown'] as $device)<option value="{{ $device }}" @selected(request('device') === $device)>{{ ucfirst($device) }}</option>@endforeach</select></label>
    <label>Browser<input name="browser" maxlength="40" value="{{ request('browser') }}" placeholder="Any"></label>
    <label>Operating system<input name="operating_system" maxlength="40" value="{{ request('operating_system') }}" placeholder="Any"></label>
    <label>Source<input name="source" maxlength="190" value="{{ request('source') }}" placeholder="Direct or domain"></label>
    <label>Page<input name="page" maxlength="512" value="{{ request('page') }}" placeholder="/projects"></label>
    <label>Visitor<select name="visitor_type"><option value="">All visitors</option><option value="new" @selected(request('visitor_type') === 'new')>New</option><option value="returning" @selected(request('visitor_type') === 'returning')>Returning</option></select></label>
    <button class="admin-button admin-button-primary" type="submit">Apply filters</button>
</form>
