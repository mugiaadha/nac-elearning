$(function () {
  "use strict";
  // chart 1
  var ctx = document.getElementById("chart1").getContext('2d');
  var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
  gradientStroke1.addColorStop(0, '#6078ea');
  gradientStroke1.addColorStop(1, '#17c5ea');

  var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
  gradientStroke2.addColorStop(0, '#ff8359');
  gradientStroke2.addColorStop(1, '#ffdf40');

  var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [{
        label: 'Laptops',
        data: [65, 59, 80, 81, 65, 59, 80, 81, 59, 80, 81, 65],
        borderColor: gradientStroke1,
        backgroundColor: gradientStroke1,
        hoverBackgroundColor: gradientStroke1,
        pointRadius: 0,
        fill: false,
        borderRadius: 20,
        borderWidth: 0
      }, {
        label: 'Mobiles',
        data: [28, 48, 40, 19, 28, 48, 40, 19, 40, 19, 28, 48],
        borderColor: gradientStroke2,
        backgroundColor: gradientStroke2,
        hoverBackgroundColor: gradientStroke2,
        pointRadius: 0,
        fill: false,
        borderRadius: 20,
        borderWidth: 0
      }]
    },

    options: {
      maintainAspectRatio: false,
      barPercentage: 0.5,
      categoryPercentage: 0.8,
      plugins: {
        legend: {
          display: false,
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
});
