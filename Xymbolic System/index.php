<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Company Directory</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        var COMPANY_ID = <?= intval($show_id) ?>;
    </script>
    <script src="main.js" defer></script>
    <script>
    // Move updated company card to top after AJAX update
    function moveCompanyCardToTop(companyId) {
        var sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;
        var cards = Array.from(sidebar.querySelectorAll('.company-card'));
        let updatedCard = null;
        for (const card of cards) {
            let id = null;
            var idInput = card.querySelector('input[name="id"]');
            if (idInput) {
                id = parseInt(idInput.value);
            } else {
                var onclick = card.getAttribute('onclick');
                if (onclick) {
                    var match = onclick.match(/company_id=(\d+)/);
                    if (match) id = parseInt(match[1]);
                }
            }
            if (id === companyId) {
                updatedCard = card;
                break;
            }
        }
        if (updatedCard) {
            sidebar.insertBefore(updatedCard, sidebar.firstChild);
        }
    }
    // Listen for custom event from main.js after update
    document.addEventListener('company-updated', function(e) {
        if (e.detail && e.detail.companyId) {
            moveCompanyCardToTop(e.detail.companyId);
        }
    });
    </script>
</head>
<body>
<?php if (isset($_GET['error'])): ?>
<script>
window.onload = function() {
    alert("<?= htmlspecialchars($_GET['error']) ?>");
};
</script>
<?php endif; ?>
<div class="searchbar">
    <i class="fa fa-search"></i>
    <form id="search-form" method="GET" style="flex:1;display:flex;align-items:center;gap:8px;margin:0;">
        <input type="text" id="search-input" name="search" placeholder="Search for Company/Products" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" autocomplete="off">
    </form>
   
    <button id="add-company-btn" style="display:none;background:#228b22;color:#fff;font-weight:700;font-size:1.3em;padding:0 32px;height:44px;border:none;border-radius:10px;margin-left:12px;cursor:pointer;">ADD</button>
    <div class="dropdown-priv" style="position:relative;">
        <button class="icon-btn" id="priv-eye-btn" type="button"><i class="fa fa-eye"></i></button>
        <div id="priv-dropdown" class="priv-dropdown-menu" style="display:none;position:absolute;right:0;top:36px;min-width:140px;background:#fff;border:1px solid #ccc;border-radius:8px;box-shadow:0 2px 8px #0002;z-index:10;">
            <div class="priv-option" data-priv="view" style="padding:10px 18px;cursor:pointer;display:flex;align-items:center;gap:8px;"><i class="fa fa-eye"></i> View only</div>
            <div class="priv-option" data-priv="admin" style="padding:10px 18px;cursor:pointer;display:flex;align-items:center;gap:8px;"><i class="fa fa-user-tie"></i> Admin</div>
        </div>
    </div>
</div>
<div class="container">
    <div class="sidebar">
        <?php
        $search = $_GET['search'] ?? '';
        $sector = $_GET['sector'] ?? '';
        $where = "WHERE 1";
        if ($search) {
            $search = $conn->real_escape_string($search);
            // Search companies by name, email, sector, OR if any of their products or categories match the search
            $where .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR sector LIKE '%$search%' OR id IN (SELECT company_id FROM products WHERE name LIKE '%$search%' OR category LIKE '%$search%'))";
        }
        if ($sector) {
            $sector = $conn->real_escape_string($sector);
            $where .= " AND sector = '$sector'";
        }
        $where .= " AND status != 'archived'";
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $companies = $conn->query("SELECT * FROM companies $where ORDER BY id DESC LIMIT $limit OFFSET $offset");
        $selected_id = $_GET['company_id'] ?? null;
        $first_id = null;
        while($c = $companies->fetch_assoc()):
            if ($first_id === null) $first_id = $c['id'];
        ?>
        <div class="company-card<?= ($selected_id == $c['id'] || (!$selected_id && $first_id == $c['id'])) ? ' selected' : '' ?>" onclick="window.location.search='company_id=<?= $c['id'] ?>&search=<?= urlencode($search) ?>&sector=<?= urlencode($sector) ?>'">
            <div class="location"><i class="fa fa-map-marker-alt"></i> <?= htmlspecialchars($c['location']) ?></div>
            <div class="name"><?= htmlspecialchars($c['name']) ?></div>
            <div class="contact">
                <div><i class="fa fa-envelope"></i> <?= htmlspecialchars($c['email']) ?></div>
                <div><i class="fa fa-phone"></i> <?= htmlspecialchars($c['contact_number']) ?></div>
            </div>
            <!-- Move the delete button to the bottom -->
            <div style="width:100%;display:flex;justify-content:flex-end;align-items:center;margin-top:5px;">
                <form action="process.php" method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    <button type="submit" name="delete" class="delete-company-btn" style="display:none;background:none;border:none;color:#d00;font-size:1.3em;cursor:pointer;" onclick="return confirm('Delete this company?')">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <div class="main-panel">
        <?php
        $show_id = $selected_id ?: $first_id;
        $company = $conn->query("SELECT * FROM companies WHERE id=".intval($show_id))->fetch_assoc();
        if ($company):
        ?>
        <div class="main-header">
            <div class="info">
                <div class="name">
                    <span class="editable-field" data-field="name" data-company-id="<?= $company['id'] ?>" contenteditable="false" data-original="<?= htmlspecialchars($company['name']) ?>"><?= htmlspecialchars($company['name']) ?></span>
                </div>
                <div class="contact">
                    <span class="editable-field" data-field="email" contenteditable="false"><i class="fa fa-envelope"></i> <?= htmlspecialchars($company['email']) ?></span> &nbsp;
                    <span class="editable-field" data-field="contact_number" contenteditable="false"><i class="fa fa-phone"></i> <?= htmlspecialchars($company['contact_number']) ?></span>
                    <br>
                    <span class="editable-field" data-field="location" contenteditable="false" data-original="<?= htmlspecialchars($company['location']) ?>">
                        <i class="fa fa-map-marker-alt"></i> <?= htmlspecialchars($company['location']) ?>
                    </span>
                </div>
                <div style="margin:8px 0 0 0;display:flex;align-items:center;gap:4px;">
                    <?php $review = (int)($company['review'] ?? 0); ?>
                    <span id="company-star-rating" style="display:flex;align-items:center;gap:2px;cursor:pointer;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa fa-star company-star" data-value="<?= $i ?>" style="color:<?= $i <= $review ? '#f5b301' : '#ccc' ?>;font-size:1.4em;transition:color 0.2s;"></i>
                        <?php endfor; ?>
                    </span>
                    <span style="font-size:1em;color:#888;margin-left:8px;">Rating</span>
                </div>
                <div class="desc">
                    <span class="editable-field" data-field="description" contenteditable="false"> <?= nl2br(htmlspecialchars($company['description'])) ?> </span>
                </div>
            </div>
            <div class="icons">
                <a href="mailto:<?= htmlspecialchars($company['email']) ?>" title="Email"><i class="fa fa-envelope"></i></a>
                <a href="tel:<?= htmlspecialchars($company['contact_number']) ?>" title="Call"><i class="fa fa-phone"></i></a>
            </div>
        </div>
        <div style="margin:16px 0;">
            <a href="<?= htmlspecialchars($company['url']) ?>" target="_blank" class="visit-site-btn" style="display:none;padding:8px 18px;background:#c87d1c;color:#fff;border-radius:6px;font-weight:600;text-decoration:none;">VISIT SITE</a>
        </div>
        <div class="products-section">
            <h3>List of Products</h3>
            <div style="margin-bottom:12px;">
                <input type="text" id="product-search-input" placeholder="Search products or category..." style="width:100%;padding:8px 14px;border-radius:8px;border:1px solid #aaa;font-size:1em;">
            </div>
            <form action="process.php" method="POST" id="add-product-form-<?= $show_id ?>" class="add-product-form" style="margin-bottom:16px;display:flex;gap:8px;align-items:center;">
                <input type="hidden" name="company_id" value="<?= $show_id ?>">
                <input type="text" name="product_name" placeholder="Product Name" style="flex:1;min-width:0;border-radius:16px;padding:6px 14px;border:1px solid #aaa;">
                <select name="product_category" class="product-category-select" data-company="<?= $show_id ?>" style="border-radius:16px;padding:6px 14px;border:1px solid #aaa;">
                    <option value="">Select Category</option>
                    <?php
                    // Fetch distinct categories for dropdown
                    $catRes = $conn->query("SELECT DISTINCT category FROM products WHERE category != '' AND company_id=".intval($show_id)." ORDER BY category ASC");
                    while ($cat = $catRes->fetch_assoc()):
                        $catName = htmlspecialchars($cat['category']);
                        if ($catName) echo "<option value=\"$catName\">$catName</option>";
                    endwhile;
                    ?>
                    <option value="_custom">Custom...</option>
                </select>
                <input type="text" name="custom_category" class="custom-category-input" data-company="<?= $show_id ?>" placeholder="Custom Category" style="display:none;flex:1;min-width:0;border-radius:16px;padding:6px 14px;border:1px solid #aaa;">
                <button type="submit" name="add_product" class="add-product-btn" style="background:#2ca542;color:#fff;border:none;border-radius:50%;width:40px;height:40px;font-size:1.5em;cursor:pointer;"><i class="fa fa-plus"></i></button>
            </form>
            <?php
            // Fetch and group products by category, sorted A-Z
            $products = $conn->query("SELECT * FROM products WHERE company_id=".intval($show_id)." ORDER BY category ASC, name ASC");
            $grouped = [];
            while($p = $products->fetch_assoc()) {
                $cat = $p['category'] ?: 'Uncategorized';
                $grouped[$cat][] = $p;
            }
            ksort($grouped, SORT_NATURAL | SORT_FLAG_CASE);
            foreach ($grouped as $cat => $plist):
            ?>
                <h4 class="product-category-header" style="margin:16px 0 4px 0;"><?= htmlspecialchars($cat) ?></h4>
                <ul class="product-list">
                <?php foreach ($plist as $p): ?>
                    <li class="product-list-item" data-product-name="<?= htmlspecialchars(strtolower($p['name'])) ?>" data-product-category="<?= htmlspecialchars(strtolower($cat)) ?>" style="display:flex;align-items:center;gap:8px;">
                        <span class="editable-field" data-field="product" data-product-id="<?= $p['id'] ?>" contenteditable="false" style="flex:1;min-width:0;"><?= htmlspecialchars($p['name']) ?></span>
                        <form action="process.php" method="POST" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <input type="hidden" name="company_id" value="<?= $show_id ?>">
                            <button type="submit" name="delete_product" class="delete-product-btn" style="display:none;background:none;border:none;color:#d00;font-size:1.2em;cursor:pointer;"><i class="fa fa-minus-circle"></i></button>
                        </form>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
            <script>
            // Privilege mode toggle for add product form
            function setProductFormVisibility(isAdmin) {
                document.querySelectorAll('.add-product-form').forEach(function(form) {
                    form.style.display = isAdmin ? 'flex' : 'none';
                });
            }
            // Set add product form visibility on page load and after sidebar reload
            function updateProductFormVisibilityByPrivilege() {
                var priv = localStorage.getItem('privilege') || 'view';
                setProductFormVisibility(priv === 'admin');
            }
            // Initial state
            updateProductFormVisibilityByPrivilege();

            // Product search bar logic (client-side filter)
            document.addEventListener('input', function(e) {
                if (e.target && e.target.id === 'product-search-input') {
                    const val = e.target.value.trim().toLowerCase();
                    const items = document.querySelectorAll('.product-list-item');
                    let anyVisible = false;
                    items.forEach(function(item) {
                        const name = item.getAttribute('data-product-name') || '';
                        const cat = item.getAttribute('data-product-category') || '';
                        if (val === '' || name.includes(val) || cat.includes(val)) {
                            item.style.display = 'flex';
                            anyVisible = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    // Hide category headers if all products in that category are hidden
                    document.querySelectorAll('.product-category-header').forEach(function(header) {
                        const nextUl = header.nextElementSibling;
                        if (!nextUl) return;
                        const visible = Array.from(nextUl.querySelectorAll('.product-list-item')).some(li => li.style.display !== 'none');
                        header.style.display = visible ? '' : 'none';
                    });
                }
            });
            // Make custom category work for all companies (robust for dynamic content)
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('product-category-select')) {
                    var companyId = e.target.getAttribute('data-company');
                    var customInput = document.querySelector('.custom-category-input[data-company="' + companyId + '"]');
                    if (e.target.value === '_custom') {
                        if (customInput) customInput.style.display = 'block';
                    } else {
                        if (customInput) {
                            customInput.style.display = 'none';
                            customInput.value = '';
                        }
                    }
                }
            });
            // Listen for privilege switch and toggle add product form
            document.addEventListener('DOMContentLoaded', function() {
                var privEyeBtn = document.getElementById('priv-eye-btn');
                var privDropdown = document.getElementById('priv-dropdown');
                if (privEyeBtn && privDropdown) {
                    privDropdown.querySelectorAll('.priv-option').forEach(function(opt) {
                        opt.addEventListener('click', function() {
                            var priv = this.getAttribute('data-priv');
                            localStorage.setItem('privilege', priv);
                            setProductFormVisibility(priv === 'admin');
                        });
                    });
                }
                // Hide add product form if admin modal cancel is clicked
                var cancelAdminBtn = document.getElementById('cancel-admin-login');
                if (cancelAdminBtn) {
                    cancelAdminBtn.addEventListener('click', function() {
                        // Always set privilege to view and hide add product form
                        localStorage.setItem('privilege', 'view');
                        setProductFormVisibility(false);
                    });
                }
            });

            // Ensure add product form stays hidden in view mode after sidebar reload
            document.addEventListener('DOMContentLoaded', function() {
                // Patch sidebar reload to call updateProductFormVisibilityByPrivilege
                var observer = new MutationObserver(function() {
                    updateProductFormVisibilityByPrivilege();
                });
                var sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    observer.observe(sidebar, { childList: true, subtree: true });
                }
            });
            </script>
        </div>
        <?php else: ?>
        <div style="color:#888;">No company selected.</div>
        <?php endif; ?>
    </div>
</div>
<!-- Add Company Modal -->
<div id="add-company-modal" class="modal-bg">
  <div class="modal-box">
    <form id="add-company-form" action="process.php" method="POST" autocomplete="off" class="modal-form">
      <div class="modal-fields">
        <input type="text" name="name" placeholder="Company Name" required class="modal-input">
        <input type="text" name="description" placeholder="Description" required class="modal-input">
        <input type="text" name="location" placeholder="Location (e.g. City, Address, or Google Maps link)" required class="modal-input">
        <div class="modal-tip">Tip: Enter a city, address, or paste a Google Maps link for best results.</div>
      </div>
      <div class="modal-row">
        <div class="modal-input-group">
          <span class="modal-icon"><i class="fa fa-envelope"></i></span>
          <input type="email" name="email" placeholder="Email" required class="modal-input-inner">
        </div>
        <div class="modal-input-group">
          <span class="modal-icon"><i class="fa fa-phone"></i></span>
          <input type="text" name="contact_number" placeholder="Phone" required class="modal-input-inner" inputmode="tel" pattern="[0-9()\-]*" oninput="this.value=this.value.replace(/[^0-9()\-]/g,'')">
        </div>
      </div>
      <div class="modal-input-group modal-url-group">
        <span class="modal-icon"><i class="fa fa-link"></i></span>
        <input type="url" name="url" placeholder="Website URL" class="modal-input-inner">
      </div>
      <div class="modal-rating-row">
        <div id="star-rating" class="modal-rating">
          <input type="hidden" name="review" id="review-rating" value="0">
          <span class="star" data-value="1"><i class="fa fa-star"></i></span>
          <span class="star" data-value="2"><i class="fa fa-star"></i></span>
          <span class="star" data-value="3"><i class="fa fa-star"></i></span>
          <span class="star" data-value="4"><i class="fa fa-star"></i></span>
          <span class="star" data-value="5"><i class="fa fa-star"></i></span>
          <span class="modal-rating-label">Rating</span>
        </div>
      </div>
      <div class="modal-btn-row">
        <button type="submit" name="add_company" class="modal-btn modal-btn-confirm">CONFIRM</button>
        <button type="button" id="cancel-add-company" class="modal-btn modal-btn-cancel">CANCEL</button>
      </div>
    </form>
  </div>
</div>
<div id="toast"></div>
<div id="loading" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:#0003;z-index:9998;align-items:center;justify-content:center;">
  <div style="background:#fff;padding:24px 32px;border-radius:12px;font-size:1.3em;color:#228b22;">Loading...</div>
</div>

<!-- Admin Login Modal -->
<div id="admin-login-modal" class="modal-bg">
  <div class="modal-box modal-login-box">
    <h2 class="modal-login-title">Admin Login</h2>
    <form id="admin-login-form" autocomplete="off" class="modal-form modal-login-form">
      <input type="text" name="username" id="admin-username" placeholder="Username" required class="modal-input">
      <div class="modal-password-group">
        <input type="password" name="password" id="admin-password" placeholder="Password" required class="modal-input">
        <button type="button" id="toggle-admin-password" class="modal-password-toggle" tabindex="-1"><i class="fa fa-eye"></i></button>
      </div>
      <div id="admin-login-error" class="modal-login-error" style="display:none;"></div>
      <div class="modal-btn-row">
        <button type="submit" class="modal-btn modal-btn-confirm">LOGIN</button>
        <button type="button" id="cancel-admin-login" class="modal-btn modal-btn-cancel">CANCEL</button>
      </div>
    </form>
  </div>
</div>

<?php if (isset($_GET['ajax'])) { exit; } ?>
<script>
    // Password show/hide toggle for admin login
    document.addEventListener('DOMContentLoaded', function() {
        var toggleBtn = document.getElementById('toggle-admin-password');
        var pwdInput = document.getElementById('admin-password');
        if (toggleBtn && pwdInput) {
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (pwdInput.type === 'password') {
                    pwdInput.type = 'text';
                    toggleBtn.innerHTML = '<i class="fa fa-eye-slash"></i>';
                } else {
                    pwdInput.type = 'password';
                    toggleBtn.innerHTML = '<i class="fa fa-eye"></i>';
                }
            });
        }
    });
    // After updating .main-panel.innerHTML
    const newCompanyId = parseInt(document.querySelector('.main-panel .editable-field[data-field="name"]')?.getAttribute('data-company-id') || companyId);
    if (!isNaN(newCompanyId)) {
        window.COMPANY_ID = newCompanyId;
    }

    // Toast modal function
    function showToast(message, duration = 2000) {
        var toast = document.getElementById('toast');
        if (!toast) return;
        toast.textContent = message;
        toast.style.display = 'block';
        toast.style.position = 'fixed';
        toast.style.top = '30px';
        toast.style.left = '50%';
        toast.style.transform = 'translateX(-50%)';
        toast.style.background = '#228b22';
        toast.style.color = '#fff';
        toast.style.padding = '16px 32px';
        toast.style.borderRadius = '10px';
        toast.style.fontSize = '1.2em';
        toast.style.zIndex = 9999;
        toast.style.boxShadow = '0 2px 12px #0003';
        setTimeout(function() {
            toast.style.display = 'none';
        }, duration);
    }

    // AJAX Add Company
    document.getElementById('add-company-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        var formData = new FormData(form);
        formData.append('add_company', '1');
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'process.php', true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    // Hide modal, show toast, reload sidebar/main-panel
                    document.getElementById('add-company-modal').style.display = 'none';
                    showToast('Company Added Successfully');
                    // Optionally reload page or update sidebar/main-panel via AJAX
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    showToast('Error adding company', 2500);
                }
            }
        };
        xhr.send(formData);
    });
</script>
</body>
</html>
