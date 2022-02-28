const $ = require('jquery')
const Chart = require('chart.js/auto').default

const drawChart = () => {
  const ctx = document.getElementById('analytic-chart')
  const options = {
    type: 'bar',
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    },
    data: {
      labels: JSON.parse(ctx.getAttribute('data-labels')),
      datasets: [{
        label: ctx.getAttribute('data-label'),
        data: JSON.parse(ctx.getAttribute('data-values')),
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgb(54, 162, 235)',
        borderWidth: 1
      }]
    }
  }

  const chart = new Chart(ctx, options)

  const resize = () => {
    const width = ctx.parentNode.parentNode.offsetWidth
    const height = 250

    chart.resize(width, height)
  }

  resize()

  window.addEventListener('resize', resize)
}

module.exports = () => {
  const body = $('body')

  body.on('shown.bs.modal', '.modal', (e) => {
    window.setTimeout(() => {
      if (document.getElementById('analytic-chart')) {
        drawChart()
      }
    }, 500)
  })
}
