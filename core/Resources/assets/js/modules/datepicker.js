const Datepicker = require('vanillajs-datepicker')

const isDateSupported = () => {
  const input = document.createElement('input')
  const value = 'a'

  input.setAttribute('type', 'date')
  input.setAttribute('value', value)

  return input.value !== value
}

const createDatePicker = (input) => {
  return new Datepicker.Datepicker(input, {
    format: 'yyyy-mm-dd'
  })
}

module.exports = () => {
  if (isDateSupported()) {
    return
  }

  const inputs = document.querySelectorAll('input[type="date"]')
  const size = inputs.length

  for (let i = 0, c = size; i < c; i++) {
    createDatePicker(inputs[i])
  }
}
