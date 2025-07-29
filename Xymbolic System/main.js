function showToast(msg, isError = false) {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.style.background = isError ? '#d00' : '#228b22';
    toast.classList.add('show');
    setTimeout(() => { toast.classList.remove('show'); }, 2200);
}

function showLoading() { document.getElementById('loading').style.display = 'flex'; }
function hideLoading() { document.getElementById('loading').style.display = 'none'; }

const searchInput = document.getElementById('search-input');
const searchForm = document.getElementById('search-form');
let debounceTimeout;
searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        const params = new URLSearchParams(new FormData(searchForm));
        showLoading();
        fetch('index.php?ajax=1&' + params.toString())
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newSidebar = doc.querySelector('.sidebar');
                if (newSidebar) {
                    document.querySelector('.sidebar').innerHTML = newSidebar.innerHTML;
                }
            })
            .finally(hideLoading);
    }, 300);
});

// Privilege dropdown logic
const privBtn = document.getElementById('priv-eye-btn');
const privDropdown = document.getElementById('priv-dropdown');
let currentPriv = localStorage.getItem('privilege') || 'view';
function updatePrivIcon() {
    if (currentPriv === 'admin') {
        privBtn.innerHTML = '<i class="fa fa-user-tie"></i>';
    } else {
        privBtn.innerHTML = '<i class="fa fa-eye"></i>';
    }
}
updatePrivIcon();
privBtn.onclick = function(e) {
    e.stopPropagation();
    privDropdown.style.display = privDropdown.style.display === 'block' ? 'none' : 'block';
};

// Show admin login modal when admin option is clicked
document.querySelectorAll('.priv-option[data-priv="admin"]').forEach(function(opt) {
    opt.onclick = function(e) {
        e.stopPropagation();
        document.getElementById('admin-login-modal').style.display = 'flex';
        privDropdown.style.display = 'none';
    };
});

// Cancel admin login modal
document.getElementById('cancel-admin-login').onclick = function() {
    document.getElementById('admin-login-modal').style.display = 'none';
    document.getElementById('admin-login-error').style.display = 'none';
};

// Handle admin login form submit
document.getElementById('admin-login-form').onsubmit = function(e) {
    e.preventDefault();
    const username = document.getElementById('admin-username').value.trim();
    const password = document.getElementById('admin-password').value.trim();
    fetch('process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'admin_login', username, password })
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            localStorage.setItem('privilege', 'admin');
            currentPriv = 'admin';
            onPrivChange();
            document.getElementById('admin-login-modal').style.display = 'none';
            document.getElementById('admin-login-error').style.display = 'none';
            showToast('Admin login successful');
        } else {
            document.getElementById('admin-login-error').textContent = resp.error || 'Login failed';
            document.getElementById('admin-login-error').style.display = 'block';
        }
    })
    .catch(() => {
        document.getElementById('admin-login-error').textContent = 'Server error';
        document.getElementById('admin-login-error').style.display = 'block';
    });
};
document.addEventListener('click', function(e) {
    if (!privDropdown.contains(e.target) && e.target !== privBtn) {
        privDropdown.style.display = 'none';
    }
});

function updateAdminUI() {
    const isAdmin = currentPriv === 'admin';
    document.querySelectorAll('.delete-company-btn').forEach(btn => btn.style.display = isAdmin ? 'block' : 'none');
    document.querySelectorAll('.delete-product-btn').forEach(btn => btn.style.display = isAdmin ? 'inline-block' : 'none');
    document.querySelectorAll('.add-product-btn').forEach(btn => btn.style.display = isAdmin ? 'inline-block' : 'none');
    document.querySelectorAll('.visit-site-btn').forEach(btn => btn.style.display = 'inline-block');
    const addBtn = document.getElementById('add-company-btn');
    if (addBtn) addBtn.style.display = isAdmin ? 'block' : 'none';
    const addProductForm = document.getElementById('add-product-form');
    if (addProductForm) addProductForm.style.display = isAdmin ? 'flex' : 'none';
}
updateAdminUI();

const addBtn = document.getElementById('add-company-btn');
const modal = document.getElementById('add-company-modal');
const cancelBtn = document.getElementById('cancel-add-company');
addBtn && addBtn.addEventListener('click', function(e) {
    e.preventDefault();
    modal.style.display = 'flex';
});
cancelBtn && cancelBtn.addEventListener('click', function() {
    modal.style.display = 'none';
});
window.addEventListener('click', function(e) {
    if (e.target === modal) modal.style.display = 'none';
});

function isAdmin() { return currentPriv === 'admin'; }

function makeEditable() {
    document.querySelectorAll('.editable-field').forEach(function(el) {});
    document.querySelectorAll('.editable-field').forEach(function(el) {
        if (isAdmin()) {
            el.setAttribute('tabindex', '0');
            el.classList.add('admin-editable');
            el.addEventListener('mouseenter', function() { el.classList.add('edit-hover'); });
            el.addEventListener('mouseleave', function() { el.classList.remove('edit-hover'); });
            el.onclick = function(e) {
                if (!el.isContentEditable) {
                    el.contentEditable = true;
                    el.focus();
                }
            };
            el.onblur = function() { saveEdit(el); };
            el.onkeydown = function(e) {
                if (e.key === 'Enter') { e.preventDefault(); el.blur(); }
            };
        } else {
            el.contentEditable = false;
            el.removeAttribute('tabindex');
            el.classList.remove('admin-editable');
            el.classList.remove('edit-hover');
            el.onclick = null;
            el.onblur = null;
            el.onkeydown = null;
        }
    });
}
function saveEdit(el) {
    el.contentEditable = false;
    el.classList.remove('edit-hover');
    const field = el.getAttribute('data-field');
    const value = el.innerText.trim();
    if (value === '') {
        showToast('Field cannot be empty', true);
        el.innerText = el.getAttribute('data-original') || '';
        return;
    }
    let data = { field, value };
    if (field === 'product') {
        data.product_id = el.getAttribute('data-product-id');
        data.company_id = COMPANY_ID;
        data.action = 'update_product';
    } else {
        data.company_id = COMPANY_ID;
        data.action = 'update_company';
    }
    fetch('process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(res => res.json()).then(resp => {
        if (!resp.success) showToast(resp.error || 'Update failed', true);
        else showToast('Update successful');
    });
}
function updateEditableOnPrivChange() { makeEditable(); }
makeEditable();

function onPrivChange() { updatePrivIcon(); updateAdminUI(); updateEditableOnPrivChange(); }
document.querySelectorAll('.priv-option').forEach(function(opt) {
    opt.onclick = function(e) {
        const priv = this.getAttribute('data-priv');
        if (priv === 'admin') {
            // Show admin login modal instead of switching privilege
            document.getElementById('admin-login-modal').style.display = 'flex';
            privDropdown.style.display = 'none';
        } else {
            currentPriv = priv;
            localStorage.setItem('privilege', currentPriv);
            onPrivChange();
            privDropdown.style.display = 'none';
        }
    };
});

// Star rating logic for add company modal
const starEls = document.querySelectorAll('#star-rating .star');
const reviewInput = document.getElementById('review-rating');
let currentRating = 0;
starEls.forEach(star => {
  star.addEventListener('mouseenter', function() {
    const val = parseInt(this.getAttribute('data-value'));
    highlightStars(val);
  });
  star.addEventListener('mouseleave', function() {
    highlightStars(currentRating);
  });
  star.addEventListener('click', function() {
    currentRating = parseInt(this.getAttribute('data-value'));
    reviewInput.value = currentRating;
    highlightStars(currentRating);
  });
});
function highlightStars(rating) {
  starEls.forEach(star => {
    const val = parseInt(star.getAttribute('data-value'));
    star.style.color = (val <= rating) ? '#f5b301' : '#ccc';
  });
}
highlightStars(currentRating);

function updateCompanyStarEvents() {
    const starEls = document.querySelectorAll('#company-star-rating .company-star');
    if (!starEls.length) return;
    let currentRating = 0;
    starEls.forEach(star => {
        if (star.style.color === 'rgb(245, 179, 1)' || star.style.color === '#f5b301') {
            currentRating++;
        }
    });
    starEls.forEach(star => {
        star.onmouseenter = function () {
            if (!isAdmin()) return;
            highlightCompanyStars(parseInt(this.getAttribute('data-value')));
        };
        star.onmouseleave = function () {
            if (!isAdmin()) return;
            highlightCompanyStars(currentRating);
        };
        star.onclick = function () {
            if (!isAdmin()) return;
            const newRating = parseInt(this.getAttribute('data-value'));
            if (newRating === currentRating) return;
            if (confirm('Are you sure you want to update the rating to ' + newRating + ' star(s)?')) {
                fetch('process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'update_company_rating',
                        company_id: COMPANY_ID,
                        review: newRating
                    })
                })
                .then(res => res.json())
                .then(resp => {
                    if (resp.success) {
                        currentRating = newRating;
                        highlightCompanyStars(currentRating);
                    } else {
                        alert(resp.error || 'Failed to update rating');
                    }
                });
            }
        };
    });
    function highlightCompanyStars(rating) {
        starEls.forEach(star => {
            const val = parseInt(star.getAttribute('data-value'));
            star.style.color = (val <= rating) ? '#f5b301' : '#ccc';
        });
    }
    highlightCompanyStars(currentRating);
}
updateCompanyStarEvents();

function loadCompanyPanel(companyId) {
    const params = new URLSearchParams(new FormData(searchForm));
    params.set('company_id', companyId);
    params.set('ajax', '1');
    fetch('index.php?' + params.toString())
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newMainPanel = doc.querySelector('.main-panel');
            if (newMainPanel) {
                document.querySelector('.main-panel').innerHTML = newMainPanel.innerHTML;
                // Update COMPANY_ID from the new main panel
                const newCompanyId = parseInt(document.querySelector('.main-panel .editable-field[data-field="name"]')?.getAttribute('data-company-id') || companyId);
                if (!isNaN(newCompanyId)) {
                    window.COMPANY_ID = newCompanyId;
                }
                makeEditable();
                updateAdminUI();
                updateCompanyStarEvents();
            }
            document.querySelectorAll('.company-card').forEach(card => {
                card.classList.remove('selected');
                if (parseInt(card.getAttribute('data-id')) === companyId) {
                    card.classList.add('selected');
                }
            });
        });
}

function attachSidebarClicks() {
    document.querySelectorAll('.company-card').forEach(card => {
        card.setAttribute('data-id', card.querySelector('input[name="id"]')?.value || '');
        card.onclick = function(e) {
            e.preventDefault();
            const id = parseInt(card.getAttribute('data-id'));
            if (id) loadCompanyPanel(id);
        };
        const deleteBtn = card.querySelector('.delete-company-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
}
attachSidebarClicks();

const sidebar = document.querySelector('.sidebar');
const observer = new MutationObserver(() => attachSidebarClicks());
observer.observe(sidebar, { childList: true, subtree: true });

function refreshSidebarAndPanel(companyId) {
    const params = new URLSearchParams(new FormData(searchForm));
    params.set('company_id', companyId);
    params.set('ajax', '1');
    fetch('index.php?' + params.toString())
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newSidebar = doc.querySelector('.sidebar');
            const newMainPanel = doc.querySelector('.main-panel');
            if (newSidebar) document.querySelector('.sidebar').innerHTML = newSidebar.innerHTML;
            if (newMainPanel) {
                document.querySelector('.main-panel').innerHTML = newMainPanel.innerHTML;
                // Update COMPANY_ID from the new main panel
                const newCompanyId = parseInt(document.querySelector('.main-panel .editable-field[data-field="name"]')?.getAttribute('data-company-id') || companyId);
                if (!isNaN(newCompanyId)) {
                    window.COMPANY_ID = newCompanyId;
                }
            }
            attachSidebarClicks();
            makeEditable();
            updateAdminUI();
            updateCompanyStarEvents();
        });
}