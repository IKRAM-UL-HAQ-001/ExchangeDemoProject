@extends("layout.main")
@section('content')
    <div class="container-fluid pb-5">
        <br>
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 mt-5">
            <div class="bg-gradient-warning shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                <p style="color: black;"><strong>Reports</strong></p>
                <div>
                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#reportModal">Show Report</button>
                </div>
            </div>
        </div>
        
        <br>
        <div class="row">
            <div class="container">
                <div class="row">
                    <!-- Withdrawals and Deposits Chart -->
                    <div class="col-lg-4 col-sm-12 mb-4">
                        <div class="card equal-height">
                            <div class="card-header">
                                <h4 class="card-title">Withdrawals and Deposits</h4>
                                <span id="withdrawalsDepositsLabel" style="color:black">Date Range: N/A</span>
                            </div>
                            <div class="card-body">
                                <canvas id="withdrawalsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Expense and Bonus Chart -->
                    <div class="col-lg-4 col-sm-12 mb-4">
                        <div class="card equal-height">
                            <div class="card-header">
                                <h4 class="card-title">Expense and Bonus</h4>
                                <span id="expenseBonusLabel" style="color:black">Date Range: N/A</span>
                            </div>
                            <div class="card-body">
                                <canvas id="profitsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Total Exchange Profit Chart -->
                    <div class="col-lg-4 col-sm-12 mb-4">
                        <div class="card equal-height">
                            <div class="card-header">
                                <h4 class="card-title">Total Exchange Profit</h4>
                                <span id="exchangeProfitLabel" style="color:black">Date Range: N/A</span>
                            </div>
                            <div class="card-body">
                                <canvas id="bonusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Modal -->
        <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center bg-warning">
                        <h5 class="modal-title" id="reportModalLabel" style="color:white">Generate Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="reportForm">
                            @csrf
                            <div class="mb-3">
                                <label for="exchange" class="form-label">Exchange</label>
                                <select class="form-select px-3" id="exchange" name="exchange_id" required>
                                    <option value="" disabled selected>Select an exchange</option>
                                    @foreach($exchangeRecords as $exchange)
                                        <option value="{{ $exchange->id }}">{{ $exchange->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="sdate" class="form-label">Start Date:</label>
                                <input type="date" class="form-control border px-3" id="sdate" name="start_date" required
                                       value="{{ \Carbon\Carbon::today()->toDateString() }}">
                            </div>
                            <div class="mb-3">
                                <label for="edate" class="form-label">End Date:</label>
                                <input type="date" class="form-control border px-3" id="edate" name="end_date" required
                                       value="{{ \Carbon\Carbon::today()->toDateString() }}">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning" id="generateReportBtn">
                            <span id="btnText">Generate Report</span>
                            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery and Chart.js -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Chart Initialization and Report Generation Script -->
    <script>
        $(document).ready(function() {
            "use strict";
    
            // Initialize Charts
            let withdrawalsChart = initializeChart('withdrawalsChart', ['Withdrawals', 'Deposits'], ['#C00A27', '#75B432']);
            let profitsChart = initializeChart('profitsChart', ['Expenses', 'Bonuses'], ['#75B432', '#E0E0E0']);
            let bonusChart = initializeChart('bonusChart', ['Total Exchange Profit'], ['#FFCE56']);
    
            // Initialize a Chart
            function initializeChart(canvasId, labels, colors) {
                return new Chart(document.getElementById(canvasId).getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: [0, 0],
                            backgroundColor: colors,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                             tooltip: {
                                bodyFont: {
                                    size: 16, // Increase font size on hover
                                },
                                titleFont: {
                                    size: 18, // Increase title font size
                                },
                            }
                        }
                    }
                });
            }
    
            // Update Chart Data
            function updateChartData(chart, data, labels = null) {
                chart.data.datasets[0].data = data;
                if (labels) {
                    chart.data.labels = labels; // Update labels dynamically if provided
                }
                chart.update();
            }
    
            // Generate Report Function
            function generateReport(exchangeId, startDate, endDate) {
                $.ajax({
                    url: '{{ route("admin.report.generate") }}',
                    method: 'POST',
                    data: {
                        exchange_id: exchangeId,
                        start_date: startDate,
                        end_date: endDate,
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        $('#btnText').text('Generating...');
                        $('#btnSpinner').removeClass('d-none');
                        $('#generateReportBtn').prop('disabled', true);
                    },
                    success: function(response) {
                        // Calculate the Total Exchange Profit
                        const totalProfit = response.latestBalance;
                        const profitSign = totalProfit >= 0 ? "+" : "−";

                        // Update Charts with New Data
                        updateChartData(withdrawalsChart, [response.withdrawal, response.deposit]);
                        updateChartData(profitsChart, [response.expense, response.bonus]);
                        updateChartData(bonusChart, [Math.abs(totalProfit)], [`${profitSign} ${Math.abs(totalProfit).toFixed(2)}`]);

                        // Update Labels with Date Range
                        $('#withdrawalsDepositsLabel').text(`Date Range: ${response.date_range.start} to ${response.date_range.end}`);
                        $('#expenseBonusLabel').text(`Date Range: ${response.date_range.start} to ${response.date_range.end}`);
                        $('#exchangeProfitLabel').html(`Date Range: ${response.date_range.start} to ${response.date_range.end} | <strong>Total Profit: ${profitSign}${Math.abs(totalProfit).toFixed(2)}</strong>`);

                        // Show Success Message
                        $('#success').removeClass('d-none').text('Report generated successfully.');

                        // Close the Modal after a Short Delay
                        setTimeout(function() {
                            $('#reportModal').modal('hide');
                        }, 1000);
                    },
                    error: function(xhr) {
                        // Handle Errors
                        let errorMsg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        $('#error').removeClass('d-none').text(errorMsg);
                    },
                    complete: function() {
                        // Reset Button Text and State
                        $('#btnText').text('Generate Report');
                        $('#btnSpinner').addClass('d-none');
                        $('#generateReportBtn').prop('disabled', false);
                    }
                });
            }
    
            // Handle Generate Report Button Click
            $('#generateReportBtn').on('click', function() {
                // Get Selected Values from Form
                const exchangeId = $('#exchange').val();
                const startDate = $('#sdate').val();
                const endDate = $('#edate').val();
    
                // Validate Inputs
                if (!exchangeId || !startDate || !endDate) {
                    $('#error').removeClass('d-none').text('All fields are required.');
                    return;
                }
    
                // Call Generate Report Function
                generateReport(exchangeId, startDate, endDate);
            });
        });
    </script>
@endsection
