<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
    <meta charset="UTF-8">
<head>
    <title>Company Directory</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .container { display: flex; height: 90vh; }
        .sidebar { width: 350px; background: #f5f5f5; overflow-y: auto; border-right: 1px solid #ddd; }
        .company-card { background: #fff; margin: 16px; padding: 16px; border-radius: 8px; box-shadow: 0 1px 4px #0001; cursor: pointer; transition: box-shadow .2s; position:relative; }
        .company-card.selected, .company-card:hover { box-shadow: 0 2px 8px #0002; background: #e9f5ff; }
        .company-card .location { color: #888; font-size: 13px; margin-bottom: 4px; }
        .company-card .name { font-size: 20px; font-weight: 600; }
        .company-card .contact { font-size: 13px, color: #555; margin-top: 4px; }
        .main-panel { flex: 1; padding: 32px 40px; overflow-y: auto; }
        .main-header { display: flex; align-items: flex-start; justify-content: space-between; }
        .main-header .info { max-width: 70%; }
        .main-header .name { font-size: 2rem; font-weight: 700; }
        .main-header .contact { color: #555; font-size: 15px; margin: 8px 0; }
        .main-header .desc { color: #666; font-size: 15px; margin-top: 12px; }
        .main-header .icons { font-size: 2rem; display: flex; gap: 18px; }
        .main-header .icons a { color: #333; text-decoration: none; }
        .products-section { margin-top: 32px; }
        .products-section h3 { font-size: 1.2rem; margin-bottom: 12px; }
        .product-list { list-style: none; padding: 0; }
        .product-list li { background: #f8f8f8; border-radius: 6px; margin-bottom: 10px; padding: 10px 16px; font-size: 16px; }
        .searchbar { display: flex; align-items: center; background: #fff; border-bottom: 1px solid #ddd; padding: 12px 24px; }
        .searchbar input { flex: 1; border: none; outline: none; font-size: 1.1rem; background: none; }
        .searchbar .icon-btn { background: none; border: none; font-size: 1.3rem; margin-left: 12px; cursor: pointer; color: #555; }
        #add-company-btn { display:none;background:#228b22;color:#fff;font-weight:700;font-size:1.3em;padding:0 32px;height:44px;border:none;border-radius:10px;margin-left:12px;cursor:pointer; }
        .editable-field {
            border: none;
            background: transparent;
            outline: none;
            font: inherit;
            color: inherit;
            width: 100%;
            transition: border-bottom 0.2s;
            border-bottom: 1.5px solid transparent;
            cursor: pointer;
        }
        .editable-field[contenteditable="true"]:hover,
        .editable-field.edit-hover {
            border-bottom: 1.5px solid #228b22;
            cursor: text;
        }
        .editable-field:focus {
            border-bottom: 1.5px solid #228b22;
            background: #e9f5ff;
            outline: none;
        }
        @media (max-width: 900px) { .container { flex-direction: column; } .sidebar { width: 100%; height: 200px; border-right: none; border-bottom: 1px solid #ddd; } .main-panel { padding: 16px; } }
    </style>
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
        <input type="text" id="search-input" name="search" placeholder="Search company name/email/sector" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" autocomplete="off">
    </form>
    <button class="icon-btn"><i class="fa fa-sliders-h"></i></button>
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
            // Search companies by name, email, sector, OR if any of their products match the search
            $where .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR sector LIKE '%$search%' OR id IN (SELECT company_id FROM products WHERE name LIKE '%$search%'))";
        }
        if ($sector) {
            $sector = $conn->real_escape_string($sector);
            $where .= " AND sector = '$sector'";
        }
        $where .= " AND status != 'archived'";
        $companies = $conn->query("SELECT * FROM companies $where ORDER BY id DESC");
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
            <form action="process.php" method="POST" style="position:absolute;right:10px;top:10px;display:inline;">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <button type="submit" name="delete" class="delete-company-btn" style="display:none;background:none;border:none;color:#d00;font-size:1.3em;cursor:pointer;" onclick="return confirm('Delete this company?')"><i class="fa fa-trash"></i></button>
            </form>
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
                    <span class="editable-field" data-field="name" contenteditable="false"><?= htmlspecialchars($company['name']) ?></span>
                </div>
                <div class="contact">
                    <span class="editable-field" data-field="email" contenteditable="false"><i class="fa fa-envelope"></i> <?= htmlspecialchars($company['email']) ?></span> &nbsp;
                    <span class="editable-field" data-field="contact_number" contenteditable="false"><i class="fa fa-phone"></i> <?= htmlspecialchars($company['contact_number']) ?></span>
                    <br><span class="editable-field" data-field="location" contenteditable="false"><i class="fa fa-map-marker-alt"></i> <?= htmlspecialchars($company['location']) ?></span>
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
            <ul class="product-list">
            <?php
            $products = $conn->query("SELECT * FROM products WHERE company_id=".intval($show_id));
            while($p = $products->fetch_assoc()): ?>
                <li style="display:flex;align-items:center;gap:8px;">
                    <span class="editable-field" data-field="product" data-product-id="<?= $p['id'] ?>" contenteditable="false" style="flex:1;min-width:0;"><?= htmlspecialchars($p['name']) ?></span>
                    <form action="process.php" method="POST" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <input type="hidden" name="company_id" value="<?= $show_id ?>">
                        <button type="submit" name="delete_product" class="delete-product-btn" style="display:none;background:none;border:none;color:#d00;font-size:1.2em;cursor:pointer;"><i class="fa fa-minus-circle"></i></button>
                    </form>
                </li>
            <?php endwhile; ?>
            </ul>
            <form action="process.php" method="POST" id="add-product-form" style="margin-top:16px;display:flex;gap:8px;align-items:center;">
                <input type="hidden" name="company_id" value="<?= $show_id ?>">
                <input type="text" name="product_name" placeholder="Product Name" style="flex:1;min-width:0;border-radius:16px;padding:6px 14px;border:1px solid #aaa;">
                <button type="submit" name="add_product" class="add-product-btn" style="display:none;background:#2ca542;color:#fff;border:none;border-radius:50%;width:40px;height:40px;font-size:1.5em;cursor:pointer;"><i class="fa fa-plus"></i></button>
            </form>
        </div>
        <?php else: ?>
        <div style="color:#888;">No company selected.</div>
        <?php endif; ?>
    </div>
</div>
<!-- Add Company Modal -->
<div id="add-company-modal" style="display:none;position:fixed;z-index:1000;left:0;top:0;width:100vw;height:100vh;background:#0006;align-items:center;justify-content:center;">
  <div style="background:#ddd;padding:24px 24px 16px 24px;border-radius:16px;min-width:340px;max-width:95vw;box-shadow:0 4px 32px #0003;display:flex;flex-direction:column;gap:12px;position:relative;">
    <form id="add-company-form" action="process.php" method="POST" autocomplete="off" style="display:flex;flex-direction:column;gap:10px;">
      <input type="text" name="name" placeholder="Company Name" required style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid #ccc;font-size:1.1em;background:#fff7f7;">
      <textarea name="description" placeholder="Description" rows="2" style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid #ccc;font-size:1.1em;background:#fff7f7;"></textarea>
      <input type="text" name="location" placeholder="Location (e.g. City, Address, or Google Maps link)" required style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid #ccc;font-size:1.1em;background:#fff7f7;">
      <div style="font-size:12px;color:#666;margin-bottom:2px;margin-top:-6px;">Tip: Enter a city, address, or paste a Google Maps link for best results.</div>
      <div style="display:flex;gap:8px;">
        <div style="flex:1;display:flex;align-items:center;background:#fff7f7;border-radius:8px;border:1px solid #ccc;">
          <span style="margin:0 8px 0 8px;"><i class="fa fa-envelope"></i></span>
          <input type="email" name="email" placeholder="Email" required style="border:none;outline:none;background:transparent;font-size:1em;width:100%;padding:10px 0;">
        </div>
        <div style="flex:1;display:flex;align-items:center;background:#fff7f7;border-radius:8px;border:1px solid #ccc;">
          <span style="margin:0 8px 0 8px;"><i class="fa fa-phone"></i></span>
          <input type="text" name="contact_number" placeholder="Phone" required style="border:none;outline:none;background:transparent;font-size:1em;width:100%;padding:10px 0;">
        </div>
      </div>
      <div style="display:flex;align-items:center;background:#fff7f7;border-radius:8px;border:1px solid #ccc;">
        <span style="margin:0 8px 0 8px;"><i class="fa fa-link"></i></span>
        <input type="url" name="url" placeholder="Website URL" style="border:none;outline:none;background:transparent;font-size:1em;width:100%;padding:10px 0;">
      </div>
      <div style="display:flex;gap:16px;justify-content:flex-end;margin-top:12px;">
        <button type="submit" name="add_company" style="background:#228b22;color:#fff;font-weight:700;font-size:1.1em;padding:8px 32px;border:none;border-radius:8px;">CONFIRM</button>
        <button type="button" id="cancel-add-company" style="background:#d00;color:#fff;font-weight:700;font-size:1.1em;padding:8px 32px;border:none;border-radius:8px;">CANCEL</button>
      </div>
    </form>
  </div>
</div>
<script>
const searchInput = document.getElementById('search-input');
const searchForm = document.getElementById('search-form');
let debounceTimeout;
searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        // Use AJAX to fetch and update the sidebar without full page reload
        const params = new URLSearchParams(new FormData(searchForm));
        fetch('index.php?ajax=1&' + params.toString())
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newSidebar = doc.querySelector('.sidebar');
                if (newSidebar) {
                    document.querySelector('.sidebar').innerHTML = newSidebar.innerHTML;
                }
            });
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
    // Always show visit-site-btn in both modes
    document.querySelectorAll('.visit-site-btn').forEach(btn => btn.style.display = 'inline-block');
    const addBtn = document.getElementById('add-company-btn');
    if (addBtn) addBtn.style.display = isAdmin ? 'block' : 'none';
    // Show/hide add product form
    const addProductForm = document.getElementById('add-product-form');
    if (addProductForm) addProductForm.style.display = isAdmin ? 'flex' : 'none';
}
updateAdminUI();

// Modal logic
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
    document.querySelectorAll('.editable-field').forEach(function(el) {
        // Remove previous listeners to avoid stacking
        el.replaceWith(el.cloneNode(true));
    });
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
    let data = { field, value };
    if (field === 'product') {
        data.product_id = el.getAttribute('data-product-id');
        data.company_id = <?= intval($show_id) ?>;
        data.action = 'update_product';
    } else {
        data.company_id = <?= intval($show_id) ?>;
        data.action = 'update_company';
    }
    fetch('process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(res => res.json()).then(resp => {
        if (!resp.success) alert(resp.error || 'Update failed');
    });
}
function updateEditableOnPrivChange() { makeEditable(); }
// Call on load and on privilege change
makeEditable();

// Update UI on privilege change
function onPrivChange() { updatePrivIcon(); updateAdminUI(); updateEditableOnPrivChange(); }
document.querySelectorAll('.priv-option').forEach(function(opt) {
    opt.onclick = function() {
        currentPriv = this.getAttribute('data-priv');
        localStorage.setItem('privilege', currentPriv);
        onPrivChange();
        privDropdown.style.display = 'none';
    };
});
</script>
<?php if (isset($_GET['ajax'])) { exit; } ?>
</body>
</html>
