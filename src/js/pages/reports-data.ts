

import Chart from '../../../node_modules/chart.js/auto';

/**
 * Represents the functionality of the reports page.
 */
class Reports {
    private static instance: Reports;

    /**
     * Creates an instance of the Reports class.
     */
    private constructor() {
        this.initBannerSlider();
        this.initChart();
    }

    /**
     * Get a singleton instance of the Reports class.
     *
     * @return {Reports} The singleton instance of Reports.
     */
    public static getInstance(): Reports {
        if (!this.instance) {
            this.instance = new this();
        }
        return this.instance;
    }

    /**
     * Initialize the banner slider on the reports page.
     */
    private initBannerSlider(): void {
        // Implementation of banner slider functionality goes here
    }

    /**
     * Initialize Chart.js bar chart.
     */
    private initChart(): void {
        const canvas = document.getElementById('myChart') as HTMLCanvasElement;
        if (!canvas) {
            console.error('Canvas element with ID "myChart" not found.');
            return;
        }
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('Failed to get 2D context for Chart.js');
            return;
        }
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Initialize the Reports functionality
document.addEventListener('DOMContentLoaded', () => {
    console.log("Reports script loaded successfully!"); // Перевірка підключення
    Reports.getInstance();
});

export {};