<form class="analytics-filters" method="GET">
    <div class="analytics-filter-fields" id="analytics-filter-fields">
        @if (isset($path))<input type="hidden" name="path" value="{{ $path }}">@endif
        <label>Range<select data-date-preset><option value="custom" @selected(($filters['range'] ?? 'custom') === 'custom')>Custom dates</option><option value="today" @selected(($filters['range'] ?? null) === 'today')>Today</option><option value="yesterday" @selected(($filters['range'] ?? null) === 'yesterday')>Yesterday</option><option value="7" @selected(($filters['range'] ?? null) === '7')>Last 7 days</option><option value="30" @selected(($filters['range'] ?? null) === '30')>Last 30 days</option><option value="90" @selected(($filters['range'] ?? null) === '90')>Last 90 days</option><option value="365" @selected(($filters['range'] ?? null) === '365')>Last 365 days</option></select></label>
        <label>From<input type="date" name="from" value="{{ $filters['from']->toDateString() }}"></label><label>To<input type="date" name="to" value="{{ $filters['to']->toDateString() }}"></label>
        <label>Country<input name="country_code" maxlength="2" placeholder="US" value="{{ $filters['country_code'] ?? '' }}"></label>
        <label>Device<select name="device"><option value="">All devices</option>@foreach (['desktop', 'mobile', 'tablet', 'unknown'] as $device)<option value="{{ $device }}" @selected(($filters['device'] ?? null) === $device)>{{ ucfirst($device) }}</option>@endforeach</select></label>
        <label>Browser<input name="browser" maxlength="40" value="{{ $filters['browser'] ?? '' }}" placeholder="Any"></label>
        <label>Operating system<input name="operating_system" maxlength="40" value="{{ $filters['operating_system'] ?? '' }}" placeholder="Any"></label>
        <label>Source<input name="source" maxlength="190" value="{{ $filters['source'] ?? '' }}" placeholder="Direct or domain"></label>
        <label>Page<input name="page" maxlength="512" value="{{ $filters['page'] ?? '' }}" placeholder="/projects"></label>
        <label>Visitor<select name="visitor_type"><option value="" @selected(($filters['visitor_type'] ?? null) === null)>All visitors</option><option value="new" @selected(($filters['visitor_type'] ?? null) === 'new')>New</option><option value="returning" @selected(($filters['visitor_type'] ?? null) === 'returning')>Returning</option></select></label>
        <button class="admin-button admin-button-primary" type="submit">Apply filters</button>
    </div>
</form>
