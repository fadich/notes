let textarea = {}

textarea.autoresize = function () {
  let textarea = document.querySelectorAll('textarea')

  textarea.forEach(function (el) {
    el.addEventListener('keydown', autosize)
    el.dispatchEvent(new Event('keydown'))
  })

  function autosize () {
    let el = this
    setTimeout(function () {
      el.style.cssText = 'height:auto;'
      // for box-sizing other than "content-box" use:
      // el.style.cssText = '-moz-box-sizing:content-box';
      el.style.cssText = 'height:' + (el.scrollHeight + 3) + 'px'
    }, 0)
  }
}

export default textarea
