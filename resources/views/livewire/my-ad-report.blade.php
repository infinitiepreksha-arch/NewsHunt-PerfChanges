<div>
    <div>
        <!-- Cards -->
        <div>
            <div class="mb-3 ml-1 dark:text-white font-semibold text-lg">{{ __('frontend-labels.sponsor_ads.clicks_summary') }}</div>
            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4 rounded">
                <div class="p-5 bg-white dark:bg-gray-600 dark:text-gray-100 rounded ">
                    <div class="text-sm">{{ __('frontend-labels.sponsor_ads.today_so_far') }}</div>
                    <div class="text-lg mt-2 text-purple-700 font-semibold dark:text-white">{{ $totalClicksToday }}</div>
                </div>
                <div class="p-5 bg-white dark:bg-gray-600 dark:text-gray-100 rounded ">
                    <div class="text-sm">{{ __('frontend-labels.sponsor_ads.yesterday') }}  </div>
                    <div class="text-lg mt-2 text-purple-700 font-semibold dark:text-white">{{ $totalClicksYesterday }}
                    </div>
                </div>
                <div class="p-5 bg-white dark:bg-gray-600 dark:text-gray-100 rounded ">
                    <div class="text-sm">{{ __('frontend-labels.sponsor_ads.last_7_days') }}</div>
                    <div class="text-lg mt-2 text-purple-700 font-semibold dark:text-white">{{ $totalClicks7Days }}
                    </div>
                </div>
                <div class="p-5 bg-white dark:bg-gray-600 dark:text-gray-100 rounded ">
                    <div class="text-sm">{{ __('frontend-labels.sponsor_ads.this_month') }}</div>
                    <div class="text-lg mt-2 text-purple-700 font-semibold dark:text-white">{{ $totalClicksThisMonth }}
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-5 bg-white dark:bg-gray-600 dark:text-gray-100 rounded p-3">
            <div class="flex justify-between">
                <div class="mb-3 ml-1 font-semibold bg-white dark:bg-gray-600 dark:text-gray-100  p-3 fs-1">{{ __('frontend-labels.sponsor_ads.click_report_by_date') }}
                </div>
                <!-- Date range picker -->
                <div class="mr-7 mb-3">
                    <div wire:ignore id="reportrange"
                        class="flex items-center space-x-2 bg-white dark:bg-gray-600 dark:text-gray-100 border p-3 rounded cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <span></span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>

                    </div>
                </div>
            </div>
            <div style="height: 400px">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="text/javascript">
        $(function() {
            let myChart = null; // Store chart instance

            /** Date picker code start */
            var start = moment().subtract(6, 'days');
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
                @this.set('reportStartDate', start.format('YYYY-MM-DD'));
                @this.set('reportEndDate', end.format('YYYY-MM-DD'));
                @this.calculateClicksReport();
            }

            $('#reportrange').daterangepicker({
                opens: 'left',
                startDate: start,
                endDate: end,
                autoApply: true,
                maxDate: moment(),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                },
                alwaysShowCalendars: true,
            }, cb);

            cb(start, end);

            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                @this.set('reportStartDate', picker.startDate.format('YYYY-MM-DD'));
                @this.set('reportEndDate', picker.endDate.format('YYYY-MM-DD'));
                @this.calculateClicksReport();
            });
            /** Date picker code ends */

            // Function to create/update chart
            function createOrUpdateChart() {
                const ctx = document.getElementById('myChart');

                if (!ctx) {
                    console.error('Chart canvas not found');
                    return;
                }

                const clicksPerDate = @this.clicksPerDate || {};
                const labels = Object.keys(clicksPerDate);
                const data = Object.values(clicksPerDate);

                // Destroy existing chart if it exists
                if (myChart) {
                    myChart.destroy();
                }

                // Create new chart
                myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '# of Clicks',
                            data: data,
                            borderColor: 'rgb(79, 70, 229)',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            fill: false,
                            tension: 0.1,
                            pointBackgroundColor: 'rgb(79, 70, 229)',
                            pointBorderColor: 'rgb(79, 70, 229)',
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });
            }

            // Listen for the renderChart event
            Livewire.on('renderChart', function(postId) {
                // Add small delay to ensure data is updated
                setTimeout(function() {
                    createOrUpdateChart();
                }, 100);
            });

            // Also listen for chartDataReady event (if you update backend)
            Livewire.on('chartDataReady', function(data) {
                setTimeout(function() {
                    createOrUpdateChart();
                }, 100);
            });

            // Initial chart render after component loads
            document.addEventListener('livewire:initialized', function() {
                setTimeout(function() {
                    createOrUpdateChart();
                }, 500);
            });

            // Re-render chart when window resizes
            window.addEventListener('resize', function() {
                if (myChart) {
                    myChart.resize();
                }
            });
        });
    </script>
</div>
