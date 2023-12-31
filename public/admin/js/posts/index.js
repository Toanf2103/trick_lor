// Filter elements
const rootURL = document.querySelector('meta[name="root-url"]').dataset.index
const searchInput = document.querySelector('input[name="search"]')
const languageSelect = document.querySelector('select[name="language"]')
const statusSelect = document.querySelector('select[name="status"]')
const refreshBtn = document.querySelector('.refresh-btn')
const dataBox = document.querySelector('#data')

// Filter variables
let searchValue = ''
let languageValue = ''
let statusValue = ''
let page = 1
let typingTimer

// Call api to get list posts
function getData(link = '') {
  showLoadingOverlay()

  const xml = new XMLHttpRequest()

  xml.onreadystatechange = function () {
    if (xml.readyState === 4 && xml.status === 200) {
      dataBox.innerHTML = xml.responseText
      setUpPaginationLink()
      hideLoadingOverlay()
    }
  }

  const url =
    link ||
    `${rootURL}/admin/posts/filter?status=${statusValue}&key=${searchValue}&category=${languageValue}&page=${page}`

  xml.open('GET', url, true)
  xml.send()
}

// Get data when website loaded
getData()

// Event handler when search key input is changed
searchInput.addEventListener('input', function () {
  searchValue = searchInput.value
  clearTimeout(typingTimer)

  typingTimer = setTimeout(function () {
    getData()
  }, 500)
})

// Event handler when language select is changed
languageSelect.addEventListener('change', function () {
  languageValue = languageSelect.value
  getData()
})

// Event handler when status select is changed
statusSelect.addEventListener('change', function () {
  statusValue = statusSelect.value
  getData()
})

// Event handler for the click event on the refresh button
refreshBtn.addEventListener('click', () => {
  // Set all filters to their default values.
  searchInput.value = ''
  languageSelect.value = ''
  statusSelect.value = ''

  // Call the getData() function if there is a parameter with a non-default value
  if (
    searchValue != searchInput.value ||
    languageValue != languageSelect.value ||
    statusValue != statusSelect.value
  ) {
    searchValue = searchInput.value
    languageValue = languageSelect.value
    statusValue = statusSelect.value

    getData()
  }
})

// Set up event for pagination page items when got data table
const setUpPaginationLink = () => {
  const pageItemsValid = document.querySelectorAll(
    'nav .pagination li.page-item:not(.disabled):not(.active) a',
  )

  pageItemsValid.forEach(btn => {
    const href = btn.getAttribute('href')
    btn.removeAttribute('href')
    btn.dataset.href = href

    btn.addEventListener('click', e => {
      e.preventDefault()
      getData(btn.dataset.href)
    })
  })
}
