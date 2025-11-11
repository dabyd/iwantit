@php
	$currentCount = \App\Helpers\TabCounter::incrementAndGet();
@endphp

<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<style>
	.container {
		max-width: 1200px;
		margin: 0 auto;
		padding: 0 20px;
	}

	.container-fluid {
		width: 100%;
		padding: 0 20px;
	}

	/* Header Styles */
	.dashboard-header {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		padding: 20px 0;
		margin-bottom: 30px;
	}

	.header-content {
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex-wrap: wrap;
	}

	.header-title h1 {
		font-size: 1.5rem;
		margin-bottom: 5px;
	}

	.header-title small {
		opacity: 0.75;
		font-size: 0.9rem;
	}

	.header-badge {
		background: rgba(255, 255, 255, 0.2);
		padding: 8px 16px;
		border-radius: 20px;
		font-size: 0.85rem;
	}

	/* Grid System */
	.row {
		display: flex;
		flex-wrap: wrap;
		margin: -10px;
	}

	.col {
		padding: 10px;
		flex: 1;
	}

	.col-12 { flex: 0 0 100%; }
	.col-8 { flex: 0 0 66.666%; }
	.col-4 { flex: 0 0 33.333%; }
	.col-6 { flex: 0 0 50%; }

	/* Main Revenue Card */
	.main-revenue-card {
		background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
		color: white;
		border-radius: 15px;
		padding: 30px;
		text-align: center;
		box-shadow: 0 10px 30px rgba(0,0,0,0.1);
		margin-bottom: 30px;
	}

	.main-revenue-title {
		font-size: 1.2rem;
		margin-bottom: 20px;
		font-weight: 500;
	}

	.main-revenue-amount {
		font-size: 3.5rem;
		font-weight: bold;
		margin: 20px 0;
		text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
	}

	.revenue-metrics {
		display: flex;
		justify-content: space-around;
		margin-top: 30px;
	}

	.revenue-metric {
		text-align: center;
		flex: 1;
	}

	.percentage-badge {
		background: rgba(255, 255, 255, 0.2);
		padding: 8px 16px;
		border-radius: 25px;
		font-weight: bold;
		font-size: 1.1rem;
		display: inline-block;
		margin-bottom: 8px;
	}

	.metric-value {
		color: white;
		font-size: 1.2rem;
		font-weight: bold;
		margin-bottom: 8px;
	}

	.metric-label {
		font-size: 0.85rem;
		opacity: 0.9;
	}

	/* KPI Cards */
	.kpi-card {
		background: white;
		border-radius: 15px;
		padding: 25px;
		box-shadow: 0 5px 15px rgba(0,0,0,0.08);
		transition: transform 0.3s ease, box-shadow 0.3s ease;
		margin-bottom: 20px;
		height: calc(100% - 20px);
	}

	.kpi-card:hover {
		transform: translateY(-5px);
		box-shadow: 0 15px 35px rgba(0,0,0,0.1);
	}

	.kpi-card.text-center {
		text-align: center;
	}

	.kpi-number {
		font-size: 3rem;
		font-weight: bold;
		color: #2c3e50;
		margin: 15px 0;
	}

	.kpi-number.text-primary { color: #007bff; }
	.kpi-number.text-success { color: #28a745; }

	.kpi-label {
		font-size: 0.9rem;
		color: #7f8c8d;
		font-weight: 500;
		text-transform: uppercase;
		letter-spacing: 1px;
	}

	.small {
		font-size: 0.85rem;
	}

	.text-muted {
		color: #6c757d;
	}

	/* Chart Container */
	.chart-container {
		background: white;
		border-radius: 15px;
		padding: 25px;
		box-shadow: 0 5px 15px rgba(0,0,0,0.08);
		margin-bottom: 20px;
	}

	.chart-title {
		font-size: 1.1rem;
		font-weight: 600;
		color: #2c3e50;
		margin-bottom: 20px;
		text-align: center;
	}

	#clicksChart {
		max-height: 300px;
	}

	/* Table Styles */
	.metrics-table {
		background: white;
		border-radius: 15px;
		overflow: hidden;
		box-shadow: 0 5px 15px rgba(0,0,0,0.08);
		margin-bottom: 20px;
	}

	.table-dashboard {
		width: 100%;
		border-collapse: collapse;
		margin: 0;
	}

	.table-dashboard th,
	.table-dashboard td {
		padding: 15px;
		text-align: left;
		border-bottom: 1px solid #dee2e6;
	}

	.table-header {
		background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
		color: white;
	}

	.table-header th {
		font-weight: 600;
		border-bottom: none;
	}

	.table-dashboard tbody tr:hover {
		background-color: #f8f9fa;
	}

	.table-dashboard tbody tr:last-child td {
		border-bottom: none;
	}

	/* Progress Bar */
	.progress {
		background-color: #e9ecef;
		border-radius: 10px;
		height: 20px;
		overflow: hidden;
		position: relative;
	}

	.progress-bar {
		height: 100%;
		background-color: #28a745;
		border-radius: 10px;
		transition: width 0.6s ease;
		display: flex;
		align-items: center;
		justify-content: center;
		color: white;
		font-size: 0.8rem;
		font-weight: 500;
	}

	.progress-bar.bg-warning {
		background-color: #ffc107;
		color: #212529;
	}

	/* Status Indicators */
	.status-indicator {
		width: 12px;
		height: 12px;
		border-radius: 50%;
		display: inline-block;
		margin-right: 8px;
	}

	.status-active { background-color: #27ae60; }
	.status-warning { background-color: #f39c12; }
	.status-inactive { background-color: #e74c3c; }

	/* Responsive Design */
	@media (max-width: 768px) {
		.col-8, .col-4 {
			flex: 0 0 100%;
		}

		.header-content {
			flex-direction: column;
			text-align: center;
			gap: 15px;
		}

		.main-revenue-amount {
			font-size: 2.5rem;
		}

		.revenue-metrics {
			flex-direction: column;
			gap: 15px;
		}

		.table-dashboard {
			font-size: 0.85rem;
		}

		.table-dashboard th,
		.table-dashboard td {
			padding: 10px;
		}
	}

	@media (max-width: 480px) {
		.container-fluid {
			padding: 0 10px;
		}

		.main-revenue-card {
			padding: 20px;
		}

		.kpi-card {
			padding: 15px;
		}

		.chart-container {
			padding: 15px;
		}

		.kpi-number {
			font-size: 2rem;
		}
	}
</style>

<div class="tab-{{ $currentCount }}">
	<h2>Dashboard</h2>

    <div class="container-fluid">
        <!-- Main Revenue Card -->
        <div class="row">
            <div class="col-12">
                <div class="main-revenue-card">
                    <h4 class="main-revenue-title">Total Product Placement Revenue</h4>
                    <div class="main-revenue-amount">824.300 €</div>
                    <div class="revenue-metrics">
                        <div class="revenue-metric">
                            <div class="percentage-badge">68%</div>
                            <div class="metric-label">% Products enabled</div>
                        </div>
                        <div class="revenue-metric">
                            <div class="metric-value">22</div>
                            <div class="metric-label"># Brands</div>
                        </div>
                        <div class="revenue-metric">
                            <div class="metric-value">77</div>
                            <div class="metric-label"># Products</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Chart Section -->
            <div class="col-8">
                <div class="chart-container">
                    <div class="chart-title">Clicks x Day</div>
                    <canvas id="clicksChart"></canvas>
                </div>
            </div>

            <!-- KPIs Section -->
            <div class="col-4">
                <div class="row">
                    <div class="col-12">
                        <div class="kpi-card text-center">
                            <div class="kpi-label">Daily Average</div>
                            <div class="kpi-number text-primary">2.4K</div>
                            <div class="small text-muted">↑ 12% vs last week</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="kpi-card text-center">
                            <div class="kpi-label">Conversion Rate</div>
                            <div class="kpi-number text-success">3.2%</div>
                            <div class="small text-muted">↑ 0.5% vs last month</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metrics Table -->
        <div class="row">
            <div class="col-12">
                <div class="metrics-table">
                    <table class="table-dashboard">
                        <thead class="table-header">
                            <tr>
                                <th>Ranking</th>
                                <th>Brand</th>
                                <th>Revenue</th>
                                <th>% Products enabled</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>3</strong></td>
                                <td>Mira Milano</td>
                                <td><strong>43.300 €</strong></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 85%">85%</div>
                                    </div>
                                </td>
                                <td><span class="status-indicator status-active"></span>Activo</td>
                            </tr>
                            <tr>
                                <td><strong>4</strong></td>
                                <td>Locations</td>
                                <td><strong>20.650 €</strong></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: 62%">62%</div>
                                    </div>
                                </td>
                                <td><span class="status-indicator status-warning"></span>En revisión</td>
                            </tr>
                            <tr>
                                <td><strong>5</strong></td>
                                <td>Dolce & Gabbana</td>
                                <td><strong>36.075 €</strong></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 92%">92%</div>
                                    </div>
                                </td>
                                <td><span class="status-indicator status-active"></span>Activo</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
	// Chart Configuration
	const ctx = document.getElementById('clicksChart').getContext('2d');
	
	const chartData = {
		labels: ['4/6/23', '4/7/23', '4/8/23', '4/9/23', '4/10/23', '4/11/23', '4/12/23', '4/13/23', '4/14/23', '4/15/23', '4/16/23', '4/17/23', '4/18/23'],
		datasets: [
			{
				label: 'Clicks',
				data: [2100, 2300, 2500, 2200, 2400, 2600, 2800, 2300, 2500, 2400, 2700, 2900, 2600],
				borderColor: '#3498db',
				backgroundColor: 'rgba(52, 152, 219, 0.1)',
				tension: 0.4,
				fill: true,
				pointBackgroundColor: '#3498db',
				pointBorderColor: '#2980b9',
				pointRadius: 4,
				pointHoverRadius: 8
			},
			{
				label: 'Conversions',
				data: [1800, 2000, 2100, 1900, 2050, 2200, 2400, 2000, 2150, 2080, 2300, 2500, 2200],
				borderColor: '#e74c3c',
				backgroundColor: 'rgba(231, 76, 60, 0.1)',
				tension: 0.4,
				fill: true,
				borderDash: [5, 5],
				pointBackgroundColor: '#e74c3c',
				pointBorderColor: '#c0392b',
				pointRadius: 4,
				pointHoverRadius: 8
			}
		]
	};

	const clicksChart = new Chart(ctx, {
		type: 'line',
		data: chartData,
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: {
					position: 'top',
					labels: {
						usePointStyle: true,
						padding: 20,
						font: {
							family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
							size: 12
						}
					}
				},
				tooltip: {
					backgroundColor: 'rgba(0,0,0,0.8)',
					titleColor: 'white',
					bodyColor: 'white',
					borderColor: 'rgba(255,255,255,0.2)',
					borderWidth: 1
				}
			},
			scales: {
				y: {
					beginAtZero: false,
					min: 1500,
					max: 3000,
					grid: {
						color: 'rgba(0,0,0,0.1)',
						drawBorder: false
					},
					ticks: {
						font: {
							family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
							size: 11
						},
						color: '#6c757d'
					}
				},
				x: {
					grid: {
						display: false
					},
					ticks: {
						font: {
							family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
							size: 11
						},
						color: '#6c757d'
					}
				}
			},
			interaction: {
				intersect: false,
				mode: 'index'
			}
		}
	});

	// Animations
	document.addEventListener('DOMContentLoaded', function() {
		// Animate numbers on load
		const numbers = document.querySelectorAll('.kpi-number');
		numbers.forEach(number => {
			const finalValue = number.textContent;
			number.textContent = '0';
			animateNumber(number, finalValue);
		});

		// Animate main revenue
		const mainRevenue = document.querySelector('.main-revenue-amount');
		animateRevenue(mainRevenue, '824.300 €');

		// Animate progress bars
		setTimeout(() => {
			const progressBars = document.querySelectorAll('.progress-bar');
			progressBars.forEach(bar => {
				const width = bar.style.width;
				bar.style.width = '0%';
				setTimeout(() => {
					bar.style.width = width;
				}, 100);
			});
		}, 500);
	});

	function animateNumber(element, finalValue) {
		const numericValue = parseInt(finalValue.replace(/[^\d]/g, ''));
		const duration = 1500;
		const increment = numericValue / (duration / 16);
		let current = 0;

		const timer = setInterval(() => {
			current += increment;
			if (current >= numericValue) {
				current = numericValue;
				clearInterval(timer);
			}
			
			if (finalValue.includes('%')) {
				element.textContent = Math.round(current) + '%';
			} else if (finalValue.includes('K')) {
				element.textContent = (current / 1000).toFixed(1) + 'K';
			} else {
				element.textContent = Math.round(current);
			}
		}, 16);
	}

	function animateRevenue(element, finalValue) {
		const numericValue = 824300;
		const duration = 2000;
		const increment = numericValue / (duration / 16);
		let current = 0;

		const timer = setInterval(() => {
			current += increment;
			if (current >= numericValue) {
				current = numericValue;
				clearInterval(timer);
			}
			
			element.textContent = Math.round(current).toLocaleString('es-ES') + ' €';
		}, 16);
	}
</script>
