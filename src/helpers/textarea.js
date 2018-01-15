(function () {
  let resize = function (ev) {
    if (ev.target.tagName.toLowerCase() !== 'textarea') {
      return
    }

    let el = ev.target

    setTimeout(function () {
      el.style.cssText = 'height:auto;'
      el.style.cssText = 'height:' + (el.scrollHeight + 3) + 'px'
    }, 0)
  }

  document.body.onkeydown = resize
  document.body.onclick = resize
})()
