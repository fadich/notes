export default {
  resize: (el) => {
    setTimeout(function () {
      el.style.cssText = 'height:auto;'
      el.style.cssText = 'height:' + (el.scrollHeight + 3) + 'px'
    }, 0)
  }
}
