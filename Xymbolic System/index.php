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
      <input type="text" name="description" placeholder="Description" required style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid #ccc;font-size:1.1em;background:#fff7f7;">
      <input type="text" name="location" placeholder="Location (e.g. City, Address, or Google Maps link)" required style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid #ccc;font-size:1.1em;background:#fff7f7;">
      <div style="font-size:12px;color:#666;margin-bottom:2px;margin-top:-6px;">Tip: Enter a city, address, or paste a Google Maps link for best results.</div>
      <div style="display:flex;gap:8px;align-items:flex-end;">
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
      <div style="display:flex;justify-content:flex-end;align-items:center;margin-top:8px;">
        <div id="star-rating" style="display:flex;gap:2px;align-items:center;">
          <input type="hidden" name="review" id="review-rating" value="0">
          <span class="star" data-value="1" style="font-size:1.7em;cursor:pointer;color:#ccc;"><i class="fa fa-star"></i></span>
          <span class="star" data-value="2" style="font-size:1.7em;cursor:pointer;color:#ccc;"><i class="fa fa-star"></i></span>
          <span class="star" data-value="3" style="font-size:1.7em;cursor:pointer;color:#ccc;"><i class="fa fa-star"></i></span>
          <span class="star" data-value="4" style="font-size:1.7em;cursor:pointer;color:#ccc;"><i class="fa fa-star"></i></span>
          <span class="star" data-value="5" style="font-size:1.7em;cursor:pointer;color:#ccc;"><i class="fa fa-star"></i></span>
          <span style="font-size:1em;color:#888;margin-left:8px;">Rating</span>
        </div>
      </div>
      <div style="display:flex;gap:16px;justify-content:flex-end;margin-top:12px;">
        <button type="submit" name="add_company" style="background:#228b22;color:#fff;font-weight:700;font-size:1.1em;padding:8px 32px;border:none;border-radius:8px;">CONFIRM</button>
        <button type="button" id="cancel-add-company" style="background:#d00;color:#fff;font-weight:700;font-size:1.1em;padding:8px 32px;border:none;border-radius:8px;">CANCEL</button>
      </div>
    </form>
  </div>
</div>
<div id="toast"></div>
<div id="loading" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:#0003;z-index:9998;align-items:center;justify-content:center;">
  <div style="background:#fff;padding:24px 32px;border-radius:12px;font-size:1.3em;color:#228b22;">Loading...</div>
</div>

<!-- Admin Login Modal -->
<div id="admin-login-modal" style="display:none;position:fixed;z-index:10000;left:0;top:0;width:100vw;height:100vh;background:#0006;align-items:center;justify-content:center;">
  <div style="background:#fff;padding:32px 32px 24px 32px;border-radius:16px;min-width:320px;max-width:95vw;box-shadow:0 4px 32px #0003;display:flex;flex-direction:column;gap:16px;position:relative;">
    <h2 style="margin:0 0 8px 0;font-size:1.3em;color:#228b22;text-align:center;">Admin Login</h2>
    <form id="admin-login-form" autocomplete="off" style="display:flex;flex-direction:column;gap:12px;">
      <input type="text" name="username" id="admin-username" placeholder="Username" required style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid #ccc;font-size:1.1em;">
      <div style="position:relative;width:100%;">
        <input type="password" name="password" id="admin-password" placeholder="Password" required style="width:100%;padding:10px 12px 10px 12px;border-radius:8px;border:1px solid #ccc;font-size:1.1em;">
        <button type="button" id="toggle-admin-password" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1em;color:#888;padding:0 4px;">
          <i class="fa fa-eye"></i>
        </button>
      </div>
      <div id="admin-login-error" style="color:#d00;font-size:1em;display:none;"></div>
      <div style="display:flex;gap:16px;justify-content:flex-end;margin-top:8px;">
        <button type="submit" style="background:#228b22;color:#fff;font-weight:700;font-size:1.1em;padding:8px 32px;border:none;border-radius:8px;">LOGIN</button>
        <button type="button" id="cancel-admin-login" style="background:#d00;color:#fff;font-weight:700;font-size:1.1em;padding:8px 32px;border:none;border-radius:8px;">CANCEL</button>
      </div>
    </form>
  </div>
</div>

<?php if (isset($_GET['ajax'])) { exit; } ?>
<script>
    // After updating .main-panel.innerHTML
    const newCompanyId = parseInt(document.querySelector('.main-panel .editable-field[data-field="name"]')?.getAttribute('data-company-id') || companyId);
    if (!isNaN(newCompanyId)) {
        window.COMPANY_ID = newCompanyId;
    }
</script>
</body>
</html>
