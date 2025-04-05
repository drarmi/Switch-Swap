
<?php

get_header('profile');

?>

<h1 class="profile-page-title">דוחות ונתונים</h1>

<ul class="dashboard-primary-nav">
    <li><a href="http://swap.loc/dashboard/reports-data">הצג הכל</a></li>
    <li><a href="http://swap.loc/dashboard/reports-data/movements/">תנועות</a></li>
    <li><a href="http://swap.loc/dashboard/reports-data/credit/" class="active">קרדיט</a></li>
    <li><a href="http://swap.loc/dashboard/reports-data/rating/">דירוג</a></li>
    <li><a href="http://swap.loc/dashboard/reports-data/leads/">המובילים</a></li>
</ul>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>

<div class="dashboard-charts">

	<div class="filter-buttons">
		<button class="filter-button swap-tab-button active" data-filter="month">חודשי</button>
		<button class="filter-button swap-tab-button" data-filter="quarter">רבעוני</button>
		<button class="filter-button swap-tab-button" data-filter="year">שנתי</button>
	</div>


	<div class="chart-wrap">
		<div class="total-chart-info">
			<span id="total-earnings" class="total-count">0</span>
			<span class="total-text">סה״כ</span>
		</div>
		<canvas id="earningsChart" height="200">No Data to display</canvas>
	</div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
	const ctx = document.getElementById('earningsChart').getContext('2d');
	let chart;

	const buttons = document.querySelectorAll('.filter-button');
	buttons.forEach(button => {
		button.addEventListener('click', function () {
			buttons.forEach(btn => btn.classList.remove('active'));
			this.classList.add('active');
			const filterType = this.dataset.filter;
			fetchSalesData(filterType);
		});
	});

	function fetchSalesData(filterType = 'month') {
		const formData = new FormData();
		formData.append('action', 'getSales');
		formData.append('filter_type', filterType);

		fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				const ordersData = data.data.orders_data;
				const labels = Object.keys(ordersData).sort(); // sorted dates
				const earnings = labels.map(date => ordersData[date].earnings);

				updateLineChart(labels, earnings);
				document.getElementById('total-earnings').textContent = data.data.earnings_total.toLocaleString();
			} else {
				console.error('Error loading sales:', data.data);
			}
		})
		.catch(error => {
			console.error('Fetch error:', error);
		});
	}

	function updateLineChart(labels, dataPoints) {
		if (chart) {
			chart.destroy();
		}

        
    const ctx = document.getElementById('earningsChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 200);

    gradient.addColorStop(0, 'rgba(199, 167, 127, 0.3)');
    gradient.addColorStop(1, 'rgba(199, 167, 127, 0)');

		chart = new Chart(ctx, {
			type: 'line',
			data: {
				labels: labels,
				datasets: [{
					label: 'Earnings',
					data: dataPoints,


					tension: 0.3,
					borderWidth: 2,
					pointRadius: 4,
					fill: true,
					borderColor: '#C7A77F',
					backgroundColor: gradient
				}]
			},
			options: {
				responsive: true,
				scales: {
					x: {
						type: 'time',
						time: {
							unit: 'day',
							displayFormats: {
								day: 'dd.MM'
							}
						}
					},
					y: {
						beginAtZero: true,
						ticks: {
							callback: value => '₪ ' + value.toLocaleString()
						}
					}
				},
				plugins: {
					tooltip: {
						callbacks: {
							label: context => '₪ ' + context.raw.toLocaleString()
						}
					},
					legend: { display: false }
				}
			}
		});
	}

	// Initial load
	fetchSalesData('month');
});
</script>