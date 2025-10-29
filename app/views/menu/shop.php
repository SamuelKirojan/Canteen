<?php
// Shop page: full menu grid + client cart + server-side order submit
$favoriteIds = $favoriteIds ?? [];
?>
<section id="menu" class="menu">
  <div class="container">
    <!-- Ongoing Orders Section -->
    <div id="ongoingOrdersSection" style="display:none;" class="mb-4">
      <div class="alert alert-info d-flex align-items-center" role="alert">
        <i class="bi bi-clock-history me-2" style="font-size:1.5rem;"></i>
        <div class="flex-grow-1">
          <h5 class="mb-1">You have ongoing order(s)</h5>
          <p class="mb-0 small">Click below to view real-time status</p>
        </div>
      </div>
      <div id="ongoingOrdersList" class="vstack gap-2"></div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h3 class="mb-0">Menu</h3>
      <div class="text-muted small">Browse and add items to your cart.</div>
    </div>
    <div class="row g-4">
      <?php foreach ($items as $item): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card h-100">
            <?php if (!empty($item['image_url'])): ?>
              <?php $img=$item['image_url']; if (!preg_match('#^https?://|^/#',$img)) { $img=ltrim($img,'/'); } ?>
              <img src="<?php echo htmlspecialchars($img); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>" style="height:180px;object-fit:cover;">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start mb-1">
                <h6 class="card-title mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                <?php if (!empty($_SESSION['user_id'])): ?>
                  <?php $isFavorite = in_array($item['id'], $favoriteIds); ?>
                  <button class="btn btn-sm btn-favorite <?php echo $isFavorite ? 'btn-danger' : 'btn-outline-danger'; ?>" 
                          data-menu-id="<?php echo $item['id']; ?>" 
                          data-is-favorite="<?php echo $isFavorite ? '1' : '0'; ?>"
                          style="border-radius:50%;width:28px;height:28px;padding:0;flex-shrink:0;">
                    <i class="bi bi-heart<?php echo $isFavorite ? '-fill' : ''; ?>" style="font-size:0.9rem;"></i>
                  </button>
                <?php endif; ?>
              </div>
              <?php if ($item['price'] !== null): ?>
                <div class="text-primary fw-semibold mb-1">Rp <?php echo number_format((float)$item['price'] * 1000, 0, ',', '.'); ?></div>
              <?php endif; ?>
              <?php if ($item['stock'] !== null): ?>
                <div class="text-muted small mb-2">Stock: <?php echo (int)$item['stock']; ?></div>
              <?php endif; ?>
              <?php if (!empty($item['description'])): ?>
                <p class="small flex-grow-1 mb-3"><?php echo htmlspecialchars($item['description']); ?></p>
              <?php endif; ?>
              <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm flex-nowrap" style="width: 130px;">
                  <button class="btn btn-outline-secondary btn-qty-dec" type="button" disabled>-</button>
                  <input type="number" class="form-control text-center qty-input" value="1" min="1" step="1">
                  <button class="btn btn-outline-secondary btn-qty-inc" type="button">+</button>
                </div>
                <button class="btn btn-sm btn-primary flex-grow-1 btn-add-to-cart" data-item-id="<?php echo (int)$item['id']; ?>" data-item-name="<?php echo htmlspecialchars($item['name']); ?>" data-item-price="<?php echo htmlspecialchars((float)$item['price']); ?>" data-item-image="<?php echo htmlspecialchars($img ?? ''); ?>">Add to Cart</button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
        <div class="col-12 text-center text-muted">No menu items found.</div>
      <?php endif; ?>
    </div>
  </div>
</section>

<style>
  .floating-btn { position: fixed; left: 20px; bottom: 20px; z-index: 1085; width: 56px; height: 56px; border-radius: 50%; display: none; align-items: center; justify-content: center; padding: 0; }
  .cart-count { position: absolute; top: -2px; right: -2px; font-size: 12px; font-weight: 600; line-height: 1; color: #fff; background: #df1529; padding: 2px 6px; border-radius: 999px; }
  .pulse { animation: pulse 0.4s ease; }
  @keyframes pulse { 0%{transform:scale(1);} 50%{transform:scale(1.1);} 100%{transform:scale(1);} }
  
  /* Confirmation modal animation */
  #confirmModal .modal-dialog { animation: slideInDown 0.3s ease-out; }
  @keyframes slideInDown { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
  #confirmModal.fade .modal-dialog { transition: transform 0.3s ease-out; }
</style>

<button id="btnCart" type="button" class="btn btn-primary rounded-circle floating-btn position-relative d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#checkoutModal" aria-label="Cart" style="width:56px;height:56px;">
  <i class="bi bi-bag" style="font-size:20px;"></i>
  <span id="cartCount" class="cart-count">0</span>
</button>

<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0">Items</h6>
            </div>
            <div id="cartList" class="list-group mb-3"></div>
            <div class="d-flex justify-content-between align-items-center border-top pt-2">
              <div class="fw-semibold">Total</div>
              <div id="cartTotal" class="fw-bold">Rp 0</div>
            </div>
            <hr>
            <div class="mb-3">
              <label class="form-label fw-semibold">Payment Method</label>
              <div class="d-flex gap-2">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="paymentMethod" id="paymentTransfer" value="Transfer" checked>
                  <label class="form-check-label" for="paymentTransfer">Transfer</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="paymentMethod" id="paymentCash" value="Cash">
                  <label class="form-check-label" for="paymentCash">Cash</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="paymentMethod" id="paymentQRIS" value="QRIS">
                  <label class="form-check-label" for="paymentQRIS">QRIS</label>
                </div>
              </div>
            </div>
            <hr>
            <div class="mb-2">Order Type</div>
            <div class="btn-group mb-3" role="group">
              <input type="radio" class="btn-check" name="orderType" id="orderPickup" autocomplete="off" checked>
              <label class="btn btn-outline-secondary" for="orderPickup">Pick up</label>
              <input type="radio" class="btn-check" name="orderType" id="orderDelivery" autocomplete="off">
              <label class="btn btn-outline-secondary" for="orderDelivery">Deliver</label>
            </div>
            <div id="pickupForm" class="row g-3">
              <div class="col-12">
                <input id="pickupName" type="text" class="form-control" placeholder="Name">
              </div>
            </div>
            <div id="deliveryForm" class="row g-3" style="display:none;">
              <div class="col-md-6">
                <input id="deliveryName" type="text" class="form-control" placeholder="Name">
              </div>
              <div class="col-md-6">
                <input id="deliveryPhone" type="text" class="form-control" placeholder="Phone Number">
              </div>
              <div class="col-12">
                <input id="deliveryAddress" type="text" class="form-control" placeholder="Address">
              </div>
              <div class="col-md-6">
                <input id="deliveryCost" type="text" class="form-control" placeholder="Delivery Cost (optional)">
              </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button id="btnPlaceOrder" type="button" class="btn btn-primary">Order Now</button>
      </div>
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirm Your Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="confirmContent">
        <!-- Order summary will be injected here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="btnConfirmOrder" type="button" class="btn btn-primary">Confirm Order</button>
      </div>
    </div>
  </div>
</div>

<!-- Toasts container -->
<div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index:1090;">
  <div id="toastContainer" class="toast-container"></div>
</div>

<style>
  .custom-toast {
    min-width: 300px;
    font-size: 1rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
  }
  .custom-toast .toast-body {
    padding: 1rem;
  }
  .toast-success { background-color: #198754; color: white; }
  .toast-warning { background-color: #ffc107; color: #000; }
  .toast-danger { background-color: #dc3545; color: white; }
  .toast-info { background-color: #0dcaf0; color: #000; }
</style>

<script>
(function(){
  const cartKey = 'canteen_cart_v1';
  const lastOrderKey = 'canteen_last_order_v1';
  let cart = JSON.parse(localStorage.getItem(cartKey) || '[]');
  const fmt = n => new Intl.NumberFormat('id-ID').format(n);
  const cartCountEl = document.getElementById('cartCount');
  const btnCart = document.getElementById('btnCart');
  const cartList = document.getElementById('cartList');
  const cartTotal = document.getElementById('cartTotal');
  const confirmContent = document.getElementById('confirmContent');
  const confirmModalEl = document.getElementById('confirmModal');
  let confirmModal = null;
  try { 
    if(window.bootstrap && bootstrap.Modal) {
      confirmModal = new bootstrap.Modal(confirmModalEl);
      console.log('Bootstrap Modal initialized');
    } else {
      console.log('Bootstrap not available, using fallback');
    }
  } catch(e){ console.error('Modal init error:', e); }

  function showToast(message, type='success'){
    const container = document.getElementById('toastContainer');
    const toastEl = document.createElement('div');
    toastEl.className = `toast custom-toast toast-${type} align-items-center border-0 show`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    
    const icon = type === 'success' ? '✓' : (type === 'warning' ? '⚠' : (type === 'danger' ? '✕' : 'ℹ'));
    
    toastEl.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">
          <strong style="font-size:1.2rem;margin-right:8px;">${icon}</strong>
          ${message}
        </div>
        <button type="button" class="btn-close ${type==='warning'||type==='info'?'':'btn-close-white'} me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;
    
    container.appendChild(toastEl);
    
    try {
      if (window.bootstrap && bootstrap.Toast) {
        const t = new bootstrap.Toast(toastEl, { delay: 3000 });
        t.show();
        toastEl.addEventListener('hidden.bs.toast', ()=> toastEl.remove());
      } else {
        setTimeout(() => {
          toastEl.classList.remove('show');
          setTimeout(() => toastEl.remove(), 300);
        }, 3000);
      }
    } catch(_) {
      setTimeout(() => {
        toastEl.classList.remove('show');
        setTimeout(() => toastEl.remove(), 300);
      }, 3000);
    }
  }

  function saveCart(){ localStorage.setItem(cartKey, JSON.stringify(cart)); }
  function updateBadge(){
    const c = cart.reduce((s,i)=>s+i.qty,0);
    cartCountEl.textContent = c;
    const wasHidden = btnCart.style.display === '' || btnCart.style.display === 'none';
    btnCart.style.display = c>0 ? 'inline-flex' : 'none';
    if(c>0){ btnCart.classList.remove('pulse'); void btnCart.offsetWidth; btnCart.classList.add('pulse'); }
  }
  function renderCart(){
    cartList.innerHTML = '';
    if(cart.length===0){ cartList.innerHTML = '<div class="list-group-item text-muted">Cart is empty</div>'; cartTotal.textContent='Rp 0'; return; }
    let total=0;
    cart.forEach((item,idx)=>{
      const lineTotal = item.qty*item.price*1000; total += lineTotal;
      const div = document.createElement('div'); div.className='list-group-item';
      div.innerHTML = `<div class="d-flex align-items-center">
        <img src="${item.image||''}" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:8px;" class="me-3">
        <div class="flex-grow-1">
          <div class="d-flex justify-content-between"><strong>${item.name}</strong><span>Rp ${fmt(item.price*1000)}</span></div>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="input-group input-group-sm" style="width:140px;">
              <button class="btn btn-outline-secondary btn-line-dec" data-idx="${idx}" ${item.qty<=1?'disabled':''}>-</button>
              <input type="number" class="form-control text-center line-qty" data-idx="${idx}" value="${item.qty}" min="1">
              <button class="btn btn-outline-secondary btn-line-inc" data-idx="${idx}">+</button>
            </div>
            <div class="ms-3">Rp ${fmt(lineTotal)}</div>
            <button class="btn btn-sm btn-link text-danger ms-2 btn-remove" data-idx="${idx}"><i class="bi bi-trash"></i></button>
          </div>
        </div>
      </div>`;
      cartList.appendChild(div);
    });
    cartTotal.textContent = 'Rp '+fmt(total);
  }

  function upsertToCart(data){ const idx = cart.findIndex(x=>x.id===data.id); if(idx>=0){ cart[idx].qty += data.qty; } else { cart.push(data); } saveCart(); renderCart(); updateBadge(); }

  document.addEventListener('click', function(e){
    if(e.target.closest('.btn-qty-inc')){ const group=e.target.closest('.input-group'); const input=group.querySelector('.qty-input'); input.value=(+input.value||1)+1; group.querySelector('.btn-qty-dec').disabled=(+input.value)<=1; }
    if(e.target.closest('.btn-qty-dec')){ const group=e.target.closest('.input-group'); const input=group.querySelector('.qty-input'); const v=Math.max(1,(+input.value||1)-1); input.value=v; group.querySelector('.btn-qty-dec').disabled=v<=1; }
    if(e.target.closest('.btn-add-to-cart')){ const btn=e.target.closest('.btn-add-to-cart'); const card=btn.closest('.card'); const qty=Math.max(1,+card.querySelector('.qty-input').value||1); const item={ id:+btn.dataset.itemId, name:btn.dataset.itemName, price:+btn.dataset.itemPrice, image:btn.dataset.itemImage, qty }; upsertToCart(item); showToast(`Added to cart: ${item.name} x ${item.qty}`, 'success'); }
    if(e.target.closest('.btn-line-inc')){ const idx=+e.target.dataset.idx; cart[idx].qty++; saveCart(); renderCart(); updateBadge(); }
    if(e.target.closest('.btn-line-dec')){ const idx=+e.target.dataset.idx; cart[idx].qty=Math.max(1,cart[idx].qty-1); saveCart(); renderCart(); updateBadge(); }
    if(e.target.closest('.btn-remove')){ const idx=+e.target.dataset.idx; cart.splice(idx,1); saveCart(); renderCart(); updateBadge(); }
  });

  document.addEventListener('input', function(e){
    if(e.target.classList.contains('qty-input')){ const dec=e.target.closest('.input-group').querySelector('.btn-qty-dec'); const v=Math.max(1,+e.target.value||1); e.target.value=v; dec.disabled=v<=1; }
    if(e.target.classList.contains('line-qty')){ const idx=+e.target.dataset.idx; const v=Math.max(1,+e.target.value||1); cart[idx].qty=v; saveCart(); renderCart(); updateBadge(); }
  });

  document.getElementById('orderPickup').addEventListener('change', function(){ document.getElementById('pickupForm').style.display='block'; document.getElementById('deliveryForm').style.display='none'; });
  document.getElementById('orderDelivery').addEventListener('change', function(){ document.getElementById('pickupForm').style.display='none'; document.getElementById('deliveryForm').style.display='flex'; });

  document.getElementById('btnPlaceOrder').addEventListener('click', function(){
    if(cart.length===0){ showToast('Your cart is empty.','warning'); return; }
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value || 'Transfer';
    const isDelivery = document.getElementById('orderDelivery').checked;
    const name = (isDelivery ? document.getElementById('deliveryName').value : document.getElementById('pickupName').value).trim();
    const phone = isDelivery ? document.getElementById('deliveryPhone').value.trim() : '';
    const address = isDelivery ? document.getElementById('deliveryAddress').value.trim() : '';
    const deliveryCostStr = isDelivery ? (document.getElementById('deliveryCost').value||'') : '';
    if(!name){ showToast('Please enter your name.','warning'); return; }
    if(isDelivery && !address){ showToast('Please enter your delivery address.','warning'); return; }

    // Build confirmation summary
    let subtotal = 0;
    let lines = cart.map(i=>{ const line=i.qty*i.price*1000; subtotal+=line; return `<div class="d-flex justify-content-between"><span>${i.name} x ${i.qty}</span><span>Rp ${fmt(line)}</span></div>`; }).join('');
    const dCost = (deliveryCostStr && !isNaN(+deliveryCostStr)) ? (+deliveryCostStr) : 0;
    const total = subtotal + (isDelivery ? dCost*1000 : 0);
    confirmContent.innerHTML = `
      <div class="mb-2"><strong>Payment Method:</strong> ${paymentMethod}</div>
      <div class="mb-2"><strong>Order Type:</strong> ${isDelivery?'Delivery':'Pickup'}</div>
      <div class="mb-2"><strong>Name:</strong> ${name}</div>
      ${isDelivery?`<div class="mb-2"><strong>Phone:</strong> ${phone||'-'}</div><div class="mb-2"><strong>Address:</strong> ${address}</div>`:''}
      <hr>
      <div class="mb-2 fw-semibold">Items</div>
      ${lines||'<div class="text-muted">No items</div>'}
      <hr>
      <div class="d-flex justify-content-between"><span>Subtotal</span><span>Rp ${fmt(subtotal)}</span></div>
      ${isDelivery && dCost>0?`<div class="d-flex justify-content-between"><span>Delivery Cost</span><span>Rp ${fmt(dCost*1000)}</span></div>`:''}
      <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>Rp ${fmt(total)}</span></div>
    `;
    // Store order data for confirm button
    confirmModalEl.dataset.orderData = JSON.stringify({isDelivery, name, phone, address, deliveryCostStr, paymentMethod});
    // Show confirmation modal
    console.log('Showing confirmation modal');
    try{ 
      if(confirmModal) {
        confirmModal.show();
      } else {
        // Manual fallback
        confirmModalEl.style.display='block';
        confirmModalEl.classList.add('show');
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'confirmBackdrop';
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
      }
    } catch(e){ 
      console.error('Error showing modal:', e);
      alert('Error showing confirmation. Check console.');
    }
  });

  document.getElementById('btnConfirmOrder').addEventListener('click', function(){
    console.log('Confirm Order clicked');
    const data = JSON.parse(confirmModalEl.dataset.orderData || '{}');
    const payload = new FormData();
    payload.append('type', data.isDelivery ? 'delivery' : 'pickup');
    payload.append('name', data.name);
    payload.append('phone', data.phone);
    payload.append('address', data.address);
    payload.append('deliveryCost', data.deliveryCostStr);
    payload.append('paymentMethod', data.paymentMethod || 'Transfer');
    payload.append('items', JSON.stringify(cart));
    const btn = this; const prevText = btn.textContent; btn.disabled = true; btn.textContent = 'Placing...';
    fetch('index.php?r=order/create', { method:'POST', body: payload }).then(r=>{
      console.log('Order response:', r);
      if(r.redirected){ 
        localStorage.removeItem(cartKey); 
        window.location = r.url; 
        return; 
      }
      return r.text().then(t=>{ 
        console.error('Order error:', t);
        showToast(t||'Unexpected response','danger'); 
      });
    }).catch(err=>{
      console.error('Fetch error:', err);
      showToast('Order failed: '+err,'danger');
    }).finally(()=>{ 
      btn.disabled = false; 
      btn.textContent = prevText; 
      // Hide modal
      try{
        if(confirmModal) {
          confirmModal.hide();
        } else {
          confirmModalEl.style.display='none';
          confirmModalEl.classList.remove('show');
          confirmModalEl.removeAttribute('aria-modal');
          confirmModalEl.setAttribute('aria-hidden', 'true');
          const backdrop = document.getElementById('confirmBackdrop');
          if(backdrop) backdrop.remove();
          document.body.classList.remove('modal-open');
          document.body.style.overflow = '';
          document.body.style.paddingRight = '';
        }
      } catch(_){}
    });
  });

  function loadOngoingOrders(){
    fetch('index.php?r=order/ongoing')
      .then(r => r.json())
      .then(data => {
        if(data.orders && data.orders.length > 0){
          const section = document.getElementById('ongoingOrdersSection');
          const list = document.getElementById('ongoingOrdersList');
          list.innerHTML = '';
          data.orders.forEach(o => {
            const statusBg = o.status === 'Not Ready' ? 'bg-warning' : (o.status === 'Ready' ? 'bg-success' : 'bg-info');
            const card = document.createElement('a');
            card.href = `index.php?r=order/info&id=${o.id}`;
            card.className = 'card text-decoration-none border-primary';
            card.style.borderWidth = '2px';
            card.innerHTML = `
              <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="fw-semibold">Order #${o.id}</div>
                  <span class="badge ${statusBg} text-uppercase">${o.status}</span>
                </div>
                <div class="small text-muted">${o.type} • ${o.item_count} item(s) • Rp ${new Intl.NumberFormat('id-ID').format(o.total*1000)}</div>
                <div class="small text-primary mt-1"><i class="bi bi-arrow-right-circle me-1"></i>View real-time status</div>
              </div>
            `;
            list.appendChild(card);
          });
          section.style.display = 'block';
        }
      })
      .catch(e => console.error('Failed to load ongoing orders:', e));
  }

  function init(){ renderCart(); updateBadge(); loadOngoingOrders(); }
  const checkoutModalEl = document.getElementById('checkoutModal');
  if (checkoutModalEl) {
    checkoutModalEl.addEventListener('show.bs.modal', function(){ renderCart(); });
  }
  init();
})();

// Favorite button handler
(function(){
  console.log('Favorite buttons initialized');
  
  function showFavoriteToast(message, type='success'){
    const container = document.getElementById('toastContainer');
    if(!container) return;
    
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-bg-' + type + ' border-0 show';
    toast.setAttribute('role', 'alert');
    toast.style.minWidth = '250px';
    
    const toastBody = document.createElement('div');
    toastBody.className = 'd-flex align-items-center';
    
    const bodyContent = document.createElement('div');
    bodyContent.className = 'toast-body d-flex align-items-center gap-2';
    
    // Add icon
    const icon = document.createElement('i');
    if(type === 'success'){
      icon.className = 'bi bi-check-circle-fill';
      icon.style.fontSize = '1.2rem';
    } else {
      icon.className = 'bi bi-x-circle-fill';
      icon.style.fontSize = '1.2rem';
    }
    
    const textSpan = document.createElement('span');
    textSpan.textContent = message;
    
    bodyContent.appendChild(icon);
    bodyContent.appendChild(textSpan);
    
    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'btn-close btn-close-white me-2 m-auto';
    closeBtn.setAttribute('data-bs-dismiss', 'toast');
    
    toastBody.appendChild(bodyContent);
    toastBody.appendChild(closeBtn);
    toast.appendChild(toastBody);
    
    container.appendChild(toast);
    
    // Auto dismiss
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }
  
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.btn-favorite');
    if(!btn) return;
    
    e.preventDefault();
    e.stopPropagation();
    
    const menuId = btn.dataset.menuId;
    const isFavorite = btn.dataset.isFavorite === '1';
    
    console.log('Toggling favorite for menu:', menuId, 'Current:', isFavorite);
    
    const formData = new FormData();
    formData.append('menu_id', menuId);
    
    btn.disabled = true;
    
    fetch('index.php?r=favorite/toggle', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      console.log('Favorite response:', data);
      if(data.success){
        const newIsFavorite = data.is_favorite;
        btn.dataset.isFavorite = newIsFavorite ? '1' : '0';
        
        // Update button style
        if(newIsFavorite){
          btn.classList.remove('btn-outline-danger');
          btn.classList.add('btn-danger');
          btn.querySelector('i').classList.remove('bi-heart');
          btn.querySelector('i').classList.add('bi-heart-fill');
          showFavoriteToast('Added to favorites!', 'success');
        } else {
          btn.classList.remove('btn-danger');
          btn.classList.add('btn-outline-danger');
          btn.querySelector('i').classList.remove('bi-heart-fill');
          btn.querySelector('i').classList.add('bi-heart');
          showFavoriteToast('Removed from favorites', 'success');
        }
      } else {
        showFavoriteToast(data.error || 'Failed to update favorite', 'danger');
      }
      btn.disabled = false;
    })
    .catch(err => {
      console.error('Favorite error:', err);
      showFavoriteToast('Network error', 'danger');
      btn.disabled = false;
    });
  });
})();
</script>
