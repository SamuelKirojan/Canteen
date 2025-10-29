<?php
$items = $items ?? [];
$error = $error ?? '';
$adminName = $_SESSION['admin_name'] ?? 'Admin';
?>

<section class="py-4">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-1">Menu Management</h2>
        <p class="text-muted mb-0">Edit menu items, prices, and stock levels</p>
      </div>
      <div>
        <a href="index.php?r=admin/dashboard" class="btn btn-outline-primary me-2">
          <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
        </a>
        <a href="index.php?r=admin/logout" class="btn btn-outline-danger">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
      </div>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-card-list me-2"></i>Menu Items</h5>
      </div>
      <div class="card-body p-0">
        <?php if (empty($items)): ?>
          <div class="p-4 text-center text-muted">
            <i class="bi bi-inbox" style="font-size:3rem;"></i>
            <p class="mt-2 mb-0">No menu items yet</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Image</th>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Price (Rp)</th>
                  <th>Stock</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                  <tr data-menu-id="<?php echo (int)$item['id']; ?>">
                    <td class="fw-bold"><?php echo (int)$item['id']; ?></td>
                    <td>
                      <?php if (!empty($item['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width:50px;height:50px;object-fit:cover;border-radius:0.25rem;">
                      <?php else: ?>
                        <div style="width:50px;height:50px;background:#e9ecef;border-radius:0.25rem;"></div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="editable-field" data-field="name" data-menu-id="<?php echo $item['id']; ?>">
                        <span class="field-value"><?php echo htmlspecialchars($item['name']); ?></span>
                        <button class="btn btn-sm btn-link edit-btn"><i class="bi bi-pencil"></i></button>
                      </div>
                    </td>
                    <td>
                      <div class="editable-field" data-field="description" data-menu-id="<?php echo $item['id']; ?>">
                        <span class="field-value"><?php echo htmlspecialchars($item['description']); ?></span>
                        <button class="btn btn-sm btn-link edit-btn"><i class="bi bi-pencil"></i></button>
                      </div>
                    </td>
                    <td>
                      <div class="editable-field" data-field="price" data-menu-id="<?php echo $item['id']; ?>">
                        <span class="field-value"><?php echo number_format((float)$item['price'] * 1000, 0, ',', '.'); ?></span>
                        <button class="btn btn-sm btn-link edit-btn"><i class="bi bi-pencil"></i></button>
                      </div>
                    </td>
                    <td>
                      <div class="editable-field" data-field="stock" data-menu-id="<?php echo $item['id']; ?>">
                        <span class="field-value badge bg-<?php echo (int)$item['stock'] > 10 ? 'success' : ((int)$item['stock'] > 0 ? 'warning' : 'danger'); ?>">
                          <?php echo (int)$item['stock']; ?>
                        </span>
                        <button class="btn btn-sm btn-link edit-btn"><i class="bi bi-pencil"></i></button>
                      </div>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary quick-stock-btn" data-menu-id="<?php echo $item['id']; ?>" data-action="add">
                        <i class="bi bi-plus-circle"></i> Add 10
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- Toast Container -->
<div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index:1090;">
  <div id="toastContainer"></div>
</div>

<style>
  .editable-field {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .editable-field .field-value {
    flex: 1;
  }
  .editable-field .edit-btn {
    opacity: 0;
    transition: opacity 0.2s;
  }
  .editable-field:hover .edit-btn {
    opacity: 1;
  }
  .custom-toast {
    min-width: 300px;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
  }
</style>

<script>
(function(){
  console.log('Menu Management initialized');
  
  function showToast(message, type='success'){
    console.log('Toast:', type, message);
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast custom-toast align-items-center text-bg-${type} border-0 show`;
    toast.setAttribute('role', 'alert');
    
    const icon = type === 'success' ? '✓' : (type === 'danger' ? '✕' : 'ℹ');
    
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">
          <strong style="font-size:1.2rem;margin-right:8px;">${icon}</strong>
          ${message}
        </div>
        <button type="button" class="btn-close ${type==='warning'?'':'btn-close-white'} me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  function updateMenu(menuId, field, value){
    console.log('Updating menu', menuId, field, '=', value);
    
    const formData = new FormData();
    formData.append('menu_id', menuId);
    formData.append('field', field);
    formData.append('value', value);
    
    return fetch('index.php?r=admin/updateMenu', {
      method: 'POST',
      body: formData
    })
    .then(r => {
      console.log('Response status:', r.status);
      return r.json();
    })
    .then(data => {
      console.log('Response data:', data);
      if(data.success){
        showToast(`${field} updated successfully`, 'success');
        return true;
      } else {
        console.error('Update failed:', data.error);
        showToast(data.error || 'Failed to update', 'danger');
        return false;
      }
    })
    .catch(err => {
      console.error('Fetch error:', err);
      showToast('Network error: ' + err.message, 'danger');
      return false;
    });
  }

  // Editable fields
  document.querySelectorAll('.editable-field').forEach(field => {
    const editBtn = field.querySelector('.edit-btn');
    const valueSpan = field.querySelector('.field-value');
    const fieldName = field.dataset.field;
    const menuId = field.dataset.menuId;
    
    editBtn.addEventListener('click', function(){
      const currentValue = valueSpan.textContent.trim();
      let inputValue = currentValue;
      
      // For price, remove formatting
      if(fieldName === 'price'){
        inputValue = currentValue.replace(/\./g, '');
      }
      
      const input = document.createElement('input');
      input.type = fieldName === 'description' ? 'text' : (fieldName === 'price' || fieldName === 'stock' ? 'number' : 'text');
      input.className = 'form-control form-control-sm';
      input.value = inputValue;
      input.style.width = '100%';
      
      if(fieldName === 'price' || fieldName === 'stock'){
        input.min = '0';
        input.step = fieldName === 'price' ? '1000' : '1';
      }
      
      const save = () => {
        let newValue = input.value.trim();
        if(!newValue) {
          showToast('Value cannot be empty', 'warning');
          return;
        }
        
        // For price, convert from display format to database format
        let dbValue = newValue;
        if(fieldName === 'price'){
          dbValue = (parseFloat(newValue) / 1000).toFixed(2);
        }
        
        updateMenu(menuId, fieldName, dbValue).then(success => {
          if(success){
            if(fieldName === 'price'){
              valueSpan.textContent = new Intl.NumberFormat('id-ID').format(newValue);
            } else if(fieldName === 'stock'){
              valueSpan.className = 'field-value badge bg-' + (newValue > 10 ? 'success' : (newValue > 0 ? 'warning' : 'danger'));
              valueSpan.textContent = newValue;
            } else {
              valueSpan.textContent = newValue;
            }
            field.replaceChild(valueSpan, input);
          }
        });
      };
      
      input.addEventListener('blur', save);
      input.addEventListener('keydown', e => {
        if(e.key === 'Enter') save();
        if(e.key === 'Escape') field.replaceChild(valueSpan, input);
      });
      
      field.replaceChild(input, valueSpan);
      input.focus();
      input.select();
    });
  });

  // Quick stock buttons
  document.querySelectorAll('.quick-stock-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      const menuId = this.dataset.menuId;
      const row = document.querySelector(`tr[data-menu-id="${menuId}"]`);
      const stockField = row.querySelector('.editable-field[data-field="stock"]');
      const stockValue = stockField.querySelector('.field-value');
      const currentStock = parseInt(stockValue.textContent.trim()) || 0;
      const newStock = currentStock + 10;
      
      console.log('Quick add stock to menu', menuId, ':', currentStock, '->', newStock);
      
      updateMenu(menuId, 'stock', newStock).then(success => {
        if(success){
          stockValue.className = 'field-value badge bg-' + (newStock > 10 ? 'success' : (newStock > 0 ? 'warning' : 'danger'));
          stockValue.textContent = newStock;
        }
      });
    });
  });
})();
</script>
