<?php 
	get_header('profile');
?>

<h1 class="profile-page-title">דוחות ונתונים</h1>
<ul class="dashboard-primary-nav">
    <li><a href="http://swap.loc/dashboard/reports-data">הצג הכל</a></li>
    <li><a href="http://swap.loc/dashboard/reports-data/movements/" class="active">תנועות</a></li>
    <li><a href="http://swap.loc/dashboard/reports-data/credit/">קרדיט</a></li>
    <li><a href="http://swap.loc/dashboard/reports-data/rating/">דירוג</a></li>
    <li><a href="http://swap.loc/dashboard/reports-data/leads/">המובילים</a></li>
</ul>

<div class="dashboard-charts">

	<div class="filter-buttons">
		<button class="filter-button swap-tab-button active" data-filter="month">חודשי</button>
		<button class="filter-button swap-tab-button" data-filter="quarter">רבעוני</button>
		<button class="filter-button swap-tab-button" data-filter="year">שנתי</button>
	</div>

	<p class="diagram-title">השכרות</p>
	<div class="chart-wrap">
		<div class="total-chart-info">
			<span id="total-bookings-count" class="total-count">0</span>
			<span class="total-text">סה״כ</span>
		</div>
		<canvas id="bookingsChart" height="200"></canvas>
	</div>

    <div class="sales-auctions-filters">
        <button id="show-all" class="swap-tab-button-2 active">הצג הכל</button>
        <button id="show-sales" class="swap-tab-button-2">מכירות פומביות</button>
        <button id="show-auctions" class="swap-tab-button-2">מכירות</button>=
    </div>
	<div class="chart-wrap">
		<div class="total-chart-info">
			<span id="total-sales-auctions-count" class="total-count">0</span>
			<span class="total-text">סה״כ</span>
		</div>
		<canvas id="salesAuctionsChart" height="200">No Data to display</canvas>
	</div>

</div>


<script>
	var omnisDashboardData = {
		ajax_url: "<?php echo admin_url('admin-ajax.php'); ?>",
		nonce: "<?php echo wp_create_nonce('omnis_dashboard_nonce'); ?>"
	};
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>


<script>
jQuery(document).ready(function ($) {
	let salesChart, bookingsChart;


    function renderBookingsChart(data) {
		console.log("Bookings Raw Data:", data);

	const bookingsData = [];

	const ordersData = data.orders_data || {};

	Object.keys(ordersData).forEach(date => {
		if (ordersData[date].bookings !== undefined) {
			bookingsData.push({ x: date, y: ordersData[date].bookings });
		}
	});

	if (bookingsChart) bookingsChart.destroy();


    const ctx = document.getElementById('bookingsChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 200);

    gradient.addColorStop(0, 'rgba(199, 167, 127, 0.3)');
    gradient.addColorStop(1, 'rgba(199, 167, 127, 0)');


	bookingsChart = new Chart(document.getElementById('bookingsChart'), {
		type: 'line',
		data: {
			datasets: [{
				data: bookingsData,
				borderColor: '#C7A77F',
				backgroundColor: gradient,
				fill: true,
				tension: 0.4,
				id: 'bookings'
			}]
		},
		options: {
			plugins: { legend: { display: false } },
			scales: {
				x: {
					type: 'time',
					time: {
						unit: 'day',
						displayFormats: { day: 'dd MMM' },
						tooltipFormat: 'dd MMM yyyy'
					},
				},
				y: {
					beginAtZero: true,
					ticks: { stepSize: 1 },
				}
			}
		}
	});

	$('#total-bookings-count').text(data.orders_count_total);
}


	function renderSalesAuctionsChart(data) {
		const salesData = [];
		const auctionsData = [];

		const ordersData = data.orders_data || {};

		Object.keys(ordersData).forEach(date => {
			if (ordersData[date].sales !== undefined) {
				salesData.push({ x: date, y: ordersData[date].sales });
			}
			if (ordersData[date].auctions !== undefined) {
				auctionsData.push({ x: date, y: ordersData[date].auctions });
			}
		});


        

		const allDatasets = [
			{
				data: salesData,
				borderColor: '#C7A77F',
				backgroundColor: 'transparent',
                borderWidth: 8,

				fill: true,
				tension: 0.4,
				id: 'sales'
			},
			{
				data: auctionsData,
				borderColor: '#6AC4DC',
				backgroundColor: 'transparent',
                borderWidth: 8,

				fill: true,
				tension: 0.4,
				id: 'auctions'
			}
		];

		if (salesChart) salesChart.destroy();
		salesChart = new Chart(document.getElementById('salesAuctionsChart'), {
			type: 'line',
			data: {
				datasets: allDatasets
			},
			options: {
				plugins: {
					legend: { display: false },
                    tooltip: {
                        displayColors: false
                    }
				},
				scales: {
					x: {
						type: 'time',
						time: {
							unit: 'day',
							displayFormats: { day: 'dd MMM' },
							tooltipFormat: 'dd MMM yyyy'
						},
					},
					y: {
						beginAtZero: true,
						ticks: { stepSize: 1 },
					}
				}
			}
		});

		// Перемикачі
		$('#show-all').on('click', () => {
			salesChart.data.datasets.forEach(ds => ds.hidden = false);
			salesChart.update();
		});

		$('#show-auctions').on('click', () => {
			salesChart.data.datasets.forEach(ds => {
				ds.hidden = ds.id !== 'auctions';
			});
			salesChart.update();
		});

		$('#show-sales').on('click', () => {
			salesChart.data.datasets.forEach(ds => {
				ds.hidden = ds.id !== 'sales';
			});
			salesChart.update();
		});

		$('#total-sales-auctions-count').text(data.orders_count_total);
	}

	function fetchData(action, callback) {
		const filterType = $('.filter-button.active').data('filter');



		$.ajax({
			url: omnisDashboardData.ajax_url,
			type: 'POST',
			data: {
				action: action,
				filter_type: filterType,
				_nonce: omnisDashboardData.nonce
			},
			success: function (response) {
				if (response.success) {
					callback(response.data);
				} else {
					console.error(action + ' error:', response.data);
				}
			}
		});
	}

function renderAllCharts() {
	let combinedSalesAuctions = {
		orders_count_total: 0,
		orders_data: {}
	};
	let combinedBookings = {
		orders_count_total: 0,
		orders_data: {}
	};

	let responsesReceived = 0;

	function handleResponse(type, target) {
		return function (data) {
			if (target === 'salesAuctions') {
				combinedSalesAuctions.orders_count_total += data.orders_count_total;

				for (const date in data.orders_data) {
					if (!combinedSalesAuctions.orders_data[date]) {
						combinedSalesAuctions.orders_data[date] = {};
					}
					combinedSalesAuctions.orders_data[date][type] = data.orders_data[date].orders_count || 0;
				}
			}

			if (target === 'bookings') {
				combinedBookings.orders_count_total += data.orders_count_total;

				for (const date in data.orders_data) {
					if (!combinedBookings.orders_data[date]) {
						combinedBookings.orders_data[date] = {};
					}
					combinedBookings.orders_data[date].bookings = data.orders_data[date].orders_count || 0;
				}
			}

			responsesReceived++;
			if (responsesReceived === 3) {
				renderSalesAuctionsChart(combinedSalesAuctions);
				renderBookingsChart(combinedBookings);
			}
		};
	}

	fetchData('getSales', handleResponse('sales', 'salesAuctions'));
	fetchData('getAuctions', handleResponse('auctions', 'salesAuctions'));
	fetchData('getBookings', handleResponse('bookings', 'bookings'));
}

	$('.filter-button').on('click', function () {
		$('.filter-button').removeClass('active');
		$(this).addClass('active');
		renderAllCharts();
	});


	$('#filter-type').on('change', renderAllCharts);
	renderAllCharts();
});
</script>