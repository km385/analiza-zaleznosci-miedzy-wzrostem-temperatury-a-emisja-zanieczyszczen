@props(['emissions', 'countries', 'pollutants', 'variables'])

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<canvas id="chart"></canvas>
<script>
    var emissions = @json($emissions);
    
    
    var temperatureChanges = emissions.map(function(item) {
        return item.temperatureChange;
    });

    var yearsData = emissions.map(function(item) {
        return item.year.year;
    });

    var emissionData = emissions.map(function(item) {
        return item.emissionValue;
    });
    
    var ctx = document.getElementById('chart').getContext('2d');
    var chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: yearsData,
        datasets: [{
            label: 'Temperature Change',
            type: 'bar',
            data: temperatureChanges,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
            yAxisID: 'temperature' 
        }, {
            label: 'Emissions Value',
            data: emissionData,
            backgroundColor: 'rgba(192, 75, 75, 0.2)',
            borderColor: 'rgba(192, 75, 75, 1)',
            borderWidth: 1,
            yAxisID: 'emissionData' 
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: [
                {
                    id: 'temperature',
                    type: 'linear',
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Temperature Change'
                    }
                },
                {
                    id: 'additionalData',
                    type: 'linear',
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Emission Data'
                    }
                }
            ]
        }
    }
});


</script>
<div style="padding: 2vh;">
    <span>Import files</span>
    <form action="/import/xml" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="importFile">
        <button type="submit" class="import-button">Import XML</button>
    </form>
    <form action="/import/json" method="POST" enctype="multipart/form-data" style="margin-top: 10px; margin-bottom: 10px;">
        @csrf
        <input type="file" name="importFile">
        <button type="submit" class="import-button">Import JSON</button>
    </form>
    
    <span>Export files</span><br>
    <a href="/xml?country={{ request('country') }}&variable={{ request('variable') }}&pollutant={{ request('pollutant') }}" class="export-button">Export XML</a>
    <a href="/json?country={{ request('country') }}&variable={{ request('variable') }}&pollutant={{ request('pollutant') }}" class="export-button">Export JSON</a>
    <div style="margin-bottom: 10px;"></div>
    <div class="filter-container">

        <form action="/" method="GET">
            <label for="country-select">Country:</label>
            <select id="country-select" name="country">
                <option value="" disabled>{{ $emissions->isNotEmpty() ? optional($emissions->first()->country)->name : '' }}</option>


                @foreach ($countries as $country)
                <option value="{{ $country->name }}" {{ request('country') == $country->name ? 'selected' : '' }}>
                    {{ $country->name }}
                </option>
                @endforeach
            </select>

            <label for="variable-select">Variable:</label>
            <select id="variable-select" name="variable">
                <option value="" disabled>{{ $variables->isNotEmpty() ? $variables[0] : '' }}</option>

                @foreach ($variables as $variable)
                <option value="{{ $variable }}" {{ request('variable') == $variable ? 'selected' : '' }}>
                    {{ $variable }}
                </option>
                @endforeach
            </select>

            <label for="pollutant-select">Pollutant:</label>
            <select id="pollutant-select" name="pollutant">
                <option value="" disabled>{{ $emissions->isNotEmpty() ? optional($emissions->first()->pollutant)->name : '' }}</option>


                @foreach ($pollutants as $pollutant)
                <option value="{{ $pollutant->name }}" {{ request('pollutant') == $pollutant->name ? 'selected' : '' }}>
                    {{ $pollutant->name }}
                </option>
                @endforeach
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Country</th>
                <th>Year</th>
                <th>Pollutant</th>
                <th>Variable</th>
                <th>Emission Value</th>
                <th>Temperature Change</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($emissions as $emission)
            <tr>
                <td>{{ $emission->country->name }}</td>
                <td>{{ $emission->year->year }}</td>
                <td>{{ $emission->pollutant->name }}</td>
                <td>{{ $emission->variable }}</td>
                <td>{{ $emission->emissionValue }}</td>
                <td>{{ $emission->temperatureChange }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>