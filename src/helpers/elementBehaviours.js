function ElemBehaviours () {
  let showForm = false

  this.resize = (element) => {
    setTimeout(function () {
      element.style.cssText = 'height:auto;'
      element.style.cssText = 'height:' + (element.scrollHeight + 3) + 'px'
    }, 0)
  }

  this.showForm = (event) => {
    let el = event.target
    let form = this.getParentByTagName(el, 'form')

    showForm = true
    form.classList.add('collapse')
  }

  this.hideForm = (event, delay) => {
    let el = event.target
    let form = this.getParentByTagName(el, 'form')

    showForm = false

    setTimeout(() => {
      if (!showForm) {
        let txts = form.getElementsByTagName('textarea'.toUpperCase())

        for (let i in txts) {
          let txt = txts[i]

          if (txt.style) {
            txt.style.cssText = 'height:auto;'
          }
        }
        form.classList.remove('collapse')
      }
    }, delay || 500)
  }

  /**
   * Get parent node for given tagname
   * @param  {Object} node    DOM node
   * @param  {String} tagname HTML tagName
   * @return {Object}         Parent node
   *
   * @see https://gist.github.com/ludder/4045263
   */
  this.getParentByTagName = (node, tagname) => {
    let parent

    if (node === null || tagname === '') {
      return null
    }

    parent = node.parentNode
    tagname = tagname.toUpperCase()

    while (parent.tagName !== 'HTML') {
      if (parent.tagName === tagname) {
        return parent
      }
      parent = parent.parentNode
    }

    return parent
  }
}

export default new ElemBehaviours()
