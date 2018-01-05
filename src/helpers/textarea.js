(function () {
  let autoresize = function (ev) {
    if (ev.target.tagName.toLowerCase() !== 'textarea') {
      return
    }

    let el = ev.target
    setTimeout(function () {
      el.dispatchEvent(new Event('keydown'))
      el.style.cssText = 'height:auto;'
      // for box-sizing other than "content-box" use:
      // el.style.cssText = '-moz-box-sizing:content-box';
      el.style.cssText = 'height:' + (el.scrollHeight + 3) + 'px'
    }, 0)
  }

  document.body.onkeydown = autoresize
})()
